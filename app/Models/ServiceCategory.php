<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        'title_en',
        'title_ar',
        'bg_color',
        'icon_name',
        'is_active',
        'image',
    ];


    public function services()
    {
        return $this->hasMany(ServicePage::class);
    }
}
