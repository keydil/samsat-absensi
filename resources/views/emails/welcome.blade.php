<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Absensi SAMSAT Anda</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #334155;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 560px; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 32px 24px 32px; border-b: 1px solid #f1f5f9; background-color: #ffffff;">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <div style="font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                                            SAMSAT <span style="color: #2563eb;">ABSENSI</span>
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="font-size: 12px; font-weight: 600; color: #64748b; background-color: #f1f5f9; padding: 4px 10px; border-radius: 9999px;">
                                            Resmi
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
                                Halo, {{ $user->name }} 👋
                            </h1>
                            <p style="margin: 0 0 24px 0; font-size: 15px; line-height: 1.6; color: #475569;">
                                Akun Anda telah berhasil didaftarkan oleh Administrator ke dalam <strong>Sistem Absensi Digital SAMSAT</strong>. Berikut adalah kredensial akun Anda:
                            </p>

                            <!-- Credentials Box -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 28px;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding-bottom: 10px; font-size: 13px; color: #64748b; font-weight: 600;">NIP / USERNAME</td>
                                        <td align="right" style="padding-bottom: 10px; font-size: 14px; font-weight: 700; color: #0f172a; font-family: monospace;">
                                            {{ $user->username }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-bottom: 10px; font-size: 13px; color: #64748b; font-weight: 600;">KODE USER</td>
                                        <td align="right" style="padding-bottom: 10px; font-size: 14px; font-weight: 700; color: #0f172a; font-family: monospace;">
                                            {{ $user->code_name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 13px; color: #64748b; font-weight: 600;">PASSWORD SEMENTARA</td>
                                        <td align="right" style="font-size: 14px; font-weight: 700; color: #2563eb; font-family: monospace;">
                                            {{ $rawPassword }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin-bottom: 28px;">
                                <a href="{{ config('app.url') }}/login" target="_blank" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 15px; padding: 12px 32px; border-radius: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                                    Masuk ke Sistem Absensi &rarr;
                                </a>
                            </div>

                            <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #94a3b8; text-align: center;">
                                Demi keamanan, harap segera perbarui password Anda setelah berhasil masuk.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                Email ini dikirim secara otomatis oleh Sistem Absensi SAMSAT V2.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
