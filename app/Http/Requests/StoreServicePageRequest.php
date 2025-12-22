<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => 'required|string|max:255|unique:service_pages,slug',
            'service_id' => 'nullable|integer',
            'is_active' => 'boolean',
            'price' => 'required|numeric',
            'price_before_discount' => 'required|numeric',
            'type' => 'required|in:one_time,subscription',
            'status' => 'required|in:active,inactive',
            'order' => 'required|integer',
            'category_id' => 'required|integer',
            'whatsapp_number' => 'nullable|string|max:255',

            // Hero Section
            'hero_section' => 'nullable|array',


            // Problem Section
            'problem_section' => 'nullable|array',


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
            'gallery.images' => 'array|max:20',
            'gallery.images.*.path' => 'required|max:5096',
            'gallery.images.*.alt_ar' => 'required|string|max:255',
            'gallery.images.*.alt_en' => 'required|string|max:255',
            'gallery.images.*.order' => 'integer',

            // Stats
            'stats' => 'array|max:10',
            'stats.*.number' => 'required|string|max:50',
            'stats.*.label_ar' => 'required|string|max:100',
            'stats.*.label_en' => 'required|string|max:100',
            'stats.*.order' => 'integer',

            // Testimonials
            'testimonials' => 'array|max:20',


            // CTA Section
            'cta_section' => 'nullable|array',

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
