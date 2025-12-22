<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubCategoryRequest extends FormRequest
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
            'title_en' => 'sometimes|string|max:255',
            'title_ar' => 'sometimes|string|max:255',
            'image' => 'sometimes|file|image|max:5048',
            'bg_color' => 'sometimes|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'icon_name' => 'sometimes|string|max:255',
            'parent_id' => 'sometimes|exists:categories,id'
        ];
    }
}
