<?php

namespace App\Exports;

use App\Models\OrangTua;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrangTuaExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
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
            'Nama Ibu',
            'No. Telepon',
            'Alamat',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
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
            $row->nama_ayah,
            $row->nama_ibu,
            $row->no_telp,
            $row->alamat,
            $row->created_at,
            $row->updated_at,
        ];
    }
    
    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     *
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ],
        ];
    }
}