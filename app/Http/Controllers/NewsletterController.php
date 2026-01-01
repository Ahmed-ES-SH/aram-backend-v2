<?php

namespace App\Http\Controllers;

use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{

    use ApiResponse;
    protected $imageservice;

    public function __construct(ImageService $imageservice)
    {
        $this->imageservice = $imageservice;
    }

    public function index()
    {
        $newsletters = Newsletter::latest()->get();
        return $this->successResponse($newsletters, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'nullable|string',
            'section_1_title' => 'nullable|string|max:255',
            'section_1_description' => 'nullable|string',
            'section_1_image' => 'nullable|file|max:5096',
            'section_2_title' => 'nullable|string|max:255',
            'section_2_description' => 'nullable|string',
            'section_2_image' => 'nullable|file|max:5096',
            'section_3_title' => 'nullable|string|max:255',
            'section_3_description' => 'nullable|string',
            'section_3_image' => 'nullable|file|max:5096',
        ]);


        $newsletter = Newsletter::create($validated);

        $imageFields = ['section_1_image', 'section_2_image', 'section_3_image'];
        foreach ($imageFields as $field) {
            if ($request->has($field)) {
                $this->imageservice->ImageUploaderwithvariable($request, $newsletter, 'images/newsletters', $field);
            }
        }

        return $this->successResponse($newsletter, 201);
    }

    public function show($id)
    {
        $newsletter = Newsletter::findOrFail($id);
        return $this->successResponse($newsletter, 200);
    }

    public function update(Request $request, $id)
    {
        $newsletter = Newsletter::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'section_1_title' => 'nullable|string|max:255',
            'section_1_description' => 'nullable|string',
            'section_1_image' => 'nullable|file|max:5096',
            'section_2_title' => 'nullable|string|max:255',
            'section_2_description' => 'nullable|string',
            'section_2_image' => 'nullable|file|max:5096',
            'section_3_title' => 'nullable|string|max:255',
            'section_3_description' => 'nullable|string',
            'section_3_image' => 'nullable|file|max:5096',
        ]);

        $newsletter->update($validated);

        $imageFields = ['section_1_image', 'section_2_image', 'section_3_image'];
        foreach ($imageFields as $field) {
            if ($request->has($field)) {
                $this->imageservice->ImageUploaderwithvariable($request, $newsletter, 'images/newsletters', $field);
            }
        }

        return $this->successResponse($newsletter, 200);
    }

    public function destroy($id)
    {
        $newsletter = Newsletter::findOrFail($id);
        $newsletter->delete();

        return $this->successResponse(null, 200);
    }

    public function send(Request $request, $id)
    {
        $newsletter = Newsletter::findOrFail($id);

        $request->validate([
            'emails' => 'required',
        ]);

        $emails = $request->input('emails');

        if (is_string($emails)) {
            $emails = json_decode($emails, true);
        }


        foreach ($emails as $email) {
            Mail::to($email)->send(new NewsletterMail($newsletter));
        }

        return response()->json(['message' => 'Newsletter sent successfully']);
    }
}
