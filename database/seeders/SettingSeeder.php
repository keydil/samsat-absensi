<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'OFFICE_LAT', 'value' => '-6.953797'],
            ['key' => 'OFFICE_LNG', 'value' => '107.766743'],
            ['key' => 'OFFICE_RADIUS_METER', 'value' => '100'],
            
            // Senin
            ['key' => 'TOLERANSI_TELAT_MASUK_MONDAY', 'value' => '08:00'],
            ['key' => 'QR_SESSION_IN_START_MONDAY', 'value' => '07:00'],
            ['key' => 'QR_SESSION_IN_END_MONDAY', 'value' => '09:00'],
            ['key' => 'QR_SESSION_OUT_START_MONDAY', 'value' => '15:00'],
            ['key' => 'QR_SESSION_OUT_END_MONDAY', 'value' => '17:00'],
            
            // Selasa
            ['key' => 'TOLERANSI_TELAT_MASUK_TUESDAY', 'value' => '08:00'],
            ['key' => 'QR_SESSION_IN_START_TUESDAY', 'value' => '07:00'],
            ['key' => 'QR_SESSION_IN_END_TUESDAY', 'value' => '09:00'],
            ['key' => 'QR_SESSION_OUT_START_TUESDAY', 'value' => '15:00'],
            ['key' => 'QR_SESSION_OUT_END_TUESDAY', 'value' => '17:00'],
            
            // Rabu
            ['key' => 'TOLERANSI_TELAT_MASUK_WEDNESDAY', 'value' => '08:00'],
            ['key' => 'QR_SESSION_IN_START_WEDNESDAY', 'value' => '07:00'],
            ['key' => 'QR_SESSION_IN_END_WEDNESDAY', 'value' => '09:00'],
            ['key' => 'QR_SESSION_OUT_START_WEDNESDAY', 'value' => '15:00'],
            ['key' => 'QR_SESSION_OUT_END_WEDNESDAY', 'value' => '17:00'],
            
            // Kamis
            ['key' => 'TOLERANSI_TELAT_MASUK_THURSDAY', 'value' => '08:00'],
            ['key' => 'QR_SESSION_IN_START_THURSDAY', 'value' => '07:00'],
            ['key' => 'QR_SESSION_IN_END_THURSDAY', 'value' => '09:00'],
            ['key' => 'QR_SESSION_OUT_START_THURSDAY', 'value' => '15:00'],
            ['key' => 'QR_SESSION_OUT_END_THURSDAY', 'value' => '17:00'],
            
            // Jumat (Contoh jadwal khusus Jumat)
            ['key' => 'TOLERANSI_TELAT_MASUK_FRIDAY', 'value' => '08:00'],
            ['key' => 'QR_SESSION_IN_START_FRIDAY', 'value' => '07:00'],
            ['key' => 'QR_SESSION_IN_END_FRIDAY', 'value' => '09:00'],
            ['key' => 'QR_SESSION_OUT_START_FRIDAY', 'value' => '14:30'],
            ['key' => 'QR_SESSION_OUT_END_FRIDAY', 'value' => '16:30'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
