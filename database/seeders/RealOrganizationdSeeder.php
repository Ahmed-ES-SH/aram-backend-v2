<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Organization;
use App\Models\Category;

class RealOrganizationdSeeder extends Seeder
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

        // Track processed values to avoid duplicates within the file itself
        $processedTitles = [];
        $processedEmails = [];

        foreach ($data as $item) {

            $title = $item['title'] ?? 'Unknown Org';
            $title = trim($title);
            $order = Organization::max('order') + 1;

            // Check for duplicate title (in DB or processed list)
            if (in_array($title, $processedTitles) || Organization::where('title', $title)->exists()) {
                $this->command->info("Skipping duplicate title: $title");
                continue;
            }

            // Prepare Email
            // Logic: If real email provided, use it. If duplicates, skip logic below handles it.
            // If empty, generate fake unique.
            $email = !empty($item['email']) ? trim($item['email']) : fake()->unique()->safeEmail();

            // Check for duplicate email (in DB or processed list)
            if (in_array($email, $processedEmails) || Organization::where('email', $email)->exists()) {
                if (!empty($item['email'])) {
                    $this->command->info("Skipping duplicate email for title $title: $email");
                    continue;
                } else {
                    // It was a generated email that somehow collided or existed. Regenerate unique.
                    $email = fake()->unique()->safeEmail();
                }
            }

            // Mark as processed
            $processedTitles[] = $title;
            $processedEmails[] = $email;

            $phone = !empty($item['phone']) ? $item['phone'] : null;
            $locationStr = !empty($item['location']) ? trim($item['location'], " \t\n\r\0\x0B.") : '';

            // Get Coordinates
            $coords = $this->getCoordinates($locationStr);

            // Structure location as requested
            $locationVal = [
                'address' => $locationStr ?: 'Muscat, Oman', // Default if empty
                'coordinates' => $coords
            ];

            $catRaw = $item['category'] ?? '';

            $org = Organization::create([
                'title' => $title,
                'email' => $email,
                'image' => 'https://backend-v2.aram-gulf.com/real.jpg',
                'password' => Hash::make("aram"),
                'phone_number' => $phone,
                'location' => $locationVal,
                'description' => $catRaw,
                'active' => 0,
                'status' => 'not_published',
                'confirmation_price' => rand(10, 100),
                'confirmation_status' => 0,
                'booking_status' => 0,
                'order' => $order,
            ]);

            // Link Category
            if (!empty($catRaw)) {
                $catName = trim($catRaw, " \t\n\r\0\x0B.");
                if (!empty($catName)) {
                    $category = Category::where('title_ar', $catName)->first();
                    if ($category) {
                        $org->categories()->sync([$category->id]);
                    }
                }
            }
        }
    }

    private function getCoordinates($locationName)
    {
        // Default (Muscat)
        $default = ['lat' => 23.5880, 'lng' => 58.3829];

        if (empty($locationName)) {
            return $default;
        }

        $map = [
            // Seeb / Maabela / Hail / Khoudh / Mawaleh
            'معبيل' => ['lat' => 23.6143, 'lng' => 58.1221],
            'معبيلة' => ['lat' => 23.6143, 'lng' => 58.1221],
            'السيب' => ['lat' => 23.6703, 'lng' => 58.1891],
            'خوض' => ['lat' => 23.6074, 'lng' => 58.2000],
            'الخوض' => ['lat' => 23.6074, 'lng' => 58.2000],
            'حيل' => ['lat' => 23.6333, 'lng' => 58.2167],
            'الحيل' => ['lat' => 23.6333, 'lng' => 58.2167],
            'موالح' => ['lat' => 23.5973, 'lng' => 58.2432],

            // Central Muscat (Bausher, Khuwair, Ghubra, Azaiba, Ansab)
            'خوير' => ['lat' => 23.6006, 'lng' => 58.4239],
            'الخوير' => ['lat' => 23.6006, 'lng' => 58.4239],
            'بوشر' => ['lat' => 23.5672, 'lng' => 58.4039],
            'العذيبة' => ['lat' => 23.5859, 'lng' => 58.3829],
            'عذيبة' => ['lat' => 23.5859, 'lng' => 58.3829],
            'غبرة' => ['lat' => 23.5933, 'lng' => 58.4067],
            'الغبرة' => ['lat' => 23.5933, 'lng' => 58.4067],
            'انصب' => ['lat' => 23.5567, 'lng' => 58.3262],
            'الأنصب' => ['lat' => 23.5567, 'lng' => 58.3262],

            // Old Muscat / Ruwi
            'روي' => ['lat' => 23.5997, 'lng' => 58.5325],
            'مسقط' => ['lat' => 23.6139, 'lng' => 58.5922],
            'قنتب' => ['lat' => 23.5544, 'lng' => 58.6186],

            // Batinah
            'بركاء' => ['lat' => 23.6700, 'lng' => 57.8800],
            'مصنعة' => ['lat' => 23.7333, 'lng' => 57.6000],
            'المصنعة' => ['lat' => 23.7333, 'lng' => 57.6000],
            'السويق' => ['lat' => 23.8500, 'lng' => 57.4333],
            'صحار' => ['lat' => 24.3461, 'lng' => 56.7075],
            'الرستاق' => ['lat' => 23.3906, 'lng' => 57.4244],
            'عوابي' => ['lat' => 23.3333, 'lng' => 57.5167],
            'العوابي' => ['lat' => 23.3333, 'lng' => 57.5167],
            'وادي المعاول' => ['lat' => 23.4167, 'lng' => 57.8167],
            'ثرمد' => ['lat' => 23.7667, 'lng' => 57.6333],

            // Dakhiliya
            'سمائل' => ['lat' => 23.3000, 'lng' => 57.9833],
            'إزكي' => ['lat' => 22.9333, 'lng' => 57.7000],
            'نزوى' => ['lat' => 22.9333, 'lng' => 57.5333],
            'الداخلية' => ['lat' => 22.9000, 'lng' => 57.5000],
            'بهلا' => ['lat' => 22.9667, 'lng' => 57.3000],

            // Dhofar
            'صلالة' => ['lat' => 17.0151, 'lng' => 54.0924],

            // Sharqiyah
            'صور' => ['lat' => 22.5667, 'lng' => 59.5289],
            'الشرقية' => ['lat' => 22.5000, 'lng' => 59.0000],

            // Dhahirah
            'عبري' => ['lat' => 23.2333, 'lng' => 56.5167],
            'الظاهرة' => ['lat' => 23.2000, 'lng' => 56.5000],
        ];

        foreach ($map as $key => $value) {
            // Check if city name is contained in the string
            if (str_contains($locationName, $key)) {
                return $value;
            }
        }

        return $default;
    }
}
