<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Password Baru - Samsat Rancaekek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-800 bg-slate-50">

    <div class="fixed inset-0 -z-10 h-full w-full bg-white bg-[linear-gradient(to_right,#f0f0f0_1px,transparent_1px),linear-gradient(to_bottom,#f0f0f0_1px,transparent_1px)] bg-[size:6rem_4rem]">
        <div class="absolute bottom-0 left-0 right-0 top-0 bg-[radial-gradient(circle_800px_at_100%_200px,#dbeafe,transparent)]"></div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6">

        <div class="mb-8 text-center">
            <a href="/" class="inline-flex flex-col items-center gap-3 group">
                <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo Bapenda"
                    class="h-20 w-auto drop-shadow-lg transition-transform group-hover:scale-105">
                <div class="flex flex-col">
                    <span class="text-xs font-bold tracking-[0.2em] text-yellow-600 uppercase">Sistem Absensi Digital</span>
                    <span class="text-2xl font-black tracking-tight text-blue-900">SAMSAT RANCAEKEK</span>
                </div>
            </a>
        </div>

        <div class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-slate-200 shadow-2xl shadow-blue-900/10 rounded-3xl p-8 relative overflow-hidden">

            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-yellow-400 via-blue-600 to-blue-900"></div>

            <h2 class="text-xl font-bold text-slate-800 text-center mb-2">Buat Password Baru</h2>
            <p class="text-xs text-slate-500 text-center mb-6">Silakan masukkan password baru untuk akun Anda.</p>

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                    <svg class="h-5 w-5 text-rose-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm text-rose-700 font-medium">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email Anda</label>
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                        class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-100 text-slate-500 font-medium cursor-not-allowed">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                    <input id="password" type="password" name="password" required autofocus
                        class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all placeholder:text-slate-400"
                        placeholder="Minimal 6 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="block w-full px-4 py-3 rounded-xl border-slate-200 bg-slate-50 text-slate-900 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all placeholder:text-slate-400"
                        placeholder="Ketik ulang password baru">
                </div>

                <button type="submit" id="btn-submit"
                    class="w-full py-3 px-4 flex justify-center items-center gap-2 rounded-xl shadow-lg shadow-blue-500/20 text-white font-bold bg-gradient-to-r from-blue-700 to-blue-900 hover:from-blue-800 hover:to-blue-950 transform hover:-translate-y-0.5 transition-all">
                    <span id="btn-text">SIMPAN PASSWORD BARU</span>
                    <svg id="btn-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg id="btn-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>

        <p class="mt-8 text-xs text-slate-400 text-center">
            &copy; {{ date('Y') }} Bapenda Provinsi Jawa Barat.
        </p>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            const text = document.getElementById('btn-text');
            const icon = document.getElementById('btn-icon');
            const spinner = document.getElementById('btn-spinner');

            btn.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
            text.textContent = 'MENYIMPAN...';
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    </script>
</body>

</html>
