<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleComment extends FormRequest
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
            'content' => "required|string|max:255",
            'user_id' => "required|exists:users,id",
            'parent_id' => "nullable|exists:article_comments,id", // الحقل اختياري لكن إذا أُرسل يجب أن يكون معرفًا صالحًا
            'article_id' => "required|exists:articles,id",
        ];
    }


    public function messages(): array
    {
        return [
            // ✅ محتوى التعليق (Content)
            'content.required' => [
                'ar' => 'محتوى التعليق مطلوب.',
                'en' => 'The comment content is required.'
            ],
            'content.string' => [
                'ar' => 'يجب أن يكون محتوى التعليق نصًا.',
                'en' => 'The comment content must be a string.'
            ],
            'content.max' => [
                'ar' => 'يجب ألا يتجاوز التعليق 255 حرفًا.',
                'en' => 'The comment must not exceed 255 characters.'
            ],

            // ✅ معرف المستخدم (User ID)
            'user_id.required' => [
                'ar' => 'معرف المستخدم مطلوب.',
                'en' => 'The user ID is required.'
            ],
            'user_id.exists' => [
                'ar' => 'المستخدم غير موجود.',
                'en' => 'The user does not exist.'
            ],

            // ✅ معرف التعليق الأصل (Parent ID) - للردود فقط
            'parent_id.exists' => [
                'ar' => 'التعليق الأصل غير موجود.',
                'en' => 'The parent comment does not exist.'
            ],

            // ✅ معرف المقال (Article ID)
            'article_id.required' => [
                'ar' => 'معرف المقال مطلوب.',
                'en' => 'The article ID is required.'
            ],
            'article_id.exists' => [
                'ar' => 'المقال غير موجود.',
                'en' => 'The article does not exist.'
            ],
        ];
    }
}
