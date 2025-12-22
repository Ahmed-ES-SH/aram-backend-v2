<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::updateOrInsert(
            ['id' => 1], // شرط البحث
            [
                'first_section_title_en' => 'First Section Title (EN)',
                'first_section_title_ar' => 'عنوان القسم الأول',
                'first_section_contnet_ar' => 'محتوى القسم الأول بالعربية',
                'first_section_contnet_en' => 'First section content in English',

                'second_section_title_en' => 'Second Section Title (EN)',
                'second_section_title_ar' => 'عنوان القسم الثاني',
                'second_section_contnet_ar' => 'محتوى القسم الثاني بالعربية',
                'second_section_contnet_en' => 'Second section content in English',

                'thired_section_title_en' => 'Third Section Title (EN)',
                'thired_section_title_ar' => 'عنوان القسم الثالث',
                'thired_section_contnet_ar' => 'محتوى القسم الثالث بالعربية',
                'thired_section_contnet_en' => 'Third section content in English',

                'fourth_section_title_en' => 'Fourth Section Title (EN)',
                'fourth_section_title_ar' => 'عنوان القسم الرابع',
                'fourth_section_contnet_ar' => 'محتوى القسم الرابع بالعربية',
                'fourth_section_contnet_en' => 'Fourth section content in English',

                'show_map' => true,
                'address' => '1234 Street Name, City, Country',

                // ===============================
                // إضافة صور لكل قسم من Unsplash
                // ===============================
                'first_section_image'  => 'https://images.unsplash.com/photo-1506765515384-028b60a970df?auto=format&w=1200',
                'second_section_image' => 'https://images.unsplash.com/photo-1522199755839-a2bacb67c546?auto=format&w=1200',
                'thired_section_image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&w=1200',
                'fourth_section_image' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?auto=format&w=1200',

                'main_video' => null,
                'link_video' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
