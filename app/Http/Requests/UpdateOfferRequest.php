<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
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

        $offerId = $this->route('id');

        return [
            'title' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string',
            'description_en' => 'sometimes|nullable|string',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'discount_type' => 'sometimes|required|in:percentage,fixed',
            'discount_value' => 'sometimes|required|numeric|min:0',

            'usage_limit' => 'sometimes|nullable|integer|min:1',
            'per_user_limit' => 'sometimes|nullable|integer|min:1',
            'number_of_uses' => 'sometimes|nullable|integer|min:0',

            'start_date' => 'sometimes|required|date|after_or_equal:today',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',

            'status' => 'sometimes|nullable|in:waiting,active,expired',

            'organization_id' => 'sometimes|nullable|exists:organizations,id',
            'category_id' => 'sometimes|required|exists:categories,id',

            'code' => "sometimes|nullable|string|max:50|unique:offers,code,{$offerId}",
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
