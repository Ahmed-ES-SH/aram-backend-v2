<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\Organization;
use App\Models\OrganizationBenefit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('organizations')->truncate();
        DB::table('organization_benefits')->truncate();
        DB::table('organization_keywords')->truncate();
        DB::table('organization_categories')->truncate();
        DB::table('organization_sub_categories')->truncate();

        $categories = DB::table('categories')->pluck('id')->toArray();
        $subCategories = DB::table('sub_categories')->pluck('id')->toArray();
        $keywordIds = Keyword::pluck('id')->toArray();

        // استخدام صور عشوائية من Unsplash
        $unsplashImages = [
            'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1516574187841-cb9cc2ca948b?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1576097449790-4d4c1505d30d?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1585435557343-3b092031d5ad?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1551076805-e1869033e561?w=400&h=300&fit=crop',
            'https://images.unsplash.com/photo-1582750433449-648ed127bb54?w=400&h=300&fit=crop',
        ];

        $unsplashLogos = [
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1556655673-33c2d8ad598c?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1565688534245-05d6b5be184a?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1567446537710-90f13fc6d89c?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1565106430482-8f6e74349ca1?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1567446537711-0aa0f003345c?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1565687127020-99d5f0c5d0b4?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1565106430479-60b83c4cbca3?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1565687126999-7db7e54c2175?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1565106430409-3a2f4c3c3e3d?w=200&h=200&fit=crop',
        ];

        // البيانات الأساسية للمراكز كمصفوفات مباشرة (بدون json_encode)
        $baseOrganizations = [
            [
                'location' => [
                    'address' => 'Cairo, Egypt',
                    'coordinates' => [
                        'lat' => 30.0444,
                        'lng' => 31.2357
                    ]
                ],
                'accaptable_message' => 'Your booking request has been accepted.',
                'unaccaptable_message' => 'Sorry, your booking request was rejected.',
                'confirmation_price' => 100.00,
                'confirmation_status' => 'confirmed',
                'phone_number' => '+201234567890',
                'open_at' => '08:00:00',
                'close_at' => '18:00:00',
                'url' => 'https://example.com/cairo-center',
                'rateing' => 4.5,
                'status' => 'published',
                'booking_status' => 'available',
                'Number_of_reservations' => 120,
                'active' => true,
                'title' => 'Cairo Medical Center',
                'description' => 'A trusted medical center located in downtown Cairo.'
            ],
            [
                'location' => [
                    'address' => 'Giza, Egypt',
                    'coordinates' => [
                        'lat' => 29.9737,
                        'lng' => 31.2819
                    ]
                ],
                'accaptable_message' => 'Your booking is approved.',
                'unaccaptable_message' => 'We cannot accept your booking at this time.',
                'confirmation_price' => 150.00,
                'confirmation_status' => 'pending',
                'phone_number' => '+201098765432',
                'open_at' => '09:00:00',
                'close_at' => '20:00:00',
                'url' => 'https://example.com/giza-center',
                'rateing' => 4.0,
                'status' => 'under_review',
                'booking_status' => 'available',
                'Number_of_reservations' => 85,
                'active' => true,
                'title' => 'Giza Health Center',
                'description' => 'Providing high-quality health services in Giza.'
            ],
            [
                'location' => [
                    'address' => 'Alexandria, Egypt',
                    'coordinates' => [
                        'lat' => 31.2001,
                        'lng' => 29.9187
                    ]
                ],
                'accaptable_message' => 'We are glad to confirm your booking.',
                'unaccaptable_message' => 'Unfortunately, we cannot confirm your booking.',
                'confirmation_price' => 120.00,
                'confirmation_status' => 'confirmed',
                'phone_number' => '+201223344556',
                'open_at' => '07:30:00',
                'close_at' => '17:30:00',
                'url' => 'https://example.com/alex-center',
                'rateing' => 4.7,
                'status' => 'published',
                'booking_status' => 'available',
                'Number_of_reservations' => 200,
                'active' => true,
                'title' => 'Alexandria Care Center',
                'description' => 'A modern care center serving Alexandria residents.'
            ],
            [
                'location' => [
                    'address' => 'Hurghada, Egypt',
                    'coordinates' => [
                        'lat' => 27.2579,
                        'lng' => 33.8116
                    ]
                ],
                'accaptable_message' => 'Your appointment is confirmed.',
                'unaccaptable_message' => 'Your appointment cannot be confirmed.',
                'confirmation_price' => 90.00,
                'confirmation_status' => 'rejected',
                'phone_number' => '+201334455667',
                'open_at' => '10:00:00',
                'close_at' => '22:00:00',
                'url' => 'https://example.com/hurghada-center',
                'rateing' => 3.9,
                'status' => 'not_published',
                'booking_status' => 'unavailable',
                'Number_of_reservations' => 40,
                'active' => false,
                'title' => 'Hurghada Wellness Center',
                'description' => 'A wellness center in Hurghada offering relaxation and therapy services.'
            ],
            [
                'location' => [
                    'address' => 'Luxor, Egypt',
                    'coordinates' => [
                        'lat' => 25.6872,
                        'lng' => 32.6396
                    ]
                ],
                'accaptable_message' => 'Booking confirmed successfully.',
                'unaccaptable_message' => 'Booking request denied.',
                'confirmation_price' => 110.00,
                'confirmation_status' => 'confirmed',
                'phone_number' => '+201445566778',
                'open_at' => '06:00:00',
                'close_at' => '16:00:00',
                'url' => 'https://example.com/luxor-center',
                'rateing' => 4.2,
                'status' => 'published',
                'booking_status' => 'available',
                'Number_of_reservations' => 65,
                'active' => true,
                'title' => 'Luxor Health Clinic',
                'description' => 'Affordable healthcare services in Luxor.'
            ],
        ];

        // إنشاء 50 مركز باستخدام البيانات الأساسية مع تغييرات بسيطة
        $organizations = [];
        $cities = ['Cairo', 'Giza', 'Alexandria', 'Hurghada', 'Luxor', 'Mansoura', 'Tanta', 'Aswan', 'Port Said', 'Zagazig'];
        $types = ['Medical', 'Health', 'Care', 'Wellness', 'Clinic', 'Center', 'Hospital', 'Hub', 'Institute', 'Facility'];
        $adjectives = ['Advanced', 'Modern', 'Professional', 'Trusted', 'Leading', 'Premium', 'Quality', 'Expert', 'Specialized', 'Comprehensive'];
        $areaCodes = ['الشمالي', 'الجنوبي', 'الشرقي', 'الغربي', 'المركزي', 'الجديد', 'التقليدي', 'المتطور'];

        for ($i = 0; $i < 50; $i++) {
            $base = $baseOrganizations[$i % count($baseOrganizations)];
            $city = $cities[array_rand($cities)];
            $type = $types[array_rand($types)];
            $adjective = $adjectives[array_rand($adjectives)];
            $areaCode = $areaCodes[array_rand($areaCodes)];

            // استخدام الإحداثيات من البيانات الأساسية مباشرة
            $baseLat = $base['location']['coordinates']['lat'];
            $baseLng = $base['location']['coordinates']['lng'];

            $organizations[] = [
                'location' => json_encode([
                    'address' => $city . ', Egypt',
                    'coordinates' => [
                        'lat' => $baseLat + (rand(-100, 100) / 1000),
                        'lng' => $baseLng + (rand(-100, 100) / 1000)
                    ]
                ]),
                'accaptable_message' => $base['accaptable_message'],
                'unaccaptable_message' => $base['unaccaptable_message'],
                'confirmation_price' => $base['confirmation_price'] + rand(-20, 20),
                'confirmation_status' => $base['confirmation_status'],
                'phone_number' => '+201' . rand(100000000, 999999999),
                'open_at' => $base['open_at'],
                'close_at' => $base['close_at'],
                'url' => 'https://example.com/' . Str::slug($city) . '-center-' . ($i + 1),
                'rateing' => round($base['rateing'] + (rand(-10, 10) / 10), 1),
                'status' => $base['status'],
                'booking_status' => $base['booking_status'],
                'Number_of_reservations' => $base['Number_of_reservations'] + rand(-30, 50),
                'active' => $base['active'],
                'title' => $city . ' ' . $adjective . ' ' . $type,
                'description' => 'A ' . strtolower($adjective) . ' ' . strtolower($type) . ' located in ' . $city . ' providing excellent services.'
            ];
        }

        $organizationBenefits = [
            'Wide network of professional healthcare providers',
            'Affordable and transparent pricing for all services',
            'Flexible booking system with real-time availability',
            'Trusted by thousands of satisfied patients',
            'Continuous support and follow-up after appointments'
        ];

        $locations = [
            [
                'address' => '123 Nile Street, Cairo, Egypt',
                'coordinates' => ['lat' => 30.0444, 'lng' => 31.2357],
            ],
            [
                'address' => '45 King Fahd Rd, Riyadh, Saudi Arabia',
                'coordinates' => ['lat' => 24.7136, 'lng' => 46.6753],
            ],
            [
                'address' => '789 Bourj Avenue, Beirut, Lebanon',
                'coordinates' => ['lat' => 33.8938, 'lng' => 35.5018],
            ],
            [
                'address' => '12 Habib Bourguiba St, Tunis, Tunisia',
                'coordinates' => ['lat' => 36.8065, 'lng' => 10.1815],
            ],
            [
                'address' => '88 Algiers Center, Algiers, Algeria',
                'coordinates' => ['lat' => 36.7538, 'lng' => 3.0588],
            ],
        ];

        foreach ($organizations as $index => $organizationData) {
            // اختيار صور عشوائية من Unsplash
            $mainImageUrl = $unsplashImages[array_rand($unsplashImages)];
            $mainLogoImageUrl = $unsplashLogos[array_rand($unsplashLogos)];

            $maxOrder = Organization::max('order') ?? 0;

            $email = Str::slug($organizationData['title'], '_') . '_' . uniqid() . '@example.com';

            $randomLocation = $locations[array_rand($locations)];

            // تحويل الحالة إلى قيم رقمية
            $confirmationStatus = $organizationData['confirmation_status'] === 'confirmed' ? 1 : ($organizationData['confirmation_status'] === 'pending' ? 0 : 2);

            $bookingStatus = $organizationData['booking_status'] === 'available' ? 1 : 0;

            // إضافة معرّف فريد إلى العنوان لمنع التكرار مع تحسين التنسيق
            $uniqueSuffix = $index + 1;
            $areaCode = $areaCodes[array_rand($areaCodes)];
            $uniqueTitle = $organizationData['title'] . ' - ' . $areaCode . ' - ' . $uniqueSuffix;

            $organization = Organization::create([
                'title' => $uniqueTitle,
                'image' => $mainImageUrl,
                'logo' => $mainLogoImageUrl,
                'password' => Hash::make('password'),
                'description' => $organizationData['description'],
                'email' => $email,
                'location' => $organizationData['location'],
                'accaptable_message' => $organizationData['accaptable_message'],
                'unaccaptable_message' => $organizationData['unaccaptable_message'],
                'confirmation_price' => $organizationData['confirmation_price'],
                'confirmation_status' => $confirmationStatus,
                'phone_number' => $organizationData['phone_number'],
                'open_at' => $organizationData['open_at'],
                'close_at' => $organizationData['close_at'],
                'url' => $organizationData['url'],
                'order' => $maxOrder + 1,
                'rating' => $organizationData['rateing'],
                'status' => $organizationData['status'],
                'booking_status' => $bookingStatus,
                'number_of_reservations' => $organizationData['Number_of_reservations'],
                'active' => $organizationData['active'] ? 1 : 0,
            ]);

            $organization->categories()->attach(
                collect($categories)->shuffle()->take(3)->toArray()
            );

            $organization->subCategories()->attach(
                collect($subCategories)->shuffle()->take(3)->toArray()
            );

            foreach ($organizationBenefits as $benefit) {
                OrganizationBenefit::create([
                    'organization_id' => $organization->id,
                    'title' => $benefit,
                ]);
            }

            // إضافة كلمات مفتاحية (Keywords) مع منع التكرار
            $randomKeywordIds = collect($keywordIds)
                ->shuffle()
                ->take(rand(1, 5))
                ->toArray();

            // استخدام syncWithoutDetaching لتجنب التكرار
            $organization->keywords()->syncWithoutDetaching($randomKeywordIds);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $this->command->info('✅ Inserted 50 organizations with Unsplash images and unique titles.');
    }
}
