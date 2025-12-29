<?php

namespace App\Listeners;

use App\Events\SubscriptionExpired;
use App\Http\Services\NotificationService;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSubscriptionExpiredNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(SubscriptionExpired $event): void
    {
        try {
            $order = ServiceOrder::find($event->serviceOrderId);

            if (!$order) {
                Log::warning("ServiceOrder not found for SubscriptionExpired event: {$event->serviceOrderId}");
                return;
            }

            // Ensure we don't send notifications if for some reason it's not expired or other checks?
            // The event implies it *just* expired.

            $sender = User::find(1); // Assuming ID 1 is the system/admin sender as per original code

            $notificationData = [
                'content' => 'انتهت مدة الاشتراك الخاص بالطلب رقم ' . $order->id,
                'recipient_id' => $order->user_id,
                'sender_id' => $sender->id,
                'recipient_type' => $order->user_type,
                'sender_type' => 'user',
            ];

            $this->notificationService->sendNotification($notificationData, $sender);

            Log::info("Sent expiration notification for Order ID: {$order->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send subscription expiration notification for Order ID {$event->serviceOrderId}: " . $e->getMessage());
        }
    }
}
