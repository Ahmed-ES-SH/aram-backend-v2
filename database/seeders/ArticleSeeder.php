<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Article::truncate();
        ArticleTag::truncate(); // تنظيف جدول الوسوم أيضًا

        // صور من Unsplash تناسب المقالات الطبية
        $unsplashImages = [
            'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1551076805-e1869033e561?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1551601651-2a8555f1a136?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1551601651-095059c5abb2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1584467735871-8db9ac8d0e52?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1551076805-e1869033e561?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1585435557343-3b092031d5ad?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'
        ];

        $idsarray = DB::table('article_categories')->pluck('id')->toArray();
        $usersidsarray = DB::table('users')->pluck('id')->toArray();
        $tags = DB::table('tags')->pluck('id')->toArray();

        $contents = [
            [
                'title_en' => 'Eye Care',
                'title_ar' => 'العناية بالعيون',
                'description_en' => 'We provide comprehensive eye care services...',
                'description_ar' => 'نقدم خدمات شاملة للعناية بالعيون...',
            ],
            [
                'title_en' => 'Dental Services',
                'title_ar' => 'خدمات طب الأسنان',
                'description_en' => 'Our dental clinic offers a full range of dental services...',
                'description_ar' => 'عيادتنا لطب الأسنان تقدم مجموعة كاملة من الخدمات...',
            ],
            [
                'title_en' => 'Cardiology',
                'title_ar' => 'أمراض القلب',
                'description_en' => 'Our cardiology department offers specialized care...',
                'description_ar' => 'يقدم قسم أمراض القلب لدينا رعاية متخصصة...',
            ],
            [
                'title_en' => 'Pediatrics',
                'title_ar' => 'طب الأطفال',
                'description_en' => 'We provide expert healthcare services for children...',
                'description_ar' => 'نقدم خدمات رعاية صحية متخصصة للأطفال...',
            ],
            [
                'title_en' => 'Orthopedics',
                'title_ar' => 'جراحة العظام',
                'description_en' => 'Our orthopedic department provides specialized care...',
                'description_ar' => 'يقدم قسم جراحة العظام لدينا رعاية متخصصة...',
            ],
        ];

        $articles = [];

        foreach ($contents as $content) {
            // استخدام صورة عشوائية من مجموعة Unsplash
            $randomImage = $unsplashImages[array_rand($unsplashImages)];

            $articles[] = [
                'title_en' => $content['title_en'],
                'title_ar' => $content['title_ar'],
                'content_en' => $content['description_en'],
                'content_ar' => $content['description_ar'],
                'category_id' => $idsarray[array_rand($idsarray)],
                'status' => 'published',
                'author_id' => $usersidsarray[array_rand($usersidsarray)],
                'image' => $randomImage,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // إدخال المقالات والحصول على IDs
        Article::insert($articles);

        // الحصول على جميع مقالات التي تم إدخالها
        $insertedArticles = Article::all();

        $articleTags = [];

        foreach ($insertedArticles as $article) {
            // اختيار عدد عشوائي من الوسوم لكل مقال (من 2 إلى 4 وسوم)
            $numberOfTags = rand(2, 4);
            $selectedTags = array_rand($tags, $numberOfTags);

            // إذا كان tag واحد فقط، حوّله إلى array
            if (!is_array($selectedTags)) {
                $selectedTags = [$selectedTags];
            }

            foreach ($selectedTags as $tagIndex) {
                $articleTags[] = [
                    'tag_id' => $tags[$tagIndex],
                    'article_id' => $article->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // إدخال جميع الوسوم دفعة واحدة
        ArticleTag::insert($articleTags);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
