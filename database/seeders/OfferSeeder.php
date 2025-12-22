<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('offers')->truncate();

        $categories = DB::table('categories')->pluck('id')->toArray();
        $organizations = DB::table('organizations')->pluck('id')->toArray();

        // صور من Unsplash تناسب كل عرض
        $unsplashImages = [
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1531299204818-ea3f054a7c6c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1556742044-3c52d6e88c62?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1554224154-26032ffc0d07?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1556742045-658174f8f2e5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1556742046-3f4ec8b8c2e9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
            'https://images.unsplash.com/photo-1556742047-6c8b0f8b8b0f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'
        ];

        $offers = [
            [
                'title'            => 'خصم الصيف',
                'description'      => 'خصم 20% على جميع المنتجات خلال موسم الصيف.',
                'number_of_uses'   => 0,
                'usage_limit'      => 100,
                'discount_type'    => 'percentage',
                'discount_value'   => 20.00,
                'code'             => 'SUMMER20',
                'start_date'       => '2025-06-01',
                'end_date'         => '2025-08-31',
                'status'           => 'active',
            ],
            [
                'title'            => 'عرض العودة للمدارس',
                'description'      => 'خصم 50 جنيه على كل طلبية فوق 300 جنيه.',
                'number_of_uses'   => 0,
                'usage_limit'      => 200,
                'discount_type'    => 'fixed',
                'discount_value'   => 50.00,
                'code'             => 'SCHOOL50',
                'start_date'       => '2025-08-15',
                'end_date'         => '2025-09-15',
                'status'           => 'waiting',
            ],
            [
                'title'            => 'عرض الجمعة البيضاء',
                'description'      => 'خصومات تصل إلى 70% على فئات مختارة.',
                'number_of_uses'   => 0,
                'usage_limit'      => 500,
                'discount_type'    => 'percentage',
                'discount_value'   => 70.00,
                'code'             => 'BLACK70',
                'start_date'       => '2025-11-25',
                'end_date'         => '2025-11-30',
                'status'           => 'active',
            ],
            [
                'title'            => 'عرض نهاية العام',
                'description'      => 'خصم 100 جنيه على جميع المنتجات.',
                'number_of_uses'   => 0,
                'usage_limit'      => 150,
                'discount_type'    => 'fixed',
                'discount_value'   => 100.00,
                'code'             => 'END100',
                'start_date'       => '2025-12-20',
                'end_date'         => '2025-12-31',
                'status'           => 'waiting',
            ],
            [
                'title'            => 'عرض رمضان',
                'description'      => 'خصم 25% على المأكولات والمشروبات.',
                'number_of_uses'   => 0,
                'usage_limit'      => 300,
                'discount_type'    => 'percentage',
                'discount_value'   => 25.00,
                'code'             => 'RAMADAN25',
                'start_date'       => '2025-03-01',
                'end_date'         => '2025-04-15',
                'status'           => 'active',
            ],
            [
                'title'            => 'عرض عيد الحب',
                'description'      => 'خصم 40 جنيه على أي هدية.',
                'number_of_uses'   => 0,
                'usage_limit'      => 80,
                'discount_type'    => 'fixed',
                'discount_value'   => 40.00,
                'code'             => 'LOVE40',
                'start_date'       => '2025-02-10',
                'end_date'         => '2025-02-15',
                'status'           => 'expired',
            ],
            [
                'title'            => 'عرض الجمعة السعيدة',
                'description'      => 'خصم 15% على كل المنتجات يوم الجمعة.',
                'number_of_uses'   => 0,
                'usage_limit'      => 50,
                'discount_type'    => 'percentage',
                'discount_value'   => 15.00,
                'code'             => 'FRIDAY15',
                'start_date'       => '2025-09-01',
                'end_date'         => '2025-09-01',
                'status'           => 'active',
            ],
            [
                'title'            => 'عرض الشتاء',
                'description'      => 'خصم 30% على الملابس الشتوية.',
                'number_of_uses'   => 0,
                'usage_limit'      => 120,
                'discount_type'    => 'percentage',
                'discount_value'   => 30.00,
                'code'             => 'WINTER30',
                'start_date'       => '2025-12-01',
                'end_date'         => '2026-01-15',
                'status'           => 'waiting',
            ],
            [
                'title'            => 'عرض الشحن المجاني',
                'description'      => 'احصل على شحن مجاني للطلبات فوق 200 جنيه.',
                'number_of_uses'   => 0,
                'usage_limit'      => 400,
                'discount_type'    => 'fixed',
                'discount_value'   => 0.00,
                'code'             => 'FREESHIP',
                'start_date'       => '2025-07-01',
                'end_date'         => '2025-07-31',
                'status'           => 'active',
            ],
            [
                'title'            => 'عرض VIP',
                'description'      => 'خصم خاص للأعضاء المميزين.',
                'number_of_uses'   => 0,
                'usage_limit'      => null,
                'discount_type'    => 'percentage',
                'discount_value'   => 10.00,
                'code'             => 'VIP10',
                'start_date'       => '2025-01-01',
                'end_date'         => '2025-12-31',
                'status'           => 'active',
            ],
        ];

        foreach ($offers as $index => $offerData) {
            // استخدام صورة عشوائية من مجموعة Unsplash
            $randomImage = $unsplashImages[array_rand($unsplashImages)];

            $offer = Offer::create([
                'title' => $offerData['title'],
                'description' => $offerData['description'],
                'number_of_uses' => $offerData['number_of_uses'],
                'usage_limit' => $offerData['usage_limit'],
                'discount_type' => $offerData['discount_type'],
                'discount_value' => $offerData['discount_value'],
                'image' => $randomImage,
                'code' => $offerData['code'],
                'start_date' => $offerData['start_date'],
                'end_date' => $offerData['end_date'],
                'status' => $offerData['status'],
                'category_id' => $categories[array_rand($categories)],
                'organization_id' => $organizations[array_rand($organizations)],
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
