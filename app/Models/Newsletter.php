<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'section_1_title',
        'section_1_description',
        'section_1_image',
        'section_2_title',
        'section_2_description',
        'section_2_image',
        'section_3_title',
        'section_3_description',
        'section_3_image',
    ];
}
