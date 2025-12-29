<?php

namespace App\Http\Services;

use App\Models\PendingServiceOrderFile;
use App\Models\ServiceTrackingFile;
use App\Models\Organization;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TempUploadService
{
    /**
     * Maximum file size in bytes (10MB).
     */
    public const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Allowed MIME types.
     */
    public const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * Hours until temp file expires.
     */
    public const TEMP_EXPIRES_HOURS = 24;

    /**
     * Upload directory relative to public path.
     */
    public const UPLOAD_DIR = 'uploads/temp-files';

    /**
     * Upload a single file to temporary storage.
     *
     * @param UploadedFile $file
     * @return PendingServiceOrderFile
     * @throws \Exception
     */
    public function upload(UploadedFile $file, $orderId): PendingServiceOrderFile
    {
        // Validate file
        $this->validateFile($file);

        // Get current user info
        $user = Auth::user();

        // Prepare upload directory
        $relativePath = self::UPLOAD_DIR;
        $absolutePath = public_path($relativePath);

        if (!is_dir($absolutePath)) {
            mkdir($absolutePath, 0755, true);
        }

        // Extract file metadata BEFORE moving
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate safe unique filename
        $safeName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $safeName);
        $filename = $safeName . '_' . uniqid() . '.' . $extension;

        // Move file to public path
        $file->move($absolutePath, $filename);

        // Create database record
        $pendingFile = PendingServiceOrderFile::create([
            'disk' => 'public_path',
            'service_order_id' => $orderId,
            'file_path' => env('BACK_END_URL') . '/' . $relativePath . '/' . $filename,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'expires_at' => now()->addHours(self::TEMP_EXPIRES_HOURS),
        ]);

        return $pendingFile;
    }

    /**
     * Validate uploaded file.
     *
     * @param UploadedFile $file
     * @throws \Exception
     */
    public function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds maximum allowed size of 10MB.');
        }

        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new \Exception('File type not allowed. Allowed types: images, PDF, Word, Excel.');
        }
    }

    /**
     * Get pending file by UUID.
     *
     * @param string $uuid
     * @return PendingServiceOrderFile|null
     */
    public function getByUuid(string $uuid): ?PendingServiceOrderFile
    {
        return PendingServiceOrderFile::where('uuid', $uuid)
            ->notExpired()
            ->notAttached()
            ->first();
    }

    /**
     * Attach temp files to a ServiceTracking entity.
     *
     * @param array $uuids
     * @param \App\Models\ServiceTracking $serviceTracking
     * @return array Array of created ServiceTrackingFile records
     */
    public function attachToServiceTracking(array $uuids, $serviceTracking): array
    {
        $user = Auth::user();
        $uploadedByType = $user instanceof Organization ? 'organization' : 'user';

        $createdFiles = [];

        foreach ($uuids as $uuid) {
            $pendingFile = $this->getByUuid($uuid);

            if (!$pendingFile) {
                continue; // Skip invalid/expired/already attached files
            }

            // Determine file type based on MIME
            $isImage = in_array($pendingFile->mime_type, [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ]);
            $fileType = $isImage ? 'design_file' : 'attachment';

            // Create final file record
            $serviceTrackingFile = ServiceTrackingFile::create([
                'service_tracking_id' => $serviceTracking->id,
                'disk' => $pendingFile->disk,
                'path' => url($pendingFile->file_path),
                'file_type' => $fileType,
                'original_name' => $pendingFile->original_name,
                'mime_type' => $pendingFile->mime_type,
                'size' => $pendingFile->size,
                'uploaded_by' => $user->id,
                'uploaded_by_type' => $uploadedByType,
            ]);

            // Mark pending file as attached
            $pendingFile->update(['attached_at' => now()]);

            $createdFiles[] = $serviceTrackingFile;
        }

        return $createdFiles;
    }

    /**
     * Delete a pending file by UUID.
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool
    {
        $pendingFile = PendingServiceOrderFile::where('uuid', $uuid)
            ->notAttached()
            ->first();

        if (!$pendingFile) {
            return false;
        }

        // Delete physical file
        $filePath = public_path($pendingFile->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete database record
        $pendingFile->delete();

        return true;
    }

    /**
     * Cleanup expired temp files.
     *
     * @return int Number of deleted files
     */
    public function cleanupExpired(): int
    {
        $expiredFiles = PendingServiceOrderFile::expired()
            ->notAttached()
            ->get();

        $count = 0;

        foreach ($expiredFiles as $file) {
            $filePath = public_path($file->file_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $file->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Get allowed MIME types as validation string.
     *
     * @return string
     */
    public static function getAllowedMimesString(): string
    {
        return implode(',', self::ALLOWED_MIMES);
    }
}
