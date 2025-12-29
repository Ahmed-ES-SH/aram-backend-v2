<?php

namespace App\Http\Services;

use App\Models\Coupon;
use App\Models\CouponOrganization;
use App\Models\CouponUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;

class CouponAuthorizationService
{
    /**
     * Determine if the user is authorized to use this coupon.
     * This follows strict entitlement rules:
     * 1. Direct assignment in coupon_users.
     * 2. Organization assignment in coupon_organizations (if user is org).
     * 3. Distributed assignment (Org -> User) in coupon_users.
     *
     * @param User $user
     * @param Coupon $coupon
     * @return int|null Returns the organization_id that distributed this coupon if applicable, or null if direct/org itself.
     * @throws Exception
     */
    public function authorize(User $user, Coupon $coupon): ?int
    {
        // 1. User Logic (Type User OR General)
        if ($coupon->type === 'user' || $coupon->type === 'general') {
            // Check if the user has this coupon assigned
            // Note: For organizations using a 'general' coupon, they might be in coupon_organizations
            // We need to distinguish based on who is using it.

            if ($user->account_type === 'organization' && $coupon->type === 'general') {
                return $this->authorizeOrganizationCoupon($user, $coupon);
            }

            return $this->authorizeUserCoupon($user, $coupon);
        }

        // 2. Organization Logic
        if ($coupon->type === 'organization') {
            return $this->authorizeOrganizationCoupon($user, $coupon);
        }

        throw new Exception('نوع الكوبون غير معروف', Response::HTTP_FORBIDDEN);
    }

    private function authorizeUserCoupon(User $user, Coupon $coupon): ?int
    {
        $assignment = CouponUser::where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$assignment) {
            throw new Exception('غير مصرح لك باستخدام هذا الكوبون', Response::HTTP_FORBIDDEN);
        }

        // Return the organization_id if this was distributed by an organization
        // This is crucial for usage tracking later.
        return $assignment->organization_id;
    }

    private function authorizeOrganizationCoupon(User $user, Coupon $coupon): ?int
    {
        // Case A: User is an Organization Account
        if ($user->account_type === 'organization') {
            $exists = CouponOrganization::where('coupon_id', $coupon->id)
                ->where('organization_id', $user->id)
                ->exists();

            if (!$exists) {
                throw new Exception('هذه المنظمة غير مصرح لها باستخدام هذا الكوبون', Response::HTTP_FORBIDDEN);
            }

            // Organization using its own coupon directly
            return $user->id;
        }

        // Case B: User using a coupon distributed by an Organization (inherited entitlement)
        // Even if the coupon type is 'organization', if a USER is using it,
        // it MUST be assigned to them via coupon_users table from that org.

        $assignment = CouponUser::where('coupon_id', $coupon->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$assignment || !$assignment->organization_id) {
            throw new Exception('غير مصرح لك باستخدام هذا الكوبون', Response::HTTP_FORBIDDEN);
        }

        return $assignment->organization_id;
    }
}
