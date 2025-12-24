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
        Schema::create('promoter_ratios', function (Blueprint $table) {
            $table->id();
            $table->decimal('visit_ratio', 10, 2)->default(1);
            $table->decimal('signup_ratio', 10, 2)->default(1);
            $table->decimal('purchase_ratio', 10, 2)->default(1);
            $table->decimal('service_ratio', 10, 2)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promoter_ratios');
    }
};
