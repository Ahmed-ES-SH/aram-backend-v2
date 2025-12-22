<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رد على طلب الحجز</title>
</head>

<body style="margin:0; direction:rtl; padding:0; background-color:#f5f5f5; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Card Container -->
                <table cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color:#FFA552; padding:20px;">
                            <img src="https://aram-gulf.com/logo.png" alt="Logo" width="80" style="margin-bottom:10px;">
                            <h2 style="margin:0; color:#ffffff; font-size:22px;">منصة آرام الخليج المحدودة</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px 40px; text-align:right; color:#333;">
                            <h3 style="color:#FFA552; margin-bottom:15px;">
                                @if($status === 'confirmed')
                                ✅ تم قبول حجزك!
                                @else
                                ❌ نأسف، تم رفض حجزك
                                @endif
                            </h3>

                            <p style="font-size:16px; line-height:1.7;">
                                مرحبًا <strong>{{ $appointment->user->name ?? 'عزيزي المستخدم' }}</strong>،
                                <br><br>
                                نود إعلامك بأن طلب الحجز الخاص بك لدى مركز <strong>{{ $organization->title }}</strong>
                                بتاريخ <strong>{{ \Carbon\Carbon::parse($appointment->start_time)->format('Y-m-d H:i') }}</strong>
                                قد تم
                                @if($status === 'confirmed')
                                <span style="color:#28a745; font-weight:bold;">قبوله ✅</span>.
                                @else
                                <span style="color:#dc3545; font-weight:bold;">رفضه ❌</span>.
                                @endif
                            </p>

                            @if($status === 'confirmed')
                            <p style="font-size:15px; line-height:1.6; color:#555;">
                                يمكنك الآن مراجعة تفاصيل الموعد من خلال المنصة وتأكيد الحضور أو التواصل مع المركز في حال وجود أي استفسارات.
                            </p>
                            @else
                            <p style="font-size:15px; line-height:1.6; color:#555;">
                                نعتذر عن عدم قبول الحجز في هذا الوقت. يمكنك محاولة اختيار موعد آخر يناسبك أو التواصل مع المركز مباشرة.
                            </p>
                            @endif

                            <!-- CTA Button -->
                            <div style="text-align:center; margin-top:25px;">
                                <a href="{{ config('app.frontend_url') }}/appointments/{{ $appointment->id }}"
                                    target="_blank"
                                    style="background-color:#FFA552; color:#fff; text-decoration:none; padding:12px 30px; border-radius:8px; font-size:16px; font-weight:bold; display:inline-block;">
                                    عرض تفاصيل الحجز
                                </a>
                            </div>

                            <hr style="border:none; border-top:1px solid #eee; margin:35px 0;">

                            <p style="font-size:14px; color:#777; line-height:1.6;">
                                نتمنى لك يومًا رائعًا 🌞<br>
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