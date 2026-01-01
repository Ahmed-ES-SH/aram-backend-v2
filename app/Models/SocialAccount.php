<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'whatsapp_number',
        'gmail_account',
        'official_account',
        'facebook_account',
        'x_account',
        'youtube_account',
        'instgram_account',
        'snapchat_account',
        'tiktok_account',
        'official_state',
        'gmail_state',
        'facebook_state',
        'x_state',
        'youtube_state',
        'instgram_state',
        'snapchat_state',
        'tiktok_state',
    ];
}
