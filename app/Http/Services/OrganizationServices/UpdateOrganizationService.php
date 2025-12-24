<?php

namespace App\Http\Services\OrganizationServices;

use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use Exception;
use Illuminate\Support\Facades\Hash;

class UpdateOrganizationService
{
    use ApiResponse;
    protected $imageservice;

    public function __construct(ImageService $imageservice)
    {
        $this->imageservice = $imageservice;
    }


    public function updateOrganization($request, $id)
    {

        $data = $request->validated();
        $org = Organization::findOrFail($id);
        // تحديث كلمة المرور بعد التحقق من وجودها
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Decode location if sent as JSON string
        if ($request->has('location') && is_string($request->location)) {
            $data['location'] = json_decode($request->location, true);
        }

        $org->update($data);



        // تحديث الصورة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            $this->imageservice->ImageUploaderwithvariable($request, $org, 'images/organizations', 'image');
        }

        // تحديث الصورة إذا تم رفع صورة جديدة
        if ($request->hasFile('logo')) {
            $this->imageservice->ImageUploaderwithvariable($request, $org, 'images/logo-organizations', 'logo');
        }

        // Update benefits if provided
        if ($request->has('benefits')) {
            // Delete old benefits
            $org->benefits()->delete();

            // Insert new benefits
            foreach ($request->benefits as $benefit) {
                $org->benefits()->create([
                    'title' => $benefit['title'],
                ]);
            }
        }

        // Update Main Categories if provided
        if ($request->has('categories')) {
            $org->categories()->sync($request->categories);
        }

        // Update subCategories if provided
        if ($request->has('sub_categories')) {
            $org->subCategories()->sync($request->sub_categories);
        }

        // Update keywords if provided
        if ($request->has('keywords')) {
            $keywordIds = collect($request->keywords)
                ->map(function ($item) {
                    // If the item is an array or object with an 'id', return it
                    if (is_array($item) && isset($item['id'])) {
                        return $item['id'];
                    }

                    if (is_object($item) && isset($item->id)) {
                        return $item->id;
                    }

                    // Otherwise, assume it's already an ID
                    return $item;
                })
                ->toArray();

            $org->keywords()->sync($keywordIds);
        }


        $org->load(['subCategories', 'keywords', 'categories',  'benefits']);

        return $org->fresh();
    }
}
