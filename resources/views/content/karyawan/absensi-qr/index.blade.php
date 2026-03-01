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
                    <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Kamera Scanner
                        </h3>
                        <div class="flex gap-2">
                            <button id="start-camera"
                                class="px-3 py-1 text-xs font-bold bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                Start Kamera
                            </button>
                            <button id="stop-camera"
                                class="px-3 py-1 text-xs font-bold bg-red-600 text-white rounded hover:bg-red-700 transition hidden">
                                Stop
                            </button>
                        </div>
                    </div>

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
                                                    {{ $absen->present_desc_system }}</p>
                                                <p class="text-xs text-slate-500">{{ $absen->time }} WIB &bull;
                                                    {{ $absen->shift->shift_name ?? 'Harian' }}</p>
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

        const OFFICE_LAT = {{ env('OFFICE_LAT', -6.9824624) }};
        const OFFICE_LNG = {{ env('OFFICE_LNG', 107.7540507) }};
        const MAX_RADIUS = {{ env('OFFICE_RADIUS_METER', 50) }};

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
            navigator.geolocation.watchPosition(
                (pos) => {
                    userLatitude = pos.coords.latitude;
                    userLongitude = pos.coords.longitude;
                    const distance = haversineJS(userLatitude, userLongitude, OFFICE_LAT, OFFICE_LNG);
                    updateLocationUI(distance <= MAX_RADIUS ? 'ok' : 'far', null, distance);
                },
                (err) => {
                    updateLocationUI('denied', 'Izin lokasi ditolak. Aktifkan GPS di pengaturan browser kamu.');
                }, {
                    enableHighAccuracy: true
                }
            );
        }
        getLocation();

        document.getElementById('start-camera').addEventListener('click', function() {
            startScanner();
            this.classList.add('hidden');
            document.getElementById('stop-camera').classList.remove('hidden');
            document.getElementById('camera-placeholder').classList.add('hidden');
        });

        document.getElementById('stop-camera').addEventListener('click', function() {
            if (html5QrcodeScanner) html5QrcodeScanner.clear();
            html5QrcodeScanner = null;
            this.classList.add('hidden');
            document.getElementById('start-camera').classList.remove('hidden');
            document.getElementById('camera-placeholder').classList.remove('hidden');
        });

        function startScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            }, false);
            html5QrcodeScanner.render(onScanSuccess, () => {});
        }

        function onScanSuccess(decodedText) {
            html5QrcodeScanner.clear();
            document.getElementById('camera-placeholder').classList.remove('hidden');

            if (!userLatitude || !userLongitude) {
                Swal.fire('Lokasi Belum Siap', 'Tunggu sebentar, GPS sedang diambil. Coba lagi.', 'warning')
                    .then(() => {
                        startScanner();
                    });
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
            // Reset dulu sebelum buka modal
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
                willClose: () => stopFaceCamera(),
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
                prosesAbsen(qrData.qr_id);
            } else {
                startScanner();
            }
        }

        let faceStream = null;
        let faceInterval = null;

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
                        },
                        // Setting biar lebih terang di kondisi gelap
                        brightness: {
                            ideal: 100
                        },
                        exposureMode: 'continuous',
                        whiteBalanceMode: 'continuous',
                    }
                });
                video.srcObject = faceStream;
                // Setelah video.srcObject = faceStream;
                video.style.filter = 'brightness(1.4) contrast(1.1)';

                // Tunggu video bener-bener siap & playing
                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                // Set ukuran canvas sesuai video asli
                const w = video.videoWidth || 320;
                const h = video.videoHeight || 240;
                overlay.width = w;
                overlay.height = h;
                capture.width = w;
                capture.height = h;

                status.textContent = 'Kamera aktif. Hadapkan wajah ke kamera...';

                // Mulai deteksi SETELAH video siap
                faceInterval = setInterval(async () => {
                        if (video.readyState < 2) return; // skip kalau video belum siap

                        const ctx = overlay.getContext('2d');
                        ctx.clearRect(0, 0, overlay.width, overlay.height);

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

                        // Capture foto
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

        function stopFaceCamera() {
            if (faceInterval) clearInterval(faceInterval);
            if (faceStream) faceStream.getTracks().forEach(t => t.stop());
            faceDetected = false;
            capturedFaceImage = null;
        }

        function prosesAbsen(qrId) {
            fetch("{{ route('user.scanStore') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        qr_id: qrId,
                        status: 'Hadir',
                        face_image: capturedFaceImage,
                        latitude: userLatitude,
                        longitude: userLongitude,
                    })
                })
                .then(r => r.json())
                .then(data => {
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
                });
        }
    </script>
@endsection
