<?php

namespace App\Exports;

use App\Models\Absen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class RekapAbsensiExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $tanggal;

    public function __construct($tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        $query = Absen::with(['user', 'shift'])->orderBy('created_at', 'desc');

        if ($this->tanggal) {
            $query->whereDate('created_at', $this->tanggal);
        }

        $dataRaw = $query->get();

        $grouped = $dataRaw->groupBy(function ($item) {
            return $item->user_id . '-' . Carbon::parse($item->created_at)->format('Y-m-d');
        });

        $finalData = $grouped->map(function ($group) {
            $sample = $group->first();

            $dataMasuk = $group->filter(fn($i) => str_contains($i->present_desc_system, 'Masuk'))->first();
            $dataPulang = $group->filter(fn($i) => str_contains($i->present_desc_system, 'Keluar') || str_contains($i->present_desc_system, 'Pulang'))->first();

            $jamMasuk = $dataMasuk ? Carbon::parse($dataMasuk->created_at)->format('H:i') : '-';
            $jamPulang = $dataPulang ? Carbon::parse($dataPulang->created_at)->format('H:i') : '-';

            return [
                'Nama Pegawai' => $sample->user->name ?? 'User Terhapus',
                'Shift' => $sample->shift->shift_name ?? '-',
                'Tanggal' => Carbon::parse($sample->created_at)->translatedFormat('d F Y'),
                'Jam Masuk' => $jamMasuk,
                'Jam Pulang' => $jamPulang,
                'Status' => $sample->status,
            ];
        });

        return $finalData;
    }

    public function headings(): array
    {
        return ['Nama Pegawai', 'Shift', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status Akhir'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
