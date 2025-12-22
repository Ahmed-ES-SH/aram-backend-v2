<?php

namespace App\Http\Services;

use App\Http\Services\NotificationService;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CancelAppointmentService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Cancel an appointment safely and send notification.
     */
    public function cancelAppointment(array $data)
    {
        DB::beginTransaction();

        try {
            $appointment = Appointment::findOrFail($data['appointment_id']);

            // Validate that the canceler is one of the parties
            if (
                ($data['cancler_type'] === 'user' && $appointment->user_id !== $data['cancler_id']) ||
                ($data['cancler_type'] === 'organization' && $appointment->organization_id !== $data['cancler_id'])
            ) {
                throw new Exception('You are not authorized to cancel this appointment.');
            }

            // Determine the new status
            $newStatus = $data['cancler_type'] == 'user'
                ? 'cancelled_by_user'
                : 'cancelled_by_org';

            // Update the appointment status
            $appointment->update([
                'status' => $newStatus,
                'cancelled_at' => now(),
            ]);

            // Format date safely
            $dateFormatted = \Carbon\Carbon::parse($appointment->start_time)->format('Y-m-d H:i');

            // Prepare notification data
            if ($data['cancler_type'] == 'user') {
                $organization = Organization::find($appointment->organization_id);
                $user = User::find($appointment->user_id);

                $notificationData = [
                    'sender_type' => 'user',
                    'sender_id' => $user->id,
                    'recipient_type' => 'organization',
                    'recipient_id' => $organization->id,
                    'content' => "قام المستخدم {$user->name} بإلغاء الحجز بتاريخ {$dateFormatted}",
                ];

                $this->notificationService->sendNotification($notificationData, $user);

                Mail::send('emails.cancel_appointment', [
                    'organization' => $organization,
                    'user' => $user,
                    'appointment' => $appointment,
                    'recipient_type' => 'organization'
                ], function ($message) use ($organization, $appointment, $user) {
                    // إرسال إلى المستخدم والمركز معًا
                    $message->to([$organization->email])
                        ->subject('إلغاء الحجز - منصة آرام الخليج المحدودة');
                });
            } else {
                $organization = Organization::find($appointment->organization_id);
                $user = User::find($appointment->user_id);

                $notificationData = [
                    'sender_type' => 'organization',
                    'sender_id' => $organization->id,
                    'recipient_type' => 'user',
                    'recipient_id' => $user->id,
                    'content' => "قام المركز {$organization->title} بإلغاء الحجز بتاريخ {$dateFormatted}",
                ];

                $this->notificationService->sendNotification($notificationData, $organization);

                Mail::send('emails.cancel_appointment', [
                    'organization' => $organization,
                    'user' => $user,
                    'appointment' => $appointment,
                    'recipient_type' => 'user'
                ], function ($message) use ($organization, $appointment, $user) {
                    // إرسال إلى المستخدم والمركز معًا
                    $message->to([$user->email])
                        ->subject('إلغاء الحجز - منصة آرام الخليج المحدودة');
                });
            }



            DB::commit();

            return [
                'success' => true,
                'message' => 'The appointment was cancelled successfully.',
                'appointment' => $appointment
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
