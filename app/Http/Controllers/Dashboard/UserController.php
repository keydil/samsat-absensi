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

        // 1. MENGHITUNG TOTAL HARI KERJA BULAN INI (Mode Testing Santai)
        // Hitung hari kerja hanya jika ada aktivitas absensi di hari tersebut (mengabaikan Sabtu/Minggu)
        $workingDates = Absen::whereBetween('date', [$startOfMonth, $today])
            ->select('date')
            ->distinct()
            ->pluck('date')
            ->filter(function($date) {
                return Carbon::parse($date)->isWeekday();
            });
            
        $totalWorkingDays = $workingDates->count();
        if ($totalWorkingDays == 0) $totalWorkingDays = 1;

        // 2. MENGAMBIL DATA ABSEN BULAN INI
        $absenBulanIni = Absen::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $today])
            ->get()
            ->groupBy('date');

        $userHadir = 0;
        $userTelat = 0;
        $userIzinSakit = 0;
        
        $warningTelat = 0;
        $warningHadir = 0;
        $warningIzinSakit = 0;

        $lastSpDate = $user->last_sp_at ? Carbon::parse($user->last_sp_at) : null;
        $hasActiveSpThisMonth = $lastSpDate && $lastSpDate->gte($startOfMonth);

        foreach ($absenBulanIni as $date => $records) {
            $status = $records->first()->status;
            if ($status == 'Hadir') {
                $userHadir++;
                if (!$hasActiveSpThisMonth || $date > $lastSpDate->format('Y-m-d')) $warningHadir++;
            }
            elseif ($status == 'Telat') {
                $userTelat++;
                if (!$hasActiveSpThisMonth || $date > $lastSpDate->format('Y-m-d')) $warningTelat++;
            }
            elseif (in_array($status, ['Izin', 'Sakit'])) {
                $userIzinSakit++;
                if (!$hasActiveSpThisMonth || $date > $lastSpDate->format('Y-m-d')) $warningIzinSakit++;
            }
        }

        $userBolos = max(0, $totalWorkingDays - ($userHadir + $userTelat + $userIzinSakit));
        
        // Hitung bolos paska SP
        $workingDaysForWarning = $totalWorkingDays;
        if ($hasActiveSpThisMonth) {
            $workingDaysForWarning = $workingDates->filter(function($d) use ($lastSpDate) {
                return $d > $lastSpDate->format('Y-m-d');
            })->count();
        }
        $warningBolos = max(0, $workingDaysForWarning - ($warningHadir + $warningTelat + $warningIzinSakit));

        // 3. LOGIKA SKOR KEDISIPLINAN
        $score = ($userHadir * 10) + ($userTelat * -5) + ($userBolos * -10);

        // 4. LOGIKA WARNING OTOMATIS (Strike System)
        $warnings = [];
        if ($warningTelat > 5) {
            $warnings[] = "Anda telah Terlambat sebanyak {$warningTelat} kali bulan ini" . ($hasActiveSpThisMonth ? " (Paska SP1)" : "") . ". Harap perbaiki kedisiplinan Anda.";
        }
        if ($warningBolos > 3) {
            $warnings[] = "Anda tercatat Tidak Hadir (Bolos) sebanyak {$warningBolos} kali bulan ini" . ($hasActiveSpThisMonth ? " (Paska SP1)" : "") . ". Tindakan indisipliner lanjutan dapat diberikan.";
        }
        
        // Cek status SP
        $spStatus = $hasActiveSpThisMonth ? (count($warnings) > 0 ? 'SP2' : 'SP1') : 'Aman';

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
            'totalHadir', 'riwayat', 'userHadir', 'userTelat', 'userIzinSakit', 'userBolos', 'score', 'warnings', 'spStatus'
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