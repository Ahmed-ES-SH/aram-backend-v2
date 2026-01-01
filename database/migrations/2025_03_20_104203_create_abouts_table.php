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

        Schema::create('abouts', function (Blueprint $table) {
            $table->id();  // تعريف العمود الرئيسي
            $table->string('first_section_title_ar')->nullable(); //
            $table->string('first_section_title_en')->nullable(); //
            $table->string('second_section_title_ar')->nullable(); //
            $table->string('second_section_title_en')->nullable(); //
            $table->string('thired_section_title_ar')->nullable(); //
            $table->string('thired_section_title_en')->nullable(); //
            $table->string('fourth_section_title_ar')->nullable(); //
            $table->string('fourth_section_title_en')->nullable(); //
            $table->text('first_section_content_ar')->nullable();  // محتوى عن الشركة بالعربية
            $table->text('first_section_content_en')->nullable();  // محتوى عن الشركة بالإنجليزية
            $table->text('second_section_content_ar')->nullable();  // رؤية الشركة بالعربية
            $table->text('second_section_content_en')->nullable();  // رؤية الشركة بالإنجليزية
            $table->text('thired_section_content_ar')->nullable();  // أهداف الشركة بالعربية
            $table->text('thired_section_content_en')->nullable();  // أهداف الشركة بالإنجليزية
            $table->text('fourth_section_content_en')->nullable();  // قيم الشركة بالإنجليزية
            $table->text('fourth_section_content_ar')->nullable();  // قيم الشركة بالعربية
            $table->boolean('show_map')->nullable();  // إظهار الخريطة
            $table->string('address')->nullable();  // عنوان الشركة (اختياري)
            $table->string('first_section_image')->nullable();  // مسار صورة "عن الشركة" (اختياري)
            $table->string('second_section_image')->nullable();  // مسار صورة الرؤية
            $table->string('thired_section_image')->nullable();  // مسار صورة الأهداف
            $table->string('fourth_section_image')->nullable();  // مسار صورة القيم
            $table->text('main_video')->nullable();  // مسار الفيديو الرئيسي (اختياري)
            $table->text('link_video')->nullable();  // مسار الفيديو الرئيسي (اختياري)
            $table->text('cooperation_pdf')->nullable();  // مسار الفيديو الرئيسي (اختياري)
            $table->timestamps();  // تسجيل تاريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
