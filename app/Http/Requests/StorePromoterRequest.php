<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromoterRequest extends FormRequest
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
            'promoter_type' => 'required|in:user,organization',
            'promoter_id' => 'required|integer',
            'referral_code' => 'required|string|max:255',
            'discount_percentage' => 'required|decimal:1,5',
            'status' => 'required|in:active,disabled',
        ];
    }
}
