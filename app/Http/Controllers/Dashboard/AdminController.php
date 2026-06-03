<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absen;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $totalPegawai = User::where('role', 'Karyawan')->count();

        $today = Carbon::today();

        $hadirHariIni = Absen::whereDate('date', $today)
            ->where('status', 'Hadir')
            ->distinct('user_id')
            ->count('user_id');

        $terlambat = Absen::whereDate('date', $today)
            ->where('status', 'Telat')
            ->distinct('user_id')
            ->count('user_id');

        $izinSakit = Absen::whereDate('date', $today)
            ->whereIn('status', ['Izin', 'Sakit'])
            ->distinct('user_id')
            ->count('user_id');

        $tidakHadir = $totalPegawai - ($hadirHariIni + $terlambat + $izinSakit);
        if($tidakHadir < 0) $tidakHadir = 0; // Jaga-jaga error minus

        $riwayatTerbaru = Absen::selectRaw('
                date,
                user_id,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(bukti_surat) as bukti_surat,
                MIN(created_at) as created_at
            ')
            ->with('user')
            ->groupBy('date', 'user_id')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('content.admin.index', compact(
            'totalPegawai', 
            'hadirHariIni', 
            'terlambat', 
            'izinSakit',
            'tidakHadir',
            'riwayatTerbaru'
        ));
    }
    public function export(Request $request)
    {
        $tanggal = $request->input('tanggal');
        
        $namaFile = 'Rekap-Absensi-' . ($tanggal ?? 'Semua') . '.xlsx';

        return Excel::download(new RekapAbsensiExport($tanggal), $namaFile);
    }
}