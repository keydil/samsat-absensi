<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absen;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();

        // 1. MENGHITUNG TOTAL HARI KERJA BULAN INI (Senin-Jumat)
        $totalWorkingDays = $startOfMonth->diffInDaysFiltered(function (Carbon $date) use ($today) {
            return $date->isWeekday() && $date->lte($today);
        });
        if ($totalWorkingDays == 0) $totalWorkingDays = 1;

        // 2. MENGAMBIL DATA ABSEN BULAN INI
        $absenBulanIni = Absen::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $today])
            ->get()
            ->groupBy('date');

        $userHadir = 0;
        $userTelat = 0;
        $userIzinSakit = 0;

        foreach ($absenBulanIni as $date => $records) {
            $status = $records->first()->status;
            if ($status == 'Hadir') $userHadir++;
            elseif ($status == 'Telat') $userTelat++;
            elseif (in_array($status, ['Izin', 'Sakit'])) $userIzinSakit++;
        }

        $userBolos = max(0, $totalWorkingDays - ($userHadir + $userTelat + $userIzinSakit));
        
        // 3. LOGIKA SKOR KEDISIPLINAN
        $score = ($userHadir * 10) + ($userTelat * -5) + ($userBolos * -10);

        // 4. LOGIKA WARNING OTOMATIS
        $warnings = [];
        if ($userTelat > 5) {
            $warnings[] = "Anda telah Terlambat sebanyak {$userTelat} kali bulan ini. Harap perbaiki kedisiplinan Anda.";
        }
        if ($userBolos > 3) {
            $warnings[] = "Anda tercatat Tidak Hadir (Bolos) sebanyak {$userBolos} kali bulan ini. SP (Surat Peringatan) dapat dikeluarkan.";
        }

        $totalHadir = $userHadir + $userTelat; // Untuk total absensi card lama
  
        $riwayat = Absen::selectRaw('
                date,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(approval_status) as approval_status,
                MAX(bukti_surat) as bukti_surat,
                MIN(created_at) as created_at
            ')
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return view('content.karyawan.index', compact(
            'totalHadir', 'riwayat', 'userHadir', 'userTelat', 'userIzinSakit', 'userBolos', 'score', 'warnings'
        ));
    }
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Absen::selectRaw('
                date,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(approval_status) as approval_status,
                MAX(bukti_surat) as bukti_surat,
                MIN(created_at) as created_at
            ')
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if ($request->has('tanggal') && $request->tanggal != null) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $riwayat = $query->paginate(10)->withQueryString();

        return view('content.karyawan.riwayat.riwayat', compact('riwayat'));
    }
}