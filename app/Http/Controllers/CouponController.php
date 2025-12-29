<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Services\CouponAuthorizationService;
use App\Http\Services\CouponFetchService;
use App\Http\Services\CouponService;
use App\Http\Services\CouponUsageService;
use App\Http\Services\CouponValidationService;
use App\Http\Traits\ApiResponse;
use App\Models\Coupon;
use App\Models\CouponOrganization;
use App\Models\CouponUsage;
use App\Models\CouponUser;
use Exception;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use ApiResponse;

    protected $couponFetchService;
    protected $couponService;
    protected $couponValidationService;
    protected $couponAuthorizationService;
    protected $couponUsageService;

    public function __construct(
        CouponFetchService $couponFetchService,
        CouponService $couponService,
        CouponValidationService $couponValidationService,
        CouponAuthorizationService $couponAuthorizationService,
        CouponUsageService $couponUsageService
    ) {
        $this->couponFetchService = $couponFetchService;
        $this->couponService = $couponService;
        $this->couponValidationService = $couponValidationService;
        $this->couponAuthorizationService = $couponAuthorizationService;
        $this->couponUsageService = $couponUsageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->couponFetchService->getAllCoupons($request);
    }

    public function activeCoupons(Request $request)
    {
        return $this->couponFetchService->getActiveCoupons($request);
    }

    public function accountCoupons(Request $request)
    {
        return $this->couponFetchService->getAccountCoupons($request);
    }

    public function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'card_id' => 'nullable|exists:cards,id',
        ]);

        try {
            $coupon = Coupon::where('code', $request->code)->first();

            if (!$coupon) {
                return $this->errorResponse('كود الكوبون غير صحيح', 404);
            }

            $user = $request->user();

            // 1. Authorization: Can this user touch this coupon?
            // Returns the organization ID if distributed, or null.
            $distributingOrgId = $this->couponAuthorizationService->authorize($user, $coupon);

            // 2. Validation: Is the coupon valid (dates, status, limits)?
            $this->couponValidationService->validate($coupon, $distributingOrgId);

            // 3. Usage: Apply benefit and record usage
            // This unifies logic for Free Cards and General/Discount coupons.
            // If benefit_type is free_card, it requires card_id (validated in service or logic).

            // NOTE: This assumes checkCoupon is intended to *CONSUME* the coupon.
            $result = $this->couponUsageService->apply($user, $coupon, $distributingOrgId, $request->all());

            if ($coupon->benefit_type === 'free_card') {
                return $this->successResponse([], 201, $result['message']);
            }

            return $this->successResponse($coupon, 200, 'Code is valid and applied');
        } catch (Exception $e) {
            // Determine status code (default to 400 if 0 or strictly internal)
            $code = $e->getCode();
            if ($code < 100 || $code > 599) {
                $code = 400;
            }
            return $this->errorResponse($e->getMessage(), $code);
        }
    }

    public function sendCoupon(Request $request)
    {
        return $this->couponService->sendCoupon($request);
    }

    public function distribute(Request $request)
    {
        return $this->couponService->distributeCoupon($request);
    }

    public function store(StoreCouponRequest $request)
    {
        return $this->couponService->createCoupon($request, $request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->couponFetchService->getCouponDetails($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, $id)
    {
        return $this->couponService->updateCoupon($request, $id, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->couponService->deleteCoupon($id);
    }
}
