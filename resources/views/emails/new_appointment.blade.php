<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد حجز جديد</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');

        /* إعادة تعيين أساسية وضبط الخط */
        body {
            font-family: 'Cairo', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            /* خلفية فاتحة للبريد */
            color: #333333;
            /* لون النص الأساسي */
            direction: rtl;
            /* دعم اللغة العربية */
            text-align: right;
        }

        /* حاوية المحتوى الرئيسية */
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            /* خلفية بيضاء للمحتوى */
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 2px solid #ddd
        }

        /* الترويسة (Header) */
        .header {
            background-color: #feb803;
            /* لون أزرق رئيسي للعلامة التجارية */
            color: #ffffff;
            padding: 20px 30px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        /* قسم محتوى البريد (Body) */
        .content-body {
            padding: 30px;
            line-height: 1.6;
        }

        /* قسم تفاصيل الحجز */
        .appointment-details {
            margin: 25px 0;
            padding: 15px;
            background-color: #e9f7ff;
            /* خلفية فاتحة لتمييز التفاصيل */
            border-radius: 6px;
            border-right: 5px solid #feb803;
            /* خط عمودي لزيادة التمييز */
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            /* توزيع المحتوى على اليمين واليسار */
            padding: 8px 0;
            border-bottom: 1px dashed #cccccc;
            /* خط فاصل خفيف */
        }

        .detail-item:last-child {
            border-bottom: none;
            /* إزالة الخط الفاصل الأخير */
        }

        .detail-item strong {
            color: #0056b3;
            /* لون مميز للعناوين */
            font-weight: 700;
        }

        /* الزر (Button) */
        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background-color: #28a745;
            /* لون أخضر جذاب لزر الإجراء (CTA) */
            color: #ffffff !important;
            /* مهم استخدام !important لضمان اللون */
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 700;
            transition: background-color 0.3s ease;
            white-space: nowrap;
            /* يمنع انقسام النص في الزر */
        }

        .button:hover {
            background-color: #1e7e34;
            /* لون أغمق عند التمرير */
        }

        /* التذييل (Footer) - اختياري */
        .footer {
            text-align: center;
            padding: 15px 30px;
            font-size: 12px;
            color: #999999;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>

<body style="direction:rtl;">
    <div class="container">

        <div class="header">
            <h2>طلب حجز جديد 🔔</h2>
        </div>

        <div class="content-body">

            <!-- Header -->
            <div>
                <div align="center" style="background-color:#feb803; padding:25px;">
                    <img src="{{ url('https://aram-gulf.com/logo.png') }}" alt="Logo" width="80"
                        style="margin-bottom:10px;">
                    <h2 style="margin:0; color:#ffffff; font-size:22px;">منصة آرام الخليج المحدودة</h2>
                </div>
            </div>

            <p>مرحباً بك يا **{{ $organization->title }}**،</p>
            <p>لقد وصلك طلب حجز موعد جديد. يرجى مراجعة التفاصيل أدناه واتخاذ الإجراء اللازم للموافقة أو الرفض.</p>

            <div class="appointment-details">
                <p style="margin-top: 0; font-weight: bold; color: #feb803;">**تفاصيل الموعد:**</p>

                <div class="detail-item">
                    <span>**التاريخ:**</span>
                    <strong>{{ $appointment->start_time}}</strong>
                </div>

                <div class="detail-item">
                    <span>**الوقت:**</span>
                    <strong>{{ $appointment->start_time }}</strong>
                </div>

                <div class="detail-item" style="border-bottom: none;">
                    <span>**مبلغ التأكيد:**</span>
                    <strong style="color: #c80000;">{{ $appointment->price }} ريال عُماني</strong>
                </div>
            </div>


            <p>للاطلاع على جميع تفاصيل الحجز والتواصل مع العميل، يرجى النقر على الزر أدناه وتسجيل الدخول إلى لوحة التحكم الخاصة بك.</p>

            <div class="button-container">
                <a href="{{ env('FRONTEND_URL') }}" class="button" target="_blank" rel="noopener noreferrer">
                    ✅ مراجعة وتأكيد الحجز
                </a>

            </div>

            <p style="text-align: left; margin-top: 40px; font-size: 14px; color: #666;">مع خالص التحية،<br>فريق العمل</p>

        </div>

        <div class="footer">
            <p>هذه رسالة آلية. يرجى عدم الرد عليها.</p>
        </div>
    </div>
</body>

</html>
