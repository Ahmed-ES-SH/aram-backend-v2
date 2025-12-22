<?php

namespace App\Http\Requests\Conversation;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'participant_one_id'   => 'required|integer',
            'participant_one_type' => 'required|string|in:user,organization',
            'participant_two_id'   => 'required|integer',
            'participant_two_type' => 'required|string|in:user,organization',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                $this->participant_one_id == $this->participant_two_id &&
                $this->participant_one_type == $this->participant_two_type
            ) {
                $validator->errors()->add('participants', 'Participants must be different.');
            }
        });
    }
}
