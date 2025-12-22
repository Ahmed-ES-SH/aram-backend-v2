<?php

namespace Database\Seeders;

use App\Models\VariableData;
use Illuminate\Database\Seeder;

class StatsSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VariableData::updateOrCreate(
            ['id' => 4],
            [
                'column_1' => json_encode([
                    'en' => "Our Statistics",
                    'ar' => "إحصائياتنا",
                ]),

                'column_2' => json_encode([
                    'en' => "Numbers That Speak",
                    'ar' => "أرقام تتحدث",
                ]),

                'column_3' => json_encode([
                    'en' => "Trusted by thousands of clients worldwide",
                    'ar' => "موثوق من آلاف العملاء حول العالم",
                ]),

                'column_4' => json_encode([
                    [
                        'value' => 2500,
                        'suffix' => "+",
                        'label' => [
                            'en' => "Clients",
                            'ar' => "عميل"
                        ],
                        'key' => "clients",
                        'icon_name' => "FaUsers",
                    ],
                    [
                        'value' => 850,
                        'suffix' => "+",
                        'label' => [
                            'en' => "Projects",
                            'ar' => "مشروع"
                        ],
                        'key' => "projects",
                        'icon_name' => "FaBriefcase",
                    ],
                    [
                        'value' => 15,
                        'suffix' => "+",
                        'label' => [
                            'en' => "Experience Years",
                            'ar' => "سنة خبرة"
                        ],
                        'key' => "experience",
                        'icon_name' => "FaClock",
                    ],
                    [
                        'value' => 45,
                        'suffix' => "",
                        'label' => [
                            'en' => "Awards",
                            'ar' => "جائزة"
                        ],
                        'key' => "awards",
                        'icon_name' => "FaAward",
                    ],
                ]),
            ]
        );
    }
}
