<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QrCode as QrCodeModel;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQRController extends Controller
{
    /**
     * Halaman utama QR Code — Auto-Display + Riwayat (read-only).
     * Form manual dihapus, QR di-generate otomatis oleh sistem.
     */
    public function index()
    {
        // Auto-expire QR yang sudah lewat waktunya
        $now = Carbon::now('Asia/Jakarta');
        QrCodeModel::where('status', 'active')
            ->where('end_time', '<', $now)
            ->update(['status' => 'expired']);

        // Ambil semua riwayat QR (read-only)
        $activeQr = QrCodeModel::orderByDesc('created_at')
            ->get();

        return view('content.admin.generate-qr.index', compact('activeQr'));
    }

    /**
     * API Endpoint: Cek & auto-generate QR Code aktif berdasarkan jadwal sesi.
     * GET /api/qr/current-active
     *
     * Flow:
     * 1. Cek waktu sekarang terhadap jadwal sesi dari .env
     * 2. Jika dalam sesi → cek DB, ada QR aktif? Return. Belum? Auto-generate.
     * 3. Jika di luar sesi → return { active: false }
     */
    public function currentActive()
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        // Auto-expire QR yang sudah lewat waktunya
        QrCodeModel::where('status', 'active')
            ->where('end_time', '<', $now)
            ->update(['status' => 'expired']);

        // Konfigurasi jadwal sesi dari .env
        $sessions = [
            [
                'type' => 'in_present',
                'label' => 'Absen Masuk',
                'start' => env('QR_SESSION_IN_START', '07:00'),
                'end' => env('QR_SESSION_IN_END', '09:00'),
            ],
            [
                'type' => 'out_present',
                'label' => 'Absen Pulang',
                'start' => env('QR_SESSION_OUT_START', '16:00'),
                'end' => env('QR_SESSION_OUT_END', '17:00'),
            ],
        ];

        // Cek apakah sekarang masuk salah satu sesi
        $activeSession = null;
        foreach ($sessions as $session) {
            if ($currentTime >= $session['start'] && $currentTime < $session['end']) {
                $activeSession = $session;
                break;
            }
        }

        if (!$activeSession) {
            // Hitung sesi berikutnya
            $nextSession = null;
            foreach ($sessions as $session) {
                if ($currentTime < $session['start']) {
                    $nextSession = $session;
                    break;
                }
            }

            return response()->json([
                'active' => false,
                'message' => 'Di luar jam absensi.',
                'current_time' => $now->format('H:i:s'),
                'next_session' => $nextSession ? [
                    'label' => $nextSession['label'],
                    'start' => $nextSession['start'],
                ] : null,
            ]);
        }

        // Cek apakah sudah ada QR aktif untuk sesi ini hari ini
        $startTime = Carbon::parse($today . ' ' . $activeSession['start'], 'Asia/Jakarta');
        $endTime = Carbon::parse($today . ' ' . $activeSession['end'], 'Asia/Jakarta');

        $qr = QrCodeModel::where('date', $today)
            ->where('present', $activeSession['type'])
            ->where('status', 'active')
            ->first();

        // Jika belum ada → auto-generate
        if (!$qr) {
            $qr_code_value = Str::uuid()->toString();

            $qr = QrCodeModel::create([
                'shift_id' => null,
                'code_qr' => $qr_code_value,
                'present' => $activeSession['type'],
                'date' => $today,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'active',
            ]);
        }

        // Generate SVG QR Code
        $qrSvg = QrCode::format('svg')->size(300)->generate($qr->code_qr);

        return response()->json([
            'active' => true,
            'data' => [
                'qr_id' => $qr->id,
                'code_qr' => $qr->code_qr,
                'present_type' => $activeSession['type'],
                'session_label' => $activeSession['label'],
                'start_time' => Carbon::parse($qr->start_time)->format('H:i'),
                'end_time' => Carbon::parse($qr->end_time)->format('H:i'),
                'date' => $qr->date,
                'svg' => base64_encode($qrSvg),
            ],
            'current_time' => $now->format('H:i:s'),
        ]);
    }

    /**
     * Halaman Kiosk / Display mode untuk TV kantor.
     * Full-screen, auto-polling, tanpa sidebar.
     */
    public function display()
    {
        return view('content.admin.generate-qr.display');
    }

    /**
     * Menampilkan detail QR Code tertentu.
     */
    public function show($code)
    {
        $qr = QrCodeModel::where('code_qr', $code)->firstOrFail();
        $showQR = QrCode::size(200)->generate($code);
        return view('content.admin.generate-qr.show', compact('qr', 'showQR'));
    }

    /**
     * Hapus QR Code.
     */
    public function destroy($id)
    {
        $qr = QrCodeModel::findOrFail($id);
        $qr->delete();
        return redirect()->back()->with('message', 'QR Code berhasil dihapus!');
    }
}
