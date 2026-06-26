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
        $messages = [];
        
        foreach ($days as $day) {
            $dayName = ucfirst(strtolower($day));
            $rules["QR_SESSION_IN_START_$day"] = 'required|date_format:H:i';
            $rules["QR_SESSION_IN_END_$day"] = "required|date_format:H:i|after:QR_SESSION_IN_START_$day";
            $rules["TOLERANSI_TELAT_MASUK_$day"] = "required|date_format:H:i|after_or_equal:QR_SESSION_IN_START_$day";
            $rules["QR_SESSION_OUT_START_$day"] = "required|date_format:H:i|after:QR_SESSION_IN_END_$day";
            $rules["QR_SESSION_OUT_END_$day"] = "required|date_format:H:i|after:QR_SESSION_OUT_START_$day";

            $messages["QR_SESSION_IN_END_$day.after"] = "Sesi Masuk (Selesai) hari $dayName harus lebih besar dari Sesi Masuk (Mulai).";
            $messages["TOLERANSI_TELAT_MASUK_$day.after_or_equal"] = "Batas Toleransi hari $dayName tidak boleh kurang dari Sesi Masuk (Mulai).";
            $messages["QR_SESSION_OUT_START_$day.after"] = "Sesi Pulang (Mulai) hari $dayName harus lebih besar dari Sesi Masuk (Selesai).";
            $messages["QR_SESSION_OUT_END_$day.after"] = "Sesi Pulang (Selesai) hari $dayName harus lebih besar dari Sesi Pulang (Mulai).";
        }

        $validated = $request->validate($rules, $messages);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
