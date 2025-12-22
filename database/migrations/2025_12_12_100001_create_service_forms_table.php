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
        Schema::create('service_forms', function (Blueprint $table) {
            $table->id();

            // Link to service page
            $table->foreignId('service_page_id')->constrained('service_pages')->onDelete('cascade');

            // Localized form name
            $table->string('name_ar');
            $table->string('name_en');

            // Localized form description
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            // Form versioning
            $table->integer('version')->default(1);

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('service_page_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_forms');
    }
};
