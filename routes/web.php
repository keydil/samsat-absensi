<?php

use App\Http\Controllers\Admin\DataUserController;
use App\Http\Controllers\Admin\GenerateQRController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Karyawan\ScanQRController;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('beranda');
Route::middleware('web')->group(function () {
    // Redirect user admin
    Route::get('/admin', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard.admin');
        } else {
            return redirect()->route('login')->withErrors([
                'loginAkses' => 'Sesi login anda telah berakhir, Silahkan login kembali.'
            ]);
        }
    });
    // Redirect user karyawan
    Route::get('/karyawan', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard.karyawan');
        } else {
            return redirect()->route('login')->withErrors([
                'loginAkses' => 'Sesi login anda telah berakhir, Silakan login kembali.'
            ]);
        }
    });
});

// Route Login & Logout
Route::get('login', fn() => view('auth.login'))->name('login');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');

// Route Dashboard Admin & User
Route::get('dashboard/admin', [AdminController::class, 'index'])->name('dashboard.admin')->middleware('RoleUser:Admin');
Route::get('dashboard/karyawan', [UserController::class, 'index'])->name('dashboard.user')->middleware('RoleUser:Karyawan');


Route::middleware(['RoleUser:Admin'])->group(function () {
    // Admin Data User
    Route::get('/dashboard/admin/data/user', [DataUserController::class, 'index'])->name('admin.dataUser');
    // Generate QR-Code Admin
    Route::get('/dashboard/admin/generate-qr', [GenerateQRController::class, 'index'])->name('admin.generate-qr');
    Route::post('/dashboard/admin/generate-qr/store', [GenerateQRController::class, 'store'])->name('admin.generate-qr.store');
    Route::get('/dashboard/admin/generate-qr/show/{code}', [GenerateQRController::class, 'show'])->name('admin.generate-qr.show');
});

Route::middleware(['RoleUser:Karyawan'])->group(function() {
    Route::get('/dashboard/karyawan/absensi', [ScanQRController::class, 'index'])->name('user.scanQR');
    Route::post('/dashboard/karyawan/absensi/scan-qr/check', [ScanQRController::class, 'check'])->name('user.scanCheck');
    Route::post('/dashboard/karyawan/absensi/scan-qr/store', [ScanQRController::class, 'store'])->name('user.scanStore');
});

