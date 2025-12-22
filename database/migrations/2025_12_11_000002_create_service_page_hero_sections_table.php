<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_page_hero_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_page_id')->constrained()->cascadeOnDelete();
            $table->string('badge_ar');
            $table->string('badge_en');
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('subtitle_ar');
            $table->string('subtitle_en');
            $table->text('description_ar');
            $table->text('description_en');
            $table->string('watch_btn_ar', 100)->nullable();
            $table->string('watch_btn_en', 100)->nullable();
            $table->string('explore_btn_ar', 100)->nullable();
            $table->string('explore_btn_en', 100)->nullable();
            $table->string('hero_image');
            $table->string('background_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_page_hero_sections');
    }
};
