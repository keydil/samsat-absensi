<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) { // Hapus type hint :void agar lebih fleksibel (opsional)
        
        $middleware->alias([
            // Alias 'auth' biarkan saja jika memang pakai custom middleware
            'auth' => \App\Http\Middleware\Authenticate::class, 
            
            // --- TAMBAHKAN BARIS INI ---
            'RoleUser' => \App\Http\Middleware\RoleUser::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tangkap error 419 (Page Expired) agar tidak tampil halaman error developer
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return back()
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->withErrors(['email' => 'Sesi formulir Anda telah berakhir karena didiamkan terlalu lama. Silakan coba kirim ulang.']);
        });
    })->create();