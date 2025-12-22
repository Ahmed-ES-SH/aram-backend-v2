<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvisionalData extends Model
{
    protected $fillable = [
        'uniqueId',
        'payment_id',
        'metadata',
        'ref_code',
        'expire_at',
    ];
}
