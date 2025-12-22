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

class ServicePageSeeder_2 extends Seeder
{
    public function run(): void
    {
        // Create the main service page
        $servicePage = ServicePage::updateOrCreate([
            'slug' => 'mobile-app-development',
            'is_active' => true,
            'price' => 4999.99,
            'price_before_discount' => 6999.99,
            'type' => 'one_time',
            'status' => 'active',
            'order' => 2,
            'category_id' => 2,
            'whatsapp_number' => '+966500000002',
        ]);

        // Hero Section
        ServicePageHeroSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'badge_ar' => 'الحل الأمثل للرقمنة',
            'badge_en' => 'Optimal Digitization Solution',
            'title_ar' => 'تطبيقات جوال متميزة',
            'title_en' => 'Premium Mobile Applications',
            'subtitle_ar' => 'تغيير تجربة العملاء',
            'subtitle_en' => 'Transforming Customer Experience',
            'description_ar' => 'نطور تطبيقات جوال مبتكرة تلبي احتياجات عملك وتواكب التطور التكنولوجي',
            'description_en' => 'We develop innovative mobile applications that meet your business needs and keep pace with technological development',
            'hero_image' => '/services/service-man.png',
        ]);

        // Problem Section
        $problemSection = ServicePageProblemSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'التحديات التي نواجهها',
            'title_en' => 'Challenges We Address',
            'subtitle_ar' => 'التطبيقات التقليدية تواجه العديد من المشاكل',
            'subtitle_en' => 'Traditional applications face many problems',
        ]);

        // Problem Items
        $problemItems = [
            [
                'icon' => 'star',
                'title_ar' => 'بطء الأداء',
                'title_en' => 'Poor Performance',
                'description_ar' => 'تطبيقات بطيئة تؤثر على تجربة المستخدم',
                'description_en' => 'Slow applications that affect user experience',
                'order' => 0,
            ],
            [
                'icon' => 'book',
                'title_ar' => 'تصميم غير جذاب',
                'title_en' => 'Unattractive Design',
                'description_ar' => 'واجهات مستخدم قديمة لا تجذب العملاء',
                'description_en' => 'Outdated user interfaces that don\'t attract customers',
                'order' => 1,
            ],
            [
                'icon' => 'dollar',
                'title_ar' => 'تكاليف صيانة عالية',
                'title_en' => 'High Maintenance Costs',
                'description_ar' => 'تكاليف صيانة وتحديث مستمرة',
                'description_en' => 'Continuous maintenance and update costs',
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
            'subtitle_ar' => 'تطبيقات ذكية تلبي كل متطلباتك',
            'subtitle_en' => 'Smart applications that meet all your requirements',
            'cta_text_ar' => 'ابدأ مشروعك الآن',
            'cta_text_en' => 'Start Your Project Now',
        ]);

        // Solution Features
        $features = [
            [
                'feature_key' => 'ios-apps',
                'icon' => 'FaApple',
                'color' => 'bg-gray-800',
                'title_ar' => 'تطبيقات iOS',
                'title_en' => 'iOS Applications',
                'description_ar' => 'تطبيقات متوافقة مع أجهزة آبل بجودة عالية',
                'description_en' => 'High-quality applications compatible with Apple devices',
                'preview_image' => '/services/main-image.png',
                'order' => 0,
            ],
            [
                'feature_key' => 'android-apps',
                'icon' => 'FaAndroid',
                'color' => 'bg-green-500',
                'title_ar' => 'تطبيقات أندرويد',
                'title_en' => 'Android Applications',
                'description_ar' => 'حلول متكاملة لمنصة أندرويد',
                'description_en' => 'Integrated solutions for Android platform',
                'preview_image' => '/services/service-1.png',
                'order' => 1,
            ],
            [
                'feature_key' => 'cross-platform',
                'icon' => 'FaMobile',
                'color' => 'bg-blue-500',
                'title_ar' => 'تطبيقات متعددة المنصات',
                'title_en' => 'Cross-Platform Applications',
                'description_ar' => 'حل واحد يعمل على جميع المنصات',
                'description_en' => 'One solution works on all platforms',
                'preview_image' => '/services/service-2.png',
                'order' => 2,
            ],
            [
                'feature_key' => 'ui-ux',
                'icon' => 'FaPalette',
                'color' => 'bg-purple-500',
                'title_ar' => 'تصميم واجهات متقدمة',
                'title_en' => 'Advanced UI/UX Design',
                'description_ar' => 'تصاميم جذابة وسهلة الاستخدام',
                'description_en' => 'Attractive and user-friendly designs',
                'preview_image' => '/services/service-3.png',
                'order' => 3,
            ],
        ];

        foreach ($features as $feature) {
            ServicePageSolutionFeature::updateOrCreate(array_merge($feature, ['solution_section_id' => $solutionSection->id]));
        }

        // Gallery Images
        $galleryImages = [
            ['path' => '/services/service-1.png', 'alt_ar' => 'واجهة تطبيق جوال', 'alt_en' => 'Mobile App Interface', 'order' => 0],
            ['path' => '/services/service-2.png', 'alt_ar' => 'تصميم تطبيق', 'alt_en' => 'App Design', 'order' => 1],
            ['path' => '/services/service-3.png', 'alt_ar' => 'تطبيق في العمل', 'alt_en' => 'App in Action', 'order' => 2],
        ];

        foreach ($galleryImages as $image) {
            ServicePageGalleryImage::updateOrCreate(array_merge($image, ['service_page_id' => $servicePage->id]));
        }

        // Stats
        $stats = [
            ['number' => '500+', 'label_ar' => 'تطبيق تم تطويره', 'label_en' => 'Apps Developed', 'order' => 0],
            ['number' => '98%', 'label_ar' => 'رضا العملاء', 'label_en' => 'Client Satisfaction', 'order' => 1],
            ['number' => '24/7', 'label_ar' => 'دعم فني', 'label_en' => 'Technical Support', 'order' => 2],
        ];

        foreach ($stats as $stat) {
            ServicePageStat::updateOrCreate(array_merge($stat, ['service_page_id' => $servicePage->id]));
        }

        // Testimonials
        $testimonials = [
            [
                'name_ar' => 'خالد السعيد',
                'name_en' => 'Khaled Al-Saeed',
                'text_ar' => 'التطبيق الذي طوروه لشركتنا زاد من مبيعاتنا بنسبة 40%',
                'text_en' => 'The app they developed for our company increased our sales by 40%',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 0,
            ],
            [
                'name_ar' => 'نورة القحطاني',
                'name_en' => 'Nora Al-Qahtani',
                'text_ar' => 'فريق محترف ونتائج تتجاوز التوقعات',
                'text_en' => 'Professional team and results that exceed expectations',
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
            'testimonial_title_ar' => 'تجارب عملائنا',
            'testimonial_title_en' => 'Our Clients Experiences',
            'cta_title_ar' => 'حوّل فكرتك إلى تطبيق واقعي',
            'cta_title_en' => 'Turn Your Idea into a Real Application',
            'cta_subtitle_ar' => 'نحن هنا لتحقيق رؤيتك الرقمية',
            'cta_subtitle_en' => 'We are here to realize your digital vision',
            'cta_button1_ar' => 'احصل على استشارة مجانية',
            'cta_button1_en' => 'Get Free Consultation',
            'cta_button2_ar' => 'اطلب عرض سعر',
            'cta_button2_en' => 'Request a Quote',
        ]);

        // ========== SERVICE TRACKING SAMPLE DATA ==========

        // Get first user and organization if they exist, otherwise create tracking with ID 1
        $user = User::first();
        $organization = Organization::first();

        // Sample service trackings for users
        if ($user) {
            // Pending tracking
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Client needs e-commerce mobile app',
                    'platform' => 'both',
                    'priority' => 'high',
                ],
            ]);

            // In-progress tracking
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(15),
                'metadata' => [
                    'notes' => 'UI/UX design approved, starting development phase',
                    'progress' => '30%',
                    'deadline' => now()->addDays(45)->toDateString(),
                ],
            ]);

            // Completed tracking
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(60),
                'end_time' => now()->subDays(5),
                'metadata' => [
                    'notes' => 'App successfully published on App Store and Play Store',
                    'rating' => '5 stars',
                    'downloads' => '5000+',
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
                'start_time' => now()->subDays(30),
                'metadata' => [
                    'notes' => 'Enterprise app for internal use - testing phase',
                    'users_count' => 250,
                    'modules' => ['attendance', 'tasks', 'reports'],
                ],
            ]);

            // Pending tracking for organization
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'Banking app with high security requirements',
                    'priority' => 'urgent',
                    'budget' => 75000,
                ],
            ]);
        }

        // ========== SERVICE PAGE CONTACT MESSAGES ==========
        \App\Models\ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '+966500000002',
            'message' => 'I am interested in this service.',
            'status' => 'completed',
        ]);

        \App\Models\ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '+966500000003',
            'message' => 'Please contact me for more details.',
            'status' => 'pending',
        ]);
    }
}
