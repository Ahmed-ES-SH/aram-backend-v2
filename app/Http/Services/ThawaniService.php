<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ThawaniService
{
    public function createSession(PaymentDTO $dto, string $provisionalId, string $invoiceNumber, array $customerMetadata = []): Response
    {
        $currentDetails = $dto->getCurrentDetails();
        $products = $this->formatProducts($dto, $currentDetails);
        $total = $this->calculateTotal($dto, $currentDetails);

        // Merge customer metadata with specific payment info
        $metadata = array_merge($customerMetadata, [
            'invoice_id' => $invoiceNumber,
            'provisional_data_id' => $provisionalId,
            'customer_id' => $dto->userId, // Add basic customer tracking to metadata for webhook
            'payment_type' => $dto->dataType,
        ]);

        return Http::withOptions(['verify' => false])
            ->withHeaders([
                'thawani-api-key' => env('THAWANI_SECRET_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('THAWANI_PUBLISHABLE_TEST_URL') . '/checkout/session', [
                'client_reference_id' => uniqid('order_'),
                'mode' => 'payment',
                'products' => $products,
                'success_url' => env('FRONTEND_URL') . "/successpayment?total_invoice=$total&provisionalData_id=$provisionalId&payment_type=$dto->dataType&invoice_number=$invoiceNumber",
                'cancel_url' => env('FRONTEND_URL') . "/paymentfailed?total_invoice=$total",
                'metadata' => $metadata,
            ]);
    }

    private function formatProducts(PaymentDTO $dto, ?array $currentDetails): array
    {
        if ($dto->dataType === 'book' && $currentDetails) {
            return [[
                'name' => mb_substr($currentDetails['orgTitle'] ?? 'Book', 0, 34, 'UTF-8') . '...',
                'is_paid' => $currentDetails['is_paid'] ?? false,
                'quantity' => 1,
                'unit_amount' => (int) round(($currentDetails['price'] ?? 0) * 1000),
            ]];
        }

        if ($dto->dataType === 'service' && $currentDetails) {
            return [[
                'name' => mb_substr($currentDetails['slug'] ?? 'Service', 0, 34, 'UTF-8') . '...',
                'quantity' => 1,
                'unit_amount' => (int) round(($currentDetails['price'] ?? 0) * 1000),
            ]];
        }

        if (empty($currentDetails)) {
            return [];
        }

        return collect($currentDetails)->map(fn($card) => [
            'name' => $card['title'] ?? 'Item',
            'quantity' => $card['quantity'] ?? 1,
            'unit_amount' => intval(floatval($card['price'] ?? 0) * 1000),
        ])->values()->toArray();
    }

    private function calculateTotal(PaymentDTO $dto, ?array $currentDetails): float
    {
        if ($dto->dataType === 'book' || $dto->dataType === 'service') {
            return (float) ($currentDetails['price'] ?? 0);
        }

        if (empty($currentDetails)) {
            return 0.0;
        }

        return collect($currentDetails)->reduce(
            fn($carry, $card) =>
            $carry + (($card['quantity'] ?? 1) * floatval($card['price'] ?? 0)),
            0.0
        );
    }
}
