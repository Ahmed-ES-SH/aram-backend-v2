<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationReview;
use App\Models\User;
use App\Models\Organization; // لو عندك موديل المنظمات
use Faker\Factory as Faker;

class OrganizationReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = User::pluck('id')->toArray();
        $organizationIds = Organization::pluck('id')->toArray();

        if (empty($userIds) || empty($organizationIds)) {
            return; // تجنب حدوث خطأ لو مفيش بيانات
        }

        $reviews = [];

        foreach ($organizationIds as $orgId) {
            for ($i = 0; $i < 10; $i++) {
                $reviews[] = [
                    'stars' => $faker->numberBetween(1, 5),
                    'head_line' => $faker->sentence(6),
                    'content' => $faker->paragraph(3),
                    'like_counts' => $faker->optional()->numberBetween(0, 100) ?? 0,
                    'user_id' => $faker->randomElement($userIds),
                    'organization_id' => $orgId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        OrganizationReview::insert($reviews);
    }
}
