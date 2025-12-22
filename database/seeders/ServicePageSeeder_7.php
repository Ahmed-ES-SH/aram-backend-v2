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
use Illuminate\Database\Seeder;

class ServicePageSeeder_7 extends Seeder
{
    public function run(): void
    {
        // Create the main service page
        $servicePage = ServicePage::updateOrCreate([
            'slug' => 'graphic-design',
            'is_active' => true,
            'price' => 1299.99,
            'price_before_discount' => 1999.99,
            'type' => 'one_time',
            'status' => 'active',
            'order' => 6,
            'category_id' => 6,
            'whatsapp_number' => '+966500000007',
        ]);

        // Hero Section
        ServicePageHeroSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'badge_ar' => 'التميز في التصميم البصري',
            'badge_en' => 'Excellence in Visual Design',
            'title_ar' => 'تصميم جرافيك',
            'title_en' => 'Graphic Design',
            'subtitle_ar' => 'إبداع بلا حدود',
            'subtitle_en' => 'Unlimited Creativity',
            'description_ar' => 'نحول أفكارك إلى تصميمات إبداعية تجذب الأنظار وتنقل رسالتك بفعالية',
            'description_en' => 'We turn your ideas into creative designs that attract attention and convey your message effectively',
            'hero_image' => '/services/service-man.png',
        ]);

        // Problem Section
        $problemSection = ServicePageProblemSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'تحديات التصميم التقليدي',
            'title_en' => 'Traditional Design Challenges',
            'subtitle_ar' => 'لماذا تفشل الهويات البصرية؟',
            'subtitle_en' => 'Why Do Visual Identities Fail?',
        ]);

        // Problem Items
        $problemItems = [
            [
                'icon' => 'FaPalette',
                'title_ar' => 'تصميمات تقليدية',
                'title_en' => 'Traditional Designs',
                'description_ar' => 'تصميمات مكررة لا تعبر عن هوية فريدة',
                'description_en' => 'Repeated designs that don\'t express a unique identity',
                'order' => 0,
            ],
            [
                'icon' => 'FaClock',
                'title_ar' => 'وقت طويل للتنفيذ',
                'title_en' => 'Long Execution Time',
                'description_ar' => 'فترات انتظار طويلة للحصول على التصميم النهائي',
                'description_en' => 'Long waiting periods to get the final design',
                'order' => 1,
            ],
            [
                'icon' => 'FaMoneyBillAlt',
                'title_ar' => 'تكاليف غير متوقعة',
                'title_en' => 'Unexpected Costs',
                'description_ar' => 'تكاليف إضافية للتعديلات والتغييرات',
                'description_en' => 'Additional costs for modifications and changes',
                'order' => 2,
            ],
        ];

        foreach ($problemItems as $item) {
            ServicePageProblemItem::updateOrCreate(array_merge($item, ['problem_section_id' => $problemSection->id]));
        }

        // Solution Section
        $solutionSection = ServicePageSolutionSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'حلولنا الإبداعية',
            'title_en' => 'Our Creative Solutions',
            'subtitle_ar' => 'تصميمات مبتكرة تناسب جميع الاحتياجات',
            'subtitle_en' => 'Innovative Designs That Suit All Needs',
            'cta_text_ar' => 'ابدأ مشروعك الإبداعي',
            'cta_text_en' => 'Start Your Creative Project',
        ]);

        // Solution Features
        $features = [
            [
                'feature_key' => 'logo-design',
                'icon' => 'FaPaintBrush',
                'color' => 'bg-purple-600',
                'title_ar' => 'تصميم الشعارات',
                'title_en' => 'Logo Design',
                'description_ar' => 'شعارات فريدة تعبر عن هوية علامتك التجارية',
                'description_en' => 'Unique logos that express your brand identity',
                'preview_image' => '/services/main-image.png',
                'order' => 0,
            ],
            [
                'feature_key' => 'brand-identity',
                'icon' => 'FaBrush',
                'color' => 'bg-blue-500',
                'title_ar' => 'هوية العلامة التجارية',
                'title_en' => 'Brand Identity',
                'description_ar' => 'تصميم متكامل للهوية البصرية لعلامتك التجارية',
                'description_en' => 'Comprehensive design of your brand\'s visual identity',
                'preview_image' => '/services/service-1.png',
                'order' => 1,
            ],
            [
                'feature_key' => 'social-media-design',
                'icon' => 'FaHashtag',
                'color' => 'bg-pink-500',
                'title_ar' => 'تصميم وسائل التواصل',
                'title_en' => 'Social Media Design',
                'description_ar' => 'تصميم محتوى مخصص لجميع منصات التواصل الاجتماعي',
                'description_en' => 'Custom content design for all social media platforms',
                'preview_image' => '/services/service-2.png',
                'order' => 2,
            ],
            [
                'feature_key' => 'print-design',
                'icon' => 'FaPrint',
                'color' => 'bg-yellow-600',
                'title_ar' => 'تصميم المواد المطبوعة',
                'title_en' => 'Print Design',
                'description_ar' => 'تصميم الكتيبات والبروشورات والمواد الترويجية',
                'description_en' => 'Design of brochures, flyers and promotional materials',
                'preview_image' => '/services/service-3.png',
                'order' => 3,
            ],
            [
                'feature_key' => 'motion-graphics',
                'icon' => 'FaVideo',
                'color' => 'bg-green-600',
                'title_ar' => 'الجرافيك المتحرك',
                'title_en' => 'Motion Graphics',
                'description_ar' => 'تصميم فيديوهات إبداعية للرسوم المتحركة',
                'description_en' => 'Creative design of animated videos',
                'preview_image' => '/services/main-image.png',
                'order' => 4,
            ],
        ];

        foreach ($features as $feature) {
            ServicePageSolutionFeature::updateOrCreate(array_merge($feature, ['solution_section_id' => $solutionSection->id]));
        }

        // Gallery Images
        $galleryImages = [
            ['path' => '/services/service-1.png', 'alt_ar' => 'تصميم شعار احترافي', 'alt_en' => 'Professional Logo Design', 'order' => 0],
            ['path' => '/services/service-2.png', 'alt_ar' => 'تصميم هوية بصرية', 'alt_en' => 'Visual Identity Design', 'order' => 1],
            ['path' => '/services/service-3.png', 'alt_ar' => 'تصميم وسائل التواصل', 'alt_en' => 'Social Media Design', 'order' => 2],
        ];

        foreach ($galleryImages as $image) {
            ServicePageGalleryImage::updateOrCreate(array_merge($image, ['service_page_id' => $servicePage->id]));
        }

        // Stats
        $stats = [
            ['number' => '2000+', 'label_ar' => 'تصميم ناجح', 'label_en' => 'Successful Designs', 'order' => 0],
            ['number' => '24 ساعة', 'label_ar' => 'تسليم سريع', 'label_en' => 'Fast Delivery', 'order' => 1],
            ['number' => '100%', 'label_ar' => 'رضا العملاء', 'label_en' => 'Client Satisfaction', 'order' => 2],
            ['number' => '3 مراجعات', 'label_ar' => 'مراجعات مجانية', 'label_en' => 'Free Revisions', 'order' => 3],
        ];

        foreach ($stats as $stat) {
            ServicePageStat::updateOrCreate(array_merge($stat, ['service_page_id' => $servicePage->id]));
        }

        // Testimonials
        $testimonials = [
            [
                'name_ar' => 'ريم العلي',
                'name_en' => 'Reem Al-Ali',
                'text_ar' => 'الشعار الذي صمموه لشركتي كان سبباً رئيسياً في نجاح علامتنا التجارية',
                'text_en' => 'The logo they designed for my company was a major reason for the success of our brand',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 0,
            ],
            [
                'name_ar' => 'عمر السفياني',
                'name_en' => 'Omar Al-Sufyani',
                'text_ar' => 'فريق إبداعي يفهم احتياجات العميل ويترجمها إلى تصميمات متميزة',
                'text_en' => 'A creative team that understands customer needs and translates them into outstanding designs',
                'rating' => 5,
                'avatar' => 'avatars/user2.jpg',
                'order' => 1,
            ],
            [
                'name_ar' => 'نوف الخالد',
                'name_en' => 'Nouf Al-Khaled',
                'text_ar' => 'التصاميم التي قدموها لمنصات التواصل الاجتماعي زادت من تفاعل متابعينا بنسبة 500%',
                'text_en' => 'The designs they provided for social media platforms increased our followers\' engagement by 500%',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 2,
            ],
            [
                'name_ar' => 'فيصل القحطاني',
                'name_en' => 'Faisal Al-Qahtani',
                'text_ar' => 'المهنية وسرعة الاستجابة والتعديلات المجانية جعلت التعامل معهم تجربة رائعة',
                'text_en' => 'Professionalism, quick response and free revisions made dealing with them a great experience',
                'rating' => 5,
                'avatar' => 'avatars/user2.jpg',
                'order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            ServicePageTestimonial::updateOrCreate(array_merge($testimonial, ['service_page_id' => $servicePage->id]));
        }

        // CTA Section
        ServicePageCtaSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'testimonial_title_ar' => 'إبداعنا يتحدث',
            'testimonial_title_en' => 'Our Creativity Speaks',
            'cta_title_ar' => 'اجعل علامتك التجارية لا تُنسى',
            'cta_title_en' => 'Make Your Brand Unforgettable',
            'cta_subtitle_ar' => 'تصميمات إبداعية تحقق أهدافك التسويقية',
            'cta_subtitle_en' => 'Creative designs that achieve your marketing goals',
            'cta_button1_ar' => 'اطلب تصميمك الآن',
            'cta_button1_en' => 'Order Your Design Now',
            'cta_button2_ar' => 'استشارة مجانية',
            'cta_button2_en' => 'Free Consultation',
        ]);

        // ========== SERVICE TRACKING SAMPLE DATA ==========

        // Get first user and organization if they exist, otherwise create tracking with ID 1
        $user = User::first();
        $organization = Organization::first();

        // Sample service trackings for users
        if ($user) {
            // Pending tracking - Logo design
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Startup logo design with modern style',
                    'industry' => 'technology',
                    'color_preferences' => ['blue', 'white'],
                ],
            ]);

            // In-progress tracking - Social media package
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(5),
                'metadata' => [
                    'notes' => 'Monthly social media design package for cafe',
                    'platforms' => ['instagram', 'facebook', 'twitter'],
                    'posts_per_month' => 30,
                    'progress' => '40%',
                ],
            ]);

            // Completed tracking - Brand identity
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(30),
                'end_time' => now()->subDays(5),
                'metadata' => [
                    'notes' => 'Complete brand identity for fitness center',
                    'deliverables' => ['logo', 'business_cards', 'letterhead', 'social_media_kit'],
                    'revisions' => 2,
                    'satisfaction' => 'excellent',
                ],
            ]);
        }

        // Sample service trackings for organizations
        if ($organization) {
            // In-progress tracking for organization - Rebranding project
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(20),
                'metadata' => [
                    'notes' => 'Complete rebranding for established company',
                    'scope' => 'full_rebrand',
                    'team_size' => 3,
                    'budget' => 50000,
                    'deadline' => now()->addDays(15)->toDateString(),
                ],
            ]);

            // Pending tracking for organization - Event design
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Design for annual conference and exhibition',
                    'event_date' => now()->addMonths(2)->toDateString(),
                    'design_elements' => ['banners', 'brochures', 'badges', 'stage_backdrop'],
                    'expected_attendees' => 1000,
                ],
            ]);

            // Completed tracking for organization - Product packaging
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(90),
                'end_time' => now()->subDays(30),
                'metadata' => [
                    'notes' => 'Product packaging design for new food product line',
                    'products' => 5,
                    'market_response' => 'very_positive',
                    'sales_increase' => '200%',
                    'awards' => ['design_excellence_2024'],
                ],
            ]);

            // Cancelled tracking for organization
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'cancelled',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(60),
                'end_time' => now()->subDays(45),
                'metadata' => [
                    'notes' => 'Project cancelled due to budget constraints',
                    'cancellation_reason' => 'budget_issues',
                    'work_completed' => '20%',
                    'refund_percentage' => 80,
                ],
            ]);
        }
        // ========== SERVICE PAGE CONTACT MESSAGES ==========
        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '+966500000007',
            'message' => 'I am interested in this service.',
            'status' => 'processing',
        ]);

        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '+966500000008',
            'message' => 'Please contact me for more details.',
            'status' => 'pending',
        ]);
    }
}
