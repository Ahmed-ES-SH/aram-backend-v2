<?php

namespace App\Http\Controllers;

use App\Helpers\TextNormalizer;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\CardCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CardCategoryController extends Controller
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
    public function index()
    {
        try {
            $Categories = CardCategory::withCount('cards')->orderBy('created_at', 'desc')->paginate(30);
            if ($Categories->total() === 0) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function activeCategories(Request $request)
    {
        try {

            $state = $request->state;

            $Categories = CardCategory::withCount('cards')
                ->orderBy('created_at', 'desc')
                ->where('is_active', $state)
                ->paginate(30);

            if ($Categories->total() === 0) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }





    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            if (!$query) {
                return $this->errorResponse([
                    'message' => 'يرجى إدخال كلمة البحث.',
                ], 422);
            }

            // ✅ Normalize Arabic letters
            $normalizedQuery = TextNormalizer::normalizeArabic($query);

            // ✅ SQL replace chain to normalize Arabic columns
            $normalizedSql = TextNormalizer::sqlNormalizeColumn('title_ar');
            // ✅ Execute manual query without Scout
            $results = CardCategory::withCount('cards')
                ->where(function ($q) use ($normalizedQuery, $normalizedSql) {
                    $q->whereRaw("$normalizedSql LIKE ?", ["%$normalizedQuery%"])
                        ->orWhere('title_en', 'LIKE', "%$normalizedQuery%");
                })->paginate(30);

            return $this->paginationResponse($results, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function publicCategories()
    {
        try {
            $Categories = CardCategory::withCount('cards')->orderBy('created_at', 'desc')->where('is_active', true)->limit(12)->get();
            if ($Categories->isEmpty()) {
                return $this->noContentResponse();
            }
            return $this->successResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function AllCategories()
    {
        try {
            $Categories = CardCategory::withCount('cards')->orderBy('created_at', 'desc')->get();
            if ($Categories->isEmpty()) {
                return $this->noContentResponse();
            }
            return $this->successResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    
    public function AllPublicCategories()
    {
        try {
            $Categories = CardCategory::withCount('cards')->orderBy('created_at', 'desc')->get();
            if ($Categories->isEmpty()) {
                return $this->noContentResponse();
            }
            return $this->successResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $category = new CardCategory();
            $category->fill($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/cardcategories', 'image');
            }
            return $this->successResponse($category, 201);
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
            $category = CardCategory::with('cards')->findOrFail($id);
            return $this->successResponse($category, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        try {
            $category = CardCategory::findOrFail($id);
            $data = $request->validated();
            $category->update($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/cardcategories');
            }
            $category->fresh();
            $category->load('sub_categories');

            return $this->successResponse($category, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function updateState($id, Request $request)
    {
        try {
            $category = CardCategory::findOrFail($id);

            // Get the new value of is_active from the request
            $is_active = $request->is_active;

            // Update using correct array syntax
            $category->update([
                'is_active' => $is_active
            ]);

            $category->fresh();
            $category->load('cards');

            // Return the fresh updated model
            return $this->successResponse($category, 200);
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
            $articleCategory = CardCategory::findOrFail($id);

            if ($articleCategory->image) {
                $this->imageservice->deleteOldImage($articleCategory, 'images/cardcategories');
            }

            $articleCategory->delete();

            return $this->successResponse(['name' => $articleCategory->title_en], 200);
        } catch (QueryException $e) {
            // 1451 = Cannot delete or update a parent row: a foreign key constraint fails
            if ($e->errorInfo[1] == 1451) {
                return $this->errorResponse(
                    'لا يمكن حذف القسم لأنه مرتبط ببطاقات أخرى.',
                    400
                );
            }

            return $this->errorResponse('حدث خطأ في قاعدة البيانات.', 500);
        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ غير متوقع.', 500);
        }
    }
}
