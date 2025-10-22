<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::create([
            'shift_name' => 'Pagi',
            'in_time' => '07:00',
            'out_time' => '15:00',
        ]);

        Shift::create([
            'shift_name' => 'Siang',
            'in_time' => '13:00',
            'out_time' => '21:00',
        ]);

        Shift::create([
            'shift_name' => 'Malam',
            'in_time' => '19:00',
            'out_time' => '03:00',
        ]);
    }
}
