<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pegawai - Samsat Rancaekek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-800 bg-slate-50">

    <div
        class="fixed inset-0 -z-10 h-full w-full bg-white bg-[linear-gradient(to_right,#f0f0f0_1px,transparent_1px),linear-gradient(to_bottom,#f0f0f0_1px,transparent_1px)] bg-[size:6rem_4rem]">
        <div
            class="absolute bottom-0 left-0 right-0 top-0 bg-[radial-gradient(circle_800px_at_100%_200px,#dbeafe,transparent)]">
        </div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6">

        <div class="mb-8 text-center">
            <a href="/" class="inline-flex flex-col items-center gap-3 group">
                <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo Bapenda"
                    class="h-20 w-auto drop-shadow-lg transition-transform group-hover:scale-105">
                <div class="flex flex-col">
                    <span class="text-xs font-bold tracking-[0.2em] text-yellow-600 uppercase">Sistem Absensi
                        Digital</span>
                    <span class="text-2xl font-black tracking-tight text-blue-900">SAMSAT RANCAEKEK</span>
                </div>
            </a>
        </div>

        <div
            class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-slate-200 shadow-2xl shadow-blue-900/10 rounded-3xl p-8 relative overflow-hidden">

            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 via-blue-600 to-blue-900">
            </div>

            <h2 class="text-xl font-bold text-slate-800 text-center mb-6">Silakan Masuk</h2>

            @if ($errors->has('loginError') || $errors->has('loginAkses'))
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm text-red-700 font-medium">
                        {{ $errors->first('loginError') ?: $errors->first('loginAkses') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="text" class="block text-sm font-semibold text-slate-700 mb-1">Username / Kode
                        User</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="text" type="text" name="text" value="{{ old('text') }}" required
                            autofocus
                            class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all placeholder:text-slate-400"
                            placeholder="Masukkan NIP atau Username">
                    </div>
                    @error('text')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required
                            class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all placeholder:text-slate-400"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 flex justify-center items-center gap-2 rounded-xl shadow-lg shadow-blue-500/20 text-white font-bold bg-gradient-to-r from-blue-700 to-blue-900 hover:from-blue-800 hover:to-blue-950 transform hover:-translate-y-0.5 transition-all">
                    <span>MASUK APLIKASI</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="/" class="text-sm text-slate-500 ...">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>

        <p class="mt-8 text-xs text-slate-400 text-center">
            &copy; {{ date('Y') }} Bapenda Provinsi Jawa Barat.
        </p>
    </div>

</body>

</html>
