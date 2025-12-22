<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        // DB::table('categories')->truncate();

        $urlimage = env('BACK_END_URL');
        $path = 'images/categories';
        $fullpath = public_path($path);

        $images = scandir($fullpath);
        $imagesarray = array_filter($images, function ($image) {
            return in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        $categories = [
            ['title_ar' => 'التقنية', 'title_en' => 'Technology', 'icon_name' => 'FaLaptopCode'],
            ['title_ar' => 'الصحة', 'title_en' => 'Health', 'icon_name' => 'FaHeartbeat'],
            ['title_ar' => 'العلوم', 'title_en' => 'Science', 'icon_name' => 'FaAtom'],
            ['title_ar' => 'الاقتصاد', 'title_en' => 'Economics', 'icon_name' => 'FaChartLine'],
            ['title_ar' => 'البيئة', 'title_en' => 'Environment', 'icon_name' => 'FaLeaf'],
            ['title_ar' => 'التعليم', 'title_en' => 'Education', 'icon_name' => 'FaBook'],
            ['title_ar' => 'الرياضة', 'title_en' => 'Sports', 'icon_name' => 'FaFootballBall'],
            ['title_ar' => 'الثقافة', 'title_en' => 'Culture', 'icon_name' => 'FaTheaterMasks'],
            ['title_ar' => 'السفر', 'title_en' => 'Travel', 'icon_name' => 'FaPlaneDeparture'],
            ['title_ar' => 'الطعام', 'title_en' => 'Food', 'icon_name' => 'FaUtensils'],
            ['title_ar' => 'الأعمال', 'title_en' => 'Business', 'icon_name' => 'FaBriefcase'],
            ['title_ar' => 'الفن', 'title_en' => 'Art', 'icon_name' => 'FaPalette'],
            ['title_ar' => 'التاريخ', 'title_en' => 'History', 'icon_name' => 'FaLandmark'],
            ['title_ar' => 'المالية', 'title_en' => 'Finance', 'icon_name' => 'FaMoneyBillWave'],
            ['title_ar' => 'الأزياء', 'title_en' => 'Fashion', 'icon_name' => 'FaTshirt'],
            ['title_ar' => 'التسويق', 'title_en' => 'Marketing', 'icon_name' => 'FaBullhorn'],
            ['title_ar' => 'التكنولوجيا الحديثة', 'title_en' => 'Modern Technology', 'icon_name' => 'FaMicrochip'],
            ['title_ar' => 'الإعلام', 'title_en' => 'Media', 'icon_name' => 'FaVideo'],
            ['title_ar' => 'التحليل السياسي', 'title_en' => 'Political Analysis', 'icon_name' => 'FaBalanceScale'],
        ];

        foreach ($categories as $cat) {
            $imageuser = $imagesarray[array_rand($imagesarray)];
            $imageurl = $urlimage . '/' . $path . '/' . $imageuser;
            $bgColor = '#' . strtoupper(dechex(rand(0x000000, 0xFFFFFF)));

            DB::table('card_categories')->insert([
                'title_ar' => $cat['title_ar'],
                'title_en' => $cat['title_en'],
                'image' => $imageurl,
                'bg_color' => $bgColor,
                'is_active' => rand(0, 1),
                'icon_name' => $cat['icon_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
