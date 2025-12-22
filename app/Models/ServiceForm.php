<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ServiceForm extends Model
{
    protected $fillable = [
        'service_page_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'version',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    // ========== RELATIONSHIPS ==========

    public function servicePage(): BelongsTo
    {
        return $this->belongsTo(ServicePage::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ServiceFormField::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ServiceFormSubmission::class);
    }

    // ========== SCOPES ==========

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForService(Builder $query, int $servicePageId): Builder
    {
        return $query->where('service_page_id', $servicePageId);
    }

    // ========== HELPER METHODS ==========

    /**
     * Get form schema for frontend rendering
     */
    public function getSchema(string $locale = 'en'): array
    {
        $nameField = "name_{$locale}";
        $descField = "description_{$locale}";

        return [
            'id' => $this->id,
            'name' => $this->$nameField ?? $this->name_en,
            'description' => $this->$descField ?? $this->description_en,
            'version' => $this->version,
            'fields' => $this->fields->map(fn($field) => $field->getFieldSchema($locale)),
        ];
    }

    /**
     * Duplicate this form with all fields
     */
    public function duplicate(): ServiceForm
    {
        $newForm = $this->replicate();
        $newForm->version = 1;
        $newForm->is_active = false;
        $newForm->save();

        foreach ($this->fields as $field) {
            $newField = $field->replicate();
            $newField->service_form_id = $newForm->id;
            $newField->save();
        }

        return $newForm;
    }

    /**
     * Increment the form version
     */
    public function incrementVersion(): bool
    {
        return $this->update(['version' => $this->version + 1]);
    }

    /**
     * Get localized name
     */
    public function getLocalizedName(string $locale = 'en'): string
    {
        $field = "name_{$locale}";
        return $this->$field ?? $this->name_en;
    }

    /**
     * Get localized description
     */
    public function getLocalizedDescription(string $locale = 'en'): ?string
    {
        $field = "description_{$locale}";
        return $this->$field ?? $this->description_en;
    }
}
