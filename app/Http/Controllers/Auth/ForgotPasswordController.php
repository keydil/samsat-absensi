<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email ini tidak terdaftar di sistem.',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);

        // Update / Insert Token di DB
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Kirim Email (Otomatis deteksi SendGrid API via HTTPS Port 443 atau fallback ke Standard Mail)
        try {
            $mailable = new ResetPasswordMail($user, $token);
            $apiKey = env('SENDGRID_API_KEY') ?: env('MAIL_PASSWORD');

            if ($apiKey && str_starts_with($apiKey, 'SG.')) {
                \App\Services\SendGridApiService::sendHtmlEmail($user->email, 'Reset Password - Absensi SAMSAT', $mailable->render());
            } else {
                Mail::to($user->email)->send($mailable);
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email reset password: ' . $e->getMessage()]);
        }

        return back()->with('status', 'Link reset password telah dikirim ke email Anda! Silakan periksa Inbox/Spam.');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kedaluwarsa.']);
        }

        // Cek Expired (60 Menit)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Link reset password sudah kedaluwarsa. Silakan minta link baru.']);
        }

        // Update Password User
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token yang sudah dipakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui! Silakan login kembali.');
    }
}
