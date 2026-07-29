<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->email,
        ]);

        return $this->subject('Reset Password - Absensi SAMSAT')
                    ->view('emails.reset-password', [
                        'resetUrl' => $resetUrl
                    ]);
    }
}
