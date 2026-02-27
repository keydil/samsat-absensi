@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        
        <div class="relative overflow-hidden rounded-2xl bg-slate-900 px-6 py-10 shadow-xl sm:px-12 sm:py-12">
            <div class="absolute -top-24 -left-20 h-64 w-64 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute top-1/2 -right-20 h-64 w-64 rounded-full bg-emerald-600/20 blur-3xl"></div>

            <div class="relative z-10 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-white">
                        Halo, {{ Auth::user()->name }}! 👋
                    </h2>
                    <p class="mt-2 text-slate-400">
                        Jangan lupa absen hari ini ya. Semangat kerjanya!
                    </p>
                    <div class="mt-4 inline-flex items-center gap-2 rounded-lg bg-slate-800/50 px-3 py-1 text-sm text-slate-300 ring-1 ring-inset ring-slate-700">
                        <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>

                <a href="{{ route('user.scanQR') }}" class="group flex items-center gap-3 rounded-xl bg-blue-600 px-6 py-4 font-semibold text-white shadow-lg shadow-blue-500/30 transition-all hover:bg-blue-500 hover:scale-105 hover:shadow-blue-500/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 group-hover:bg-white/30 transition">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <span class="block text-xs font-medium text-blue-200">Klik Disini</span>
                        <span class="text-lg">Scan Absensi</span>
                    </div>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Hadir Bulan Ini</p>
                        <p class="text-2xl font-bold text-slate-900">{{ $totalHadir ?? 0 }} <span class="text-sm font-normal text-slate-400">Kali</span></p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Shift Hari Ini</p>
                        <p class="text-lg font-bold text-slate-900">
                            {{ $riwayat->first() && \Carbon\Carbon::parse($riwayat->first()->created_at)->isToday() ? $riwayat->first()->shift->shift_name : 'Belum Absen' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Status Akun</p>
                        <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Aktif</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Aktivitas Terakhir</h3>
            </div>
            <div class="overflow-hidden">
                @if($riwayat->isEmpty())
                    <div class="p-8 text-center text-slate-500">
                        <p>Belum ada riwayat absensi.</p>
                    </div>
                @else
                    <ul role="list" class="divide-y divide-slate-100">
                        @foreach($riwayat as $log)
                            <li class="flex items-center justify-between gap-x-6 px-6 py-4 hover:bg-slate-50">
                                <div class="flex min-w-0 gap-x-4">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600">
                                        {{ substr($log->shift->shift_name ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-sm font-semibold leading-6 text-slate-900">
                                            {{ $log->present_desc_system ?? 'Absen' }}
                                        </p>
                                        <p class="mt-1 truncate text-xs leading-5 text-slate-500">
                                            {{ $log->shift->shift_name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                                    <p class="text-sm leading-6 text-slate-900 font-mono">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }} WIB
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>
@endsection