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
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        
        $pegawai = User::where('role', 'Karyawan')->get();
        $totalPegawai = $pegawai->count();

        // 1. DATA RINGKASAN HARI INI
        $hadirHariIni = Absen::whereDate('date', $today)->where('status', 'Hadir')->distinct('user_id')->count('user_id');
        $terlambat = Absen::whereDate('date', $today)->where('status', 'Telat')->distinct('user_id')->count('user_id');
        $izinSakit = Absen::whereDate('date', $today)->whereIn('status', ['Izin', 'Sakit'])->distinct('user_id')->count('user_id');
        $tidakHadir = max(0, $totalPegawai - ($hadirHariIni + $terlambat + $izinSakit));

        $riwayatTerbaru = Absen::selectRaw('
                date, user_id,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status, MAX(bukti_surat) as bukti_surat, MIN(created_at) as created_at
            ')
            ->with('user')
            ->groupBy('date', 'user_id')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 2. MENGHITUNG TOTAL HARI KERJA BULAN INI (Mode Testing Santai)
        // Hitung hari kerja hanya jika ada aktivitas absensi di hari tersebut (mengabaikan Sabtu/Minggu)
        $workingDates = Absen::whereBetween('date', [$startOfMonth, $today])
            ->select('date')
            ->distinct()
            ->pluck('date')
            ->filter(function($date) {
                return Carbon::parse($date)->isWeekday();
            });
            
        $totalWorkingDays = $workingDates->count();
        if ($totalWorkingDays == 0) $totalWorkingDays = 1; // Prevent division by zero

        // Ambil semua absen bulan ini
        $absenBulanIni = Absen::whereBetween('date', [$startOfMonth, $today])->get();

        // 3. STATISTIK BULANAN (Untuk Doughnut Chart)
        $monthlyHadir = $absenBulanIni->where('status', 'Hadir')->groupBy(fn($item) => $item->date . '-' . $item->user_id)->count();
        $monthlyTelat = $absenBulanIni->where('status', 'Telat')->groupBy(fn($item) => $item->date . '-' . $item->user_id)->count();
        $monthlyIzinSakit = $absenBulanIni->whereIn('status', ['Izin', 'Sakit'])->groupBy(fn($item) => $item->date . '-' . $item->user_id)->count();
        
        $totalPossibleAbsen = $totalWorkingDays * $totalPegawai;
        $monthlyBolos = max(0, $totalPossibleAbsen - ($monthlyHadir + $monthlyTelat + $monthlyIzinSakit));

        $monthlyChartData = [$monthlyHadir, $monthlyTelat, $monthlyIzinSakit, $monthlyBolos];

        // 4. STATISTIK TREN 7 HARI TERAKHIR (Untuk Bar Chart)
        $weeklyChartLabels = [];
        $weeklyHadirData = [];
        $weeklyTelatData = [];
        $weeklyBolosData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            if ($date->isWeekend()) continue; // Skip weekend on chart

            $weeklyChartLabels[] = $date->translatedFormat('d M');
            
            $dayAbsens = Absen::whereDate('date', $date)->get()->groupBy('user_id');
            $dayHadir = 0;
            $dayTelat = 0;
            $dayIzinSakit = 0;

            foreach ($dayAbsens as $userId => $records) {
                $status = $records->first()->status;
                if ($status == 'Hadir') $dayHadir++;
                elseif ($status == 'Telat') $dayTelat++;
                elseif (in_array($status, ['Izin', 'Sakit'])) $dayIzinSakit++;
            }

            $weeklyHadirData[] = $dayHadir;
            $weeklyTelatData[] = $dayTelat;
            $weeklyBolosData[] = max(0, $totalPegawai - ($dayHadir + $dayTelat + $dayIzinSakit));
        }

        // 5. MENGHITUNG SKOR KEDISIPLINAN, RANKING & WARNINGS
        $pegawaiRankings = [];
        $usersWithWarnings = []; // <-- TAMBAHAN BARU
        
        foreach ($pegawai as $user) {
            $userAbsens = $absenBulanIni->where('user_id', $user->id)->groupBy('date');
            
            $userHadir = 0;
            $userTelat = 0;
            $userIzinSakit = 0;

            foreach ($userAbsens as $date => $records) {
                $status = $records->first()->status;
                if ($status == 'Hadir') $userHadir++;
                elseif ($status == 'Telat') $userTelat++;
                elseif (in_array($status, ['Izin', 'Sakit'])) $userIzinSakit++;
            }

            $userBolos = max(0, $totalWorkingDays - ($userHadir + $userTelat + $userIzinSakit));
            
            // LOGIKA WARNING
            $userWarnings = [];
            if ($userTelat > 5) $userWarnings[] = "Sering Terlambat ($userTelat kali)";
            if ($userBolos > 3) $userWarnings[] = "Sering Bolos ($userBolos kali)";
            
            if (count($userWarnings) > 0) {
                $usersWithWarnings[] = [
                    'user' => $user,
                    'issues' => $userWarnings
                ];
            }

            // LOGIKA SKOR: Hadir(+10), Telat(-5), Bolos(-10)
            $score = ($userHadir * 10) + ($userTelat * -5) + ($userBolos * -10);

            $pegawaiRankings[] = [
                'user' => $user,
                'score' => $score,
                'hadir' => $userHadir,
                'telat' => $userTelat,
                'bolos' => $userBolos
            ];
        }

        // Sort by score DESC
        usort($pegawaiRankings, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Split Top 3 and Bottom 3
        $topPegawai = array_slice($pegawaiRankings, 0, 3);
        
        $bottomPegawai = [];
        if (count($pegawaiRankings) > 3) {
            // Ambil sisa user setelah dipotong Top 3
            $sisaPegawai = array_slice($pegawaiRankings, 3);
            
            // Ambil maksimal 3 user dari yang paling bawah
            $bottomPegawai = array_slice($sisaPegawai, -3, 3);
            
            // Balik urutannya biar yang paling minus di nomor 1
            $bottomPegawai = array_reverse($bottomPegawai);
        }

        return view('content.admin.index', compact(
            'totalPegawai', 'hadirHariIni', 'terlambat', 'izinSakit', 'tidakHadir', 'riwayatTerbaru',
            'monthlyChartData', 'weeklyChartLabels', 'weeklyHadirData', 'weeklyTelatData', 'weeklyBolosData',
            'topPegawai', 'bottomPegawai', 'usersWithWarnings'
        ));
    }

    public function export(Request $request)
    {
        $tanggal = $request->input('tanggal');
        
        $namaFile = 'Rekap-Absensi-' . ($tanggal ?? 'Semua') . '.xlsx';

        return Excel::download(new RekapAbsensiExport($tanggal), $namaFile);
    }
}