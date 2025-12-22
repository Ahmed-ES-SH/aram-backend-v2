<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'message',
        'message_type',
        'attachment',
        'conversation_id',
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'is_read',
    ];



    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }


    public function sender()
    {
        return $this->morphTo();
    }


    public function receiver()
    {
        return $this->morphTo();
    }
}
