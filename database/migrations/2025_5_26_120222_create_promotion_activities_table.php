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
        Schema::create('promotion_activities', function (Blueprint $table) {
            $table->id();

            // Promoter info
            $table->enum('promoter_type', ['user', 'organization']);
            $table->foreignId('promoter_id')->constrained('promoters', 'promoter_id')->cascadeOnDelete()->index();

            // Activity details
            $table->enum('activity_type', ['signup', 'purchase', 'visit'])->index();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('member_id')->nullable();
            $table->enum('member_type', ['user', 'organization'])->nullable();

            // Optional tracking info
            $table->string('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('device_type')->nullable();
            $table->string('ref_code')->nullable()->index();
            $table->decimal('commission_amount', 10, 2)->nullable();

            // Time tracking
            $table->timestamp('activity_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_activities');
    }
};
