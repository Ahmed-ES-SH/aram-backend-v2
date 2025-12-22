<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ServiceFormField extends Model
{
    protected $fillable = [
        'service_form_id',
        'field_key',
        'field_type',
        'label_ar',
        'label_en',
        'placeholder_ar',
        'placeholder_en',
        'options',
        'validation_rules',
        'order',
        'visibility_logic',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'visibility_logic' => 'array',
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Available field types
     */
    const FIELD_TYPES = [
        'short_text',
        'long_text',
        'email',
        'number',
        'dropdown',
        'checkbox',
        'radio',
        'file_upload',
        'image_upload',
        'url',
        'date',
        'multi_select',
        'phone',
        'time',
        'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    public function form(): BelongsTo
    {
        return $this->belongsTo(ServiceForm::class, 'service_form_id');
    }

    // ========== SCOPES ==========

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('is_required', true);
    }

    // ========== HELPER METHODS ==========

    /**
     * Get field schema for frontend rendering
     */
    public function getFieldSchema(string $locale = 'en'): array
    {
        $labelField = "label_{$locale}";
        $placeholderField = "placeholder_{$locale}";

        return [
            'key' => $this->field_key,
            'type' => $this->field_type,
            'label' => $this->$labelField ?? $this->label_en,
            'placeholder' => $this->$placeholderField ?? $this->placeholder_en,
            'required' => $this->is_required,
            'validation' => $this->validation_rules,
            'options' => $this->getLocalizedOptions($locale),
            'visibility' => $this->visibility_logic,
            'order' => $this->order,
        ];
    }

    /**
     * Get localized options for dropdown/radio/multi_select
     */
    public function getLocalizedOptions(string $locale = 'en'): ?array
    {
        if (!$this->options || !isset($this->options['choices'])) {
            return null;
        }

        $labelField = "label_{$locale}";

        return [
            'choices' => array_map(function ($choice) use ($labelField) {
                return [
                    'value' => $choice['value'],
                    'label' => $choice[$labelField] ?? $choice['label_en'] ?? $choice['value'],
                ];
            }, $this->options['choices']),
        ];
    }

    /**
     * Generate Laravel validation rules from validation_rules JSON
     */
    public function getValidationRules(): array
    {
        $rules = [];
        $config = $this->validation_rules ?? [];

        // Required
        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Type-specific rules
        switch ($this->field_type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'number':
                $rules[] = 'numeric';
                if (isset($config['min_value'])) {
                    $rules[] = 'min:' . $config['min_value'];
                }
                if (isset($config['max_value'])) {
                    $rules[] = 'max:' . $config['max_value'];
                }
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'date':
                $rules[] = 'date';
                if (isset($config['min_date'])) {
                    $rules[] = 'after_or_equal:' . $config['min_date'];
                }
                if (isset($config['max_date'])) {
                    $rules[] = 'before_or_equal:' . $config['max_date'];
                }
                break;
            case 'time':
                $rules[] = 'date_format:H:i';
                break;
            case 'datetime':
                $rules[] = 'date';
                break;
            case 'phone':
                $rules[] = 'string';
                if (isset($config['pattern'])) {
                    $rules[] = 'regex:/' . $config['pattern'] . '/';
                }
                break;
            case 'short_text':
            case 'long_text':
                $rules[] = 'string';
                if (isset($config['min_length'])) {
                    $rules[] = 'min:' . $config['min_length'];
                }
                if (isset($config['max_length'])) {
                    $rules[] = 'max:' . $config['max_length'];
                }
                if (isset($config['pattern'])) {
                    $rules[] = 'regex:/' . $config['pattern'] . '/';
                }
                break;
            case 'dropdown':
            case 'radio':
                $validValues = array_column($this->options['choices'] ?? [], 'value');
                if (!empty($validValues)) {
                    $rules[] = 'in:' . implode(',', $validValues);
                }
                break;
            case 'multi_select':
            case 'checkbox':
                $rules[] = 'array';
                break;
            case 'file_upload':
            case 'image_upload':
                $rules[] = 'file';
                if (isset($config['file_size_kb'])) {
                    $rules[] = 'max:' . $config['file_size_kb'];
                }
                if (isset($config['mime_types'])) {
                    $rules[] = 'mimes:' . implode(',', array_map(function ($mime) {
                        return explode('/', $mime)[1] ?? $mime;
                    }, $config['mime_types']));
                }
                if ($this->field_type === 'image_upload') {
                    $rules[] = 'image';
                }
                break;
        }

        return $rules;
    }

    /**
     * Check if field is visible based on submission data
     */
    public function isVisible(array $submissionData): bool
    {
        if (!$this->visibility_logic) {
            return true;
        }

        $dependsOn = $this->visibility_logic['depends_on'] ?? null;
        $condition = $this->visibility_logic['condition'] ?? 'equals';
        $value = $this->visibility_logic['value'] ?? null;

        if (!$dependsOn) {
            return true;
        }

        $fieldValue = $submissionData[$dependsOn] ?? null;

        switch ($condition) {
            case 'equals':
                return $fieldValue === $value;
            case 'not_equals':
                return $fieldValue !== $value;
            case 'contains':
                return is_array($fieldValue) && in_array($value, $fieldValue);
            case 'not_empty':
                return !empty($fieldValue);
            case 'empty':
                return empty($fieldValue);
            default:
                return true;
        }
    }
}
