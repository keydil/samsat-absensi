@extends('layouts.app')

@push('styles')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

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

        {{-- WARNING OTOMATIS HRD --}}
        @if(isset($usersWithWarnings) && count($usersWithWarnings) > 0)
            <div class="rounded-xl border border-rose-300 bg-rose-50 p-6 shadow-sm shadow-rose-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                    <svg class="w-32 h-32 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-lg font-bold text-rose-800 flex items-center gap-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Tindakan Diperlukan: Pegawai Melampaui Batas Indisipliner
                    </h3>
                    <p class="mt-1 text-sm text-rose-700">Sistem mendeteksi pegawai berikut memiliki tingkat pelanggaran tinggi bulan ini (>5 kali telat atau >3 kali bolos).</p>
                    
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($usersWithWarnings as $warning)
                            <div class="bg-white rounded border border-rose-200 p-4 shadow-sm flex flex-col justify-between h-full relative overflow-hidden">
                                @if($warning['sp_level'] == 2)
                                    <div class="absolute top-0 right-0 bg-rose-600 text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg">SP2</div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-800">{{ $warning['user']->name }}</p>
                                    <ul class="mt-2 space-y-1 mb-4">
                                        @foreach($warning['issues'] as $issue)
                                            <li class="text-xs text-rose-600 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                {{ $issue }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <form id="form-sp-{{ $warning['user']->id }}" action="{{ route('dashboard.admin.proses-sp', $warning['user']->id) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="confirmSP('{{ $warning['user']->id }}', '{{ addslashes($warning['user']->name) }}', '{{ $warning['sp_level'] }}')" class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-rose-50 border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-600 hover:text-white transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Tandai Selesai (Proses {{ $warning['sp_level'] == 2 ? 'SP2' : 'SP1' }})
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- 1. KARTU STATISTIK HARI INI --}}
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
                        <p class="mt-1 text-3xl font-bold text-slate-600">{{ $izinSakit }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. GRAFIK (CHART.JS) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Grafik Bulanan (Donut) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm col-span-1">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Rasio Kehadiran Bulan Ini</h3>
                <div class="relative h-64 w-full flex justify-center items-center">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            {{-- Grafik Mingguan (Bar) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Tren Kehadiran (7 Hari Terakhir)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. LEADERBOARD / KEDISIPLINAN --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Top 3 Rajin --}}
            <div class="rounded-2xl border border-emerald-200 bg-white shadow-sm overflow-hidden">
                <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
                    <span class="text-2xl">🏆</span>
                    <h3 class="text-lg font-bold text-emerald-800">Top 3 Paling Disiplin</h3>
                </div>
                <div class="p-0">
                    @forelse($topPegawai as $index => $pegawai)
                        <div class="flex items-center justify-between p-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full font-bold {{ $index == 0 ? 'bg-yellow-100 text-yellow-600' : ($index == 1 ? 'bg-slate-200 text-slate-600' : 'bg-orange-100 text-orange-600') }}">
                                    #{{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $pegawai['user']->name }}</p>
                                    <p class="text-xs text-slate-500">Hadir: {{ $pegawai['hadir'] }} | Telat: {{ $pegawai['telat'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-black text-emerald-600">{{ $pegawai['score'] }}</p>
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Poin</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500">Belum ada data bulan ini.</div>
                    @endforelse
                </div>
                <div class="bg-emerald-50/50 p-3 border-t border-emerald-100 text-center">
                    <button onclick="openLeaderboardModal()" class="text-sm font-bold text-emerald-600 hover:text-emerald-800 transition-colors w-full py-2">
                        Lihat Semua Karyawan &rarr;
                    </button>
                </div>
            </div>

            {{-- Bottom 3 (Sering Telat/Bolos) --}}
            <div class="rounded-2xl border border-rose-200 bg-white shadow-sm overflow-hidden">
                <div class="bg-rose-50 px-6 py-4 border-b border-rose-100 flex items-center gap-3">
                    <span class="text-2xl">⚠️</span>
                    <h3 class="text-lg font-bold text-rose-800">Perlu Perhatian (Terbawah)</h3>
                </div>
                <div class="p-0">
                    @forelse($bottomPegawai as $index => $pegawai)
                        <div class="flex items-center justify-between p-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 font-bold text-rose-600">
                                    #{{ count($topPegawai) + $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $pegawai['user']->name }}</p>
                                    <p class="text-xs text-slate-500">Bolos: {{ $pegawai['bolos'] }} | Telat: {{ $pegawai['telat'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-black {{ $pegawai['score'] < 0 ? 'text-rose-600' : 'text-slate-600' }}">{{ $pegawai['score'] }}</p>
                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Poin</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500">Belum ada data bulan ini.</div>
                    @endforelse
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
                            @foreach($riwayatTerbaru as $absen)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-xs uppercase">
                                                {{ substr($absen->user->name ?? '?', 0, 2) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-900">{{ $absen->user->name ?? 'User Dihapus' }}</p>
                                                <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($absen->date)->translatedFormat('d M Y') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">
                                        {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '--:--' }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">
                                        {{ $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : '--:--' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center">
                                            @if($absen->status == 'Hadir')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    Hadir
                                                </span>
                                            @elseif($absen->status == 'Telat')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-semibold text-yellow-700 border border-yellow-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                                    Terlambat
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    {{ $absen->status }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL FULL LEADERBOARD --}}
    <div id="leaderboardModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeLeaderboardModal()"></div>
        
        <!-- Modal Panel -->
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all flex flex-col max-h-[85vh]">
            <!-- Header -->
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🏆</span>
                    <h3 class="text-xl font-bold text-slate-800">Ranking Kedisiplinan Karyawan</h3>
                </div>
                <button onclick="closeLeaderboardModal()" class="rounded-lg p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Body (Scrollable) -->
            <div class="overflow-y-auto p-0 flex-1">
                @forelse($pegawaiRankings as $index => $pegawai)
                    <div class="flex items-center justify-between p-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            @php
                                $rankClass = 'bg-slate-100 text-slate-600';
                                if ($index == 0) $rankClass = 'bg-yellow-100 text-yellow-600';
                                elseif ($index == 1) $rankClass = 'bg-slate-200 text-slate-600';
                                elseif ($index == 2) $rankClass = 'bg-orange-100 text-orange-600';
                                elseif ($pegawai['score'] < 0) $rankClass = 'bg-rose-100 text-rose-600';
                            @endphp
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full font-bold {{ $rankClass }}">
                                #{{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $pegawai['user']->name }}</p>
                                <p class="text-xs text-slate-500">
                                    Hadir: {{ $pegawai['hadir'] }} | Telat: {{ $pegawai['telat'] }} | Bolos: {{ $pegawai['bolos'] }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xl font-black {{ $pegawai['score'] < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $pegawai['score'] }}</p>
                            <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Poin</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500">Belum ada data karyawan.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Script inisialisasi Chart.js --}}
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openLeaderboardModal() {
            const modal = document.getElementById('leaderboardModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeLeaderboardModal() {
            const modal = document.getElementById('leaderboardModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Doughnut Chart (Bulan Ini)
            const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
            new Chart(ctxMonthly, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir Tepat Waktu', 'Terlambat', 'Izin/Sakit', 'Tidak Hadir (Bolos)'],
                    datasets: [{
                        data: {{ json_encode($monthlyChartData) }},
                        backgroundColor: [
                            '#10b981', // emerald-500 (Hadir)
                            '#eab308', // yellow-500 (Telat)
                            '#64748b', // slate-500 (Izin/Sakit)
                            '#ef4444'  // red-500 (Bolos)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    },
                    cutout: '70%'
                }
            });

            // 2. Bar Chart (7 Hari Terakhir)
            const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
            new Chart(ctxWeekly, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($weeklyChartLabels) !!},
                    datasets: [
                        {
                            label: 'Hadir',
                            data: {{ json_encode($weeklyHadirData) }},
                            backgroundColor: '#10b981', // emerald-500
                            borderRadius: 4
                        },
                        {
                            label: 'Telat',
                            data: {{ json_encode($weeklyTelatData) }},
                            backgroundColor: '#eab308', // yellow-500
                            borderRadius: 4
                        },
                        {
                            label: 'Bolos',
                            data: {{ json_encode($weeklyBolosData) }},
                            backgroundColor: '#ef4444', // red-500
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12 } }
                    }
                }
            });
        });

        function confirmSP(userId, userName, spLevel) {
            let spLabel = spLevel == 2 ? 'SP2' : 'SP1';
            Swal.fire({
                title: 'Tindak Lanjut Pelanggaran?',
                text: `Apakah Anda yakin sudah memproses peringatan (${spLabel}) kepada ${userName}? Tindakan ini akan mereset hitungan indisipliner bulan ini.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tandai Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-sp-' + userId).submit();
                }
            });
        }
    </script>
@endsection