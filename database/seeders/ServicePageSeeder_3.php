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
use Illuminate\Database\Seeder;

class ServicePageSeeder_3 extends Seeder
{
    public function run(): void
    {
        // Create the main service page
        $servicePage = ServicePage::updateOrCreate([
            'slug' => 'ecommerce-solutions',
            'is_active' => true,
            'price' => 2999.99,
            'price_before_discount' => 4499.99,
            'type' => 'subscription',
            'status' => 'active',
            'order' => 3,
            'category_id' => 3,
            'whatsapp_number' => '+966500000003',
        ]);

        // Hero Section
        ServicePageHeroSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'badge_ar' => 'منصة التجارة الإلكترونية الشاملة',
            'badge_en' => 'Complete E-commerce Platform',
            'title_ar' => 'متجرك الإلكتروني',
            'title_en' => 'Your Online Store',
            'subtitle_ar' => 'بين يديك',
            'subtitle_en' => 'At Your Fingertips',
            'description_ar' => 'أنشئ متجرك الإلكتروني المتكامل بأحدث التقنيات وأفضل الحلول التسويقية',
            'description_en' => 'Build your integrated online store with the latest technologies and best marketing solutions',
            'hero_image' => 'services/nfc-hero.png',
        ]);

        // Problem Section
        $problemSection = ServicePageProblemSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'معوقات البيع عبر الإنترنت',
            'title_en' => 'Online Selling Obstacles',
            'subtitle_ar' => 'تحديات تواجه المتاجر التقليدية',
            'subtitle_en' => 'Challenges facing traditional stores',
        ]);

        // Problem Items
        $problemItems = [
            [
                'icon' => 'star',
                'title_ar' => 'منصات معقدة',
                'title_en' => 'Complex Platforms',
                'description_ar' => 'صعوبة في إدارة المتجر والمنتجات',
                'description_en' => 'Difficulty in managing store and products',
                'order' => 0,
            ],
            [
                'icon' => 'book',
                'title_ar' => 'تكاليف خفية',
                'title_en' => 'Hidden Costs',
                'description_ar' => 'رسوم إضافية غير متوقعة تزيد التكلفة',
                'description_en' => 'Unexpected additional fees increase costs',
                'order' => 1,
            ],
            [
                'icon' => 'dollar',
                'title_ar' => 'ضعف المبيعات',
                'title_en' => 'Poor Sales',
                'description_ar' => 'تصميم غير محفز للشراء وغياب الاستراتيجيات التسويقية',
                'description_en' => 'Non-stimulating design for purchases and absence of marketing strategies',
                'order' => 2,
            ],
        ];

        foreach ($problemItems as $item) {
            ServicePageProblemItem::updateOrCreate(array_merge($item, ['problem_section_id' => $problemSection->id]));
        }

        // Solution Section
        $solutionSection = ServicePageSolutionSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'حلولنا المتكاملة',
            'title_en' => 'Our Integrated Solutions',
            'subtitle_ar' => 'كل ما تحتاجه لنجاح متجرك الإلكتروني',
            'subtitle_en' => 'Everything you need for your online store success',
            'cta_text_ar' => 'أنشئ متجرك الآن',
            'cta_text_en' => 'Create Your Store Now',
        ]);

        // Solution Features
        $features = [
            [
                'feature_key' => 'responsive-design',
                'icon' => 'FaDesktop',
                'color' => 'bg-blue-600',
                'title_ar' => 'تصميم متجاوب',
                'title_en' => 'Responsive Design',
                'description_ar' => 'يعمل على جميع الأجهزة والشاشات',
                'description_en' => 'Works on all devices and screens',
                'preview_image' => 'services/main-image.png',
                'order' => 0,
            ],
            [
                'feature_key' => 'payment-gateways',
                'icon' => 'FaCreditCard',
                'color' => 'bg-green-600',
                'title_ar' => 'بوابات دفع متعددة',
                'title_en' => 'Multiple Payment Gateways',
                'description_ar' => 'مدى، فيزا، أبل باي، وغيرها',
                'description_en' => 'Mada, Visa, Apple Pay, and others',
                'preview_image' => 'services/service-1.png',
                'order' => 1,
            ],
            [
                'feature_key' => 'inventory-management',
                'icon' => 'FaWarehouse',
                'color' => 'bg-orange-500',
                'title_ar' => 'إدارة مخزون ذكية',
                'title_en' => 'Smart Inventory Management',
                'description_ar' => 'تتبع المخزون تلقائياً وإشعارات النفاد',
                'description_en' => 'Automatic inventory tracking and low stock alerts',
                'preview_image' => 'services/service-2.png',
                'order' => 2,
            ],
            [
                'feature_key' => 'seo-optimized',
                'icon' => 'FaSearch',
                'color' => 'bg-purple-600',
                'title_ar' => 'تحسين محركات البحث',
                'title_en' => 'SEO Optimized',
                'description_ar' => 'تصدر نتائج البحث وجذب عملاء جدد',
                'description_en' => 'Rank high in search results and attract new customers',
                'preview_image' => 'services/service-3.png',
                'order' => 3,
            ],
        ];

        foreach ($features as $feature) {
            ServicePageSolutionFeature::updateOrCreate(array_merge($feature, ['solution_section_id' => $solutionSection->id]));
        }

        // Gallery Images
        $galleryImages = [
            ['path' => 'services/service-1.png', 'alt_ar' => 'واجهة متجر إلكتروني', 'alt_en' => 'E-commerce Store Interface', 'order' => 0],
            ['path' => 'services/service-2.png', 'alt_ar' => 'صفحة المنتج', 'alt_en' => 'Product Page', 'order' => 1],
            ['path' => 'services/service-3.png', 'alt_ar' => 'تجربة التسوق', 'alt_en' => 'Shopping Experience', 'order' => 2],
        ];

        foreach ($galleryImages as $image) {
            ServicePageGalleryImage::updateOrCreate(array_merge($image, ['service_page_id' => $servicePage->id]));
        }

        // Stats
        $stats = [
            ['number' => '1000+', 'label_ar' => 'متجر ناجح', 'label_en' => 'Successful Stores', 'order' => 0],
            ['number' => '50%', 'label_ar' => 'زيادة في المبيعات', 'label_en' => 'Sales Increase', 'order' => 1],
            ['number' => '30 دقيقة', 'label_ar' => 'تشغيل فوري', 'label_en' => 'Instant Setup', 'order' => 2],
        ];

        foreach ($stats as $stat) {
            ServicePageStat::updateOrCreate(array_merge($stat, ['service_page_id' => $servicePage->id]));
        }

        // Testimonials
        $testimonials = [
            [
                'name_ar' => 'محمد العتيبي',
                'name_en' => 'Mohammed Al-Otaibi',
                'text_ar' => 'زادت مبيعات متجري بنسبة 300% بعد إنشاء المتجر الإلكتروني',
                'text_en' => 'My store sales increased by 300% after creating the online store',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 0,
            ],
            [
                'name_ar' => 'فاطمة الزهراني',
                'name_en' => 'Fatima Al-Zahrani',
                'text_ar' => 'منصة سهلة الاستخدام مع دعم فني ممتاز على مدار الساعة',
                'text_en' => 'Easy-to-use platform with excellent 24/7 technical support',
                'rating' => 5,
                'avatar' => 'avatars/user2.jpg',
                'order' => 1,
            ],
            [
                'name_ar' => 'عبدالله القصيبي',
                'name_en' => 'Abdullah Al-Qusaibi',
                'text_ar' => 'الحلول التسويقية المدمجة ساعدتني في الوصول لعملاء جدد',
                'text_en' => 'The integrated marketing solutions helped me reach new customers',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 2,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            ServicePageTestimonial::updateOrCreate(array_merge($testimonial, ['service_page_id' => $servicePage->id]));
        }

        // CTA Section
        ServicePageCtaSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'testimonial_title_ar' => 'قصص نجاح عملائنا',
            'testimonial_title_en' => 'Our Clients Success Stories',
            'cta_title_ar' => 'ابدأ رحلتك في عالم التجارة الإلكترونية',
            'cta_title_en' => 'Start Your E-commerce Journey',
            'cta_subtitle_ar' => 'انضم إلى آلاف التجار الناجحين',
            'cta_subtitle_en' => 'Join thousands of successful merchants',
            'cta_button1_ar' => 'احصل على عرض خاص',
            'cta_button1_en' => 'Get Special Offer',
            'cta_button2_ar' => 'استشارة مجانية',
            'cta_button2_en' => 'Free Consultation',
        ]);

        // ========== SERVICE TRACKING SAMPLE DATA ==========

        // Get first user and organization if they exist, otherwise create tracking with ID 1
        $user = User::first();
        $organization = Organization::first();

        // Sample service trackings for users
        if ($user) {
            // Pending tracking - Basic store
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Customer wants basic fashion store',
                    'products_count' => 50,
                    'budget' => 3500,
                ],
            ]);

            // In-progress tracking - Electronics store
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(7),
                'metadata' => [
                    'notes' => 'Electronics store with 200+ products',
                    'progress' => '40%',
                    'features' => ['multi-vendor', 'arabic_support', 'loyalty_program'],
                ],
            ]);

            // Completed tracking - Grocery store
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(45),
                'end_time' => now()->subDays(10),
                'metadata' => [
                    'notes' => 'Online grocery store with delivery system',
                    'monthly_sales' => 50000,
                    'active_users' => 1200,
                ],
            ]);
        }

        // Sample service trackings for organizations
        if ($organization) {
            // In-progress tracking for organization - B2B wholesale
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(20),
                'metadata' => [
                    'notes' => 'B2B wholesale platform for industrial supplies',
                    'target_companies' => 500,
                    'integration' => ['erp', 'crm', 'accounting'],
                ],
            ]);

            // Pending tracking for organization - International store
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Multi-language store for international market',
                    'languages' => ['ar', 'en', 'fr'],
                    'currencies' => ['SAR', 'USD', 'EUR'],
                    'budget' => 25000,
                ],
            ]);

            // Completed tracking for organization - Luxury brand
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(120),
                'end_time' => now()->subDays(30),
                'metadata' => [
                    'notes' => 'Luxury fashion brand e-commerce platform',
                    'quarterly_revenue' => 1500000,
                    'social_media_integration' => true,
                ],
            ]);
        }

        // ========== SERVICE PAGE CONTACT MESSAGES ==========
        \App\Models\ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '+966500000003',
            'message' => 'I am interested in this service.',
            'status' => 'processing',
        ]);

        \App\Models\ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '+966500000004',
            'message' => 'Please contact me for more details.',
            'status' => 'completed',
        ]);
    }
}
