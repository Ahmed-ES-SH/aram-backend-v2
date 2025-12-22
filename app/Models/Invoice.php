<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'total_invoice',
        'invoice_type',
        'owner_id',
        'owner_type',
        'status',
        'currency',
        'payment_method',
        'before_discount',
        'discount',
        'ref_code',
    ];
}
