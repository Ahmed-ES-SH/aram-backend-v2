<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    protected $fillable = [
        'user_id',
        'account_type',
        'amount',
        'status',
        'bank_number',
        'note',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }




    public function transaction()
    {
        return $this->hasOne(Transaction::class, function ($q) {
            $q->where('type', 'withdrawal');
        }, 'id', 'source_id')->where('source_type', 'withdraw_requests');
    }
}
