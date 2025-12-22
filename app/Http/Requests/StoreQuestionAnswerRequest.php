<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionAnswerRequest extends FormRequest
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
            'question' => "required|string",
            'answer' => "required|string",
            'user_id' => "nullable|exists:users,id",
            'is_visible' => "nullable|boolean", // تحديد النوع كـ boolean
        ];
    }


    public function messages(): array
    {
        return [
            'question.required' => [
                'ar' => 'السؤال مطلوب.',
                'en' => 'The question field is required.',
            ],
            'question.string' => [
                'ar' => 'يجب أن يكون السؤال نصًا.',
                'en' => 'The question must be a string.',
            ],

            'answer.required' => [
                'ar' => 'الإجابة مطلوبة.',
                'en' => 'The answer field is required.',
            ],
            'answer.string' => [
                'ar' => 'يجب أن تكون الإجابة نصًا.',
                'en' => 'The answer must be a string.',
            ],

            'user_id.exists' => [
                'ar' => 'المستخدم المحدد غير موجود.',
                'en' => 'The selected user does not exist.',
            ],

            'is_visible.boolean' => [
                'ar' => 'يجب أن يكون الحقل مرئيًا إما صحيحًا أو خطأ.',
                'en' => 'The visibility field must be true or false.',
            ],
        ];
    }
}
