<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>رمز التحقق</title>
</head>

<body style="font-family: 'Tahoma', Arial, sans-serif; direction:rtl; background-color: #f7f7f7; margin: 0; padding: 0;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <tr>
            <td style="background: #feb803; color: #ffffff; text-align: center; padding: 20px; font-size: 22px; font-weight: bold;">
                رمز التحقق
            </td>
        </tr>
        <tr>
            <td style="padding: 30px; text-align: center; color: #333;">
                <h2 style="margin-bottom: 10px;">كود التحقق الخاص بك:</h2>
                <p style="font-size: 28px; font-weight: bold; color: #feb803; margin: 15px 0;">
                    {{ $otp }}
                </p>
                <p style="font-size: 16px; color: #555; margin-top: 20px;">
                    هذا الكود صالح لمدة <strong>5 دقائق</strong> فقط.<br>
                    الرجاء عدم مشاركته مع أي شخص حفاظًا على أمان حسابك.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background: #f1f1f1; text-align: center; font-size: 12px; color: #777; padding: 15px;">
                هذه الرسالة أُرسلت إليك تلقائيًا، لا تقم بالرد عليها.
            </td>
        </tr>
    </table>

</body>

</html>
