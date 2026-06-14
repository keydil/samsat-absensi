<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Absen;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoAbsenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ambil semua Karyawan
        $karyawans = User::where('role', 'Karyawan')->get();
        if ($karyawans->isEmpty()) {
            $this->command->info('Tidak ada user Karyawan ditemukan.');
            return;
        }

        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();

        // Cari tahu tanggal kemarin (biar nggak nimpa tanggal 15 yang hari ini)
        $endDate = $today->copy()->subDay();
        if ($endDate->lt($startOfMonth)) {
            $endDate = $startOfMonth; // jaga-jaga kalau tanggal 1
        }

        // HAPUS BARIS INI: Absen::whereBetween('date', [$startOfMonth, $today])->delete();

        $this->command->info('Mulai menyuntikkan data absen dari ' . $startOfMonth->format('Y-m-d') . ' sampai ' . $endDate->format('Y-m-d'));

        foreach ($karyawans as $user) {
            $currentDate = $startOfMonth->copy();
            
            // Cek apakah user ini adalah tumbal
            $isTumbal = Str::contains(strtolower($user->name), 'fadhil') || Str::contains(strtolower($user->name), 'yudi');

            while ($currentDate->lte($endDate)) {
                // Skip Sabtu & Minggu
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();
                    continue;
                }

                // JANGAN TIMPA DATA YANG UDAH ADA (Testing Manual dari User)
                $existingAbsen = Absen::where('user_id', $user->id)
                                      ->whereDate('date', $currentDate->format('Y-m-d'))
                                      ->exists();
                
                if ($existingAbsen) {
                    $currentDate->addDay();
                    continue;
                }

                $status = 'Hadir';
                $jamMasuk = '07:45:00'; // Default Hadir pagi

                if ($isTumbal) {
                    // Skenario TUMBAL (Fadhil & Yudi)
                    // Kita bikin dia Telat 6x dan Bolos 4x
                    $rand = rand(1, 100);
                    if ($rand <= 40) {
                        // 40% Chance Bolos (Tidak buat record sama sekali)
                        $currentDate->addDay();
                        continue;
                    } elseif ($rand <= 100) {
                        // 60% Chance Telat (Jam 08:30)
                        $status = 'Telat';
                        $jamMasuk = '08:30:00';
                    }
                } else {
                    // Skenario NORMAL (Karyawan Baik)
                    // 85% Hadir, 15% Telat, 0% Bolos
                    $rand = rand(1, 100);
                    if ($rand <= 85) {
                        $status = 'Hadir';
                        $jamMasuk = '07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    } else {
                        $status = 'Telat';
                        $jamMasuk = '08:15:00';
                    }
                }

                // Insert Absen Masuk
                $absenDatetime = $currentDate->format('Y-m-d') . ' ' . $jamMasuk;
                
                Absen::create([
                    'user_id' => $user->id,
                    'present_desc_system' => 'Memulai Scan QrCode Masuk pada : ' . Carbon::parse($absenDatetime)->format('H:i:s'),
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => $status,
                    'approval_status' => 'approved',
                    'lat_location_present' => -6.9202,
                    'lng_location_present' => 107.603,
                    'time' => $jamMasuk,
                    'created_at' => $absenDatetime,
                    'updated_at' => $absenDatetime,
                ]);

                $currentDate->addDay();
            }
            
            $this->command->info("Data absensi berhasil diinjeksi untuk: {$user->name} " . ($isTumbal ? '[TUMBAL]' : '[BAIK]'));
        }
        
        $this->command->info('Seeding selesai!');
    }
}
