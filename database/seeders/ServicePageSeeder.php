<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
use Illuminate\Support\Facades\DB;

class ServicePageSeeder extends Seeder
{

    public function run(): void
    {


        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        ServicePage::truncate();
        ServicePageHeroSection::truncate();
        ServicePageProblemSection::truncate();
        ServicePageProblemItem::truncate();
        ServicePageSolutionSection::truncate();
        ServicePageSolutionFeature::truncate();
        ServicePageGalleryImage::truncate();
        ServicePageStat::truncate();
        ServicePageTestimonial::truncate();
        ServicePageCtaSection::truncate();
        ServiceTracking::truncate();



        $this->call([
            ServicePageSeeder_1::class,
            ServicePageSeeder_2::class,
            ServicePageSeeder_3::class,
            ServicePageSeeder_4::class,
            ServicePageSeeder_5::class,
            ServicePageSeeder_6::class,
            ServicePageSeeder_7::class,
            ServicePageSeeder_8::class,
        ]);


        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
