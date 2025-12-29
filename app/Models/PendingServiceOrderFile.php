<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PendingServiceOrderFile extends Model
{
    protected $fillable = [
        'uuid',
        'service_order_id',
        'disk',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'expires_at',
        'attached_at',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Relationship to ServiceOrder.
     */
    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * Scope: Not expired files.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope: Not attached files.
     */
    public function scopeNotAttached($query)
    {
        return $query->whereNull('attached_at');
    }

    /**
     * Scope: Expired files for cleanup.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Check if file is expired.
     */
    public function isExpired(): bool
    {
        return Carbon::parse($this->expires_at)->isPast();
    }

    /**
     * Check if file is attached.
     */
    public function isAttached(): bool
    {
        return !is_null($this->attached_at);
    }

    /**
     * Get full URL for the file.
     */
    public function getFullUrl(): string
    {
        return url($this->file_path);
    }

    /**
     * Accessor for attached_at.
     */
    public function getAttachedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->toDateTimeString() : null;
    }

    /**
     * Accessor for expires_at.
     */
    public function getExpiresAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->toDateTimeString() : null;
    }
}
