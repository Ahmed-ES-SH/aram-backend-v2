<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\CardBenefit;
use App\Models\Keyword;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        // DB::table('cards')->truncate();

        $categories = DB::table('card_categories')->pluck('id')->toArray();
        $keywordIds = Keyword::pluck('id')->toArray();

        $relativePath = 'images/cards';
        $fullPath = public_path($relativePath);

        $images = scandir($fullPath);
        $imagesArray = array_values(array_filter($images, function ($image) {
            return in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        }));

        $cardsData = [
            [
                'title' => 'Premium Design Card',
                'description' => 'A premium service card for advanced UI/UX design projects.',
                'price_before_discount' => 200,
                'price' => 150,
                'number_of_promotional_purchases' => 35,
                'duration' => '3 months',
                'image' => 'https://via.placeholder.com/300x200?text=Premium+Design',
                'active' => true,
                'category_id' => 1,
            ],
            [
                'title' => 'Basic Logo Card',
                'description' => 'Simple and elegant logo design service for startups.',
                'price_before_discount' => 100,
                'price' => 80,
                'number_of_promotional_purchases' => 50,
                'duration' => '1 month',
                'image' => 'https://via.placeholder.com/300x200?text=Basic+Logo',
                'active' => true,
                'category_id' => 1,
            ],
            [
                'title' => 'Social Media Boost',
                'description' => 'Boost your social media presence with professional designs.',
                'price_before_discount' => 150,
                'price' => 120,
                'number_of_promotional_purchases' => 20,
                'duration' => '2 months',
                'image' => 'https://via.placeholder.com/300x200?text=Social+Media+Boost',
                'active' => true,
                'category_id' => 2,
            ],
            [
                'title' => 'E-Commerce Starter Pack',
                'description' => 'Complete e-commerce store setup for small businesses.',
                'price_before_discount' => 300,
                'price' => 250,
                'number_of_promotional_purchases' => 15,
                'duration' => '6 months',
                'image' => 'https://via.placeholder.com/300x200?text=E-Commerce+Pack',
                'active' => true,
                'category_id' => 3,
            ],
            [
                'title' => 'SEO Optimization Card',
                'description' => 'Improve your website ranking with professional SEO services.',
                'price_before_discount' => 180,
                'price' => 140,
                'number_of_promotional_purchases' => 28,
                'duration' => '2 months',
                'image' => 'https://via.placeholder.com/300x200?text=SEO+Optimization',
                'active' => true,
                'category_id' => 4,
            ],
            [
                'title' => 'Content Writing Pack',
                'description' => 'High-quality articles and blog posts for your brand.',
                'price_before_discount' => 120,
                'price' => 100,
                'number_of_promotional_purchases' => 40,
                'duration' => '1 month',
                'image' => 'https://via.placeholder.com/300x200?text=Content+Writing',
                'active' => true,
                'category_id' => 5,
            ],
            [
                'title' => 'Photography Essentials',
                'description' => 'Professional product and event photography services.',
                'price_before_discount' => 250,
                'price' => 200,
                'number_of_promotional_purchases' => 10,
                'duration' => '3 months',
                'image' => 'https://via.placeholder.com/300x200?text=Photography',
                'active' => false,
                'category_id' => 6,
            ],
            [
                'title' => 'Business Card Design',
                'description' => 'Creative and professional business card design.',
                'price_before_discount' => 80,
                'price' => 60,
                'number_of_promotional_purchases' => 60,
                'duration' => '15 days',
                'image' => 'https://via.placeholder.com/300x200?text=Business+Card',
                'active' => true,
                'category_id' => 1,
            ],
            [
                'title' => 'Mobile App UI Kit',
                'description' => 'Custom mobile app UI kit for iOS and Android.',
                'price_before_discount' => 220,
                'price' => 180,
                'number_of_promotional_purchases' => 22,
                'duration' => '2 months',
                'image' => 'https://via.placeholder.com/300x200?text=Mobile+UI+Kit',
                'active' => true,
                'category_id' => 7,
            ],
            [
                'title' => 'Video Editing Pro',
                'description' => 'Professional video editing for marketing and events.',
                'price_before_discount' => 300,
                'price' => 240,
                'number_of_promotional_purchases' => 18,
                'duration' => '4 months',
                'image' => 'https://via.placeholder.com/300x200?text=Video+Editing',
                'active' => false,
                'category_id' => 8,
            ],
        ];


        $cardBenefits = [
            'Fast delivery',
            '24/7 support',
            'High quality',
            'Customizable',
            'Eco-friendly',
        ];


        foreach ($cardsData as $card) {
            $maxOrder = Card::max('order') ?? 0;

            $randomImages = collect($imagesArray)->random(1)->values();
            // الصورة الرئيسية (الأولى فقط)
            $mainImageUrl = env('BACK_END_URL') . '/' . $relativePath . '/' . $randomImages[0];
            $priceBefore = rand(50, 200);
            $priceAfter = rand(20, $priceBefore - 5);

            $card = Card::create([
                'title' => $card['title'],
                'description' => $card['description'],
                'price_before_discount' => $priceBefore,
                'price' => $priceAfter,
                'number_of_promotional_purchases' => rand(0, 100),
                'duration' => rand(10, 24) . ' months',
                'image' => $mainImageUrl,
                'order' => $maxOrder + 1,
                'active' => (bool)rand(0, 1),
                'category_id' => $categories[array_rand($categories)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($cardBenefits as $benefit) {
                CardBenefit::create([
                    'card_id' => $card->id,
                    'title' => $benefit,
                ]);
            }


            // إضافة كلمات مفتاحية (Keywords)
            $randomKeywordIds = collect($keywordIds)
                ->shuffle()
                ->take(rand(1, 5))
                ->toArray();

            $card->keywords()->attach($randomKeywordIds);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
