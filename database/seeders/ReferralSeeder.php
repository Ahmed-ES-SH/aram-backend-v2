<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('referrals')->truncate();

        $promoters = DB::table('promoters')->pluck('id')->toArray();
        $users     = DB::table('users')->pluck('id')->toArray();

        $statuses = ['pending', 'converted', 'rewarded', 'fraud'];

        $referrals = [];

        for ($i = 0; $i < 20; $i++) {
            $status = $statuses[array_rand($statuses)];
            $convertedAt = in_array($status, ['converted', 'rewarded'])
                ? Carbon::now()->subDays(rand(1, 30))
                : null;

            $referrals[] = [
                'promoter_id'      => $promoters[array_rand($promoters)],
                'referred_user_id' => $users[array_rand($users)],
                'ip'               => rand(0, 1) ? "192.168.1." . rand(2, 254) : null,
                'status'           => $status,
                'converted_at'     => $convertedAt,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        DB::table('referrals')->insert($referrals);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
