<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
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
            'description' => 'required|string',
            'price_before_discount' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5048',
            'active' => 'required|boolean',
            'order' => 'nullable',
            'category_id' => 'required|exists:card_categories,id',
        ];
    }


    protected function prepareForValidation()
    {

        if ($this->has('benefits') && is_string($this->benefits)) {
            $this->merge([
                'benefits' => json_decode($this->benefits, true),
            ]);
        }


        if ($this->has('keywords') && is_string($this->keywords)) {
            $this->merge([
                'keywords' => json_decode($this->keywords, true),
            ]);
        }
    }


    public function messages(): array
    {
        return [
            'title.required' => 'حقل العنوان مطلوب.',
            'description.required' => 'حقل الوصف مطلوب.',
            'price_before_discount.numeric' => 'السعر قبل الخصم يجب أن يكون رقم.',
            'price.required' => 'السعر مطلوب.',
            'price.numeric' => 'السعر يجب أن يكون رقم.',
            'duration.required' => 'مدة البطاقة مطلوبة.',
            'duration.integer' => 'المدة يجب أن تكون رقم صحيح.',
            'image.required' => 'الصورة مطلوبة.',
            'image.image' => 'الملف يجب أن يكون صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة jpg أو jpeg أو png أو webp.',
            'active.required' => 'حالة البطاقة مطلوبة.',
            'active.boolean' => 'حالة البطاقة يجب أن تكون true أو false.',
            'category_id.required' => 'القسم مطلوب.',
            'category_id.exists' => 'القسم غير موجود.',
        ];
    }
}
