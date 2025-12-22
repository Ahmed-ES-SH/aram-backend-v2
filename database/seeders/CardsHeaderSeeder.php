<?php

namespace Database\Seeders;

use App\Models\VariableData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardsHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VariableData::updateOrCreate(
            ['id' => 1],
            [
                'column_1' => json_encode([
                    'en' => "Premium Collection",
                    'ar' => "مجموعة مميزة"
                ]),
                'column_2' => json_encode([
                    'en' => "Our Premium Cards",
                    'ar' => "بطاقاتنا المميزة"
                ]),
                'column_3' => json_encode([
                    'en' => "Discover our exclusive collection of premium membership cards",
                    'ar' => "اكتشف مجموعتنا الحصرية من بطاقات العضوية المميزة"
                ]),
                'column_4' => json_encode([
                    [
                        'icon_name' => "FaCrown",
                        'text' => ['en' => "Premium Quality", 'ar' => "جودة عالية"],
                        'color' => "from-amber-500/20 to-amber-600/10"
                    ],
                    [
                        'icon_name' => "FaShieldAlt",
                        'text' => ['en' => "Secure", 'ar' => "آمن"],
                        'color' => "from-emerald-500/20 to-emerald-600/10"
                    ],
                    [
                        'icon_name' => "FaPercent",
                        'text' => ['en' => "Exclusive Deals", 'ar' => "عروض حصرية"],
                        'color' => "from-purple-500/20 to-purple-600/10"
                    ],
                    [
                        'icon_name' => "FaClock",
                        'text' => ['en' => "Lifetime Access", 'ar' => "وصول دائم"],
                        'color' => "from-blue-500/20 to-blue-600/10"
                    ]
                ])
            ]
        );
    }
}
