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

        $hadirHariIni = Absen::whereDate('created_at', Carbon::today())
            ->where(function($q) {
                $q->where('status', 'Hadir')
                  ->orWhere('status', 'in_present');
            })
            ->distinct('user_id')
            ->count('user_id');

        $terlambat = 0; 

        $tidakHadir = $totalPegawai - $hadirHariIni;
        if($tidakHadir < 0) $tidakHadir = 0; // Jaga-jaga error minus

        $riwayatTerbaru = Absen::with(['user', 'shift'])
            ->latest()
            ->take(5)
            ->get();

        return view('content.admin.index', compact(
            'totalPegawai', 
            'hadirHariIni', 
            'terlambat', 
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