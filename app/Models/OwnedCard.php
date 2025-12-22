<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnedCard extends Model
{
    protected $fillable = [
        'cvv',
        'owner_id',
        'issue_date',
        'usage_limit',
        'expiry_date',
        'current_usage',
        'owner_type',
        'card_number',
        'status',
        'card_id',
    ];


    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
}
