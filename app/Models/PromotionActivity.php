<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionActivity extends Model
{
    protected $fillable = [
        'promoter_type',
        'promoter_id',
        'activity_type',
        'metadata',
        'ip_address',
        'country',
        'device_type',
        'ref_code',
        'commission_amount',
        'activity_at',
        'is_active',
        'member_id',
        'member_type',
    ];


    protected $casts = [
        'metadata' => 'array',
        'activity_at' => 'datetime',
    ];


    /**
     * Relation to promoter
     */
    public function promoter()
    {
        return $this->belongsTo(User::class, 'promoter_id');
    }

    public function userMember()
    {

        return $this->belongsTo(User::class, 'member_id');
    }


    public function orgMember()
    {

        return $this->belongsTo(Organization::class, 'member_id');
    }
}
