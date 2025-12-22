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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('price_before_discount');
            $table->decimal('price');
            $table->integer('number_of_promotional_purchases')->default(0);
            $table->string('duration');
            $table->text('image');
            $table->integer('order')->nullable();
            $table->boolean('active')->default(0);
            $table->foreignId('category_id')->constrained('card_categories', 'id')->onUpdate('cascade')->onDelete('cascade');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
