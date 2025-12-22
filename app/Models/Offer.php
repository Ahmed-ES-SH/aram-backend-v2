<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'number_of_uses',
        'usage_limit',
        'discount_type',
        'discount_value',
        'code',
        'start_date',
        'end_date',
        'status',
        'organization_id',
        'category_id',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
