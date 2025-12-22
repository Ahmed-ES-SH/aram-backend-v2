<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ArticleCategory extends Model
{

    use Searchable;

    protected $fillable = [
        'title_en',
        'title_ar',
        'image',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }


    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title_en' => $this->title_en,
            'title_ar' => $this->title_ar,
        ];
    }
}
