<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إلغاء الحجز</title>
</head>

<body style="margin:0; direction:rtl; padding:0; background-color:#f4f6f8; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Main Card -->
                <table cellpadding="0" cellspacing="0" width="600"
                    style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 25px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color:#FFA552; padding:25px;">
                            <img src="{{ url('https://aram-gulf.com/logo.png') }}" alt="Logo" width="80" style="margin-bottom:10px;">
                            <h2 style="margin:0; color:#ffffff; font-size:22px;">منصة آرام الخليج المحدودة</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <!-- Body -->
                    <tr>
                        <td dir="rtl" style="padding:35px 40px; text-align:right; color:#333; font-family:'Tahoma', sans-serif;">
                            <h3 style="color:#ff7a00; margin-bottom:15px;">🚫 تم إلغاء الحجز</h3>

                            @if($recipient_type === 'user')
                            <!-- رسالة موجهة للمستخدم -->
                            <p style="font-size:16px; line-height:2; color:#444;">
                                نود إعلامك أنه تم <strong style="color:#dc3545;">إلغاء الحجز</strong> لدى مركز:
                            </p>
                            <p style="font-size:17px; font-weight:bold; color:#000; margin-bottom:10px;">
                                {{ $organization->title }}
                            </p>
                            <p style="font-size:16px; line-height:2; color:#444;">
                                بتاريخ:
                            </p>
                            <p style="font-size:17px; font-weight:bold; color:#000; margin-bottom:10px;">
                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('d / m / Y - H:i') }}
                            </p>
                            <p style="font-size:16px; color:#555; line-height:2;">
                                تم إلغاء الحجز بنجاح. نتمنى أن نراك في مواعيد أخرى قريبًا 🌟
                            </p>

                            @elseif($recipient_type === 'organization')
                            <!-- رسالة موجهة للمركز -->
                            <p style="font-size:16px; line-height:2; color:#444;">
                                نود إعلامكم بأن المستخدم:
                            </p>
                            <p style="font-size:17px; font-weight:bold; color:#000; margin-bottom:10px;">
                                {{ $user->name ?? 'أحد المستخدمين' }}
                            </p>
                            <p style="font-size:16px; line-height:2; color:#444;">
                                قام <strong style="color:#dc3545;">بإلغاء الحجز</strong> لدى مركزكم بتاريخ:
                            </p>
                            <p style="font-size:17px; font-weight:bold; color:#000; margin-bottom:10px;">
                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('d / m / Y - H:i') }}
                            </p>
                            <p style="font-size:16px; color:#555; line-height:2;">
                                يمكنكم الآن تحديث جدول المواعيد أو إتاحة هذا الوقت لحجوزات أخرى في النظام.
                            </p>
                            @endif

                            <!-- بطاقة تفاصيل الموعد -->
                            <div
                                style="background-color:#f9f9f9; border:1px solid #eee; border-radius:10px; padding:20px; margin:25px 0; direction:rtl;">
                                <table width="100%" style="border-collapse:collapse;">
                                    <tr>
                                        <td style="font-size:15px; color:#555;">📅 تاريخ الموعد:</td>
                                        <td style="font-weight:bold; color:#333; text-align:right;">
                                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('d / m / Y - H:i') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:15px; color:#555;">🏢 المركز:</td>
                                        <td style="font-weight:bold; color:#333; text-align:right;">
                                            {{ $organization->title }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:15px; color:#555;">💰 مبلغ التأكيد:</td>
                                        <td style="font-weight:bold; color:#333; text-align:right;">
                                            {{ $appointment->price ?? 'غير محدد' }} ريال عمانى
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>


                    <!-- CTA Button -->
                    <div style="text-align:center; margin-top:25px;">
                        <a href="{{ config('app.frontend_url') }}/en/listofreservations?account_type={{ $recipient_type }}&id={{ $recipient_type === 'user' ? $user->id : $organization->id }}"
                            target="_blank"
                            style="background-color:#FFA552; color:#fff; text-decoration:none; padding:12px 30px; border-radius:8px; font-size:16px; font-weight:bold; display:inline-block;">
                            عرض جميع الحجوزات
                        </a>
                    </div>

                    <hr style="border:none; border-top:1px solid #eee; margin:35px 0;">

                    <p style="font-size:14px; color:#777; line-height:1.6;">
                        مع أطيب التحيات، 🌿<br>
                        <strong>فريق منصة آرام الخليج المحدودة</strong>
                    </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td align="center" style="background-color:#fafafa; padding:15px; font-size:13px; color:#999;">
                جميع الحقوق محفوظة © {{ date('Y') }} - منصة آرام الخليج المحدودة
            </td>
        </tr>

    </table>

    </td>
    </tr>
    </table>

</body>

</html>
