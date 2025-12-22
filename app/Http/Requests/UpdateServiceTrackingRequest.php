<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceTrackingRequest extends FormRequest
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
            'service_id' => 'sometimes|exists:service_pages,id',
            'user_id' => 'sometimes|integer',
            'user_type' => ['sometimes', 'string', Rule::in(['user', 'organization'])],
            'order_id' => 'nullable|exists:orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'status' => ['sometimes', 'string', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'current_phase' => ['sometimes', 'string', Rule::in([
                'initiation',
                'planning',
                'execution',
                'monitoring',
                'review',
                'delivery',
                'support',
            ])],
            'metadata' => 'nullable|array',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // Max 10MB per file
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'service_id.exists' => 'Selected service does not exist',
            'user_type.in' => 'User type must be either user or organization',
            'status.in' => 'Invalid status value',
            'current_phase.in' => 'Invalid phase value',
            'end_time.after_or_equal' => 'End time must be after or equal to start time',
        ];
    }
}
