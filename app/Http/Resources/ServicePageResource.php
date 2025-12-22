<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicePageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', 'en');
        $locale = in_array($locale, ['ar', 'en']) ? $locale : 'en';

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'orders_count' => $this->orders_count,
            'category' => $this->category,
            'hero' => $this->formatHero($locale),
            'gallery' => $this->formatGallery($locale),
            'problemSection' => $this->formatProblemSection($locale),
            'solutionSection' => $this->formatSolutionSection($locale),
            'stats' => $this->formatStats($locale),
            'testimonials' => $this->formatTestimonials($locale),
            'cta' => $this->formatCta($locale),
            'form' => $this->formatForm($locale),
            'video' => $this->video,
            'price' => $this->price,
            'price_before_discount' => $this->price_before_discount,
            'whatsapp_number' => $this->whatsapp_number ?? null,
            'messages' => $this->contactMessages,
        ];
    }

    private function formatHero(string $locale): ?array
    {
        if (!$this->heroSection) return null;

        $h = $this->heroSection;
        return [
            'badge' => $h->{"badge_{$locale}"} ?? $h->badge_en,
            'title' => $h->{"title_{$locale}"} ?? $h->title_en,
            'subtitle' => $h->{"subtitle_{$locale}"} ?? $h->subtitle_en,
            'description' => $h->{"description_{$locale}"} ?? $h->description_en,
            'watchBtn' => $h->{"watch_btn_{$locale}"} ?? $h->watch_btn_en,
            'exploreBtn' => $h->{"explore_btn_{$locale}"} ?? $h->explore_btn_en,
            'heroImage' => $h->hero_image,
            'backgroundImage' => $h->background_image,
        ];
    }

    private function formatGallery(string $locale): array
    {
        return [
            'images' => $this->galleryImages->map(fn($img) => [
                'id' => (string) $img->id,
                'src' => $img->path,
                'alt' => $img->{"alt_{$locale}"} ?? $img->alt_en,
            ])->toArray(),
        ];
    }

    private function formatProblemSection(string $locale): ?array
    {
        if (!$this->problemSection) return null;

        $ps = $this->problemSection;
        return [
            'title' => $ps->{"title_{$locale}"} ?? $ps->title_en,
            'subtitle' => $ps->{"subtitle_{$locale}"} ?? $ps->subtitle_en,
            'items' => $ps->items->map(fn($item) => [
                'icon' => $item->icon,
                'title' => $item->{"title_{$locale}"} ?? $item->title_en,
                'description' => $item->{"description_{$locale}"} ?? $item->description_en,
            ])->toArray(),
        ];
    }

    private function formatSolutionSection(string $locale): ?array
    {
        if (!$this->solutionSection) return null;

        $ss = $this->solutionSection;
        return [
            'title' => $ss->{"title_{$locale}"} ?? $ss->title_en,
            'subtitle' => $ss->{"subtitle_{$locale}"} ?? $ss->subtitle_en,
            'cta' => $ss->{"cta_text_{$locale}"} ?? $ss->cta_text_en,
            'features' => $ss->features->map(fn($f) => [
                'id' => $f->feature_key,
                'icon' => $f->icon,
                'color' => $f->color,
                'order' => $f->order,
                'title' => $f->{"title_{$locale}"} ?? $f->title_en,
                'description' => $f->{"description_{$locale}"} ?? $f->description_en,
                'image' => $f->preview_image ?  $f->preview_image : null,
            ])->sortBy('order')->toArray(),
        ];
    }

    private function formatStats(string $locale): array
    {
        return $this->stats->map(fn($s) => [
            'number' => $s->number,
            'label' => $s->{"label_{$locale}"} ?? $s->label_en,
        ])->toArray();
    }

    private function formatTestimonials(string $locale): array
    {
        $cta = $this->ctaSection;

        return [
            'title' => $cta?->{"testimonial_title_{$locale}"} ?? $cta?->testimonial_title_en,
            'items' => $this->testimonials->map(fn($t) => [
                'name' => $t->{"name_{$locale}"} ?? $t->name_en,
                'text' => $t->{"text_{$locale}"} ?? $t->text_en,
                'rating' => $t->rating,
                'avatar' => $t->avatar ? asset('storage/' . $t->avatar) : null,
            ])->toArray(),
        ];
    }

    private function formatCta(string $locale): ?array
    {
        if (!$this->ctaSection) return null;

        $c = $this->ctaSection;
        return [
            'ctaTitle' => $c->{"cta_title_{$locale}"} ?? $c->cta_title_en,
            'ctaSubtitle' => $c->{"cta_subtitle_{$locale}"} ?? $c->cta_subtitle_en,
            'ctaButton1' => $c->{"cta_button1_{$locale}"} ?? $c->cta_button1_en,
            'ctaButton2' => $c->{"cta_button2_{$locale}"} ?? $c->cta_button2_en,
        ];
    }

    private function formatForm(string $locale): ?array
    {
        if (!$this->form) return null;

        $f = $this->form;

        return [
            'id' => $f->id,
            'title' => $f->{"name_{$locale}"} ?? $f->name_en,
            'fields' => $f->fields->map(fn($field) => [
                'id' => $field->field_key . '_' . $field->id, // Construct ID to match frontend generic style if needed, or just use key
                'name' => $field->field_key,
                'type' => $this->mapFieldType($field->field_type),
                'label' => $field->{"label_{$locale}"} ?? $field->label_en,
                'placeholder' => $field->{"placeholder_{$locale}"} ?? $field->placeholder_en,
                'required' => (bool)$field->is_required,
                'width' => $field->options['width'] ?? 'full',
                'rows' => $field->options['rows'] ?? null,
                'options' => $field->getLocalizedOptions($locale)['choices'] ?? [],
                'order' => $field->order,
                'accept' => $field->options['accept'] ?? null,
            ])->values()->toArray(),
        ];
    }

    private function mapFieldType(string $backendType): string
    {
        $map = [
            'short_text' => 'text',
            'long_text' => 'textarea',
            'image_upload' => 'image',
            'file_upload' => 'file',
        ];

        return $map[$backendType] ?? $backendType;
    }
}
