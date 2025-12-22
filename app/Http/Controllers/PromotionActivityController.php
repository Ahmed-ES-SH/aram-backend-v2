<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromoterActivity;
use Exception;
use App\Http\Traits\ApiResponse;
use App\Models\PromotionActivity;
use Illuminate\Http\Request;
use App\Models\Promoter;
use App\Models\User;
use App\Models\Organization;
use App\Models\PromoterRatio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PromotionActivityController extends Controller
{
    use ApiResponse;

    /**
     * Get promotion activities for a specific user.
     */
    public function getPromoterActivities(Request $request)
    {
        try {

            $user = $request->user();

            // Find the promoter profile for this user/org
            $promoter = Promoter::where('promoter_id', $user->id)
                ->where('promoter_type', $user->account_type)
                ->first();

            if (!$promoter) {
                return $this->errorResponse('Promoter profile not found.', 404);
            }

            $activities = PromotionActivity::where('promoter_id', $user->id)
                ->where('promoter_type', $user->account_type)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return $this->paginationResponse($activities, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getActivitiesByType(Request $request)
    {
        try {
            $request->validate([
                'promoter_id' => 'required|exists:promoters,promoter_id',
                'promoter_type' => 'required|in:user,organization',
                'activity_type' => 'required|in:signup,purchase,visit'
            ]);

            // Get activities without relationships first
            $activities = PromotionActivity::where('promoter_id', $request->promoter_id)
                ->where('promoter_type', $request->promoter_type)
                ->where('activity_type', $request->activity_type)
                ->paginate(15);

            // Load relationships based on each record's member_type
            $activities->getCollection()->transform(function ($activity) {
                if ($activity->member_type == 'user') {
                    $activity->load('userMember:id,name,email,image,role');
                    $activity->setRelation('member', $activity->userMember);
                    $activity->setRelation('userMember', null); // Optional: hide original
                } else {
                    $activity->load('orgMember:id,title as name,email,logo as image');
                    $activity->setRelation('member', $activity->orgMember);
                    $activity->setRelation('orgMember', null); // Optional: hide original
                }
                return $activity;
            });

            if ($activities->total() == 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($activities, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getTopPromotersData()
    {
        // جلب أفضل 5 مروجين بناءً على مجموع المبيعات من عمليات الشراء
        $topPromoters = PromotionActivity::with('promoter')
            ->select('promoter_id')
            ->where('activity_type', 'purchase')
            ->groupBy('promoter_id')
            ->orderByRaw('SUM(commission_amount) DESC')
            ->limit(5)
            ->get();

        // تحويل البيانات للشكل المطلوب
        $promotersData = $topPromoters->map(function ($activity) {
            return [
                'id' => $activity->promoter->id,
                'name' => $activity->promoter->name,
                'sales' => $activity->where('activity_type', 'purchase')->sum('commission_amount'), // مجموع المبيعات
                'avatar' => $activity->promoter->image ?? '/defaults/male-noimage.jpg',
                'trend' => $activity->where('activity_type', 'purchase')->pluck('commission_amount')->toArray(), // بيانات الاتجاه (trend)
            ];
        });

        $finalData = [
            'data' => $promotersData,
            'total' => Promoter::count()
        ];

        return $this->successResponse($finalData, 200);
    }



    public function getPromoterData(Request $request)
    {
        try {

            $request->validate([
                'account_type' => 'required|in:user,organization',
                'account_id' => 'required|integer',
                'page' => 'nullable|integer',
            ]);

            // ===============================
            // Find promoter profile
            // ===============================
            $promoter = Promoter::where('promoter_id', $request->account_id)
                ->where('promoter_type', $request->account_type)
                ->first();

            if (!$promoter) {
                return $this->errorResponse('Promoter profile not found.', 404);
            }

            // ===============================
            // Fetch paginated activities
            // ===============================
            $activities = PromotionActivity::where('promoter_id', $promoter->id)
                ->where('promoter_type', $request->account_type)
                ->orderBy('created_at', 'desc')
                ->paginate(15, ['*'], 'page', $request->page);

            $loopData = $activities->items();

            // ===============================
            // Loop through activities
            // If signup → load member data
            // ===============================
            foreach ($loopData as $activity) {

                // Default empty member
                $activity->member = null;

                if ($activity->activity_type === 'signup') {

                    if ($activity->member_type == 'user') {
                        $activity->member = User::select('id', 'name', 'email', 'image', 'phone')
                            ->find($activity->member_id);
                    }

                    if ($activity->member_type == 'organization') {
                        $activity->member = Organization::select('id', 'title as name', 'email', 'logo as image', 'phone_number as phone')
                            ->find($activity->member_id);
                    }
                }
            }

            return $this->paginationResponse($activities, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getTopReferredBuyers(Request $request)
    {
        try {
            $request->validate([
                'promoter_id'   => 'required|integer',
                'promoter_type' => 'required|in:user,organization'
            ]);

            // ==============================
            // Step 1: Get all purchase activities for promoter
            // ==============================
            $purchases = PromotionActivity::where('promoter_id', $request->promoter_id)
                ->where('promoter_type', $request->promoter_type)
                ->where('activity_type', 'purchase')
                ->whereNotNull('member_id')
                ->whereNotNull('member_type')
                ->get();

            if ($purchases->isEmpty()) {
                return $this->successResponse([], 200);
            }

            // ==============================
            // Step 2: Group by member_id + member_type
            // ==============================
            $grouped = $purchases->groupBy(
                fn($item) =>
                $item->member_type . '_' . $item->member_id
            );

            // ==============================
            // Step 3: Build summary for each member
            // ==============================
            $members = $grouped->map(function ($items, $key) {

                $first = $items->first();
                $memberId = $first->member_id;
                $memberType = $first->member_type;

                // ==============================
                // Step 4: Fetch member data based on type
                // ==============================
                if ($memberType === 'user') {
                    $memberData = User::select('id', 'name', 'email', 'phone', 'image')
                        ->find($memberId);
                } else {
                    $memberData = Organization::select('id', 'title as name', 'email', 'phone_number as phone', 'logo as image')
                        ->find($memberId);
                }

                return [
                    'member_id'   => $memberId,
                    'member_type' => $memberType,
                    'member'      => $memberData,
                    'purchases'   => $items->count(),
                    'total_spent' => $items->sum('commission_amount'), // أو orderAmount لو موجود
                    'last_purchase_at' => $items->max('activity_at')
                ];
            });

            // ==============================
            // Step 5: Sort by total spent & limit to top 5
            // ==============================
            $topFive = $members
                ->sortByDesc('total_spent')
                ->take(5)
                ->values();

            return $this->successResponse($topFive, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function store(StorePromoterActivity $request)
    {
        try {

            $data = $request->validated();

            $promoter = Promoter::where('referral_code', $request->ref_code)->first();

            if (!$promoter) {
                return $this->errorResponse('Promoter not found', 404);
            }



            $ipExists = PromotionActivity::where('ip_address', $request->getClientIp())->where('promoter_id', $promoter->promoter_id)->where('promoter_type', $promoter->promoter_type)->first();
            $ratios = PromoterRatio::find(1);



            if ($promoter->status == 'disabled') {
                return $this->errorResponse('Promoter is disabled', 403);
            }


            $twodaysAgo = Carbon::now()->subDays(2);

            if ($request->activity_type == 'visit' && $ipExists && $ipExists->activity_at > $twodaysAgo) {
                return $this->errorResponse('IP already exists', 403);
            }

            $meta_data = [
                'ip_address' => $request->getClientIp(),
                'country' => $request->country ?? 'بلد غير محدد',
                'device_type' => $request->device_type ?? 'غير محدد',
                'ref_code' => $request->ref_code,
                'activity_type' => $request->activity_type,
                'browser' => $request->browser
            ];

            $data['promoter_id'] = $promoter->promoter_id;
            $data['promoter_type'] = $promoter->promoter_type;
            $data['ip_address'] = $request->getClientIp();
            $data['commission_amount'] = $ratios->visit_ratio ?? 0;
            $data['metadata'] = $meta_data;
            $data['is_active'] = true;
            $data['activity_at'] = now();

            $activity = PromotionActivity::create($data);

            return $this->successResponse($activity, 201);
        } catch (Exception $e) {
            Log::error("Promoter Activity Error", [
                'error' => $e->getMessage(),
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
