@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Admin</h1>
                <p class="text-slate-500">Ringkasan aktivitas absensi hari ini, <span class="font-semibold text-blue-600">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>.</p>
            </div>
            <div>
                <a href="{{ route('admin.rekap-absensi') }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                    Lihat Rekap Lengkap &rarr;
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total Pegawai</p>
                        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalPegawai }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Hadir Hari Ini</p>
                        <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $hadirHariIni }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Terlambat</p>
                        <p class="mt-1 text-3xl font-bold text-yellow-600">{{ $terlambat }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-50 text-yellow-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Izin & Sakit</p>
                        <p class="mt-1 text-3xl font-bold text-blue-600">{{ $izinSakit }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-white px-6 py-4">
                <h3 class="text-lg font-bold text-slate-800">Aktivitas Absensi Terbaru (Live)</h3>
            </div>
            
            <div class="overflow-x-auto">
                @if($riwayatTerbaru->isEmpty())
                    <div class="p-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Belum ada aktivitas hari ini.</p>
                        </div>
                    </div>
                @else
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                            <tr>
                                <th class="px-6 py-3">Pegawai</th>
                                <th class="px-6 py-3">Jam Masuk</th>
                                <th class="px-6 py-3">Jam Pulang</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($riwayatTerbaru as $log)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3 font-medium text-slate-900">
                                        {{ $log->user->name ?? 'User Terhapus' }}
                                    </td>
                                    <td class="px-6 py-3 font-mono text-xs">
                                        @if($log->jam_masuk)
                                            <span class="text-emerald-600 font-bold">{{ \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') }}</span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 font-mono text-xs">
                                        @if($log->jam_pulang)
                                            <span class="text-blue-600 font-bold">{{ \Carbon\Carbon::parse($log->jam_pulang)->format('H:i') }}</span>
                                        @else
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        @if($log->status === 'Hadir')
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Hadir</span>
                                        @elseif($log->status === 'Telat')
                                            <span class="inline-flex items-center rounded-full bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Telat</span>
                                        @elseif($log->status === 'Izin')
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">Izin</span>
                                        @elseif($log->status === 'Sakit')
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">Sakit</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-600/20">{{ $log->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>
@endsection