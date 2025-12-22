<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCategory extends Model
{
    protected $fillable = ['coupon_id', 'subcategory_id'];
}
