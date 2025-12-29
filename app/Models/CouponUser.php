<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{
    protected $fillable = ['user_id', 'coupon_id', 'organization_id', 'usage_limit', 'current_usage'];
}
