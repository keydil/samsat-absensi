@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Scan Absensi</h1>
                <p class="text-sm text-slate-500">Arahkan kamera ke QR Code untuk melakukan absensi.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        {{-- CARD STATUS LOKASI --}}
        <div id="location-status" class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 flex items-center gap-4">
            <div id="loc-icon" class="flex-shrink-0 h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-700" id="loc-title">Mengambil lokasi GPS...</p>
                <p class="text-xs text-slate-500 mt-0.5" id="loc-desc">Mohon izinkan akses lokasi di browser kamu</p>
            </div>
            <div id="loc-badge" class="hidden text-right">
                <span id="loc-distance" class="text-2xl font-bold text-slate-900"></span>
                <span class="text-xs text-slate-500 block">dari kantor</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden relative">
                    <div class="p-4 border-b border-slate-100 bg-white flex justify-between items-center rounded-t-xl">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Kamera Scanner
                        </h3>
                        <div class="flex gap-2 flex-wrap">
                            <button id="start-camera"
                                class="px-4 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg shadow-sm shadow-blue-200 hover:bg-blue-700 hover:shadow-md transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Start Kamera
                            </button>
                            <button id="stop-camera"
                                class="px-4 py-2 text-sm font-bold bg-rose-50 text-rose-600 border border-rose-200 rounded-lg hover:bg-rose-600 hover:text-white transition-all hidden flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                                Stop
                            </button>
                            <button onclick="openNonPresenceModal('Izin')"
                                class="px-4 py-2 text-sm font-semibold bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-all shadow-sm">
                                📝 Ajukan Izin
                            </button>
                            <button onclick="openNonPresenceModal('Sakit')"
                                class="px-4 py-2 text-sm font-semibold bg-white text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-rose-600 transition-all shadow-sm">
                                🏥 Lapor Sakit
                            </button>
                        </div>
                    </div>

                    {{-- CSS OVERRIDES UNTUK HTML5-QRCODE --}}
                    <style>
                        #reader {
                            border: none !important;
                        }
                        #reader__dashboard_section_csr span {
                            font-size: 0 !important; /* Hilangkan tulisan teks tapi pertahankan select/button */
                        }
                        #reader__dashboard_section_csr span > * {
                            font-size: 14px !important; /* Kembalikan font size untuk select dan button */
                        }
                        #html5-qrcode-select-camera {
                            width: 100%;
                            padding: 10px 16px;
                            border-radius: 8px;
                            border: 1px solid #cbd5e1;
                            background-color: #f8fafc;
                            color: #334155;
                            font-size: 14px;
                            font-weight: 500;
                            margin-bottom: 16px;
                            margin-top: 8px;
                            outline: none;
                            cursor: pointer;
                            appearance: none;
                            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
                            background-repeat: no-repeat;
                            background-position: right 12px center;
                            background-size: 16px;
                        }
                        #html5-qrcode-button-camera-start {
                            width: 100%;
                            background-color: #2563eb !important;
                            color: white !important;
                            font-weight: 600;
                            padding: 12px 16px;
                            border-radius: 8px;
                            border: none;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            margin-bottom: 12px;
                            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                        }
                        #html5-qrcode-button-camera-start:hover {
                            background-color: #1d4ed8 !important;
                        }
                        #html5-qrcode-button-camera-stop {
                            width: 100%;
                            background-color: #ef4444 !important;
                            color: white !important;
                            font-weight: 600;
                            padding: 12px 16px;
                            border-radius: 8px;
                            border: none;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            margin-bottom: 12px;
                        }
                        #html5-qrcode-button-camera-stop:hover {
                            background-color: #b91c1c !important;
                        }
                        #html5-qrcode-anchor-scan-type-change {
                            color: #64748b !important;
                            text-decoration: none !important;
                            font-size: 13px;
                            font-weight: 600;
                            display: block;
                            margin-top: 10px;
                            padding: 8px;
                            border-radius: 6px;
                        }
                        #html5-qrcode-anchor-scan-type-change:hover {
                            color: #2563eb !important;
                            background-color: #eff6ff !important;
                        }
                        #html5-qrcode-button-file-selection {
                            width: 100%;
                            background-color: #f1f5f9 !important;
                            color: #475569 !important;
                            font-weight: 600;
                            padding: 10px 16px;
                            border-radius: 8px;
                            border: 1px solid #cbd5e1;
                            cursor: pointer;
                            transition: all 0.2s ease;
                        }
                        #html5-qrcode-button-file-selection:hover {
                            background-color: #e2e8f0 !important;
                        }
                    </style>

                    <div
                        class="relative bg-slate-50 rounded-b-xl overflow-hidden min-h-[400px] flex flex-col items-center justify-center">
                        <div id="reader"
                            class="w-full max-w-sm bg-white rounded-xl shadow-sm border-2 border-dashed border-slate-300 overflow-hidden z-10 relative">
                        </div>
                        <div id="camera-placeholder"
                            class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 z-0">
                            <div class="bg-white p-4 rounded-full shadow-sm mb-4">
                                <svg class="w-10 h-10 text-blue-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h4 class="font-medium text-slate-700 mb-1">Kamera Siap</h4>
                            <p class="text-sm text-slate-500 text-center max-w-xs px-4">
                                Klik tombol <span class="font-bold text-blue-600">Start Kamera</span> di pojok kanan atas
                                untuk memulai scan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm h-full flex flex-col">
                    <div class="p-4 border-b border-slate-100 bg-slate-50">
                        <h3 class="font-semibold text-slate-800">Riwayat Hari Ini</h3>
                    </div>
                    <div class="p-0 flex-1 overflow-y-auto max-h-[500px]">
                        @if ($absens->isEmpty())
                            <div class="p-8 text-center text-slate-500 h-full flex flex-col items-center justify-center">
                                <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm">Belum ada data absen hari ini.</p>
                            </div>
                        @else
                            <ul class="divide-y divide-slate-100">
                                @foreach ($absens as $absen)
                                    <li class="p-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 rounded-full {{ str_contains($absen->present_desc_system, 'Masuk') ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    @if (str_contains($absen->present_desc_system, 'Masuk'))
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    @endif
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800">
                                                    {{ $absen->present_desc_system ?? 'Absen' }}
                                                </p>
                                                <p class="text-xs text-slate-500">{{ $absen->time }} WIB</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let html5QrcodeScanner = null;
        let userLatitude = null;
        let userLongitude = null;
        let faceDetected = false;
        let capturedFaceImage = null;
        let faceStream = null;
        let faceInterval = null;

        const OFFICE_LAT = {{ \App\Models\Setting::get('OFFICE_LAT', -6.953797) }};
        const OFFICE_LNG = {{ \App\Models\Setting::get('OFFICE_LNG', 107.766743) }};
        const MAX_RADIUS = {{ \App\Models\Setting::get('OFFICE_RADIUS_METER', 100) }};

        function haversineJS(lat1, lng1, lat2, lng2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(
                dLng / 2) ** 2;
            return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
        }

        function formatDistance(meter) {
            return meter >= 1000 ? (meter / 1000).toFixed(1) + ' km' : meter + ' m';
        }

        function updateLocationUI(state, customMsg, distance) {
            const card = document.getElementById('location-status');
            const icon = document.getElementById('loc-icon');
            const title = document.getElementById('loc-title');
            const desc = document.getElementById('loc-desc');
            const badge = document.getElementById('loc-badge');
            const distEl = document.getElementById('loc-distance');

            if (state === 'ok') {
                card.className = 'rounded-xl border border-emerald-200 bg-emerald-50 shadow-sm p-4 flex items-center gap-4';
                icon.className = 'flex-shrink-0 h-12 w-12 rounded-full bg-emerald-100 flex items-center justify-center';
                icon.innerHTML =
                    `<svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
                title.textContent = '✅ Kamu berada di dalam area kantor';
                title.className = 'text-sm font-semibold text-emerald-700';
                desc.textContent = 'Absensi dapat dilakukan. Silakan scan QR Code.';
                desc.className = 'text-xs text-emerald-600 mt-0.5';
                badge.classList.remove('hidden');
                distEl.textContent = formatDistance(distance);
                distEl.className = 'text-2xl font-bold text-emerald-600';
            } else if (state === 'far') {
                card.className = 'rounded-xl border border-red-200 bg-red-50 shadow-sm p-4 flex items-center gap-4';
                icon.className = 'flex-shrink-0 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center';
                icon.innerHTML =
                    `<svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
                title.textContent = '❌ Kamu di luar area kantor';
                title.className = 'text-sm font-semibold text-red-700';
                desc.textContent =
                    `Kamu harus berada dalam radius ${MAX_RADIUS}m dari kantor. Sekarang kamu ${formatDistance(distance)} dari kantor.`;
                desc.className = 'text-xs text-red-500 mt-0.5';
                badge.classList.remove('hidden');
                distEl.textContent = formatDistance(distance);
                distEl.className = 'text-2xl font-bold text-red-600';
            } else if (state === 'denied') {
                card.className = 'rounded-xl border border-amber-200 bg-amber-50 shadow-sm p-4 flex items-center gap-4';
                icon.className = 'flex-shrink-0 h-12 w-12 rounded-full bg-amber-100 flex items-center justify-center';
                icon.innerHTML =
                    `<svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
                title.textContent = '⚠️ Izin lokasi diperlukan';
                title.className = 'text-sm font-semibold text-amber-700';
                desc.textContent = customMsg ?? 'Aktifkan izin lokasi di browser untuk melakukan absensi.';
                desc.className = 'text-xs text-amber-600 mt-0.5';
                badge.classList.add('hidden');
            }
        }

        function getLocation() {
            if (!navigator.geolocation) {
                updateLocationUI('denied', 'Browser tidak mendukung GPS.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    userLatitude = pos.coords.latitude;
                    userLongitude = pos.coords.longitude;
                    const distance = haversineJS(userLatitude, userLongitude, OFFICE_LAT, OFFICE_LNG);
                    updateLocationUI(distance <= MAX_RADIUS ? 'ok' : 'far', null, distance);
                },
                (err) => {
                    updateLocationUI('denied', 'Izin lokasi ditolak. Aktifkan GPS di pengaturan browser kamu.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        }
        getLocation();

        document.getElementById('start-camera').addEventListener('click', function() {
            startScanner();
        });

        document.getElementById('stop-camera').addEventListener('click', function() {
            if (html5QrcodeScanner) html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
            document.getElementById('stop-camera').classList.add('hidden');
            document.getElementById('start-camera').classList.remove('hidden');
            document.getElementById('camera-placeholder').classList.remove('hidden');
        });

        function startScanner() {
            document.getElementById('reader').innerHTML = '';
            html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            }, false);
            html5QrcodeScanner.render(onScanSuccess, () => {});
            document.getElementById('stop-camera').classList.remove('hidden');
            document.getElementById('start-camera').classList.add('hidden');
            document.getElementById('camera-placeholder').classList.add('hidden');
        }

        function onScanSuccess(decodedText) {
            html5QrcodeScanner.clear();
            document.getElementById('camera-placeholder').classList.remove('hidden');
            document.getElementById('stop-camera').classList.add('hidden');
            document.getElementById('start-camera').classList.remove('hidden');

            if (!userLatitude || !userLongitude) {
                Swal.fire('Lokasi Belum Siap', 'Tunggu sebentar, GPS sedang diambil. Coba lagi.', 'warning')
                    .then(() => startScanner());
                return;
            }

            const distance = haversineJS(userLatitude, userLongitude, OFFICE_LAT, OFFICE_LNG);
            if (distance > MAX_RADIUS) {
                Swal.fire({
                    icon: 'error',
                    title: 'Di Luar Area Kantor',
                    html: `Kamu berada <b>${formatDistance(distance)}</b> dari kantor.<br>Absensi hanya bisa dilakukan dalam radius <b>${MAX_RADIUS}m</b>.`,
                }).then(() => startScanner());
                return;
            }

            fetch("{{ route('user.scanCheck') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        code_qr: decodedText
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showFaceVerification(data.data);
                    } else {
                        Swal.fire('Gagal', data.message, 'error').then(() => startScanner());
                    }
                });
        }

        async function showFaceVerification(qrData) {
            faceDetected = false;
            capturedFaceImage = null;

            const {
                value: confirmed
            } = await Swal.fire({
                title: `QR Terbaca: Absen ${qrData.present_type}`,
                html: `
                    <p class="text-sm text-slate-500 mb-3">Verifikasi wajah diperlukan sebelum absen.</p>
                    <div style="position:relative;display:inline-block;width:100%">
                        <video id="face-video" style="border-radius:12px;background:#000;display:block;width:100%" autoplay muted playsinline></video>
                        <canvas id="face-overlay" style="position:absolute;top:0;left:0;border-radius:12px;width:100%;height:100%"></canvas>
                    </div>
                    <canvas id="face-capture" style="display:none"></canvas>
                    <p id="face-status" class="text-xs mt-2 text-slate-500">Memuat model deteksi wajah...</p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Absen Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2563eb',
                didOpen: () => startFaceDetection(),
                willClose: () => stopFaceCameraOnly(), // ← hanya stop kamera, TIDAK reset foto
                preConfirm: () => {
                    if (!faceDetected) {
                        Swal.showValidationMessage('Wajah tidak terdeteksi! Hadapkan wajah ke kamera.');
                        return false;
                    }
                    if (!capturedFaceImage) {
                        Swal.showValidationMessage(
                            'Foto belum ter-capture. Tunggu sebentar lalu coba lagi.');
                        return false;
                    }
                    return true;
                }
            });

            if (confirmed) {
                prosesAbsen(qrData.qr_id); // capturedFaceImage masih ada
            } else {
                capturedFaceImage = null; // reset hanya kalau batal
                startScanner();
            }
        }

        // Stop kamera TANPA reset capturedFaceImage
        function stopFaceCameraOnly() {
            if (faceInterval) clearInterval(faceInterval);
            if (faceStream) faceStream.getTracks().forEach(t => t.stop());
            faceDetected = false;
        }

        async function startFaceDetection() {
            const video = document.getElementById('face-video');
            const overlay = document.getElementById('face-overlay');
            const capture = document.getElementById('face-capture');
            const status = document.getElementById('face-status');

            try {
                const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                ]);

                faceStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: {
                            ideal: 320
                        },
                        height: {
                            ideal: 240
                        }
                    }
                });
                video.srcObject = faceStream;
                video.style.filter = 'brightness(1.4) contrast(1.1)';

                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                const w = video.videoWidth || 320;
                const h = video.videoHeight || 240;
                overlay.width = w;
                overlay.height = h;
                capture.width = w;
                capture.height = h;

                status.textContent = 'Kamera aktif. Hadapkan wajah ke kamera...';

                faceInterval = setInterval(async () => {
                    if (video.readyState < 2) return;

                    const ctx = overlay.getContext('2d');
                    ctx.clearRect(0, 0, overlay.width, overlay.height);

                    const detection = await faceapi.detectSingleFace(
                        video,
                        new faceapi.TinyFaceDetectorOptions({
                            scoreThreshold: 0.3,
                            inputSize: 416
                        })
                    ).withFaceLandmarks(true);

                    if (detection) {
                        faceDetected = true;
                        status.innerHTML =
                            '<span style="color:#16a34a;font-weight:bold">✅ Wajah terdeteksi! Klik Absen Sekarang.</span>';

                        const dims = faceapi.matchDimensions(overlay, video, true);
                        faceapi.draw.drawDetections(overlay, faceapi.resizeResults(detection, dims));

                        capture.getContext('2d').drawImage(video, 0, 0, capture.width, capture.height);
                        capturedFaceImage = capture.toDataURL('image/jpeg', 0.8);
                    } else {
                        faceDetected = false;
                        status.textContent = 'Wajah tidak terdeteksi. Pastikan pencahayaan cukup.';
                    }
                }, 500);

            } catch (err) {
                status.textContent = 'Gagal akses kamera: ' + err.message;
            }
        }

        function prosesAbsen(qrId) {
            const fotoYangDikirim = capturedFaceImage;

            console.log('=== DEBUG PROSES ABSEN ===');
            console.log('qrId:', qrId);
            console.log('face_image length:', fotoYangDikirim ? fotoYangDikirim.length : 'NULL');
            console.log('face_image prefix:', fotoYangDikirim ? fotoYangDikirim.substring(0, 50) : 'NULL');
            console.log('latitude:', userLatitude);
            console.log('longitude:', userLongitude);

            fetch("{{ route('user.scanStore', [], false) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        qr_id: qrId,
                        status: 'Hadir',
                        face_image: fotoYangDikirim,
                        latitude: userLatitude,
                        longitude: userLongitude,
                    })
                })
                .then(r => r.json())
                .then(data => {
                    console.log('=== SERVER RESPONSE ===', data);
                    if (data.success) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            })
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error').then(() => startScanner());
                    }
                })
                .catch(err => console.log('FETCH ERROR:', err));
        }

        // ===== NON-PRESENCE (IZIN / SAKIT) =====
        function openNonPresenceModal(type) {
            document.getElementById('np-modal').classList.remove('hidden');
            document.getElementById('np-status').value = type;
            document.getElementById('np-title').textContent = type === 'Izin' ? '📋 Ajukan Izin' : '🏥 Lapor Sakit';
            document.getElementById('np-subtitle').textContent = type === 'Izin'
                ? 'Isi form di bawah untuk mengajukan izin hari ini.'
                : 'Isi form di bawah untuk melaporkan sakit hari ini.';
            // Reset form
            document.getElementById('np-keterangan').value = '';
            document.getElementById('np-bukti').value = '';
            document.getElementById('np-preview').classList.add('hidden');
        }

        function closeNonPresenceModal() {
            document.getElementById('np-modal').classList.add('hidden');
        }

        function previewFile(input) {
            const preview = document.getElementById('np-preview');
            const previewImg = document.getElementById('np-preview-img');
            const previewName = document.getElementById('np-preview-name');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                previewName.textContent = file.name;
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => { previewImg.src = e.target.result; previewImg.classList.remove('hidden'); };
                    reader.readAsDataURL(file);
                } else {
                    previewImg.classList.add('hidden');
                }
                preview.classList.remove('hidden');
            }
        }

        function submitNonPresence() {
            const status = document.getElementById('np-status').value;
            const keterangan = document.getElementById('np-keterangan').value;
            const buktiInput = document.getElementById('np-bukti');

            if (!keterangan.trim()) {
                Swal.fire('Perhatian', 'Alasan/keterangan wajib diisi.', 'warning');
                return;
            }
            if (!buktiInput.files || !buktiInput.files[0]) {
                Swal.fire('Perhatian', 'Upload bukti surat wajib diisi.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('status', status);
            formData.append('keterangan', keterangan);
            formData.append('bukti_surat', buktiInput.files[0]);

            const submitBtn = document.getElementById('np-submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengirim...';

            fetch("{{ route('user.storeNonPresence', [], false) }}", {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async r => {
                if (!r.ok) {
                    const err = await r.json().catch(() => null);
                    throw err || { message: `HTTP Error ${r.status}` };
                }
                return r.json();
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Pengajuan';
                if (data.success) {
                    closeNonPresenceModal();
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    const errMsg = data.errors ? Object.values(data.errors).flat().join('\n') : data.message;
                    Swal.fire('Gagal', errMsg, 'error');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Pengajuan';
                const errMsg = err.message || (err.errors ? Object.values(err.errors).flat().join('\n') : 'Terjadi kesalahan jaringan.');
                Swal.fire('Error', errMsg, 'error');
            });
        }
    </script>

    {{-- MODAL IZIN / SAKIT --}}
    <div id="np-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeNonPresenceModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10 animate-in">
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <h3 id="np-title" class="text-lg font-bold text-slate-800"></h3>
                <p id="np-subtitle" class="text-sm text-slate-500 mt-1"></p>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" id="np-status" value="">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan / Keterangan <span class="text-red-500">*</span></label>
                    <textarea id="np-keterangan" rows="3" placeholder="Contoh: Demam tinggi, perlu istirahat..."
                        class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Bukti Surat <span class="text-red-500">*</span></label>
                    <input type="file" id="np-bukti" accept="image/*,.pdf" onchange="previewFile(this)"
                        class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, atau PDF. Maks 2MB.</p>
                    <div id="np-preview" class="hidden mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <img id="np-preview-img" class="hidden w-full max-h-40 object-contain rounded mb-2" />
                        <p id="np-preview-name" class="text-xs font-medium text-slate-600"></p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex gap-3 justify-end">
                <button onclick="closeNonPresenceModal()" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">Batal</button>
                <button id="np-submit-btn" onclick="submitNonPresence()" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">Kirim Pengajuan</button>
            </div>
        </div>
    </div>
@endsection
