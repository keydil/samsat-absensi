# 🏢 Sistem Absensi Digital — Samsat Rancaekek

Sistem absensi digital berbasis QR Code untuk **P3DW Rancaekek, Bapenda Provinsi Jawa Barat**. Dibangun menggunakan Laravel 12 dengan fitur keamanan Face Biometric dan Radius Locking.

🔗 **Live Demo:** [web-production-721d5.up.railway.app](https://web-production-721d5.up.railway.app)

---

## ✨ Fitur Utama

- **QR Code Absensi** — Admin generate QR, karyawan scan untuk absen masuk & pulang
- **Face Biometric** — Verifikasi wajah menggunakan `face-api.js` untuk mencegah titip absen
- **Radius Locking** — Absensi hanya bisa dilakukan dalam radius 50 meter dari kantor (GPS)
- **Status QR Otomatis** — QR memiliki 3 status: `Terjadwal`, `Aktif`, `Expired`
- **Pencegahan QR Dobel** — Admin tidak bisa buat QR tipe yang sama di hari yang sama jika masih aktif/terjadwal
- **CRUD Manajemen User** — Admin bisa tambah, edit, hapus data pegawai
- **Rekap Absensi** — Export data kehadiran ke format Excel
- **Real-time Location Status** — Karyawan bisa lihat berapa jarak mereka dari kantor secara realtime
- **Role-based Access** — Dashboard terpisah untuk Admin dan Karyawan

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 12 (PHP 8.2) |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL |
| QR Code | `simplesoftwareio/simple-qrcode` |
| QR Scanner | `html5-qrcode` |
| Face Detection | `face-api.js` |
| Export Excel | `maatwebsite/excel` |
| Deploy | Railway |

---

## 🚀 Instalasi Lokal

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL / XAMPP

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/keydil/samsat-absensi.git
cd samsat-absensi
```

**2. Install dependencies**
```bash
composer install
npm install
```

**3. Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi `.env`**
```env
APP_NAME="Absensi Samsat"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_samsat
DB_USERNAME=root
DB_PASSWORD=

# Koordinat kantor (sesuaikan dengan lokasi kantor)
OFFICE_LAT=-6.9824624
OFFICE_LNG=107.7540507
OFFICE_RADIUS_METER=50
```

**5. Buat database & jalankan migration**
```bash
php artisan migrate
php artisan db:seed
```

**6. Build assets & jalankan server**
```bash
npm run build
php artisan serve
```

Akses di: `http://localhost:8000`

---

## 👤 Akun Default (Seeder)

| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `password` |
| Karyawan | `karyawan` | `password` |

> ⚠️ Segera ganti password setelah login pertama kali!

---

## 📁 Struktur Folder Penting

```
app/
├── Http/Controllers/
│   ├── Admin/
│   │   ├── DataUserController.php    # CRUD manajemen pegawai
│   │   ├── GenerateQRController.php  # Generate & hapus QR Code
│   │   └── RekapAbsensiController.php
│   ├── Auth/AuthController.php
│   ├── Dashboard/
│   │   ├── AdminController.php
│   │   └── UserController.php
│   └── Karyawan/ScanQRController.php # Scan QR + validasi GPS + foto wajah
├── Models/
│   ├── Absen.php
│   ├── QrCode.php
│   └── User.php
resources/views/
├── content/admin/      # View dashboard admin
├── content/karyawan/   # View dashboard karyawan
└── welcome.blade.php   # Landing page
```

---

## ⚙️ Konfigurasi Koordinat Kantor

Untuk mengubah lokasi kantor, edit nilai berikut di `.env`:

```env
OFFICE_LAT=-6.9824624    # Latitude kantor
OFFICE_LNG=107.7540507   # Longitude kantor
OFFICE_RADIUS_METER=50   # Radius maksimal absensi (meter)
```

Cara cari koordinat: buka Google Maps → klik lokasi kantor → copy koordinat yang muncul.

---

## 🌐 Deploy ke Railway

**1. Fork/push repo ke GitHub**

**2. Buat project baru di [Railway](https://railway.app)**
- New Project → Deploy from GitHub → pilih repo ini
- Tambah service MySQL

**3. Set environment variables di Railway:**
```
APP_KEY=base64:xxxx           # php artisan key:generate --show
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MySQL.RAILWAY_PRIVATE_DOMAIN}}
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your-mysql-password

OFFICE_LAT=-6.9824624
OFFICE_LNG=107.7540507
OFFICE_RADIUS_METER=50
```

**4. Set Custom Start Command di Settings:**
```bash
php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

---

## 📸 Screenshot

| Landing Page | Dashboard Admin | Scan QR Karyawan |
|---|---|---|
| ![Landing](public/images/logo-bapenda.png) | Dashboard Admin | Scan QR |

---

## 📄 Lisensi

Project ini dibuat untuk keperluan internal Samsat Rancaekek dibuat oleh **Fadhil Firdaus Adha**.

---

<p align="center">Made with ❤️ for Samsat Rancaekek</p>

---

# 🎓 LAMPIRAN C.5: HASIL IMPLEMENTASI KODE PROGRAM (TUGAS AKHIR)
**Sistem Informasi Absensi Pegawai Berbasis Dynamic QR-Code & Cloud Email Delivery**
**Instansi: SAMSAT (Laravel Web Framework)**

---

## DOKUMENTASI POTONGAN KODE PROGRAM UTAMA (CORE MODULES)

Potongan kode program di bawah ini merupakan bagian inti (*core modules*) dari sistem yang dikembangkan, mencakup algoritma enkripsi dinamis QR-Code anti-joki, integrasi pengiriman email transaksional cloud REST API, dan modul pemulihan keamanan akun.

---

### 1. Modul Keamanan & Generator QR-Code Dinamis (HMAC Signature Anti-Joki)
* **Nama File:** `app/Http/Controllers/Admin/GenerateQRController.php`
* **Deskripsi:** Algoritma ini memproduksi payload QR Code yang dibungkus dengan stempel waktu (*timestamp*) dan tanda tangan digital HMAC SHA-256. Hal ini memastikan QR Code berubah secara dinamis dan tidak dapat difoto/di-screenshot oleh pegawai untuk dititipkan (anti-joki absensi).

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode as QrCodeModel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQRController extends Controller
{
    /**
     * API Endpoint: Cek & Auto-Generate QR Code Dinamis berbasis Jadwal Sesi.
     * Menggunakan Enkripsi HMAC SHA-256 untuk Mencegah Titip Absen (Anti-Joki).
     */
    public function currentActive()
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        // Auto-expire QR Code yang sudah melewati batas waktu sesi
        QrCodeModel::where('status', 'active')
            ->where('end_time', '<', $now)
            ->update(['status' => 'expired']);

        $day = strtoupper($now->format('l'));

        // Cek Jadwal Sesi Absensi (Absen Masuk / Absen Pulang)
        $sessions = [
            [
                'type'  => 'in_present',
                'label' => 'Absen Masuk',
                'start' => \App\Models\Setting::get("QR_SESSION_IN_START_$day", '07:00'),
                'end'   => \App\Models\Setting::get("QR_SESSION_IN_END_$day", '09:00'),
            ],
            [
                'type'  => 'out_present',
                'label' => 'Absen Pulang',
                'start' => \App\Models\Setting::get("QR_SESSION_OUT_START_$day", '16:00'),
                'end'   => \App\Models\Setting::get("QR_SESSION_OUT_END_$day", '17:00'),
            ],
        ];

        // Verifikasi kesesuaian waktu akses dengan jadwal sesi
        $activeSession = null;
        foreach ($sessions as $session) {
            if ($currentTime >= $session['start'] && $currentTime < $session['end']) {
                $activeSession = $session;
                break;
            }
        }

        if (!$activeSession) {
            return response()->json([
                'active' => false,
                'message' => 'Di luar jam absensi.',
                'current_time' => $now->format('H:i:s')
            ]);
        }

        // Ambil atau buat record QR Code aktif untuk hari ini
        $qr = QrCodeModel::where('date', $today)
            ->where('present', $activeSession['type'])
            ->where('status', 'active')
            ->first();

        if (!$qr) {
            $qr = QrCodeModel::create([
                'code_qr'    => Str::uuid()->toString(),
                'present'    => $activeSession['type'],
                'date'       => $today,
                'start_time' => Carbon::parse($today . ' ' . $activeSession['start']),
                'end_time'   => Carbon::parse($today . ' ' . $activeSession['end']),
                'status'     => 'active',
            ]);
        }

        // --- SISTEM ANTI-JOKI (DYNAMIC CRYPTOGRAPHIC QR PAYLOAD) ---
        // Bungkus ID QR dengan UNIX Timestamp terkini dan Kunci Rahasia Aplikasi (HMAC SHA-256)
        $timestamp = time();
        $payload   = $qr->id . '|' . $timestamp;
        $signature = hash_hmac('sha256', $payload, config('app.key'));
        $secureQrString = $payload . '|' . $signature;

        // Generate gambar SVG QR-Code secara real-time
        $qrSvg = QrCode::format('svg')->size(300)->generate($secureQrString);

        return response()->json([
            'active' => true,
            'data'   => [
                'qr_id'        => $qr->id,
                'code_qr'      => $secureQrString,
                'session_label'=> $activeSession['label'],
                'svg'          => base64_encode($qrSvg),
            ],
            'current_time' => $now->format('H:i:s'),
        ]);
    }
}
```

---

### 2. Modul Integrasi Service Email Cloud REST API (Bypass Firewall Container)
* **Nama File:** `app/Services/SendGridApiService.php`
* **Deskripsi:** Layanan pengiriman email transaksional berbasis HTTP REST API (Port 443 HTTPS). Didesain khusus untuk mengatasi keterbatasan blokir port SMTP bawaan server cloud (seperti Render/PaaS) tanpa mengurangi tingkat keberhasilan (*deliverability*) pengiriman email ke berbagai provider inbox.

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SendGridApiService
{
    /**
     * Mengirimkan Email Transaksional HTML via Cloud REST API (Port 443 HTTPS).
     *
     * @param string $toEmail   Alamat email penerima
     * @param string $subject   Subjek email
     * @param string $htmlBody  Konten HTML email (Blade render)
     * @return bool
     * @throws \Exception
     */
    public static function sendHtmlEmail(string $toEmail, string $subject, string $htmlBody): bool
    {
        $apiKey    = env('SENDGRID_API_KEY') ?: env('MAIL_PASSWORD');
        $fromEmail = env('MAIL_FROM_ADDRESS', 'zibgitumbal@gmail.com');
        $fromName  = env('MAIL_FROM_NAME', 'SAMSAT Absensi');

        // Eksekusi HTTP Request POST ke Endpoint Cloud API SendGrid
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->post('https://api.sendgrid.com/v3/mail/send', [
                'personalizations' => [
                    [
                        'to' => [
                            ['email' => $toEmail]
                        ]
                    ]
                ],
                'from' => [
                    'email' => $fromEmail,
                    'name'  => $fromName
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type'  => 'text/html',
                        'value' => $htmlBody
                    ]
                ]
            ]);

        // Status 202 Accepted menandakan email telah berhasil diterima oleh gateway cloud
        if ($response->successful() || $response->status() === 202) {
            return true;
        }

        $errorMsg = $response->json('errors.0.message') ?: $response->body();
        throw new \Exception('SendGrid HTTP API Error (' . $response->status() . '): ' . $errorMsg);
    }
}
```

---

### 3. Modul Pemulihan Akun & Reset Password Berbasis Token Terenkripsi
* **Nama File:** `app/Http/Controllers/Auth/ForgotPasswordController.php`
* **Deskripsi:** Controller ini mengelola alur *Forgot Password*. Sistem membuat token acak terenkripsi yang memiliki batas waktu kedaluwarsa 60 menit, disimpan dalam database, kemudian memicu pengiriman link verifikasi via Cloud API.

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Memproses permintaan link reset password dari pengguna.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email ini tidak terdaftar di sistem.',
        ]);

        $user  = User::where('email', $request->email)->first();
        $token = Str::random(60);

        // Simpan / Update Token Reset Password di Database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email'      => $request->email,
                'token'      => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Trigger Pengiriman Email via Hybrid API Service
        try {
            $mailable = new ResetPasswordMail($user, $token);
            $apiKey   = env('SENDGRID_API_KEY') ?: env('MAIL_PASSWORD');

            // Deteksi Otomatis: Pake SendGrid REST API jika Key terdeteksi
            if ($apiKey && str_starts_with($apiKey, 'SG.')) {
                \App\Services\SendGridApiService::sendHtmlEmail(
                    $user->email, 
                    'Reset Password - Absensi SAMSAT', 
                    $mailable->render()
                );
            } else {
                \Illuminate\Support\Facades\Mail::to($user->email)->send($mailable);
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email reset password: ' . $e->getMessage()]);
        }

        return back()->with('status', 'Link reset password telah dikirim ke email Anda!');
    }

    /**
     * Memverifikasi token dan memperbarui password baru pengguna.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        // Batasi Validitas Token Maksimal 60 Menit
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Link reset password sudah kedaluwarsa.']);
        }

        // Update Password Baru Pengguna
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus Token dari Database setelah Berhasil Digunakan
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui!');
    }
}
```

---

### 4. Modul Restriksi Hak Akses (Role-Based Access Control / RBAC Middleware)
* **Nama File:** `app/Http/Middleware/RoleUser.php`
* **Deskripsi:** Middleware ini bertugas mencegat dan memvalidasi peran (*role*) akun pengguna (Admin, Kepala, Karyawan) sebelum mengizinkan eksekusi rute halaman tertentu demi menjaga keamanan sistem data absensi.

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleUser
{
    /**
     * Memeriksa apakah pengguna memiliki salah satu role yang diizinkan.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'loginAkses' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.'
            ]);
        }

        $user = Auth::user();

        // Izinkan jika role pengguna sesuai dengan daftar $roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect sesuai hak akses masing-masing jika tidak memiliki izin
        if ($user->role === 'Karyawan') {
            return redirect()->route('dashboard.karyawan');
        }

        return redirect()->route('dashboard.admin');
    }
}
```