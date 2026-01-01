<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
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
            'email'                 => 'required|email|max:255|unique:organizations,email',
            'password'              => 'required|min:8',
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'features'              => 'nullable|string',
            'accaptable_message'    => 'nullable|string',
            'unaccaptable_message'  => 'nullable|string',
            'location'              => 'nullable|json',
            'phone_number'          => 'nullable|string|max:20',
            'confirmation_price'    => 'nullable|numeric',
            'confirmation_status'   => 'required|boolean',
            'open_at'               => 'required|string',
            'close_at'              => 'required|string',
            'url'                   => 'nullable|url',
            'image'                 => 'required|file',
            'logo'                  => 'required|file',
            'verification_code'     => 'nullable|string',
            'email_verified'        => 'boolean',
            'email_verification_token' => 'nullable|string',
            'active'                => 'nullable|boolean',
            'status'                => 'in:published,not_published,under_review',
            'rating'               => 'numeric|min:0|max:5',
            'number_of_reservations' => 'integer|min:0',
            'is_signed'             => 'boolean',
            'order'                 => 'integer|min:1',
            'booking_status'        => 'required|boolean',
            'cooperation_file'      => 'nullable|string',
            'account_type'          => 'string|in:organization',
            'categories' => 'nullable|array',
            'sub_categories' => 'nullable|array',
        ];
    }


    protected function prepareForValidation()
    {

        if ($this->has('benefits') && is_string($this->benefits)) {
            $this->merge([
                'benefits' => json_decode($this->benefits, true),
            ]);
        }

        if ($this->has('sub_categories') && is_string($this->sub_categories)) {
            $this->merge([
                'sub_categories' => json_decode($this->sub_categories, true),
            ]);
        }


        if ($this->has('categories') && is_string($this->categories)) {
            $this->merge([
                'categories' => json_decode($this->categories, true),
            ]);
        }


        if ($this->has('keywords') && is_string($this->keywords)) {
            $this->merge([
                'keywords' => json_decode($this->keywords, true),
            ]);
        }
    }


    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'يجب إدخال بريد إلكتروني صحيح.',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'image.required' => 'صورة الغلاف مطلوبة.',
            'logo.required' => ' شعار المركز مطلوب.',
            'active.required' => 'الرجاء تحديد أحد الإختيارات',
            'booking_status.required' => 'الرجاء تحديد أحد الإختيارات',
            'confirmation_status.required' => 'الرجاء تحديد أحد الإختيارات',
            'password.min' => 'يجب أن تكون كلمة المرور على الأقل 8 أحرف.',
            'title.required' => 'اسم المؤسسة مطلوب.',
            'description.required' => 'الوصف مطلوب.',
            'phone_number.required' => 'رقم الهاتف مطلوب.',
            'confirmation_price.numeric' => 'سعر التأكيد يجب أن يكون رقم.',
            'status.in' => 'الحالة يجب أن تكون published أو not_published أو under_review.',
            'rating.max' => 'التقييم يجب ألا يزيد عن 5.',
            'category_id.required' => 'التصنيف مطلوب.',
            'open_at.required' => 'موعد بدء عمل المركز مطلوب.',
            'close_at.required' => 'موعد غلق المركز  مطلوب.',
            'category_id.exists' => 'التصنيف غير موجود.',
        ];
    }
}
