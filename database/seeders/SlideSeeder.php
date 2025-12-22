<?php

namespace Database\Seeders;

use App\Models\Slide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('slides')->truncate();

        $path = 'images/slides'; // Folder inside public
        $fullpath = public_path($path);

        // Get images from public/images/users
        $images = scandir($fullpath);
        $imagesArray = array_filter($images, function ($image) {
            return in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        $slides = [
            [
                'title' => json_encode([
                    'en' => 'Exclusive Premium Cards by Aram Gulf Ltd.',
                    'ar' => 'بطاقات متميزة حصرية من آرام الخليج المحدودة'
                ], JSON_UNESCAPED_UNICODE),
                'description' => json_encode([
                    'en' => 'Discover our exclusive premium cards offering special privileges and benefits. Enjoy exclusive discounts, priority access to services, and partnerships with leading brands. Each card is designed to provide exceptional value and enhance your daily experience with personalized advantages.',
                    'ar' => 'اكتشف بطاقاتنا المتميزة الحصرية التي تقدم امتيازات و فوائد خاصة. استمتع بخصومات حصرية، وصول مميز للخدمات، وشراكات مع علامات تجارية رائدة. كل بطاقة مصممة لتقدم قيمة استثنائية وتعزز تجربتك اليومية بمزايا مخصصة.'
                ], JSON_UNESCAPED_UNICODE),
                'circle_1_color' => '#FF5733',
                'circle_2_color' => '#33C1FF',
                'status' => 'active',
            ],
            [
                'title' => json_encode([
                    'en' => 'VIP Membership Advantages with Aram Gulf',
                    'ar' => 'مزايا العضوية الخاصة مع آرام الخليج'
                ], JSON_UNESCAPED_UNICODE),
                'description' => json_encode([
                    'en' => 'Experience VIP benefits including personalized concierge services, exclusive event invitations, and luxury travel privileges. Our premium cards offer escalating rewards and dedicated support, transforming your lifestyle with exceptional service and recognition.',
                    'ar' => 'جرب مزايا VIP التي تشمل خدمات كونسيرج مخصصة، دعوات حصرية للفعاليات، وامتيازات سرفات فاخرة. بطاقاتنا المتميزة تقدم مكافآت متدرجة ودعم مخصص، محولة نمط حياتك بخدمة استثنائية واعتراف مميز.'
                ], JSON_UNESCAPED_UNICODE),
                'circle_1_color' => '#28A745',
                'circle_2_color' => '#FFC107',
                'status' => 'active',
            ],
            [
                'title' => json_encode([
                    'en' => 'Smart Reservation System by Aram Gulf Ltd.',
                    'ar' => 'نظام الحجز الذكي من آرام الخليج المحدودة'
                ], JSON_UNESCAPED_UNICODE),
                'description' => json_encode([
                    'en' => 'Book appointments easily across various specialized centers with our intelligent scheduling system. Enjoy real-time availability, flexible rescheduling, and automated reminders for all your reservation needs in different specialties.',
                    'ar' => 'احجز مواعيدك بسهولة عبر مراكز متخصصة متنوعة مع نظام الجدولة الذكي لدينا. استمتع بمزايا التوافر الفوري، إعادة الجدولة المرنة، وتذكيرات آلية لجميع احتياجات حجزك في مختلف التخصصات.'
                ], JSON_UNESCAPED_UNICODE),
                'circle_1_color' => '#6F42C1',
                'circle_2_color' => '#20C997',
                'status' => 'active',
            ],
            [
                'title' => json_encode([
                    'en' => 'Efficient Center Booking Management',
                    'ar' => 'إدارة حجز المراكز بكفاءة'
                ], JSON_UNESCAPED_UNICODE),
                'description' => json_encode([
                    'en' => 'Streamline appointments across multiple centers and specialties with our comprehensive booking platform. Benefit from waitlist management, provider ratings, and analytics to optimize your schedule and reduce conflicts.',
                    'ar' => 'بسط جدولة المواعيد عبر مراكز وتخصصات متعددة مع منصة الحجز الشاملة لدينا. استفد من إدارة قوائم الانتظار، تقييمات مقدمي الخدمة، والتحليلات لتحسين جدولك والحد من التعارضات.'
                ], JSON_UNESCAPED_UNICODE),
                'circle_1_color' => '#E83E8C',
                'circle_2_color' => '#FD7E14',
                'status' => 'active',
            ],
        ];

        foreach ($slides as $index => $slide) {
            $randomImage = $imagesArray[array_rand($imagesArray)];
            // Direct public URL (no Storage facade)
            $imageUrl = env('BACK_END_URL') . '/images/slides' . '/slide-' . ($index + 1) . '.png';

            $slide = Slide::create([
                'title' => $slide['title'],
                'description' => $slide['description'],
                'circle_1_color' => $slide['circle_1_color'],
                'circle_2_color' => $slide['circle_2_color'],
                'status' => $slide['status'],
                'image' => $imageUrl
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
