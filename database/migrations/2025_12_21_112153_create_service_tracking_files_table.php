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
        Schema::create('service_tracking_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_tracking_id')
                ->constrained('service_trackings')
                ->cascadeOnDelete();

            $table->enum('file_type', ['attachment', 'design_file']);
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->unsignedBigInteger('uploaded_by');
            $table->enum('uploaded_by_type', ['user', 'organization']);

            $table->timestamps();

            $table->index(['service_tracking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_tracking_files');
    }
};
