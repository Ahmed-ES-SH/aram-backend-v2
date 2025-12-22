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

class ServicePageSeeder_4 extends Seeder
{
    public function run(): void
    {
        // Create the main service page
        $servicePage = ServicePage::updateOrCreate([
            'slug' => 'digital-marketing',
            'is_active' => true,
            'price' => 1999.99,
            'price_before_discount' => 2999.99,
            'type' => 'one_time',
            'status' => 'active',
            'order' => 4,
            'category_id' => 4,
            'whatsapp_number' => '+966500000004',
        ]);

        // Hero Section
        ServicePageHeroSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'badge_ar' => 'الحل التسويقي المتكامل',
            'badge_en' => 'Integrated Marketing Solution',
            'title_ar' => 'التسويق الرقمي',
            'title_en' => 'Digital Marketing',
            'subtitle_ar' => 'يصل عملك للعالم',
            'subtitle_en' => 'Takes Your Business Global',
            'description_ar' => 'حلول تسويقية مبتكرة تزيد من وصولك للعملاء وتحقق أعلى العوائد على الاستثمار',
            'description_en' => 'Innovative marketing solutions that increase your reach to customers and achieve the highest ROI',
            'hero_image' => '/services/service-man.png',
        ]);

        // Problem Section
        $problemSection = ServicePageProblemSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'تحديات التسويق التقليدي',
            'title_en' => 'Traditional Marketing Challenges',
            'subtitle_ar' => 'لماذا تفشل الحملات التسويقية؟',
            'subtitle_en' => 'Why Do Marketing Campaigns Fail?',
        ]);

        // Problem Items
        $problemItems = [
            [
                'icon' => 'star',
                'title_ar' => 'تكاليف عالية',
                'title_en' => 'High Costs',
                'description_ar' => 'الإعلانات التقليدية مكلفة ولا تضمن وصولاً دقيقاً',
                'description_en' => 'Traditional ads are expensive and don\'t guarantee accurate reach',
                'order' => 0,
            ],
            [
                'icon' => 'book',
                'title_ar' => 'صعوبة القياس',
                'title_en' => 'Difficulty in Measurement',
                'description_ar' => 'عدم القدرة على تتبع النتائج بدقة',
                'description_en' => 'Inability to accurately track results',
                'order' => 1,
            ],
            [
                'icon' => 'dollar',
                'title_ar' => 'ضعف التفاعل',
                'title_en' => 'Weak Engagement',
                'description_ar' => 'الجمهور لا يتفاعل مع الحملات التقليدية',
                'description_en' => 'Audience doesn\'t engage with traditional campaigns',
                'order' => 2,
            ],
        ];

        foreach ($problemItems as $item) {
            ServicePageProblemItem::updateOrCreate(array_merge($item, ['problem_section_id' => $problemSection->id]));
        }

        // Solution Section
        $solutionSection = ServicePageSolutionSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'حلولنا الرقمية',
            'title_en' => 'Our Digital Solutions',
            'subtitle_ar' => 'استراتيجيات تسويقية ذكية تحقق النتائج',
            'subtitle_en' => 'Smart Marketing Strategies That Deliver Results',
            'cta_text_ar' => 'ابدأ حملتك الآن',
            'cta_text_en' => 'Start Your Campaign Now',
        ]);

        // Solution Features
        $features = [
            [
                'feature_key' => 'seo-services',
                'icon' => 'FaSearch',
                'color' => 'bg-blue-600',
                'title_ar' => 'تحسين محركات البحث',
                'title_en' => 'SEO Services',
                'description_ar' => 'تصدر نتائج البحث الأولى وزيادة الزيارات العضوية',
                'description_en' => 'Rank first in search results and increase organic traffic',
                'preview_image' => '/services/main-image.png',
                'order' => 0,
            ],
            [
                'feature_key' => 'social-media',
                'icon' => 'FaHashtag',
                'color' => 'bg-pink-500',
                'title_ar' => 'إدارة وسائل التواصل',
                'title_en' => 'Social Media Management',
                'description_ar' => 'إدارة متكاملة لحساباتك على جميع المنصات',
                'description_en' => 'Integrated management of your accounts on all platforms',
                'preview_image' => '/services/service-1.png',
                'order' => 1,
            ],
            [
                'feature_key' => 'content-marketing',
                'icon' => 'FaEdit',
                'color' => 'bg-green-500',
                'title_ar' => 'التسويق بالمحتوى',
                'title_en' => 'Content Marketing',
                'description_ar' => 'إنشاء محتوى جذاب يحول الزوار إلى عملاء',
                'description_en' => 'Creating engaging content that converts visitors into customers',
                'preview_image' => '/services/service-2.png',
                'order' => 2,
            ],
            [
                'feature_key' => 'google-ads',
                'icon' => 'FaChartLine',
                'color' => 'bg-yellow-500',
                'title_ar' => 'إعلانات جوجل',
                'title_en' => 'Google Ads',
                'description_ar' => 'حملات إعلانية مستهدفة تحقق أعلى عائد استثمار',
                'description_en' => 'Targeted ad campaigns that achieve the highest ROI',
                'preview_image' => '/services/service-3.png',
                'order' => 3,
            ],
        ];

        foreach ($features as $feature) {
            ServicePageSolutionFeature::updateOrCreate(array_merge($feature, ['solution_section_id' => $solutionSection->id]));
        }

        // Gallery Images
        $galleryImages = [
            ['path' => '/services/service-1.png', 'alt_ar' => 'تحليلات التسويق الرقمي', 'alt_en' => 'Digital Marketing Analytics', 'order' => 0],
            ['path' => '/services/service-2.png', 'alt_ar' => 'حملة إعلانية ناجحة', 'alt_en' => 'Successful Ad Campaign', 'order' => 1],
            ['path' => '/services/service-3.png', 'alt_ar' => 'إدارة وسائل التواصل', 'alt_en' => 'Social Media Management', 'order' => 2],
        ];

        foreach ($galleryImages as $image) {
            ServicePageGalleryImage::updateOrCreate(array_merge($image, ['service_page_id' => $servicePage->id]));
        }

        // Stats
        $stats = [
            ['number' => '500+', 'label_ar' => 'حملة ناجحة', 'label_en' => 'Successful Campaigns', 'order' => 0],
            ['number' => '300%', 'label_ar' => 'زيادة في المبيعات', 'label_en' => 'Sales Increase', 'order' => 1],
            ['number' => '24/7', 'label_ar' => 'تحليل وتقارير', 'label_en' => 'Analysis & Reports', 'order' => 2],
        ];

        foreach ($stats as $stat) {
            ServicePageStat::updateOrCreate(array_merge($stat, ['service_page_id' => $servicePage->id]));
        }

        // Testimonials
        $testimonials = [
            [
                'name_ar' => 'وليد الشمري',
                'name_en' => 'Waleed Al-Shammari',
                'text_ar' => 'حملاتهم التسويقية زادت مبيعات متجري الإلكتروني بنسبة 400% في 3 أشهر',
                'text_en' => 'Their marketing campaigns increased my online store sales by 400% in 3 months',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 0,
            ],
            [
                'name_ar' => 'هناء الرشيد',
                'name_en' => 'Hana Al-Rashid',
                'text_ar' => 'فريق محترف يقدم تقارير مفصلة وتحليلات دقيقة تساعد في اتخاذ القرارات',
                'text_en' => 'Professional team that provides detailed reports and accurate analytics that help in decision making',
                'rating' => 5,
                'avatar' => 'avatars/user2.jpg',
                'order' => 1,
            ],
            [
                'name_ar' => 'سعود الفهد',
                'name_en' => 'Saud Al-Fahad',
                'text_ar' => 'استراتيجيتهم في التسويق بالمحتوى حولت مدونتنا إلى مصدر رئيسي للعملاء',
                'text_en' => 'Their content marketing strategy turned our blog into a major source of customers',
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
            'testimonial_title_ar' => 'نجاحات عملائنا تتحدث',
            'testimonial_title_en' => 'Our Clients Success Speaks',
            'cta_title_ar' => 'ارتقِ بتسويق عملك إلى مستوى جديد',
            'cta_title_en' => 'Elevate Your Business Marketing to a New Level',
            'cta_subtitle_ar' => 'احصل على خطة تسويقية مخصصة لعملك',
            'cta_subtitle_en' => 'Get a customized marketing plan for your business',
            'cta_button1_ar' => 'اطلب خطة تسويقية',
            'cta_button1_en' => 'Request Marketing Plan',
            'cta_button2_ar' => 'استشارة مجانية',
            'cta_button2_en' => 'Free Consultation',
        ]);

        // ========== SERVICE TRACKING SAMPLE DATA ==========

        // Get first user and organization if they exist, otherwise create tracking with ID 1
        $user = User::first();
        $organization = Organization::first();

        // Sample service trackings for users
        if ($user) {
            // Pending tracking - SEO campaign
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Client needs SEO for new website',
                    'keywords' => 50,
                    'competitors' => 5,
                ],
            ]);

            // In-progress tracking - Social media campaign
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(15),
                'metadata' => [
                    'notes' => 'Instagram & TikTok campaign for fashion brand',
                    'platforms' => ['instagram', 'tiktok'],
                    'budget' => 5000,
                    'progress' => '65%',
                ],
            ]);

            // Completed tracking - Google Ads campaign
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(90),
                'end_time' => now()->subDays(30),
                'metadata' => [
                    'notes' => 'Google Ads campaign for real estate company',
                    'roi' => '350%',
                    'leads_generated' => 450,
                    'conversion_rate' => '12%',
                ],
            ]);
        }

        // Sample service trackings for organizations
        if ($organization) {
            // In-progress tracking for organization - Full digital transformation
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(45),
                'metadata' => [
                    'notes' => 'Complete digital marketing overhaul for manufacturing company',
                    'services' => ['seo', 'social_media', 'content', 'email_marketing'],
                    'team_size' => 5,
                    'budget' => 75000,
                ],
            ]);

            // Pending tracking for organization - Product launch campaign
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'New product launch campaign across all digital channels',
                    'product' => 'Smart Home Device',
                    'target_markets' => ['KSA', 'UAE', 'Qatar'],
                    'budget' => 100000,
                ],
            ]);

            // Completed tracking for organization - Brand awareness campaign
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(180),
                'end_time' => now()->subDays(60),
                'metadata' => [
                    'notes' => '6-month brand awareness campaign for tech startup',
                    'brand_mentions' => 25000,
                    'social_reach' => '2.5M',
                    'website_traffic_increase' => '180%',
                ],
            ]);
        }

        // ========== SERVICE PAGE CONTACT MESSAGES ==========
        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '+966500000004',
            'message' => 'I am interested in this service.',
            'status' => 'processing',
        ]);

        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '+966500000005',
            'message' => 'Please contact me for more details.',
            'status' => 'completed',
        ]);
    }
}
