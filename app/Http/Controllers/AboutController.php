<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAboutContentRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use ApiResponse;
    protected $imageservice;

    public function __construct(ImageService $imageservice)
    {
        $this->imageservice = $imageservice;
    }





    public function index()
    {
        try {
            $companydetailes = About::findOrFail(1);
            return $this->successResponse($companydetailes, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getcooperation_pdf()
    {
        try {
            $model = About::findOrFail(1);
            return $this->successResponse($model->cooperation_pdf, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function uploadcooperation_pdf(Request $request)
    {
        try {
            $model = About::findOrFail(1);
            $model->cooperation_pdf = $request->cooperation_pdf;
            $model->save();
            return $this->successResponse($model->cooperation_pdf, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAboutContentRequest $request)
    {
        try {
            $detailes = About::findOrFail(1);
            $data = $request->validated();
            $detailes->fill($data);

            // معالجة الصور
            $imageFields = ['first_section_image', 'second_section_image', 'thired_section_image', 'fourth_section_image', 'cooperation_pdf'];
            foreach ($imageFields as $field) {
                if ($request->has($field)) {
                    $this->imageservice->ImageUploaderwithvariable($request, $detailes, 'images/companydetailes', $field);
                }
            }

            // حفظ البيانات
            $detailes->save();

            return $this->successResponse($detailes, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
