<?php

namespace App\Http\Controllers;

use App\Models\organizationReview;
use App\Models\ReviewLikesCheck;
use Illuminate\Http\Request;

class ReviewLikesCheckController extends Controller
{


    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'organization_id' => 'required|exists:organizations,id',
                'review_id' => 'required|exists:organization_reviews,id',
            ]);

            // تحقق مما إذا كان المستخدم قد تفاعل مع المراجعة من قبل
            $existingReaction = ReviewLikesCheck::where('user_id', $request->user_id)
                ->where('review_id', $request->review_id)
                ->first();

            if ($existingReaction) {
                // إذا كان هناك تفاعل سابق، إرجاع رسالة تفيد بذلك
                return response()->json([
                    'message' => 'User has already reacted to this review.'
                ]);
            }

            // إذا لم يكن قد تفاعل مسبقًا، يتم حفظ التفاعل الجديد
            $check = new ReviewLikesCheck();
            $check->user_id = $request->user_id;
            $check->review_id = $request->review_id;
            $check->organization_id = $request->organization_id;
            $check->save();

            $review = organizationReview::findOrFail($request->review_id);
            $review->like_counts += 1;
            $review->save();

            return response()->json([
                'data' => $check,
                'message' => 'User reacted successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function GetReviewsForUser($orgId, $userId)
    {
        try {
            $reviews = ReviewLikesCheck::where('organization_id', $orgId)
                ->where('user_id', $userId)
                ->pluck('review_id'); // يجلب المعرّفات فقط كمصفوفة

            return response()->json(['data' =>  $reviews], 200); // إرجاع البيانات كمصفوفة مباشرة
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($reviewId, $userId)
    {
        try {
            // تحقق من وجود التفاعل الخاص بالمراجعة والمستخدم
            $check = ReviewLikesCheck::where('review_id', $reviewId)
                ->where('user_id', $userId) // تأكد من معرّف المستخدم
                ->first();

            // إذا لم يوجد تفاعل، أعد رسالة خطأ
            if (!$check) {
                return response()->json(['message' => 'No reaction found to remove.'], 404);
            }

            // ابحث عن المراجعة وحدث العداد
            $review = organizationReview::findOrFail($reviewId);
            if ($review->like_counts > 0) {
                $review->like_counts -= 1;
                $review->save();
            }

            // احذف التفاعل من جدول التفاعلات
            $check->delete();

            // إرجاع رسالة نجاح
            return response()->json(['message' => 'User successfully removed their reaction.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
