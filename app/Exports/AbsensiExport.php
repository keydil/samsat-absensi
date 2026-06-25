<?php

namespace App\Exports;

use App\Models\Absen;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filterType;
    protected $tanggalFilter;
    protected $userIdFilter;

    public function __construct($filterType = 'daily', $tanggalFilter = null, $userIdFilter = null)
    {
        $this->filterType = $filterType;
        $this->tanggalFilter = $tanggalFilter;
        $this->userIdFilter = $userIdFilter;
    }

    public function collection()
    {
        $query = Absen::selectRaw('
                date,
                user_id,
                MAX(CASE WHEN present_desc_system LIKE "%Masuk%" THEN created_at END) as jam_masuk,
                MAX(CASE WHEN present_desc_system LIKE "%Keluar%" THEN created_at END) as jam_pulang,
                MAX(status) as status,
                MAX(approval_status) as approval_status,
                MAX(status_desc) as status_desc
            ')
            ->with('user')
            ->groupBy('date', 'user_id')
            ->orderBy('date', 'desc');

        if ($this->userIdFilter) {
            $query->where('user_id', $this->userIdFilter);
        }

        if ($this->tanggalFilter) {
            if ($this->filterType == 'daily') {
                $query->whereDate('date', $this->tanggalFilter);
            } elseif ($this->filterType == 'weekly') {
                $startOfWeek = \Carbon\Carbon::parse($this->tanggalFilter)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::parse($this->tanggalFilter)->endOfWeek();
                $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
            } elseif ($this->filterType == 'monthly') {
                $startOfMonth = \Carbon\Carbon::parse($this->tanggalFilter)->startOfMonth();
                $endOfMonth = \Carbon\Carbon::parse($this->tanggalFilter)->endOfMonth();
                $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Email',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Status Persetujuan',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        // Format waktu masuk
        $jamMasuk = $row->jam_masuk ? Carbon::parse($row->jam_masuk)->format('H:i') : '-';
        // Format waktu pulang
        $jamPulang = $row->jam_pulang ? Carbon::parse($row->jam_pulang)->format('H:i') : '-';
        
        // Status Persetujuan
        $approval = '-';
        if (in_array($row->status, ['Izin', 'Sakit'])) {
            if ($row->approval_status == 'pending') $approval = 'Menunggu';
            elseif ($row->approval_status == 'approved') $approval = 'Disetujui';
            elseif ($row->approval_status == 'rejected') $approval = 'Ditolak';
        }

        // Keterangan khusus Izin/Sakit
        $keterangan = '-';
        if (in_array($row->status, ['Izin', 'Sakit']) && $row->status_desc) {
            $keterangan = $row->status_desc;
        }

        return [
            $row->user->name ?? 'User Dihapus',
            $row->user->email ?? '-',
            Carbon::parse($row->date)->translatedFormat('d F Y'),
            $jamMasuk,
            $jamPulang,
            $row->status,
            $approval,
            $keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Baris pertama (Headings) di bold
            1    => ['font' => ['bold' => true]],
        ];
    }
}
