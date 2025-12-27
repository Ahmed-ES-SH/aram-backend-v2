<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationReview;
use App\Http\Traits\ApiResponse;
use App\Models\OrganizationReview;
use Illuminate\Http\Request;

class OrganizationReviewController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function ReviewsForOrg($id)
    {
        try {
            $reviewsforOrg = OrganizationReview::where('organization_id', $id)
                ->orderBy('created_at', 'desc')
                ->with('user', function ($query) {
                    $query->select('id', 'name', 'image');
                })
                ->paginate(10);


            if ($reviewsforOrg->isEmpty()) {
                return response()->json(['message' => "No reviews Founded For THis Organization"], 404);
            }

            return response()->json([
                'data' => $reviewsforOrg->items(),
                'pagination' => [
                    'current_page' => $reviewsforOrg->currentPage(),
                    'last_page' => $reviewsforOrg->lastPage(),
                    'total' => $reviewsforOrg->total(),
                    'per_page' => $reviewsforOrg->perPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function ReviewsNumbers($id)
    {
        try {
            // حساب عدد المراجعات لكل تصنيف من النجوم (من 1 إلى 5)
            $reviewsData = OrganizationReview::where('organization_id', $id)
                ->selectRaw('stars, COUNT(*) as count')
                ->groupBy('stars')
                ->orderBy('stars', 'asc')
                ->pluck('count', 'stars');

            // حساب العدد الكلي لجميع التقييمات
            $totalReviews = $reviewsData->sum();

            // حساب مجموع القيم بناءً على التقييمات
            $totalStars = 0;
            $starsSummary = [];

            for ($i = 1; $i <= 5; $i++) {
                $count = $reviewsData[$i] ?? 0;
                $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100, 2) : 0;

                $starsSummary[$i] = [
                    'count' => $count,
                    'percentage' => $percentage
                ];

                // إضافة قيمة التقييم إلى المجموع
                $totalStars += $count * $i;
            }

            // حساب متوسط التقييم (إجمالي النجوم مقسوماً على عدد التقييمات)
            $averageRating = $totalReviews > 0 ? round($totalStars / $totalReviews, 2) : 0;

            // إرجاع الاستجابة
            return response()->json([
                'organization_id' => $id,
                'total_reviews' => $totalReviews,
                'average_rating' => $averageRating,
                'reviews_count_by_stars' => $starsSummary
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed Error", ['message' => $e->getMessage()], 500);
        }
    }





    public function store(StoreOrganizationReview $request)
    {
        try {
            $data = $request->validated();
            $review = new OrganizationReview();
            $review->fill($data);
            $review->save();
            $reviewWithUser = $review->load('user');
            return $this->successResponse($reviewWithUser, 201);
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
            $review = OrganizationReview::findOrFail($id);
            $review->delete();
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
