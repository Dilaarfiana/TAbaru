<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithChunkReading
{
    use Exportable;
    
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
        // Mengatur lebar kolom agar lebih baik
        $sheet->getColumnDimension('A')->setWidth(15); // ID Siswa
        $sheet->getColumnDimension('B')->setWidth(35); // Nama Siswa
        $sheet->getColumnDimension('C')->setWidth(15); // Jenis Kelamin
        $sheet->getColumnDimension('D')->setWidth(20); // Tempat Lahir
        $sheet->getColumnDimension('E')->setWidth(15); // Tanggal Lahir
        $sheet->getColumnDimension('F')->setWidth(15); // Tanggal Masuk
        $sheet->getColumnDimension('G')->setWidth(15); // Status
        
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
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]
            ],
            
            // Style untuk semua sel
            'A1:G' . ($this->siswas->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0']  // Warna abu-abu
                    ],
                ],
            ],
        ];
    }
    
    // Untuk mengurangi penggunaan memory, tambahkan proses chunking
    public function chunkSize(): int
    {
        return 1000; // Export per 1000 data
    }
}