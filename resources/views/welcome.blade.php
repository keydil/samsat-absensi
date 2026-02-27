<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Absensi Digital Samsat Rancaekek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-yellow-400 selection:text-blue-900">

    <div class="fixed inset-0 -z-10 h-full w-full bg-white bg-[linear-gradient(to_right,#f0f0f0_1px,transparent_1px),linear-gradient(to_bottom,#f0f0f0_1px,transparent_1px)] bg-[size:6rem_4rem]">
        <div class="absolute bottom-0 left-0 right-0 top-0 bg-[radial-gradient(circle_800px_at_100%_200px,#dbeafe,transparent)]"></div>
    </div>

    <nav class="sticky top-0 z-50 w-full border-b border-slate-200/80 bg-white/80 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-bapenda.png') }}" class="h-11 w-auto drop-shadow-sm" alt="Logo Bapenda">
                <div class="hidden flex-col md:flex">
                    <span class="text-xs font-bold tracking-widest text-yellow-600">P3DW RANCAEKEK</span>
                    <span class="text-sm font-extrabold tracking-tight text-blue-900">BAPENDA PROVINSI JAWA BARAT</span>
                </div>
            </div>
            
            <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-blue-900 px-6 py-2 font-medium text-white transition duration-300 hover:bg-blue-800 hover:shadow-lg hover:shadow-blue-500/30">
                <span class="relative">Login Pegawai</span>
            </a>
        </div>
    </nav>

    <main class="relative isolate px-6 pt-14 lg:px-8">
        <div class="mx-auto max-w-4xl py-20 sm:py-28 text-center">
            
            <div class="mb-8 flex justify-center">
                <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-slate-600 ring-1 ring-slate-900/10 hover:ring-slate-900/20 bg-white/50 backdrop-blur-sm">
                    Versi Terbaru 2.0 <a href="#" class="font-semibold text-blue-600"><span class="absolute inset-0" aria-hidden="true"></span>Read more <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>

            <h1 class="text-4xl font-black tracking-tight text-slate-900 sm:text-6xl">
                Sistem Absensi Digital <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-yellow-500">Samsat Rancaekek</span>
            </h1>
            
            <p class="mt-6 text-lg leading-8 text-slate-600 max-w-2xl mx-auto">
                Platform pencatatan kehadiran Aparatur Sipil Negara (ASN) dan Non-ASN yang terintegrasi, 
                <span class="font-semibold text-blue-800">Real-time</span>, dan berbasis 
                <span class="font-semibold text-blue-800">Geotagging</span>.
            </p>

            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="{{ route('login') }}" class="rounded-xl bg-gradient-to-br from-blue-900 to-blue-700 px-8 py-3.5 text-base font-bold text-white shadow-xl shadow-blue-500/20 transition-all hover:scale-105 hover:shadow-blue-500/40 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    🚀 Masuk ke Aplikasi
                </a>
                <a href="#" class="text-sm font-semibold leading-6 text-slate-900 group">
                    Panduan Penggunaan <span aria-hidden="true" class="inline-block transition-transform group-hover:translate-x-1">→</span>
                </a>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-6 lg:px-8 pb-24">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                
                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md hover:border-blue-200 group">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-50 opacity-50 transition-all group-hover:bg-blue-100"></div>
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>          
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Face Biometric</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Validasi kehadiran menggunakan teknologi pengenalan wajah untuk mencegah kecurangan (anti-spoofing).</p>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md hover:border-yellow-200 group">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-yellow-50 opacity-50 transition-all group-hover:bg-yellow-100"></div>
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-500 text-white shadow-lg shadow-yellow-500/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Radius Locking</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Presensi hanya dapat dilakukan dalam radius 50 meter dari titik koordinat kantor Samsat Rancaekek.</p>
                </div>

                <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md hover:border-blue-200 group">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-blue-50 opacity-50 transition-all group-hover:bg-blue-100"></div>
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-900 text-white shadow-lg shadow-blue-900/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Real-time Report</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Data kehadiran, keterlambatan, dan pulang cepat terekap otomatis dan dapat diunduh dalam format PDF/Excel.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-6 text-center lg:px-8">
            <p class="text-xs leading-5 text-slate-500">
                &copy; {{ date('Y') }} Pusat Pengelolaan Pendapatan Daerah Wilayah (P3DW) Rancaekek. <br class="sm:hidden"> Bapenda Provinsi Jawa Barat.
            </p>
        </div>
    </footer>

</body>
</html>