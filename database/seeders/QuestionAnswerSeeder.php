<?php

namespace Database\Seeders;

use App\Models\QuestionAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {

        QuestionAnswer::truncate();
        // الأسئلة والأجوبة
        $questionsAnswers = [
            ['question' => 'ما هو Laravel؟', 'answer' => 'Laravel هو إطار عمل PHP مفتوح المصدر يستخدم لتطوير تطبيقات الويب.', 'user_id' => 1],
            ['question' => 'ما هي RESTful APIs؟', 'answer' => 'RESTful APIs هي واجهات برمجة تطبيقات تعتمد على مبادئ REST، وهي تسمح بالتواصل بين الأنظمة المختلفة باستخدام HTTP.', 'user_id' => 2],
            ['question' => 'ما هو الفرق بين GET و POST في HTTP؟', 'answer' => 'GET يستخدم لجلب البيانات من السيرفر، بينما POST يستخدم لإرسال البيانات إلى السيرفر.', 'user_id' => 3],
            ['question' => 'كيف يمكنني إعداد بيئة تطوير Laravel؟', 'answer' => 'يمكن إعداد بيئة Laravel باستخدام Composer لتنصيب الحزم، بالإضافة إلى Homestead أو Valet لتوفير بيئة خادم محلي.', 'user_id' => 4],
            ['question' => 'ما هو مفهوم MVC؟', 'answer' => 'MVC هو اختصار لـ Model-View-Controller، وهو نمط تصميم يستخدم لفصل منطق الأعمال (Model) عن العرض (View) والتحكم (Controller).', 'user_id' => 5],
            ['question' => 'كيف يمكنني التعامل مع قواعد البيانات في Laravel؟', 'answer' => 'يمكن التعامل مع قواعد البيانات باستخدام Eloquent ORM الخاص بـ Laravel أو من خلال استعلامات SQL التقليدية باستخدام Query Builder.', 'user_id' => 1],
            ['question' => 'ما هو مفهوم Middleware في Laravel؟', 'answer' => 'Middleware هو طبقة وسيطة يمكن استخدامها لتنفيذ عمليات قبل أو بعد معالجة الطلب في Laravel.', 'user_id' => 2],
            ['question' => 'كيف يمكنني إرسال بريد إلكتروني باستخدام Laravel؟', 'answer' => 'يمكنك استخدام مكتبة Mail في Laravel لإرسال بريد إلكتروني باستخدام SMTP أو عبر خدمات مثل Mailgun و SendGrid.', 'user_id' => 3],
            ['question' => 'ما هي الحزم في Laravel؟', 'answer' => 'الحزم في Laravel هي وحدات قابلة لإعادة الاستخدام تضيف ميزات ووظائف جديدة إلى تطبيق Laravel.', 'user_id' => 4],
            ['question' => 'ما هو Artisan؟', 'answer' => 'Artisan هو أداة سطر أوامر في Laravel تساعد في تنفيذ المهام الروتينية مثل إنشاء الملفات وتنفيذ الهجرات.', 'user_id' => 5],
            ['question' => 'كيف يمكنني تأمين تطبيق Laravel؟', 'answer' => 'يمكن تأمين تطبيق Laravel باستخدام الميزات المدمجة مثل التحقق من الصلاحيات Middleware و الحماية من الهجمات عبر CSRF.', 'user_id' => 1],
            ['question' => 'ما هو مفهوم Service Container في Laravel؟', 'answer' => 'Service Container هو أداة قوية في Laravel تُستخدم لإدارة التبعية بين الكائنات.', 'user_id' => 2],
            ['question' => 'ما هي الهجرات (Migrations) في Laravel؟', 'answer' => 'الهجرات هي طريقة لإدارة إصدار قاعدة البيانات في Laravel، حيث يمكنك إنشاء وتعديل الجداول بسهولة.', 'user_id' => 3],
            ['question' => 'ما هو Blade في Laravel؟', 'answer' => 'Blade هو محرك القوالب الخاص بـ Laravel والذي يسمح لك بكتابة قوالب ديناميكية باستخدام تركيبات بسيطة.', 'user_id' => 4],
            ['question' => 'كيف يمكنني تنفيذ مصادقة المستخدمين في Laravel؟', 'answer' => 'يمكنك تنفيذ مصادقة المستخدمين باستخدام نظام Auth المدمج في Laravel الذي يدعم تسجيل الدخول والتسجيل وإدارة الجلسات.', 'user_id' => 5],
            ['question' => 'ما هو الفرق بين Vue.js و React؟', 'answer' => 'Vue.js و React كلاهما مكتبتان لبناء واجهات المستخدم، لكن React مكتبة موجهة للمكونات بينما Vue.js إطار أكثر تكاملاً.', 'user_id' => 1],
            ['question' => 'كيف يمكنني تحسين أداء تطبيق Laravel؟', 'answer' => 'يمكن تحسين الأداء باستخدام التخزين المؤقت (caching)، وضبط قواعد البيانات، وتقليل عدد الاستعلامات.', 'user_id' => 2],
            ['question' => 'ما هو مفهوم Pivot Table في Laravel؟', 'answer' => 'Pivot Table هو جدول وسيط يستخدم لتمثيل العلاقات المتعددة بين الجداول في قاعدة البيانات.', 'user_id' => 3],
            ['question' => 'كيف يمكنني إدارة المهام المجدولة في Laravel؟', 'answer' => 'يمكنك استخدام Scheduler في Laravel لإدارة المهام المجدولة عبر أوامر Artisan.', 'user_id' => 4],
            ['question' => 'ما هو الفرق بين include و require في PHP؟', 'answer' => 'include يتضمن ملفًا خارجيًا في حين أن require يوقف تنفيذ البرنامج إذا كان الملف غير موجود.', 'user_id' => 5],
        ];

        // إدخال البيانات في جدول questions_answers
        DB::table('question_answers')->insert($questionsAnswers);
    }
}
