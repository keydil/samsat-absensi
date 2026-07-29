<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmployeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $rawPassword;

    public function __construct(User $user, string $rawPassword)
    {
        $this->user = $user;
        $this->rawPassword = $rawPassword;
    }

    public function build()
    {
        return $this->subject('Selamat Datang - Akun Absensi SAMSAT Anda Telah Aktif')
                    ->view('emails.welcome');
    }
}
