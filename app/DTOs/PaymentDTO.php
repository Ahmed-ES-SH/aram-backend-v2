<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class PaymentDTO
{
    public function __construct(
        public readonly ?array $cardsDetails,
        public readonly ?array $bookDetails,
        public readonly ?array $serviceDetails,
        public readonly string $dataType,
        public readonly string $accountType,
        public readonly int|string $userId,
        public readonly float $totalInvoice,
        public readonly string $invoiceType,
        public readonly ?float $beforeDiscount,
        public readonly ?float $discount,
        public readonly string $paymentMethod,
        public readonly ?string $refCode,
        public readonly ?string $country,
        public readonly ?string $ipAddress,
        public readonly ?string $deviceType,
        public readonly ?array $files,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $validated = $request->validate([
            'data_type' => 'required|in:cards,book,service',
            'account_type' => 'required|in:user,organization',
            'user_id' => 'required',
            'total_invoice' => 'required',
            'invoice_type' => 'required',
            'payment_method' => 'required',
            'cardsDetails' => 'nullable',
            'bookDetails' => 'nullable',
            'bookDetailes' => 'nullable',
            'serviceDetails' => 'nullable',
            'before_discount' => 'nullable',
            'discount' => 'nullable',
            'ref_code' => 'nullable|exists:promoters,referral_code',
            'files' => 'nullable',
            'files.*' => 'nullable|file|max:5096',
        ]);

        // Standardize decoding logic here
        $cardsRaw = $request->input('cardsDetails');
        $bookRaw = $request->input('bookDetails') ?? $request->input('bookDetailes');
        $serviceRaw = $request->input('serviceDetails');

        $cards = is_string($cardsRaw) ? json_decode($cardsRaw, true) : ($cardsRaw ?? []);
        $book = is_string($bookRaw) ? json_decode($bookRaw, true) : ($bookRaw ?? []);
        $service = is_string($serviceRaw) ? json_decode($serviceRaw, true) : ($serviceRaw ?? []);

        return new self(
            cardsDetails: $cards,
            bookDetails: $book,
            serviceDetails: $service,
            dataType: $validated['data_type'],
            accountType: $validated['account_type'],
            userId: $validated['user_id'],
            totalInvoice: (float) $validated['total_invoice'],
            invoiceType: $validated['invoice_type'],
            beforeDiscount: isset($validated['before_discount']) ? (float) $validated['before_discount'] : null,
            discount: isset($validated['discount']) ? (float) $validated['discount'] : null,
            paymentMethod: $validated['payment_method'],
            refCode: $validated['ref_code'] ?? null,
            country: $request->country ?? null,
            ipAddress: $request->ip(),
            deviceType: $request->device_type ?? null,
            files: $validated['files'] ?? null,
        );
    }

    public function getCurrentDetails(): ?array
    {
        return match ($this->dataType) {
            'cards' => $this->cardsDetails,
            'book' => $this->bookDetails,
            'service' => $this->serviceDetails,
            default => null,
        };
    }
}
