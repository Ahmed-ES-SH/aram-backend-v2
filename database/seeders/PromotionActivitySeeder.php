<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromotionActivity;
use App\Models\Promoter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromotionActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        PromotionActivity::truncate();
        // Ensure we have promoters first
        $promoters = Promoter::all();

        if ($promoters->isEmpty()) {
            $this->command->warn('⚠️ No promoters found. Please seed promoters first.');
            return;
        }

        // Empty the table before inserting
        DB::table('promotion_activities')->truncate();

        $activities = [];

        // Define fake product samples
        $products = [
            [
                'title' => 'Professional Web Design Card',
                'image' => 'https://backend-v1.aram-gulf.com/images/cards/card-3.png',
                'price' => '120.00',
                'quantity' => 1,
                'duration' => '6 months',
            ],
            [
                'title' => 'Basic Logo Card',
                'image' => 'https://backend-v1.aram-gulf.com/images/cards/card-5.png',
                'price' => '56.00',
                'quantity' => 1,
                'duration' => '12 months',
            ],
            [
                'title' => 'SEO Optimization Card',
                'image' => 'https://backend-v1.aram-gulf.com/images/cards/card-1.png',
                'price' => '37.00',
                'quantity' => 1,
                'duration' => '23 months',
            ],
            [
                'title' => 'Social Media Marketing Card',
                'image' => 'https://backend-v1.aram-gulf.com/images/cards/card-2.png',
                'price' => '89.00',
                'quantity' => 1,
                'duration' => '8 months',
            ],
        ];

        // Create 20 activities for EACH promoter
        foreach ($promoters as $promoter) {
            for ($i = 0; $i < 20; $i++) {
                // إضافة نوع signup إلى الأنواع المتاحة
                $activityType = fake()->randomElement(['visit', 'purchase', 'signup']);

                // Generate different dates for each activity
                $activityDate = now()
                    ->subDays(fake()->numberBetween(0, 60)) // Last 60 days
                    ->subHours(fake()->numberBetween(0, 23))
                    ->subMinutes(fake()->numberBetween(0, 59));

                $common = [
                    'promoter_type' => $promoter->promoter_type,
                    'promoter_id' => $promoter->id, // Use actual promoter ID
                    'activity_type' => $activityType,
                    'ip_address' => fake()->ipv4(),
                    'country' => fake()->country(),
                    'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
                    'ref_code' => strtoupper(Str::random(8)),
                    'activity_at' => $activityDate,
                    'created_at' => $activityDate, // Same as activity date for realistic data
                    'updated_at' => $activityDate,
                ];

                if ($activityType === 'visit') {
                    $activities[] = array_merge($common, [
                        'member_id' => null,
                        'member_type' => null,
                        'metadata' => json_encode([
                            'user_id' => fake()->numberBetween(1, 50),
                            'user_type' => fake()->randomElement(['user', 'organization']),
                            'ip' => $common['ip_address'],
                            'visited_url' => 'https://platform.test/course/' . fake()->numberBetween(1, 100),
                            'referrer_url' => fake()->randomElement([
                                'https://facebook.com',
                                'https://twitter.com',
                                'https://instagram.com',
                                'https://whatsapp.com',
                                'https://tiktok.com',
                                null
                            ]),
                            'user_agent' => fake()->userAgent(),
                            'visit_duration_seconds' => fake()->numberBetween(5, 600),
                        ]),
                        'commission_amount' => null,
                    ]);
                } else if ($activityType === 'purchase') {
                    // purchase activity
                    $chosenProducts = fake()->randomElements($products, fake()->numberBetween(1, 2));
                    $total = collect($chosenProducts)->sum(fn($p) => (float) $p['price']);
                    $commissionRate = fake()->randomElement([0.05, 0.08, 0.10, 0.15]); // 5% to 15% commission
                    $commissionAmount = round($total * $commissionRate, 2);

                    $activities[] = array_merge($common, [
                        'member_id' => fake()->numberBetween(1, 50),
                        'member_type' => fake()->randomElement(['user', 'organization']),
                        'metadata' => json_encode([
                            'user_id' => fake()->numberBetween(1, 50),
                            'user_type' => fake()->randomElement(['user', 'organization']),
                            'items' => $chosenProducts,
                            'total' => $total,
                            'commission_rate' => $commissionRate * 100, // Store as percentage
                        ]),
                        'commission_amount' => $commissionAmount,
                    ]);
                } else {
                    // signup activity
                    $memberId = fake()->numberBetween(1, 50);
                    $memberType = fake()->randomElement(['user', 'organization']);

                    $activities[] = array_merge($common, [
                        'member_id' => $memberId,
                        'member_type' => $memberType,
                        'metadata' => json_encode([
                            'user_id' => $memberId,
                            'user_type' => $memberType,
                            'ip' => $common['ip_address'],
                            'signup_source' => fake()->randomElement(['direct', 'referral', 'social_media', 'email']),
                            'user_agent' => fake()->userAgent(),
                            'registration_method' => fake()->randomElement(['web', 'mobile_app', 'api']),
                            'email_verified' => fake()->boolean(80), // 80% chance of verified email
                            'profile_completed' => fake()->boolean(60), // 60% chance of completed profile
                            'welcome_email_sent' => fake()->boolean(90), // 90% chance of welcome email sent
                            'initial_referral_bonus' => fake()->randomElement([0, 5, 10, 15]), // bonus points
                        ]),
                        'commission_amount' => null, // لا توجد عمولة للتسجيل
                    ]);
                }

                // Insert in batches to avoid memory issues
                if (count($activities) >= 500) {
                    PromotionActivity::insert($activities);
                    $activities = [];
                }
            }
        }

        // Insert any remaining activities
        if (!empty($activities)) {
            PromotionActivity::insert($activities);
        }

        $totalActivities = count($promoters) * 20;
        $this->command->info("✅ Inserted {$totalActivities} promotion activities (including signup type) using {$promoters->count()} promoters.");

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
