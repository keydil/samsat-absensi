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
        $tanggalFilter = $request->input('tanggal');

        $query = Absen::selectRaw('
                date,
                user_id,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(approval_status) as approval_status,
                MAX(status_desc) as status_desc,
                MAX(bukti_surat) as bukti_surat,
                MIN(created_at) as created_at
            ')
            ->with('user')
            ->groupBy('date', 'user_id')
            ->orderBy('date', 'desc');

        if ($tanggalFilter) {
            $query->whereDate('created_at', $tanggalFilter);
        }

        $absensi = $query->paginate(10)->withQueryString(); // withQueryString biar pagination gak ngereset filter

        // Data Menunggu Persetujuan
        $absensi_pending = Absen::where('approval_status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('content.admin.rekap-absensi.index', compact('absensi', 'absensi_pending'));
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
}