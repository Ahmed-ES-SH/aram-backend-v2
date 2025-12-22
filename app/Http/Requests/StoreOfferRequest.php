<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'number_of_uses' => 'nullable|integer|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:waiting,active,expired',
            'organization_id' => 'required|exists:organizations,id',
            'category_id' => 'required|exists:categories,id',
            'code' => 'nullable|string|max:50|unique:offers,code',
        ];
    }


    public function messages(): array
    {
        return [
            'title.required' => 'عنوان العرض مطلوب',
            'discount_type.required' => 'نوع الخصم مطلوب',
            'discount_value.required' => 'قيمة الخصم مطلوبة',
            'start_date.required' => 'تاريخ بداية العرض مطلوب',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'category_id.required' => 'الفئة مطلوبة',
        ];
    }
}
