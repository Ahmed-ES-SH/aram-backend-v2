<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use App\Http\Traits\ApiResponse;
use App\Models\Invoice;
use App\Models\PendingServiceOrderFile;
use App\Models\PromoterRatio;
use App\Models\PromotionActivity;
use App\Models\ProvisionalData;
use App\Models\ServiceOrder;
use App\Models\ServicePage;
use App\Models\ServiceTracking;
use App\Models\ServiceTrackingFile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

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
            $notificationData = null;
            $sender = null;

            DB::transaction(function () use ($data, $user, &$notificationData, &$sender) {

                // 1. Lock invoice first
                $invoice = Invoice::where('invoice_number', $data['invoice_number'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. If already paid → exit
                if ($invoice->status === 'paid') {
                    throw new Exception("This Invoice is already finished.");
                }

                // 3. Validate provisional data
                $provisionalData = ProvisionalData::where('uniqueId', $data['provisionalData_id'])
                    ->firstOrFail();

                $decodedMetadata = json_decode($provisionalData['metadata'], true);
                $service_id = $decodedMetadata['items']['service_id'];
                $activityId = $decodedMetadata['activity_id'] ?? null;

                $service = ServicePage::where('id', $service_id)->firstOrFail();
                Log::info($service);
                $order = ServiceOrder::where('id', $provisionalData->service_order_id)->firstOrFail();

                $activity = $activityId
                    ? PromotionActivity::where('id', $activityId)->firstOrFail()
                    : null;

                $ratios = PromoterRatio::find(1);

                // 4. Update invoice → paid
                $invoice->update([
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);

                // 5. Create service tracking
                $serviceTracking = ServiceTracking::create([
                    'service_id' => $service_id,
                    'user_id' => $user->id,
                    'user_type' => $user->account_type,
                    'service_order_id' => $order->id,
                    'invoice_id' => $invoice->id,
                    'status' => ServiceTracking::STATUS_PENDING,
                    'current_phase' => ServiceTracking::PHASE_INITIATION,
                    'metadata' => ['initial_setup' => true],
                ]);

                // 6. Attach files
                $pendingOrderFiles = PendingServiceOrderFile::where('service_order_id', $order->id)->get();

                foreach ($pendingOrderFiles as $file) {
                    $isImage = in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);
                    ServiceTrackingFile::create([
                        'service_tracking_id' => $serviceTracking->id,
                        'file_type' => $isImage ? 'design_file' : 'attachment',
                        'path' => $file->file_path,
                        'original_name' => $file->original_name,
                        'mime_type' => $file->mime_type,
                        'size' => $file->size,
                        'uploaded_by' => $user->id,
                        'uploaded_by_type' => $user->account_type,
                    ]);
                    $file->delete();
                }

                // 7. Update order
                $order->update([
                    'payment_status' => 'paid',
                    'subscription_status' => $service->type === 'one_time' ? null : 'active',
                    'subscription_start_time' => now(),
                    'subscription_end_time' => $service->type === 'one_time' ? null : now()->addDays(30),
                ]);

                // 8. Update activity
                if ($activity) {
                    $ip = $decodedMetadata['ip_address'] ?? ($data ? $data->ip() : null);
                    $device = $decodedMetadata['device_type'] ?? ($data ? $data->header('User-Agent') : null);

                    $activity->update([
                        'is_active' => true,
                        'commission_amount' => $ratios->purchase_ratio,
                        'country' => $decodedMetadata['country'] ?? null,
                        'ip_address' => $ip ?? null,
                        'device_type' => $device ?? null,
                        'activity_at' => now(),
                    ]);
                }

                // 9. Delete provisional data
                $provisionalData->delete();

                // Prepare notification data for afterCommit
                $adminsIds = User::where('role', 'admin')->pluck('id')->toArray();
                $superAdminIds = User::where('role', 'super_admin')->pluck('id')->toArray();
                $allAdminIds = array_merge($adminsIds, $superAdminIds);
                $sender = User::where('id', 1)->where('role', 'admin')->first();

                $notificationData = [
                    'user_ids' => $allAdminIds,
                    'sender_type' => 'user',
                    'content' => "تم ارسال طلب خدمة جديدة حيث تم طلب الخدمه : " . $service->slug,
                ];


                $this->notificationService->sendMultipleNotifications($notificationData, $sender);
            });




            return $this->successResponse("Service Payment Processed Successfully.");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
