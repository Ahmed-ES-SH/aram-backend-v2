<?php

namespace Database\Seeders;

use App\Models\VariableData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationsHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VariableData::updateOrCreate(
            ['id' => 2],
            [
                'column_1' => json_encode([
                    [
                        'label' => ['en' => "Active Centers", 'ar' => "المراكز النشطة"],
                        'value' => "150+"
                    ],
                    [
                        'label' => ['en' => "Categories", 'ar' => "الأقسام"],
                        'value' => "25+"
                    ],
                    [
                        'label' => ['en' => "Monthly Visitors", 'ar' => "الزوار شهريا"],
                        'value' => "10K+"
                    ],
                    [
                        'label' => ['en' => "Satisfaction Rate", 'ar' => "نسبة الرضا"],
                        'value' => "98%"
                    ],
                ]),

                'column_2' => json_encode([
                    'en' => "Our Partners",
                    'ar' => "شركاؤنا"
                ]),

                'column_3' => json_encode([
                    'en' => "Trusted Organizations",
                    'ar' => "مراكز موثوقة"
                ]),

                'column_4' => json_encode([
                    'en' => "Browse our network of verified and trusted service centers",
                    'ar' => "تصفح شبكتنا من مراكز الخدمة الموثوقة والمعتمدة"
                ]),
            ]
        );
    }
}
