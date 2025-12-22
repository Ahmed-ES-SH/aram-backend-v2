<?php

namespace App\Http\Services\OrganizationServices;

use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ImageService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteOrganizationService
{

    use ApiResponse;
    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }


    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $org = Organization::findOrFail($id);

                // Delete org images
                if (!empty($org->image)) {
                    $this->imageservice->deleteOldImage($org, 'images/organizations');
                }
                if (!empty($org->logo)) {
                    $this->imageservice->deleteOldImage($org, 'images/logo-organizations');
                }

                // Delete offers images
                foreach ($org->offers as $offer) {
                    if (!empty($offer->image)) {
                        $this->imageservice->deleteOldImage($offer, 'images/offers');
                    }
                }

                // Delete offers themselves
                $org->offers()->delete();

                // Detach many-to-many
                $org->subCategories()->detach();
                $org->categories()->detach();
                $org->keywords()->detach();

                // Delete benefits
                $org->benefits()->delete();

                // Delete organization
                $org->delete();
            });

            return $this->successResponse(['message' => 'Organization deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Organization not found', 404);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
