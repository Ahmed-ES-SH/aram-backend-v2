<?php

namespace App\Http\Controllers;

use App\Helpers\TextNormalizer;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Article;
use App\Models\ArticleTag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ArticleController extends Controller
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
            $articles = Article::query()
                ->when($request->filled('category_id'), function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                })
                ->when($request->filled('author_id'), function ($q) use ($request) {
                    $q->where('author_id', $request->author_id);
                })
                ->when($request->filled('status'), function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->filled('from_date'), function ($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->from_date);
                })
                ->when($request->filled('to_date'), function ($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->to_date);
                })
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($subQ) use ($search) {
                        $subQ->where('title_en', 'like', "%{$search}%")
                            ->orWhere('title_ar', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->with(['author:id,name,image', 'category', 'interactions:id,article_id,totalReactions'])
                ->withCount('comments', 'tags')
                ->paginate(20);

            if ($articles->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getArticlesBySearch(Request $request)
    {
        try {
            // التحقق من صحة الإدخال
            $validated = $request->validate([
                'search_content' => 'required|string|min:2'
            ]);

            // استخدام القيمة الصحيحة
            $contentSearch = '%' . $validated['search_content'] . '%';

            // البحث فقط في المقالات المنشورة
            $articles = Article::where('title_en', 'like', $contentSearch)
                ->orWhere('title_ar', 'like', $contentSearch)
                ->orWhere('content_en', 'like', $contentSearch)
                ->orWhere('content_ar', 'like', $contentSearch)
                ->orderByDesc('views')
                ->withCount('comments', 'tags')
                ->with([
                    'author:id,name,image',
                    'category:id,title_en,title_ar',
                    'interactions:id,article_id,totalReactions',
                ])
                ->paginate(20);

            if ($articles->total() == 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function topTenArticlesByViews()
    {
        try {
            $articles = Article::whereNotNull('views')
                ->where('status', 'published')
                ->orderByDesc('views')
                ->with(['author:id,name,image', 'category:id,title_en,title_ar,image', 'tags:id,tag'])
                ->limit(10)
                ->get();

            if ($articles->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($articles, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getLastThree()
    {
        try {
            $articles = Article::with(['author:id,name,image', 'category', 'tags:id,tag'])
                ->withCount('tags')
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->limit(3)->get();

            if ($articles->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($articles, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getArticlesByStatus($status, Request $request)
    {
        try {
            $query = $request->input('query');

            $builder = Article::where('status', $status);

            // ✅ Normalize the input query
            $normalizedQuery = $query ? TextNormalizer::normalizeArabic($query) : null;


            // ✅ SQL normalization expressions for columns
            $normalizedTitle_en = $this->normalizeColumn('title_en');
            $normalizedTitle_ar = $this->normalizeColumn('title_ar');
            $normalizedDescription_en = $this->normalizeColumn('content_en');
            $normalizedDescription_ar = $this->normalizeColumn('content_ar');


            $builder->when($query, function ($q) use ($normalizedQuery, $normalizedTitle_en, $normalizedTitle_ar, $normalizedDescription_en, $normalizedDescription_ar) {
                $q->where(function ($subQ) use ($normalizedQuery, $normalizedTitle_en, $normalizedTitle_ar, $normalizedDescription_en, $normalizedDescription_ar) {
                    $subQ->whereRaw("$normalizedTitle_en LIKE ?", ["%$normalizedQuery%"])
                        ->orWhereRaw("$normalizedTitle_ar LIKE ?", ["%$normalizedQuery%"])
                        ->orWhereRaw("$normalizedDescription_en LIKE ?", ["%$normalizedQuery%"])
                        ->orWhereRaw("$normalizedDescription_ar LIKE ?", ["%$normalizedQuery%"]);
                });
            });

            $builder->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });


            $articles = $builder
                ->with(['author:id,name,image', 'category', 'tags:id,tag'])
                ->withCount('tags')
                ->orderByDesc('created_at')
                ->paginate(12);


            if ($articles->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getArticlesByTag(Request $request)
    {
        try {
            $request->validate([
                'tag_id' => 'required|exists:tags,id',
                'category' => 'nullable|exists:article_categories,id'
            ]);

            $categoryId = $request->category;

            $articleTags = ArticleTag::where('tag_id', $request->tag_id)
                ->whereHas('article', function ($q) use ($categoryId) {
                    $q->where('status', 'published');
                })
                ->with(['article' => function ($query) use ($categoryId) {
                    $query->where('status', 'published')->with(['category', 'tags', 'author:id,name,image'])->withCount('tags');
                    if ($categoryId) {
                        $query->where('category_id', $categoryId);
                    }
                }])
                ->paginate(12);

            if ($articleTags->total() == 0) {
                return $this->noContentResponse();
            }

            // استخراج المقالات فقط من النتائج
            $articles = $articleTags->getCollection()->pluck('article')->filter();

            // إعادة الهيكلة لتعيد المقالات في مصفوفة منفصلة
            return response()->json([
                'data' => [
                    'articles' => $articles,
                    'pagination' => [
                        'current_page' => $articleTags->currentPage(),
                        'last_page'   => $articleTags->lastPage(),
                        'total' => $articleTags->total(),
                    ]
                ],
                'message' => 'Articles retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    private function normalizeColumn($column)
    {
        return "LOWER(
        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE($column, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و')
    )";
    }


    public function getRandomArticles()
    {
        try {
            $articles = Article::where('status', 'published')->with(['category', 'tags', 'author'])->withCount('tags')->limit(8)->get();
            return $this->successResponse($articles, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getPublishedArticlesBySearch(Request $request)
    {
        try {
            // التحقق من صحة الإدخال
            $validated = $request->validate([
                'search_content' => 'required|string|min:2'
            ]);

            // استخدام القيمة الصحيحة
            $contentSearch = '%' . $validated['search_content'] . '%';

            // البحث فقط في المقالات المنشورة
            $articles = Article::where('status', 'published')
                ->where(function ($query) use ($contentSearch) {
                    $query->where('title_en', 'like', $contentSearch)
                        ->orWhere('title_ar', 'like', $contentSearch)
                        ->orWhere('content_en', 'like', $contentSearch)
                        ->orWhere('content_ar', 'like', $contentSearch);
                })
                ->orderByDesc('views')
                ->with([
                    'author:id,name,image',
                    'category:id,title_en,title_ar',
                    'interactions:id,article_id,totalReactions'
                ])
                ->withCount('comments')
                ->paginate(20);

            if ($articles->total() == 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($articles, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }






    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        try {
            $data = $request->validated();
            $article = new Article();
            $article = Article::create(Arr::except($data, ['image']));
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $article, 'images/articles', 'image');
            }
            $article->refresh();
            return $this->successResponse($article, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        try {
            $article = Article::with(['category', 'tags', 'author', 'interactions'])->findOrFail($id);
            return $this->successResponse($article, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateArticleRequest $request)
    {
        try {
            $data = $request->validated();
            $article = Article::findOrFail($id);
            $article->update(Arr::except($data, ['image']));
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $article, 'images/articles', 'image');
            }
            $article->refresh();
            return $this->successResponse($article, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);
            if ($article->image) {
                $this->imageservice->deleteOldImage($article, 'images/articles');
            }
            $article->delete();
            return $this->successResponse(['message' => 'تم حذف المقال بنجاح', 'title' => $article->title_en], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
