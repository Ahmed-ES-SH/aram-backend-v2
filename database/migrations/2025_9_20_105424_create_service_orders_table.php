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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['user', 'organization']);
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'on_hold', 'completed', 'canceled', 'refunded'])->default('pending');
            $table->enum('subscription_status', ['active', 'expired'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_page_id')->constrained()->cascadeOnDelete();
            $table->json('metadata')->nullable();
            $table->dateTime('subscription_start_time')->nullable();
            $table->dateTime('subscription_end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
