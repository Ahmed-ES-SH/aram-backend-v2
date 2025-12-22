<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageHeroSection extends Model
{
    protected $fillable = [
        'service_page_id',
        'badge_ar',
        'badge_en',
        'title_ar',
        'title_en',
        'subtitle_ar',
        'subtitle_en',
        'description_ar',
        'description_en',
        'watch_btn_ar',
        'watch_btn_en',
        'explore_btn_ar',
        'explore_btn_en',
        'hero_image',
        'background_image',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }
}
