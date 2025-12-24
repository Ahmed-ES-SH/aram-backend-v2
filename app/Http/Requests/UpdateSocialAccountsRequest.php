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
            'facebook_account' => ['nullable', 'url'], // يجب أن يكون رابط URL
            'x_account' => ['nullable', 'url'], // X (تويتر سابقًا)
            'youtube_account' => ['nullable', 'url'], // رابط يوتيوب
            'instgram_account' => ['nullable', 'url'], // رابط انستجرام
            'snapchat_account' => ['nullable', 'url'], // رابط سناب شات
            'tiktok_account' => ['nullable', 'url'], // رابط سناب شات
        ];
    }
}
