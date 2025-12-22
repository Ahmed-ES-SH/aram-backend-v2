<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyMember extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'family_member_id',
        'relationship',
        'status',
    ];

    /**
     * Get the user who owns this family record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the related family member (also a user).
     */
    public function member()
    {
        return $this->belongsTo(User::class, 'family_member_id');
    }
}
