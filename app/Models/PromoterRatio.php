<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoterRatio extends Model
{
    protected $fillable = [
        'visit_ratio',
        'signup_ratio',
        'purchase_ratio',
    ];
}
