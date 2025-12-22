<?php

namespace Database\Seeders;

use App\Models\FooterLink;
use App\Models\FooterList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        FooterLink::truncate();
        FooterList::truncate();

        // إدخال القوائم أولًا
        $listsData = [
            ['title' => 'list_1'],
            ['title' => 'list_2'],
            ['title' => 'list_3'],
            ['title' => 'list_4'],
            ['title' => 'list_5'],
        ];

        $lists = [];
        foreach ($listsData as $data) {
            $lists[] = FooterList::create($data);
        }

        // إنشاء الروابط وربطها بالقوائم باستخدام IDs الحقيقية
        $links = [
            ['title_en' => 'Home', 'title_ar' => 'الرئيسية', 'url' => '/home', 'list' => 0],
            ['title_en' => 'About Us', 'title_ar' => 'من نحن', 'url' => '/about', 'list' => 0],
            ['title_en' => 'Contact', 'title_ar' => 'اتصل بنا', 'url' => '/contact', 'list' => 0],

            ['title_en' => 'Services', 'title_ar' => 'الخدمات', 'url' => '/services', 'list' => 1],
            ['title_en' => 'Portfolio', 'title_ar' => 'أعمالنا', 'url' => '/portfolio', 'list' => 1],
            ['title_en' => 'Careers', 'title_ar' => 'الوظائف', 'url' => '/careers', 'list' => 1],

            ['title_en' => 'Blog', 'title_ar' => 'المدونة', 'url' => '/blog', 'list' => 2],
            ['title_en' => 'News', 'title_ar' => 'الأخبار', 'url' => '/news', 'list' => 2],
            ['title_en' => 'Press', 'title_ar' => 'الصحافة', 'url' => '/press', 'list' => 2],

            ['title_en' => 'Privacy Policy', 'title_ar' => 'سياسة الخصوصية', 'url' => '/privacy-policy', 'list' => 3],
            ['title_en' => 'Terms of Service', 'title_ar' => 'شروط الخدمة', 'url' => '/terms', 'list' => 3],
            ['title_en' => 'Support', 'title_ar' => 'الدعم', 'url' => '/support', 'list' => 3],

            ['title_en' => 'FAQ', 'title_ar' => 'الأسئلة الشائعة', 'url' => '/faq', 'list' => 4],
            ['title_en' => 'Help Center', 'title_ar' => 'مركز المساعدة', 'url' => '/help-center', 'list' => 4],
            ['title_en' => 'Contact Sales', 'title_ar' => 'اتصل بالمبيعات', 'url' => '/contact-sales', 'list' => 4],
        ];

        foreach ($links as $link) {
            FooterLink::create([
                'list_id' => $lists[$link['list']]->id,
                'link_title_en' => $link['title_en'],
                'link_title_ar' => $link['title_ar'],
                'link_url' => $link['url'],
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
