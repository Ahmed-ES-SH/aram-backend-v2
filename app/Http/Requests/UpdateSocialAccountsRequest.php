<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialAccountsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'whatsapp_number' => ['nullable', 'string', 'regex:/^\+?[0-9]{7,16}$/'], // رقم هاتف بصيغة صحيحة
            'gmail_account' => ['nullable', 'email', 'ends_with:@gmail.com'], // يجب أن يكون بريد Gmail
            'facebook_account' => ['nullable', 'url'], // يجب أن يكون رابط URL
            'x_account' => ['nullable', 'url'], // X (تويتر سابقًا)
            'youtube_account' => ['nullable', 'url'], // رابط يوتيوب
            'instgram_account' => ['nullable', 'url'], // رابط انستجرام
            'snapchat_account' => ['nullable', 'url'], // رابط سناب شات
            'tiktok_account' => ['nullable', 'url'], // رابط سناب شات
        ];
    }


    public function messages(): array
    {
        return [
            'whatsapp_number.regex' => [
                'ar' => 'رقم الواتساب يجب أن يكون بصيغة صحيحة ويحتوي على أرقام فقط ولا يزيد عن 16 رقم.',
                'en' => 'The WhatsApp number must be in a valid format and contain only numbers.'
            ],
            'gmail_account.email' => [
                'ar' => 'يجب إدخال بريد إلكتروني صحيح.',
                'en' => 'A valid email address is required.'
            ],
            'gmail_account.ends_with' => [
                'ar' => 'يجب أن يكون البريد الإلكتروني من نوع Gmail (@gmail.com).',
                'en' => 'The email must be a Gmail address (@gmail.com).'
            ],
            'facebook_account.url' => [
                'ar' => 'يجب إدخال رابط URL صحيح لحساب فيسبوك.',
                'en' => 'A valid URL for the Facebook account is required.'
            ],
            'x_account.url' => [
                'ar' => 'يجب إدخال رابط URL صحيح لحساب X (تويتر سابقًا).',
                'en' => 'A valid URL for the X (formerly Twitter) account is required.'
            ],
            'youtube_account.url' => [
                'ar' => 'يجب إدخال رابط URL صحيح لحساب يوتيوب.',
                'en' => 'A valid URL for the YouTube account is required.'
            ],
            'instgram_account.url' => [
                'ar' => 'يجب إدخال رابط URL صحيح لحساب إنستجرام.',
                'en' => 'A valid URL for the Instagram account is required.'
            ],
            'snapchat_account.url' => [
                'ar' => 'يجب إدخال رابط URL صحيح لحساب سناب شات.',
                'en' => 'A valid URL for the Snapchat account is required.'
            ],
        ];
    }
}
