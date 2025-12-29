<?php

namespace App\Http\Services;

use App\Models\Card;
use App\Models\Coupon;
use App\Models\CouponOrganization;
use App\Models\CouponUsage;
use App\Models\CouponUser;
use App\Models\OwnedCard;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CouponUsageService
{
    /**
     * Apply the coupon benefit and record usage.
     * This method handles the Transaction.
     *
     * @param User $user
     * @param Coupon $coupon
     * @param int|null $distributingOrgId
     * @param array $data Additional data needed for usage (e.g. card_id)
     * @return array Result data
     * @throws Exception
     */
    public function apply(User $user, Coupon $coupon, ?int $distributingOrgId = null, array $data = []): array
    {
        return DB::transaction(function () use ($user, $coupon, $distributingOrgId, $data) {

            // Handle specific benefit types
            if ($coupon->benefit_type === 'free_card') {
                $this->applyFreeCardBenefit($user, $coupon, $data);
            }

            // Record the usage securely
            $this->recordUsage($user, $coupon, $distributingOrgId);

            return ['status' => 'success', 'message' => 'تم تطبيق الكوبون بنجاح'];
        });
    }

    private function recordUsage(User $user, Coupon $coupon, ?int $distributingOrgId): void
    {
        // 1. Create CouponUsage Record
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'organization_id' => $distributingOrgId, // Tracks who distributed it (or null if general/direct)
            'discount_applied' => 0, // Default to 0 for free_card, or calculation logic for others
        ]);

        // 2. Increment User Counter
        // We only care if there is a specific assignment record to increment
        CouponUser::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->increment('current_usage');

        // 3. Increment Organization Counter (Critical for distributed limits)
        if ($distributingOrgId) {
            CouponOrganization::where('organization_id', $distributingOrgId)
                ->where('coupon_id', $coupon->id)
                ->increment('current_usage');
        } elseif ($user->account_type === 'organization') {
            // Direct usage by Organization account (if applicable)
            CouponOrganization::where('organization_id', $user->id)
                ->where('coupon_id', $coupon->id)
                ->increment('current_usage');
        }
    }

    private function applyFreeCardBenefit(User $user, Coupon $coupon, array $data): void
    {
        $cardId = $data['card_id'] ?? null;
        if (!$cardId) {
            throw new Exception("بيانات البطاقة مطلوبة", 400);
        }

        $card = Card::findOrFail($cardId);

        // Calculate Duration
        $durationRaw = $card->duration ?? null;
        $months = 12; // Default
        if ($durationRaw && preg_match('/(\d+)/', $durationRaw, $matches)) {
            $months = intval($matches[1]);
        }

        $issueDate = Carbon::now();
        $expiryDate = (clone $issueDate)->addMonths($months);

        // Generate Card details
        $cardNumber = implode('', [
            str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
        ]);
        $cvv = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);

        OwnedCard::create([
            'cvv' => $cvv,
            'owner_id' => $user->id,
            'owner_type' => $user->account_type ?? 'user',
            'issue_date' => $issueDate,
            'usage_limit' => $coupon->usage_limit, // Or should this be checking card logic? Using coupon limit as requested in old code logic.
            'expiry_date' => $expiryDate,
            'current_usage' => 0,
            'status' => 'active',
            'card_number' => $cardNumber,
            'card_id' => $cardId,
        ]);
    }
}
