<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvisionalData extends Model
{
    protected $fillable = [
        'uniqueId',
        'payment_id',
        'metadata',
        'ref_code',
        'expire_at',
        'service_order_id',
    ];

    /**
     * Get the service_order_id from metadata
     */
    public function getServiceOrderIdAttribute()
    {
        if ($this->attributes['service_order_id'] ?? null) {
            return $this->attributes['service_order_id'];
        }

        // Fallback to metadata for backwards compatibility
        $metadata = json_decode($this->attributes['metadata'] ?? '{}', true);
        return $metadata['service_order_id'] ?? null;
    }
}
