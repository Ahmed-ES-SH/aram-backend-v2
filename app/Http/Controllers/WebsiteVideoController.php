<?php

namespace App\Http\Controllers;

use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\WebsiteVideo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
    $validated = $request->validate([
        "video_id"     => "nullable|string",
        "video_image"  => "nullable|image|max:5120",
        "video"        => "nullable|file|max:40960",
        "video_url"    => "nullable|string|url",
        "aspect_ratio" => "nullable|string",
        "video_type"   => "nullable|in:file,youtube",
        "is_file"      => "nullable|boolean",
    ]);

    return DB::transaction(function () use ($request, $validated) {

        /**
         * 1️⃣ Prepare base data
         */
        $data = collect($validated)->only([
            'video_type',
            'is_file',
            'video_url',
            'aspect_ratio',
        ])->toArray();

        /**
         * 2️⃣ Find or create model
         */
        $video = WebsiteVideo::firstOrNew([
            'video_id' => $request->video_id,
        ]);

        $oldVideoUrl = $video->exists ? $video->video_url : null;

        /**
         * 3️⃣ Handle video upload
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
        }

        /**
         * 4️⃣ Save model
         */
        $video->fill($data);
        $video->save();

        /**
         * 5️⃣ Delete old video safely AFTER save
         */
        if ($oldVideoUrl && isset($data['video_url'])) {
            $oldName = basename(parse_url($oldVideoUrl, PHP_URL_PATH));
            $oldPath = public_path('videos/website_videos/' . $oldName);

            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        /**
         * 6️⃣ Update image AFTER model exists
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
    });
}

}
