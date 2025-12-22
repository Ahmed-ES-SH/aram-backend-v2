<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'id_number' => 'nullable|unique:users,id_number',
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'image' => 'nullable|file|image|max:4096',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'country' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'device_type' => 'nullable|string',
            'role' => 'nullable|string|in:admin,user',
            'location' => 'nullable|string',
            'ref_code' => 'nullable|string',
        ];
    }




    public function messages(): array
    {
        return [
            'name.required' => ['ar' => 'حقل الاسم مطلوب.', 'en' => 'The name field is required.'],
            'name.unique' => ['ar' => 'الاسم مستخدم بالفعل.', 'en' => 'The name has already been taken.'],

            'phone.required' => ['ar' => 'رقم الهاتف  مطلوب.', 'en' => 'The phone number is required.'],

            'email.required' => ['ar' => 'حقل البريد الإلكتروني مطلوب.', 'en' => 'The email field is required.'],
            'email.email' => ['ar' => 'يجب أن يكون البريد الإلكتروني صالحًا.', 'en' => 'The email must be a valid email address.'],
            'email.unique' => ['ar' => 'البريد الإلكتروني مستخدم بالفعل.', 'en' => 'The email has already been taken.'],

            'password.required' => ['ar' => 'حقل كلمة المرور مطلوب.', 'en' => 'The password field is required.'],
            'password.min' => ['ar' => 'يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل.', 'en' => 'The password must be at least 8 characters.'],

            'image.image' => ['ar' => 'يجب أن يكون الملف صورة.', 'en' => 'The file must be an image.'],
            'image.max' => ['ar' => 'يجب ألا يزيد حجم الصورة عن 4 ميجابايت.', 'en' => 'The image size must not exceed 4MB.'],

            'phone.regex' => ['ar' => 'رقم الهاتف غير صالح.', 'en' => 'The phone number format is invalid.'],

            'country.string' => ['ar' => 'يجب أن يكون اسم الدولة نصيًا.', 'en' => 'The country must be a string.'],

            'gender.required' => ['ar' => 'حقل الجنس مطلوب.', 'en' => 'The gender field is required.'],
            'gender.in' => ['ar' => 'يجب أن يكون الجنس ذكر أو أنثى.', 'en' => 'The gender must be either male or female.'],

            'birth_date.required' => ['ar' => 'حقل تاريخ الميلاد مطلوب.', 'en' => 'The birth date field is required.'],
            'birth_date.date' => ['ar' => 'يجب أن يكون تاريخ الميلاد تاريخًا صحيحًا.', 'en' => 'The birth date must be a valid date.'],
        ];
    }
}
