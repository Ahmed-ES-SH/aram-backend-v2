<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'organization_id',
        'start_time',
        'end_time',
        'price',
        'is_paid',
        'status',
        'user_notes',
        'organization_notes',
        'confirmed_at',
        'rejected_at',
        'cancelled_at',
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
