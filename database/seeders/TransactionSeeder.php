<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['deposit', 'withdrawal', 'purchase', 'sale', 'commission', 'refund', 'transfer'];
        $statuses = ['pending', 'completed', 'failed'];
        $directions = ['in', 'out'];
        $sources = ['service', 'order', 'withdrawal', 'deposit', 'commission', 'refund'];

        for ($i = 0; $i < 20; $i++) {
            $type = $types[array_rand($types)];
            $direction = $type === 'deposit' || $type === 'sale' || $type === 'refund' ? 'in' : 'out';

            DB::table('transactions')->insert([
                'user_id'      => 1,
                'account_type'      => 'user',
                'type'         => $type,
                'amount'       => rand(50, 5000) / 10, // random between 5.0 and 500.0
                'direction'    => $direction,
                'status'       => $statuses[array_rand($statuses)],
                'source_type'  => $sources[array_rand($sources)],
                'source_id'    => rand(1, 50),
                'note'         => 'Test transaction for ' . $type,
                'meta'         => json_encode([
                    'gateway' => 'manual',
                    'reference' => Str::random(10),
                ]),
                'created_at'   => now()->subDays(rand(0, 30)),
                'updated_at'   => now(),
            ]);
        }
    }
}
