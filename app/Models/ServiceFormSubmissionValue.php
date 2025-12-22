<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFormSubmissionValue extends Model
{
    protected $fillable = [
        'submission_id',
        'field_id',
        'value',
    ];

    // ========== RELATIONSHIPS ==========

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ServiceFormSubmission::class, 'submission_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServiceFormField::class, 'field_id');
    }

    // ========== HELPER METHODS ==========

    /**
     * Get display-friendly value based on field type
     */
    public function getDisplayValue()
    {
        if (!$this->field) {
            return $this->value;
        }

        switch ($this->field->field_type) {
            case 'checkbox':
            case 'multi_select':
                $decoded = json_decode($this->value, true);
                return is_array($decoded) ? $decoded : $this->value;

            case 'file_upload':
            case 'image_upload':
                return $this->value ? asset('storage/' . $this->value) : null;

            case 'dropdown':
            case 'radio':
                // Could map value to label if needed
                return $this->value;

            default:
                return $this->value;
        }
    }

    /**
     * Set value with proper encoding for arrays
     */
    public function setValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}
