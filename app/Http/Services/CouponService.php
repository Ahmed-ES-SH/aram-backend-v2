<?php

namespace App\Http\Services;

use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Coupon;
use App\Models\CouponCategory;
use App\Models\CouponOrganization;
use App\Models\CouponUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CouponService
{
    use ApiResponse;

    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }

    /**
     * Create a new coupon.
     */
    public function createCoupon(Request $request, $data)
    {
        // Create coupon
        $coupon = Coupon::create($data);

        // Upload image if exists
        if ($request->hasFile('image')) {
            $this->imageservice->ImageUploaderwithvariable($request, $coupon, 'images/coupons', 'image');
        }

        // Add subCategories
        if ($request->has('sub_categories')) {
            foreach ($request->sub_categories as $subCategoryId) {
                CouponCategory::firstOrCreate(
                    [
                        'subcategory_id' => $subCategoryId,
                        'coupon_id' => $coupon->id,
                    ]
                );
            }
        }

        // Add organizations
        if ($request->has('organizations')) {
            foreach ($request->organizations as $organization) {
                CouponOrganization::firstOrCreate(
                    [
                        'organization_id' => $organization['id'],
                        'coupon_id' => $coupon->id,
                    ]
                );
            }
        }

        // Add users
        if ($request->has('users')) {
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
    }

    /**
     * Update an existing coupon.
     */
    public function updateCoupon(Request $request, $id, $data)
    {
        $coupon = Coupon::with('category')->findOrFail($id);

        $coupon->update($data);

        // Update image
        if ($request->hasFile('image')) {
            $this->imageservice->ImageUploaderwithvariable($request, $coupon, 'images/coupons', 'image');
        }

        // Update subCategories
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
    }

    /**
     * Delete a coupon.
     */
    public function deleteCoupon($id)
    {
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
    }

    /**
     * Send/Assign coupon to users or organizations.
     */
    public function sendCoupon(Request $request)
    {
        $validated = $request->validate([
            'users' => 'nullable|json',
            'organizations' => 'nullable|json',
            'coupon_id' => 'required|exists:coupons,id',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        $couponId = $validated['coupon_id'];
        $coupon = Coupon::findOrFail($couponId);
        $users = json_decode($validated['users'] ?? '[]', true);
        $organizations = json_decode($validated['organizations'] ?? '[]', true);
        $limit = $validated['usage_limit'] ?? $coupon->usage_limit ?? null;

        if (empty($users) && empty($organizations)) {
            throw ValidationException::withMessages([
                'targets' => 'At least one user or organization must be selected.',
            ]);
        }

        DB::transaction(function () use ($users, $organizations, $couponId, $limit) {
            if (!empty($users)) {
                $userData = collect($users)
                    ->filter(fn($u) => isset($u['id']))
                    ->map(fn($u) => [
                        'user_id' => $u['id'],
                        'coupon_id' => $couponId,
                        'current_usage' => 0, // ✅ Sign current_usage
                        'usage_limit' => $limit // ✅ Sign usage_limit
                    ])
                    ->values()
                    ->toArray();

                if (!empty($userData)) {
                    // upsert(values, uniqueBy, updateColumns)
                    // If we want to ensure current_usage is reset to 0, we can include it in update columns or leave update null (all columns)
                    CouponUser::upsert($userData, ['user_id', 'coupon_id'], ['usage_limit', 'current_usage']);
                }
            }

            if (!empty($organizations)) {
                $orgData = collect($organizations)
                    ->filter(fn($o) => isset($o['id']))
                    ->map(fn($o) => [
                        'organization_id' => $o['id'],
                        'coupon_id' => $couponId,
                        'current_usage' => 0, // ✅ Sign current_usage
                        'usage_limit' => $limit // ✅ Sign usage_limit
                    ])
                    ->values()
                    ->toArray();

                if (!empty($orgData)) {
                    CouponOrganization::upsert($orgData, ['organization_id', 'coupon_id'], ['usage_limit', 'current_usage']);
                }
            }
        });

        return $this->successResponse([], 200, 'Coupon successfully assigned.');
    }

    /**
     * Distribute coupon from organization to user.
     */
    public function distributeCoupon(Request $request)
    {
        // Ensure auth user is an organization
        $user = $request->user();
        if ($user->account_type !== 'organization') {
            return $this->errorResponse('Only organizations can distribute coupons.', 403);
        }

        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'user_id' => 'required|exists:users,id',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        $couponId = $request->coupon_id;
        $coupon = Coupon::findOrFail($couponId);
        $targetUserId = $request->user_id;
        $orgId = $user->id;
        $limit = $request->usage_limit ?? $coupon->usage_limit ?? null;

        // Verify Organization has this coupon
        $hasCoupon = CouponOrganization::where('coupon_id', $couponId)
            ->where('organization_id', $orgId)
            ->exists();

        if (!$hasCoupon) {
            return $this->errorResponse('You do not have this coupon to distribute.', 403);
        }

        // Assign to User with organization_id tracking
        CouponUser::updateOrCreate(
            [
                'user_id' => $targetUserId,
                'coupon_id' => $couponId,
            ],
            [
                'organization_id' => $orgId,
                'current_usage' => 0, // ✅ Initialize usage
                'usage_limit' => $limit,
            ]
        );

        return $this->successResponse([], 200, 'Coupon distributed successfully to the user.');
    }
}
