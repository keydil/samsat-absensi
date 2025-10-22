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
        // Update otomatis QR yang sudah lewat waktu menjadi expired
        $now = Carbon::now();
        QrCodeModel::where('status', 'active')
            ->where('end_time', '<', $now)
            ->update(['status' => 'expired']);

        // Ambil data shift dan QR yang masih aktif
        $shifts = Shift::all();
        $activeQr = QrCodeModel::with('shift')
            // ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get();

        return view('content.admin.generate-qr.index', compact('shifts', 'activeQr'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'present_type' => 'required|in:in_present,out_present',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ],[
            'shift_id.required' => 'Silahkan pilih shift jam kerja',
            'present_type.required' => 'Silahkan pilih jenis absen',
            'date.required' => 'Masukkan tanggal absen',
            'start_time.required' => 'Masukkan waktu mulai aktif QR',
            'end_time.required' => 'Masukkan waktu berakhir QR',
        ]);

        $shift = Shift::findOrFail($request->shift_id);

        // Gabungkan tanggal + waktu menjadi datetime
        $start_time = Carbon::parse($request->date . ' ' . $request->start_time);
        $end_time   = Carbon::parse($request->date . ' ' . $request->end_time);

        // Generate kode unik QR
        $qr_code_value = Str::uuid()->toString();

        // Simpan ke database
        QrCodeModel::create([
            'shift_id' => $shift->id,
            'code_qr' => $qr_code_value,
            'present' => $request->present_type,
            'date' => $request->date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => 'active', 
        ]);

        return redirect()->back()->with([
            'message' => 'QR Code berhasil dibuat!',
            'date' => $request->date,
            'qr_code_value' => $qr_code_value,
            'qr_shift_name' => $shift->shift_name,
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
