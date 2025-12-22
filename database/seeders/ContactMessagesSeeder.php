<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ContactMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تعطيل مفاتيح العلاقات فقط إذا لزم الأمر
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // تفريغ الجدول
        // ContactMessage::truncate();

        // إنشاء كائن Faker
        $faker = Faker::create();

        // تخزين البيانات في مصفوفة واحدة قبل الإدراج
        $data = [];

        for ($i = 0; $i < 30; $i++) {
            $data[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'subject' => $faker->sentence(10), // رسالة قصيرة
                'message' => $faker->sentence(40), // رسالة قصيرة
                'status' => $faker->randomElement(['pending', 'under_review', 'resolved']), // قيمة عشوائية للحالة
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // إدخال جميع البيانات دفعة واحدة (أداء أفضل)
        ContactMessage::insert($data);

        // إعادة تفعيل مفاتيح العلاقات إذا تم تعطيلها
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
