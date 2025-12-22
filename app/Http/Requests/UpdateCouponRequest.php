<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
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
            'code'           => 'sometimes|unique:coupons,code',
            'title'          => 'sometimes|string|max:255',
            'description'    => 'sometimes|string',
            'image'          => 'nullable|file|max:5048',
            'type'           => 'sometimes|in:user,organization,general',
            'benefit_type'   => 'sometimes|in:percentage,fixed,free_card',
            'discount_value' => 'nullable|numeric|min:0',
            'start_date'     => 'sometimes|date|before_or_equal:end_date',
            'end_date'       => 'sometimes|date|after_or_equal:start_date',
            'category_id'    => 'nullable|exists:categories,id',
            'usage_limit'    => 'nullable|integer|min:1',
            'status'         => 'sometimes|in:active,inactive,expired',
        ];
    }


    protected function prepareForValidation()
    {

        if ($this->has('sub_categories') && is_string($this->sub_categories)) {
            $this->merge([
                'sub_categories' => json_decode($this->sub_categories, true),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'code.required'           => 'Coupon code is required.',
            'code.unique'             => 'This coupon code is already taken.',
            'title.required'          => 'Coupon title is required.',
            'description.required'    => 'Coupon description is required.',
            'type.in'                 => 'Coupon type must be either user, organization, or general.',
            'benefit_type.in'         => 'Benefit type must be one of: percentage, fixed, free_card.',
            'discount_value.numeric'  => 'Discount value must be numeric.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'category_id.exists'      => 'Selected category does not exist.',
            'status.in'               => 'Status must be active, inactive, or expired.',
        ];
    }
}
