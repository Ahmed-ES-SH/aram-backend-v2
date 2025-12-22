<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageSolutionFeature extends Model
{
    protected $fillable = [
        'solution_section_id',
        'feature_key',
        'icon',
        'color',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'preview_image',
        'order',
    ];

    public function solutionSection(): BelongsTo
    {
        return $this->belongsTo(ServicePageSolutionSection::class, 'solution_section_id');
    }
}
