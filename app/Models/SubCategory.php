<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable =  [
        'title_en',
        'title_ar',
        'bg_color',
        'icon_name',
        'image',
        'is_active',
        'parent_id',
    ];


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }


    public function organizations()
    {
        return $this->belongsToMany(
            Organization::class,
            'organization_categories',   // pivot table
            'category_id',            // FK in pivot
            'organization_id'            // FK in pivot
        );
    }
}
