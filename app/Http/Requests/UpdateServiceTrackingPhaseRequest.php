<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceTrackingPhaseRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'current_phase' => ['required', 'string', Rule::in([
                'initiation',
                'planning',
                'execution',
                'monitoring',
                'review',
                'delivery',
                'support',
            ])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'current_phase.required' => 'Phase is required',
            'current_phase.in' => 'Phase must be one of: initiation, planning, execution, monitoring, review, delivery, support',
        ];
    }
}
