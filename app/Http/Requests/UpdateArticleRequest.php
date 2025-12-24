<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends BaseFormRequest
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
}
