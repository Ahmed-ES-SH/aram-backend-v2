<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;


    public function __construct($user)
    {
        $this->user = $user;
        $this->url = url("/api/verify-email/{$user->id}?token=" . urlencode($user->email_verification_token)) . "&account_type={$user->account_type}";
    }


    public function build()
    {
        return $this->subject('تفعيل الحساب الخاص بك . ')
            ->view('emails.verify-email')
            ->with([
                'url' => $this->url,
                'user' => $this->user
            ]);
    }
}
