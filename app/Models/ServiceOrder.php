<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'invoice_id',
        'service_page_id',
        'metadata',
        'status',
        'payment_status',
        'subscription_status',
        'subscription_start_time',
        'subscription_end_time',
    ];


    protected $casts = [
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(ServicePage::class, 'service_page_id');
    }

    public function tracking()
    {
        return $this->hasMany(ServiceTracking::class, 'service_order_id');
    }

    /**
     * Files attached during payment (temporary, before payment confirmed)
     */
    public function pendingFiles()
    {
        return $this->hasMany(PendingServiceOrderFile::class, 'service_order_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        });

        $query->when($filters['payment_status'] ?? null, function ($q, $paymentStatus) {
            $q->where('payment_status', $paymentStatus);
        });

        $query->when($filters['subscription_status'] ?? null, function ($q, $subscriptionStatus) {
            $q->where('subscription_status', $subscriptionStatus);
        });

        $query->when($filters['service_id'] ?? null, function ($q, $serviceId) {
            $q->where('service_page_id', $serviceId);
        });

        $query->when($filters['user_id'] ?? null, function ($q, $userId) {
            $q->where('user_id', $userId);
        });

        $query->when($filters['user_type'] ?? null, function ($q, $userType) {
            $q->where('user_type', $userType);
        });

        $query->when($filters['date_from'] ?? null, function ($q, $dateFrom) {
            $q->whereDate('created_at', '>=', $dateFrom);
        });

        $query->when($filters['date_to'] ?? null, function ($q, $dateTo) {
            $q->whereDate('created_at', '<=', $dateTo);
        });

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
    }
}
