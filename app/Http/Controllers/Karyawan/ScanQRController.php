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
        $absens = Absen::with('shift')
            ->where('user_id', Auth::id())
            ->latest('date')
            ->get();

        return view('content.karyawan.absensi-qr.index', compact('absens'));
    }

    // 🟡 Cek QR valid atau tidak
    public function check(Request $request)
    {
        $qr = QrCodeModel::with('shift')->where('code_qr', $request->code_qr)->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak ditemukan.'
            ]);
        }

        // Cek status aktif & waktu aktif
        $now = Carbon::now();
        if (
            $qr->status != 'active' ||
            $now->lt(Carbon::parse($qr->start_time)) ||
            $now->gt(Carbon::parse($qr->end_time))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah tidak aktif atau di luar waktu absensi.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_id' => $qr->id,
                'shift_id' => $qr->shift_id,
                'shift' => $qr->shift->shift_name,
                'present_type' => $qr->present == 'in_present' ? 'Masuk' : 'Keluar',
                'date' => Carbon::parse($qr->date)->format('d-m-Y')
            ]
        ]);
    }

    // 🔵 Simpan absensi
    public function store(Request $request)
    {
        $request->validate([
            'qr_id' => 'required|exists:qr_codes,id',
            'shift_id' => 'required|exists:shifts,id',
            'status' => 'required|in:Hadir,Izin,Sakit',
            'status_desc' => 'nullable|string'
        ]);

        $user = Auth::user();
        $qr = QrCodeModel::with('shift')->find($request->qr_id);

        // 🔒 Cek QR masih aktif
        $now = Carbon::now();
        if (
            $qr->status != 'active' ||
            $now->lt(Carbon::parse($qr->start_time)) ||
            $now->gt(Carbon::parse($qr->end_time))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah tidak aktif atau di luar waktu absensi.'
            ]);
        }

        // ⛔ Cek sudah absen di shift ini belum
        $already = Absen::where('user_id', $user->id)
        ->where('qr_code_id', $qr->id)
            ->where('date', $qr->date)
            ->first();

        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah melakukan absensi pada shift ini.'
            ]);
        }

        // 🧠 Cek lembur
        $isLembur = false;

        // Cari absen terakhir user hari ini
        $lastAbsen = Absen::where('user_id', $user->id)
            ->where('date', $qr->date)
            ->orderByDesc('created_at')
            ->first();

        if ($lastAbsen) {
            // Jika shift baru memiliki urutan lebih tinggi (misal: shift pagi = 1, shift malam = 2)
            // maka dianggap lembur
            $currentShift = Shift::find($qr->shift_id);
            $previousShift = Shift::find($lastAbsen->shift_id);

            if ($previousShift && $currentShift && $currentShift->id > $previousShift->id) {
                $isLembur = true;
            }
        }

        // 📝 Simpan data absensi
        Absen::create([
            'user_id' => $user->id,
            'shift_id' => $qr->shift_id,
            'qr_code_id' => $qr->id,
            'date' => $qr->date,
            'time' => Carbon::now()->format('H:i'),
            'status' => $request->status,
            'status_desc' => $request->status_desc,
            'present_desc_system' => $isLembur
                ? 'Lembur pada shift ' . $qr->shift->shift_name
                : 'Absen ' . ($qr->present == 'in_present' ? 'Masuk' : 'Keluar'),
            'hours' => $isLembur ? 'Lembur' : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $isLembur
                ? 'Absensi lembur berhasil disimpan.'
                : 'Absensi berhasil disimpan.'
        ]);
    }
}
