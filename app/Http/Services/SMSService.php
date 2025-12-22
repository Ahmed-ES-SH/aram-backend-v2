<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class SMSService
{
    public function send(string $message, array $numbers, int $lang = 0, ?string $schedule = null, ?string $referenceIds = null)
    {
        $payload = [
            'UserId'      => env('ISMARTSMS_USER'),
            'Password'    => env('ISMARTSMS_PASS'),
            'MobileNo'    => implode(',', $numbers), // multiple numbers allowed
            'Message'     => $message,
            'PushDateTime' => $schedule ?? '',
            'Lang'        => $lang, // 0 = English, 64 = Arabic
            'Header'      => env('ISMARTSMS_HEADER'),
            'referenceIds' => $referenceIds ?? ''
        ];

        $response = Http::asForm()->post(env('ISMARTSMS_URL'), $payload);

        return $response->body(); // return code from iSmart SMS
    }
}
