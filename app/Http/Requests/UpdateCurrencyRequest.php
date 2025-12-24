<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends BaseFormRequest
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
            'name'          => 'sometimes|required|string|max:255',
            'code'          => 'sometimes|required|string|max:10',
            'symbol'        => 'sometimes|required|string|max:10',
            'exchange_rate' => 'sometimes|required|numeric|min:0',
            'is_default'    => 'nullable|boolean',
        ];
    }




    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
}
