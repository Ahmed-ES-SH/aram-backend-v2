<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'session_id',
        'amount',
        'status',
        'pending',
        'user_id',
        'user_type',
        'paid_at',
    ];
}
