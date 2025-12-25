<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use App\Http\Traits\ApiResponse;
use App\Models\Invoice;
use App\Models\PromoterRatio;
use App\Models\PromotionActivity;
use App\Models\ProvisionalData;
use App\Models\ServiceOrder;
use App\Models\ServicePage;
use App\Models\ServiceTracking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ProcessServicePayment
{
    use ApiResponse;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function processServicePayment($data)
    {
        try {

            $user = $data->user();

            $provisionalData = ProvisionalData::where('uniqueId', $data['provisionalData_id'])->firstOrFail();
            $decodedMetadata = json_decode(
                $provisionalData['metadata'],
                true
            );

            $service_id = $decodedMetadata['items']['service_id'];
            // Retrieve activity_id from ProvisionalData details
            $metadata = json_decode($provisionalData->metadata, true);

            $activityId = $metadata['activity_id'] ?? null;

            $activity = $activityId
                ? PromotionActivity::where('id', $activityId)->firstOrFail()
                : null;

            $invoice = Invoice::where('invoice_number', $data['invoice_number'])->firstOrFail();

            $ratios = PromoterRatio::find(1);

            if ($invoice->status == 'paid') {
                return $this->errorResponse("This Invoice is already Finshied .", 500);
            }


            $service = ServicePage::where('id', $service_id)->firstOrFail();

            DB::beginTransaction();

            $adminsIds = User::where('role', 'admin')->pluck('id')->toArray();
            $sender = User::where('id', '1')->where('role', 'admin')->first();



            $notificationData = [
                'user_ids' => $adminsIds,
                'sender_type' => 'user',
                'content' => "تم ارسال طلب خدمة جديدة حيث تم طلب الخدمه : " . $service->slug,
            ];

            $this->notificationService->sendMultipleNotifications($notificationData, $sender);


            $order = ServiceOrder::create([
                'service_page_id' => $service_id,
                'user_id' => $user->id,
                'user_type' => $user->account_type,
                'invoice_id' => $invoice->id,
                'metadata' => $metadata,
                'status' => 'pending',
            ]);

            // Create Default Service Tracking
            ServiceTracking::create([
                'service_id' => $service_id,
                'user_id' => $user->id,
                'user_type' => $user->account_type,
                'service_order_id' => $order->id,
                'invoice_id' => $invoice->id,
                'status' => ServiceTracking::STATUS_PENDING,
                'current_phase' => ServiceTracking::PHASE_INITIATION,
                'metadata' => ['initial_setup' => true],
            ]);


            $invoice->update(['status' => 'paid', 'payment_date' => now()]);

            if ($activity) {

                // Fallback IP
                $ip = $metadata->ip_address
                    ?? ($data ? $data->ip() : null);

                // Fallback Device Type
                $device = $metadata->device_type
                    ?? ($data ? $data->header('User-Agent') : null);

                $activity->update([
                    'is_active' => true,
                    'commission_amount' => $ratios->purchase_ratio,
                    'country' => $metadata->country ?? null,
                    'ip_address' => $ip ?? null,
                    'device_type' => $device ?? null,
                    'activity_at' => now(),
                ]);
            }

            $provisionalData->delete();

            DB::commit();

            return $this->successResponse("Service Payment Processed Successfully.");
        } catch (Exception $e) {
            // Log error if needed
            // If Thawani failed, we might want to void the invoice? 
            // For now, returning 500 as per original behavior.
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
