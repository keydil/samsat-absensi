@extends('layouts.app')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="space-y-8">
        
        {{-- WARNING OTOMATIS (Alert SP) --}}
        @if(count($warnings ?? []) > 0)
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 text-rose-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-rose-800">⚠️ SURAT PERINGATAN (SP) OTOMATIS</h3>
                        <div class="mt-2 text-sm text-rose-700 space-y-1">
                            @foreach($warnings as $warning)
                                <p>• {{ $warning }}</p>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs font-semibold text-rose-800 bg-rose-200/50 inline-block px-2 py-1 rounded">Pesan ini telah diteruskan ke Admin HRD.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- INFO TELAT HARI INI --}}
        @if($riwayat->first() && \Carbon\Carbon::parse($riwayat->first()->created_at)->isToday() && $riwayat->first()->status === 'Telat')
        <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 text-orange-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-orange-800">Perhatian</h3>
                    <p class="mt-1 text-sm text-orange-700">Anda berhasil absen hari ini, namun tercatat <strong>Telat</strong>. Harap perhatikan waktu kedatangan Anda besok (-5 Poin).</p>
                </div>
            </div>
        </div>
        @endif
        
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

        {{-- RAPOR KINERJA KARYAWAN --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Personal Score Card --}}
            <div class="rounded-2xl border {{ isset($score) && $score < 0 ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} p-6 shadow-sm col-span-1 flex flex-col justify-center items-center text-center">
                <p class="text-sm font-bold uppercase tracking-widest {{ isset($score) && $score < 0 ? 'text-rose-500' : 'text-emerald-500' }}">Skor Kedisiplinan Bulan Ini</p>
                <h1 class="text-6xl font-black mt-4 {{ isset($score) && $score < 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ $score ?? 0 }}</h1>
                <p class="mt-4 text-xs font-medium {{ isset($score) && $score < 0 ? 'text-rose-600/70' : 'text-emerald-600/70' }}">Hadir (+10) | Telat (-5) | Bolos (-10)</p>
            </div>

            {{-- Mini Chart Kinerja --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h3 class="text-base font-bold text-slate-800 mb-4">Statistik Performa Pribadi</h3>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-emerald-50 rounded-lg p-3 text-center border border-emerald-100">
                        <p class="text-xs text-emerald-600 font-bold uppercase">Hadir</p>
                        <p class="text-xl font-black text-emerald-700">{{ $userHadir ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3 text-center border border-yellow-100">
                        <p class="text-xs text-yellow-600 font-bold uppercase">Telat</p>
                        <p class="text-xl font-black text-yellow-700">{{ $userTelat ?? 0 }}</p>
                    </div>
                    <div class="bg-rose-50 rounded-lg p-3 text-center border border-rose-100">
                        <p class="text-xs text-rose-600 font-bold uppercase">Bolos</p>
                        <p class="text-xl font-black text-rose-700">{{ $userBolos ?? 0 }}</p>
                    </div>
                </div>
                <div class="relative h-24 w-full">
                    <canvas id="kinerjaChart"></canvas>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Riwayat 5 Absensi Terakhir</h3>
                <a href="{{ route('user.history') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Lihat Semua</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-white text-xs uppercase font-bold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Jam Masuk</th>
                            <th class="px-6 py-3">Jam Pulang</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($riwayat as $absen)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ \Carbon\Carbon::parse($absen->date)->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '--:--' }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : '--:--' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($absen->status == 'Hadir')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Hadir
                                        </span>
                                    @elseif($absen->status == 'Telat')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-semibold text-yellow-700 ring-1 ring-inset ring-yellow-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                            Terlambat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                            {{ $absen->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p>Belum ada riwayat absensi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(isset($userHadir))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('kinerjaChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Hadir', 'Telat', 'Bolos'],
                    datasets: [{
                        label: 'Total Hari',
                        data: [{{ $userHadir }}, {{ $userTelat }}, {{ $userBolos }}],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)', // emerald
                            'rgba(234, 179, 8, 0.8)',  // yellow
                            'rgba(244, 63, 94, 0.8)'   // rose
                        ],
                        borderRadius: 4,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    indexAxis: 'y', // horizontal bar chart
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { stepSize: 1 } },
                        y: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
    @endif
@endsection