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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // علاقات
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');

            // بيانات الحجز
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->boolean('is_paid')->default(0);

            // حالات الحجز
            $table->enum('status', [
                'pending',      // بانتظار تأكيد المركز
                'confirmed',    // تم تأكيد الموعد
                'rejected',     // تم الرفض
                'cancelled_by_user',  // المستخدم ألغى الحجز
                'cancelled_by_org',    // المركز ألغى الحجز
                'done'    //   تم انتهاء الحجز
            ])->default('pending');

            // ملاحظات أو رسائل من المستخدم أو المركز
            $table->text('user_notes')->nullable();
            $table->text('organization_notes')->nullable();

            // وقت التأكيد أو الرفض
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
