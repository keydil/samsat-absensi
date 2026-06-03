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
            ['key' => 'TOLERANSI_TELAT_MASUK', 'value' => '08:00'],
            ['key' => 'QR_SESSION_IN_START', 'value' => '07:00'],
            ['key' => 'QR_SESSION_IN_END', 'value' => '09:00'],
            ['key' => 'QR_SESSION_OUT_START', 'value' => '15:00'],
            ['key' => 'QR_SESSION_OUT_END', 'value' => '17:00'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}
