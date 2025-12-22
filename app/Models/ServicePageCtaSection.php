<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageCtaSection extends Model
{
    protected $fillable = [
        'service_page_id',
        'testimonial_title_ar',
        'testimonial_title_en',
        'cta_title_ar',
        'cta_title_en',
        'cta_subtitle_ar',
        'cta_subtitle_en',
        'cta_button1_ar',
        'cta_button1_en',
        'cta_button2_ar',
        'cta_button2_en',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }
}
