<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleComment extends BaseFormRequest
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
}
