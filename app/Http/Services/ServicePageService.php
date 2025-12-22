<?php

namespace App\Http\Services;

use App\Models\ServicePage;
use App\Models\ServicePageHeroSection;
use App\Models\ServicePageProblemSection;
use App\Models\ServicePageProblemItem;
use App\Models\ServicePageSolutionSection;
use App\Models\ServicePageSolutionFeature;
use App\Models\ServicePageGalleryImage;
use App\Models\ServicePageStat;
use App\Models\ServicePageTestimonial;
use App\Models\ServicePageCtaSection;
use App\Models\ServiceForm;
use App\Models\ServiceFormField;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

class ServicePageService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Create a new service page with all sections
     */
    public function create(array $data, Request $request): ServicePage
    {
        return DB::transaction(function () use ($data, $request) {
            // Create main service page
            $servicePage = ServicePage::create([
                'slug' => $data['slug'],
                'service_id' => $data['service_id'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'price' => $data['price'] ?? null,
                'price_before_discount' => $data['price_before_discount'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'type' => $data['type'] ?? 'one_time',
                'order' => $data['order'] ?? 0,
                'status' => $data['status'] ?? 'active',
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
            ]);

            // Create sections
            $this->createHeroSection($servicePage, $data['hero_section'] ?? null);

            // Handle Hero Image Upload
            if ($request->file('hero_image')) {
                $storagePath = 'images/service-pages';
                $imageFile = $request->file('hero_image');

                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $imageFile->getClientOriginalExtension();
                $filename = $originalName . '_' . uniqid() . '.' . $extension;

                // Upload file
                $imageFile->move(public_path($storagePath), $filename);

                $fullImagePath = url('/') . '/' . $storagePath . '/' . $filename;

                // Update DB
                $servicePage->heroSection()->update([
                    'hero_image' => $fullImagePath,
                ]);
            }

            $this->createProblemSection($servicePage, $data['problem_section'] ?? null);
            $this->createSolutionSection($servicePage, $data['solution_section'] ?? null);

            // Handle Gallery Creation (reuse update logic for file handling)
            if ($request->has('gallery_images')) {
                $this->updateGallerySection(
                    $servicePage,
                    [], // No deleted images for new creation
                    $request->gallery_images
                );
            }

            $this->createStats($servicePage, $data['stats'] ?? []);
            $this->createTestimonials($servicePage, $data['testimonials'] ?? []);
            $this->createCtaSection($servicePage, $data['cta_section'] ?? null);

            // Handle Service Form
            if (isset($data['form'])) {
                $this->createServiceForm($servicePage, $data['form']);
            }

            return $servicePage->load($this->getEagerLoadRelations());
        });
    }

    /**
     * Update an existing service page
     */
    public function update(ServicePage $servicePage, array $data, Request $request): ServicePage
    {
        return DB::transaction(function () use ($servicePage, $data, $request) {
            // Update main service page
            $servicePage->update([
                'slug' => $data['slug'] ?? $servicePage->slug,
                'service_id' => $data['service_id'] ?? $servicePage->service_id,
                'is_active' => $data['is_active'] ?? $servicePage->is_active,
                'price' => $data['price'] ?? $servicePage->price,
                'price_before_discount' => $data['price_before_discount'] ?? $servicePage->price_before_discount,
                'category_id' => $data['category_id'] ?? $servicePage->category_id,
                'type' => $data['type'] ?? $servicePage->type,
                'order' => $data['order'] ?? $servicePage->order,
                'status' => $data['status'] ?? $servicePage->status,
                'whatsapp_number' => $data['whatsapp_number'] ?? $servicePage->whatsapp_number,
            ]);

            // Update sections
            if (isset($data['hero_section'])) {

                $old_image = $servicePage->heroSection->hero_image;
                $this->updateHeroSection($servicePage, $data['hero_section']);

                // Check image upload
                if ($request->file('hero_image')) {
                    $storagePath = 'images/service-pages';
                    $imageFile = $request->file('hero_image');

                    $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $imageFile->getClientOriginalExtension();
                    $filename = $originalName . '_' . uniqid() . '.' . $extension;

                    // Upload file
                    $imageFile->move(public_path($storagePath), $filename);

                    $fullImagePath = url('/') . '/' . $storagePath . '/' . $filename;

                    // Delete old image
                    if ($old_image) {
                        $old_image_name = basename(parse_url($old_image, PHP_URL_PATH));
                        $file_path = public_path($storagePath . '/' . $old_image_name);

                        if (File::exists($file_path)) {
                            File::delete($file_path);
                        }
                    }

                    // Update DB
                    $servicePage->heroSection->update([
                        'hero_image' => $fullImagePath,
                    ]);
                }
            }
            if (isset($data['problem_section'])) {

                $this->updateProblemSection($servicePage, $data['problem_section']);
            }
            if (isset($data['solution_section'])) {

                $this->updateSolutionSection($servicePage, $data['solution_section']);
            }

            // Handle Gallery Update
            if ($request->has('deleted_images') || $request->has('gallery_images')) {
                $this->updateGallerySection(
                    $servicePage,
                    $request->deleted_images,
                    $request->gallery_images,
                );
            }

            if (isset($data['stats'])) {
                $this->updateStats($servicePage, $data['stats']);
            }
            if (isset($data['testimonials'])) {
                $this->updateTestimonials($servicePage, $data['testimonials']);
            }
            if (isset($data['cta_section'])) {

                $this->updateCtaSection($servicePage, $data['cta_section']);
            }

            // Handle Service Form Update
            if (isset($data['form'])) {
                $this->updateServiceForm($servicePage, $data['form']);
            }

            return $servicePage->fresh($this->getEagerLoadRelations());
        });
    }

    /**
     * Delete a service page and all related images
     */
    public function delete(ServicePage $servicePage): void
    {
        DB::transaction(function () use ($servicePage) {
            // Delete hero image
            if ($servicePage->heroSection?->hero_image) {
                $this->imageService->deleteOldImage($servicePage->heroSection, 'images/service-pages');
            }

            // Delete solution preview image
            if ($servicePage->solutionSection?->preview_image) {
                $this->imageService->deleteOldImage($servicePage->solutionSection, 'images/service-pages');
            }

            // Delete gallery images
            foreach ($servicePage->galleryImages as $image) {
                $this->imageService->deleteOldImage($image, 'images/service-pages/gallery');
            }

            // Delete testimonial avatars
            foreach ($servicePage->testimonials as $testimonial) {
                if ($testimonial->avatar) {
                    $this->imageService->deleteOldImage($testimonial, 'images/service-pages/avatars');
                }
            }

            // Delete service form
            if ($servicePage->form) {
                $servicePage->form->delete();
            }

            $servicePage->delete();
        });
    }

    /**
     * Get eager load relations array
     */
    public function getEagerLoadRelations(): array
    {
        return [
            'heroSection',
            'problemSection.items' => fn($q) => $q->orderBy('order'),
            'solutionSection.features' => fn($q) => $q->orderBy('order'),
            'galleryImages' => fn($q) => $q->orderBy('order'),
            'stats' => fn($q) => $q->orderBy('order'),
            'testimonials' => fn($q) => $q->orderBy('order'),
            'ctaSection',
            'category',
            'contactMessages',
            'form.fields' => fn($q) => $q->orderBy('order'),
        ];
    }

    // =========================================================================
    // HERO SECTION
    // =========================================================================

    protected function createHeroSection(ServicePage $servicePage, ?array $data): void
    {
        if (!$data) return;

        if (isset($data['hero_image'])) {
            unset($data['hero_image']);
        }

        ServicePageHeroSection::create(array_merge($data, [
            'service_page_id' => $servicePage->id,
        ]));
    }

    protected function updateHeroSection(ServicePage $servicePage, array $data): void
    {
        if (isset($data['hero_image'])) {
            unset($data['hero_image']);
        }

        $servicePage->heroSection?->update($data)
            ?? $this->createHeroSection($servicePage, $data);
    }

    // =========================================================================
    // PROBLEM SECTION
    // =========================================================================

    protected function createProblemSection(ServicePage $servicePage, ?array $data): void
    {
        if (!$data) return;

        $items = $data['items'] ?? [];
        unset($data['items']);

        $problemSection = ServicePageProblemSection::create(array_merge($data, [
            'service_page_id' => $servicePage->id,
        ]));

        foreach ($items as $index => $item) {
            ServicePageProblemItem::create(array_merge($item, [
                'problem_section_id' => $problemSection->id,
                'order' => $item['order'] ?? $index,
            ]));
        }
    }

    protected function updateProblemSection(ServicePage $servicePage, array $data): void
    {
        $items = $data['items'] ?? null;
        unset($data['items']);

        if ($servicePage->problemSection) {
            $servicePage->problemSection->update($data);

            if ($items !== null) {
                // Delete existing items and recreate
                $servicePage->problemSection->items()->delete();
                foreach ($items as $index => $item) {
                    ServicePageProblemItem::create(array_merge($item, [
                        'problem_section_id' => $servicePage->problemSection->id,
                        'order' => $item['order'] ?? $index,
                    ]));
                }
            }
        } else {
            $data['items'] = $items ?? [];
            $this->createProblemSection($servicePage, $data);
        }
    }

    // =========================================================================
    // SOLUTION SECTION
    // =========================================================================

    protected function createSolutionSection(ServicePage $servicePage, ?array $data): void
    {
        if (!$data) return;

        $features = $data['features'] ?? [];
        unset($data['features']);

        $solutionSection = ServicePageSolutionSection::create(array_merge($data, [
            'service_page_id' => $servicePage->id,
        ]));

        $storagePath = 'images/service-pages/solutions';

        foreach ($features as $index => $feature) {
            $imagePath = $this->storeAvatarIfUploaded($feature['preview_image'] ?? null, $storagePath);

            ServicePageSolutionFeature::create(array_merge($feature, [
                'feature_key' => $feature['feature_key'] ?? "random" . $index,
                'solution_section_id' => $solutionSection->id,
                'preview_image' => $imagePath ?? ($feature['preview_image'] ?? null),
                'order' => $feature['order'] ?? $index,
            ]));
        }
    }

    protected function updateSolutionSection(ServicePage $servicePage, array $data): void
    {
        $features = $data['features'] ?? null;
        unset($data['features']);

        if ($servicePage->solutionSection) {
            $servicePage->solutionSection->update($data);

            if ($features !== null) {
                $storagePath = 'images/service-pages/solutions';

                DB::transaction(function () use ($servicePage, $features, $storagePath) {
                    $existing = $servicePage->solutionSection->features->keyBy('id');
                    $incomingIds = collect($features)->pluck('id')->filter()->values()->all();

                    // 1) Delete removed features
                    foreach ($existing as $id => $model) {
                        if (!in_array($id, $incomingIds)) {
                            if ($model->preview_image) {
                                $this->imageService->deleteOldImage($model, $storagePath);
                            }
                            $model->delete();
                        }
                    }

                    // 2) Update existing or create new
                    foreach ($features as $index => $feature) {
                        if (!empty($feature['id']) && $existing->has($feature['id'])) {
                            // Update existing
                            $model = $existing->get($feature['id']);
                            $imageInput = $feature['preview_image'] ?? null;

                            $imagePath = $this->storeAvatarIfUploaded($imageInput, $storagePath, $model);

                            // If incoming image is explicitly null (user removed image) -> delete old image
                            if ($imageInput === null && isset($feature['preview_image']) && $model->preview_image) {
                                $this->imageService->deleteOldImage($model, $storagePath);
                            }

                            $model->update(array_merge($feature, [
                                'preview_image' => $imagePath ?? ($feature['preview_image'] ?? $model->preview_image),
                                'order' => $feature['order'] ?? $index,
                                'feature_key' => $feature['feature_key'] ?? $model->feature_key,
                            ]));
                        } else {
                            // Create new
                            $imagePath = $this->storeAvatarIfUploaded($feature['preview_image'] ?? null, $storagePath);

                            ServicePageSolutionFeature::create(array_merge($feature, [
                                'solution_section_id' => $servicePage->solutionSection->id,
                                'preview_image' => $imagePath ?? ($feature['preview_image'] ?? null),
                                'order' => $feature['order'] ?? $index,
                                'feature_key' => $feature['feature_key'] ?? "random" . $index,
                            ]));
                        }
                    }
                });
            }
        } else {
            $data['features'] = $features ?? [];
            $this->createSolutionSection($servicePage, $data);
        }
    }

    // =========================================================================
    // GALLERY SECTION
    // =========================================================================



    protected function updateGallerySection(ServicePage $servicePage, array $deletedImages, $newImages): void
    {
        if (is_string($deletedImages)) {
            $deletedImages = json_decode($deletedImages, true);
        }

        foreach ($deletedImages as $image) {
            Log::info('deleted_image', $image);
            $old_image = $image['path'];
            $storagePath = 'images/service-pages/gallery';
            // Delete old image
            if ($old_image) {
                $model = ServicePageGalleryImage::find($image['id']);
                $old_image_name = basename(parse_url($old_image, PHP_URL_PATH));
                $file_path = public_path($storagePath . '/' . $old_image_name);

                if (File::exists($file_path)) {
                    File::delete($file_path);
                }

                $model->delete();
            }
        }

        foreach ($newImages as $image) {
            Log::info('new_image', $image);
            if (!isset($image['file'])) {
                continue; // صورة قديمة → تجاهلها هنا
            }

            $maxOrder = ServicePageGalleryImage::max('order') ?? 0;
            $order = $maxOrder + 1;

            $file = $image['file'];

            if (!$file instanceof \Illuminate\Http\UploadedFile) {
                continue; // أمان إضافي
            }

            $storagePath = 'images/service-pages/gallery';

            $originalName = pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            );
            $extension = $file->getClientOriginalExtension();

            $filename = $originalName . '_' . uniqid() . '.' . $extension;

            $file->move(public_path($storagePath), $filename);

            $fullImagePath = url('/') . '/' . $storagePath . '/' . $filename;

            ServicePageGalleryImage::create([
                'service_page_id' => $servicePage->id,
                'order' => $order,
                'path' => $fullImagePath,
                'alt_en' => $image['alt_en'] ?? "",
                'alt_ar' => $image['alt_ar'] ?? "",
            ]);
        }
    }


    // =========================================================================
    // STATS
    // =========================================================================

    protected function createStats(ServicePage $servicePage, array $stats): void
    {
        foreach ($stats as $index => $stat) {
            ServicePageStat::create(array_merge($stat, [
                'service_page_id' => $servicePage->id,
                'order' => $stat['order'] ?? $index,
            ]));
        }
    }

    protected function updateStats(ServicePage $servicePage, array $stats): void
    {
        $servicePage->stats()->delete();
        $this->createStats($servicePage, $stats);
    }

    // =========================================================================
    // TESTIMONIALS
    // =========================================================================

    protected function storeAvatarIfUploaded($avatar, string $storagePath, $oldModel = null): ?string
    {
        // If avatar is an uploaded file, store it and optionally delete the old image.
        if ($avatar instanceof UploadedFile) {
            // delete old image if exists on the old model
            if ($oldModel && $oldModel->avatar) {
                $this->imageService->deleteOldImage($oldModel, $storagePath);
            }

            $originalName = pathinfo(
                $avatar->getClientOriginalName(),
                PATHINFO_FILENAME
            );
            $extension = $avatar->getClientOriginalExtension();
            $filename = $originalName . '_' . uniqid() . '.' . $extension;

            $avatar->move(public_path($storagePath), $filename);

            return url('/') . '/' . $storagePath . '/' . $filename;
        }

        // If it's a string (URL) or null, return as-is (caller decides whether to set)
        return $avatar ?? null;
    }

    protected function createTestimonials(ServicePage $servicePage, array $testimonials): void
    {
        $storagePath = 'images/service-pages/avatars';

        foreach ($testimonials as $index => $testimonial) {
            // handle avatar if file uploaded or keep string/null
            $avatarPath = $this->storeAvatarIfUploaded($testimonial['avatar'] ?? null, $storagePath);

            ServicePageTestimonial::create(array_merge($testimonial, [
                'service_page_id' => $servicePage->id,
                'avatar' => $avatarPath ?? ($testimonial['avatar'] ?? null),
                'order' => $testimonial['order'] ?? $index,
            ]));
        }
    }

    protected function updateTestimonials(ServicePage $servicePage, array $testimonials): void
    {
        $storagePath = 'images/service-pages/avatars';

        DB::transaction(function () use ($servicePage, $testimonials, $storagePath) {

            // Existing testimonials keyed by id (if id exists)
            $existing = $servicePage->testimonials->keyBy('id');

            // collect incoming ids (only numeric/non-empty ids)
            $incomingIds = collect($testimonials)->pluck('id')->filter()->values()->all();

            // 1) Delete testimonials that were removed in the incoming payload
            foreach ($existing as $id => $model) {
                if (!in_array($id, $incomingIds)) {
                    // delete stored avatar if any (safe-guard: only delete local images via your imageService)
                    if ($model->avatar) {
                        $this->imageService->deleteOldImage($model, $storagePath);
                    }
                    $model->delete();
                }
            }

            // 2) Update existing or create new
            foreach ($testimonials as $index => $testimonial) {
                // Update path for avatar if a file is uploaded. The helper deletes old image when replacing with file.
                if (!empty($testimonial['id']) && $existing->has($testimonial['id'])) {
                    // update existing
                    $model = $existing->get($testimonial['id']);

                    $avatarInput = $testimonial['avatar'] ?? null;

                    // If avatarInput is UploadedFile, store it and delete old via helper.
                    $avatarPath = $this->storeAvatarIfUploaded($avatarInput, $storagePath, $model);

                    // If incoming avatar is explicitly null (user removed avatar) -> delete old image
                    if ($avatarInput === null && isset($testimonial['avatar']) && $model->avatar) {
                        $this->imageService->deleteOldImage($model, $storagePath);
                    }

                    $model->update(array_merge($testimonial, [
                        'avatar' => $avatarPath ?? ($testimonial['avatar'] ?? $model->avatar),
                        'order' => $testimonial['order'] ?? $index,
                    ]));
                } else {
                    // create new testimonial
                    $avatarPath = $this->storeAvatarIfUploaded($testimonial['avatar'] ?? null, $storagePath);

                    ServicePageTestimonial::create(array_merge($testimonial, [
                        'service_page_id' => $servicePage->id,
                        'avatar' => $avatarPath ?? ($testimonial['avatar'] ?? null),
                        'order' => $testimonial['order'] ?? $index,
                    ]));
                }
            }
        });
    }

    // =========================================================================
    // CTA SECTION
    // =========================================================================

    protected function createCtaSection(ServicePage $servicePage, ?array $data): void
    {
        if (!$data) return;

        ServicePageCtaSection::create(array_merge($data, [
            'service_page_id' => $servicePage->id,
        ]));
    }

    protected function updateCtaSection(ServicePage $servicePage, array $data): void
    {
        $servicePage->ctaSection?->update($data)
            ?? $this->createCtaSection($servicePage, $data);
    }

    // =========================================================================
    // SERVICE FORM
    // =========================================================================

    protected function createServiceForm(ServicePage $servicePage, array $formData): void
    {
        // Extract title map
        $title = $formData['title'] ?? []; // {"ar": "...", "en": "..."}

        // Handle case where title might be JSON string
        if (is_string($title)) {
            $title = json_decode($title, true) ?? [];
        }

        $form = ServiceForm::create([
            'service_page_id' => $servicePage->id,
            'name_ar' => $title['ar'] ?? ($title['en'] ?? 'Service Form'),
            'name_en' => $title['en'] ?? ($title['ar'] ?? 'Service Form'),
            'is_active' => true,
            'version' => 1,
        ]);

        $fields = $formData['fields'] ?? [];
        foreach ($fields as $index => $fieldData) {
            $this->createServiceFormField($form, $fieldData, $index);
        }
    }

    protected function updateServiceForm(ServicePage $servicePage, array $formData): void
    {
        $form = $servicePage->form; // Access relationship directly

        $title = $formData['title'] ?? [];
        if (is_string($title)) {
            $title = json_decode($title, true) ?? [];
        }

        if (!$form) {
            // Create if not exists
            $this->createServiceForm($servicePage, $formData);
            return;
        }

        $form->update([
            'name_ar' => $title['ar'] ?? $form->name_ar,
            'name_en' => $title['en'] ?? $form->name_en,
            'version' => $form->version + 1,
        ]);

        // Fields Logic
        // Get existing fields keyed by field_key
        $existingFields = $form->fields()->get()->keyBy('field_key');

        // Payload fields
        $incomingFields = $formData['fields'] ?? [];
        $incomingKeys = [];

        foreach ($incomingFields as $index => $fieldData) {
            // Frontend sends "name" for key.
            $key = $fieldData['name'] ?? 'field_' . $index;
            $incomingKeys[] = $key;

            if ($existingFields->has($key)) {
                // Update
                $fieldModel = $existingFields->get($key);
                $this->updateServiceFormField($fieldModel, $fieldData, $index);
            } else {
                // Create
                $this->createServiceFormField($form, $fieldData, $index);
            }
        }

        // Delete removed fields
        foreach ($existingFields as $key => $field) {
            if (!in_array($key, $incomingKeys)) {
                $field->delete();
            }
        }
    }

    protected function createServiceFormField(ServiceForm $form, array $fieldData, int $index): void
    {
        ServiceFormField::create($this->mapFieldData($form->id, $fieldData, $index));
    }

    protected function updateServiceFormField(ServiceFormField $field, array $fieldData, int $index): void
    {
        $field->update($this->mapFieldData($field->service_form_id, $fieldData, $index));
    }

    protected function mapFieldData(int $formId, array $data, int $index): array
    {
        $typeMap = [
            'text' => 'short_text',
            'textarea' => 'long_text',
            'email' => 'email',
            'image' => 'image_upload',
            'file' => 'file_upload',
            'number' => 'number',
            'dropdown' => 'dropdown',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
        ];

        $frontendType = $data['type'] ?? 'text';
        $backendType = $typeMap[$frontendType] ?? $frontendType;

        $labels = $data['label'] ?? [];
        if (is_string($labels)) $labels = json_decode($labels, true) ?? [];

        $placeholders = $data['placeholder'] ?? [];
        if (is_string($placeholders)) $placeholders = json_decode($placeholders, true) ?? [];

        $options = $data['options'] ?? [];
        if (is_string($options)) $options = json_decode($options, true) ?? [];

        // Build validation rules if needed
        $validationRules = [];
        if (($data['type'] ?? '') === 'image') {
            $validationRules['mime_types'] = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            $validationRules['file_size_kb'] = 5120; // 5MB
        }
        if (isset($data['accept']) && str_starts_with($data['accept'], 'image/')) {
            $validationRules['mime_types'] = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        }

        return [
            'service_form_id' => $formId,
            'field_key' => $data['name'] ?? 'field_' . uniqid(),
            'field_type' => $backendType,
            'label_ar' => $labels['ar'] ?? '',
            'label_en' => $labels['en'] ?? '',
            'placeholder_ar' => $placeholders['ar'] ?? null,
            'placeholder_en' => $placeholders['en'] ?? null,
            'is_required' => filter_var($data['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'order' => $data['order'] ?? $index,
            'options' => [
                'choices' => $options,
                'width' => $data['width'] ?? 'full',
                'rows' => $data['rows'] ?? null,
                'accept' => $data['accept'] ?? null,
            ],
            'validation_rules' => !empty($validationRules) ? $validationRules : null,
        ];
    }
}
