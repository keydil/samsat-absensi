<?php

use App\Http\Controllers\Admin\DataUserController;
use App\Http\Controllers\Admin\GenerateQRController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Karyawan\ScanQRController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RekapAbsensiController;
use Illuminate\Support\Facades\Artisan;

// RUTE RAHASIA UNTUK SUNTIK DATA DI RAILWAY
Route::get('/rahasia/suntik-absen', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'DemoAbsenSeeder', '--force' => true]);
        return '<h2>BERHASIL!</h2><pre style="background:#222;color:#0f0;padding:20px;">' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<h2 style="color:red;">GAGAL! ADA ERROR:</h2><pre style="background:#ffebee;color:#c62828;padding:20px;">' . $e->getMessage() . '</pre>';
    }
});

// RUTE RAHASIA UNTUK MENGEMBALIKAN FOTO MUKA KLIEN YANG TERHAPUS
Route::get('/rahasia/restore-klien', function () {
    $ilham = \App\Models\User::where('name', 'LIKE', '%ilham%')->orWhere('email', 'LIKE', '%karyawan123%')->first();
    $yudi = \App\Models\User::where('name', 'LIKE', '%yudi%')->first();

    if ($ilham) {
        // 08 June 2026: "ilham khoerun" - Jam Masuk 10:49 - Status Hadir (FOTO MUKA)
        \App\Models\Absen::updateOrCreate(
            ['user_id' => $ilham->id, 'date' => '2026-06-08'],
            [
                'time' => '10:49:00',
                'status' => 'Hadir',
                'present_desc_system' => 'Memulai Scan QrCode Masuk pada : 10:49:00',
                'present_user_image' => 'https://res.cloudinary.com/drsjumtal/image/upload/v1780890946/absensi-faces/face_39_1780890945.jpg',
                'approval_status' => 'approved',
                'created_at' => '2026-06-08 10:49:00',
                'updated_at' => '2026-06-08 10:49:00',
            ]
        );

        // 03 June 2026: "ilham khoerun" - Izin (Disetujui) - "sakit"
        \App\Models\Absen::updateOrCreate(
            ['user_id' => $ilham->id, 'date' => '2026-06-03'],
            [
                'status' => 'Izin',
                'status_desc' => 'sakit',
                'bukti_surat' => 'https://res.cloudinary.com/drsjumtal/image/upload/v1780466710/absensi-surat/surat_2_1780466709.png',
                'approval_status' => 'approved',
                'created_at' => '2026-06-03 08:00:00',
                'updated_at' => '2026-06-03 08:00:00',
            ]
        );
    }

    if ($yudi) {
        // 04 June 2026: "yudi hermawan" - Sakit (Ditolak) - "demam tinggi"
        \App\Models\Absen::updateOrCreate(
            ['user_id' => $yudi->id, 'date' => '2026-06-04'],
            [
                'status' => 'Sakit',
                'status_desc' => 'demam tinggi',
                'bukti_surat' => 'https://res.cloudinary.com/drsjumtal/image/upload/v1780536958/absensi-surat/surat_36_1780536957.png',
                'approval_status' => 'rejected',
                'created_at' => '2026-06-04 08:00:00',
                'updated_at' => '2026-06-04 08:00:00',
            ]
        );
    }

    return '<h2 style="color:green; text-align:center; margin-top:50px;">DATA MUKA KLIEN DAN SURAT IZIN BERHASIL DISELAMATKAN! 🎉<br>Silakan cek Rekap Absensi lu!</h2>';
});

Route::get('/', fn() => view('welcome'))->name('beranda');
Route::middleware('web')->group(function () {
    // Redirect user admin
    Route::get('/admin', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard.admin');
        } else {
            return redirect()
                ->route('login')
                ->withErrors([
                    'loginAkses' => 'Sesi login anda telah berakhir, Silahkan login kembali.',
                ]);
        }
    });
    // Redirect user karyawan
    Route::get('/karyawan', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard.karyawan');
        } else {
            return redirect()
                ->route('login')
                ->withErrors([
                    'loginAkses' => 'Sesi login anda telah berakhir, Silakan login kembali.',
                ]);
        }
    });
});

