<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceTrackingRequest;
use App\Http\Requests\UpdateServiceTrackingRequest;
use App\Http\Requests\UpdateServiceTrackingStatusRequest;
use App\Http\Requests\UpdateServiceTrackingPhaseRequest;
use App\Http\Services\NotificationService;
use App\Http\Services\TempUploadService;
use App\Http\Traits\ApiResponse;
use App\Models\ServiceTracking;
use App\Models\Organization;
use App\Models\PendingServiceOrderFile;
use App\Models\ServicePage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceTrackingFile;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ServiceTrackingController extends Controller
{

    use ApiResponse;

    protected $notificationService;
    protected $tempUploadService;

    public function __construct(NotificationService $notificationService, TempUploadService $tempUploadService)
    {
        $this->notificationService = $notificationService;
        $this->tempUploadService = $tempUploadService;
    }

    // ========== ADMIN FUNCTIONS ==========

    /**
     * Display a listing of all service trackings (Admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceTracking::with(['service', 'order', 'invoice']);

        // Filter by status
        if ($request->has('status')) {
            $query->status($request->status);
        }

        // Filter by phase
        if ($request->has('current_phase')) {
            $query->phase($request->current_phase);
        }

        // Filter by service
        if ($request->has('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Filter by user type
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by user_id
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $trackings = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $trackings,
        ]);
    }

    /**
     * Store a newly created service tracking (Admin).
     */
    public function store(StoreServiceTrackingRequest $request): JsonResponse
    {
        try {
            $sender = User::select('id', 'name', 'email', 'phone', 'image')->where('id', 1)->first();
            $service = ServicePage::select('id', 'slug')->where('id', $request->service_id)->first();
            $data = $request->validated();
            $data['start_time'] = now();

            $notificationData = [
                'recipient_id' => $request->user_id,
                'recipient_type' => $request->user_type,
                'sender_id' => 1,
                'sender_type' => 'user',
                'content' => 'هناك تحديث جديد بخصوص الطلب ذو الرقم ' . $request->service_order_id . ' بخصوص الخدمة ' . $service->slug,
            ];

            $tracking = null;

            $pending_files = PendingServiceOrderFile::where('service_order_id', $request->service_order_id)->get();

            DB::transaction(function () use ($data,  $pending_files, &$tracking) {
                $tracking = ServiceTracking::create($data);



                foreach ($pending_files as $file) {
                    $isImage = $file->mime_type && str_starts_with($file->mime_type, 'image/');
                    $fileType = $isImage ? 'design_file' : 'attachment';
                    ServiceTrackingFile::create([
                        'service_tracking_id' => $tracking->id,
                        'disk' => 'public_path',
                        'path' => $file->file_path,
                        'file_type' => $fileType,
                        'original_name' => $file->original_name,
                        'mime_type' => $file->mime_type,
                        'size' => $file->size,
                        'uploaded_by' => $data['user_id'],
                        'uploaded_by_type' => $data['user_type'],
                    ]);


                    $file->delete();
                }

                $tracking->load(['files']);
            });

            // Notification only after successful transaction
            $this->notificationService->sendNotification($notificationData, $sender);

            if ($tracking['metadata'] && is_string($tracking['metadata'])) {
                $tracking['metadata'] = json_decode($tracking['metadata'], true);
            }

            return $this->successResponse($tracking, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified service tracking (Admin).
     */
    public function show(ServiceTracking $serviceTracking): JsonResponse
    {
        $serviceTracking->load(['service', 'order', 'invoice']);

        // Load owner based on user_type
        if ($serviceTracking->isUserOwned()) {
            $serviceTracking->load('user');
        } else {
            $serviceTracking->load('organization');
        }

        return response()->json([
            'success' => true,
            'data' => $serviceTracking,
        ]);
    }

    /**
     * Update the specified service tracking (Admin).
     */
    public function update(UpdateServiceTrackingRequest $request, ServiceTracking $serviceTracking): JsonResponse
    {
        $serviceTracking->update($request->validated());

        // Handle file uploads
        $this->handleFileUploads($request, $serviceTracking);

        return response()->json([
            'success' => true,
            'message' => 'Service tracking updated successfully',
            'data' => $serviceTracking->fresh(['service', 'order', 'invoice']),
        ]);
    }

    /**
     * Remove the specified service tracking (Admin).
     */
    public function destroy(ServiceTracking $serviceTracking): JsonResponse
    {
        $serviceTracking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service tracking deleted successfully',
        ]);
    }

    /**
     * Update the status of a service tracking (Admin).
     */
    public function updateStatus(UpdateServiceTrackingStatusRequest $request, ServiceTracking $serviceTracking): JsonResponse
    {
        $oldStatus = $serviceTracking->status;
        $newStatus = $request->validated()['status'];

        // Automatically set start_time and end_time based on status changes
        $updateData = ['status' => $newStatus];

        if ($newStatus === ServiceTracking::STATUS_IN_PROGRESS && $oldStatus === ServiceTracking::STATUS_PENDING) {
            $updateData['start_time'] = now();
        }

        if (in_array($newStatus, [ServiceTracking::STATUS_COMPLETED, ServiceTracking::STATUS_CANCELLED])) {
            $updateData['end_time'] = now();
        }

        $serviceTracking->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $serviceTracking->fresh(),
        ]);
    }

    /**
     * Update the phase of a service tracking (Admin).
     */
    public function updatePhase(UpdateServiceTrackingPhaseRequest $request, ServiceTracking $serviceTracking): JsonResponse
    {
        $serviceTracking->updatePhase($request->validated()['current_phase']);

        return response()->json([
            'success' => true,
            'message' => 'Phase updated successfully',
            'data' => $serviceTracking->fresh(),
        ]);
    }

    /**
     * Advance to next phase (Admin).
     */
    public function advancePhase(ServiceTracking $serviceTracking): JsonResponse
    {
        $nextPhase = $serviceTracking->getNextPhase();

        if (!$nextPhase) {
            return response()->json([
                'success' => false,
                'message' => 'Already at the final phase',
            ], 400);
        }

        $serviceTracking->advancePhase();

        return response()->json([
            'success' => true,
            'message' => 'Advanced to phase: ' . $nextPhase,
            'data' => $serviceTracking->fresh(),
        ]);
    }

    /**
     * Get statistics for service trackings (Admin).
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = ServiceTracking::query();

        // Apply date filters if provided
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Get counts by status
        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->pending()->count(),
            'in_progress' => (clone $query)->inProgress()->count(),
            'completed' => (clone $query)->completed()->count(),
            'cancelled' => (clone $query)->cancelled()->count(),
        ];

        // Get counts by phase
        $byPhase = [];
        foreach (ServiceTracking::getPhases() as $phase) {
            $byPhase[$phase] = (clone $query)->phase($phase)->count();
        }
        $stats['by_phase'] = $byPhase;

        // Get counts by service
        $byService = ServiceTracking::selectRaw('service_id, count(*) as count')
            ->groupBy('service_id')
            ->with('service:id,slug')
            ->get();

        $stats['by_service'] = $byService;

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get available statuses and phases (for dropdowns).
     */
    public function getOptions(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'statuses' => ServiceTracking::getStatuses(),
                'phases' => ServiceTracking::getPhases(),
                'user_types' => ServiceTracking::getUserTypes(),
            ],
        ]);
    }

    // ========== USER FUNCTIONS ==========

    /**
     * Get current user's service trackings.
     */
    public function myTrackings(Request $request): JsonResponse
    {
        $user = Auth::user();
        $userType = $user instanceof Organization ? 'organization' : 'user';

        $query = ServiceTracking::forUser($user->id, $userType)
            ->with(['service', 'order', 'invoice']);

        // Filter by status
        if ($request->has('status')) {
            $query->status($request->status);
        }

        // Filter by phase
        if ($request->has('current_phase')) {
            $query->phase($request->current_phase);
        }

        // Filter to active only
        if ($request->boolean('active_only')) {
            $query->active();
        }

        $trackings = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $trackings,
        ]);
    }

    /**
     * Get a specific tracking for the current user.
     */
    public function myTrackingShow(ServiceTracking $serviceTracking): JsonResponse
    {
        $user = Auth::user();
        $userType = $user instanceof Organization ? 'organization' : 'user';

        // Ensure the tracking belongs to the current user
        if ($serviceTracking->user_id !== $user->id || $serviceTracking->user_type !== $userType) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this tracking',
            ], 403);
        }

        $serviceTracking->load(['service', 'order', 'invoice']);

        return response()->json([
            'success' => true,
            'data' => $serviceTracking,
        ]);
    }

    /**
     * Get active trackings count for current user.
     */
    public function myActiveCount(): JsonResponse
    {
        $user = Auth::user();
        $userType = $user instanceof Organization ? 'organization' : 'user';

        $count = ServiceTracking::forUser($user->id, $userType)
            ->active()
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'active_count' => $count,
            ],
        ]);
    }

    /**
     * Cancel a tracking (User can only cancel their own pending trackings).
     */
    public function cancelMyTracking(ServiceTracking $serviceTracking): JsonResponse
    {
        $user = Auth::user();
        $userType = $user instanceof Organization ? 'organization' : 'user';

        // Ensure the tracking belongs to the current user
        if ($serviceTracking->user_id !== $user->id || $serviceTracking->user_type !== $userType) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this tracking',
            ], 403);
        }

        // Users can only cancel pending trackings
        if ($serviceTracking->status !== ServiceTracking::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending trackings can be cancelled',
            ], 400);
        }

        $serviceTracking->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Tracking cancelled successfully',
            'data' => $serviceTracking->fresh(),
        ]);
    }

    /**
     * Get tracking by order ID.
     */
    public function getByOrder(int $orderId): JsonResponse
    {
        $tracking = ServiceTracking::where('order_id', $orderId)
            ->with(['service', 'invoice'])
            ->first();

        if (!$tracking) {
            return response()->json([
                'success' => false,
                'message' => 'No tracking found for this order',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tracking,
        ]);
    }

    /**
     * Get tracking by invoice ID.
     */
    public function getByInvoice(int $invoiceId): JsonResponse
    {
        $tracking = ServiceTracking::where('invoice_id', $invoiceId)
            ->with(['service', 'order'])
            ->first();

        if (!$tracking) {
            return response()->json([
                'success' => false,
                'message' => 'No tracking found for this invoice',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tracking,
        ]);
    }
    /**
     * Handle file uploads for service tracking.
     */
    private function handleFileUploads(Request $request, ServiceTracking $serviceTracking): void
    {
        if (!$request->hasFile('files')) {
            return;
        }

        $files = $request->file('files');
        $files = is_array($files) ? $files : [$files];

        $user = Auth::user();
        $uploadedByType = $user instanceof Organization ? 'organization' : 'user';

        // Base upload directory (relative to public/)
        $relativePath = 'uploads/service-tracking';
        $absolutePath = public_path($relativePath);

        // Ensure directory exists (secure permissions)
        if (!is_dir($absolutePath)) {
            mkdir($absolutePath, 0755, true);
        }

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            /*
         |----------------------------------------------------------
         | Extract ALL metadata BEFORE moving the file
         |----------------------------------------------------------
         */
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $mimeType     = $file->getMimeType();
            $size         = $file->getSize();

            $isImage = in_array($mimeType, [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ]);

            $fileType = $isImage ? 'design_file' : 'attachment';

            /*
         |----------------------------------------------------------
         | Generate safe unique filename
         |----------------------------------------------------------
         */
            $safeName = pathinfo($originalName, PATHINFO_FILENAME);
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $safeName);

            $filename = $safeName . '_' . uniqid() . '.' . $extension;

            /*
         |----------------------------------------------------------
         | Move file (this deletes the temp php file)
         |----------------------------------------------------------
         */
            $file->move($absolutePath, $filename);

            /*
         |----------------------------------------------------------
         | Persist file record
         |----------------------------------------------------------
         */
            ServiceTrackingFile::create([
                'service_tracking_id' => $serviceTracking->id,
                'disk'                => 'public_path',
                'path'                => url($relativePath . '/' . $filename),
                'file_type'           => $fileType,
                'original_name'       => $originalName,
                'mime_type'           => $mimeType,
                'size'                => $size,
                'uploaded_by'         => $user->id,
                'uploaded_by_type'    => $uploadedByType,
            ]);
        }
    }
}
