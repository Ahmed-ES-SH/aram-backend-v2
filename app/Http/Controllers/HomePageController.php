<?php

namespace App\Http\Controllers;

use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\HomePage;
use App\Models\WebsiteVideo;
use Exception;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    use ApiResponse;

    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }


    public function activeHeroSection()
    {
        try {
            $section = HomePage::findOrFail(1);
            $is_active = $section->column_30;
            return $this->successResponse($is_active, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getSection($id, Request $request)
    {
        try {
            $request->validate([
                'limit_number' => 'nullable|integer|min:1|max:30',
                'main_page' => 'nullable|boolean',
            ]);

            $limit = $request->input('limit_number', 30);

            $section = HomePage::findOrFail($id);

            if ($request->boolean('main_page')) {

                $Mainvideo = WebsiteVideo::where("video_id", 'main_page')->first();
                $demovideo = WebsiteVideo::where("video_id", 'demo_video')->first();

                $data = [
                    'id' => $section->id,
                    'main_video' => $Mainvideo,
                    'demo_video' => $demovideo,
                ];

                for ($i = 1; $i <= $limit; $i++) {
                    $column = 'column_' . $i;
                    $value = $section->$column;

                    $data[$column] = $this->isJson($value)
                        ? json_decode($value, true)
                        : $value;
                }

                return $this->successResponse($data, 200);
            }

            $data = [
                'id' => $section->id,
                'video' => $section->video, // always string (link)
                'image' => $section->image, // always string (link)
            ];

            // Add dynamic columns
            for ($i = 1; $i <= $limit; $i++) {
                $column = 'column_' . $i;
                $value = $section->$column;

                if ($this->isJson($value)) {
                    $data[$column] = json_decode($value, true);
                } else {
                    $data[$column] = $value;
                }
            }

            return $this->successResponse($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function updateSection(Request $request, $id)
    {
        try {
            // تحقق أساسي من الصورة
            $rules = [
                'image' => 'nullable|file|image|max:5048', // 5048 KB ≈ 5 MB
                'video' => 'nullable|max:30720', // 30 MB
            ];

            // لو فيه limit_number نضيف قواعد تحقق للأعمدة المطلوبة
            $limit = $request->query('limit_number', 0);

            for ($i = 1; $i <= $limit; $i++) {
                $rules['column_' . $i] = ['required']; // أي محتوى غير فاضي
            }

            $request->validate($rules);

            // تجهيز البيانات (استثناء الصورة لأنها هتتعامل لوحدها)
            $data = $request->except(['image', 'video']);

            // Update or Create
            $section = HomePage::updateOrCreate(
                ['id' => $id],
                $data
            );

            // معالجة الصورة إذا تم رفعها
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable(
                    $request,
                    $section,
                    'images/homepage',
                    'image'
                );
            }


            // معالجة الصورة إذا تم رفعها
            if ($request->hasFile('video')) {
                $this->imageservice->ImageUploaderwithvariable(
                    $request,
                    $section,
                    'videos/homepage',
                    'video'
                );
            } elseif ($request->has('video')) {
                $section->video = $request->video;
            }

            $section->save();

            return $this->successResponse($section, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    private function isJson($string): bool
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
