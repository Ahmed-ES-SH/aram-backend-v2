<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleCategorySeeder extends Seeder
{


    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('article_categories')->truncate();
        $urlimage = env('BACK_END_URL');
        $path = 'images/articleCategories';
        $fullpath = public_path($path);
        $images = scandir($fullpath);
        $imagesarray = array_filter($images, function ($image) {
            return in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        $categories = [
            [
                'title_ar' => 'التقنية',
                'title_en' => 'Technology',
            ],
            [
                'title_ar' => 'الصحة',
                'title_en' => 'Health',
            ],
            [
                'title_ar' => 'العلوم',
                'title_en' => 'Science',
            ],
            [
                'title_ar' => 'الاقتصاد',
                'title_en' => 'Economics',
            ],
            [
                'title_ar' => 'البيئة',
                'title_en' => 'Environment',
            ],
            [
                'title_ar' => 'التعليم',
                'title_en' => 'Education',
            ],
            [
                'title_ar' => 'الرياضة',
                'title_en' => 'Sports',
            ],
            [
                'title_ar' => 'الثقافة',
                'title_en' => 'Culture',
            ],
            [
                'title_ar' => 'السفر',
                'title_en' => 'Travel',
            ],
            [
                'title_ar' => 'الطعام',
                'title_en' => 'Food',
            ],
            [
                'title_ar' => 'الأعمال',
                'title_en' => 'Business',
            ],
            [
                'title_ar' => 'الفن',
                'title_en' => 'Art',
            ],
            [
                'title_ar' => 'التاريخ',
                'title_en' => 'History',
            ],
            [
                'title_ar' => 'المالية',
                'title_en' => 'Finance',
            ],
            [
                'title_ar' => 'الأزياء',
                'title_en' => 'Fashion',
            ],
            [
                'title_ar' => 'التسويق',
                'title_en' => 'Marketing',
            ],
            [
                'title_ar' => 'التكنولوجيا الحديثة',
                'title_en' => 'Modern Technology',
            ],
            [
                'title_ar' => 'الإعلام',
                'title_en' => 'Media',
            ],
            [
                'title_ar' => 'التحليل السياسي',
                'title_en' => 'Political Analysis',
            ]
        ];
        foreach ($categories as $cat) {
            $imageuser = $imagesarray[array_rand($imagesarray)];
            // $imageurl = $urlimage . '/' . 'public/'  . $path . '/' . $imageuser;
            $imageurl = $urlimage . '/'  . $path . '/' . $imageuser;
            DB::table('article_categories')->insert([
                'title_ar' => $cat['title_ar'], // توليد عنوان عشوائي
                'title_en' => $cat['title_en'], // توليد عنوان عشوائي
                'image' => $imageurl, // الصورة المولدة
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
