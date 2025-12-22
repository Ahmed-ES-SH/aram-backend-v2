<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_page_problem_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_page_id')->constrained()->cascadeOnDelete();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('subtitle_ar');
            $table->text('subtitle_en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_page_problem_sections');
    }
};
