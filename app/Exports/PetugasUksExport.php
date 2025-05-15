<?php

namespace App\Exports;

use App\Models\PetugasUKS;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PetugasUksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = PetugasUKS::query();

        // Filter berdasarkan status jika ada
        if ($this->request && isset($this->request['status']) && $this->request['status'] !== '') {
            $query->where('status_aktif', $this->request['status']);
        }

        // Filter berdasarkan keyword jika ada
        if ($this->request && isset($this->request['keyword']) && $this->request['keyword'] !== '') {
            $keyword = $this->request['keyword'];
            $query->where(function($q) use ($keyword) {
                $q->where('NIP', 'like', "%{$keyword}%")
                  ->orWhere('nama_petugas_uks', 'like', "%{$keyword}%");
            });
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIP',
            'Nama Petugas',
            'Alamat',
            'No. Telepon',
            'Status',
            'Tanggal Dibuat',
            'Terakhir Diperbarui'
        ];
    }

    /**
     * @param PetugasUKS $petugas
     * @return array
     */
    public function map($petugas): array
    {
        return [
            $petugas->NIP,
            $petugas->nama_petugas_uks,
            $petugas->alamat ?? '-',
            $petugas->no_telp ?? '-',
            $petugas->status_aktif ? 'Aktif' : 'Tidak Aktif',
            $petugas->created_at->format('d-m-Y H:i:s'),
            $petugas->updated_at->format('d-m-Y H:i:s')
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}