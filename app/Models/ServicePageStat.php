<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePageStat extends Model
{
    protected $fillable = [
        'service_page_id',
        'number',
        'label_ar',
        'label_en',
        'order',
    ];

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }
}
