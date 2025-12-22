<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganiztionWithOfferRequest extends FormRequest
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
            // Organization fields
            'email' => 'required|email|unique:organizations,email|unique:users,email',
            'password' => 'required|string|min:3',
            'title' => 'required|string',
            'description' => 'required|string',
            'open_at' => 'required|date_format:H:i',
            'close_at' => 'required|date_format:H:i',
            'confirmation_price' => 'required|numeric|min:0',
            'confirmation_status' => 'required|boolean',
            'booking_status' => 'required|boolean',
            'image' => 'required|image',
            'logo' => 'required|image',
            'category_id' => 'nullable',
            'categories' => 'required|array',
            'categories.*' => 'integer|exists:categories,id',
            'subcategories' => 'required|array',
            'subcategories.*' => 'integer|exists:sub_categories,id',
            'device_type' => 'nullable|string',

            // Offer fields
            'offer.title' => 'required|string',
            'offer.description' => 'required|string',
            'offer.discount_type' => 'required|in:percentage,fixed',
            'offer.discount_value' => 'required|numeric|min:0',
            'offer.start_date' => 'required|date',
            'offer.end_date' => 'required|date|after:offer.start_date',
            'offer.code' => 'required|string',
            'offer.category_id' => 'required|integer|exists:categories,id',
            'offer.image' => 'required|file|max:5048',
            'ref_code' => 'nullable|string',
        ];
    }


    protected function prepareForValidation()
    {

        if ($this->has('benefits') && is_string($this->benefits)) {
            $this->merge([
                'benefits' => json_decode($this->benefits, true),
            ]);
        }

        if ($this->has('subcategories') && is_string($this->subcategories)) {
            $this->merge([
                'subcategories' => json_decode($this->subcategories, true),
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
            // Organization messages
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تحتوي على 6 أحرف على الأقل',
            'title.required' => 'اسم المركز مطلوب',
            'description.required' => 'الوصف مطلوب',
            'open_at.required' => 'وقت الفتح مطلوب',
            'close_at.required' => 'وقت الإغلاق مطلوب',
            'confirmation_price.required' => 'سعر التأكيد مطلوب',
            'confirmation_status.required' => 'حالة التأكيد مطلوبة',
            'booking_status.required' => 'حالة الحجز مطلوبة',
            'image.required' => 'الصورة مطلوبة',
            'logo.required' => 'اللوجو مطلوب',

            // Offer messages
            'offer.title.required' => 'عنوان العرض مطلوب',
            'offer.description.required' => 'وصف العرض مطلوب',
            'offer.discount_type.required' => 'نوع الخصم مطلوب',
            'offer.discount_type.in' => 'نوع الخصم يجب أن يكون percentage أو fixed',
            'offer.discount_value.required' => 'قيمة الخصم مطلوبة',
            'offer.discount_value.numeric' => 'قيمة الخصم يجب أن تكون رقم',
            'offer.discount_value.min' => 'قيمة الخصم يجب أن تكون أكبر من 0',
            'offer.start_date.required' => 'تاريخ البداية مطلوب',
            'offer.end_date.required' => 'تاريخ الانتهاء مطلوب',
            'offer.end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',
            'offer.code.required' => 'كود العرض مطلوب',
            'offer.category_id.required' => 'التصنيف مطلوب',
            'offer.category_id.exists' => 'التصنيف غير موجود',
            'offer.image.required' => 'صورة العرض مطلوبة',
        ];
    }
}
