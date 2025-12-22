<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'code'           => 'required|string|max:50|unique:coupons,code',
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'image'          => 'nullable|file|max:5048',
            'type'           => 'required|in:user,organization,general',
            'benefit_type'   => 'required|in:percentage,fixed,free_card',
            'discount_value' => 'nullable|numeric|min:0',
            'start_date'     => 'required|date|before_or_equal:end_date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'category_id'    => 'required|exists:categories,id',
            'usage_limit'    => 'nullable|integer|min:1',
            'status'         => 'required|in:active,inactive,expired',
            'organizations' => 'sometimes|array',
            'users' => 'sometimes|array'
        ];
    }



    protected function prepareForValidation()
    {

        if ($this->has('sub_categories') && is_string($this->sub_categories)) {
            $this->merge([
                'sub_categories' => json_decode($this->sub_categories, true),
            ]);
        }
        if ($this->has('users') && is_string($this->users)) {
            $this->merge([
                'users' => json_decode($this->users, true),
            ]);
        }
        if ($this->has('organizations') && is_string($this->organizations)) {
            $this->merge([
                'organizations' => json_decode($this->organizations, true),
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
            'type.in'                 => 'Coupon type must be either user or organization , general',
            'benefit_type.in'         => 'Benefit type must be one of: percentage, fixed, free_card.',
            'discount_value.required' => 'Discount value is required.',
            'discount_value.numeric'  => 'Discount value must be numeric.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'category_id.exists'      => 'Selected category does not exist.',
            'status.in'               => 'Status must be active, inactive, or expired.',
        ];
    }
}
