<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends BaseFormRequest
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
            'id_number' => 'nullable|unique:users,id_number',
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'image' => 'nullable|file|image|max:4096',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'country' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'device_type' => 'nullable|string',
            'role' => 'nullable|string|in:admin,user,super_admin',
            'location' => 'nullable|string',
            'ref_code' => 'nullable|string',
        ];
    }
}
