<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageProblemItem extends Model
{
    protected $fillable = [
        'problem_section_id',
        'icon',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'order',
    ];

    public function problemSection(): BelongsTo
    {
        return $this->belongsTo(ServicePageProblemSection::class, 'problem_section_id');
    }
}
