<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyMemberRequest extends FormRequest
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
            'family_member_id' => 'required|exists:users,id|different:user_id',
            'relationship' => 'nullable|string|max:50',
        ];
    }


    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'family_member_id.required' => 'The family member ID is required.',
            'family_member_id.exists' => 'The selected family member does not exist.',
            'family_member_id.different' => 'You cannot add yourself as a family member.',
            'relationship.max' => 'The relationship may not be greater than 50 characters.',
        ];
    }
}
