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
        Schema::create('service_form_submission_values', function (Blueprint $table) {
            $table->id();

            // Link to submission
            $table->foreignId('submission_id')->constrained('service_form_submissions')->onDelete('cascade');

            // Link to field
            $table->foreignId('field_id')->constrained('service_form_fields')->onDelete('cascade');

            // Submitted value (text or JSON for arrays/files)
            $table->text('value')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('submission_id');
            $table->index('field_id');
            $table->unique(['submission_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_form_submission_values');
    }
};
