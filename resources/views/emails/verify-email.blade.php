<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفعيل الحساب</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            direction: rtl;
            text-align: right;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #feb803;
            padding: 30px 20px;
            text-align: center;
        }

        .logo {
            margin-bottom: 15px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .verify-button {
            display: inline-block;
            background-color: #feb803;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .verify-button:hover {
            background-color: #e5a600;
        }

        .note {
            font-size: 14px;
            color: #777;
            text-align: center;
            margin-top: 20px;
            line-height: 1.5;
        }

        .footer {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .footer p {
            font-size: 14px;
            color: #888;
            margin: 5px 0;
        }

        .link {
            word-break: break-all;
            color: #feb803;
            text-decoration: none;
        }
    </style>
</head>

<body style="direction:rtl;">
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="https://aram-gulf.com/logo.png" alt="Logo" width="80">
            </div>
            <h1>تفعيل الحساب</h1>
        </div>

        <div class="content">
            <p class="greeting">مرحباً {{ $user->name ?? $user->title }},</p>

            <p class="message">
                شكراً لتسجيلك في منصتنا. لتفعيل حسابك والبدء في استخدام خدماتنا، يرجى النقر على زر التفعيل أدناه.
            </p>

            <div class="button-container">
                <a href="{{ $url }}" class="verify-button">تفعيل الحساب</a>
            </div>

            <p class="note">
                إذا لم تكن قد قمت بإنشاء هذا الحساب، يمكنك تجاهل هذه الرسالة.<br>
                إذا لم يعمل الزر أعلاه، يمكنك نسخ الرابط التالي ولصقه في متصفحك:
            </p>

        </div>

        <div class="footer">
            <p>© {{ date('Y') }} جميع الحقوق محفوظة</p>
            <p>للاستفسارات، يرجى التواصل مع فريق الدعم</p>
        </div>
    </div>
</body>

</html>