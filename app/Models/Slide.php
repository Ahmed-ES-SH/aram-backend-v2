<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'circle_1_color',
        'circle_2_color',
        'status',
    ];


    protected  $casts = ['title' => 'array', 'description' => 'array'];
}
