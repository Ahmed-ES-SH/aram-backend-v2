<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadTempFileRequest;
use App\Http\Services\TempUploadService;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TempUploadController extends Controller
{
    use ApiResponse;

    protected TempUploadService $tempUploadService;

    public function __construct(TempUploadService $tempUploadService)
    {
        $this->tempUploadService = $tempUploadService;
    }

    /**
     * Upload a single file to temporary storage.
     *
     * POST /api/uploads/temp
     *
     * @param UploadTempFileRequest $request
     * @return JsonResponse
     */
    public function upload(UploadTempFileRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');
            $pendingFile = $this->tempUploadService->upload($file, $request->service_order_id);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'id' => $pendingFile->uuid,
                    'original_name' => $pendingFile->original_name,
                    'mime_type' => $pendingFile->mime_type,
                    'size' => $pendingFile->size,
                    'expires_at' => $pendingFile->expires_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a pending upload by UUID.
     *
     * DELETE /api/uploads/temp/{uuid}
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(string $uuid): JsonResponse
    {
        $deleted = $this->tempUploadService->deleteByUuid($uuid);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'File not found or already attached.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ]);
    }
}
