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
    public function index()
    {
        $now = Carbon::now('Asia/Jakarta');
        QrCodeModel::where('status', 'active')
            ->where('end_time', '<', $now)
            ->update(['status' => 'expired']);

        $activeQr = QrCodeModel::orderByDesc('created_at')->get();

        return view('content.admin.generate-qr.index', compact('activeQr'));
    }

    public function currentActive()
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        QrCodeModel::where('status', 'active')
            ->where('end_time', '<', $now)
            ->update(['status' => 'expired']);

        $day = strtoupper($now->format('l'));

        $sessions = [
            [
                'type' => 'in_present',
                'label' => 'Absen Masuk',
                'start' => \App\Models\Setting::get("QR_SESSION_IN_START_$day", '07:00'),
                'end' => \App\Models\Setting::get("QR_SESSION_IN_END_$day", '09:00'),
            ],
            [
                'type' => 'out_present',
                'label' => 'Absen Pulang',
                'start' => \App\Models\Setting::get("QR_SESSION_OUT_START_$day", '16:00'),
                'end' => \App\Models\Setting::get("QR_SESSION_OUT_END_$day", '17:00'),
            ],
        ];

        $activeSession = null;
        foreach ($sessions as $session) {
            if ($currentTime >= $session['start'] && $currentTime < $session['end']) {
                $activeSession = $session;
                break;
            }
        }

        if (!$activeSession) {
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

        $startTime = Carbon::parse($today . ' ' . $activeSession['start'], 'Asia/Jakarta');
        $endTime = Carbon::parse($today . ' ' . $activeSession['end'], 'Asia/Jakarta');

        $qr = QrCodeModel::where('date', $today)
            ->where('present', $activeSession['type'])
            ->where('status', 'active')
            ->first();

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

        $timestamp = time();
        $payload = $qr->id . '|' . $timestamp;
        $signature = hash_hmac('sha256', $payload, config('app.key'));
        $secureQrString = $payload . '|' . $signature;

        $qrSvg = QrCode::format('svg')->size(300)->generate($secureQrString);

        return response()->json([
            'active' => true,
            'data' => [
                'qr_id' => $qr->id,
                'code_qr' => $secureQrString,
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

    public function display()
    {
        return view('content.admin.generate-qr.display');
    }

    public function show($code)
    {
        $qr = QrCodeModel::where('code_qr', $code)->firstOrFail();
        $showQR = QrCode::size(200)->generate($code);
        return view('content.admin.generate-qr.show', compact('qr', 'showQR'));
    }

    public function destroy($id)
    {
        $qr = QrCodeModel::findOrFail($id);
        $qr->delete();
        return redirect()->back()->with('message', 'QR Code berhasil dihapus!');
    }
}
