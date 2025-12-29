<?php

namespace App\Http\Services;

use App\Models\Coupon;
use App\Models\CouponOrganization;
use App\Models\CouponUsage;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;

class CouponValidationService
{
    /**
     * Validate the coupon status, dates, and strict usage limits.
     * Use this AFTER the user has been authorized.
     *
     * @param Coupon $coupon
     * @param int|null $distributingOrgId The organization ID if this coupon was distributed by one.
     * @throws Exception
     */
    public function validate(Coupon $coupon, ?int $distributingOrgId = null): void
    {
        $now = now();

        // 1. Status Check
        if ($coupon->status !== 'active') {
            throw new Exception('هذا الكوبون غير مفعل', Response::HTTP_BAD_REQUEST);
        }

        // 2. Date Checks
        if ($coupon->start_date && $coupon->start_date > $now) {
            throw new Exception('لم يبدأ تاريخ صلاحية هذا الكوبون بعد', Response::HTTP_BAD_REQUEST);
        }

        if ($coupon->end_date && $coupon->end_date < $now) {
            throw new Exception('انتهت صلاحية هذا الكوبون', Response::HTTP_BAD_REQUEST);
        }

        // 3. Global Usage Limit Check
        // We check the actual recorded usages in coupon_usages table
        if ($coupon->usage_limit) {
            $globalUsageCount = CouponUsage::where('coupon_id', $coupon->id)->count();
            if ($globalUsageCount >= $coupon->usage_limit) {
                throw new Exception('تجاوز هذا الكوبون الحد الأقصى للاستخدام', Response::HTTP_BAD_REQUEST);
            }
        }

        // 4. Organization Usage Limit Check (Distributed usage)
        // If this coupon comes from an organization (distributed), we must check that organization's specific limit.
        if ($distributingOrgId) {
            $this->checkOrganizationLimit($coupon, $distributingOrgId);
        }
    }

    private function checkOrganizationLimit(Coupon $coupon, int $orgId): void
    {
        $orgRelation = CouponOrganization::where('coupon_id', $coupon->id)
            ->where('organization_id', $orgId)
            ->first();

        // If the *Organization* has a limit for this coupon (not the global limit)
        if ($orgRelation && $orgRelation->usage_limit) {
            // Count total usages attributed to this organization:
            // 1. Usages by the organization itself (user_id is null, org_id is set)
            // 2. Usages by users where organization_id matches (distributed usages)

            $totalOrgUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('organization_id', $orgId)
                ->count();

            if ($totalOrgUsage >= $orgRelation->usage_limit) {
                throw new Exception('تم الوصول إلى الحد الأقصى لاستخدام الكوبون لهذه المنظمة', Response::HTTP_BAD_REQUEST);
            }
        }
    }
}
