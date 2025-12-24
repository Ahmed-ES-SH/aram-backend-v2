<?php

namespace App\Http\Controllers;

use App\Helpers\TextNormalizer;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Card;
use App\Models\Coupon;
use App\Models\CouponCategory;
use App\Models\CouponOrganization;
use App\Models\CouponUser;
use App\Models\OwnedCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{

    use ApiResponse;

    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // ✅ Validate request inputs
            $request->validate([
                'query'        => 'nullable|string',
                'type'         => 'nullable|in:user,organization,general',
                'benefit_type' => 'nullable|in:percentage,fixed,free_card',
                'status'       => 'nullable|in:active,inactive,expired',
                'category_id'  => 'nullable|string', // IDs separated by comma
                'dateFrom'     => 'nullable|date',
                'dateTo'       => 'nullable|date',
            ]);

            // ✅ Base query
            $couponsQuery = Coupon::query();

            /**
             * ===============================
             * Case 1: If search query is provided
             * ===============================
             */
            if ($request->filled('query')) {
                $normalizedQuery = TextNormalizer::normalizeArabic($request->input('query'));

                // ✅ SQL expressions for normalizing Arabic text
                $normalizedCode = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(code, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
                $normalizedTitle = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(title, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
                $normalizedDescription = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(description, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

                // ✅ Apply search filter on code, title, description
                $couponsQuery->where(function ($q) use ($normalizedQuery, $normalizedCode, $normalizedTitle, $normalizedDescription) {
                    $q->whereRaw("$normalizedCode LIKE ?", ["%$normalizedQuery%"])
                        ->orWhereRaw("$normalizedTitle LIKE ?", ["%$normalizedQuery%"])
                        ->orWhereRaw("$normalizedDescription LIKE ?", ["%$normalizedQuery%"]);
                });
            }

            /**
             * ===============================
             * Case 2: Apply filters if provided
             * ===============================
             */

            // ✅ Filter by type
            if ($request->filled('type')) {
                $couponsQuery->where('type', $request->type);
            }

            // ✅ Filter by benefit_type
            if ($request->filled('benefitType')) {
                $couponsQuery->where('benefit_type', $request->benefitType);
            }

            // ✅ Filter by status
            if ($request->filled('status')) {
                $couponsQuery->where('status', $request->status);
            }

            // ✅ Filter by category_id (single or multiple IDs)
            if ($request->filled('category_id')) {
                $categories = explode(',', $request->category_id);

                if (count($categories) > 1) {
                    $couponsQuery->whereIn('category_id', $categories);
                } else {
                    $couponsQuery->where('category_id', $categories[0]);
                }
            }

            // ✅ Filter by start_date / end_date
            $couponsQuery->when($request->filled('dateFrom') || $request->filled('dateTo'), function ($q) use ($request) {
                $dateFrom = $request->get('dateFrom');
                $dateTo   = $request->get('dateTo');

                if ($dateFrom && $dateTo) {
                    $q->where(function ($subQ) use ($dateFrom, $dateTo) {
                        $subQ->whereBetween('start_date', [$dateFrom, $dateTo])
                            ->orWhereBetween('end_date', [$dateFrom, $dateTo]);
                    });
                } elseif ($dateFrom) {
                    $q->where(function ($subQ) use ($dateFrom) {
                        $subQ->where('start_date', '>=', $dateFrom)
                            ->orWhere('end_date', '>=', $dateFrom);
                    });
                } elseif ($dateTo) {
                    $q->where(function ($subQ) use ($dateTo) {
                        $subQ->where('start_date', '<=', $dateTo)
                            ->orWhere('end_date', '<=', $dateTo);
                    });
                }
            });

            /**
             * ===============================
             * Final Query: Add ordering
             * ===============================
             */
            $coupons = $couponsQuery
                ->withCount('subCategories')
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            // ✅ Check if data is empty
            if ($coupons->isEmpty()) {
                return $this->noContentResponse();
            }

            // ✅ Return paginated response
            return $this->paginationResponse($coupons, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function activeCoupons(Request $request)
    {
        try {
            $coupons = Coupon::where('status', 'active')
                ->with('category')
                ->where(function ($q) {
                    $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                });

            // 🔍 Search handling if query is provided
            if ($request->filled('query')) {
                $search = $request->query('query');

                $coupons->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            $coupons = $coupons->get();

            return $this->successResponse($coupons, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function accountCoupons(Request $request)
    {
        try {
            $request->validate([
                'id'   => 'required|integer',
                'type' => 'required|in:user,organization',
            ]);

            $coupons = collect();

            if ($request->type === 'user') {
                $coupons = Coupon::withCount([
                    'usages as usage_count' => function ($query) use ($request) {
                        $query->where('user_id', $request->id);
                    }
                ])
                    ->where('status', 'active')
                    ->whereHas('users', function ($q) use ($request) {
                        $q->where('users.id', $request->id);
                    })
                    ->get();
            }

            if ($request->type === 'organization') {
                $coupons = Coupon::withCount([
                    'usages as usage_count' => function ($query) use ($request) {
                        $query->where('organization_id', $request->id);
                    }
                ])
                    ->where('status', 'active')
                    ->whereHas('organizations', function ($q) use ($request) {
                        $q->where('organizations.id', $request->id);
                    })
                    ->get();
            }

            if ($coupons->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($coupons, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function checkCoupon(Request $request)
    {
        try {



            $request->validate([
                'code' => 'required|string',
                'card_id' => 'nullable|exists:cards,id',
            ]);


            $user = $request->user();

            $coupon = Coupon::where('code', $request->code)->first();

            if (!$coupon) {
                return $this->errorResponse('Invaild Coupon Code', 404);
            }

            if ($coupon->status !== 'active') {
                return $this->errorResponse('This Coupon is not active', 400);
            }

            $now = now();

            if ($coupon->start_date && $coupon->start_date > $now) {
                return $this->errorResponse('This Coupon has not started yet', 400);
            }

            if ($coupon->end_date && $coupon->end_date < $now) {
                return $this->errorResponse('This Coupon has expired', 400);
            }

            // Optional: Check global usage limit if needed
            if ($coupon->usage_limit && $coupon->usages()->count() >= $coupon->usage_limit) {
                return $this->errorResponse('This Coupon has reached its usage limit', 400);
            }


            if ($coupon->benefit_type == 'free_card' && $request->card_id) {
                $card = Card::where('id', $request->card_id)->first();
                $durationRaw = $card['duration'] ?? null;

                if ($durationRaw && preg_match('/(\d+)/', $durationRaw, $matches)) {
                    $months = intval($matches[1]);
                } else {
                    $months = 12;
                }

                $cardNumber = implode('', [
                    str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                    str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                    str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                    str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                ]);

                $cvv = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);

                $issueDate = Carbon::now();
                $expiryDate = (clone $issueDate)->addMonths($months);
                OwnedCard::create([
                    'cvv' => $cvv,
                    'owner_id' => $user->id,
                    'owner_type' => $user->account_type,
                    'issue_date' => $issueDate,
                    'usage_limit' => $coupon->usage_limit,
                    'expiry_date' => $expiryDate,
                    'current_usage' => 0,
                    'status' => 'active',
                    'card_number' => $cardNumber,
                    'card_id' => $request->card_id,
                ]);

                return $this->successResponse([], 201, 'تم إضافة البطاقة الى قسم الباطاقات الخاصة بالحساب فى لوحة التحكم');
            }

            return $this->successResponse($coupon, 200, 'Coupon is valid');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function sendCoupon(Request $request)
    {
        $validated = $request->validate([
            'users' => 'nullable|json',
            'organizations' => 'nullable|json',
            'coupon_id' => 'required|exists:coupons,id',
        ]);

        $couponId = $validated['coupon_id'];
        $users = json_decode($validated['users'] ?? '[]', true);
        $organizations = json_decode($validated['organizations'] ?? '[]', true);

        // Logical validation: at least one target must exist
        if (empty($users) && empty($organizations)) {
            throw ValidationException::withMessages([
                'targets' => 'At least one user or organization must be selected.',
            ]);
        }

        DB::transaction(function () use ($users, $organizations, $couponId) {

            if (!empty($users)) {
                $userData = collect($users)
                    ->filter(fn($u) => isset($u['id']))
                    ->map(fn($u) => [
                        'user_id' => $u['id'],
                        'coupon_id' => $couponId,
                    ])
                    ->values()
                    ->toArray();

                if (empty($userData)) {
                    throw new \RuntimeException('Invalid users payload.');
                }

                CouponUser::upsert($userData, ['user_id', 'coupon_id']);
            }

            if (!empty($organizations)) {
                $orgData = collect($organizations)
                    ->filter(fn($o) => isset($o['id']))
                    ->map(fn($o) => [
                        'organization_id' => $o['id'],
                        'coupon_id' => $couponId,
                    ])
                    ->values()
                    ->toArray();

                if (empty($orgData)) {
                    throw new \RuntimeException('Invalid organizations payload.');
                }

                CouponOrganization::upsert($orgData, ['organization_id', 'coupon_id']);
            }
        });

        return $this->successResponse(
            [],
            200,
            'Coupon successfully assigned to selected targets.'
        );
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request)
    {
        try {

            $data = $request->validated();
            // إنشاء المستخدم وملء البيانات
            $coupon = Coupon::create($data);

            // معالجة الصورة إذا تم رفعها
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $coupon, 'images/coupons', 'image');
            }


            // add subCategories if provided
            if ($request->has('sub_categories')) {
                // Insert new subCategories
                foreach ($request->sub_categories as $subCategoryId) {
                    CouponCategory::firstOrCreate(
                        [
                            'subcategory_id' => $subCategoryId,
                            'coupon_id' => $coupon->id,
                        ]
                    );
                }
            }


            // add subCategories if provided
            if ($request->has('organizations')) {
                // Insert new subCategories
                foreach ($request->organizations as $organization) {
                    CouponOrganization::firstOrCreate(
                        [
                            'organization_id' => $organization['id'],
                            'coupon_id' => $coupon->id,
                        ]
                    );
                }
            }


            // add subCategories if provided
            if ($request->has('users')) {
                // Insert new subCategories
                foreach ($request->users as $user) {
                    CouponUser::firstOrCreate(
                        [
                            'user_id' => $user['id'],
                            'coupon_id' => $coupon->id,
                        ]
                    );
                }
            }


            $coupon->save();

            return $this->successResponse($coupon, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Get card with relations
            $coupon = Coupon::with(['subCategories', 'category', 'users:id,name,image,email', 'organizations:id,title,logo,email'])->findOrFail($id);

            return $this->successResponse($coupon, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Card not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $coupon = Coupon::with('category')->findOrFail($id);

            $coupon->update($data);

            // تحديث الصورة إذا تم رفع صورة جديدة
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $coupon, 'images/coupons', 'image');
            }


            // Update subCategories if provided
            if ($request->filled('sub_categories') && is_array($request->sub_categories)) {
                // Remove old relations
                CouponCategory::where('coupon_id', $coupon->id)->delete();

                // Insert new ones
                foreach ($request->sub_categories as $subCategoryId) {
                    CouponCategory::firstOrCreate([
                        'coupon_id' => $coupon->id,
                        'subcategory_id' => $subCategoryId,
                    ]);
                }
            }


            $coupon->load(['subCategories']);


            return $this->successResponse($coupon->fresh(), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $coupon = Coupon::findOrFail($id);

            // Detach relations
            $coupon->users()->detach();
            $coupon->organizations()->detach();
            $coupon->subCategories()->detach();

            // Delete image if stored
            if ($coupon->image) {
                $this->imageservice->deleteOldImage($coupon, 'images/coupons');
            }

            // Delete coupon
            $coupon->delete();

            return response()->json([
                'success' => true,
                'message' => 'Coupon deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
