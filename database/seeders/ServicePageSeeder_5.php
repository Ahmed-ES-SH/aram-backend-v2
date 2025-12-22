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

class ServicePageSeeder_5 extends Seeder
{
    public function run(): void
    {
        // Create the main service page
        $servicePage = ServicePage::updateOrCreate([
            'slug' => 'web-development',
            'is_active' => true,
            'price' => 3999.99,
            'price_before_discount' => 5499.99,
            'type' => 'one_time',
            'status' => 'active',
            'order' => 5,
            'category_id' => 5,
            'whatsapp_number' => '+966500000005',
        ]);

        // Hero Section
        ServicePageHeroSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'badge_ar' => 'الحل التقني المتكامل',
            'badge_en' => 'Complete Technical Solution',
            'title_ar' => 'تطوير المواقع',
            'title_en' => 'Web Development',
            'subtitle_ar' => 'مواقع احترافية',
            'subtitle_en' => 'Professional Websites',
            'description_ar' => 'نطور مواقع إلكترونية متكاملة تلبي احتياجات عملك وتواكب أحدث التقنيات',
            'description_en' => 'We develop integrated websites that meet your business needs and keep pace with the latest technologies',
            'hero_image' => '/services/nfc-hero.png',
        ]);

        // Problem Section
        $problemSection = ServicePageProblemSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'مشاكل المواقع التقليدية',
            'title_en' => 'Traditional Website Problems',
            'subtitle_ar' => 'لماذا تفشل العديد من المواقع؟',
            'subtitle_en' => 'Why Do Many Websites Fail?',
        ]);

        // Problem Items
        $problemItems = [
            [
                'icon' => 'FaExclamationTriangle',
                'title_ar' => 'تصميم غير جذاب',
                'title_en' => 'Unattractive Design',
                'description_ar' => 'واجهات مستخدم قديمة لا تناسب العصر الرقمي',
                'description_en' => 'Outdated user interfaces not suitable for the digital age',
                'order' => 0,
            ],
            [
                'icon' => 'FaMobileAlt',
                'title_ar' => 'عدم التوافق مع الجوال',
                'title_en' => 'Mobile Incompatibility',
                'description_ar' => 'مواقع لا تعمل بشكل صحيح على الهواتف الذكية',
                'description_en' => 'Websites that don\'t work properly on smartphones',
                'order' => 1,
            ],
            [
                'icon' => 'FaTachometerAlt',
                'title_ar' => 'بطء الأداء',
                'title_en' => 'Slow Performance',
                'description_ar' => 'سرعة تحميل بطيئة تؤثر على تجربة المستخدم',
                'description_en' => 'Slow loading speeds that affect user experience',
                'order' => 2,
            ],
        ];

        foreach ($problemItems as $item) {
            ServicePageProblemItem::updateOrCreate(array_merge($item, ['problem_section_id' => $problemSection->id]));
        }

        // Solution Section
        $solutionSection = ServicePageSolutionSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'حلولنا التقنية',
            'title_en' => 'Our Technical Solutions',
            'subtitle_ar' => 'مواقع متطورة تلبي جميع المتطلبات',
            'subtitle_en' => 'Advanced Websites That Meet All Requirements',
            'cta_text_ar' => 'طور موقعك الآن',
            'cta_text_en' => 'Develop Your Website Now',
        ]);

        // Solution Features
        $features = [
            [
                'feature_key' => 'responsive-design',
                'icon' => 'FaDesktop',
                'color' => 'bg-blue-600',
                'title_ar' => 'تصميم متجاوب',
                'title_en' => 'Responsive Design',
                'description_ar' => 'يعمل على جميع الأجهزة والشاشات المختلفة',
                'description_en' => 'Works on all devices and different screens',
                'preview_image' => '/services/main-image.png',
                'order' => 0,
            ],
            [
                'feature_key' => 'seo-optimized',
                'icon' => 'FaSearch',
                'color' => 'bg-green-600',
                'title_ar' => 'تحسين لمحركات البحث',
                'title_en' => 'SEO Optimized',
                'description_ar' => 'بناء سليم يساعد في تصدر نتائج البحث',
                'description_en' => 'Proper structure that helps rank in search results',
                'preview_image' => '/services/service-1.png',
                'order' => 1,
            ],
            [
                'feature_key' => 'fast-loading',
                'icon' => 'FaRocket',
                'color' => 'bg-red-500',
                'title_ar' => 'سرعة فائقة',
                'title_en' => 'Super Fast',
                'description_ar' => 'تحميل سريع يحسن تجربة المستخدم وترتيب الموقع',
                'description_en' => 'Fast loading improves user experience and site ranking',
                'preview_image' => '/services/service-2.png',
                'order' => 2,
            ],
            [
                'feature_key' => 'secure-websites',
                'icon' => 'FaLock',
                'color' => 'bg-yellow-600',
                'title_ar' => 'مواقع آمنة',
                'title_en' => 'Secure Websites',
                'description_ar' => 'حماية متقدمة ضد الاختراقات والهجمات',
                'description_en' => 'Advanced protection against hacks and attacks',
                'preview_image' => '/services/service-3.png',
                'order' => 3,
            ],
            [
                'feature_key' => 'cms-integration',
                'icon' => 'FaCog',
                'color' => 'bg-purple-600',
                'title_ar' => 'أنظمة إدارة محتوى',
                'title_en' => 'CMS Integration',
                'description_ar' => 'سهولة إدارة المحتوى بدون خبرة برمجية',
                'description_en' => 'Easy content management without programming experience',
                'preview_image' => '/services/main-image.png',
                'order' => 4,
            ],
        ];

        foreach ($features as $feature) {
            ServicePageSolutionFeature::updateOrCreate(array_merge($feature, ['solution_section_id' => $solutionSection->id]));
        }

        // Gallery Images
        $galleryImages = [
            ['path' => '/services/service-1.png', 'alt_ar' => 'واجهة موقع حديث', 'alt_en' => 'Modern Website Interface', 'order' => 0],
            ['path' => '/services/service-2.png', 'alt_ar' => 'تصميم موقع متجاوب', 'alt_en' => 'Responsive Website Design', 'order' => 1],
            ['path' => '/services/service-3.png', 'alt_ar' => 'لوحة تحكم الموقع', 'alt_en' => 'Website Dashboard', 'order' => 2],
        ];

        foreach ($galleryImages as $image) {
            ServicePageGalleryImage::updateOrCreate(array_merge($image, ['service_page_id' => $servicePage->id]));
        }

        // Stats
        $stats = [
            ['number' => '1000+', 'label_ar' => 'موقع تم تطويره', 'label_en' => 'Websites Developed', 'order' => 0],
            ['number' => '99.9%', 'label_ar' => 'وقت تشغيل', 'label_en' => 'Uptime', 'order' => 1],
            ['number' => '24/7', 'label_ar' => 'دعم فني', 'label_en' => 'Technical Support', 'order' => 2],
            ['number' => '50%', 'label_ar' => 'توفير في التكلفة', 'label_en' => 'Cost Saving', 'order' => 3],
        ];

        foreach ($stats as $stat) {
            ServicePageStat::updateOrCreate(array_merge($stat, ['service_page_id' => $servicePage->id]));
        }

        // Testimonials
        $testimonials = [
            [
                'name_ar' => 'فهد العنزي',
                'name_en' => 'Fahad Al-Anzi',
                'text_ar' => 'الموقع الذي طوروه لشركتنا زاد من مبيعاتنا عبر الإنترنت بنسبة 300%',
                'text_en' => 'The website they developed for our company increased our online sales by 300%',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 0,
            ],
            [
                'name_ar' => 'أمل الحربي',
                'name_en' => 'Amal Al-Harbi',
                'text_ar' => 'فريق محترف يقدم حلولاً تقنية متكاملة مع متابعة ممتازة',
                'text_en' => 'Professional team that provides integrated technical solutions with excellent follow-up',
                'rating' => 5,
                'avatar' => 'avatars/user2.jpg',
                'order' => 1,
            ],
            [
                'name_ar' => 'تركي المطيري',
                'name_en' => 'Turki Al-Mutairi',
                'text_ar' => 'سرعة التنفيذ وجودة العمل تفوق التوقعات، أنصح بالتعامل معهم',
                'text_en' => 'The speed of execution and quality of work exceed expectations, I recommend dealing with them',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 2,
            ],
            [
                'name_ar' => 'لطيفة السبيعي',
                'name_en' => 'Latifa Al-Subaie',
                'text_ar' => 'لوحة التحكم سهلة الاستخدام حتى للمبتدئين في التعامل مع المواقع',
                'text_en' => 'The control panel is easy to use even for beginners in dealing with websites',
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
            'testimonial_title_ar' => 'ثقة عملائنا',
            'testimonial_title_en' => 'Our Clients Trust',
            'cta_title_ar' => 'طور موقعك الإلكتروني باحترافية',
            'cta_title_en' => 'Develop Your Website Professionally',
            'cta_subtitle_ar' => 'احصل على موقع إلكتروني يمثل عملك بأفضل صورة',
            'cta_subtitle_en' => 'Get a website that represents your business in the best way',
            'cta_button1_ar' => 'اطلب عرض سعر',
            'cta_button1_en' => 'Request a Quote',
            'cta_button2_ar' => 'استشارة مجانية',
            'cta_button2_en' => 'Free Consultation',
        ]);

        // ========== SERVICE TRACKING SAMPLE DATA ==========

        // Get first user and organization if they exist, otherwise create tracking with ID 1
        $user = User::first();
        $organization = Organization::first();

        // Sample service trackings for users
        if ($user) {
            // Pending tracking - Personal blog
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Personal blog website with custom design',
                    'pages' => 10,
                    'features' => ['blog', 'contact', 'portfolio'],
                ],
            ]);

            // In-progress tracking - E-commerce store
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(20),
                'metadata' => [
                    'notes' => 'E-commerce website for fashion products',
                    'products' => 150,
                    'progress' => '70%',
                    'technologies' => ['laravel', 'vuejs', 'mysql'],
                ],
            ]);

            // Completed tracking - Corporate website
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(60),
                'end_time' => now()->subDays(15),
                'metadata' => [
                    'notes' => 'Corporate website for consulting company',
                    'traffic_increase' => '250%',
                    'conversion_rate' => '8%',
                    'maintenance' => 'monthly',
                ],
            ]);
        }

        // Sample service trackings for organizations
        if ($organization) {
            // In-progress tracking for organization - Enterprise portal
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(40),
                'metadata' => [
                    'notes' => 'Enterprise employee portal with multiple modules',
                    'users' => 500,
                    'modules' => ['hr', 'finance', 'projects', 'reports'],
                    'budget' => 120000,
                ],
            ]);

            // Pending tracking for organization - Educational platform
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Online learning platform with video courses',
                    'courses' => 100,
                    'students' => 5000,
                    'features' => ['video_streaming', 'quizzes', 'certificates'],
                ],
            ]);

            // Completed tracking for organization - Government portal
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(180),
                'end_time' => now()->subDays(45),
                'metadata' => [
                    'notes' => 'Government services portal with high security standards',
                    'daily_visitors' => 10000,
                    'services_count' => 50,
                    'satisfaction_rate' => '95%',
                ],
            ]);
        }

        // ========== SERVICE PAGE CONTACT MESSAGES ==========
        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '+966500000005',
            'message' => 'I am interested in this service.',
            'status' => 'processing',
        ]);

        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '+966500000006',
            'message' => 'Please contact me for more details.',
            'status' => 'completed',
        ]);
    }
}
