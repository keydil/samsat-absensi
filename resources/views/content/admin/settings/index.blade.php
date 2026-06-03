@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 400px; z-index: 10; border-radius: 0.5rem; }
    </style>
@endpush

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Aplikasi</h1>
            <p class="text-sm text-slate-500">Konfigurasi jadwal absensi dan geofencing kantor.</p>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-emerald-50 p-4 text-emerald-800 ring-1 ring-emerald-200">
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Jadwal Absensi --}}
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                        <h3 class="font-bold text-slate-800">Jadwal Sesi Absensi</h3>
                        <p class="text-xs text-slate-500 mt-1">Gunakan format HH:MM (contoh: 07:00)</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Masuk - Mulai</label>
                                <input type="time" name="QR_SESSION_IN_START" value="{{ $settings['QR_SESSION_IN_START'] ?? '07:00' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Masuk - Selesai</label>
                                <input type="time" name="QR_SESSION_IN_END" value="{{ $settings['QR_SESSION_IN_END'] ?? '09:00' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Toleransi Telat (Masuk)</label>
                            <p class="text-xs text-slate-500 mb-1">Absen di atas jam ini akan otomatis tercatat 'Telat'</p>
                            <input type="time" name="TOLERANSI_TELAT_MASUK" value="{{ $settings['TOLERANSI_TELAT_MASUK'] ?? '08:00' }}" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <hr class="border-slate-100">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Pulang - Mulai</label>
                                <input type="time" name="QR_SESSION_OUT_START" value="{{ $settings['QR_SESSION_OUT_START'] ?? '15:00' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Pulang - Selesai</label>
                                <input type="time" name="QR_SESSION_OUT_END" value="{{ $settings['QR_SESSION_OUT_END'] ?? '17:00' }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Geofencing Map --}}
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-slate-800">Geofencing (Pusat Kantor)</h3>
                            <p class="text-xs text-slate-500 mt-1">Geser Pin Merah ke lokasi yang diinginkan</p>
                        </div>
                        <button type="button" onclick="useCurrentLocation()" class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20 hover:bg-blue-100">
                            📍 Gunakan Lokasi Saya
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div id="map" class="shadow-sm border border-slate-200"></div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Latitude</label>
                                <input type="text" id="lat_input" name="OFFICE_LAT" value="{{ $settings['OFFICE_LAT'] ?? '-6.953797' }}" readonly class="mt-1 block w-full bg-slate-50 rounded-md border-slate-300 shadow-sm sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Longitude</label>
                                <input type="text" id="lng_input" name="OFFICE_LNG" value="{{ $settings['OFFICE_LNG'] ?? '107.766743' }}" readonly class="mt-1 block w-full bg-slate-50 rounded-md border-slate-300 shadow-sm sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Radius Maksimal (Meter)</label>
                            <input type="number" id="radius_input" name="OFFICE_RADIUS_METER" value="{{ $settings['OFFICE_RADIUS_METER'] ?? '100' }}" onchange="updateCircleRadius()" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const initLat = parseFloat("{{ $settings['OFFICE_LAT'] ?? '-6.953797' }}");
        const initLng = parseFloat("{{ $settings['OFFICE_LNG'] ?? '107.766743' }}");
        let initRadius = parseFloat("{{ $settings['OFFICE_RADIUS_METER'] ?? '100' }}");

        const map = L.map('map').setView([initLat, initLng], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);
        let circle = L.circle([initLat, initLng], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: initRadius
        }).addTo(map);

        // Update inputs when marker is dragged
        marker.on('dragend', function (e) {
            const pos = marker.getLatLng();
            updateLatLngInputs(pos.lat, pos.lng);
        });

        function updateLatLngInputs(lat, lng) {
            document.getElementById('lat_input').value = lat;
            document.getElementById('lng_input').value = lng;
            circle.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        }

        function updateCircleRadius() {
            const rad = parseFloat(document.getElementById('radius_input').value) || 100;
            circle.setRadius(rad);
        }

        function useCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        marker.setLatLng([lat, lng]);
                        updateLatLngInputs(lat, lng);
                        alert("Lokasi berhasil diperbarui sesuai GPS Anda saat ini!");
                    }, 
                    function(error) {
                        let errorMsg = "Gagal mendapatkan lokasi. ";
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg += "Anda menolak permintaan Izin Lokasi di browser.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg += "Informasi lokasi tidak tersedia.";
                                break;
                            case error.TIMEOUT:
                                errorMsg += "Waktu permintaan lokasi habis (timeout).";
                                break;
                            default:
                                errorMsg += "Terjadi kesalahan tidak dikenal: " + error.message;
                                break;
                        }
                        alert(errorMsg);
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                alert("Browser Anda tidak mendukung GPS.");
            }
        }
    </script>
@endpush
