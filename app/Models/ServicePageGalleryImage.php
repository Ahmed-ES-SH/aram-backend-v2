<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageGalleryImage extends Model
{
    protected $fillable = [
        'service_page_id',
        'path',
        'alt_ar',
        'alt_en',
        'order',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }
}
