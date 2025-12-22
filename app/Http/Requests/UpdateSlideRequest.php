<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
            'image' => 'sometimes|max:5048',
            'title' => 'sometimes', // لأنه JSON
            'description' => 'sometimes',
            'circle_1_color' => 'nullable|string|max:20',
            'circle_2_color' => 'nullable|string|max:20',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
