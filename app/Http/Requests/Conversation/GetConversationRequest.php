<?php

namespace App\Http\Requests\Conversation;

use Illuminate\Foundation\Http\FormRequest;

class GetConversationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'participant_id'   => 'required|integer',
            'participant_type' => 'required|string|in:user,organization',
            'conversation_id'  => 'required|integer|exists:conversations,id',
        ];
    }
}
