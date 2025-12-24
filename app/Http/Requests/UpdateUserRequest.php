<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends BaseFormRequest
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
            'name' => 'sometimes|unique:users,name|min:3',
            'email' => 'sometimes|email|unique:users,email',
            'password' => 'sometimes|string',
            'image' => 'nullable|file|image|max:4096',
            'phone' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'country' => 'nullable|string',
            'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|date',
            'role' => 'sometimes|in:admin,user',
            'status' => 'sometimes|in:active,inactive,banned',
            'failed_attempts' => 'sometimes',
            'last_login_at' => 'sometimes',
            'is_signed' => 'sometimes',
            'location' => 'nullable|string'
        ];
    }
}
