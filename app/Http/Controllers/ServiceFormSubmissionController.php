<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\ServiceForm;
use App\Models\ServiceFormField;
use App\Models\ServiceFormSubmission;
use App\Models\ServiceFormSubmissionValue;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ServiceFormSubmissionController extends Controller
{
    use ApiResponse;

    /**
     * Get locale from request
     */
    private function getLocale(Request $request): string
    {
        $locale = $request->header('Accept-Language', 'en');
        return in_array($locale, ['ar', 'en']) ? $locale : 'en';
    }

    // ========== USER ENDPOINTS ==========

    /**
     * Submit a service form (User)
     */
    public function submit(Request $request, ServiceForm $serviceForm): JsonResponse
    {
        try {
            if (!$serviceForm->is_active) {
                return $this->errorResponse('This form is not available', 400);
            }

            $user = Auth::user();
            $userType = $user instanceof Organization ? 'organization' : 'user';

            // Build dynamic validation rules from form fields
            $rules = [];
            $messages = [];
            $locale = $this->getLocale($request);

            foreach ($serviceForm->fields as $field) {
                $fieldRules = $field->getValidationRules();
                $rules["fields.{$field->field_key}"] = $fieldRules;

                $labelField = "label_{$locale}";
                $label = $field->$labelField ?? $field->label_en;
                $messages["fields.{$field->field_key}.required"] = "{$label} is required";
            }

            // Validate submission
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Create submission in transaction
            $submission = DB::transaction(function () use ($request, $serviceForm, $user, $userType) {
                $submission = ServiceFormSubmission::create([
                    'service_form_id' => $serviceForm->id,
                    'user_id' => $user->id,
                    'user_type' => $userType,
                    'status' => ServiceFormSubmission::STATUS_PENDING,
                ]);

                // Save field values
                $fields = $request->input('fields', []);
                foreach ($serviceForm->fields as $field) {
                    $value = $fields[$field->field_key] ?? null;

                    // Handle file uploads
                    if (in_array($field->field_type, ['file_upload', 'image_upload'])) {
                        if ($request->hasFile("fields.{$field->field_key}")) {
                            $file = $request->file("fields.{$field->field_key}");
                            $path = $file->store('form_submissions/' . $submission->id, 'public');
                            $value = $path;
                        }
                    }

                    // Only save if value exists
                    if ($value !== null) {
                        ServiceFormSubmissionValue::create([
                            'submission_id' => $submission->id,
                            'field_id' => $field->id,
                            'value' => is_array($value) ? json_encode($value) : $value,
                        ]);
                    }
                }

                return $submission;
            });

            return $this->successResponse(
                $submission->load('values'),
                201,
                'Form submitted successfully'
            );
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get user's form submissions (User)
     */
    public function mySubmissions(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userType = $user instanceof Organization ? 'organization' : 'user';

            $query = ServiceFormSubmission::forUser($user->id, $userType)
                ->with(['form:id,name_ar,name_en', 'form.servicePage:id,slug']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $submissions = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10));

            return $this->paginationResponse($submissions);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get single submission details (User)
     */
    public function mySubmissionShow(ServiceFormSubmission $submission, Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userType = $user instanceof Organization ? 'organization' : 'user';

            if ($submission->user_id !== $user->id || $submission->user_type !== $userType) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $locale = $this->getLocale($request);
            $submission->load(['form', 'values.field']);

            return $this->successResponse([
                'submission' => $submission,
                'values' => $submission->getDetailedValues($locale),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ========== ADMIN ENDPOINTS ==========

    /**
     * List all form submissions (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ServiceFormSubmission::with([
                'form:id,name_ar,name_en,service_page_id',
                'form.servicePage:id,slug',
            ]);

            // Filter by form
            if ($request->has('service_form_id')) {
                $query->where('service_form_id', $request->service_form_id);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by user type
            if ($request->has('user_type')) {
                $query->where('user_type', $request->user_type);
            }

            // Filter by date range
            if ($request->has('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->has('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $perPage = $request->get('per_page', 15);
            $submissions = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->paginationResponse($submissions);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get single submission details (Admin)
     */
    public function show(ServiceFormSubmission $submission, Request $request): JsonResponse
    {
        try {
            $locale = $this->getLocale($request);
            $submission->load(['form', 'values.field']);

            // Load owner
            if ($submission->isUserOwned()) {
                $submission->load('user');
            } else {
                $submission->load('organization');
            }

            return $this->successResponse([
                'submission' => $submission,
                'values' => $submission->getDetailedValues($locale),
                'owner' => $submission->getOwner(),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update submission status (Admin)
     */
    public function updateStatus(Request $request, ServiceFormSubmission $submission): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:' . implode(',', ServiceFormSubmission::getStatuses()),
            ]);

            $submission->updateStatus($request->status);

            return $this->successResponse(
                $submission->fresh(),
                200,
                'Status updated successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete a submission (Admin)
     */
    public function destroy(ServiceFormSubmission $submission): JsonResponse
    {
        try {
            // Delete associated files
            foreach ($submission->values as $value) {
                if (in_array($value->field->field_type, ['file_upload', 'image_upload'])) {
                    if ($value->value) {
                        Storage::disk('public')->delete($value->value);
                    }
                }
            }

            $submission->delete();

            return $this->successResponse([], 200, 'Submission deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get submission statistics (Admin)
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $query = ServiceFormSubmission::query();

            if ($request->has('service_form_id')) {
                $query->where('service_form_id', $request->service_form_id);
            }

            if ($request->has('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->has('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $stats = [
                'total' => (clone $query)->count(),
                'pending' => (clone $query)->pending()->count(),
                'reviewed' => (clone $query)->reviewed()->count(),
                'approved' => (clone $query)->approved()->count(),
                'rejected' => (clone $query)->rejected()->count(),
            ];

            // By form
            $byForm = ServiceFormSubmission::selectRaw('service_form_id, count(*) as count')
                ->groupBy('service_form_id')
                ->with('form:id,name_en')
                ->get();

            $stats['by_form'] = $byForm;

            return $this->successResponse($stats);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
