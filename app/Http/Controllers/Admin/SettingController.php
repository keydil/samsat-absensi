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
        $indoDays = [
            'MONDAY' => 'Senin',
            'TUESDAY' => 'Selasa',
            'WEDNESDAY' => 'Rabu',
            'THURSDAY' => 'Kamis',
            'FRIDAY' => 'Jumat'
        ];
        
        $messages = [];
        
        foreach ($days as $day) {
            $dayName = $indoDays[$day];
            
            $rules["QR_SESSION_IN_START_$day"] = 'required|date_format:H:i';
            $rules["QR_SESSION_IN_END_$day"] = "required|date_format:H:i|after:QR_SESSION_IN_START_$day";
            $rules["TOLERANSI_TELAT_MASUK_$day"] = "required|date_format:H:i|after_or_equal:QR_SESSION_IN_START_$day";
            $rules["QR_SESSION_OUT_START_$day"] = "required|date_format:H:i|after:QR_SESSION_IN_END_$day";
            $rules["QR_SESSION_OUT_END_$day"] = "required|date_format:H:i|after:QR_SESSION_OUT_START_$day";

            $messages["QR_SESSION_IN_END_$day.after"] = "Jam Selesai Masuk di hari $dayName tidak boleh lebih awal dari Jam Mulai Masuk.";
            $messages["TOLERANSI_TELAT_MASUK_$day.after_or_equal"] = "Batas Telat di hari $dayName tidak masuk akal jika diset sebelum Jam Mulai Masuk.";
            $messages["QR_SESSION_OUT_START_$day.after"] = "Jam Mulai Pulang di hari $dayName harus lebih besar dari Jam Selesai Masuk.";
            $messages["QR_SESSION_OUT_END_$day.after"] = "Jam Selesai Pulang di hari $dayName tidak boleh lebih awal dari Jam Mulai Pulang.";
        }

        $validated = $request->validate($rules, $messages);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
