<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Display — Samsat Rancaekek</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: white;
            height: 100vh;
            overflow: hidden;
            cursor: none;
        }

        body:hover {
            cursor: default;
        }

        .glow-ring {
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.3), 0 0 80px rgba(59, 130, 246, 0.1);
        }

        .glow-ring-orange {
            box-shadow: 0 0 40px rgba(249, 115, 22, 0.3), 0 0 80px rgba(249, 115, 22, 0.1);
        }

        .pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.03) 1px, transparent 0);
            background-size: 40px 40px;
        }
    </style>
</head>

<body class="bg-grid">

    {{-- TOP BAR --}}
    <div class="flex items-center justify-between px-8 py-4 bg-black/20 backdrop-blur-sm border-b border-white/5">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/logo-bapenda.png') }}" alt="Logo" class="h-10 w-auto">
            <div>
                <p class="text-[10px] font-bold tracking-[0.2em] text-yellow-400 uppercase">SAMSAT</p>
                <p class="text-sm font-bold tracking-tight">RANCAEKEK</p>
            </div>
        </div>
        <div class="text-right">
            <p id="display-date" class="text-sm text-slate-400 font-medium"></p>
            <p id="display-time" class="text-4xl font-black tracking-tight tabular-nums"></p>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex items-center justify-center" style="height: calc(100vh - 80px);">

        {{-- LOADING --}}
        <div id="kiosk-loading" class="text-center fade-in">
            <div class="inline-flex items-center justify-center h-24 w-24 rounded-full bg-white/5 mb-6">
                <svg class="animate-spin h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
            <p class="text-lg text-slate-400">Memuat sistem absensi...</p>
        </div>

        {{-- QR ACTIVE --}}
        <div id="kiosk-active" class="hidden text-center fade-in">
            <div class="mb-6">
                <span id="kiosk-badge"
                    class="inline-flex items-center gap-2 rounded-full px-6 py-2 text-lg font-bold"></span>
            </div>

            <div id="kiosk-qr-wrapper" class="bg-white p-6 rounded-3xl inline-block mb-6 glow-ring">
                <img id="kiosk-qr-image" src="" alt="QR Code" class="w-72 h-72 sm:w-80 sm:h-80" />
            </div>

            <div class="space-y-2">
                <p class="text-xl font-bold text-white">Scan QR Code untuk Absen</p>
                <p class="text-slate-400 text-sm">Arahkan kamera HP ke QR Code di atas</p>
                <p class="text-[10px] text-blue-300 font-medium tracking-wide">🔒 Terenkripsi & Otomatis ganti tiap 10 detik</p>
                <div class="flex items-center justify-center gap-2 mt-3">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-sm text-emerald-400 font-medium">Aktif sampai <span id="kiosk-end-time"
                            class="font-bold"></span> WIB</span>
                </div>
            </div>
        </div>

        {{-- INACTIVE --}}
        <div id="kiosk-inactive" class="hidden text-center fade-in">
            <div class="inline-flex items-center justify-center h-28 w-28 rounded-full bg-white/5 mb-6">
                <svg class="h-14 w-14 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-3xl font-black text-white mb-2">Di Luar Jam Absensi</h2>
            <p class="text-lg text-slate-400 mb-6" id="kiosk-inactive-msg"></p>

            <div id="kiosk-next-session" class="hidden bg-white/5 backdrop-blur-sm rounded-2xl px-8 py-5 inline-block border border-white/10">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Sesi Berikutnya</p>
                <p class="text-xl font-bold text-blue-400" id="kiosk-next-label"></p>
            </div>
        </div>

    </div>

    {{-- BOTTOM BAR --}}
    <div class="fixed bottom-0 left-0 right-0 px-8 py-3 bg-black/30 backdrop-blur-sm border-t border-white/5 flex items-center justify-between">
        <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Bapenda Prov. Jawa Barat — Sistem Absensi Digital</p>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 pulse-slow">Auto-refresh aktif</span>
            <button onclick="toggleFullscreen()"
                class="text-xs text-slate-400 hover:text-white transition px-3 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10">
                ⛶ Fullscreen
            </button>
            <a href="{{ route('admin.generate-qr') }}"
                class="text-xs text-slate-400 hover:text-white transition px-3 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10">
                ← Kembali
            </a>
        </div>
    </div>

    <script>
        // Clock
        function updateDisplayClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'
            });
            const dateStr = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                timeZone: 'Asia/Jakarta'
            });
            document.getElementById('display-time').textContent = timeStr;
            document.getElementById('display-date').textContent = dateStr;
        }
        setInterval(updateDisplayClock, 1000);
        updateDisplayClock();

        // Fullscreen toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // Auto-polling QR
        let lastKioskQr = null;

        async function fetchKioskQR() {
            try {
                const res = await fetch("{{ route('api.qr.current-active') }}");
                const data = await res.json();

                const loading = document.getElementById('kiosk-loading');
                const active = document.getElementById('kiosk-active');
                const inactive = document.getElementById('kiosk-inactive');

                loading.classList.add('hidden');

                if (data.active) {
                    active.classList.remove('hidden');
                    inactive.classList.add('hidden');

                    // Update QR
                    if (lastKioskQr !== data.data.code_qr) {
                        document.getElementById('kiosk-qr-image').src = 'data:image/svg+xml;base64,' + data.data
                            .svg;
                        lastKioskQr = data.data.code_qr;
                        active.classList.remove('fade-in');
                        void active.offsetWidth; // trigger reflow
                        active.classList.add('fade-in');
                    }

                    // Badge
                    const badge = document.getElementById('kiosk-badge');
                    const wrapper = document.getElementById('kiosk-qr-wrapper');
                    if (data.data.present_type === 'in_present') {
                        badge.className =
                            'inline-flex items-center gap-2 rounded-full px-6 py-2 text-lg font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30';
                        badge.innerHTML =
                            '<span class="h-3 w-3 rounded-full bg-blue-400 animate-pulse"></span> Sesi Absen Masuk';
                        wrapper.className = 'bg-white p-6 rounded-3xl inline-block mb-6 glow-ring';
                    } else {
                        badge.className =
                            'inline-flex items-center gap-2 rounded-full px-6 py-2 text-lg font-bold bg-orange-500/20 text-orange-300 border border-orange-500/30';
                        badge.innerHTML =
                            '<span class="h-3 w-3 rounded-full bg-orange-400 animate-pulse"></span> Sesi Absen Pulang';
                        wrapper.className = 'bg-white p-6 rounded-3xl inline-block mb-6 glow-ring-orange';
                    }

                    document.getElementById('kiosk-end-time').textContent = data.data.end_time;

                } else {
                    active.classList.add('hidden');
                    inactive.classList.remove('hidden');
                    lastKioskQr = null;

                    document.getElementById('kiosk-inactive-msg').textContent = data.message;

                    const nextEl = document.getElementById('kiosk-next-session');
                    if (data.next_session) {
                        nextEl.classList.remove('hidden');
                        document.getElementById('kiosk-next-label').textContent =
                            `${data.next_session.label} — ${data.next_session.start} WIB`;
                    } else {
                        nextEl.classList.add('hidden');
                    }
                }
            } catch (err) {
                console.error('Kiosk fetch error:', err);
            }
        }

        fetchKioskQR();
        setInterval(fetchKioskQR, 10000);
    </script>
</body>

</html>
