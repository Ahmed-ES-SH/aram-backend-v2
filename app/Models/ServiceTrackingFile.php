<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTrackingFile extends Model
{
    protected $fillable = [
        'service_tracking_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
        'uploaded_by_type',
    ];



    public function tracking()
    {
        return $this->belongsTo(ServiceTracking::class);
    }
}
