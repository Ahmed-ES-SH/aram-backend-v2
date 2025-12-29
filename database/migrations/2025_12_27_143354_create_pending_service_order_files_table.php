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
        Schema::create('pending_service_order_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')
                ->nullable()
                ->constrained('service_orders')
                ->nullOnDelete();

            $table->string('disk')->default('public');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamp('expires_at')->index();
            $table->timestamp('attached_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_service_order_files');
    }
};
