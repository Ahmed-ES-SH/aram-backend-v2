<?php

namespace App\Http\Requests;

use App\Models\ServiceFormField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceFormFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fieldId = $this->route('serviceFormField') ?? $this->route('id');

        return [
            'field_key' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('service_form_fields')->ignore($fieldId),
            ],
            'field_type' => ['sometimes', 'string', Rule::in(ServiceFormField::FIELD_TYPES)],
            'label_ar' => 'sometimes|string|max:255',
            'label_en' => 'sometimes|string|max:255',
            'placeholder_ar' => 'nullable|string|max:255',
            'placeholder_en' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.choices' => 'nullable|array',
            'options.choices.*.value' => 'required_with:options.choices|string',
            'options.choices.*.label_ar' => 'required_with:options.choices|string',
            'options.choices.*.label_en' => 'required_with:options.choices|string',
            'validation_rules' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'visibility_logic' => 'nullable|array',
            'is_required' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'field_key.regex' => 'Field key must start with a letter and contain only lowercase letters, numbers, and underscores',
            'field_key.unique' => 'Field key already exists',
            'field_type.in' => 'Invalid field type',
        ];
    }
}
