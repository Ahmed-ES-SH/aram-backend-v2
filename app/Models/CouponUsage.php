<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'user_id',
        'organization_id',
        'order_id',
        'order_amount',
        'discount_applied',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
