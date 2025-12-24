<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends BaseFormRequest
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
            'conversation_id' => 'required|exists:conversations,id',
            'sender_id' => 'required|integer',
            'sender_type' => 'required|in:user,organization',
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|in:user,organization',
            'message' => 'nullable|string',
            'message_type' => 'required|in:text,pdf,image,audio',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,mp3,webm,pdf,docx|max:21960',
        ];
    }
}
