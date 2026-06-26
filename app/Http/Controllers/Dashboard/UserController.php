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
        $filterType = $request->input('filter_type', 'daily');
        $tanggalFilter = $request->input('tanggal');

        $query = Absen::selectRaw('
                date,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(approval_status) as approval_status,
                MAX(bukti_surat) as bukti_surat,
                MAX(status_desc) as status_desc,
                MIN(created_at) as created_at
            ')
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if ($tanggalFilter) {
            if ($filterType == 'daily') {
                $query->whereDate('date', $tanggalFilter);
            } elseif ($filterType == 'weekly') {
                $startOfWeek = \Carbon\Carbon::parse($tanggalFilter)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::parse($tanggalFilter)->endOfWeek();
                $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
            } elseif ($filterType == 'monthly') {
                $startOfMonth = \Carbon\Carbon::parse($tanggalFilter)->startOfMonth();
                $endOfMonth = \Carbon\Carbon::parse($tanggalFilter)->endOfMonth();
                $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }
        }

        $riwayat = $query->paginate(10)->withQueryString();

        return view('content.karyawan.riwayat.riwayat', compact('riwayat', 'filterType', 'tanggalFilter'));
    }

    public function exportHistory(Request $request)
    {
        $user = Auth::user();
        $filterType = $request->input('filter_type', 'daily');
        $tanggalFilter = $request->input('tanggal');
        
        $fileName = 'Riwayat_Absensi_' . str_replace(' ', '_', $user->name);
        if ($tanggalFilter) {
            if ($filterType == 'daily') {
                $fileName .= '_' . \Carbon\Carbon::parse($tanggalFilter)->format('d_M_Y');
            } elseif ($filterType == 'weekly') {
                $fileName .= '_Minggu_' . \Carbon\Carbon::parse($tanggalFilter)->format('W_Y');
            } elseif ($filterType == 'monthly') {
                $fileName .= '_Bulan_' . \Carbon\Carbon::parse($tanggalFilter)->format('M_Y');
            }
        } else {
            $fileName .= '_Keseluruhan';
        }
        $fileName .= '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AbsensiExport($filterType, $tanggalFilter, $user->id), $fileName);
    }

    public function globalHistory(Request $request)
    {
        $filterType = $request->input('filter_type', 'monthly'); // default monthly
        $tanggalFilter = $request->input('tanggal', date('Y-m-d'));

        $query = Absen::selectRaw('
                date,
                user_id,
                MAX(status) as status
            ')
            ->groupBy('date', 'user_id');

        if ($tanggalFilter) {
            if ($filterType == 'daily') {
                $query->whereDate('date', $tanggalFilter);
            } elseif ($filterType == 'weekly') {
                $startOfWeek = \Carbon\Carbon::parse($tanggalFilter)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::parse($tanggalFilter)->endOfWeek();
                $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
            } elseif ($filterType == 'monthly') {
                $startOfMonth = \Carbon\Carbon::parse($tanggalFilter)->startOfMonth();
                $endOfMonth = \Carbon\Carbon::parse($tanggalFilter)->endOfMonth();
                $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }
            // If 'all', do not apply date filter
        }

        $absenRecords = $query->get();
        $users = \App\Models\User::where('role', 'Karyawan')->get();

        $summary = [];
        foreach ($users as $user) {
            $userAbsens = $absenRecords->where('user_id', $user->id);
            
            $hadir = $userAbsens->where('status', 'Hadir')->count();
            $telat = $userAbsens->where('status', 'Telat')->count();
            $izin = $userAbsens->where('status', 'Izin')->count();
            $sakit = $userAbsens->where('status', 'Sakit')->count();

            // We don't calculate 'Bolos' exactly here to keep it simple, but we can return the available counts
            $summary[] = [
                'user' => $user,
                'hadir' => $hadir,
                'telat' => $telat,
                'izin' => $izin,
                'sakit' => $sakit,
                'total' => $hadir + $telat + $izin + $sakit
            ];
        }

        // Sort by total desc, then name
        usort($summary, function($a, $b) {
            if ($a['total'] == $b['total']) {
                return strcmp($a['user']->name, $b['user']->name);
            }
            return $b['total'] <=> $a['total'];
        });

        return view('content.karyawan.riwayat.global', compact('summary', 'filterType', 'tanggalFilter'));
    }
}