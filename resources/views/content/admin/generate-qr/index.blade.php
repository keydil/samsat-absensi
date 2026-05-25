@extends('layouts.app')

@section('content')
    <div class="space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">QR Code Absensi</h1>
                <p class="text-sm text-slate-500">QR Code di-generate otomatis berdasarkan jadwal sesi kerja.</p>
            </div>
            <a href="{{ route('admin.generate-qr.display') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-slate-800 transition-all hover:-translate-y-0.5">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Mode Display / Kiosk
            </a>
        </div>

        {{-- LIVE AUTO QR DISPLAY --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></div>
                    <h3 class="font-bold text-slate-800">QR Code Aktif — Auto Refresh</h3>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="live-clock">--:--:--</span>
                </div>
            </div>

            <div id="qr-auto-display" class="p-8">
                <div class="flex flex-col items-center justify-center min-h-[320px]">
                    {{-- Loading state --}}
                    <div id="qr-loading" class="text-center">
                        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-blue-50 mb-4">
                            <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-500 font-medium">Memuat QR Code...</p>
                    </div>

                    {{-- QR Active state --}}
                    <div id="qr-active" class="hidden text-center w-full max-w-md">
                        <div class="mb-4">
                            <span id="qr-session-badge"
                                class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-bold"></span>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-lg border border-slate-100 inline-block mb-4">
                            <img id="qr-image" src="" alt="QR Code" class="w-56 h-56 mx-auto" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs text-slate-400 font-mono" id="qr-uuid"></p>
                            <p class="text-sm text-slate-600">
                                Aktif: <span id="qr-time-range" class="font-bold text-slate-800"></span> WIB
                            </p>
                        </div>
                    </div>

                    {{-- Inactive state --}}
                    <div id="qr-inactive" class="hidden text-center">
                        <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-slate-100 mb-4">
                            <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-slate-700 mb-1">Di Luar Jam Absensi</h4>
                        <p class="text-sm text-slate-500 mb-3" id="inactive-message"></p>
                        <div id="next-session-info" class="hidden">
                            <p class="text-xs text-slate-400">Sesi berikutnya:</p>
                            <p class="text-sm font-bold text-blue-600" id="next-session-label"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL RIWAYAT QR (READ-ONLY) --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-white px-6 py-4">
                <h3 class="font-bold text-slate-800">Riwayat QR Code</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Tipe Absen</th>
                            <th class="px-6 py-4">Jam Aktif</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($activeQr as $qr)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    @if ($qr->present == 'in_present')
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">Masuk</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20">Pulang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($qr->start_time)->format('H:i') }} s/d
                                    {{ \Carbon\Carbon::parse($qr->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4">{{ $qr->date }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($qr->status == 'active')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span
                                                class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                            Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.generate-qr.show', $qr->code_qr) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors shadow-sm">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <p>Belum ada riwayat QR Code.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        // Live clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'
            });
            document.getElementById('live-clock').textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Auto-polling QR Code setiap 15 detik
        let lastQrCode = null;

        async function fetchActiveQR() {
            try {
                const res = await fetch("{{ route('api.qr.current-active') }}");
                const data = await res.json();

                const loading = document.getElementById('qr-loading');
                const active = document.getElementById('qr-active');
                const inactive = document.getElementById('qr-inactive');

                loading.classList.add('hidden');

                if (data.active) {
                    active.classList.remove('hidden');
                    inactive.classList.add('hidden');

                    // Update QR image
                    const qrImg = document.getElementById('qr-image');
                    if (lastQrCode !== data.data.code_qr) {
                        qrImg.src = 'data:image/svg+xml;base64,' + data.data.svg;
                        lastQrCode = data.data.code_qr;
                    }

                    // Update badge
                    const badge = document.getElementById('qr-session-badge');
                    if (data.data.present_type === 'in_present') {
                        badge.className =
                            'inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-bold bg-blue-100 text-blue-700';
                        badge.innerHTML =
                            '<span class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span> Absen Masuk';
                    } else {
                        badge.className =
                            'inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-bold bg-orange-100 text-orange-700';
                        badge.innerHTML =
                            '<span class="h-2 w-2 rounded-full bg-orange-500 animate-pulse"></span> Absen Pulang';
                    }

                    document.getElementById('qr-uuid').textContent = data.data.code_qr;
                    document.getElementById('qr-time-range').textContent =
                        `${data.data.start_time} — ${data.data.end_time}`;
                } else {
                    active.classList.add('hidden');
                    inactive.classList.remove('hidden');
                    lastQrCode = null;

                    document.getElementById('inactive-message').textContent = data.message;

                    const nextInfo = document.getElementById('next-session-info');
                    if (data.next_session) {
                        nextInfo.classList.remove('hidden');
                        document.getElementById('next-session-label').textContent =
                            `${data.next_session.label} — ${data.next_session.start} WIB`;
                    } else {
                        nextInfo.classList.add('hidden');
                    }
                }
            } catch (err) {
                console.error('Gagal fetch QR:', err);
            }
        }

        // Fetch pertama kali + polling setiap 15 detik
        fetchActiveQR();
        setInterval(fetchActiveQR, 15000);
    </script>
@endsection