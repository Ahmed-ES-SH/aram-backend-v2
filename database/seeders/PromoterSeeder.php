<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promoter;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PromoterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        $this->command->info('🚀 Starting Promoter Seeder...');


        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Promoter::truncate();

        $users = User::select('id', 'name', 'email')->limit(20)->get();
        $discountPercentages = [1, 1.2, 1.8, 2.5, 3.2];
        $statuses = ['active', 'disabled'];
        $createdCount = 0;

        foreach ($users as $user) {
            $promoter = Promoter::firstOrCreate(
                [
                    'promoter_type' => 'user',
                    'promoter_id' => $user->id
                ],
                [
                    'discount_percentage' => $discountPercentages[array_rand($discountPercentages)],
                    'referral_code' => $this->generateUniqueReferralCode(),
                    'status' => $statuses[array_rand($statuses)],
                    'total_visits' => rand(0, 100),
                    'total_signups' => rand(0, 50),
                    'total_purchases' => rand(0, 30),
                    'total_earnings' => rand(0, 1000),
                ]
            );

            if ($promoter->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Promoters seeded successfully: {$createdCount} new records created.");

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * توليد كود إحالة فريد
     */
    private function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Promoter::where('referral_code', $code)->exists());

        return $code;
    }
}
