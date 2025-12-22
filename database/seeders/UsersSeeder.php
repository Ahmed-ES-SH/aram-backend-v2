<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('users')->truncate();

        $faker = Faker::create();

        // قائمة بأسماء الدول العشوائية
        $countries = ['USA', 'UK', 'Germany', 'France', 'Italy', 'Canada', 'Spain', 'Egypt', 'Brazil', 'India'];

        foreach (range(1, 50) as $index) {

            // صورة عشوائية من Unsplash (يتم تغييرها لكل مستخدم)
            $imageUrl = 'https://source.unsplash.com/random/300x300?face&v=' . uniqid();

            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('asd'),
                'image' => $imageUrl,
                'country' => $faker->randomElement($countries),
                'gender' => $faker->randomElement(['male', 'female']),
                'birth_date' => $faker->date('Y-m-d'),
                'phone' => $faker->phoneNumber,
                'role' => $faker->randomElement(['admin', 'user', 'super_admin']),
                'created_at' => $faker->dateTimeBetween('-1 years', 'now'),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
