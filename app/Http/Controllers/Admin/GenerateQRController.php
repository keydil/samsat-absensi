<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\QrCode as QrCodeModel;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQRController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        QrCodeModel::where('status', 'active')
            ->where('end_time', '<', $now)
            ->update(['status' => 'expired']);

        $shifts = Shift::all();
        $activeQr = QrCodeModel::with('shift')
            // ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get();

        return view('content.admin.generate-qr.index', compact('shifts', 'activeQr'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'present_type' => 'required|in:in_present,out_present',
                'date' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
            ],
            [
                'present_type.required' => 'Silahkan pilih jenis absen',
                'date.required' => 'Masukkan tanggal absen',
                'start_time.required' => 'Masukkan waktu mulai aktif QR',
                'end_time.required' => 'Masukkan waktu berakhir QR',
            ],
        );

        // ✅ CEK DOBEL: tanggal + tipe yang sama ga boleh ada QR aktif lagi
        $exists = QrCodeModel::where('date', $request->date)->where('present', $request->present_type)->where('status', 'active')->exists();

        if ($exists) {
            $label = $request->present_type == 'in_present' ? 'Absen Masuk' : 'Absen Pulang';
            return redirect()
                ->back()
                ->withErrors(['duplicate' => "QR {$label} untuk tanggal {$request->date} sudah ada dan masih aktif!"])
                ->withInput();
        }

        $start_time = Carbon::parse($request->date . ' ' . $request->start_time);
        $end_time = Carbon::parse($request->date . ' ' . $request->end_time);
        $qr_code_value = Str::uuid()->toString();

        QrCodeModel::create([
            'shift_id' => null, // shift dihapus dari flow
            'code_qr' => $qr_code_value,
            'present' => $request->present_type,
            'date' => $request->date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => 'active',
        ]);

        return redirect()
            ->back()
            ->with([
                'message' => 'QR Code berhasil dibuat!',
                'qr_code_value' => $qr_code_value,
                'qr_present_type' => $request->present_type,
            ]);
    }

    public function show($code)
    {
        $qr = QrCodeModel::with('shift')->where('code_qr', $code)->firstOrFail();
        $showQR = QrCode::size(200)->generate($code);
        return view('content.admin.generate-qr.show', compact('qr', 'showQR'));
    }
}
