<?php

namespace App\Models;

use App\Helpers\TextNormalizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class ServicePage extends Model
{
    protected $fillable = [
        'slug',
        'is_active',
        'price',
        'type',
        'price_before_discount',
        'status',
        'category_id',
        'orders_count',
        'whatsapp_number',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'price_before_discount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (ServicePage $page) {
            Cache::forget("service_page_{$page->slug}_ar");
            Cache::forget("service_page_{$page->slug}_en");
        });

        static::deleted(function (ServicePage $page) {
            Cache::forget("service_page_{$page->slug}_ar");
            Cache::forget("service_page_{$page->slug}_en");
        });
    }

    // ========== SERVICE PAGE SECTIONS ==========


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function heroSection(): HasOne
    {
        return $this->hasOne(ServicePageHeroSection::class);
    }

    public function problemSection(): HasOne
    {
        return $this->hasOne(ServicePageProblemSection::class);
    }

    public function solutionSection(): HasOne
    {
        return $this->hasOne(ServicePageSolutionSection::class);
    }



    public function galleryImages(): HasMany
    {
        return $this->hasMany(ServicePageGalleryImage::class)->orderBy('order');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(ServicePageStat::class)->orderBy('order');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(ServicePageTestimonial::class)->orderBy('order');
    }

    public function ctaSection(): HasOne
    {
        return $this->hasOne(ServicePageCtaSection::class);
    }

    // ========== SERVICE FORM ==========

    /**
     * Get the service form for this service page.
     */
    public function form(): HasOne
    {
        return $this->hasOne(ServiceForm::class , 'service_page_id');
    }

    /**
     * Get active form for this service
     */
    public function activeForm(): HasOne
    {
        return $this->hasOne(ServiceForm::class)->where('is_active', true);
    }

    // ========== SERVICE TRACKING ==========

    /**
     * Get all service trackings for this service page.
     */
    public function trackings(): HasMany
    {
        return $this->hasMany(ServiceTracking::class, 'service_id');
    }


    public function trackingFiles(): HasMany
    {
        return $this->hasMany(ServiceTrackingFile::class);
    }



    /**
     * Get all service contact messages for this service page.
     */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ServicePageContactMessage::class);
    }

    /**
     * Get active trackings for this service.
     */
    public function activeTrackings(): HasMany
    {
        return $this->hasMany(ServiceTracking::class, 'service_id')
            ->whereIn('status', [ServiceTracking::STATUS_PENDING, ServiceTracking::STATUS_IN_PROGRESS]);
    }

    /**
     * Get completed trackings for this service.
     */
    public function completedTrackings(): HasMany
    {
        return $this->hasMany(ServiceTracking::class, 'service_id')
            ->where('status', ServiceTracking::STATUS_COMPLETED);
    }

    /**
     * Get the count of active trackings.
     */
    public function getActiveTrackingsCountAttribute(): int
    {
        return $this->activeTrackings()->count();
    }

    /**
     * Get the count of completed trackings.
     */
    public function getCompletedTrackingsCountAttribute(): int
    {
        return $this->completedTrackings()->count();
    }

    /**
     * Get the total trackings count.
     */
    public function getTotalTrackingsCountAttribute(): int
    {
        return $this->trackings()->count();
    }


    public function ScopeSort($query)
    {
        return $query->orderBy('order');
    }


    public function scopeSearchNormalized($query, $term)
    {
        if (!$term) {
            return $query;
        }

        $normalizedQuery = TextNormalizer::normalizeArabic($term);

        $normalizedName = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(slug, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

        return $query->where(function ($q) use ($normalizedQuery, $normalizedName) {
            $q->whereRaw("$normalizedName LIKE ?", ["%$normalizedQuery%"]);
        });
    }
}
