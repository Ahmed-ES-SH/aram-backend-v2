<?php

namespace App\Http\Services;

use App\Http\Traits\ApiResponse;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\ProvisionalData;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\DB;

class ProcessBookPaymentService
{

    use ApiResponse;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function processBookPayment($data)
    {
        try {
            $provisionalData = ProvisionalData::where('id', $data['provisionalData_id'])->firstOrFail();
            $invoice = Invoice::where('invoice_number', $data['invoice_number'])->firstOrFail();

            if ($invoice->status == 'paid') {
                return $this->errorResponse('This invoice has already been paid.');
            }

            $book = json_decode($provisionalData->details, true);
            $organization = Organization::findOrFail($book['orgId']);
            // نفعل المعاملة لحماية الترحيل لو فشل شيء
            DB::beginTransaction();

            $appointment = Appointment::create([
                'user_id' => $book['userId'],
                'organization_id' => $book['orgId'],
                'start_time' => $book['formatDate'],
                'price' => $book['price'],
                'user_notes' => $book['notes'],
                'status' => 'pending',
                'is_paid' => $book['is_paid']
            ]);

            if (! $appointment) {
                throw new Exception('Failed to create appointment.');
            }

            // 4.2 Send internal notification (inside transaction)
            $notificationData = [
                'sender_type' => 'user',
                'sender_id' => $book['userId'],
                'recipient_type' => 'organization',
                'recipient_id' => $book['orgId'],
                'content' => "قام المستخدم بإرسال طلب حجز بتاريخ {$book['formatDate']}"
            ];

            $this->notificationService->sendNotification($notificationData, User::find($book['userId']));

            Mail::send('emails.new_appointment', [
                'organization' => $organization,
                'appointment' => $appointment,
            ], function ($message) use ($organization) {
                $message->to($organization->email, $organization->title)
                    ->subject('New Appointment Request');
            });


            Transaction::create([
                "user_id" => $book['userId'],
                "account_type" => 'user',
                "type" => "book",
                "direction" => "out",
                "amount" => $book['price'],
                "status" => "completed",
            ]);

            Transaction::create([
                "user_id" => $book['orgId'],
                "account_type" => 'organization',
                "type" => "book",
                "direction" => "in",
                "amount" => $book['price'],
                "status" => "completed",
            ]);

            $wallet = Wallet::firstOrCreate(
                [
                    'user_id' => $book['orgId'],
                    'account_type' => 'organization',
                ],
                [
                    'available_balance' => 0,
                    'pending_balance' => 0,
                ]
            );

            // Ensure numeric value
            $amount = (float) $book['price'];

            // Safely increment pending balance
            $wallet->pending_balance = (float) $wallet->pending_balance + $amount;
            $wallet->save();


            $invoice->update(['status' => 'paid', 'payment_date' => now()]);
            $provisionalData->delete();

            DB::commit();

            return $this->successResponse($appointment, 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
