<?php

namespace App\Http\Controllers;

use App\Helpers\TextNormalizer;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
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
            $query = $request->input('query');
            $categoryId = $request->input('category_id');
            $active = $request->input('active');
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');
            $duration = $request->input('duration');
            $numberOfPromotionalPurchases = $request->input('number_of_promotional_purchases');

            $cardsQuery = Card::query()
                // ✅ Get counts for related benefits & keywords
                ->withCount(['keywords', 'benefits'])
                // ✅ Get the keywords themselves
                ->with(['keywords:id,title', 'category']);

            // ✅ Normalize Arabic columns for search
            $normalizedTitleSql = TextNormalizer::sqlNormalizeColumn('title');
            $normalizedDescriptionSql = TextNormalizer::sqlNormalizeColumn('description');

            // ✅ Search filter (if query is provided)
            if ($query) {
                $normalizedQuery = TextNormalizer::normalizeArabic($query);
                $cardsQuery->where(function ($q) use ($normalizedQuery, $normalizedTitleSql, $normalizedDescriptionSql) {
                    $q->whereRaw("$normalizedTitleSql LIKE ?", ["%$normalizedQuery%"])
                        ->orWhereRaw("$normalizedDescriptionSql LIKE ?", ["%$normalizedQuery%"]);
                });
            }

            // ✅ Category filter (if category_id is provided)
            if ($categoryId) {
                $cardsQuery->where('category_id', $categoryId);
            }

            // ✅ Active filter
            if ($request->has('active')) {
                $cardsQuery->where('active', $active);
            }

            // ✅ Price range filter
            if ($minPrice) {
                $cardsQuery->where('price', '>=', $minPrice);
            }
            if ($maxPrice) {
                $cardsQuery->where('price', '<=', $maxPrice);
            }

            // ✅ Duration filter
            if ($duration) {
                $cardsQuery->where('duration', $duration);
            }

            // ✅ Number of promotional purchases filter
            if ($numberOfPromotionalPurchases) {
                $cardsQuery->where('number_of_promotional_purchases', $numberOfPromotionalPurchases);
            }

            // ✅ Get results with pagination (12 per page)
            $cards = $cardsQuery
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return $this->paginationResponse($cards, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function publicCards(Request $request)
    {
        try {
            $query      = $request->input('query');
            $categoryId = $request->input('category_id');

            // Base query: active cards only
            $cardsQuery = Card::where('active', 1)
                ->withCount(['keywords', 'benefits']) // ✅ Count services & benefits
                ->with(['keywords:id,title']); // ✅ Return only keyword ID & name

            // ✅ Filter by category if provided
            if (!empty($categoryId)) {
                $cardsQuery->where('category_id', $categoryId);
            }

            // ✅ If search query provided
            if (!empty($query)) {
                $normalizedQuery = TextNormalizer::normalizeArabic($query);

                $normalizedTitle       = TextNormalizer::sqlNormalizeColumn('title');
                $normalizedDescription = TextNormalizer::sqlNormalizeColumn('description');

                $cardsQuery->where(function ($q) use ($normalizedQuery, $normalizedTitle, $normalizedDescription) {
                    $q->whereRaw("$normalizedTitle LIKE ?", ["%$normalizedQuery%"])
                        ->orWhereRaw("$normalizedDescription LIKE ?", ["%$normalizedQuery%"]);
                });
            }

            // ✅ Pagination (default 12 per page, or custom limit if provided)
            $limit = $request->input('limit', 12);

            $cards = $cardsQuery
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            // ✅ Append keyword count to each card
            $cards->getCollection()->transform(function ($card) {
                $card->keywords_count = $card->keywords->count();
                return $card;
            });

            return $this->paginationResponse($cards, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function EightCards(Request $request)
    {
        try {


            // Base query: active cards only
            $cardsQuery = Card::where('active', 1)
                ->with(['keywords:id,title', 'benefits']); // keep relations


            // ✅ Limit to 8 cards max
            $cards = $cardsQuery
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            return $this->successResponse($cards, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCardRequest $request)
    {
        try {
            $data = $request->validated();

            // ✅ Create new card
            $card = new Card();
            $card->fill($data);

            // ✅ Handle image upload if exists
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $card, 'images/cards', 'image');
            }

            $card->save();

            // ✅ Store benefits if provided
            if ($request->has('benefits') && is_array($request->benefits)) {
                foreach ($request->benefits as $benefit) {
                    $card->benefits()->create([
                        'title' => $benefit['title'] ?? null,
                    ]);
                }
            }

            // ✅ Store keywords if provided
            if ($request->has('keywords') && is_array($request->keywords)) {
                $keywordIds = array_map(function ($keyword) {
                    return $keyword['keyword_id'] ?? null;
                }, $request->keywords);

                // Remove nulls
                $keywordIds = array_filter($keywordIds);

                if (!empty($keywordIds)) {
                    $card->keywords()->attach($keywordIds);
                }
            }

            return $this->successResponse($card->load(['benefits', 'keywords']), 201);
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
            // Get card with relations
            $card = Card::with(['benefits', 'keywords', 'category'])->withCount('benefits')->findOrFail($id);

            return $this->successResponse($card, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Card not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardRequest $request, $id)
    {
        try {
            $data = $request->validated();

            // Find card
            $card = Card::findOrFail($id);

            // Update card basic fields
            $card->fill($data);

            // Update image if provided
            if ($request->has('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $card, 'images/cards', 'image');
            }

            $card->save();

            // Update benefits if provided
            if ($request->has('benefits')) {
                // Delete old benefits
                $card->benefits()->delete();

                // Insert new benefits
                foreach ($request->benefits as $benefit) {
                    $card->benefits()->create([
                        'title' => $benefit['title'],
                    ]);
                }
            }

            // Update keywords if provided
            if ($request->has('keywords')) {
                // Extract IDs
                $keywordIds = collect($request->keywords)->pluck('id')->toArray();
                $card->keywords()->sync($keywordIds);
            }

            return $this->successResponse($card->load(['benefits', 'keywords']), 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Card not found', 404);
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


            $card = Card::findOrFail($id);

            if ($card->image) {
                $this->imageservice->deleteOldImage($card, 'images/cards');
            }

            // Delete related benefits
            $card->benefits()->delete();

            // Detach related keywords
            $card->keywords()->detach();

            // Delete the main card
            $card->delete();

            return $this->successResponse(null, 200, 'Card deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
