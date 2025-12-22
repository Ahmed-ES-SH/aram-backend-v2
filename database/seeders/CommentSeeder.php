<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // تأكد إن عندك بيانات في users و articles عشان العلاقات
        $userIds = DB::table('users')->pluck('id')->toArray();
        $articleIds = DB::table('articles')->pluck('id')->toArray();

        if (empty($userIds) || empty($articleIds)) {
            $this->command->warn('No users or articles found, skipping comment seeding.');
            return;
        }

        foreach (range(1, 50) as $i) {
            DB::table('article_comments')->insert([
                'content'     => $faker->sentence(15),
                'likes_count' => $faker->numberBetween(0, 100),
                'status'      => $faker->randomElement(['approved', 'pending', 'rejected']),
                'user_id'     => $faker->randomElement($userIds),
                'article_id'  => $faker->randomElement($articleIds),
                'parent_id'   => $faker->boolean(30) // 30% احتمال يكون رد
                    ? DB::table('article_comments')->inRandomOrder()->value('id')
                    : null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
