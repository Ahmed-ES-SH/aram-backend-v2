<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePageSolutionSection extends Model
{
    protected $fillable = [
        'service_page_id',
        'title_ar',
        'title_en',
        'subtitle_ar',
        'subtitle_en',
        'cta_text_ar',
        'cta_text_en',
        'preview_image',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(ServicePageSolutionFeature::class, 'solution_section_id')->orderBy('order');
    }
}
