<?php

namespace App\Jobs;

use App\Mail\NewsletterEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscriber;
    protected $subject;
    protected $content;

    /**
     * إنشاء وظيفة جديدة.
     */
    public function __construct($subscriber, $subject, $content)
    {
        $this->subscriber = $subscriber;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * تنفيذ المهمة.
     */
    public function handle(): void
    {
        Mail::to($this->subscriber->email)->send(new NewsletterEmail([
            'subject' => $this->subject,
            'content' => $this->content,
        ]));
    }
}
