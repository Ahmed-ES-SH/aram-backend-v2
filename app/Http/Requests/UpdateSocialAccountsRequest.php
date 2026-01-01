<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialAccountsRequest extends BaseFormRequest
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
            'official_account' => ['nullable', 'string'], // يجب أن يكون رابط URL
            'facebook_account' => ['nullable', 'string'], // يجب أن يكون رابط URL
            'x_account' => ['nullable', 'string'], // X (تويتر سابقًا)
            'youtube_account' => ['nullable', 'string'], // رابط يوتيوب
            'instgram_account' => ['nullable', 'string'], // رابط انستجرام
            'snapchat_account' => ['nullable', 'string'], // رابط سناب شات
            'tiktok_account' => ['nullable', 'string'], // رابط سناب شات
            'official_state' => ['nullable', 'boolean'],
            'gmail_state' => ['nullable', 'boolean'],
            'facebook_state' => ['nullable', 'boolean'],
            'x_state' => ['nullable', 'boolean'],
            'youtube_state' => ['nullable', 'boolean'],
            'instgram_state' => ['nullable', 'boolean'],
            'snapchat_state' => ['nullable', 'boolean'],
            'tiktok_state' => ['nullable', 'boolean'],
        ];
    }
}
