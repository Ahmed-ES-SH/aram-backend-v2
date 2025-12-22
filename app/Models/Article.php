<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        "title_en",
        "title_ar",
        "content_en",
        "content_ar",
        "image",
        "status",
        "views",
        "category_id",
        "author_id",
    ];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function interactions()
    {
        return $this->hasMany(ArticleInteractions::class, 'article_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class, 'article_id');
    }

    public function commentsWithReplies()
    {
        return $this->hasMany(ArticleComment::class, 'article_id')
            ->whereNull('parent_id')
            ->with('replies'); // جلب الردود مع كل تعليق
    }
}
