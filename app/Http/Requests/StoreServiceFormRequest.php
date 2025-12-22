<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_page_id' => 'required|exists:service_pages,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'service_page_id.required' => 'Service page is required',
            'service_page_id.exists' => 'Selected service page does not exist',
            'name_ar.required' => 'Arabic name is required',
            'name_en.required' => 'English name is required',
        ];
    }
}
