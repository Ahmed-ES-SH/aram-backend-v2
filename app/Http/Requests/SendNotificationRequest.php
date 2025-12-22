<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
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
            'user_ids' => 'nullable',
            "content" => "required|string",
            "recipient_id" => "required|integer",
            "recipient_type" => 'required|in:user,organization',
            "sender_id" => "required|integer",
            "sender_type" => 'required|in:user,organization'
        ];
    }

    public function messages()
    {
        return [
            "content.required" => ["ar" => "محتوى الإشعار مطلوب.", "en" => "Notification content is required."],
            "content.string" => ["ar" => "يجب أن يكون محتوى الإشعار نصًا.", "en" => "Notification content must be a string."],
            "user_id.required" => ["ar" => "حقل معرف المستخدم مطلوب.", "en" => "User ID field is required."],
            "user_id.exists" => ["ar" => "المستخدم المحدد غير موجود.", "en" => "The selected user does not exist."],
            "sender_id.required" => ["ar" => "حقل معرف المرسل مطلوب.", "en" => "Sender ID field is required."],
            "sender_id.exists" => ["ar" => "المرسل المحدد غير موجود.", "en" => "The selected sender does not exist."],
        ];
    }
}
