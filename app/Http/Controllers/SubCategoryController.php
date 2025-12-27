<?php

namespace App\Http\Controllers;

use App\Helpers\TextNormalizer;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
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
            $Categories = SubCategory::orderBy('created_at', 'desc')->with('parent')->paginate(30);
            if ($Categories->total() === 0) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getSubCategoriesByParent(Request $request)
    {
        try {
            $request->validate([
                'parent_id' => 'required|exists:categories,id',
                'is_active' => 'nullable|boolean',
                'query'     => 'nullable|string|max:255',
            ]);

            $categories = SubCategory::with('parent:id,image,title_en')->withCount('organizations')->where('parent_id', $request->parent_id);

            if (!is_null($request->is_active)) {
                $categories->where('is_active', $request->is_active);
            }

            if (!empty($request->query('query'))) {
                $query = $request->query('query', ''); // default string

                // ✅ Normalize Arabic search term
                $normalizedQuery = TextNormalizer::normalizeArabic($query);

                // ✅ SQL normalize for Arabic column
                $normalizedSql = TextNormalizer::sqlNormalizeColumn('title_ar');

                // ✅ Fulltext search
                $categories->where(function ($q) use ($normalizedQuery, $normalizedSql, $query) {
                    $q->whereRaw("MATCH(title_en) AGAINST (? IN BOOLEAN MODE)", [$query])
                        ->orWhereRaw("MATCH($normalizedSql) AGAINST (? IN BOOLEAN MODE)", [$normalizedQuery]);
                });
            }

            $categories = $categories->orderBy('created_at', 'desc')->paginate(12);

            if ($categories->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function activeSubCategories(Request $request)
    {
        try {

            $state = $request->state;

            $Categories = SubCategory::orderBy('created_at', 'desc')->where('is_active', $state)->paginate(30);
            if ($Categories->total() === 0) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function publicSubCategories()
    {
        try {
            $Categories = SubCategory::orderBy('created_at', 'desc')->where('is_active', true)->paginate(15);
            if ($Categories->isEmpty()) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function AllSubCategories()
    {
        try {
            $Categories = SubCategory::orderBy('created_at', 'desc')->get();
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
    public function store(StoreSubCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $category = new SubCategory();
            $category->fill($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/subcategories', 'image');
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
        $category = SubCategory::findOrFail($id);
        return $this->successResponse($category, 200);
        try {
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateSubCategoryRequest $request)
    {
        try {
            $category = SubCategory::findOrFail($id);
            $data = $request->validated();
            $category->update($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/subcategories');
            }
            return $this->successResponse($category->fresh(), 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function updateState($id, Request $request)
    {
        try {
            $category = SubCategory::findOrFail($id);

            // Get the new value of is_active from the request
            $is_active = $request->is_active;

            // Update using correct array syntax
            $category->update([
                'is_active' => $is_active
            ]);

            // Return the fresh updated model
            return $this->successResponse($category->fresh(), 200);
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
            $Category = SubCategory::findOrFail($id);

            if ($Category->image) {
                $this->imageservice->deleteOldImage($Category, 'images/subcategories');
            }

            $Category->delete();

            return $this->successResponse($Category, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
