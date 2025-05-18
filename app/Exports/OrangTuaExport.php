<?php

namespace App\Exports;

use App\Models\OrangTua;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrangTuaExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithChunkReading
{
    use Exportable;
    
    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return OrangTua::query()->with('siswa');
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Orang Tua',
            'ID Siswa',
            'Nama Siswa',
            'Nama Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'Pekerjaan Ibu',
            'No. Telepon',
            'Alamat',
            'Tanggal Dibuat',
            'Terakhir Diupdate'
        ];
    }
    
    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id_orang_tua,
            $row->id_siswa,
            $row->siswa->nama_siswa ?? 'Data Siswa Tidak Ditemukan',
            $row->nama_ayah ?? '-',
            $row->pekerjaan_ayah ?? '-',
            $row->nama_ibu ?? '-',
            $row->pekerjaan_ibu ?? '-',
            $row->no_telp ?? '-',
            $row->alamat ?? '-',
            $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '-',
            $row->updated_at ? $row->updated_at->format('d-m-Y H:i:s') : '-',
        ];
    }
    
    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     *
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Mengatur lebar kolom agar lebih baik
        $sheet->getColumnDimension('A')->setWidth(10); // ID Orang Tua
        $sheet->getColumnDimension('B')->setWidth(15); // ID Siswa
        $sheet->getColumnDimension('C')->setWidth(25); // Nama Siswa
        $sheet->getColumnDimension('D')->setWidth(20); // Nama Ayah
        $sheet->getColumnDimension('E')->setWidth(20); // Pekerjaan Ayah
        $sheet->getColumnDimension('F')->setWidth(20); // Nama Ibu
        $sheet->getColumnDimension('G')->setWidth(20); // Pekerjaan Ibu
        $sheet->getColumnDimension('H')->setWidth(15); // No. Telepon
        $sheet->getColumnDimension('I')->setWidth(30); // Alamat
        $sheet->getColumnDimension('J')->setWidth(20); // Created At
        $sheet->getColumnDimension('K')->setWidth(20); // Updated At
        
        return [
            // Style header (baris pertama)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            // Style untuk semua sel
            'A:K' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0']
                    ]
                ]
            ],
        ];
    }
    
    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 50; // Process 1000 records at a time
    }
}