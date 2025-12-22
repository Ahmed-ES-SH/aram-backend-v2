<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageTestimonial extends Model
{
    protected $fillable = [
        'service_page_id',
        'name_ar',
        'name_en',
        'text_ar',
        'text_en',
        'rating',
        'avatar',
        'order',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }
}
