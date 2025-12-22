<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
    <style>
        /* تنسيقات عامة */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .header h1 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            padding: 10px 0;
        }

        .header p {
            color: #4a5568;
            font-size: 16px;
            margin: 5px 0 0;
        }

        .content {
            padding: 25px 0;
            color: #4a5568;
            line-height: 1.8;
            font-size: 16px;
        }

        .content a {
            color: #3182ce;
            text-decoration: none;
            font-weight: 600;
        }

        .content a:hover {
            text-decoration: underline;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }

        .footer a {
            color: #3182ce;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 24px;
            background-color: #3182ce;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #2c5282;
        }

        .icon {
            width: 24px;
            height: 24px;
            vertical-align: middle;
            margin-right: 8px;
        }
    </style>
</head>

<body style="direction:rtl;">
    <div class="email-container">
        <!-- العنوان -->
        <div class="header">
            <h1>{{ $data['subject'] }}</h1>
            <p> Newsletter from {{ env('MAIL_FROM_NAME') }} </p>
        </div>

        <!-- المحتوى -->
        <div class="content">
            {!! $data['content'] !!}


        </div>

        <!-- التذييل -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Gulf Aram Limited . All rights reserved. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>

</html>
