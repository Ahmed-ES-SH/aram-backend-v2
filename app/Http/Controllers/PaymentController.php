<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Services\PaymentService;
use App\Http\Services\ProcessBookPaymentService;
use App\Http\Services\ProcessCardsPaymentService;
use App\Http\Services\ProcessServicePayment;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessPaymentJob;
use Exception;

class PaymentController extends Controller
{

    use ApiResponse;

    protected $paymentService;
    protected $proccessCardsPaymentService;
    protected $proccessBookPaymentService;
    protected $processServicePayment;

    public function __construct(
        PaymentService $paymentService,
        ProcessCardsPaymentService $proccessCardsPaymentService,
        ProcessBookPaymentService $proccessBookPaymentService,
        ProcessServicePayment $processServicePayment
    ) {
        $this->paymentService = $paymentService;
        $this->proccessCardsPaymentService = $proccessCardsPaymentService;
        $this->proccessBookPaymentService = $proccessBookPaymentService;
        $this->processServicePayment = $processServicePayment;
    }

    public function createSession(Request $request)
    {
        try {
            $response = $this->paymentService->createSession($request);
            return response()->json($response);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'فشلت عملية إنشاء الجلسة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function callback(Request $request)
    {
        $validated = $request->validate([
            'provisionalData_id' => 'required|exists:provisional_data,uniqueId',
            'invoice_number' => 'required',
            'payment_type' => 'required|in:cards,book,service',
            'payment_id' => 'nullable',
            'session_id' => 'nullable',
        ]);

        $type = $validated['payment_type'];

        try {
            $response = match ($type) {
                'cards' => $this->proccessCardsPaymentService->processCardsPayment($request),
                'book' => $this->proccessBookPaymentService->processBookPayment($request),
                'service' => $this->processServicePayment->processServicePayment($request),
                default => throw new Exception('Invalid payment type provided.', 422),
            };

            return response()->json($response, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?? 500);
        }
    }


    public function webhook(Request $request)
    {
        // 1. Verify Signature
        if (!$this->verifyThawaniSignature($request)) {
            Log::error('Invalid Thawani Signature');
            return response()->json(['status' => 'error', 'message' => 'Invalid Signature'], 401);
        }

        $payload = $request->all();

        // 2. Verify Payment Status
        if (!isset($payload['data']['payment_status']) || $payload['data']['payment_status'] !== 'paid') {
            return response()->json(['status' => 'ignored']);
        }

        $metadata = $payload['data']['metadata'] ?? [];
        $paymentType = $metadata['payment_type'] ?? null;
        $invoiceNumber = $metadata['invoice_id'] ?? null;

        // 3. Idempotency Check
        if ($invoiceNumber) {
            $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
            if ($invoice && $invoice->status === 'paid') {
                return response()->json(['status' => 'success', 'message' => 'Already processed']);
            }
        }

        // 4. Prepare data for Queue
        $data = [
            'provisionalData_id' => $metadata['provisional_data_id'] ?? null,
            'invoice_number' => $invoiceNumber,
            // 'activity_id' is now retrieved from ProvisionalData inside the Job/Service
        ];

        // 5. Dispatch Job
        try {
            if ($paymentType) {
                ProcessPaymentJob::dispatch($paymentType, $data);
            } else {
                Log::warning('Payment type missing in webhook metadata');
            }
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch payment job', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'success']);
    }

    private function verifyThawaniSignature(Request $request): bool
    {
        $signature = $request->header('thawani-signature');
        $timestamp = $request->header('thawani-timestamp');
        $secret = env('THAWANI_WEBHOOK_SECRET_KEY');

        if (!$signature || !$timestamp || !$secret) {
            return false;
        }

        $payload = $request->getContent();
        $stringToSign = $payload . '-' . $timestamp;

        // 1️⃣ تحقق من hex
        $computedHex = hash_hmac('sha256', $stringToSign, $secret);
        if (hash_equals($signature, $computedHex)) {
            return true;
        }

        // 2️⃣ إذا لم ينجح، تحقق من base64
        $computedBase64 = base64_encode(hash_hmac('sha256', $stringToSign, $secret, true));
        if (hash_equals($signature, $computedBase64)) {
            return true;
        }

        // لا شيء تطابق → فشل
        Log::warning('Invalid Thawani Signature', [
            'signature' => $signature,
            'computedHex' => $computedHex,
            'computedBase64' => $computedBase64,
        ]);

        return false;
    }
}
