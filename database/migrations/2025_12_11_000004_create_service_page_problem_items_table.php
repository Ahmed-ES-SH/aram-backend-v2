<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_page_problem_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_section_id')->constrained('service_page_problem_sections')->cascadeOnDelete();
            $table->string('icon', 50);
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_ar');
            $table->text('description_en');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_page_problem_items');
    }
};
