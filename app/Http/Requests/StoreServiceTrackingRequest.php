<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceTrackingRequest extends FormRequest
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
            'service_id' => 'required|exists:service_pages,id',
            'user_id' => 'required|integer',
            'user_type' => ['required', 'string', Rule::in(['user', 'organization'])],
            'service_order_id ' => 'nullable|exists:service_orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'status' => ['nullable', 'string', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'current_phase' => ['nullable', 'string', Rule::in([
                'initiation',
                'planning',
                'execution',
                'monitoring',
                'review',
                'delivery',
                'support',
            ])],
            'metadata' => 'nullable',
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
            'service_id.required' => 'Service is required',
            'service_id.exists' => 'Selected service does not exist',
            'user_id.required' => 'User is required',
            'user_type.required' => 'User type is required',
            'user_type.in' => 'User type must be either user or organization',
            'status.in' => 'Invalid status value',
            'current_phase.in' => 'Invalid phase value',
            'end_time.after_or_equal' => 'End time must be after or equal to start time',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'pending']);
        }

        // Set default phase if not provided
        if (!$this->has('current_phase')) {
            $this->merge(['current_phase' => 'initiation']);
        }

        if ($this->has('files') && is_string($this->files)) {
            $this->merge([
                'files' => json_decode($this->files, true),
            ]);
        }
    }
}
