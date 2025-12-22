<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\CouponCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('coupons')->truncate();


        $categories = DB::table('categories')->pluck('id')->toArray();
        $Subcategories = DB::table('sub_categories')->pluck('id')->toArray();


        $relativePath = 'images/coupons';
        $fullPath = public_path($relativePath);
        $images = scandir($fullPath);
        $imagesArray = array_values(array_filter($images, function ($image) {
            return in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        }));

        $coupons = [
            [
                'code'           => 'WELCOME10',
                'title'          => 'Welcome 10% Discount',
                'description'    => 'Get 10% off on your first purchase.',
                'type'           => 'general',
                'benefit_type'   => 'percentage',
                'discount_value' => 10,
                'start_date'     => '2025-01-01',
                'end_date'       => '2025-12-31',
                'status'         => 'active',
            ],
            [
                'code'           => 'FREECARD25',
                'title'          => 'Free Card for New Users',
                'description'    => 'Claim your free membership card this season.',
                'type'           => 'user',
                'benefit_type'   => 'free_card',
                'discount_value' => null,
                'start_date'     => '2025-02-01',
                'end_date'       => '2025-08-31',
                'status'         => 'active',
            ],
            [
                'code'           => 'CENTER50',
                'title'          => '50 EGP Off Centers',
                'description'    => 'Enjoy 50 EGP off at selected centers.',
                'type'           => 'organization',
                'benefit_type'   => 'fixed',
                'discount_value' => 50,
                'start_date'     => '2025-03-01',
                'end_date'       => '2025-04-30',
                'status'         => 'active',
            ],
            [
                'code'           => 'STUDENT15',
                'title'          => 'Student 15% Discount',
                'description'    => 'Special 15% off for students with valid ID.',
                'type'           => 'user',
                'benefit_type'   => 'percentage',
                'discount_value' => 15,
                'start_date'     => '2025-01-15',
                'end_date'       => '2025-05-15',
                'status'         => 'active',
            ],
            [
                'code'           => 'SUMMER20',
                'title'          => 'Summer 20% Off',
                'description'    => 'Beat the heat with a 20% summer discount.',
                'type'           => 'general',
                'benefit_type'   => 'percentage',
                'discount_value' => 20,
                'start_date'     => '2025-06-01',
                'end_date'       => '2025-08-31',
                'status'         => 'inactive',
            ],
            [
                'code'           => 'ORG100',
                'title'          => '100 EGP Off for Orgs',
                'description'    => 'Organizations get 100 EGP off bulk orders.',
                'type'           => 'organization',
                'benefit_type'   => 'fixed',
                'discount_value' => 100,
                'start_date'     => '2025-01-10',
                'end_date'       => '2025-07-10',
                'status'         => 'active',
            ],
            [
                'code'           => 'FREESHIP',
                'title'          => 'Free Shipping Coupon',
                'description'    => 'Get free shipping on all orders this week.',
                'type'           => 'general',
                'benefit_type'   => 'fixed',
                'discount_value' => 0,
                'start_date'     => '2025-02-05',
                'end_date'       => '2025-02-12',
                'status'         => 'expired',
            ],
            [
                'code'           => 'VIPCARD',
                'title'          => 'VIP Free Card',
                'description'    => 'Exclusive VIP free card for loyal users.',
                'type'           => 'user',
                'benefit_type'   => 'free_card',
                'discount_value' => null,
                'start_date'     => '2025-04-01',
                'end_date'       => '2025-10-01',
                'status'         => 'active',
            ],
            [
                'code'           => 'FLASH30',
                'title'          => 'Flash Sale 30% Off',
                'description'    => 'Limited-time 30% discount for flash sale.',
                'type'           => 'general',
                'benefit_type'   => 'percentage',
                'discount_value' => 30,
                'start_date'     => '2025-03-15',
                'end_date'       => '2025-03-16',
                'status'         => 'expired',
            ],
            [
                'code'           => 'CORP500',
                'title'          => 'Corporate 500 EGP Discount',
                'description'    => 'Special 500 EGP discount for corporate accounts.',
                'type'           => 'organization',
                'benefit_type'   => 'fixed',
                'discount_value' => 500,
                'start_date'     => '2025-05-01',
                'end_date'       => '2025-12-31',
                'status'         => 'inactive',
            ],
        ];

        foreach ($coupons as $couponData) {
            $randomImages = collect($imagesArray)->random(1)->values();
            $mainImageUrl = env('BACK_END_URL') . '/' . $relativePath . '/' . $randomImages[0];

            $coupon = Coupon::create([
                'code' => $couponData['code'] . '_' . strtoupper(Str::random(5)),
                'title' => $couponData['title'],
                'description' => $couponData['description'],
                'image' => $mainImageUrl,
                'type' => $couponData['type'],
                'benefit_type' => $couponData['benefit_type'],
                'discount_value' => $couponData['discount_value'],
                'start_date' => $couponData['start_date'],
                'end_date' => $couponData['end_date'],
                'status' => $couponData['status'],
                'category_id' => $categories[array_rand($categories)],
            ]);

            for ($i = 0; $i < 3; $i++) {
                $randomId = $Subcategories[array_rand($Subcategories)];
                CouponCategory::create([
                    'coupon_id' => $coupon->id,
                    'subcategory_id' => $randomId,
                ]);
            }
        }
    }
}
