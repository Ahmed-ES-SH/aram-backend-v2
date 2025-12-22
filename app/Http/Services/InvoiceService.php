<?php

namespace App\Http\Services;

use App\DTOs\PaymentDTO;
use App\Models\Invoice;

class InvoiceService
{
    public function create(PaymentDTO $dto): Invoice
    {
        return Invoice::create([
            'invoice_number' => uniqid('invoice_'),
            'total_invoice' => $dto->totalInvoice,
            'invoice_type' => $dto->invoiceType,
            'owner_id' => $dto->userId,
            'owner_type' => $dto->accountType,
            'payment_method' => $dto->paymentMethod,
            'before_discount' => $dto->beforeDiscount,
            'discount' => $dto->discount,
            'ref_code' => $dto->refCode,
        ]);
    }
}
