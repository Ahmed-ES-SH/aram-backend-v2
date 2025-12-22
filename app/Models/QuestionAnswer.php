<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'user_id',
        'is_visible',
    ];

    protected $table = 'question_answers';
}
