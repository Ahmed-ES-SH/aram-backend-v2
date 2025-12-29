<?php

namespace App\Http\Services;

use App\Helpers\TextNormalizer;
use App\Http\Traits\ApiResponse;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponFetchService
{
    use ApiResponse;

    /**
     * Get all coupons with filters.
     */
    public function getAllCoupons(Request $request)
    {
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
    }

    /**
     * Get active coupons.
     */
    public function getActiveCoupons(Request $request)
    {
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
    }

    /**
     * Get coupons for a specific account (user/organization).
     */
    public function getAccountCoupons(Request $request)
    {
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
    }

    /**
     * Get single coupon details.
     */
    public function getCouponDetails($id)
    {
        try {
            // Get card with relations
            $coupon = Coupon::with(['subCategories', 'category', 'users:id,name,image,email', 'organizations:id,title,logo,email'])->findOrFail($id);

            return $this->successResponse($coupon, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Card not found', 404);
        }
    }
}
