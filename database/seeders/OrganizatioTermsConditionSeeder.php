<?php

namespace Database\Seeders;

use App\Models\OrganizationTermsCondition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizatioTermsConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrganizationTermsCondition::truncate();
        $policies = [
            ['content_ar' => 'نحن لا نشارك معلوماتك الشخصية مع أطراف ثالثة دون موافقتك.', 'content_en' => 'We do not share your personal information with third parties without your consent.'],
            ['content_ar' => 'يتم تخزين جميع البيانات التي تم جمعها بشكل آمن.', 'content_en' => 'All collected data is stored securely.'],
            ['content_ar' => 'يمكنك طلب حذف حسابك في أي وقت.', 'content_en' => 'You can request to delete your account at any time.'],
            ['content_ar' => 'يتم استخدام ملفات تعريف الارتباط لتحسين تجربة المستخدم وليس للتتبع.', 'content_en' => 'Cookies are used to improve user experience, not for tracking.'],
            ['content_ar' => 'سيتم استخدام بريدك الإلكتروني فقط للتواصل المتعلق بخدماتنا.', 'content_en' => 'Your email address will only be used for communication related to our services.'],
            ['content_ar' => 'نحن نلتزم بـ GDPR واللوائح الأخرى لحماية البيانات.', 'content_en' => 'We comply with GDPR and other data protection regulations.'],
            ['content_ar' => 'يتم التعامل مع معلومات الدفع بواسطة مزودي خدمات آمنين.', 'content_en' => 'Payment information is handled by secure third-party providers.'],
            ['content_ar' => 'يقتصر الوصول إلى البيانات الشخصية على الموظفين المصرح لهم فقط.', 'content_en' => 'Access to personal data is restricted to authorized personnel only.'],
            ['content_ar' => 'نقوم بتحديث تدابير الأمان لدينا بانتظام.', 'content_en' => 'We regularly update our security measures.'],
            ['content_ar' => 'يتم إخفاء هوية بيانات المستخدم عند استخدامها للتحليلات.', 'content_en' => 'User data is anonymized when used for analytics.'],
            ['content_ar' => 'لا نبيع بياناتك للمعلنين.', 'content_en' => 'We do not sell your data to advertisers.'],
            ['content_ar' => 'يتم إخطار المستخدمين بأي اختراقات للبيانات على الفور.', 'content_en' => 'Users are notified of any data breaches promptly.'],
            ['content_ar' => 'نوفر أدوات لإدارة إعدادات الخصوصية الخاصة بك.', 'content_en' => 'We provide tools to manage your privacy settings.'],
            ['content_ar' => 'يمكنك تعديل تفضيلاتك للاشتراكات البريدية في أي وقت.', 'content_en' => 'Your preferences for email subscriptions can be modified anytime.'],
            ['content_ar' => 'يتم الاحتفاظ بالبيانات فقط طالما كانت ضرورية للأغراض المحددة.', 'content_en' => 'Data is only retained as long as necessary for the purposes stated.'],
            ['content_ar' => 'الأدوات التي نستخدمها تلتزم بمعايير الخصوصية لدينا.', 'content_en' => 'Third-party tools used comply with our privacy standards.'],
            ['content_ar' => 'نستخدم التشفير لحماية المعلومات الحساسة.', 'content_en' => 'We use encryption to protect sensitive information.'],
            ['content_ar' => 'تتم مراقبة خوادمنا على مدار الساعة للأمان.', 'content_en' => 'Our servers are monitored for security 24/7.'],
            ['content_ar' => 'يتم التعامل مع بيانات الأطفال بما يتوافق مع COPPA.', 'content_en' => 'Children\'s data is handled in compliance with COPPA.'],
            ['content_ar' => 'نرحب بتعليقاتك حول ممارسات الخصوصية لدينا.', 'content_en' => 'We welcome your feedback on privacy practices.'],
        ];

        foreach ($policies as $policy) {
            OrganizationTermsCondition::create($policy);
        }
    }
}
