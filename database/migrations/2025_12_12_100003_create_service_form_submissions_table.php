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
        Schema::create('service_form_submissions', function (Blueprint $table) {
            $table->id();

            // Link to form
            $table->foreignId('service_form_id')->constrained('service_forms')->onDelete('cascade');

            // Polymorphic user (User or Organization)
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['user', 'organization']);

            // Submission status
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected'])->default('pending');

            // Optional link to service tracking
            $table->foreignId('service_tracking_id')->nullable()->constrained('service_trackings')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('service_form_id');
            $table->index(['user_id', 'user_type']);
            $table->index('status');
            $table->index('service_tracking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_form_submissions');
    }
};
