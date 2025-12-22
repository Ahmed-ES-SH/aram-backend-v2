<?php

namespace Database\Seeders;

use App\Models\PromoterRatio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class promoterRatioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PromoterRatio::updateOrCreate(['id' => 1], [
            'visit_ratio' => 1,
            'signup_ratio' => 1,
            'purchase_ratio' => 1,
        ]);
    }
}
