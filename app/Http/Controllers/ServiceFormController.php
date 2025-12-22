<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceFormRequest;
use App\Http\Requests\UpdateServiceFormRequest;
use App\Http\Traits\ApiResponse;
use App\Models\ServiceForm;
use App\Models\ServicePage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceFormController extends Controller
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

    // ========== ADMIN ENDPOINTS ==========

    /**
     * List all service forms (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ServiceForm::with(['servicePage:id,slug', 'fields']);

            // Filter by service page
            if ($request->has('service_page_id')) {
                $query->where('service_page_id', $request->service_page_id);
            }

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $forms = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->paginationResponse($forms);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a new service form (Admin)
     */
    public function store(StoreServiceFormRequest $request): JsonResponse
    {
        try {
            $form = ServiceForm::create($request->validated());

            return $this->successResponse(
                $form->load('fields'),
                201,
                'Service form created successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get a single service form with fields (Admin)
     */
    public function show(ServiceForm $serviceForm): JsonResponse
    {
        try {
            $serviceForm->load(['servicePage:id,slug', 'fields']);

            return $this->successResponse($serviceForm);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update a service form (Admin)
     */
    public function update(UpdateServiceFormRequest $request, ServiceForm $serviceForm): JsonResponse
    {
        try {
            $serviceForm->update($request->validated());
            $serviceForm->incrementVersion();

            return $this->successResponse(
                $serviceForm->fresh(['fields']),
                200,
                'Service form updated successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete a service form (Admin)
     */
    public function destroy(ServiceForm $serviceForm): JsonResponse
    {
        try {
            $serviceForm->delete();

            return $this->successResponse([], 200, 'Service form deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Duplicate a service form (Admin)
     */
    public function duplicate(ServiceForm $serviceForm): JsonResponse
    {
        try {
            $newForm = $serviceForm->duplicate();

            return $this->successResponse(
                $newForm->load('fields'),
                201,
                'Service form duplicated successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Toggle form active status (Admin)
     */
    public function toggleActive(ServiceForm $serviceForm): JsonResponse
    {
        try {
            $serviceForm->update(['is_active' => !$serviceForm->is_active]);

            return $this->successResponse(
                $serviceForm->fresh(),
                200,
                $serviceForm->is_active ? 'Form activated' : 'Form deactivated'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ========== PUBLIC ENDPOINTS ==========

    /**
     * Get form schema for a service (Public/User)
     */
    public function getFormSchema(string $slug, Request $request): JsonResponse
    {
        try {
            $locale = $this->getLocale($request);

            $servicePage = ServicePage::where('slug', $slug)
                ->where('status', 'active')
                ->first();

            if (!$servicePage) {
                return $this->notFoundResponse('Service not found');
            }

            $form = ServiceForm::where('service_page_id', $servicePage->id)
                ->active()
                ->with('fields')
                ->first();

            if (!$form) {
                return $this->successResponse([
                    'has_form' => false,
                    'form' => null,
                    'fields' => [],
                ]);
            }

            return $this->successResponse([
                'has_form' => true,
                'form' => $form->getSchema($locale),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
