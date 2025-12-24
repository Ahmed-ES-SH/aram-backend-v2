<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionAnswerRequest;
use App\Http\Traits\ApiResponse;
use App\Models\QuestionAnswer;


class QuestionAnswerController extends Controller
{

    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Q_As = QuestionAnswer::orderBy('created_at', 'desc')->paginate(12);
            return  $this->paginationResponse($Q_As, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() // تصحيح اسم المفتاح
            ], 500); // إضافة كود الحالة 500
        }
    }


    public function approvedQuestions()
    {
        try {
            $approvedQuestions = QuestionAnswer::where('is_visible', true)
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return  $this->paginationResponse($approvedQuestions, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestionAnswerRequest $request)
    {
        try {
            $data = $request->validated();
            $Q_A = QuestionAnswer::create($data);
            return  $this->successResponse($Q_A, 201);
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
            $Q_A = QuestionAnswer::findOrFail($id);
            return $this->successResponse($Q_A, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(StoreQuestionAnswerRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $Q_A = QuestionAnswer::findOrFail($id);
            $Q_A->update($data);
            return $this->successResponse($Q_A, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500); // إضافة كود الحالة 500 للأخطاء العامة
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // البحث عن السجل باستخدام المعرف
            $Q_A = QuestionAnswer::findOrFail($id);
            $Q_A->delete(); // حذف السجل
            return $this->successResponse([], 200, 'Question and answer deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
