<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'content_en' => 'required|string',
            'content_ar' => 'required|string',
            'image' => 'required|file|image|max:40960',
            'status' => 'sometimes|in:draft,published,archived',
            'category_id' => 'required|exists:article_categories,id',
            'author_id' => 'required|exists:users,id',
        ];
    }


    public function messages(): array
    {
        return [
            'title_en.required' => ['ar' => 'عنوان المقال بالإنجليزية مطلوب.', 'en' => 'The English title is required.'],
            'title_en.string' => ['ar' => 'يجب أن يكون العنوان نصًا.', 'en' => 'The title must be a string.'],
            'title_en.max' => ['ar' => 'يجب ألا يتجاوز العنوان 255 حرفًا.', 'en' => 'The title must not exceed 255 characters.'],

            'title_ar.required' => ['ar' => 'عنوان المقال بالعربية مطلوب.', 'en' => 'The Arabic title is required.'],
            'title_ar.string' => ['ar' => 'يجب أن يكون العنوان نصًا.', 'en' => 'The title must be a string.'],
            'title_ar.max' => ['ar' => 'يجب ألا يتجاوز العنوان 255 حرفًا.', 'en' => 'The title must not exceed 255 characters.'],

            'content_en.required' => ['ar' => 'محتوى المقال بالإنجليزية مطلوب.', 'en' => 'The English content is required.'],
            'content_en.string' => ['ar' => 'يجب أن يكون المحتوى نصًا.', 'en' => 'The content must be a string.'],

            'content_ar.required' => ['ar' => 'محتوى المقال بالعربية مطلوب.', 'en' => 'The Arabic content is required.'],
            'content_ar.string' => ['ar' => 'يجب أن يكون المحتوى نصًا.', 'en' => 'The content must be a string.'],

            'image.required' => ['ar' => 'الصورة مطلوبة.', 'en' => 'The image is required.'],
            'image.file' => ['ar' => 'يجب أن يكون الملف صورة صالحة.', 'en' => 'The file must be a valid image.'],
            'image.image' => ['ar' => 'يجب أن يكون الملف صورة.', 'en' => 'The file must be an image.'],
            'image.max' => ['ar' => 'يجب ألا يتجاوز حجم الصورة 40 ميجابايت.', 'en' => 'The image must not exceed 40MB.'],

            'status.in' => ['ar' => 'يجب أن تكون الحالة إما "مسودة" أو "منشورة" أو "مؤرشفة".', 'en' => 'The status must be either "draft", "published", or "archived".'],

            'category_id.required' => ['ar' => 'فئة المقال مطلوبة.', 'en' => 'The category is required.'],
            'category_id.exists' => ['ar' => 'الفئة المحددة غير موجودة.', 'en' => 'The selected category does not exist.'],

            'author_id.required' => ['ar' => 'المؤلف مطلوب.', 'en' => 'The author is required.'],
            'author_id.exists' => ['ar' => 'المؤلف المحدد غير موجود.', 'en' => 'The selected author does not exist.'],
        ];
    }
}
