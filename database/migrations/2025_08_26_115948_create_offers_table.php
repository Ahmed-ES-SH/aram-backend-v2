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
        Schema::create('offers', function (Blueprint $table) {
            // الأعمدة الأساسية
            $table->id(); // معرف فريد للعرض/الكوبون
            $table->string('title'); // عنوان العرض باللغة العربية
            $table->text('description')->nullable(); // وصف العرض باللغة العربية
            $table->text('image')->nullable(); // صورة العرض أو الكوبون
            $table->integer('number_of_uses')->default(0); // وصف العرض باللغة الإنجليزية
            $table->integer('usage_limit')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // قيمة الخصم
            $table->string('code')->unique()->nullable();
            $table->date('start_date'); // تاريخ بداية العرض
            $table->date('end_date'); // تاريخ نهاية العرض
            $table->enum('status', ['waiting', 'active', 'expired'])->default('active'); // تاريخ بداية العرض
            $table->foreignId('organization_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onUpdate('cascade')->onDelete('cascade'); // العلاقة مع الفئة
            $table->timestamps(); // created_at و updated_at
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
