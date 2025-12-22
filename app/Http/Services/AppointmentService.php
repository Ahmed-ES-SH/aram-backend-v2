<?php

namespace App\Http\Services;

use App\Models\Appointment;
use App\Models\Organization;
use App\Models\User;
use App\Http\Requests\SendNotificationRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AppointmentService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new appointment and handle notifications & emails
     */
    public function create(array $data, Organization $organization)
    {
        try {
            // 1. Validate
            $validator = Validator::make($data, [
                'user_id' => 'required|exists:users,id',
                'start_time' => 'required|date_format:Y-m-d H:i',
                'end_time' => 'nullable|date_format:Y-m-d H:i:s|after:start_time',
                'user_notes' => 'nullable|string',
                'is_paid' => 'required'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'code' => 422,
                    'errors' => $validator->errors(),
                ];
            }

            $validated = $validator->validated();

            // 2. Check working hours
            $date = Carbon::parse($validated['start_time'])->toDateString();
            $start = Carbon::parse($validated['start_time']);
            $end = isset($validated['end_time'])
                ? Carbon::parse($validated['end_time'])
                : $start->copy()->addMinutes(30);

            $openAt = Carbon::parse("$date {$organization->open_at}");
            $closeAt = Carbon::parse("$date {$organization->close_at}");

            if ($start < $openAt || $end > $closeAt) {
                return [
                    'success' => false,
                    'code' => 400,
                    'errors' => [
                        'ar' => 'الوقت المحدد خارج ساعات العمل.',
                        'en' => 'Selected time is outside working hours.',
                    ],
                ];
            }


            // ✅ 4. Begin Transaction
            return DB::transaction(function () use ($validated, $organization, $start, $end) {

                // 4.1 Create appointment
                $appointment = Appointment::create([
                    'user_id' => $validated['user_id'],
                    'organization_id' => $organization->id,
                    'start_time' => $start,
                    'end_time' => $end,
                    'price' => $organization->confirmation_price,
                    'status' => 'pending',
                    'is_paid' => $validated['is_paid'],
                    'user_notes' => $validated['user_notes'] ?? null,
                ]);

                // 4.2 Send internal notification (inside transaction)
                $notificationData = [
                    'sender_type' => 'user',
                    'sender_id' => $validated['user_id'],
                    'recipient_type' => 'organization',
                    'recipient_id' => $organization->id,
                    'content' => "قام المستخدم بإرسال طلب حجز بتاريخ {$start->format('Y-m-d H:i')}"
                ];

                $this->notificationService->sendNotification($notificationData, User::find($validated['user_id']));


                // 4.3 Send email (inside try to avoid rollback on mail error)
                try {
                    Mail::send('emails.new_appointment', [
                        'organization' => $organization,
                        'appointment' => $appointment,
                    ], function ($message) use ($organization) {
                        $message->to($organization->email, $organization->title)
                            ->subject('New Appointment Request');
                    });
                } catch (Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage());
                }

                return [
                    'success' => true,
                    'appointment' => $appointment,
                ];
            });
        } catch (Exception $e) {
            Log::error('Appointment creation failed: ' . $e->getMessage());

            return [
                'success' => false,
                'code' => 500,
                'errors' => [
                    'ar' => 'حدث خطأ أثناء إنشاء الحجز.',
                    'en' => 'An error occurred while creating the appointment.',
                    'debug' => $e->getMessage(),
                ],
            ];
        }
    }
}
