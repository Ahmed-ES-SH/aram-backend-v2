<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationReview extends Model
{
    protected $fillable = [
        "stars",
        "head_line",
        "content",
        "like_counts",
        "user_id",
        "organization_id",
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orgreviewslikes()
    {
        return $this->hasMany(ReviewLikesCheck::class, 'user_id');
    }
}
