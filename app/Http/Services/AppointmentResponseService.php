<?php

namespace App\Http\Services;

use App\Http\Traits\ApiResponse;
use App\Models\Appointment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Exception;

class AppointmentResponseService
{
    use ApiResponse;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Respond to a user appointment request by the organization.
     *
     * @param Appointment $appointment
     * @param array $data ['status' => 'accepted'|'rejected', 'organization_notes' => optional string]
     * @param Organization $organization
     * @return array
     */
    public function respondToAppointment(Appointment $appointment, array $data, Organization $organization)
    {
        try {
            // 1. Validate input
            $validator = Validator::make($data, [
                'status' => 'required|in:confirmed,rejected',
                'organization_notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'code' => 422,
                    'errors' => $validator->errors(),
                ];
            }

            $validated = $validator->validated();

            // 2. Ensure appointment belongs to this organization
            if ($appointment->organization_id !== $organization->id) {
                return [
                    'success' => false,
                    'code' => 403,
                    'errors' => [
                        'ar' => 'هذا الموعد لا يخص هذا المركز.',
                        'en' => 'This appointment does not belong to this organization.',
                    ],
                ];
            }

            // 3. Ensure appointment is still pending
            if ($appointment->status !== 'pending') {
                return [
                    'success' => false,
                    'code' => 409,
                    'errors' => [
                        'ar' => 'تم الرد على هذا الموعد مسبقًا.',
                        'en' => 'This appointment has already been responded to.',
                    ],
                ];
            }

            // 4. Start transaction
            return DB::transaction(function () use ($appointment, $validated, $organization) {
                Log::info("Starting transaction for appointment ID: {$appointment->id}");

                // 4.1 Update appointment status
                $appointment->update([
                    'status' => $validated['status'],
                    'organization_notes' => $validated['organization_notes'] ?? null,
                    'responded_at' => now(),
                ]);
                Log::info("Appointment ID: {$appointment->id} updated to status: {$validated['status']}");

                // 4.2 Send internal notification to user
                $user = User::find($appointment->user_id);

                if (!$user) {
                    Log::error("User not found for appointment ID: {$appointment->id}");
                } else {
                    Log::info("User found for appointment ID: {$appointment->id}. Email: {$user->email}");
                }

                $notificationData = [
                    'sender_type' => 'organization',
                    'sender_id' => $organization->id,
                    'recipient_type' => 'user',
                    'recipient_id' => $user->id,
                    'content' => $validated['status'] === 'confirmed'
                        ? ($organization->accaptable_message ?? "تم قبول الحجز الخاص بك.")
                        : ($organization->unaccaptable_message ?? "تم رفض الحجز الخاص بك."),
                ];

                $this->notificationService->sendNotification($notificationData, $organization);
                Log::info("Notification sent (internal) for appointment ID: {$appointment->id}");

                // 4.3 Send email to user
                if ($user && $user->email) {
                    Log::info("Attempting to send email to: {$user->email}");
                    try {
                        Mail::send('emails.appointment_response', [
                            'organization' => $organization,
                            'appointment' => $appointment,
                            'status' => $validated['status'],
                        ], function ($message) use ($user, $validated) {
                            $subject = $validated['status'] === 'confirmed'
                                ? 'Your Appointment Has Been Confirmed'
                                : 'Your Appointment Has Been Rejected';

                            $message->to($user->email, $user->name)->subject($subject);
                        });
                        Log::info("Mail::send executed successfully for {$user->email}");
                    } catch (Exception $e) {
                        Log::error('Email sending failed (Appointment Response): ' . $e->getMessage());
                        Log::error($e->getTraceAsString());
                    }
                } else {
                    Log::warning("Skipping email: User or User Email missing for appointment ID: {$appointment->id}");
                }

                return [
                    'success' => true,
                    'appointment' => $appointment,
                ];
            });
        } catch (Exception $e) {
            Log::error('Appointment response failed: ' . $e->getMessage());

            return [
                'success' => false,
                'code' => 500,
                'errors' => [
                    'ar' => 'حدث خطأ أثناء الرد على الطلب.',
                    'en' => 'An error occurred while responding to the appointment.',
                    'debug' => $e->getMessage(),
                ],
            ];
        }
    }
}
