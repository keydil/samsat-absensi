<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\QrCode as QrCodeModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanQRController extends Controller
{
    public function index()
    {
        $absens = Absen::where('user_id', Auth::id())->latest('date')->get();
        return view('content.karyawan.absensi-qr.index', compact('absens'));
    }

    public function check(Request $request)
    {
        // Sistem Anti-Joki (Dynamic Cryptographic QR)
        $qrString = $request->code_qr;
        $parts = explode('|', $qrString);

        if (count($parts) !== 3) {
            return response()->json(['success' => false, 'message' => 'Format QR Code tidak valid atau sudah dimanipulasi.']);
        }

        $qrId = $parts[0];
        $timestamp = $parts[1];
        $signature = $parts[2];

        // 1. Validasi Integritas (Signature HMAC)
        $expectedSignature = hash_hmac('sha256', "$qrId|$timestamp", config('app.key'));
        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(['success' => false, 'message' => 'QR Code palsu atau telah dimodifikasi.']);
        }

        // 2. Validasi Kedaluwarsa (Maks 15 Detik)
        $age = time() - (int)$timestamp;
        if ($age > 15) {
            return response()->json(['success' => false, 'message' => 'QR Code Kedaluwarsa! (' . $age . ' detik). Silakan scan langsung dari layar TV.']);
        }

        // 3. Lanjut seperti biasa
        $qr = QrCodeModel::find($qrId);

        if (!$qr) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak ditemukan di sistem.']);
        }

        $now = Carbon::now('Asia/Jakarta');
        $start = Carbon::parse($qr->start_time)->setTimezone('Asia/Jakarta');
        $end = Carbon::parse($qr->end_time)->setTimezone('Asia/Jakarta');

        if ($qr->status != 'active' || $now->lt($start) || $now->gt($end)) {
            return response()->json(['success' => false, 'message' => 'QR Code sudah tidak aktif atau di luar waktu absensi.']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_id' => $qr->id,
                'present_type' => $qr->present == 'in_present' ? 'Masuk' : 'Keluar',
                'date' => Carbon::parse($qr->date)->format('d-m-Y'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'qr_id' => 'required|exists:qr_codes,id',
                'status' => 'required|in:Hadir,Izin,Sakit',
                'face_image' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal: ' . $e->getMessage()]);
        }

        $user = Auth::user();
        $qr = QrCodeModel::find($request->qr_id);

        if (!$qr) {
            return response()->json(['success' => false, 'message' => 'QR tidak ditemukan.']);
        }

        $now = Carbon::now('Asia/Jakarta');
        $start = Carbon::parse($qr->start_time)->setTimezone('Asia/Jakarta');
        $end = Carbon::parse($qr->end_time)->setTimezone('Asia/Jakarta');

        if ($qr->status != 'active' || $now->lt($start) || $now->gt($end)) {
            return response()->json([
                'success' => false,
                'message' => 'QR tidak aktif. Status: ' . $qr->status . ' | Sekarang: ' . $now . ' | Aktif: ' . $start . ' s/d ' . $end,
            ]);
        }

        $already = Absen::where('user_id', $user->id)->where('qr_code_id', $qr->id)->exists();
        if ($already) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah melakukan absensi ini.']);
        }

        $officeLat = (float) \App\Models\Setting::get('OFFICE_LAT', -6.953797);
        $officeLng = (float) \App\Models\Setting::get('OFFICE_LNG', 107.766743);
        $maxRadius = (float) \App\Models\Setting::get('OFFICE_RADIUS_METER', 100);
        $distance = $this->haversineDistance($request->latitude, $request->longitude, $officeLat, $officeLng);

        if ($distance > $maxRadius) {
            return response()->json(['success' => false, 'message' => "Di luar radius. Jarak: {$distance}m, Maks: {$maxRadius}m"]);
        }

        $faceImagePath = null;
        if ($request->filled('face_image')) {
            if (app()->environment('production')) {
                try {
                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->face_image);
                    $imageDecoded = base64_decode($imageData);
                    $tempPath = sys_get_temp_dir() . '/face_' . $user->id . '_' . time() . '.jpg';
                    file_put_contents($tempPath, $imageDecoded);

                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key' => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                    ]);

                    $result = $cloudinary->uploadApi()->upload($tempPath, [
                        'folder' => 'absensi-faces',
                        'public_id' => 'face_' . $user->id . '_' . time(),
                    ]);

                    @unlink($tempPath);
                    $faceImagePath = $result['secure_url'];
                } catch (\Exception $e) {
                    // Fallback to local storage if Cloudinary fails (e.g. rate limit)
                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->face_image);
                    $imageDecoded = base64_decode($imageData);
                    $filename = 'face_' . $user->id . '_' . time() . '_fallback.jpg';
                    $path = public_path('images/absensi/' . $filename);
                    if (!file_exists(public_path('images/absensi'))) {
                        mkdir(public_path('images/absensi'), 0755, true);
                    }
                    file_put_contents($path, $imageDecoded);
                    $faceImagePath = asset('images/absensi/' . $filename);
                }
            } else {
                try {
                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->face_image);
                    $imageDecoded = base64_decode($imageData);
                    $filename = 'face_' . $user->id . '_' . time() . '.jpg';
                    $path = public_path('images/absensi/' . $filename);
                    if (!file_exists(public_path('images/absensi'))) {
                        mkdir(public_path('images/absensi'), 0755, true);
                    }
                    file_put_contents($path, $imageDecoded);
                    $faceImagePath = asset('images/absensi/' . $filename);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Gagal simpan foto lokal: ' . $e->getMessage()]);
                }
            }
        }

        $timeNow = Carbon::now('Asia/Jakarta')->format('H:i');
        
        $finalStatus = $request->status;
        if ($qr->present == 'in_present' && $finalStatus == 'Hadir') {
            $day = strtoupper($now->format('l'));
            $batasTelat = \App\Models\Setting::get("TOLERANSI_TELAT_MASUK_$day", '08:00');
            if ($timeNow > $batasTelat) {
                $finalStatus = 'Telat';
            }
        }

        try {
            Absen::create([
                'user_id' => $user->id,
                'qr_code_id' => $qr->id,
                'date' => $qr->date,
                'time' => $timeNow,
                'status' => $finalStatus,
                'status_desc' => 'Absensi via QR Code',
                'present_desc_system' => 'Absen ' . ($qr->present == 'in_present' ? 'Masuk' : 'Keluar'),
                'present_user_image' => $faceImagePath,
                'lat_location_present' => $request->latitude,
                'lng_location_present' => $request->longitude,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal simpan absensi: ' . $e->getMessage()]);
        }

        return response()->json(['success' => true, 'message' => 'Absensi berhasil disimpan.']);
    }

    /**
     * Simpan pengajuan Izin / Sakit (Non-Presence).
     * Bypass: GPS geofencing & face biometric TIDAK dijalankan.
     */
    public function storeNonPresence(Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:Izin,Sakit',
                'keterangan' => 'required|string|max:500',
                'bukti_surat' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ], [
                'status.required' => 'Pilih status pengajuan.',
                'keterangan.required' => 'Masukkan alasan/keterangan.',
                'bukti_surat.required' => 'Upload bukti surat wajib diisi.',
                'bukti_surat.mimes' => 'File harus berupa gambar (JPG/PNG) atau PDF.',
                'bukti_surat.max' => 'Ukuran file maksimal 2MB.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = Auth::user();
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        // Cek duplikasi: maksimal 1 pengajuan Izin/Sakit per hari
        $alreadySubmitted = Absen::where('user_id', $user->id)
            ->where('date', $today)
            ->whereIn('status', ['Izin', 'Sakit'])
            ->whereNull('qr_code_id')
            ->exists();

        if ($alreadySubmitted) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah mengajukan Izin/Sakit hari ini.',
            ]);
        }

        // Upload bukti surat
        $buktiSuratPath = null;
        if ($request->hasFile('bukti_surat')) {
            $file = $request->file('bukti_surat');

            if (app()->environment('production')) {
                // Upload ke Cloudinary
                try {
                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key' => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                    ]);

                    $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'absensi-surat',
                        'public_id' => 'surat_' . $user->id . '_' . time(),
                    ]);

                    $buktiSuratPath = $result['secure_url'];
                } catch (\Throwable $e) {
                    // Fallback to local storage if Cloudinary fails
                    $filename = 'surat_' . $user->id . '_' . time() . '_fallback.' . $file->getClientOriginalExtension();
                    $destinationPath = public_path('images/surat');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $filename);
                    $buktiSuratPath = asset('images/surat/' . $filename);
                }
            } else {
                // Simpan ke local storage
                $filename = 'surat_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('images/surat');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $buktiSuratPath = asset('images/surat/' . $filename);
            }
        }

        // Simpan ke tabel absens — tanpa QR, tanpa GPS, tanpa foto wajah
        try {
            Absen::create([
                'user_id' => $user->id,
                'qr_code_id' => null,
                'date' => $today,
                'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
                'status' => $request->status,
                'status_desc' => $request->keterangan,
                'present_desc_system' => 'Pengajuan ' . $request->status,
                'present_user_image' => null,
                'lat_location_present' => null,
                'lng_location_present' => null,
                'bukti_surat' => $buktiSuratPath,
                'approval_status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pengajuan: ' . $e->getMessage() . ' di baris ' . $e->getLine(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan ' . $request->status . ' berhasil dikirim.',
        ]);
    }

    private function haversineDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
