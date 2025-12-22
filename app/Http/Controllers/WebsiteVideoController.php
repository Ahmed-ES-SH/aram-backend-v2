<?php

namespace App\Http\Controllers;

use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\WebsiteVideo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WebsiteVideoController extends Controller
{

    use ApiResponse;
    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }



    public function getVideo(Request $request)
    {
        try {

            $request->validate([
                "video_id" => "required|string"
            ]);

            $video = WebsiteVideo::where("video_id", $request->video_id)->first();

            if (!$video) {
                return $this->noContentResponse();
            }

            return $this->successResponse($video, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?? 500);
        }
    }


    public function getMainPageVideos()
    {
        try {

            $Mainvideo = WebsiteVideo::where("video_id", 'main_page')->first();
            $demovideo = WebsiteVideo::where("video_id", 'demo_video')->first();

            $data = [
                'main_video' => $Mainvideo,
                'demo_video' => $demovideo
            ];

            return $this->successResponse($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?? 500);
        }
    }


    public function updateVideo(Request $request)
    {
        try {
            $validated = $request->validate([
                "video_id"     => "nullable|string",
                "video_image"  => "nullable|image|max:5120",
                "video"        => "nullable|file|max:40960",
                "video_url"    => "nullable|string|url",
                "aspect_ratio" => "nullable|string",
                "video_type"   => "nullable|in:file,youtube",
                "is_file"      => "nullable|boolean",
            ]);

            /**
             * 1️⃣ Get existing video (if any)
             */
            $existingVideo = WebsiteVideo::where('video_id', $request->video_id)->first();

            /**
             * 2️⃣ Prepare base data
             */
            $data = collect($validated)->only([
                'video_type',
                'is_file',
                'video_url',
                'aspect_ratio',
            ])->toArray();

            /**
             * 3️⃣ Handle video file upload
             */
            if ($request->hasFile('video')) {
                $videoFile = $request->file('video');

                $filename =
                    pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME)
                    . '_' . uniqid()
                    . '.' . $videoFile->getClientOriginalExtension();

                $storagePath = 'videos/website_videos';
                $videoFile->move(public_path($storagePath), $filename);

                $data['video_url'] = url('/') . '/' . $storagePath . '/' . $filename;

                // 🧹 Delete old video only if updating
                if ($existingVideo && $existingVideo->video_url) {
                    $oldName = basename(parse_url($existingVideo->video_url, PHP_URL_PATH));
                    $oldPath = public_path($storagePath . '/' . $oldName);

                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
            }

            /**
             * 4️⃣ updateOrCreate
             */
            $video = WebsiteVideo::updateOrCreate(
                ['video_id' => $request->video_id],
                $data
            );

            /**
             * 5️⃣ Update image AFTER model exists
             */
            if ($request->hasFile('video_image')) {
                $this->imageservice->ImageUploaderwithvariable(
                    $request,
                    $video,
                    'images/website_videos',
                    'video_image'
                );
            }

            return $this->successResponse($video, 200);
        } catch (\Throwable $e) {
            return $this->errorResponse(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }
}
