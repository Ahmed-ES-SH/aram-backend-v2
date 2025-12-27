<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationReview extends FormRequest
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
    public function rules()
    {
        return [
            'stars' => 'required|integer|min:1|max:5',
            'head_line' => 'required|string|max:255',
            'content' => 'required|string|min:4',
            'like_counts' => 'nullable|integer|min:0',
            'user_id' => 'required|exists:users,id',
            'organization_id' => 'required|exists:organizations,id',
        ];
    }


    public function messages()
    {
        return [
            'stars.required' => 'يجب تحديد التقييم بالنجوم.',
            'stars.integer' => 'يجب أن يكون التقييم رقمًا صحيحًا.',
            'stars.min' => 'يجب أن يكون التقييم على الأقل 1 نجمة.',
            'stars.max' => 'لا يمكن أن يكون التقييم أكثر من 5 نجوم.',

            'head_line.required' => 'يجب إدخال عنوان المراجعة.',
            'head_line.string' => 'يجب أن يكون العنوان نصًا.',
            'head_line.max' => 'يجب ألا يتجاوز العنوان 255 حرفًا.',

            'content.required' => 'يجب إدخال محتوى المراجعة.',
            'content.string' => 'يجب أن يكون المحتوى نصًا.',
            'content.min' => 'يجب ألا يقل المحتوى عن 10 أحرف.',

            'like_counts.integer' => 'يجب أن يكون عدد الإعجابات رقمًا صحيحًا.',
            'like_counts.min' => 'يجب ألا يكون عدد الإعجابات أقل من 0.',

            'user_id.required' => 'يجب تحديد معرف المستخدم.',
            'user_id.exists' => 'المستخدم المحدد غير موجود.',

            'organization_id.required' => 'يجب تحديد معرف المؤسسة.',
            'organization_id.exists' => 'المؤسسة المحددة غير موجودة.',
        ];
    }
}
