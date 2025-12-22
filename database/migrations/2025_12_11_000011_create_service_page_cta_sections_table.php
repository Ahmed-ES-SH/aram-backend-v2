<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_page_cta_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_page_id')->constrained()->cascadeOnDelete();
            $table->string('testimonial_title_ar');
            $table->string('testimonial_title_en');
            $table->string('cta_title_ar');
            $table->string('cta_title_en');
            $table->text('cta_subtitle_ar');
            $table->text('cta_subtitle_en');
            $table->string('cta_button1_ar', 100)->nullable();
            $table->string('cta_button1_en', 100)->nullable();
            $table->string('cta_button2_ar', 100)->nullable();
            $table->string('cta_button2_en', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_page_cta_sections');
    }
};
