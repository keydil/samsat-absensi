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
        $rules = [
            'OFFICE_LAT' => 'required|numeric',
            'OFFICE_LNG' => 'required|numeric',
            'OFFICE_RADIUS_METER' => 'required|numeric',
        ];

        // Looping validasi per-hari
        $days = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY'];
        foreach ($days as $day) {
            $rules["TOLERANSI_TELAT_MASUK_$day"] = 'required|string';
            $rules["QR_SESSION_IN_START_$day"] = 'required|string';
            $rules["QR_SESSION_IN_END_$day"] = 'required|string';
            $rules["QR_SESSION_OUT_START_$day"] = 'required|string';
            $rules["QR_SESSION_OUT_END_$day"] = 'required|string';
        }

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
