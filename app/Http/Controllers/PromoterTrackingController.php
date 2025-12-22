<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Promoter;
use App\Models\PromotionActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoterTrackingController extends Controller
{
    use ApiResponse;

    public function trackVisit(Request $request)
    {
        $request->validate([
            'ref_code' => 'required|string|exists:promoters,referral_code',
        ]);

        try {
            DB::beginTransaction();

            $promoter = Promoter::where('referral_code', $request->ref_code)->firstOrFail();

            // Increment visits
            $promoter->increment('total_visits');

            // Log activity
            PromotionActivity::create([
                'promoter_type' => $promoter->promoter_type, // Assuming this is stored in promoter table or derived
                'promoter_id' => $promoter->id,
                'activity_type' => 'visit',
                'data' => [
                    'url' => $request->header('referer'),
                    'user_agent' => $request->header('User-Agent'),
                ],
                'ip_address' => $request->ip(),
                'country' => null, // Could use a geo-ip service here if available
                'device_type' => $this->getDeviceType($request->header('User-Agent')),
                'ref_code' => $request->ref_code,
            ]);

            DB::commit();

            return $this->successResponse(['message' => 'Visit tracked'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function getDeviceType($userAgent)
    {
        if (empty($userAgent)) {
            return 'unknown';
        }

        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            return 'tablet';
        }

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }
}
