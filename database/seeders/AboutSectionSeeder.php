<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AboutSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('home_pages')->updateOrInsert(
            ['id' => 2], // شرط البحث
            [
                'column_1' => json_encode([
                    "en" => "Discover Our Platform",
                    "ar" => "اكتشف منصتنا"
                ], JSON_UNESCAPED_UNICODE),

                'column_2' => json_encode([
                    "en" => "Connecting people with services easily.",
                    "ar" => "ربط الأشخاص بالخدمات بسهولة."
                ], JSON_UNESCAPED_UNICODE),

                'column_3' => json_encode([
                    "en" => "Our platform provides high-quality services with trusted professionals.",
                    "ar" => "منصتنا توفر خدمات عالية الجودة مع محترفين موثوقين."
                ], JSON_UNESCAPED_UNICODE),

                'column_4' => json_encode([
                    [
                        "en" => "Trusted and secure services",
                        "ar" => "خدمات موثوقة وآمنة",
                        "icon_name" => "FiShield"
                    ],
                    [
                        "en" => "Professional support team",
                        "ar" => "فريق دعم محترف",
                        "icon_name" => "FiHeadphones"
                    ],
                    [
                        "en" => "High customer satisfaction",
                        "ar" => "رضا العملاء العالي",
                        "icon_name" => "FiCheck"
                    ]
                ], JSON_UNESCAPED_UNICODE),

                'image' => "/about.png",

                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('home_pages')->updateOrInsert(
            ['id' => 1],
            [
                'column_1' => json_encode([
                    "en" => "Aram Gulf Ltd. - Premium Cards & Booking Platform",
                    "ar" => "آرام الخليج المحدودة - بطاقات متميزة ومنصة حجز"
                ], JSON_UNESCAPED_UNICODE),

                'column_2' => json_encode([
                    "en" => "Experience exclusive privileges with our premium cards and easily book appointments across various specialized centers. Enjoy special benefits and seamless booking management.",
                    "ar" => "جرب امتيازات حصرية مع بطاقاتنا المتميزة واحجز مواعيدك بسهولة عبر مراكز متخصصة متنوعة. استمتع بفوائد خاصة وإدارة حجز سلسة."
                ], JSON_UNESCAPED_UNICODE),

                'column_3' => json_encode([
                    ["number" => "50K+", "en" => "Happy Customers", "ar" => "عملاء سعداء"],
                    ["number" => "1M+", "en" => "Bookings Made", "ar" => "عدد الحجوزات"],
                    ["number" => "500+", "en" => "Partner Venues", "ar" => "أماكن شريكة"],
                ], JSON_UNESCAPED_UNICODE),

                'column_30' => '1',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
