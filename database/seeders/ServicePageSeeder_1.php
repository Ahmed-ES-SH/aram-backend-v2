<?php

namespace Database\Seeders;

use App\Models\ServicePage;
use App\Models\ServicePageHeroSection;
use App\Models\ServicePageProblemSection;
use App\Models\ServicePageProblemItem;
use App\Models\ServicePageSolutionSection;
use App\Models\ServicePageSolutionFeature;
use App\Models\ServicePageGalleryImage;
use App\Models\ServicePageStat;
use App\Models\ServicePageTestimonial;
use App\Models\ServicePageCtaSection;
use App\Models\ServiceTracking;
use App\Models\User;
use App\Models\Organization;
use App\Models\ServicePageContactMessage;
use App\Models\ServiceTrackingFile;
use Illuminate\Database\Seeder;

class ServicePageSeeder_1 extends Seeder
{
    public function run(): void
    {
        // Create the main service page
        $servicePage = ServicePage::updateOrCreate([
            'slug' => 'nfc-cards',
            'is_active' => true,
            'price' => 29.99,
            'price_before_discount' => 49.99,
            'type' => 'subscription',
            'status' => 'active',
            'order' => 1,
            'category_id' => 1,
            'whatsapp_number' => '+966500000001',
        ]);

        // Hero Section
        ServicePageHeroSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'badge_ar' => 'الحل الجديد من التسويق الذكي',
            'badge_en' => 'New Smart Marketing Solution',
            'title_ar' => 'لمسة واحدة..',
            'title_en' => 'One Touch..',
            'subtitle_ar' => 'عالم من الفرص',
            'subtitle_en' => 'A World of Opportunities',
            'description_ar' => 'استبدل البطاقات الورقية ببطاقات NFC الذكية وشارك معلوماتك بلمسة واحدة',
            'description_en' => 'Replace paper cards with smart NFC cards and share your information with one touch',
            'hero_image' => '/services/service-man.png',
        ]);

        // Problem Section
        $problemSection = ServicePageProblemSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'المشكلة التي نحلها',
            'title_en' => 'The Problem We Solve',
            'subtitle_ar' => 'البطاقات الورقية التقليدية لها العديد من العيوب',
            'subtitle_en' => 'Traditional paper cards have many drawbacks',
        ]);

        // Problem Items
        $problemItems = [
            [
                'icon' => 'star',
                'title_ar' => 'صعوبة التحديث',
                'title_en' => 'Difficult to Update',
                'description_ar' => 'عند تغيير أي معلومة، عليك طباعة بطاقات جديدة',
                'description_en' => 'When any information changes, you have to print new cards',
                'order' => 0,
            ],
            [
                'icon' => 'book',
                'title_ar' => 'غير صديقة للبيئة',
                'title_en' => 'Not Eco-Friendly',
                'description_ar' => 'ملايين البطاقات الورقية تُلقى سنوياً',
                'description_en' => 'Millions of paper cards are thrown away annually',
                'order' => 1,
            ],
            [
                'icon' => 'dollar',
                'title_ar' => 'تكلفة متكررة',
                'title_en' => 'Recurring Cost',
                'description_ar' => 'تكاليف الطباعة المتكررة تتراكم مع الوقت',
                'description_en' => 'Recurring printing costs accumulate over time',
                'order' => 2,
            ],
        ];

        foreach ($problemItems as $item) {
            ServicePageProblemItem::updateOrCreate(array_merge($item, ['problem_section_id' => $problemSection->id]));
        }

        // Solution Section
        $solutionSection = ServicePageSolutionSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'الحل الذكي',
            'title_en' => 'The Smart Solution',
            'subtitle_ar' => 'بطاقات NFC الذكية توفر لك كل ما تحتاجه',
            'subtitle_en' => 'Smart NFC cards provide everything you need',
            'cta_text_ar' => 'اطلب الآن',
            'cta_text_en' => 'Order Now',
        ]);

        // Solution Features
        $features = [
            [
                'feature_key' => 'business-card',
                'icon' => 'FaIdCard',
                'color' => 'bg-blue-500',
                'title_ar' => 'بطاقة أعمال رقمية',
                'title_en' => 'Digital Business Card',
                'description_ar' => 'شارك جميع معلومات الاتصال بلمسة واحدة',
                'description_en' => 'Share all contact information with one touch',
                'preview_image' => '/services/main-image.png',
                'order' => 0,
            ],
            [
                'feature_key' => 'smart-menu',
                'icon' => 'FaUtensils',
                'color' => 'bg-yellow-500',
                'title_ar' => 'قائمة ذكية للمطاعم',
                'title_en' => 'Smart Menu for Restaurants',
                'description_ar' => 'قوائم طعام رقمية تفاعلية',
                'description_en' => 'Interactive digital food menus',
                'preview_image' => '/services/service-1.png',
                'order' => 1,
            ],
            [
                'feature_key' => 'google-review',
                'icon' => 'FaGoogle',
                'color' => 'bg-green-500',
                'title_ar' => 'تقييمات جوجل',
                'title_en' => 'Google Reviews',
                'description_ar' => 'احصل على تقييمات فورية من عملائك',
                'description_en' => 'Get instant reviews from your customers',
                'preview_image' => '/services/service-2.png',
                'order' => 2,
            ],
            [
                'feature_key' => 'social-media',
                'icon' => 'FaShareAlt',
                'color' => 'bg-purple-500',
                'title_ar' => 'روابط وسائل التواصل',
                'title_en' => 'Social Media Links',
                'description_ar' => 'اربط جميع حساباتك الاجتماعية',
                'description_en' => 'Link all your social accounts',
                'preview_image' => '/services/service-3.png',
                'order' => 3,
            ],
        ];

        foreach ($features as $feature) {
            ServicePageSolutionFeature::updateOrCreate(array_merge($feature, ['solution_section_id' => $solutionSection->id]));
        }

        // Gallery Images
        $galleryImages = [
            ['path' => '/services/service-1.png', 'alt_ar' => 'بطاقة NFC احترافية', 'alt_en' => 'Professional NFC Card', 'order' => 0],
            ['path' => '/services/service-2.png', 'alt_ar' => 'استخدام البطاقة', 'alt_en' => 'Using the Card', 'order' => 1],
            ['path' => '/services/service-3.png', 'alt_ar' => 'تصاميم متنوعة', 'alt_en' => 'Various Designs', 'order' => 2],
        ];

        foreach ($galleryImages as $image) {
            ServicePageGalleryImage::updateOrCreate(array_merge($image, ['service_page_id' => $servicePage->id]));
        }

        // Stats
        $stats = [
            ['number' => '24/7', 'label_ar' => 'دعم متواصل', 'label_en' => 'Continuous Support', 'order' => 0],
            ['number' => '100k+', 'label_ar' => 'عميل راضٍ', 'label_en' => 'Satisfied Customers', 'order' => 1],
            ['number' => '100%', 'label_ar' => 'ضمان الجودة', 'label_en' => 'Quality Guarantee', 'order' => 2],
        ];

        foreach ($stats as $stat) {
            ServicePageStat::updateOrCreate(array_merge($stat, ['service_page_id' => $servicePage->id]));
        }

        // Testimonials
        $testimonials = [
            [
                'name_ar' => 'أحمد محمد',
                'name_en' => 'Ahmed Mohammed',
                'text_ar' => 'خدمة ممتازة! البطاقة غيرت طريقة تواصلي مع العملاء بشكل كامل',
                'text_en' => 'Excellent service! The card completely changed how I connect with clients',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 0,
            ],
            [
                'name_ar' => 'سارة العلي',
                'name_en' => 'Sara Al-Ali',
                'text_ar' => 'تصميم أنيق وسهولة في الاستخدام. أنصح الجميع بتجربتها',
                'text_en' => 'Elegant design and easy to use. I recommend everyone to try it',
                'rating' => 5,
                'avatar' => 'avatars/user2.jpg',
                'order' => 1,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            ServicePageTestimonial::updateOrCreate(array_merge($testimonial, ['service_page_id' => $servicePage->id]));
        }

        // CTA Section
        ServicePageCtaSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'testimonial_title_ar' => 'ماذا يقول عملاؤنا',
            'testimonial_title_en' => 'What Our Clients Say',
            'cta_title_ar' => 'ابدأ رحلتك الرقمية اليوم',
            'cta_title_en' => 'Start Your Digital Journey Today',
            'cta_subtitle_ar' => 'انضم إلى آلاف العملاء الذين وثقوا بنا',
            'cta_subtitle_en' => 'Join thousands of customers who trusted us',
            'cta_button1_ar' => 'اطلب الآن',
            'cta_button1_en' => 'Order Now',
            'cta_button2_ar' => 'تواصل معنا',
            'cta_button2_en' => 'Contact Us',
        ]);

        // ========== SERVICE TRACKING SAMPLE DATA ==========

        // Get first user and organization if they exist, otherwise create tracking with ID 1
        $user = User::first();
        $organization = Organization::first();

        // Sample service trackings for users
        if ($user) {
            // Pending tracking
            $tracking1 = ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'initiation',
                'metadata' => [
                    'notes' => 'Customer requested custom design',
                    'priority' => 'normal',
                ],
            ]);

            // Add a sample file
            $this->seedTrackingFile($tracking1, 'https://via.placeholder.com/300.png/09f/fff', 'requirements.png');

            // In-progress tracking
            $tracking2 = ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'in_progress',
                'current_phase' => 'execution',
                'start_time' => now()->subDays(3),
                'metadata' => [
                    'notes' => 'Design approved, production started',
                    'priority' => 'high',
                ],
            ]);

            // Add sample files
            $this->seedTrackingFile($tracking2, 'https://via.placeholder.com/600x400.png/007bff/ffffff', 'design_draft_v1.png');
            $this->seedTrackingFile($tracking2, 'https://via.placeholder.com/600x400.png/28a745/ffffff', 'contract.png');

            // Completed tracking
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'completed',
                'current_phase' => 'delivery',
                'start_time' => now()->subDays(10),
                'end_time' => now()->subDays(2),
                'metadata' => [
                    'notes' => 'Successfully delivered',
                    'delivery_method' => 'express',
                ],
            ]);
        }

        // Sample service trackings for organizations
        if ($organization) {
            // In-progress tracking for organization
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(1),
                'metadata' => [
                    'notes' => 'Bulk order - 50 cards',
                    'priority' => 'high',
                    'quantity' => 50,
                ],
            ]);

            // Pending tracking for organization
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'pending',
                'current_phase' => 'initiation',
                'metadata' => [
                    'notes' => 'Corporate branding requested',
                    'priority' => 'normal',
                    'quantity' => 100,
                ],
            ]);
        }

        // ========== SERVICE PAGE CONTACT MESSAGES ==========
        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '+966500000001',
            'message' => 'I am interested in this service.',
            'status' => 'completed',
        ]);

        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '+966500000002',
            'message' => 'Please contact me for more details.',
            'status' => 'pending',
        ]);
    }

    /**
     * Helper to seed a tracking file from a URL.
     */
    private function seedTrackingFile(ServiceTracking $tracking, string $url, string $filename)
    {
        $storagePath = 'uploads/service-tracking';
        if (!file_exists(public_path($storagePath))) {
            mkdir(public_path($storagePath), 0777, true);
        }

        $fullPath = public_path($storagePath . '/' . $filename);

        // Download file if it doesn't exist to avoid repeated downloads
        if (!file_exists($fullPath)) {
            $content = @file_get_contents($url);
            if ($content) {
                file_put_contents($fullPath, $content);
            } else {
                // Fallback if download fails: create a dummy file
                file_put_contents($fullPath, 'Dummy content for ' . $filename);
            }
        }

        // Create record
        \App\Models\ServiceTrackingFile::create([
            'service_tracking_id' => $tracking->id,
            'disk' => 'public_path',
            'path' => $storagePath . '/' . $filename,
            'original_name' => $filename,
            'mime_type' => mime_content_type($fullPath),
            'size' => filesize($fullPath),
            'uploaded_by' => $tracking->user_id,
            'uploaded_by_type' => $tracking->user_type,
        ]);
    }
}
