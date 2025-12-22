<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponOrganization extends Model
{
    protected $fillable = [
        'coupon_id',
        'organization_id'
    ];
}