// Route Login & Logout
Route::get('login', fn() => view('auth.login'))->name('login');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');

// Route Dashboard Admin & User
Route::get('dashboard/admin', [AdminController::class, 'index'])
    ->name('dashboard.admin')
    ->middleware('RoleUser:Admin');
Route::get('dashboard/karyawan', [UserController::class, 'index'])
    ->name('dashboard.user')
    ->middleware('RoleUser:Karyawan');

Route::middleware(['RoleUser:Admin'])->group(function () {
    // Admin Data User
    Route::get('/dashboard/admin/data/user', [DataUserController::class, 'index'])->name('admin.dataUser');
    Route::get('/dashboard/admin/data/user/create', [DataUserController::class, 'create'])->name('admin.dataUser.create');
    Route::post('/dashboard/admin/data/user', [DataUserController::class, 'store'])->name('admin.dataUser.store');
    Route::get('/dashboard/admin/data/user/{user}/edit', [DataUserController::class, 'edit'])->name('admin.dataUser.edit');
    Route::put('/dashboard/admin/data/user/{user}', [DataUserController::class, 'update'])->name('admin.dataUser.update');
    Route::delete('/dashboard/admin/data/user/{user}', [DataUserController::class, 'destroy'])->name('admin.dataUser.destroy');

    // QR Code — Auto Generate (form manual dihapus)
    Route::get('/dashboard/admin/generate-qr', [GenerateQRController::class, 'index'])->name('admin.generate-qr');
    Route::get('/dashboard/admin/generate-qr/display', [GenerateQRController::class, 'display'])->name('admin.generate-qr.display');
    Route::get('/dashboard/admin/generate-qr/show/{code}', [GenerateQRController::class, 'show'])->name('admin.generate-qr.show');
    Route::delete('/dashboard/admin/generate-qr/{id}', [GenerateQRController::class, 'destroy'])->name('admin.generate-qr.destroy');

    // API: Auto-generate & fetch QR aktif
    Route::get('/api/qr/current-active', [GenerateQRController::class, 'currentActive'])->name('api.qr.current-active');

    // Route Rekap Absensi
    Route::get('/dashboard/admin/rekap-absensi', [RekapAbsensiController::class, 'index'])->name('admin.rekap-absensi');
    Route::get('/dashboard/admin/rekap-absensi/export-excel', [RekapAbsensiController::class, 'exportExcel'])->name('admin.rekap-absensi.export');
    Route::delete('/dashboard/admin/rekap-absensi/clear-old', [RekapAbsensiController::class, 'clearOldData'])->name('admin.rekap-absensi.clear-old');
    Route::delete('/dashboard/admin/rekap-absensi/{user_id}/{date}', [RekapAbsensiController::class, 'destroy'])->name('admin.rekap-absensi.destroy');
    Route::post('/dashboard/admin/rekap-absensi/approve/{id}', [RekapAbsensiController::class, 'approve'])->name('admin.rekap-absensi.approve');
    Route::post('/dashboard/admin/rekap-absensi/reject/{id}', [RekapAbsensiController::class, 'reject'])->name('admin.rekap-absensi.reject');

    // Route Pengaturan (Settings)
    Route::get('/dashboard/admin/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings');
    Route::post('/dashboard/admin/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
});

Route::middleware(['RoleUser:Karyawan'])->group(function () {
    Route::get('/dashboard/karyawan/absensi', [ScanQRController::class, 'index'])->name('user.scanQR');
    Route::post('/dashboard/karyawan/absensi/scan-qr/check', [ScanQRController::class, 'check'])->name('user.scanCheck');
    Route::post('/dashboard/karyawan/absensi/scan-qr/store', [ScanQRController::class, 'store'])->name('user.scanStore');
    Route::post('/dashboard/karyawan/absensi/non-presence', [ScanQRController::class, 'storeNonPresence'])->name('user.storeNonPresence');
    Route::get('/dashboard/user/riwayat', [UserController::class, 'history'])->name('user.history');
});
