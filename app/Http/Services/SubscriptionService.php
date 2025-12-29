<?php

namespace App\Http\Services;

use App\Models\ServiceOrder;
use App\Events\SubscriptionExpired;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Check for expired subscriptions and mark them as expired.
     * Dispatches Domain Events for side effects.
     */
    public function checkAndExpireSubscriptions(): void
    {
        // 1. Bulk Update: Mark active subscriptions as expired if time passed
        // We use chunkById to be safe, although update() usually handles it efficiently if we don't need models.
        // However, we need the IDs for the events. So we will fetch first.

        Log::info('Checking for expired subscriptions...');

        ServiceOrder::where('subscription_status', 'active')
            ->whereNotNull('subscription_end_time')
            ->where('subscription_end_time', '<', now())
            ->chunkById(100, function ($orders) {
                $expiredIds = [];

                foreach ($orders as $order) {
                    // Update status locally to avoid race conditions if possible event delays,
                    // but practically we will do a bulk update or individual updates.
                    // Individual update is better for firing model events if strictly necessary,
                    // but the plan asked for Bulk Update for performance effectively.
                    // Let's do a bulk update on the IDs we found in this chunk for atomic DB safety,
                    // OR just update the instances. 
                    // To follow the plan "Primary Operation: Perform a bulk update":

                    $expiredIds[] = $order->id;
                }

                if (!empty($expiredIds)) {
                    // Perform Bulk Update on this chunk
                    ServiceOrder::whereIn('id', $expiredIds)->update([
                        'subscription_status' => 'expired'
                    ]);

                    // Dispatch Events
                    foreach ($expiredIds as $id) {
                        event(new SubscriptionExpired($id));
                    }

                    Log::info('Expired ' . count($expiredIds) . ' subscriptions.');
                }
            });
    }
}
