<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'deleted_by',
        'participant_one_id',
        'participant_two_id',
        'participant_one_type',
        'participant_two_type',
    ];




    protected $casts = [
        'deleted_by' => 'array',
    ];

    public function participantOne()
    {
        return $this->morphTo();
    }

    public function participantTwo()
    {
        return $this->morphTo();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }



    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany(); // Laravel 8+
    }


    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'conversation_id')
            ->where('is_read', false);
    }


    // ✅ تصحيح علاقة الحظر
    public function block()
    {
        return $this->hasOne(ConversationBlock::class, 'conversation_id');
    }

    // ✅ تحسين دالة التحقق من الحظر وإرجاع بيانات الحظر
    public function getIsBlockedAttribute()
    {
        return $this->block()->exists() ? $this->block : null;
    }
}
