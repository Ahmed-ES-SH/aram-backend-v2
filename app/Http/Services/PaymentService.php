<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    use ApiResponse;

    public function __construct(
        protected InvoiceService $invoiceService,
        protected ProvisionalService $provisionalService,
        protected PromotionService $promotionService,
        protected ThawaniService $thawaniService
    ) {}

    public function createSession(Request $request)
    {
        try {
            // 1. Create DTO (Internal Validation happens here)
            $dto = PaymentDTO::fromRequest($request);

            // 2. Database Operations (Transaction)
            // We return an array containing needed data for the external API call
            $transactionResult = DB::transaction(function () use ($dto) {

                // Create Invoice
                $invoice = $this->invoiceService->create($dto);

                // Create Provisional Data
                $provisionalData = $this->provisionalService->create($dto, $invoice->invoice_number);

                // Handle Promotion (Updates Provisional Data inside if needed)
                $this->promotionService->process($dto, $provisionalData);

                return [
                    'invoice' => $invoice,
                    'provisionalData' => $provisionalData,
                ];
            });

            // 3. External API Call (Outside Transaction)
            // If this fails, we have an invoice pending payment in DB (status='pending' by default in create schema usually, or null).
            // Thawani failure means user won't pay. Invoice remains unpaid.
            // This is acceptable and avoids DB locks on external calls.

            $invoice = $transactionResult['invoice'];
            $provisionalData = $transactionResult['provisionalData'];

            // Build base customer metadata (similar to original buildCustomerMetadata)
            $customerMetadata = [
                'customer_id' => $request->user()->id ?? null,
                'customer_name' => $request->user()->name ?? 'Guest',
                'customer_email' => $request->user()->email ?? null,
                'customer_type' => $request->user()->account_type ?? 'user',
            ];

            $response = $this->thawaniService->createSession(
                $dto,
                $provisionalData->uniqueId,
                $invoice->invoice_number,
                $customerMetadata
            );

            return $response->json();
        } catch (Exception $e) {
            // Log error if needed
            // If Thawani failed, we might want to void the invoice? 
            // For now, returning 500 as per original behavior.
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
