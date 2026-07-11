<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['code_name' => 'ADM0432204'],
            [
                'username' => 'admin123',
                'name' => 'User Admin',
                'email' => 'admin123@gmail.com',
                'phone' => '6280987654321',
                'role' => 'Admin',
                'password' => Hash::make('password123'),
            ]
        );
        User::firstOrCreate(
            ['code_name' => 'EMP2245810'],
            [
                'username' => 'karyawan123',
                'name' => 'ilham khoerun', // GW GANTI JADI ILHAM SEKALIAN!
                'email' => 'karyawan123@gmail.com',
                'phone' => '6281234567890',
                'role' => 'Karyawan',
                'password' => Hash::make('password123'),
            ]
        );
        User::firstOrCreate(
            ['code_name' => 'KPL9999999'],
            [
                'username' => 'kepala123',
                'name' => 'Bapak Kepala Kantor',
                'email' => 'kepala@samsat.go.id',
                'phone' => '6281112223334',
                'role' => 'Kepala',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
