<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'code',
        'type',
        'benefit_type',
        'discount_value',
        'start_date',
        'end_date',
        'category_id',
        'usage_limit',
        'status'
    ];

    // Many-to-Many with Users
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_users', 'coupon_id', 'user_id')
            ->withTimestamps();
    }

    // Many-to-Many with Centers
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'coupon_organizations', 'coupon_id', 'organization_id')
            ->withTimestamps();
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }


    public function subCategories()
    {
        return $this->belongsToMany(
            SubCategory::class,
            'coupon_categories',   // pivot table
            'coupon_id',           // FK in pivot
            'subcategory_id'             // FK in pivot
        );
    }
}
