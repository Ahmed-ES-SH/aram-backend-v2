<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ServiceTracking extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'service_id',
        'user_id',
        'user_type',
        'metadata',
        'order_id',
        'status',
        'invoice_id',
        'start_time',
        'end_time',
        'current_phase'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'metadata' => 'array',
    ];



    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Phase constants
     */
    const PHASE_INITIATION = 'initiation';
    const PHASE_PLANNING = 'planning';
    const PHASE_EXECUTION = 'execution';
    const PHASE_MONITORING = 'monitoring';
    const PHASE_REVIEW = 'review';
    const PHASE_DELIVERY = 'delivery';
    const PHASE_SUPPORT = 'support';

    /**
     * User type constants
     */
    const USER_TYPE_USER = 'user';
    const USER_TYPE_ORGANIZATION = 'organization';

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Get all available phases
     */
    public static function getPhases(): array
    {
        return [
            self::PHASE_INITIATION,
            self::PHASE_PLANNING,
            self::PHASE_EXECUTION,
            self::PHASE_MONITORING,
            self::PHASE_REVIEW,
            self::PHASE_DELIVERY,
            self::PHASE_SUPPORT,
        ];
    }

    /**
     * Get all available user types
     */
    public static function getUserTypes(): array
    {
        return [
            self::USER_TYPE_USER,
            self::USER_TYPE_ORGANIZATION,
        ];
    }

    // ========== RELATIONSHIPS ==========

    /**
     * Get the service page that this tracking belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class, 'service_id');
    }

    /**
     * Get the user that owns this tracking (if user_type is 'user').
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the organization that owns this tracking (if user_type is 'organization').
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'user_id');
    }


    public function files()
    {
        return $this->hasMany(ServiceTrackingFile::class);
    }

    /**
     * Get the order associated with this tracking.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the invoice associated with this tracking.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // ========== SCOPES ==========

    /**
     * Scope a query to only include trackings with a specific status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending trackings.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include in-progress trackings.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope a query to only include completed trackings.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include cancelled trackings.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to only include trackings for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId, string $userType = 'user'): Builder
    {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    /**
     * Scope a query to only include active trackings (not completed or cancelled).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Scope a query by phase.
     */
    public function scopePhase(Builder $query, string $phase): Builder
    {
        return $query->where('current_phase', $phase);
    }

    // ========== HELPER METHODS ==========

    /**
     * Check if the tracking is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the tracking is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if the tracking is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the tracking is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if the tracking is active (not completed or cancelled).
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Check if owner is a user.
     */
    public function isUserOwned(): bool
    {
        return $this->user_type === self::USER_TYPE_USER;
    }

    /**
     * Check if owner is an organization.
     */
    public function isOrganizationOwned(): bool
    {
        return $this->user_type === self::USER_TYPE_ORGANIZATION;
    }

    /**
     * Get the owner (user or organization).
     */
    public function getOwner()
    {
        return $this->isUserOwned() ? $this->user : $this->organization;
    }

    /**
     * Start the service tracking.
     */
    public function start(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'start_time' => now(),
        ]);
    }

    /**
     * Complete the service tracking.
     */
    public function complete(): bool
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'end_time' => now(),
        ]);
    }

    /**
     * Cancel the service tracking.
     */
    public function cancel(): bool
    {
        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'end_time' => now(),
        ]);
    }

    /**
     * Update the current phase.
     */
    public function updatePhase(string $phase): bool
    {
        return $this->update(['current_phase' => $phase]);
    }

    /**
     * Add metadata to the tracking.
     */
    public function addMetadata(array $data): bool
    {
        $currentMetadata = $this->metadata ?? [];
        return $this->update([
            'metadata' => array_merge($currentMetadata, $data),
        ]);
    }

    /**
     * Get the duration of the service in minutes.
     */
    public function getDurationInMinutes(): ?int
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Get the next phase in sequence.
     */
    public function getNextPhase(): ?string
    {
        $phases = self::getPhases();
        $currentIndex = array_search($this->current_phase, $phases);

        if ($currentIndex === false || $currentIndex >= count($phases) - 1) {
            return null;
        }

        return $phases[$currentIndex + 1];
    }

    /**
     * Advance to the next phase.
     */
    public function advancePhase(): bool
    {
        $nextPhase = $this->getNextPhase();

        if (!$nextPhase) {
            return false;
        }

        return $this->updatePhase($nextPhase);
    }
}
