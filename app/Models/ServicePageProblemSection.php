<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePageProblemSection extends Model
{
    protected $fillable = [
        'service_page_id',
        'title_ar',
        'title_en',
        'subtitle_ar',
        'subtitle_en',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServicePageProblemItem::class, 'problem_section_id')->orderBy('order');
    }
}
