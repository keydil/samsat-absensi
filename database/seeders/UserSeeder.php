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
            'name' => 'Rizky Chandra',
            'email' => 'rizkychandra2204@gmail.com',
            'phone' => '62895605050961',
            'role' => 'Admin',
            'username' => 'chandra22',
            'password' => Hash::make('password123'),
        ]);
        User::create([
            'code_name' => 'EMP2245810',
            'name' => 'Chandra Khusuma',
            'email' => 'rizkycandrakhusuma@gmail.com',
            'phone' => '6285860517808',
            'role' => 'Karyawan',
            'username' => 'khusuma22',
            'password' => Hash::make('password123'),
        ]);
    }
}
