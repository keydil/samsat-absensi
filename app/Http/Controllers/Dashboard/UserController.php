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

        $totalHadir = Absen::where('user_id', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where(function($q) {
                $q->where('status', 'Hadir')
                  ->orWhere('status', 'in_present');
            })
            ->distinct('date')
            ->count('date');

  
        $riwayat = Absen::selectRaw('
                date,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(bukti_surat) as bukti_surat,
                MIN(created_at) as created_at
            ')
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return view('content.karyawan.index', compact('totalHadir', 'riwayat'));
    }
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Absen::selectRaw('
                date,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
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