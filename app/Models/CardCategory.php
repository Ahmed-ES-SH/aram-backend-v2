<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardCategory extends Model
{
    protected $fillable = [
        'title_en',
        'title_ar',
        'bg_color',
        'icon_name',
        'is_active',
        'image',
    ];


    public function cards()
    {
        return $this->hasMany(Card::class, 'category_id');
    }
}
