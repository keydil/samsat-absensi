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
    Route::get('/dashboard/admin/rekap-absensi/export', [RekapAbsensiController::class, 'export'])->name('admin.rekap-absensi.export');
});

Route::middleware(['RoleUser:Karyawan'])->group(function () {
    Route::get('/dashboard/karyawan/absensi', [ScanQRController::class, 'index'])->name('user.scanQR');
    Route::post('/dashboard/karyawan/absensi/scan-qr/check', [ScanQRController::class, 'check'])->name('user.scanCheck');
    Route::post('/dashboard/karyawan/absensi/scan-qr/store', [ScanQRController::class, 'store'])->name('user.scanStore');
    Route::post('/dashboard/karyawan/absensi/non-presence', [ScanQRController::class, 'storeNonPresence'])->name('user.storeNonPresence');
    Route::get('/dashboard/user/riwayat', [UserController::class, 'history'])->name('user.history');
});
