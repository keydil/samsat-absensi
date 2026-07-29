<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Absensi SAMSAT</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #334155;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 560px; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; border-bottom: 1px solid #f1f5f9; background-color: #ffffff;">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <div style="font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                                            SAMSAT <span style="color: #2563eb;">ABSENSI</span>
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="font-size: 12px; font-weight: 600; color: #dc2626; background-color: #fef2f2; padding: 4px 10px; border-radius: 9999px;">
                                            Reset Password
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="margin: 0 0 16px 0; font-size: 22px; font-weight: 700; color: #0f172a; line-height: 1.3;">
                                Permintaan Reset Password 🔑
                            </h1>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #475569;">
                                Halo <strong>{{ $user->name }}</strong>, kami menerima permintaan untuk mereset password akun Absensi SAMSAT Anda.
                            </p>
                            <p style="margin: 0 0 28px 0; font-size: 15px; line-height: 1.6; color: #475569;">
                                Silakan klik tombol di bawah ini untuk membuat password baru. Link ini berlaku selama <strong>60 menit</strong>:
                            </p>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 15px; padding: 14px 36px; border-radius: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                                    Buat Password Baru &rarr;
                                </a>
                            </div>

                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                                <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #64748b;">
                                    Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:<br>
                                    <a href="{{ $resetUrl }}" style="color: #2563eb; word-break: break-all;">{{ $resetUrl }}</a>
                                </p>
                            </div>

                            <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #94a3b8; text-align: center;">
                                Jika Anda tidak merasa meminta reset password, abaikan email ini. Akun Anda tetap aman.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                &copy; {{ date('Y') }} SAMSAT Absensi Digital. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
