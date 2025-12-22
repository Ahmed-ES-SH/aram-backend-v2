<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromoterRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\Promoter;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;


class PromoterController extends Controller
{

    use ApiResponse;


    public function index()
    {
        try {
            $promoters = Promoter::whereHas('promoter', function ($query) {
                $query->where('role', 'user');
            })->with('promoter:id,name,email,image,phone,role')->paginate(15);

            if ($promoters->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($promoters, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function searchForPromoters(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'query' => 'required|string|max:255'
            ]);
            $query = $validatedData['query'];

            $promoters = Promoter::with('promoter:id,name,email,image,phone')
                ->searchInPromoterData($query)
                ->paginate(15);

            if ($promoters->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($promoters, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function addPromoter(StorePromoterRequest $request)
    {
        try {
            $data = $request->validated();

            $modelClass = $data['promoter_type'] === 'user'
                ? User::class
                : Organization::class;

            $promotable = $modelClass::findOrFail($data['promoter_id']);

            // Prevent duplicate
            if ($promotable->promoter) {
                return response()->json([
                    'message' => 'This account already has a promoter profile.'
                ], 409);
            }

            $promoter = $promotable->promoter()->create([
                'referral_code' => $data['referral_code'],
                'discount_percentage' => $data['discount_percentage'],
                'status' => $data['status'],
            ]);

            $data = [
                'message' => 'Promoter added successfully.',
                'data' => $promoter,
            ];

            return $this->successResponse($data, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getPromoter($id, Request $request)
    {
        try {
            $request->validate([
                'activities' => 'nullable|boolean',
                'type' => 'required|in:user,organization'
            ]);

            // ===============================
            // Case 1: Get promoter basic info
            // ===============================
            $promoter = Promoter::with('promoter:id,name,email,image,phone')
                ->where('promoter_id', $id)
                ->where('promoter_type', $request->type)
                ->firstOrFail();

            // ======================================================
            // Case 2: Get paginated activities (data + last_page only)
            // ======================================================
            if ($request->activities) {
                $paginate = $promoter->activities()->paginate(10);

                $activities = [
                    'data' => $paginate->items(),
                    'last_page' => $paginate->lastPage(),
                    'total' => $paginate->total(),
                    'per_page' => $paginate->perPage(),
                ];

                return $this->successResponse([
                    'promoter' => $promoter,
                    'activities' => $activities
                ], 200);
            }

            return $this->successResponse([
                'promoter' => $promoter
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Disable a promoter without deleting it.
     */
    public function updatePromoter(Promoter $promoter, Request $request)
    {
        try {

            $validatedData = $request->validate([
                'status' => 'sometimes|in:active,disabled',
                'discount_percentage' => 'sometimes|numeric',
                'referral_code' => 'sometimes|string',
            ]);

            $promoter->update($validatedData);

            return $this->successResponse([
                'message' => 'Promoter disabled successfully.',
                'data' => $promoter,
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Permanently delete a promoter.
     */
    public function deletePromoter($id)
    {
        try {
            $promoter = Promoter::find($id);

            if (! $promoter) {
                return $this->errorResponse('Promoter not found.', 404);
            }

            $promoter->delete();

            return $this->successResponse('Promoter deleted successfully.', 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function checkPromoterCode(Request $request)
    {
        try {
            $request->validate([
                'ref_code' => 'required|exists:promoters,referral_code'
            ]);

            $promoter = Promoter::where('referral_code', $request->ref_code)->first();

            if (!$promoter) {
                return $this->errorResponse('Promoter not found.', 404);
            }

            if ($promoter->status == 'disabled') {
                return $this->errorResponse('Promoter is disabled.', 403);
            }

            return $this->successResponse($promoter, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
