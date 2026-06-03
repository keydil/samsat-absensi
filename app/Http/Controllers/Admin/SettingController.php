<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('content.admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'OFFICE_LAT' => 'required|numeric',
            'OFFICE_LNG' => 'required|numeric',
            'OFFICE_RADIUS_METER' => 'required|numeric',
            'TOLERANSI_TELAT_MASUK' => 'required|string',
            'QR_SESSION_IN_START' => 'required|string',
            'QR_SESSION_IN_END' => 'required|string',
            'QR_SESSION_OUT_START' => 'required|string',
            'QR_SESSION_OUT_END' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
