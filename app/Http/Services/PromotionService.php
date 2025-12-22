<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use App\Models\Promoter;
use App\Models\PromotionActivity;
use App\Models\ProvisionalData;

class PromotionService
{
    public function process(PaymentDTO $dto, ProvisionalData $provisionalData): void
    {
        if (!$dto->refCode) {
            return;
        }

        $promoter = Promoter::where('referral_code', $dto->refCode)->where('status', 'active')->first();

        if (!$promoter) {
            return;
        }

        $activity = $this->createActivity($dto, $promoter, $provisionalData->metadata); // metadata is array in model cast? usually string in DB but we used array in createProvisionalData

        // Update provisional data metadata with activity_id
        // We need to decode if it's stored as JSON string, assuming Eloquent Accessor/Mutator handles it or raw.
        // Based on original code: $details = json_decode($provisionalData->metadata, true);
        // But ProvisionalService creates it with array. Let's assume generic loose handling.

        $details = $provisionalData->metadata; // Assuming Model casts or it's array
        if (is_string($details)) {
            $details = json_decode($details, true);
        }

        if (is_array($details)) {
            $details['activity_id'] = $activity->id;
            $provisionalData->update(['metadata' => json_encode($details)]);
        }
    }

    private function createActivity(PaymentDTO $dto, Promoter $promoter, $metadata): PromotionActivity
    {
        return PromotionActivity::create([
            'promoter_type' => $promoter->promoter_type,
            'promoter_id' => $promoter->promoter_id,
            'metadata' => $metadata, // Original code passed validated metadata array
            'activity_type' => 'purchase',
            'country' => $dto->country,
            'device_type' => $dto->deviceType,
            'ref_code' => $dto->refCode,
            'member_id' => $dto->userId,
            'member_type' => $dto->accountType,
        ]);
    }
}
