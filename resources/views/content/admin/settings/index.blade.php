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
                {{-- Jadwal Absensi Per-Hari --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold tracking-tight text-slate-800">Jadwal Sesi Absensi</h2>
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">Senin - Jumat</span>
                    </div>

                    @php
                        $hari = [
                            'MONDAY' => 'Senin',
                            'TUESDAY' => 'Selasa',
                            'WEDNESDAY' => 'Rabu',
                            'THURSDAY' => 'Kamis',
                            'FRIDAY' => 'Jumat'
                        ];
                        $todayKey = strtoupper(now()->format('l'));
                    @endphp

                    @foreach($hari as $key => $namaHari)
                        @php
                            $isToday = ($todayKey == $key);
                        @endphp
                        <div x-data="{ open: {{ $isToday ? 'true' : 'false' }} }" 
                             class="relative rounded-xl border {{ $isToday ? 'border-blue-400 shadow-md ring-1 ring-blue-400' : 'border-slate-200 shadow-sm' }} bg-white overflow-hidden transition-all duration-200">
                            
                            @if($isToday)
                                <div class="absolute top-0 right-0 rounded-bl-xl bg-blue-600 px-3 py-1 text-[10px] font-bold text-white shadow-sm flex items-center gap-1 z-10">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                    </span>
                                    HARI INI
                                </div>
                            @endif

                            <button type="button" @click="open = !open" 
                                    class="w-full text-left border-b {{ $isToday ? 'border-blue-100 bg-blue-50/80 hover:bg-blue-100' : 'border-slate-100 bg-slate-50 hover:bg-slate-100' }} px-5 py-4 flex justify-between items-center transition-colors">
                                <h3 class="font-bold {{ $isToday ? 'text-blue-800' : 'text-slate-700' }}">Hari {{ $namaHari }}</h3>
                                <svg class="h-5 w-5 transform transition-transform duration-200" 
                                     :class="{'rotate-180': open}" 
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" x-collapse x-cloak>
                                <div class="p-5 space-y-5 bg-white">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700">Masuk - Mulai</label>
                                            <input type="time" name="QR_SESSION_IN_START_{{ $key }}" value="{{ old('QR_SESSION_IN_START_'.$key, $settings['QR_SESSION_IN_START_'.$key] ?? '07:00') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            @error('QR_SESSION_IN_START_'.$key)
                                                <p class="mt-1 text-[10px] text-red-500 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700">Masuk - Selesai</label>
                                            <input type="time" name="QR_SESSION_IN_END_{{ $key }}" value="{{ old('QR_SESSION_IN_END_'.$key, $settings['QR_SESSION_IN_END_'.$key] ?? '09:00') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            @error('QR_SESSION_IN_END_'.$key)
                                                <p class="mt-1 text-[10px] text-red-500 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700">Batas Toleransi Telat (Masuk)</label>
                                        <input type="time" name="TOLERANSI_TELAT_MASUK_{{ $key }}" value="{{ old('TOLERANSI_TELAT_MASUK_'.$key, $settings['TOLERANSI_TELAT_MASUK_'.$key] ?? '08:00') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        @error('TOLERANSI_TELAT_MASUK_'.$key)
                                            <p class="mt-1 text-[10px] text-red-500 font-medium">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <hr class="{{ $isToday ? 'border-blue-100' : 'border-slate-100' }}">

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700">Pulang - Mulai</label>
                                            <input type="time" name="QR_SESSION_OUT_START_{{ $key }}" value="{{ old('QR_SESSION_OUT_START_'.$key, $settings['QR_SESSION_OUT_START_'.$key] ?? '15:00') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            @error('QR_SESSION_OUT_START_'.$key)
                                                <p class="mt-1 text-[10px] text-red-500 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700">Pulang - Selesai</label>
                                            <input type="time" name="QR_SESSION_OUT_END_{{ $key }}" value="{{ old('QR_SESSION_OUT_END_'.$key, $settings['QR_SESSION_OUT_END_'.$key] ?? '17:00') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            @error('QR_SESSION_OUT_END_'.$key)
                                                <p class="mt-1 text-[10px] text-red-500 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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

            <div class="flex items-center justify-end">
                <div id="unsaved_alert" class="hidden mr-4 text-sm font-medium text-amber-600 bg-amber-50 px-4 py-2 rounded-lg ring-1 ring-amber-200 flex items-center gap-2 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Ada perubahan yang belum disimpan!
                </div>
                <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Unsaved changes detection
        function showUnsavedWarning() {
            document.getElementById('unsaved_alert').classList.remove('hidden');
        }

        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', showUnsavedWarning);
            input.addEventListener('input', showUnsavedWarning);
        });

        // Update inputs when marker is dragged
        marker.on('dragstart', function (e) {
            showUnsavedWarning();
        });

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
            showUnsavedWarning();
            const rad = parseFloat(document.getElementById('radius_input').value) || 100;
            circle.setRadius(rad);
        }

        function useCurrentLocation() {
            if (navigator.geolocation) {
                Swal.fire({
                    title: 'Mencari Lokasi...',
                    text: 'Mohon izinkan akses GPS di browser Anda.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        marker.setLatLng([lat, lng]);
                        updateLatLngInputs(lat, lng);
                        showUnsavedWarning();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Lokasi Ditemukan!',
                            text: 'Pin Peta berhasil dipindahkan ke lokasi Anda saat ini. Jangan lupa klik Simpan Pengaturan.',
                            confirmButtonColor: '#2563eb'
                        });
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
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Akses GPS',
                            text: errorMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                Swal.fire('Error', 'Browser Anda tidak mendukung GPS.', 'error');
            }
        }
    </script>
@endpush
