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

  
        $riwayat = Absen::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('content.karyawan.index', compact('totalHadir', 'riwayat'));
    }
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Absen::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->has('tanggal') && $request->tanggal != null) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $riwayat = $query->paginate(10)->withQueryString();

        return view('content.karyawan.riwayat.riwayat', compact('riwayat'));
    }
}