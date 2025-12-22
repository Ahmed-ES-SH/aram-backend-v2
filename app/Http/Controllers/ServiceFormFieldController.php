<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceFormFieldRequest;
use App\Http\Requests\UpdateServiceFormFieldRequest;
use App\Http\Traits\ApiResponse;
use App\Models\ServiceForm;
use App\Models\ServiceFormField;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceFormFieldController extends Controller
{
    use ApiResponse;

    /**
     * List all fields for a form (Admin)
     */
    public function index(ServiceForm $serviceForm): JsonResponse
    {
        try {
            $fields = $serviceForm->fields()->ordered()->get();

            return $this->successResponse($fields);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Add a field to a form (Admin)
     */
    public function store(StoreServiceFormFieldRequest $request, ServiceForm $serviceForm): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['service_form_id'] = $serviceForm->id;

            // Auto-set order if not provided
            if (!isset($data['order'])) {
                $data['order'] = $serviceForm->fields()->max('order') + 1;
            }

            $field = ServiceFormField::create($data);

            // Increment form version
            $serviceForm->incrementVersion();

            return $this->successResponse($field, 201, 'Field added successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get a single field (Admin)
     */
    public function show(ServiceFormField $serviceFormField): JsonResponse
    {
        try {
            return $this->successResponse($serviceFormField);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update a field (Admin)
     */
    public function update(UpdateServiceFormFieldRequest $request, ServiceFormField $serviceFormField): JsonResponse
    {
        try {
            $serviceFormField->update($request->validated());

            // Increment form version
            $serviceFormField->form->incrementVersion();

            return $this->successResponse(
                $serviceFormField->fresh(),
                200,
                'Field updated successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Delete a field (Admin)
     */
    public function destroy(ServiceFormField $serviceFormField): JsonResponse
    {
        try {
            $form = $serviceFormField->form;
            $serviceFormField->delete();

            // Increment form version
            $form->incrementVersion();

            return $this->successResponse([], 200, 'Field deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Reorder fields (Admin)
     */
    public function reorder(Request $request, ServiceForm $serviceForm): JsonResponse
    {
        try {
            $request->validate([
                'fields' => 'required|array',
                'fields.*.id' => 'required|exists:service_form_fields,id',
                'fields.*.order' => 'required|integer|min:0',
            ]);

            foreach ($request->fields as $fieldData) {
                ServiceFormField::where('id', $fieldData['id'])
                    ->where('service_form_id', $serviceForm->id)
                    ->update(['order' => $fieldData['order']]);
            }

            // Increment form version
            $serviceForm->incrementVersion();

            return $this->successResponse(
                $serviceForm->fields()->ordered()->get(),
                200,
                'Fields reordered successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get available field types (Admin)
     */
    public function getFieldTypes(): JsonResponse
    {
        return $this->successResponse([
            'field_types' => ServiceFormField::FIELD_TYPES,
            'visibility_conditions' => ['equals', 'not_equals', 'contains', 'not_empty', 'empty'],
        ]);
    }
}
