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

        $query = Absen::with(['user'])
            ->orderBy('created_at', 'desc');

        if ($tanggalFilter) {
            $query->whereDate('created_at', $tanggalFilter);
        }

        $absensi = $query->paginate(10)->withQueryString(); // withQueryString biar pagination gak ngereset filter

        return view('content.admin.rekap-absensi.index', compact('absensi'));
    }
    public function export(Request $request)
    {
        $tanggal = $request->input('tanggal');
        
        $namaFile = 'Rekap-Absensi-' . ($tanggal ?? 'Semua') . '.xlsx';

        // Download Excel
        return Excel::download(new RekapAbsensiExport($tanggal), $namaFile);
    }
}