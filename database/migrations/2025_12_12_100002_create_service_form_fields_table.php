<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_form_fields', function (Blueprint $table) {
            $table->id();

            // Link to form
            $table->foreignId('service_form_id')->constrained('service_forms')->onDelete('cascade');

            // Unique field identifier
            $table->string('field_key');

            // Field type
            $table->enum('field_type', [
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
                'datetime'
            ]);

            // Localized labels
            $table->string('label_ar');
            $table->string('label_en');

            // Localized placeholders
            $table->string('placeholder_ar')->nullable();
            $table->string('placeholder_en')->nullable();

            // Options for dropdown/radio/multi_select (JSON)
            $table->json('options')->nullable();

            // Validation rules (JSON)
            $table->json('validation_rules')->nullable();

            // Display order
            $table->integer('order')->default(0);

            // Visibility logic (JSON)
            $table->json('visibility_logic')->nullable();

            // Required field
            $table->boolean('is_required')->default(false);

            $table->timestamps();

            // Indexes
            $table->index('service_form_id');
            $table->index('order');
            $table->unique(['service_form_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_form_fields');
    }
};
