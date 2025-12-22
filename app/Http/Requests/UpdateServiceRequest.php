<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            // Basic info
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
            'rating' => 'nullable|numeric|min:0|max:5',
            'orders_count' => 'nullable|integer|min:0',
            'status' => 'sometimes|required|in:pending,approved,rejected,suspended',
            'images' => 'sometimes|array',
            'deleted_images' => 'sometimes|array',
            'benefits' => 'sometimes|array',
            'keywords' => 'sometimes|array',
            'order' => 'sometimes',
            'active' => 'sometimes',
            'organizations_supporters' => 'sometimes|array',

            // Benefit type
            'benefit_type' => 'sometimes|required|in:percentage,price,benefits',

            // Exclusive & card-related features
            'is_exclusive' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100|required_if:benefit_type,percentage',
            'discount_price' => 'nullable|numeric|min:0|required_if:benefit_type,price',
            'exclusive_start_date' => 'nullable|date',
            'exclusive_end_date' => 'nullable|date|after_or_equal:exclusive_start_date',

            // Foreign keys
            'card_id' => 'sometimes|required|exists:cards,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'category_id' => 'sometimes|required|exists:categories,id',
        ];
    }


    protected function prepareForValidation()
    {

        if ($this->has('benefits') && is_string($this->benefits)) {
            $this->merge([
                'benefits' => json_decode($this->benefits, true),
            ]);
        }

        if ($this->has('deleted_images') && is_string($this->deleted_images)) {
            $this->merge([
                'deleted_images' => json_decode($this->deleted_images, true),
            ]);
        }

        if ($this->has('organizations_supporters') && is_string($this->organizations_supporters)) {
            $this->merge([
                'organizations_supporters' => json_decode($this->organizations_supporters, true),
            ]);
        }

        if ($this->has('keywords') && is_string($this->keywords)) {
            $this->merge([
                'keywords' => json_decode($this->keywords, true),
            ]);
        }
    }
}
