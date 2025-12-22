<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'content',
        'is_read',
        'sender_type',
        'recipient_type',
        'sender_id',
        'recipient_id',
    ];




    public function sender()
    {
        return $this->morphTo();
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
