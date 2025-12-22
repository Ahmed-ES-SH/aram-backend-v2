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
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();

            // ربط الكوبون الأساسي
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');

            // ممكن يكون مستخدم أو مركز -> نخليه nullable حسب النوع
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');

            // تفاصيل العملية
            $table->unsignedBigInteger('order_id')->nullable(); // لو فيه حجوزات/طلبات مرتبطة
            $table->decimal('order_amount', 10, 2)->nullable(); // قيمة الطلب الأصلي
            $table->decimal('discount_applied', 10, 2)->default(0); // قيمة الخصم اللي اتخصمت فعليًا

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
