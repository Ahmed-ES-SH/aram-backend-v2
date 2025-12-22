<?php

namespace App\Http\Controllers;

use App\Helpers\TextNormalizer;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{

    use ApiResponse;

    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = $request->get('query', '');

            // ✅ Normalize the input query
            $normalizedQuery = TextNormalizer::normalizeArabic($query);

            // ✅ SQL normalization expressions for columns
            $normalizedTitle = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(title, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
            $normalizedDescription = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(description, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

            // ✅ Start building the query
            $builder = Offer::with(['organization:id,title,description,image,rating', 'category'])
                ->when($query, function ($q) use ($normalizedQuery, $normalizedTitle, $normalizedDescription) {
                    $q->where(function ($subQ) use ($normalizedQuery, $normalizedTitle, $normalizedDescription) {
                        $subQ->whereRaw("$normalizedTitle LIKE ?", ["%$normalizedQuery%"])
                            ->orWhereRaw("$normalizedDescription LIKE ?", ["%$normalizedQuery%"]);
                    });
                })
                ->when($request->filled('status'), function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->filled('category_id'), function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                })
                ->when($request->filled('discount_type'), function ($q) use ($request) {
                    $q->where('discount_type',   $request->discount_type);
                })
                ->when($request->filled('start_date') || $request->filled('end_date'), function ($q) use ($request) {
                    $dateFrom = $request->get('start_date');
                    $dateTo   = $request->get('end_date');

                    // لو الاتنين موجودين
                    if ($dateFrom && $dateTo) {
                        $q->where(function ($subQ) use ($dateFrom, $dateTo) {
                            $subQ->whereBetween('start_date', [$dateFrom, $dateTo])
                                ->orWhereBetween('end_date', [$dateFrom, $dateTo]);
                        });
                    }
                    // لو بس dateFrom موجود
                    elseif ($dateFrom) {
                        $q->where(function ($subQ) use ($dateFrom) {
                            $subQ->where('start_date', '>=', $dateFrom)
                                ->orWhere('end_date', '>=', $dateFrom);
                        });
                    }
                    // لو بس dateTo موجود
                    elseif ($dateTo) {
                        $q->where(function ($subQ) use ($dateTo) {
                            $subQ->where('start_date', '<=', $dateTo)
                                ->orWhere('end_date', '<=', $dateTo);
                        });
                    }
                });


            // ✅ Apply pagination
            $perPage = $request->get('per_page', 12); // default = 10
            $offers = $builder->paginate($perPage);

            return $this->paginationResponse($offers, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function activeOffers(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $sortBy = $request->get('sort_by', 'newest'); // Default to newest

            // ✅ Normalize the input query
            $normalizedQuery = TextNormalizer::normalizeArabic($query);

            // ✅ SQL normalization expressions for columns
            $normalizedTitle = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(title, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
            $normalizedDescription = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(description, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

            // ✅ Start building the query
            $builder = Offer::with(['organization:id,title,description,image,rating', 'category:id,title_en,title_ar,bg_color,icon_name'])
                ->where('status', 'active') // ✅ تثبيت الحالة على active فقط
                ->when($query, function ($q) use ($normalizedQuery, $normalizedTitle, $normalizedDescription) {
                    $q->where(function ($subQ) use ($normalizedQuery, $normalizedTitle, $normalizedDescription) {
                        $subQ->whereRaw("$normalizedTitle LIKE ?", ["%$normalizedQuery%"])
                            ->orWhereRaw("$normalizedDescription LIKE ?", ["%$normalizedQuery%"]);
                    });
                })
                ->when($request->filled('category'), function ($q) use ($request) {
                    $q->where('category_id', $request->category);
                });

            // ✅ Apply sorting based on sort_by parameter
            switch ($sortBy) {
                case 'newest':
                    $builder->orderBy('created_at', 'desc');
                    break;

                case 'popular':
                    // Assuming you have a 'popularity' column or using number_of_uses
                    $builder->orderBy('number_of_uses', 'desc');
                    break;

                case 'expiring':
                    // Order by closest end_date (soonest to expire first)
                    $builder->orderBy('end_date', 'asc');
                    break;



                // Alternative discount sorting logic for mixed discount types
                case 'discount':
                    // For mixed discount types, we need to handle them differently
                    $builder->orderByRaw("
        CASE
            WHEN discount_type = 'percentage' THEN CAST(discount_value AS DECIMAL(10,2))
            WHEN discount_type = 'fixed' THEN CAST(discount_value AS DECIMAL(10,2)) / 10
            ELSE 0
        END DESC
    ");
                    break;

                default:
                    $builder->orderBy('created_at', 'desc');
                    break;
            }

            // ✅ Pagination
            $offers = $builder->paginate($request->get('per_page', 10));

            return $this->paginationResponse($offers, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function activeOffersByOrganization(Request $request, $id)
    {
        try {
            // Get pagination limit (default 10 if not provided)
            $limit = $request->input('limit', 10);

            // Get active offers for the organization
            $offers = Offer::where('organization_id', $id)
                ->where('status', 'active')
                ->whereDate('end_date', '>=', now()) // ✅ Check that offer is still active (not expired)
                ->with(['organization:id,title,description,image,rating', 'category:id,title_en,title_ar,bg_color,icon_name'])
                ->orderByRaw("
                CASE
                    WHEN discount_type = 'percentage' THEN CAST(discount_value AS DECIMAL(10,2))
                    WHEN discount_type = 'fixed' THEN CAST(discount_value AS DECIMAL(10,2)) / 10
                    ELSE 0
                END DESC
            ") // ✅ Sort by discount logic
                ->paginate($limit);

            // If no offers found
            if ($offers->total() === 0) {
                return $this->noContentResponse();
            }

            if ($request->has('limit') && !$request->has('page')) {
                return $this->successResponse($offers->items(), 200);
            }

            // Return paginated data
            return $this->paginationResponse($offers, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function accountOffers(Request $request)
    {
        try {
            $request->validate([
                'id'   => 'required|exists:organizations,id',
            ]);


            $offers = Offer::with(['category', 'organization:id,title,description,image,rating'])
                ->where('organization_id', $request->id)
                ->orderByDesc('created_at')
                ->paginate(12);

            if ($offers->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($offers, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfferRequest $request)
    {
        try {

            $data = $request->validated();

            $offer = Offer::create($data);

            // التعامل مع الصورة الرئيسية
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $offer, 'images/offers', 'image');
            }

            return $this->successResponse($offer, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $offer = Offer::with(['category', 'organization:id,title,description,image,rating'])->findOrFail($id);

            return $this->successResponse($offer, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferRequest $request, $id)
    {
        try {
            $offer = Offer::with(['category', 'organization:id,title,description,image,rating'])->findOrFail($id);
            $data = $request->validated();

            $offer->update($data);

            // التعامل مع الصورة الرئيسية
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $offer, 'images/offers', 'image');
            }

            return $this->successResponse($offer, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate that 'status' is present and is a valid string
            $validated = $request->validate([
                'status' => ['required', 'string', 'in:waiting,active,expired'],
            ]);

            // Retrieve the service by ID or fail
            $service = Offer::findOrFail($id);

            // Update only the status
            $service->update([
                'status' => $validated['status'],
            ]);

            // Return the updated service
            return $this->successResponse($service, 200, 'Offer status updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return $this->errorResponse($ve->errors(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $offer = Offer::findOrFail($id);

            if (!empty($offer->image)) {
                $this->imageservice->deleteOldImage($offer, 'images/offers');
            }

            $offer->delete();
            return $this->successResponse([], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Offer not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
