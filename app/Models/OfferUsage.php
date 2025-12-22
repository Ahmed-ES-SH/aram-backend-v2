<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferUsage extends Model
{
    protected $fillable = [
        'account_type',
        'times_used',
        'discount_applied',
        'user_id',
        'organization_id',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
