<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VariableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CardsHeaderSeeder::class,
            OrganizationsHeaderSeeder::class,
            ServicesHeaderSeeder::class,
            StatsSectionSeeder::class,
        ]);
    }
}
