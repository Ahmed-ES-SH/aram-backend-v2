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
        Schema::create('promoters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promoter_id')->unique();
            $table->enum('promoter_type', ['user', 'organization']);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('referral_code')->unique();
            $table->enum('status', ['active', 'disabled'])->default('active');
            $table->unsignedInteger('total_visits')->default(0);
            $table->unsignedInteger('total_signups')->default(0);
            $table->unsignedInteger('total_purchases')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promoters');
    }
};
