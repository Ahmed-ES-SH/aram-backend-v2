<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
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
            'name'          => 'sometimes|required|string|max:255',
            'code'          => 'sometimes|required|string|max:10',
            'symbol'        => 'sometimes|required|string|max:10',
            'exchange_rate' => 'sometimes|required|numeric|min:0',
            'is_default'    => 'nullable|boolean',
        ];
    }




    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => [
                'ar' => 'اسم العملة مطلوب.',
                'en' => 'Currency name is required.'
            ],
            'name.string' => [
                'ar' => 'اسم العملة يجب أن يكون نصًا.',
                'en' => 'Currency name must be a string.'
            ],
            'name.max' => [
                'ar' => 'اسم العملة لا يمكن أن يتجاوز 255 حرفاً.',
                'en' => 'Currency name cannot exceed 255 characters.'
            ],

            'code.required' => [
                'ar' => 'رمز العملة مطلوب.',
                'en' => 'Currency code is required.'
            ],
            'code.string' => [
                'ar' => 'رمز العملة يجب أن يكون نصًا.',
                'en' => 'Currency code must be a string.'
            ],
            'code.max' => [
                'ar' => 'رمز العملة لا يمكن أن يتجاوز 10 أحرف.',
                'en' => 'Currency code cannot exceed 10 characters.'
            ],
            'code.unique' => [
                'ar' => 'رمز العملة مستخدم بالفعل.',
                'en' => 'Currency code is already taken.'
            ],

            'symbol.required' => [
                'ar' => 'رمز العرض مطلوب.',
                'en' => 'Symbol is required.'
            ],
            'symbol.string' => [
                'ar' => 'رمز العرض يجب أن يكون نصًا.',
                'en' => 'Symbol must be a string.'
            ],
            'symbol.max' => [
                'ar' => 'رمز العرض لا يمكن أن يتجاوز 10 أحرف.',
                'en' => 'Symbol cannot exceed 10 characters.'
            ],

            'exchange_rate.required' => [
                'ar' => 'سعر الصرف مطلوب.',
                'en' => 'Exchange rate is required.'
            ],
            'exchange_rate.numeric' => [
                'ar' => 'سعر الصرف يجب أن يكون رقمًا.',
                'en' => 'Exchange rate must be a number.'
            ],
            'exchange_rate.min' => [
                'ar' => 'سعر الصرف يجب أن يكون 0 أو أكثر.',
                'en' => 'Exchange rate must be at least 0.'
            ],

            'is_default.boolean' => [
                'ar' => 'حقل العملة الافتراضية يجب أن يكون صحيح أو خطأ.',
                'en' => 'The default currency field must be true or false.'
            ],
        ];
    }
}
