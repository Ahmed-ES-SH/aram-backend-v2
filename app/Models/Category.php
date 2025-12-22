<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = [
        'title_en',
        'title_ar',
        'bg_color',
        'icon_name',
        'is_active',
        'image',
    ];


    public function sub_categories()
    {
        return $this->hasMany(SubCategory::class, 'parent_id');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_categories', 'category_id', 'organization_id');
    }
}
