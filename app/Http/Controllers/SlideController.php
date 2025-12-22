<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSlideRequest;
use App\Http\Requests\UpdateSlideRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Slide;
use Exception;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class SlideController extends Controller
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
            $slides = Slide::all();

            // Decode JSON fields if needed
            $slides->transform(function ($slide) {
                if (is_string($slide->title)) {
                    $slide->title = json_decode($slide->title, true);
                }

                if (is_string($slide->description)) {
                    $slide->description = json_decode($slide->description, true);
                }

                return $slide;
            });

            return $this->successResponse($slides, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function activeSlides()
    {
        try {
            $slides = Slide::where('status', 'active')->get();

            // Decode JSON fields if needed
            $slides->transform(function ($slide) {
                if (is_string($slide->title)) {
                    $slide->title = json_decode($slide->title, true);
                }

                if (is_string($slide->description)) {
                    $slide->description = json_decode($slide->description, true);
                }

                return $slide;
            });

            return $this->successResponse($slides, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSlideRequest $request)
    {
        try {
            $data = $request->validated();
            $slide = Slide::create(collect($data)->except('image')->toArray());
            // معالجة الصورة إذا تم رفعها
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $slide, 'images/slides', 'image');
            }
            return $this->successResponse($slide, 201);
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
            $slide = Slide::findOrFail($id);

            // Decode JSON fields if needed
            if (is_string($slide->title)) {
                $slide->title = json_decode($slide->title, true);
            }

            if (is_string($slide->description)) {
                $slide->description = json_decode($slide->description, true);
            }

            return $this->successResponse($slide, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSlideRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $slide = Slide::findOrFail($id);

            $slide->update(collect($data)->except('image')->toArray());

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $slide, 'images/slides', 'image');
            }

            // Decode JSON fields if needed
            if (is_string($slide->title)) {
                $slide->title = json_decode($slide->title, true);
            }

            if (is_string($slide->description)) {
                $slide->description = json_decode($slide->description, true);
            }

            return $this->successResponse($slide, 200);
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
            $slide = Slide::findOrFail($id);
            if (filled($slide->image)) {
                $this->imageservice->deleteOldImage($slide, 'images/slides');
            }

            $slide->delete();

            return $this->successResponse([], 200, 'slide deleted successfully .');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
