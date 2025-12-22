<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
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
            'name'    => 'nullable|string|max:255',          // الاسم مطلوب وبحد أقصى 255 حرفًا.
            'email'   => 'required|email|max:255',          // البريد الإلكتروني مطلوب ويجب أن يكون تنسيقه صحيحًا.
            'phone_number'   => 'required|string|max:20',          // الهاتف اختياري وبحد أقصى 20 حرفًا.
            'subject' => 'nullable|string|max:255',         // الموضوع اختياري وبحد أقصى 255 حرفًا.
            'message' => 'required|string',          // الرسالة مطلوبة وبحد أدنى 10 أحرف.
        ];
    }


    public function messages()
    {
        return [
            'name.required'    => 'الاسم مطلوب.',
            'name.string'      => 'يجب أن يكون الاسم نصًا صحيحًا.',
            'name.max'         => 'الاسم يجب ألا يتجاوز 255 حرفًا.',
            'email.required'   => 'البريد الإلكتروني مطلوب.',
            'email.email'      => 'يجب إدخال بريد إلكتروني صحيح.',
            'email.max'        => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرفًا.',
            'phone.string'     => 'يجب أن يكون الهاتف نصًا صحيحًا.',
            'phone.max'        => 'رقم الهاتف يجب ألا يتجاوز 20 حرفًا.',
            'subject.string'   => 'يجب أن يكون الموضوع نصًا صحيحًا.',
            'subject.max'      => 'الموضوع يجب ألا يتجاوز 255 حرفًا.',
            'message.required' => 'الرسالة مطلوبة.',
            'message.string'   => 'يجب أن تكون الرسالة نصًا صحيحًا.',
            'message.min'      => 'الرسالة يجب أن تحتوي على 10 أحرف على الأقل.',
        ];
    }
}
