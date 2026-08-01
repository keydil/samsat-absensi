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