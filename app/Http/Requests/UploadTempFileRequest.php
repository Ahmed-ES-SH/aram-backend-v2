<?php

namespace App\Http\Requests;

use App\Http\Services\TempUploadService;
use Illuminate\Foundation\Http\FormRequest;

class UploadTempFileRequest extends FormRequest
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
        $maxSize = TempUploadService::MAX_FILE_SIZE / 1024; // Convert to KB
        $allowedMimes = TempUploadService::getAllowedMimesString();

        return [
            'service_order_id' => 'required|exists:service_orders,id',
            'file' => [
                'required',
                'file',
                'max:' . $maxSize,
                'mimetypes:' . $allowedMimes,
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'A file is required.',
            'file.file' => 'The uploaded item must be a file.',
            'file.max' => 'The file must not be greater than 10MB.',
            'file.mimetypes' => 'The file type is not allowed. Allowed types: images (JPEG, PNG, GIF, WebP), PDF, Word, Excel.',
        ];
    }
}
