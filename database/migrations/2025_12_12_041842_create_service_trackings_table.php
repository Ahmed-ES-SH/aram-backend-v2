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
        Schema::create('service_trackings', function (Blueprint $table) {
            $table->id();

            // Foreign key to service_pages table
            $table->foreignId('service_id')->constrained('service_pages')->onDelete('cascade');

            // Polymorphic relationship for user (can be User or Organization)
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['user', 'organization']);

            // Foreign keys for order and invoice
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders', 'id')->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');

            // Service tracking status
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled']);

            // Current phase of the service
            $table->enum('current_phase', [
                'initiation',
                'planning',
                'execution',
                'monitoring',
                'review',
                'delivery',
                'support',
            ]);

            // Metadata for flexible additional data (JSON)
            $table->json('metadata')->nullable();

            // Time tracking
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'user_type']);
            $table->index('status');
            $table->index('current_phase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_trackings');
    }
};
