<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleComment;
use App\Http\Traits\ApiResponse;
use App\Models\ArticleComment;
use Exception;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    use ApiResponse;


    public function ArticleComments(Request $request)
    {
        try {
            $request->validate([
                'article_id' => 'required|exists:articles,id'
            ]);

            $comments = ArticleComment::where('article_id', $request->article_id)->with(['parent', 'user:id,name,image,email'])->paginate(8);

            if ($comments->total() == 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($comments, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(StoreArticleComment $request)
    {
        try {
            $data = $request->validated();
            $comment = ArticleComment::create($data);
            return $this->successResponse($comment, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function updateComment(Request $request, $commentId)
    {
        try {
            $userId = auth()->id();
            $comment = ArticleComment::where('id', $commentId)->where('user_id', $userId)->first();

            if (!$comment) {
                return $this->errorResponse([
                    'ar' => 'التعليق غير موجود أو لا تملك الصلاحية لتعديله.',
                    'en' => 'The comment does not exist or you do not have permission to edit it.'
                ], 403);
            }

            $data = $request->validate([
                'content' => 'required|string|max:500'
            ]);

            $comment->update($data);

            return $this->successResponse($comment, 200);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'ar' => 'حدث خطأ أثناء تحديث التعليق: ' . $e->getMessage(),
                'en' => 'An error occurred while updating the comment: ' . $e->getMessage()
            ], 500);
        }
    }



    public function likeComment($commentId)
    {
        try {
            $userId = auth()->id(); // جلب معرف المستخدم المسجل حاليًا

            // التحقق مما إذا كان التعليق موجودًا
            $comment = ArticleComment::find($commentId);
            if (!$comment) {
                return $this->errorResponse([
                    'ar' => 'التعليق غير موجود.',
                    'en' => 'The comment does not exist.'
                ], 404);
            }

            // التحقق مما إذا كان المستخدم قد سجل إعجابه بالفعل
            if ($comment->likes()->where('user_id', $userId)->exists()) {
                return $this->errorResponse([
                    'ar' => 'لقد سجلت إعجابك بهذا التعليق من قبل.',
                    'en' => 'You have already liked this comment.'
                ], 400);
            }

            // إضافة الإعجاب إلى التعليق
            $comment->likes()->attach($userId);

            return $this->successResponse([
                'ar' => 'تم تسجيل إعجابك بالتعليق.',
                'en' => 'You have liked the comment.'
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'ar' => 'حدث خطأ أثناء تسجيل الإعجاب: ' . $e->getMessage(),
                'en' => 'An error occurred while liking the comment: ' . $e->getMessage()
            ], 500);
        }
    }



    public function unlikeComment($commentId)
    {
        try {
            $userId = auth()->id(); // جلب معرف المستخدم المسجل حاليًا

            // التحقق مما إذا كان التعليق موجودًا
            $comment = ArticleComment::find($commentId);
            if (!$comment) {
                return $this->errorResponse([
                    'ar' => 'التعليق غير موجود.',
                    'en' => 'The comment does not exist.'
                ], 404);
            }

            // التحقق مما إذا كان المستخدم قد سجل إعجابه من قبل
            if (!$comment->likes()->where('user_id', $userId)->exists()) {
                return $this->errorResponse([
                    'ar' => 'لم تسجل إعجابك بهذا التعليق من قبل.',
                    'en' => 'You have not liked this comment before.'
                ], 400);
            }

            // إلغاء الإعجاب
            $comment->likes()->detach($userId);

            return $this->successResponse([
                'ar' => 'تم إلغاء إعجابك بالتعليق.',
                'en' => 'You have unliked the comment.'
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'ar' => 'حدث خطأ أثناء إلغاء الإعجاب: ' . $e->getMessage(),
                'en' => 'An error occurred while unliking the comment: ' . $e->getMessage()
            ], 500);
        }
    }
}
