<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationBlock extends Model
{
    protected $fillable = [
        'conversation_id',
        'blocked_by',
        'blocked_user',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function blocked()
    {
        return $this->belongsTo(User::class, 'blocked_user');
    }
}
