<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absen;
use App\Models\User;

class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily'); // daily, weekly, monthly
        $tanggalFilter = $request->input('tanggal');
        $userIdFilter = $request->input('user_id');

        $query = Absen::selectRaw('
                date,
                user_id,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(approval_status) as approval_status,
                MAX(status_desc) as status_desc,
                MAX(bukti_surat) as bukti_surat,
                MAX(present_user_image) as present_user_image,
                MIN(created_at) as created_at
            ')
            ->with('user')
            ->groupBy('date', 'user_id')
            ->orderBy('date', 'desc');

        if ($userIdFilter) {
            $query->where('user_id', $userIdFilter);
        }

        if ($tanggalFilter) {
            if ($filterType == 'daily') {
                $query->whereDate('date', $tanggalFilter);
            } elseif ($filterType == 'weekly') {
                // $tanggalFilter is expected to be a string like "2023-W45" or just a date within the week
                // For simplicity, let's assume it's just a YYYY-MM-DD date and we get that week
                $startOfWeek = \Carbon\Carbon::parse($tanggalFilter)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::parse($tanggalFilter)->endOfWeek();
                $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
            } elseif ($filterType == 'monthly') {
                // $tanggalFilter is expected to be YYYY-MM
                $startOfMonth = \Carbon\Carbon::parse($tanggalFilter)->startOfMonth();
                $endOfMonth = \Carbon\Carbon::parse($tanggalFilter)->endOfMonth();
                $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }
        }

        $absensi = $query->paginate(10)->withQueryString(); 
        $absensi_pending = Absen::where('approval_status', 'pending')->with('user')->orderBy('created_at', 'desc')->get();
        $users = User::where('role', 'Karyawan')->get();

        return view('content.admin.rekap-absensi.index', compact('absensi', 'absensi_pending', 'users', 'filterType', 'tanggalFilter', 'userIdFilter'));
    }
    public function export(Request $request)
    {
        $tanggal = $request->input('tanggal');
        
        $namaFile = 'Rekap-Absensi-' . ($tanggal ?? 'Semua') . '.xlsx';

        // Download Excel
        return Excel::download(new RekapAbsensiExport($tanggal), $namaFile);
    }

    public function destroy($user_id, $date)
    {
        Absen::where('user_id', $user_id)
            ->whereDate('date', $date)
            ->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }

    public function clearOldData()
    {
        // Hapus data absensi yang usianya lebih dari 30 hari
        $threshold = \Carbon\Carbon::now()->subDays(30);
        $count = Absen::where('created_at', '<', $threshold)->delete();

        return redirect()->back()->with('success', "Berhasil membersihkan $count data absensi lama (lebih dari 30 hari).");
    }

    public function approve($id)
    {
        $absen = Absen::findOrFail($id);
        $absen->update(['approval_status' => 'approved']);
        return redirect()->back()->with('success', 'Pengajuan absensi berhasil disetujui.');
    }

    public function reject($id)
    {
        $absen = Absen::findOrFail($id);
        $absen->update(['approval_status' => 'rejected']);
        return redirect()->back()->with('success', 'Pengajuan absensi ditolak.');
    }

    public function exportExcel(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily');
        $tanggalFilter = $request->input('tanggal');
        $userIdFilter = $request->input('user_id');
        
        $fileName = 'Rekap_Absensi';
        
        if ($userIdFilter) {
            $user = User::find($userIdFilter);
            if ($user) $fileName .= '_' . str_replace(' ', '_', $user->name);
        }

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

        return Excel::download(new \App\Exports\AbsensiExport($filterType, $tanggalFilter, $userIdFilter), $fileName);
    }
}