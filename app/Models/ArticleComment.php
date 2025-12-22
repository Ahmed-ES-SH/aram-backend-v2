<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'article_id',
        'parent_id',
        'likes_count',
        'status'
    ];

    // التعليق الأصلي (الأب)
    public function parent()
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    // الردود على التعليق
    public function replies()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id');
    }


    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'comment_likes', 'comment_id', 'user_id')->withTimestamps();
    }
}
