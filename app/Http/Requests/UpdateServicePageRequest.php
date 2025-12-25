<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServicePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'slug' => "sometimes|required|string|max:255|unique:service_pages,slug,{$id}",
            'service_id' => 'nullable|integer',
            'is_active' => 'boolean',
            'price' => 'sometimes|numeric',
            'price_before_discount' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
            'type' => 'sometimes|in:one_time,subscription',
            'order' => 'sometimes|integer',
            'status' => 'sometimes|in:active,inactive',
            'whatsapp_number' => 'nullable|string|max:255',

            // Hero Section
            'hero_section' => 'nullable|array',
            'hero_section.badge_ar' => 'sometimes|required|string|max:255',
            'hero_section.badge_en' => 'sometimes|required|string|max:255',
            'hero_section.title_ar' => 'sometimes|required|string|max:255',
            'hero_section.title_en' => 'sometimes|required|string|max:255',
            'hero_section.subtitle_ar' => 'sometimes|required|string|max:255',
            'hero_section.subtitle_en' => 'sometimes|required|string|max:255',
            'hero_section.description_ar' => 'sometimes|required|string',
            'hero_section.description_en' => 'sometimes|required|string',
            'hero_image' => 'nullable|file|max:5096|mimes:jpeg,jpg,png',
            'hero_background_image' => 'nullable|file|max:5096|mimes:jpeg,jpg,png',

            // Problem Section
            'problem_section' => 'nullable|array',
            'problem_section.title_ar' => 'sometimes|required|string|max:255',
            'problem_section.title_en' => 'sometimes|required|string|max:255',
            'problem_section.subtitle_ar' => 'sometimes|required|string',
            'problem_section.subtitle_en' => 'sometimes|required|string',
            'problem_section.items' => 'array|max:10',
            'problem_section.items.*.icon' => 'required|string|max:50',
            'problem_section.items.*.title_ar' => 'required|string|max:255',
            'problem_section.items.*.title_en' => 'required|string|max:255',
            'problem_section.items.*.description_ar' => 'required|string',
            'problem_section.items.*.description_en' => 'required|string',
            'problem_section.items.*.order' => 'integer',

            // Solution Section
            'solution_section' => 'nullable|array',
            'solution_section.title_ar' => 'sometimes|required|string|max:255',
            'solution_section.title_en' => 'sometimes|required|string|max:255',
            'solution_section.subtitle_ar' => 'sometimes|required|string',
            'solution_section.subtitle_en' => 'sometimes|required|string',
            'solution_section.cta_text_ar' => 'sometimes|nullable|string|max:100',
            'solution_section.cta_text_en' => 'sometimes|nullable|string|max:100',
            'solution_section.preview_image' => 'nullable|string',
            'solution_section.features' => 'array|max:10',
            'solution_section.features.*.icon' => 'required|string|max:50',
            'solution_section.features.*.color' => 'required|string|max:50',
            'solution_section.features.*.title_ar' => 'required|string|max:255',
            'solution_section.features.*.title_en' => 'required|string|max:255',
            'solution_section.features.*.description_ar' => 'required|string',
            'solution_section.features.*.description_en' => 'required|string',
            'solution_section.features.*.order' => 'integer',
            'solution_section.features.*.preview_image' => 'nullable',

            // Gallery
            'gallery_images' => 'nullable|array',
            'gallery_images_files' => 'nullable|array',
            'deleted_images' => 'nullable|array',
            'gallery_images.*.path' => 'nullable|string',
            'gallery_images.*.alt_ar' => 'nullable|string|max:255',
            'gallery_images.*.alt_en' => 'nullable|string|max:255',
            'gallery_images.*.order' => 'integer',

            // Stats
            'stats' => 'array|max:10',
            'stats.*.number' => 'required|string|max:50',
            'stats.*.label_ar' => 'required|string|max:100',
            'stats.*.label_en' => 'required|string|max:100',
            'stats.*.order' => 'integer',

            // Testimonials
            'testimonials' => 'array|max:20',
            'testimonials.*.name_ar' => 'required|string|max:255',
            'testimonials.*.name_en' => 'required|string|max:255',
            'testimonials.*.text_ar' => 'required|string',
            'testimonials.*.text_en' => 'required|string',
            'testimonials.*.rating' => 'required|integer|min:1|max:5',
            'testimonials.*.avatar' => 'nullable',
            'testimonials.*.order' => 'integer',

            // CTA Section
            'cta_section' => 'nullable|array',
            'cta_section.testimonial_title_ar' => 'sometimes|nullable|string|max:255',
            'cta_section.testimonial_title_en' => 'sometimes|nullable|string|max:255',
            'cta_section.cta_section_title_ar' => 'sometimes|required|string|max:255',
            'cta_section.cta_section_title_en' => 'sometimes|required|string|max:255',
            'cta_section.cta_section_subtitle_ar' => 'sometimes|required|string',
            'cta_section.cta_section_subtitle_en' => 'sometimes|required|string',
            // 'cta_section.cta_section_button1_ar' => 'sometimes|required|string|max:100',
            // 'cta_section.cta_section_button1_en' => 'sometimes|required|string|max:100',
            // 'cta_section.cta_section_button2_ar' => 'sometimes|required|string|max:100',
            // 'cta_section.cta_section_button2_en' => 'sometimes|required|string|max:100',

            // Service Form
            'form' => 'nullable|array',
        ];
    }



    protected function prepareForValidation()
    {

        if ($this->has('hero_section') && is_string($this->hero_section)) {
            $this->merge([
                'hero_section' => json_decode($this->hero_section, true),
            ]);
        }

        if ($this->has('problem_section') && is_string($this->problem_section)) {
            $this->merge([
                'problem_section' => json_decode($this->problem_section, true),
            ]);
        }


        if ($this->has('solution_section') && is_string($this->solution_section)) {
            $this->merge([
                'solution_section' => json_decode($this->solution_section, true),
            ]);
        }


        if ($this->has('gallery_images') && is_string($this->gallery_images)) {
            $this->merge([
                'gallery_images' => json_decode($this->gallery_images, true),
            ]);
        }



        if ($this->has('gallery_images_files') && is_string($this->gallery_images_files)) {
            $this->merge([
                'gallery_images_files' => json_decode($this->gallery_images_files, true),
            ]);
        }

        if ($this->has('deleted_images') && is_string($this->deleted_images)) {
            $this->merge([
                'deleted_images' => json_decode($this->deleted_images, true),
            ]);
        }

        if ($this->has('stats') && is_string($this->stats)) {
            $this->merge([
                'stats' => json_decode($this->stats, true),
            ]);
        }

        if ($this->has('testimonials') && is_string($this->testimonials)) {
            $this->merge([
                'testimonials' => json_decode($this->testimonials, true),
            ]);
        }

        if ($this->has('cta_section') && is_string($this->cta_section)) {
            $this->merge([
                'cta_section' => json_decode($this->cta_section, true),
            ]);
        }

        if ($this->has('form') && is_string($this->form)) {
            $this->merge([
                'form' => json_decode($this->form, true),
            ]);
        }
    }
}
