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
        User::create([
            'code_name' => 'ADM0432204',
            'name' => 'User Admin',
            'email' => 'admin123@gmail.com',
            'phone' => '6280987654321',
            'role' => 'Admin',
            'username' => 'admin123',
            'password' => Hash::make('password123'),
        ]);
        User::create([
            'code_name' => 'EMP2245810',
            'name' => 'User Karyawan',
            'email' => 'karyawan123@gmail.com',
            'phone' => '6281234567890',
            'role' => 'Karyawan',
            'username' => 'karyawan123',
            'password' => Hash::make('password123'),
        ]);
    }
}
