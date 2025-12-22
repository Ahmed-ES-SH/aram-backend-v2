<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserArticleInteraction extends Model
{
    protected $fillable = [
        'interaction_type',
        'user_id',
        'article_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
