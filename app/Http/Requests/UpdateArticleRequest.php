<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'content_en' => 'sometimes|string',
            'content_ar' => 'sometimes|string',
            'image' => 'sometimes|file|image|max:40960',
            'status' => 'sometimes|in:draft,published,archived',
            'category_id' => 'sometimes|exists:article_categories,id',
            'author_id' => 'sometimes|exists:users,id',
        ];
    }


    public function messages(): array
    {
        return [
            'title_en.string' => ['ar' => 'يجب أن يكون العنوان بالإنجليزية نصًا.', 'en' => 'The English title must be a string.'],
            'title_en.max' => ['ar' => 'يجب ألا يتجاوز العنوان بالإنجليزية 255 حرفًا.', 'en' => 'The English title must not exceed 255 characters.'],

            'title_ar.string' => ['ar' => 'يجب أن يكون العنوان بالعربية نصًا.', 'en' => 'The Arabic title must be a string.'],
            'title_ar.max' => ['ar' => 'يجب ألا يتجاوز العنوان بالعربية 255 حرفًا.', 'en' => 'The Arabic title must not exceed 255 characters.'],

            'content_en.string' => ['ar' => 'يجب أن يكون المحتوى بالإنجليزية نصًا.', 'en' => 'The English content must be a string.'],
            'content_ar.string' => ['ar' => 'يجب أن يكون المحتوى بالعربية نصًا.', 'en' => 'The Arabic content must be a string.'],

            'image.file' => ['ar' => 'يجب أن يكون الملف صورة صالحة.', 'en' => 'The file must be a valid image.'],
            'image.image' => ['ar' => 'يجب أن يكون الملف صورة.', 'en' => 'The file must be an image.'],
            'image.max' => ['ar' => 'يجب ألا يتجاوز حجم الصورة 40 ميجابايت.', 'en' => 'The image must not exceed 40MB.'],

            'status.in' => ['ar' => 'يجب أن تكون الحالة إما "مسودة" أو "منشورة" أو "مؤرشفة".', 'en' => 'The status must be either "draft", "published", or "archived".'],

            'category_id.exists' => ['ar' => 'الفئة المحددة غير موجودة.', 'en' => 'The selected category does not exist.'],
            'author_id.exists' => ['ar' => 'المؤلف المحدد غير موجود.', 'en' => 'The selected author does not exist.'],
        ];
    }
}
