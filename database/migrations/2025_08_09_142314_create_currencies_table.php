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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // اسم العملة (مثلاً: دولار أمريكي)
            $table->string('code', 10);      // رمز العملة (مثلاً: USD, SAR)
            $table->string('symbol');        // العلامة (مثلاً: $ أو د.إ)
            $table->decimal('exchange_rate', 15, 4); // سعر الصرف مقارنة بالعملة الأساسية
            $table->boolean('is_default')->default(false); // هل هي العملة الافتراضية؟
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
