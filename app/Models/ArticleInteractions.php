<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleInteractions extends Model
{
    protected $fillable = [
        'loves',
        'likes',
        'dislikes',
        'laughters',
        'article_id',
        'totalReactions',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
