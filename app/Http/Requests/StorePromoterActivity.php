<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromoterActivity extends FormRequest
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
            'promoter_type' => 'nullable',
            'promoter_id' => 'nullable|exists:users,id',
            'activity_type' => 'required|in:signup,purchase,visit',
            'metadata' => 'nullable',
            'ip_address' => 'nullable',
            'country' => 'nullable',
            'device_type' => 'nullable',
            'ref_code' => 'nullable',
            'commission_amount' => 'nullable',
            'activity_at' => 'nullable',
            'member_id' => 'nullable',
            'member_type' => 'nullable',
            'browser' => 'nullable',
        ];
    }
}
