<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ServiceFormSubmission extends Model
{
    protected $fillable = [
        'service_form_id',
        'user_id',
        'user_type',
        'status',
        'service_tracking_id',
    ];

    /**
     * Submission statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * User types
     */
    const USER_TYPE_USER = 'user';
    const USER_TYPE_ORGANIZATION = 'organization';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_REVIEWED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }

    // ========== RELATIONSHIPS ==========

    public function form(): BelongsTo
    {
        return $this->belongsTo(ServiceForm::class, 'service_form_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ServiceFormSubmissionValue::class, 'submission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'user_id');
    }

    public function serviceTracking(): BelongsTo
    {
        return $this->belongsTo(ServiceTracking::class);
    }

    // ========== SCOPES ==========

    public function scopeForUser(Builder $query, int $userId, string $userType = 'user'): Builder
    {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReviewed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REVIEWED);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // ========== HELPER METHODS ==========

    /**
     * Get the owner (user or organization)
     */
    public function getOwner()
    {
        return $this->user_type === self::USER_TYPE_USER
            ? $this->user
            : $this->organization;
    }

    /**
     * Check if submission is owned by user
     */
    public function isUserOwned(): bool
    {
        return $this->user_type === self::USER_TYPE_USER;
    }

    /**
     * Get submission data as key-value array
     */
    public function getFormattedValues(): array
    {
        return $this->values->mapWithKeys(function ($value) {
            return [$value->field->field_key => $value->value];
        })->toArray();
    }

    /**
     * Get submission data with field details
     */
    public function getDetailedValues(string $locale = 'en'): array
    {
        return $this->values->map(function ($value) use ($locale) {
            $field = $value->field;
            $labelField = "label_{$locale}";

            return [
                'field_key' => $field->field_key,
                'field_type' => $field->field_type,
                'label' => $field->$labelField ?? $field->label_en,
                'value' => $value->getDisplayValue(),
            ];
        })->toArray();
    }

    /**
     * Update submission status
     */
    public function updateStatus(string $status): bool
    {
        if (!in_array($status, self::getStatuses())) {
            return false;
        }

        return $this->update(['status' => $status]);
    }
}
