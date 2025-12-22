<?php

namespace App\Http\Controllers;

use App\Http\Services\OrganizationServices\CreateOrganizationService;
use App\Http\Services\OrganizationServices\DeleteOrganizationService;
use App\Http\Services\OrganizationServices\FetchOrganizationsData;
use App\Http\Services\OrganizationServices\UpdateOrganizationService;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\StoreOrganiztionWithOfferRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Organization;
use Exception;

class OrganizationController extends Controller
{
    use ApiResponse;

    protected $createOrganizationService;
    protected $updateOrganizationService;
    protected $fetchOrganizationService;
    protected $deleteOrganizationService;

    public function __construct(
        CreateOrganizationService $createOrganizationService,
        UpdateOrganizationService $updateOrganizationService,
        FetchOrganizationsData $fetchOrganizationService,
        DeleteOrganizationService $deleteOrganizationService
    ) {
        $this->createOrganizationService = $createOrganizationService;
        $this->updateOrganizationService = $updateOrganizationService;
        $this->fetchOrganizationService = $fetchOrganizationService;
        $this->deleteOrganizationService = $deleteOrganizationService;
    }

    // ===============================
    // Case 1: Get total organizations count
    // ===============================
    public function organizationsCount()
    {
        $count = Organization::count();
        return $this->successResponse($count, 200);
    }

    // ===============================
    // Case 2: Get all organization IDs
    // ===============================
    public function getOrganizationsIds()
    {
        $users = Organization::pluck('id');
        return $this->successResponse($users, 200);
    }

    // ===============================
    // Case 3: Get public organization IDs
    // ===============================
    public function getPublicOrganizationsIds()
    {
        return $this->fetchOrganizationService->getPublicOrganizationsIds();
    }

    // ===============================
    // Case 4: Get organization with selected fields
    // ===============================
    public function organizationWithSelectedData(Request $request)
    {
        return $this->fetchOrganizationService->organizationWithSelectedData($request);
    }

    // ===============================
    // Case 5: Get list of organizations
    // ===============================
    public function index(Request $request)
    {
        return $this->fetchOrganizationService->index($request);
    }

    // ===============================
    // Case 6: Get active organizations
    // ===============================
    public function activeOrganizations(Request $request)
    {
        return $this->fetchOrganizationService->activeOrganizations($request);
    }

    // ===============================
    // Case 7: Get published organizations with selected fields
    // ===============================
    public function publishedOrganizationswithSelectedData(Request $request)
    {
        return $this->fetchOrganizationService->publishedOrganizationswithSelectedData($request);
    }

    // ===============================
    // Case 8: Get top 10 public organizations
    // ===============================
    public function TopTenPublicOrganizations()
    {
        return $this->fetchOrganizationService->TopTenPublicOrganizations();
    }

    // ===============================
    // Case 9: Get published organizations
    // ===============================
    public function publishedOrganizations(Request $request)
    {
        return $this->fetchOrganizationService->publishedOrganizations($request);
    }

    // ===============================
    // Case 10: Store new organization
    // ===============================
    public function store(StoreOrganizationRequest $request)
    {
        try {
            $organization = $this->createOrganizationService->store($request);
            return $this->successResponse($organization, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 11: Store organization with offer
    // ===============================
    public function StoreOgranizationWithOffer(StoreOrganiztionWithOfferRequest $request)
    {
        try {
            $createData = $this->createOrganizationService->StoreOgranizationWithOffer($request);
            return $this->successResponse([
                "organization" => $createData['organization'],
                "offer" => $createData['offer']
            ], 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 12: Validate unique email
    // ===============================
    public function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:organizations,email|unique:users,email'
        ]);

        return $this->successResponse([], 200);
    }

    // ===============================
    // Case 13: Get organization's working hours
    // ===============================
    public function getOrgTimeWork($id)
    {
        return $this->fetchOrganizationService->getOrgTimeWork($id);
    }


    // ===============================
    // Case 14: Show single organization details
    // ===============================
    public function show($id)
    {
        try {
            $organization = Organization::with(['subCategories', 'categories', 'keywords', 'benefits'])->findOrFail($id);
            $category = $organization->categories->first();
            $organization['category'] = $category;

            if (is_string($organization->location)) {
                $organization->location = json_decode($organization->location);
            }

            return $this->successResponse($organization, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 15: Update organization
    // ===============================
    public function update(UpdateOrganizationRequest $request, $id)
    {
        $organization = $this->updateOrganizationService->updateOrganization($request, $id);
        return $this->successResponse($organization, 200);
    }

    // ===============================
    // Case 16: Delete organization
    // ===============================
    public function destroy($id)
    {
        return $this->deleteOrganizationService->destroy($id);
    }

    // ===============================
    // Case 17: Check organization password
    // ===============================
    public function checkOrgPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        try {
            $org = Organization::findOrFail($id);

            if (Hash::check($request->password, $org->password)) {
                return $this->successResponse(['Message' => 'Password is Correct'], 'Done', 200);
            } else {
                return $this->errorResponse("Incorrect Password", 401);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // ===============================
    // Case 18: Get organization locations
    // ===============================
    public function getLocations(Request $request)
    {
        return $this->fetchOrganizationService->getLocations($request);
    }

    // ===============================
    // Case 18: Get OrganizationsForSelectionTable
    // ===============================
    public function OrganizationsForSelectionTable()
    {
        return $this->fetchOrganizationService->OrganizationsForSelectionTable();
    }
}
