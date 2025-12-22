<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    /**
     * تشغيل Seeder لملء قاعدة البيانات بأعضاء تجريبيين.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // إنشاء 50 بريدًا إلكترونيًا تجريبيًا
        for ($i = 0; $i < 50; $i++) {
            Member::create([
                'email' => $faker->unique()->safeEmail, // بريد إلكتروني عشوائي وفريد
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
