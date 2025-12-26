<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class RealOrganizationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = base_path('aram_centers_data.php');

        if (!file_exists($file_path)) {
            $this->command->error("File not found: {$file_path}");
            return;
        }

        $data = include $file_path;

        $categories = [];

        foreach ($data as $item) {
            if (isset($item['category']) && !empty($item['category'])) {
                // Normalize: trim spaces and trailing dots
                $catName = trim($item['category'], " \t\n\r\0\x0B.");
                if (!empty($catName)) {
                    $categories[] = $catName;
                }
            }
        }

        $uniqueCategories = array_unique($categories);

        foreach ($uniqueCategories as $catName) {
            // Check if exists to avoid duplicates if re-run
            $exists = Category::where('title_ar', $catName)->exists();

            if (!$exists) {
                Category::create([
                    'title_ar' => $catName,
                    'title_en' => $catName, // Using same name for EN as per plan
                    'is_active' => 1,
                    'bg_color' => '#f3f4f6', // Default light gray
                    'icon_name' => "Fauser",
                    'image' => "http://127.0.0.1:8000/images/categories/Philosophy.png",
                ]);
            }
        }
    }
}
