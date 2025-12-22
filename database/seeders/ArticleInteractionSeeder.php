<?php

namespace Database\Seeders;

use App\Models\ArticleInteractions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        ArticleInteractions::truncate();

        // جلب المقالات من قاعدة البيانات
        $articles = DB::table('articles')->pluck('id');

        if ($articles->isEmpty()) {
            $this->command->info('No articles found in the database. Seeder aborted.');
            return;
        }

        $interactions = [];

        foreach ($articles as $articleId) {
            // إنشاء عدد عشوائي من التفاعلات للمقال
            $likes = random_int(0, 500);
            $dislikes = random_int(0, 100);
            $loves = random_int(0, 300);
            $laughters = random_int(0, 200);

            // حساب عدد التفاعلات الإجمالية
            $totalReactions = $likes + $dislikes + $loves + $laughters;

            if ($totalReactions > 0) {
                $interactions[] = [
                    'article_id' => $articleId,
                    'likes' => $likes,
                    'dislikes' => $dislikes,
                    'loves' => $loves,
                    'laughters' => $laughters,
                    'totalReactions' => $totalReactions,
                ];
            }
        }

        // إدخال التفاعلات في الجدول
        ArticleInteractions::insert($interactions);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
