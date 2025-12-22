<?php

namespace Database\Seeders;

use App\Models\VariableData;
use Illuminate\Database\Seeder;

class ServicesHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VariableData::updateOrCreate(
            ['id' => 3],
            [
                'column_1' => json_encode([
                    'en' => "Premium Services",
                    'ar' => "خدمات مميزة",
                ]),

                'column_2' => json_encode([
                    'en' => "Discover Our ",
                    'ar' => "اكتشف ",
                ]),

                'column_3' => json_encode([
                    'en' => "Premium Services",
                    'ar' => "خدماتنا المميزة",
                ]),

                'column_4' => json_encode([
                    'en' => "We provide high-quality services to meet all your needs",
                    'ar' => "نقدم خدمات عالية الجودة لتلبية جميع احتياجاتك",
                ]),

                'column_5' => json_encode([
                    [
                        'icon_name' => "FaLayerGroup",
                        'value' => "500+",
                        'label' => [
                            'en' => "Services",
                            'ar' => "خدمة"
                        ]
                    ],
                    [
                        'icon_name' => "FaUsers",
                        'value' => "10K+",
                        'label' => [
                            'en' => "Clients",
                            'ar' => "عميل"
                        ]
                    ],
                    [
                        'icon_name' => "FaStar",
                        'value' => "4.9",
                        'label' => [
                            'en' => "Rating",
                            'ar' => "تقييم"
                        ]
                    ],
                ]),
            ]
        );
    }
}
