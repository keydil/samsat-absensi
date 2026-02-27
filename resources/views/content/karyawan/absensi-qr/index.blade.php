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
                                                    {{ $absen->shift->shift_name ?? '-' }}</p>
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
    {{-- face-api.js untuk deteksi wajah --}}
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let html5QrcodeScanner = null;
        let userLatitude = null;
        let userLongitude = null;
        let faceDetected = false;
        let capturedFaceImage = null;

        function getLocation() {
            if (!navigator.geolocation) {
                Swal.fire('Error', 'Browser tidak mendukung GPS.', 'error');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    userLatitude = pos.coords.latitude;
                    userLongitude = pos.coords.longitude;
                    console.log('GPS OK:', userLatitude, userLongitude);
                },
                (err) => {
                    Swal.fire('Izin Lokasi Diperlukan',
                        'Aktifkan izin lokasi di browser untuk absensi.', 'warning');
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
            html5QrcodeScanner.pause();

            if (!userLatitude || !userLongitude) {
                Swal.fire('Lokasi Belum Siap', 'Tunggu sebentar, GPS sedang diambil. Coba lagi.', 'warning')
                    .then(() => html5QrcodeScanner.resume());
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
                        // Lanjut ke verifikasi wajah
                        showFaceVerification(data.data);
                    } else {
                        Swal.fire('Gagal', data.message, 'error').then(() => html5QrcodeScanner.resume());
                    }
                });
        }

        async function showFaceVerification(qrData) {
            const {
                value: confirmed
            } = await Swal.fire({
                title: `QR Terbaca: Absen ${qrData.present_type}`,
                html: `
            <p class="text-sm text-slate-500 mb-3">Verifikasi wajah diperlukan sebelum absen.</p>
            <div style="position:relative;display:inline-block">
                <video id="face-video" width="280" height="210"
                    style="border-radius:12px;background:#000;display:block" autoplay muted playsinline></video>
                <canvas id="face-overlay" width="280" height="210"
                    style="position:absolute;top:0;left:0;border-radius:12px"></canvas>
            </div>
            <canvas id="face-capture" width="280" height="210" style="display:none"></canvas>
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
                    return true;
                }
            });

            if (confirmed) {
                prosesAbsen(qrData.qr_id);
            } else {
                html5QrcodeScanner.resume();
            }
        }

        let faceStream = null;
        let faceInterval = null;

        async function startFaceDetection() {
            const video = document.getElementById('face-video');
            const overlay = document.getElementById('face-overlay');
            const status = document.getElementById('face-status');

            try {
                // Load model face-api.js (CDN)
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/weights';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
                ]);

                faceStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user'
                    }
                });
                video.srcObject = faceStream;
                status.textContent = 'Kamera aktif. Hadapkan wajah ke kamera...';

                faceInterval = setInterval(async () => {
                    const ctx = overlay.getContext('2d');
                    ctx.clearRect(0, 0, overlay.width, overlay.height);

                    const detection = await faceapi.detectSingleFace(
                        video, new faceapi.TinyFaceDetectorOptions({
                            scoreThreshold: 0.5
                        })
                    ).withFaceLandmarks(true);

                    if (detection) {
                        faceDetected = true;
                        status.innerHTML =
                            '<span style="color:#16a34a;font-weight:bold">✅ Wajah terdeteksi! Klik Absen Sekarang.</span>';

                        // Gambar kotak wajah
                        const dims = faceapi.matchDimensions(overlay, video, true);
                        faceapi.draw.drawDetections(overlay, faceapi.resizeResults(detection, dims));

                        // Capture foto
                        const capCanvas = document.getElementById('face-capture');
                        capCanvas.getContext('2d').drawImage(video, 0, 0, 280, 210);
                        capturedFaceImage = capCanvas.toDataURL('image/jpeg', 0.7);
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
                        Swal.fire('Gagal', data.message, 'error').then(() => html5QrcodeScanner.resume());
                    }
                });
        }
    </script>
@endsection
