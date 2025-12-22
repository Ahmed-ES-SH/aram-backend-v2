<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\ArticleCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
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
            $Categories = ArticleCategory::orderBy('created_at', 'desc')->paginate(30);
            if ($Categories->total() === 0) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function allCategories()
    {
        try {
            $categories = ArticleCategory::orderBy('created_at', 'desc')->get();

            if ($categories->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong.', 500, $e->getMessage());
        }
    }




    public function publicCategories()
    {
        try {
            $categories = ArticleCategory::orderByDesc('created_at')->limit(10)->get();

            if ($categories->count() === 0) {
                return $this->noContentResponse();
            }

            return $this->successResponse($categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch categories.', [
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function allpublicCategories()
    {
        try {
            $categories = ArticleCategory::orderByDesc('created_at')->get();

            if ($categories->count() === 0) {
                return $this->noContentResponse();
            }

            return $this->successResponse($categories, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch categories.', [
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $request->validate([
                'query' => 'required|string|max:255',
            ]);

            $keyword = $request->input('query');

            // تنفيذ البحث
            $results = ArticleCategory::search($keyword)->get();

            return response()->json([
                'status' => true,
                'message' => 'Search results retrieved successfully.',
                'data' => $results,
            ]);
        } catch (\Throwable $e) {
            // تسجيل الخطأ في اللوجات

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while searching.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $category = new ArticleCategory();
            $category->fill($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/articleCategories', 'image');
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
        $category = ArticleCategory::findOrFail($id);
        return $this->successResponse($category, 200);
        try {
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
            $category = ArticleCategory::findOrFail($id);
            $data = $request->validated();
            $category->update($data);
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $category, 'images/articleCategories');
            }
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
            $articleCategory = ArticleCategory::findOrFail($id);

            if ($articleCategory->image) {
                $this->imageservice->deleteOldImage($articleCategory, 'images/articleCategories');
            }

            $articleCategory->delete();

            return $this->successResponse(['name' => $articleCategory->title_en], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
