<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'bg_color' => ['required', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'icon_name' => 'required|string|max:255',
            'image' => 'required|file|image|max:5048',
        ];
    }


    public function messages(): array
    {
        return [
            'title_en.required' => ['ar' => 'العنوان بالإنجليزية مطلوب.', 'en' => 'The English title is required.'],
            'title_en.string' => ['ar' => 'يجب أن يكون العنوان بالإنجليزية نصًا.', 'en' => 'The English title must be a string.'],
            'title_en.max' => ['ar' => 'يجب ألا يزيد العنوان بالإنجليزية عن 255 حرفًا.', 'en' => 'The English title must not exceed 255 characters.'],

            'title_ar.required' => ['ar' => 'العنوان بالعربية مطلوب.', 'en' => 'The Arabic title is required.'],
            'title_ar.string' => ['ar' => 'يجب أن يكون العنوان بالعربية نصًا.', 'en' => 'The Arabic title must be a string.'],
            'title_ar.max' => ['ar' => 'يجب ألا يزيد العنوان بالعربية عن 255 حرفًا.', 'en' => 'The Arabic title must not exceed 255 characters.'],

            'image.required' => ['ar' => 'يجب رفع صورة.', 'en' => 'An image is required.'],
            'image.file' => ['ar' => 'يجب أن يكون الملف المرفوع صورة.', 'en' => 'The uploaded file must be an image.'],
            'image.image' => ['ar' => 'يجب أن يكون الملف صورة صحيحة.', 'en' => 'The file must be a valid image.'],
            'image.max' => ['ar' => 'يجب ألا يزيد حجم الصورة عن 40 ميجابايت.', 'en' => 'The image size must not exceed 40MB.'],
        ];
    }
}
