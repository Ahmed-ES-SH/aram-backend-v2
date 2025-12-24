<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
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
        $orgId = $this->route('id'); // لو بتستخدم route model binding

        return [
            'email'                 => 'sometimes|required|email|max:255|unique:organizations,email,' . $orgId,
            'password'              => 'sometimes|nullable',
            'title'                 => 'sometimes|required|string|max:255',
            'description'           => 'sometimes|required|string',
            'features'              => 'nullable|string',
            'accaptable_message'    => 'nullable|string',
            'unaccaptable_message'  => 'nullable|string',
            'location'              => 'nullable|json',
            'phone_number'          => 'sometimes|required|string|max:20',
            'confirmation_price'    => 'nullable|numeric',
            'confirmation_status'   => 'nullable|boolean',
            'open_at'               => 'nullable|string',
            'close_at'              => 'nullable|string',
            'url'                   => 'nullable|url',
            'image'                 => 'nullable|file',
            'logo'                  => 'nullable|file',
            'verification_code'     => 'nullable|string',
            'email_verified'        => 'boolean',
            'email_verification_token' => 'nullable|string',
            'active'                => 'boolean',
            'status'                => 'in:published,not_published,under_review',
            'rating'               => 'numeric|min:0|max:5',
            'number_of_reservations' => 'integer|min:0',
            'order'                 => 'integer|min:1|unique:organizations,order,' . $orgId,
            'is_signed'             => 'boolean',
            'booking_status'        => 'boolean',
            'cooperation_file'      => 'nullable|string',
            'account_type'          => 'string|in:organization',
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
            'password.min' => 'يجب أن تكون كلمة المرور على الأقل 8 أحرف.',
            'title.required' => 'اسم المؤسسة مطلوب.',
            'description.required' => 'الوصف مطلوب.',
            'phone_number.required' => 'رقم الهاتف مطلوب.',
            'confirmation_price.numeric' => 'سعر التأكيد يجب أن يكون رقم.',
            'status.in' => 'الحالة يجب أن تكون published أو not_published أو under_review.',
            'rateing.max' => 'التقييم يجب ألا يزيد عن 5.',
            'category_id.required' => 'التصنيف مطلوب.',
            'category_id.exists' => 'التصنيف غير موجود.',
        ];
    }
}
