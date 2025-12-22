<?php

namespace App\Http\Requests;

use App\Models\ServiceFormField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceFormFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'field_key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('service_form_fields')->where(function ($query) {
                    return $query->where('service_form_id', $this->route('serviceForm') ?? $this->service_form_id);
                }),
            ],
            'field_type' => ['required', 'string', Rule::in(ServiceFormField::FIELD_TYPES)],
            'label_ar' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'placeholder_ar' => 'nullable|string|max:255',
            'placeholder_en' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.choices' => 'nullable|array',
            'options.choices.*.value' => 'required_with:options.choices|string',
            'options.choices.*.label_ar' => 'required_with:options.choices|string',
            'options.choices.*.label_en' => 'required_with:options.choices|string',
            'validation_rules' => 'nullable|array',
            'validation_rules.min_length' => 'nullable|integer|min:0',
            'validation_rules.max_length' => 'nullable|integer|min:1',
            'validation_rules.min_value' => 'nullable|numeric',
            'validation_rules.max_value' => 'nullable|numeric',
            'validation_rules.pattern' => 'nullable|string',
            'validation_rules.file_size_kb' => 'nullable|integer|min:1',
            'validation_rules.mime_types' => 'nullable|array',
            'validation_rules.min_date' => 'nullable|date',
            'validation_rules.max_date' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'visibility_logic' => 'nullable|array',
            'visibility_logic.depends_on' => 'required_with:visibility_logic|string',
            'visibility_logic.condition' => 'required_with:visibility_logic|string|in:equals,not_equals,contains,not_empty,empty',
            'visibility_logic.value' => 'nullable',
            'is_required' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'field_key.required' => 'Field key is required',
            'field_key.regex' => 'Field key must start with a letter and contain only lowercase letters, numbers, and underscores',
            'field_key.unique' => 'Field key already exists in this form',
            'field_type.required' => 'Field type is required',
            'field_type.in' => 'Invalid field type',
            'label_ar.required' => 'Arabic label is required',
            'label_en.required' => 'English label is required',
        ];
    }
}
