<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\QrCode as QrCodeModel;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanQRController extends Controller
{
    // 🟢 Halaman utama
    public function index()
    {
        $absens = Absen::with('shift')->where('user_id', Auth::id())->latest('date')->get();

        return view('content.karyawan.absensi-qr.index', compact('absens'));
    }

    // 🟡 Cek QR valid atau tidak
    public function check(Request $request)
    {
        $qr = QrCodeModel::with('shift')->where('code_qr', $request->code_qr)->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak ditemukan.',
            ]);
        }

        // Cek status aktif & waktu aktif (pakai timezone WIB)
        $now = Carbon::now('Asia/Jakarta');
        $start = Carbon::parse($qr->start_time)->setTimezone('Asia/Jakarta');
        $end = Carbon::parse($qr->end_time)->setTimezone('Asia/Jakarta');

        if ($qr->status != 'active' || $now->lt($start) || $now->gt($end)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah tidak aktif atau di luar waktu absensi.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_id' => $qr->id,
                'shift_id' => $qr->shift_id,
                'shift' => $qr->shift->shift_name ?? 'Harian',
                'present_type' => $qr->present == 'in_present' ? 'Masuk' : 'Keluar',
                'date' => Carbon::parse($qr->date)->format('d-m-Y'),
            ],
        ]);
    }

    // 🔵 Simpan absensi
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'qr_id' => 'required|exists:qr_codes,id',
                'status' => 'required|in:Hadir,Izin,Sakit',
                'face_image' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $e->getMessage(),
            ]);
        }

        $user = Auth::user();
        $qr = QrCodeModel::find($request->qr_id);

        if (!$qr) {
            return response()->json(['success' => false, 'message' => 'QR tidak ditemukan.']);
        }

        // 🔒 Cek QR masih aktif
        $now = Carbon::now('Asia/Jakarta');
        $start = Carbon::parse($qr->start_time)->setTimezone('Asia/Jakarta');
        $end = Carbon::parse($qr->end_time)->setTimezone('Asia/Jakarta');

        if ($qr->status != 'active' || $now->lt($start) || $now->gt($end)) {
            return response()->json([
                'success' => false,
                'message' => 'QR tidak aktif. Status: ' . $qr->status . ' | Sekarang: ' . $now . ' | Aktif: ' . $start . ' s/d ' . $end,
            ]);
        }

        // ⛔ Cek sudah absen
        $already = Absen::where('user_id', $user->id)->where('qr_code_id', $qr->id)->exists();
        if ($already) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah melakukan absensi ini.']);
        }

        // 📍 Validasi radius
        $officeLat = (float) env('OFFICE_LAT', -6.9824624);
        $officeLng = (float) env('OFFICE_LNG', 107.7540507);
        $maxRadius = (float) env('OFFICE_RADIUS_METER', 50);
        $distance = $this->haversineDistance($request->latitude, $request->longitude, $officeLat, $officeLng);

        if ($distance > $maxRadius) {
            return response()->json([
                'success' => false,
                'message' => "Di luar radius. Jarak: {$distance}m, Maks: {$maxRadius}m",
            ]);
        }

        // 📸 Simpan foto
        $faceImagePath = null;
        try {
            if ($request->filled('face_image')) {
                if (app()->environment('production')) {
                    // Cloudinary butuh temp file, bukan base64 string langsung
                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->face_image);
                    $imageDecoded = base64_decode($imageData);
                    $tempPath = tempnam(sys_get_temp_dir(), 'face_') . '.jpg';
                    file_put_contents($tempPath, $imageDecoded);

                    $uploaded = cloudinary()->upload($tempPath, [
                        'folder' => 'absensi-faces',
                        'public_id' => 'face_' . $user->id . '_' . time(),
                    ]);
                    $faceImagePath = $uploaded->getSecurePath();

                    // Hapus temp file
                    @unlink($tempPath);
                } else {
                    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $request->face_image);
                    $imageDecoded = base64_decode($imageData);
                    $filename = 'face_' . $user->id . '_' . time() . '.jpg';
                    $path = public_path('images/absensi/' . $filename);
                    if (!file_exists(public_path('images/absensi'))) {
                        mkdir(public_path('images/absensi'), 0755, true);
                    }
                    file_put_contents($path, $imageDecoded);
                    $faceImagePath = asset('images/absensi/' . $filename);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan foto: ' . $e->getMessage(),
            ]);
        }

        try {
            Absen::create([
                'user_id' => $user->id,
                'shift_id' => null,
                'qr_code_id' => $qr->id,
                'date' => $qr->date,
                'time' => Carbon::now('Asia/Jakarta')->format('H:i'),
                'status' => $request->status,
                'status_desc' => 'Absensi via QR Code',
                'present_desc_system' => 'Absen ' . ($qr->present == 'in_present' ? 'Masuk' : 'Keluar'),
                'present_user_image' => $faceImagePath,
                'lat_location_present' => $request->latitude,
                'lng_location_present' => $request->longitude,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan absensi: ' . $e->getMessage(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Absensi berhasil disimpan.']);
    }

    // Helper hitung jarak GPS (meter)
    private function haversineDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
