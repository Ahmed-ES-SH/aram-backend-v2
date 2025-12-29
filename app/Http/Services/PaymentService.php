<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use App\Http\Traits\ApiResponse;
use App\Models\PendingServiceOrderFile;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    use ApiResponse;

    private const UPLOAD_PATH = 'uploads/service-tracking';

    public function __construct(
        protected InvoiceService $invoiceService,
        protected ProvisionalService $provisionalService,
        protected PromotionService $promotionService,
        protected ThawaniService $thawaniService
    ) {}

    /**
     * Create a payment session for the given request.
     */
    public function createSession(Request $request)
    {
        try {
            $dto = PaymentDTO::fromRequest($request);
            $user = $request->user();

            $transactionResult = DB::transaction(function () use ($dto, $user) {
                $invoice = $this->invoiceService->create($dto);

                $order = $this->createServiceOrderIfNeeded($dto, $user, $invoice);

                $provisionalData = $this->provisionalService->create(
                    $dto,
                    $invoice->invoice_number,
                    $order?->id
                );

                $this->promotionService->process($dto, $provisionalData);

                if ($order && $dto->files) {
                    $this->storePendingFiles($dto->files, $order->id);
                }

                return [
                    'invoice' => $invoice,
                    'provisionalData' => $provisionalData,
                ];
            });

            return $this->createThawaniSession($request, $dto, $transactionResult);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Create a service order if the payment type is 'service'.
     */
    private function createServiceOrderIfNeeded(PaymentDTO $dto, $user, $invoice): ?ServiceOrder
    {
        if ($dto->dataType !== 'service') {
            return null;
        }

        $serviceDetails = $dto->serviceDetails;

        return ServiceOrder::create([
            'service_page_id' => $serviceDetails['service_id'],
            'user_id' => $user->id,
            'user_type' => $user->account_type,
            'invoice_id' => $invoice->id,
            'metadata' => $serviceDetails['metadata'],
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Store pending files for a service order.
     * 
     * @param array<UploadedFile> $files
     */
    private function storePendingFiles(array $files, int $orderId): void
    {
        $storagePath = public_path(self::UPLOAD_PATH);

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        foreach ($files as $file) {
            $filename = $this->generateUniqueFilename($file);
            $fullPath = $storagePath . '/' . $filename;

            $file->move($storagePath, $filename);

            PendingServiceOrderFile::create([
                'service_order_id' => $orderId,
                'disk' => 'public',
                'file_path' => env('BACK_END_URL') . '/' . self::UPLOAD_PATH . '/' . $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => mime_content_type($fullPath),
                'size' => filesize($fullPath),
                'expires_at' => now()->addDays(1),
                'attached_at' => now(),
            ]);
        }
    }

    /**
     * Generate a unique filename for uploaded file.
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        return $originalName . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Create the Thawani payment session.
     */
    private function createThawaniSession(Request $request, PaymentDTO $dto, array $transactionResult)
    {
        $invoice = $transactionResult['invoice'];
        $provisionalData = $transactionResult['provisionalData'];

        $customerMetadata = $this->buildCustomerMetadata($request->user());

        $response = $this->thawaniService->createSession(
            $dto,
            $provisionalData->uniqueId,
            $invoice->invoice_number,
            $customerMetadata
        );

        return $response->json();
    }

    /**
     * Build customer metadata for the payment provider.
     */
    private function buildCustomerMetadata($user): array
    {
        return [
            'customer_id' => $user->id ?? null,
            'customer_name' => $user->name ?? 'Guest',
            'customer_email' => $user->email ?? null,
            'customer_type' => $user->account_type ?? 'user',
        ];
    }
}
