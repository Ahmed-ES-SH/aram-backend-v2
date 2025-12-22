<?php

namespace App\Http\Controllers;

use App\Helpers\TextNormalizer;
use App\Http\Traits\ApiResponse;
use App\Models\Keyword;
use Illuminate\Http\Request;

class KeywordController extends Controller
{

    use ApiResponse;


    public function index(Request $request)
    {
        try {
            $query = $request->get('query');

            // ✅ إذا تم إرسال query → بحث
            if (!empty($query)) {
                // Normalize the input query
                $normalizedQuery = TextNormalizer::normalizeArabic($query);

                // ✅ SQL normalization expressions for columns
                $normalizedTitle = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(title, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

                $keywords = Keyword::whereRaw("$normalizedTitle LIKE ?", ['%' . $normalizedQuery . '%'])->get();

                return $this->successResponse($keywords, 200);
            }

            // ✅ إذا لم يتم إرسال query → رجع كل النتائج
            $keywords = Keyword::all();
            return $this->successResponse($keywords, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255|unique:keywords,title',
            ]);

            $keyword = Keyword::create($request->only('title'));

            return $this->successResponse($keyword, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'keyword_id' => 'required|exists:keywords,id',
            ]);

            Keyword::findOrFail($request->keyword_id)->delete();

            return $this->successResponse(null, 200, 'The keyword deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
