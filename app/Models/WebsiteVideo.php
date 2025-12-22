<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVideo extends Model
{
    protected $fillable = [
        'video_id',
        'video_url',
        'video_image',
        'video_type',
        'aspect_ratio',
        'is_file',
    ];
}
