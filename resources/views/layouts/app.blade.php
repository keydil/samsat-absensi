<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Samsat Rancaekek</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @stack('styles')
</head>

<body class="flex h-screen bg-slate-50 text-slate-800 antialiased overflow-hidden" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
        class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col shrink-0">

        <div class="flex h-16 items-center justify-center border-b border-slate-800 bg-slate-950 px-6 shadow-md">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo" class="h-8 w-auto">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold tracking-widest text-yellow-500 uppercase">SAMSAT</span>
                    <span class="text-xs font-bold tracking-tight text-white">RANCAEKEK</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 no-scrollbar">

            @if (Auth::user()->role == 'Admin')
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-2">Administrator
                </p>

                <a href="{{ route('dashboard.admin') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('dashboard.admin') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.dataUser') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.dataUser*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Data Pegawai
                </a>

                <a href="{{ route('admin.generate-qr') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.generate-qr*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    QR Code Absen
                </a>

                <a href="{{ route('admin.rekap-absensi') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.rekap-absensi*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Rekap Absensi
                </a>

                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('admin.settings*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </a>
            @endif


            @if (Auth::user()->role == 'Karyawan')
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-2">Menu Pegawai</p>

                <a href="{{ route('dashboard.user') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('dashboard.user') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('user.scanQR') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('user.scanQR*') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Scan QR Absen
                </a>

                <a href="{{ route('user.history') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('user.history*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Saya
                </a>

                <a href="{{ route('user.globalHistory') }}"
                    class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->routeIs('user.globalHistory*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Absensi Global
                </a>
            @endif

        </nav>

        <div class="border-t border-slate-800 p-4">
            <a href="{{ route('logout') }}"
                class="flex w-full items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium text-red-400 hover:bg-slate-800 hover:text-red-300 transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar
            </a>
        </div>
    </aside>

    <div class="flex flex-1 flex-col overflow-hidden relative">
        <header
            class="flex h-16 flex-shrink-0 items-center justify-between border-b border-slate-200 bg-white px-6 shadow-sm z-10">
            <button @click="sidebarOpen = true" class="text-slate-500 focus:outline-none lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div class="hidden md:flex flex-col">
                <h2 class="text-lg font-bold text-slate-800 leading-tight">Sistem Absensi Digital</h2>
                <p class="text-xs text-slate-500">P3DW Rancaekek - Jawa Barat</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <div class="text-sm font-bold text-slate-800">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->role ?? 'Administrator' }}</div>
                </div>
                <div class="h-9 w-9 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Admin' }}&background=0284c7&color=fff"
                        alt="Avatar">
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6 lg:p-8">
            @yield('content')
            <div class="mt-8 pt-6 border-t border-slate-200 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Bapenda Prov. Jawa Barat.
            </div>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
