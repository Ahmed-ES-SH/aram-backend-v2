<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use App\Models\ProvisionalData;
use Illuminate\Support\Facades\Log;

class ProvisionalService
{
    public function create(PaymentDTO $dto, ?string $invoiceNumber = null, ?string $serviceOrderId = null): ProvisionalData
    {
        $metadata = $this->buildMetadata($dto, $invoiceNumber, $serviceOrderId);
        return ProvisionalData::create([
            'uniqueId' => uniqid('provisional_data_'),
            'payment_id' => random_int(1000000000, 9999999999),
            'metadata' => json_encode($metadata),
            'ref_code' => $dto->refCode,
            'expire_at' => now()->addMinutes(60),
            'service_order_id' => $serviceOrderId,
        ]);
    }

    private function buildMetadata(PaymentDTO $dto, ?string $invoiceNumber, ?string $serviceOrderId): array
    {
        // This resembles buildCustomerMetadata in original code but also needed for ProvisionalData metadata field
        return [
            'data_type' => $dto->dataType,
            'items' => $dto->getCurrentDetails(),
            'country' => $dto->country,
            'ip_address' => $dto->ipAddress,
            'device_type' => $dto->deviceType,
            'total_invoice' => $dto->totalInvoice,
            'before_discount' => $dto->beforeDiscount,
            'discount' => $dto->discount,
            'invoice_id' => $invoiceNumber,
            'service_order_id' => $serviceOrderId,
            // Additional customer info could be added here if needed to match previous logic perfectly,
            // but the original code separated customer metadata for Thawani vs provisional metadata.
            // The original createProvisionalData used $metaData array constructed in createSession.
        ];
    }

    public function updateMetadata(ProvisionalData $provisionalData, array $newDetails): void
    {
        $provisionalData->update(['metadata' => json_encode($newDetails)]);
    }
}
