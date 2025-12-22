<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'promoter_id',
        'referred_user_id',
        'ip',
        'status',
        'converted_at',
    ];

    public function promoter()
    {
        return $this->belongsTo(Promoter::class, 'promoter_id');
    }


    public function referred_user()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
