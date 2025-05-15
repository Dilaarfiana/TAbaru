<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $siswas;
    
    public function __construct($siswas = null)
    {
        $this->siswas = $siswas ?? Siswa::all();
    }
    
    public function collection()
    {
        return $this->siswas;
    }
    
    public function headings(): array
    {
        return [
            'ID Siswa',
            'Nama Siswa',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Tanggal Masuk',
            'Status'
        ];
    }
    
    public function map($siswa): array
    {
        return [
            $siswa->id_siswa,
            $siswa->nama_siswa,
            $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $siswa->tempat_lahir ?? '-',
            $siswa->tanggal_lahir ? date('d-m-Y', strtotime($siswa->tanggal_lahir)) : '-',
            $siswa->tanggal_masuk ? date('d-m-Y', strtotime($siswa->tanggal_masuk)) : '-',
            $siswa->status_aktif == 1 ? 'Aktif' : 'Tidak Aktif'
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header (baris pertama)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']  // Teks putih
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4299E1'] // Warna biru
                ]
            ],
                        
            // Style untuk semua sel
            'A1:G100' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0']  // Warna abu-abu
                    ],
                ],
            ],
        ];
    }
}