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

class ServicePageSeeder_8 extends Seeder
{
    public function run(): void
    {
        // Create the main service page
        $servicePage = ServicePage::updateOrCreate([
            'slug' => 'video-production',
            'is_active' => true,
            'price' => 2999.99,
            'price_before_discount' => 3999.99,
            'type' => 'subscription',
            'status' => 'active',
            'order' => 7,
            'category_id' => 7,
            'whatsapp_number' => '+966500000008',
        ]);

        // Hero Section
        ServicePageHeroSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'badge_ar' => 'صناعة المحتوى المرئي الاحترافي',
            'badge_en' => 'Professional Video Content Production',
            'title_ar' => 'إنتاج وتحرير الفيديو',
            'title_en' => 'Video Production & Editing',
            'subtitle_ar' => 'قصتك بجودة عالية',
            'subtitle_en' => 'Your Story in High Quality',
            'description_ar' => 'ننتج محتوى فيديو احترافي يحكي قصتك ويوصل رسالتك بأعلى جودة فنية وإبداعية',
            'description_en' => 'We produce professional video content that tells your story and delivers your message with the highest technical and creative quality',
            'hero_image' => '/services/service-man.png',
        ]);

        // Problem Section
        $problemSection = ServicePageProblemSection::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'title_ar' => 'تحديات صناعة الفيديو',
            'title_en' => 'Video Production Challenges',
            'subtitle_ar' => 'لماذا لا تنجح الكثير من مقاطع الفيديو؟',
            'subtitle_en' => 'Why Do Many Videos Fail?',
        ]);

        // Problem Items
        $problemItems = [
            [
                'icon' => 'FaVideoSlash',
                'title_ar' => 'جودة ضعيفة',
                'title_en' => 'Poor Quality',
                'description_ar' => 'مقاطع فيديو بجودة منخفضة لا تجذب المشاهدين',
                'description_en' => 'Low quality videos that don\'t attract viewers',
                'order' => 0,
            ],
            [
                'icon' => 'FaUserClock',
                'title_ar' => 'وقت إنتاج طويل',
                'title_en' => 'Long Production Time',
                'description_ar' => 'فترات انتظار طويلة للحصول على المنتج النهائي',
                'description_en' => 'Long waiting periods to get the final product',
                'order' => 1,
            ],
            [
                'icon' => 'FaDollarSign',
                'title_ar' => 'تكاليف باهظة',
                'title_en' => 'High Costs',
                'description_ar' => 'أسعار مرتفعة مقابل جودة غير مضمونة',
                'description_en' => 'High prices for uncertain quality',
                'order' => 2,
            ],
            [
                'icon' => 'FaCreativeCommons',
                'title_ar' => 'ضعف الإبداع',
                'title_en' => 'Lack of Creativity',
                'description_ar' => 'محتوى مكرر لا يحمل أي عناصر إبداعية مميزة',
                'description_en' => 'Repetitive content with no distinctive creative elements',
                'order' => 3,
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
            'subtitle_ar' => 'خدمات فيديو متكاملة تغطي جميع احتياجاتك',
            'subtitle_en' => 'Integrated video services covering all your needs',
            'cta_text_ar' => 'ابدأ مشروع الفيديو الخاص بك',
            'cta_text_en' => 'Start Your Video Project',
        ]);

        // Solution Features
        $features = [
            [
                'feature_key' => 'corporate-videos',
                'icon' => 'FaBuilding',
                'color' => 'bg-blue-600',
                'title_ar' => 'فيديوهات مؤسسية',
                'title_en' => 'Corporate Videos',
                'description_ar' => 'فيديوهات تعرض شركتك وخدماتك بشكل احترافي',
                'description_en' => 'Videos that showcase your company and services professionally',
                'preview_image' => '/services/main-image.png',
                'order' => 0,
            ],
            [
                'feature_key' => 'social-media-videos',
                'icon' => 'FaInstagram',
                'color' => 'bg-pink-500',
                'title_ar' => 'فيديوهات وسائل التواصل',
                'title_en' => 'Social Media Videos',
                'description_ar' => 'محتوى فيديو مخصص لمنصات التواصل الاجتماعي',
                'description_en' => 'Custom video content for social media platforms',
                'preview_image' => '/services/service-1.png',
                'order' => 1,
            ],
            [
                'feature_key' => 'motion-graphics',
                'icon' => 'FaPlayCircle',
                'color' => 'bg-green-500',
                'title_ar' => 'الرسوم المتحركة',
                'title_en' => 'Motion Graphics',
                'description_ar' => 'رسوم متحركة إبداعية تناسب جميع أنواع المحتوى',
                'description_en' => 'Creative motion graphics suitable for all types of content',
                'preview_image' => '/services/service-2.png',
                'order' => 2,
            ],
            [
                'feature_key' => 'video-editing',
                'icon' => 'FaCut',
                'color' => 'bg-purple-600',
                'title_ar' => 'تعديل وتحرير الفيديو',
                'title_en' => 'Video Editing',
                'description_ar' => 'تحرير احترافي للمقاطع مع إضافة المؤثرات',
                'description_en' => 'Professional editing of clips with added effects',
                'preview_image' => '/services/service-3.png',
                'order' => 3,
            ],
            [
                'feature_key' => 'drone-videography',
                'icon' => 'FaDrone',
                'color' => 'bg-yellow-600',
                'title_ar' => 'التصوير الجوي بالدرون',
                'title_en' => 'Drone Videography',
                'description_ar' => 'لقطات جوية مذهلة تضيف بعداً جديداً لفيديوهاتك',
                'description_en' => 'Stunning aerial shots that add a new dimension to your videos',
                'preview_image' => '/services/main-image.png',
                'order' => 4,
            ],
            [
                'feature_key' => 'live-streaming',
                'icon' => 'FaBroadcastTower',
                'color' => 'bg-red-500',
                'title_ar' => 'البث المباشر',
                'title_en' => 'Live Streaming',
                'description_ar' => 'إنتاج وتنظيم فعاليات البث المباشر بجودة احترافية',
                'description_en' => 'Production and organization of live streaming events with professional quality',
                'preview_image' => '/services/service-1.png',
                'order' => 5,
            ],
        ];

        foreach ($features as $feature) {
            ServicePageSolutionFeature::updateOrCreate(array_merge($feature, ['solution_section_id' => $solutionSection->id]));
        }

        // Gallery Images
        $galleryImages = [
            ['path' => '/services/service-1.png', 'alt_ar' => 'تصوير فيديو احترافي', 'alt_en' => 'Professional Video Shooting', 'order' => 0],
            ['path' => '/services/service-2.png', 'alt_ar' => 'تعديل الفيديو', 'alt_en' => 'Video Editing', 'order' => 1],
            ['path' => '/services/service-3.png', 'alt_ar' => 'التصوير الجوي', 'alt_en' => 'Aerial Shooting', 'order' => 2],
            ['path' => '/services/main-image.png', 'alt_ar' => 'إنتاج فيديو متكامل', 'alt_en' => 'Complete Video Production', 'order' => 3],
        ];

        foreach ($galleryImages as $image) {
            ServicePageGalleryImage::updateOrCreate(array_merge($image, ['service_page_id' => $servicePage->id]));
        }

        // Stats
        $stats = [
            ['number' => '1500+', 'label_ar' => 'فيديو منتج', 'label_en' => 'Videos Produced', 'order' => 0],
            ['number' => '4K', 'label_ar' => 'جودة تصوير', 'label_en' => 'Shooting Quality', 'order' => 1],
            ['number' => '95%', 'label_ar' => 'رضا العملاء', 'label_en' => 'Client Satisfaction', 'order' => 2],
            ['number' => '48 ساعة', 'label_ar' => 'تسليم سريع', 'label_en' => 'Fast Delivery', 'order' => 3],
            ['number' => '10M+', 'label_ar' => 'مشاهدات', 'label_en' => 'Views', 'order' => 4],
        ];

        foreach ($stats as $stat) {
            ServicePageStat::updateOrCreate(array_merge($stat, ['service_page_id' => $servicePage->id]));
        }

        // Testimonials
        $testimonials = [
            [
                'name_ar' => 'سالم المري',
                'name_en' => 'Salem Al-Mary',
                'text_ar' => 'الفيديو الترويجي الذي أنتجوه لشركتنا حصد أكثر من مليون مشاهدة في أسبوع',
                'text_en' => 'The promotional video they produced for our company garnered over one million views in a week',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 0,
            ],
            [
                'name_ar' => 'لمى السديري',
                'name_en' => 'Lama Al-Sudairi',
                'text_ar' => 'فريق إبداعي يتمتع بخبرة تقنية عالية وسرعة في التسليم',
                'text_en' => 'A creative team with high technical expertise and fast delivery',
                'rating' => 5,
                'avatar' => 'avatars/user2.jpg',
                'order' => 1,
            ],
            [
                'name_ar' => 'نايف الحربي',
                'name_en' => 'Naif Al-Harbi',
                'text_ar' => 'الرسوم المتحركة التي صمموها لعلامتنا التجارية كانت مميزة جداً',
                'text_en' => 'The motion graphics they designed for our brand were very distinctive',
                'rating' => 5,
                'avatar' => 'avatars/user1.jpg',
                'order' => 2,
            ],
            [
                'name_ar' => 'أريج القحطاني',
                'name_en' => 'Areej Al-Qahtani',
                'text_ar' => 'نظموا لنا فعالية بث مباشر ناجحة حضرها أكثر من 5000 شخص',
                'text_en' => 'They organized a successful live streaming event attended by over 5,000 people',
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
            'testimonial_title_ar' => 'قصص نجاح عملائنا',
            'testimonial_title_en' => 'Our Clients Success Stories',
            'cta_title_ar' => 'حول فكرتك إلى فيديو مذهل',
            'cta_title_en' => 'Turn Your Idea into an Amazing Video',
            'cta_subtitle_ar' => 'فريقنا المتخصص جاهز لتحقيق رؤيتك الإبداعية',
            'cta_subtitle_en' => 'Our specialized team is ready to realize your creative vision',
            'cta_button1_ar' => 'اطلب فيديو الآن',
            'cta_button1_en' => 'Order Video Now',
            'cta_button2_ar' => 'استشارة مجانية',
            'cta_button2_en' => 'Free Consultation',
        ]);

        // ========== SERVICE TRACKING SAMPLE DATA ==========

        // Get first user and organization if they exist, otherwise create tracking with ID 1
        $user = User::first();
        $organization = Organization::first();

        // Sample service trackings for users
        if ($user) {
            // Pending tracking - YouTube intro
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'YouTube channel intro video',
                    'duration' => '30 seconds',
                    'style' => 'modern_animated',
                    'platform' => 'youtube',
                ],
            ]);

            // In-progress tracking - Product demo video
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(10),
                'metadata' => [
                    'notes' => 'Product demonstration video for new tech gadget',
                    'duration' => '3 minutes',
                    'shooting_days' => 2,
                    'progress' => '50%',
                    'locations' => ['studio', 'outdoor'],
                ],
            ]);

            // Completed tracking - Wedding highlights
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(45),
                'end_time' => now()->subDays(10),
                'metadata' => [
                    'notes' => 'Wedding highlights video with drone shots',
                    'duration' => '10 minutes',
                    'delivery_format' => ['4k_file', 'social_media_cuts'],
                    'satisfaction' => 'excellent',
                    'views' => 25000,
                ],
            ]);

            // On hold tracking - Documentary project
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $user->id,
                'user_type' => 'user',
                'status' => 'pending',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(30),
                'metadata' => [
                    'notes' => 'Short documentary about local heritage',
                    'hold_reason' => 'awaiting_permissions',
                    'estimated_resume' => now()->addDays(15)->toDateString(),
                    'work_completed' => '25%',
                ],
            ]);
        }

        // Sample service trackings for organizations
        if ($organization) {
            // In-progress tracking for organization - Corporate series
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'in_progress',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(25),
                'metadata' => [
                    'notes' => 'Series of corporate culture videos for internal training',
                    'episodes' => 10,
                    'episode_duration' => '5-7 minutes',
                    'progress' => '70%',
                    'team_size' => 5,
                    'budget' => 75000,
                ],
            ]);

            // Pending tracking for organization - TV commercial
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'pending',
                'current_phase' => 'planning',
                'metadata' => [
                    'notes' => 'TV commercial for national campaign',
                    'duration' => '60 seconds',
                    'channels' => ['mbc', 'rotana', 'dmc'],
                    'celebrities_involved' => true,
                    'budget' => 200000,
                ],
            ]);

            // Completed tracking for organization - Event coverage
            ServiceTracking::updateOrCreate([
                'service_id' => $servicePage->id,
                'user_id' => $organization->id,
                'user_type' => 'organization',
                'status' => 'completed',
                'current_phase' => 'planning',
                'start_time' => now()->subDays(90),
                'end_time' => now()->subDays(20),
                'metadata' => [
                    'notes' => 'Full coverage of annual conference with multi-camera setup',
                    'event_duration' => '3 days',
                    'cameras_used' => 6,
                    'deliverables' => ['highlight_video', 'full_sessions', 'interviews'],
                    'social_media_reach' => '2.5M',
                ],
            ]);
        }
        // ========== SERVICE PAGE CONTACT MESSAGES ==========
        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '+966500000008',
            'message' => 'I am interested in this service.',
            'status' => 'processing',
        ]);

        ServicePageContactMessage::updateOrCreate([
            'service_page_id' => $servicePage->id,
            'email' => 'contact2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '+966500000009',
            'message' => 'Please contact me for more details.',
            'status' => 'completed',
        ]);
    }
}
