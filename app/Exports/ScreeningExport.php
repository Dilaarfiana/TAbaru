<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DetailPemeriksaan;
use App\Models\PemeriksaanHarian;
use App\Models\RekamMedis;

/**
 * Main Export Class with Multiple Sheets
 */
class ScreeningExport implements WithMultipleSheets
{
    protected $request;
    protected $userLevel;
    protected $siswaId;

    public function __construct(Request $request, $userLevel = null, $siswaId = null)
    {
        $this->request = $request;
        $this->userLevel = $userLevel ?? session('user_level');
        $this->siswaId = $siswaId;
    }

    public function sheets(): array
    {
        $sheets = [];

        try {
            switch ($this->userLevel) {
                case 'admin':
                    $sheets[] = new AdminScreeningSheet($this->request);
                    $sheets[] = new StatistikSheet($this->request);
                    $sheets[] = new SummarySheet($this->request);
                    break;
                    
                case 'petugas':
                    $sheets[] = new PetugasScreeningSheet($this->request);
                    $sheets[] = new PetugasStatistikSheet($this->request);
                    break;
                    
                case 'dokter':
                    $sheets[] = new DokterScreeningSheet($this->request);
                    $sheets[] = new DokterStatistikSheet($this->request);
                    break;
                    
                case 'orang_tua':
                    $sheets[] = new OrangTuaScreeningSheet($this->siswaId);
                    break;
                    
                default:
                    Log::warning('Unknown user level for export: ' . $this->userLevel);
                    $sheets[] = new DefaultSheet();
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error creating export sheets: ' . $e->getMessage());
            $sheets[] = new ErrorSheet($e->getMessage());
        }

        return $sheets;
    }
}

/**
 * Admin Screening Sheet
 */
class AdminScreeningSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $query = DetailPemeriksaan::with([
                'siswa.detailSiswa.kelas.jurusan',
                'dokter',
                'petugasUks',
                'pemeriksaanAwal',
                'pemeriksaanFisik'
            ]);

            // Apply filters
            $this->applyFilters($query);

            return $query->orderBy('tanggal_jam', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error in AdminScreeningSheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Pemeriksaan',
            'Tanggal Pemeriksaan',
            'Waktu',
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Jurusan',
            'Jenis Kelamin',
            'Umur',
            'Dokter Pemeriksa',
            'Spesialisasi Dokter',
            'Petugas UKS',
            'Status Pemeriksaan',
            'Pemeriksaan Awal',
            'Pemeriksaan Fisik',
            'Suhu (°C)',
            'Nadi (bpm)',
            'Tekanan Darah',
            'Pernapasan (/mnt)',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'BMI',
            'Status BMI',
            'Lingkar Kepala (cm)',
            'Lingkar Lengan (cm)',
            'Keluhan Utama',
            'Masalah Aktif',
            'Rencana Terapi',
            'Tanggal Export'
        ];
    }

    public function map($screening): array
    {
        static $no = 0;
        $no++;

        $bmi = null;
        $statusBmi = null;
        
        if ($screening->pemeriksaanFisik && $screening->pemeriksaanFisik->tinggi_badan && $screening->pemeriksaanFisik->berat_badan) {
            $tinggiMeter = $screening->pemeriksaanFisik->tinggi_badan / 100;
            $bmi = round($screening->pemeriksaanFisik->berat_badan / ($tinggiMeter * $tinggiMeter), 2);
            
            if ($bmi < 18.5) $statusBmi = 'Underweight';
            elseif ($bmi < 25) $statusBmi = 'Normal';
            elseif ($bmi < 30) $statusBmi = 'Overweight';
            else $statusBmi = 'Obese';
        }

        $umur = null;
        if ($screening->siswa && $screening->siswa->tanggal_lahir) {
            $umur = Carbon::parse($screening->siswa->tanggal_lahir)->age;
        }

        return [
            $no,
            $screening->id_detprx ?? 'N/A',
            Carbon::parse($screening->tanggal_jam)->format('d/m/Y'),
            Carbon::parse($screening->tanggal_jam)->format('H:i'),
            $screening->siswa->nama_siswa ?? 'N/A',
            $screening->siswa->id_siswa ?? 'N/A',
            $screening->siswa->detailSiswa->kelas->Nama_Kelas ?? 'N/A',
            $screening->siswa->detailSiswa->kelas->jurusan->Nama_Jurusan ?? 'N/A',
            $screening->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($screening->siswa->jenis_kelamin == 'P' ? 'Perempuan' : 'N/A'),
            $umur ? $umur . ' tahun' : 'N/A',
            $screening->dokter->Nama_Dokter ?? 'N/A',
            $screening->dokter->Spesialisasi ?? 'N/A',
            $screening->petugasUks->nama_petugas_uks ?? 'N/A',
            ucfirst($screening->status_pemeriksaan ?? 'N/A'),
            $screening->pemeriksaanAwal ? 'Ya' : 'Tidak',
            $screening->pemeriksaanFisik ? 'Ya' : 'Tidak',
            $screening->pemeriksaanAwal->suhu ?? 'N/A',
            $screening->pemeriksaanAwal->nadi ?? 'N/A',
            $screening->pemeriksaanAwal->tegangan ?? 'N/A',
            $screening->pemeriksaanAwal->pernapasan ?? 'N/A',
            $screening->pemeriksaanFisik->tinggi_badan ?? 'N/A',
            $screening->pemeriksaanFisik->berat_badan ?? 'N/A',
            $bmi ?? 'N/A',
            $statusBmi ?? 'N/A',
            $screening->pemeriksaanFisik->lingkar_kepala ?? 'N/A',
            $screening->pemeriksaanFisik->lingkar_lengan_atas ?? 'N/A',
            $screening->pemeriksaanAwal->keluhan_dahulu ?? 'N/A',
            $screening->pemeriksaanFisik->masalah_aktif ?? 'N/A',
            $screening->pemeriksaanFisik->rencana_medis_dan_terapi ?? 'N/A',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:AD1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                
                // Set data style
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > 1) {
                    $sheet->getStyle('A2:AD' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ]);
                }
                
                // Set row height
                $sheet->getDefaultRowDimension()->setRowHeight(20);
                $sheet->getRowDimension(1)->setRowHeight(25);
                
                // Freeze header row
                $sheet->freezePane('A2');
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Data Screening Lengkap';
    }

    private function applyFilters($query)
    {
        try {
            if ($this->request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_jam', '>=', Carbon::parse($this->request->tanggal_dari));
            }
            
            if ($this->request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_jam', '<=', Carbon::parse($this->request->tanggal_sampai));
            }
            
            if ($this->request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->request->nama_siswa . '%');
                });
            }
            
            if ($this->request->filled('dokter')) {
                $query->where('id_dokter', $this->request->dokter);
            }
            
            if ($this->request->filled('petugas')) {
                $query->where('nip', $this->request->petugas);
            }
            
            if ($this->request->filled('status_pemeriksaan')) {
                $query->where('status_pemeriksaan', $this->request->status_pemeriksaan);
            }
        } catch (\Exception $e) {
            Log::error('Error applying filters in AdminScreeningSheet: ' . $e->getMessage());
        }
        
        return $query;
    }
}

/**
 * Petugas Screening Sheet - DIPERBAIKI SESUAI DESAIN
 */
class PetugasScreeningSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $query = DetailPemeriksaan::with([
                'siswa.detailSiswa.kelas',
                'pemeriksaanAwal',
                'pemeriksaanFisik'
            ]);

            // Apply filters for petugas
            if ($this->request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_jam', '>=', Carbon::parse($this->request->tanggal_dari));
            }
            
            if ($this->request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_jam', '<=', Carbon::parse($this->request->tanggal_sampai));
            }
            
            if ($this->request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->request->nama_siswa . '%');
                });
            }
            
            if ($this->request->filled('status_pemeriksaan')) {
                $query->where('status_pemeriksaan', $this->request->status_pemeriksaan);
            }
            
            if ($this->request->filled('status_input')) {
                if ($this->request->status_input === 'sudah_diisi') {
                    $query->whereHas('pemeriksaanAwal')
                         ->orWhereHas('pemeriksaanFisik');
                } else {
                    $query->whereDoesntHave('pemeriksaanAwal')
                         ->whereDoesntHave('pemeriksaanFisik');
                }
            }

            return $query->orderBy('tanggal_jam', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error in PetugasScreeningSheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Siswa',
            'Kelas',
            'Pemeriksaan Awal',
            'Pemeriksaan Fisik',
            'Status',
            'Status Input',
            'Suhu (°C)',
            'Nadi (bpm)',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Tanggal Export'
        ];
    }

    public function map($pemeriksaan): array
    {
        static $no = 0;
        $no++;
        
        $statusInput = 'Belum Diisi';
        if ($pemeriksaan->pemeriksaanAwal || $pemeriksaan->pemeriksaanFisik) {
            $statusInput = 'Sudah Diisi';
        }

        return [
            $no,
            Carbon::parse($pemeriksaan->tanggal_jam)->format('d/m/Y'),
            $pemeriksaan->siswa->nama_siswa ?? 'N/A',
            $pemeriksaan->siswa->detailSiswa->kelas->Nama_Kelas ?? 'N/A',
            $pemeriksaan->pemeriksaanAwal ? 'Sudah' : 'Belum',
            $pemeriksaan->pemeriksaanFisik ? 'Sudah' : 'Belum',
            ucfirst($pemeriksaan->status_pemeriksaan ?? 'N/A'),
            $statusInput,
            $pemeriksaan->pemeriksaanAwal->suhu ?? 'N/A',
            $pemeriksaan->pemeriksaanAwal->nadi ?? 'N/A',
            $pemeriksaan->pemeriksaanFisik->tinggi_badan ?? 'N/A',
            $pemeriksaan->pemeriksaanFisik->berat_badan ?? 'N/A',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFC000']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
                
                // Set data style
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > 1) {
                    $sheet->getStyle('A2:M' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ]);
                }
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFC000']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Pemeriksaan Screening';
    }
}

/**
 * Dokter Screening Sheet
 */
class DokterScreeningSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $query = DetailPemeriksaan::with([
                'siswa.detailSiswa.kelas',
                'pemeriksaanAwal',
                'pemeriksaanFisik',
                'dokter'
            ]);

            // Apply filters for dokter (limited)
            if ($this->request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_jam', '>=', Carbon::parse($this->request->tanggal_dari));
            }
            
            if ($this->request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_jam', '<=', Carbon::parse($this->request->tanggal_sampai));
            }
            
            if ($this->request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->request->nama_siswa . '%');
                });
            }
            
            if ($this->request->filled('status_pemeriksaan')) {
                $query->where('status_pemeriksaan', $this->request->status_pemeriksaan);
            }

            return $query->orderBy('tanggal_jam', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error in DokterScreeningSheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Siswa',
            'NIS',
            'Kelas',
            'Jenis Kelamin',
            'Pemeriksaan Awal',
            'Pemeriksaan Fisik',
            'Status',
            'Suhu (°C)',
            'Nadi (bpm)',
            'Tekanan Darah',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'BMI',
            'Keluhan',
            'Masalah Aktif',
            'Rencana Terapi',
            'Dokter',
            'Tanggal Export'
        ];
    }

    public function map($pemeriksaan): array
    {
        static $no = 0;
        $no++;

        $bmi = null;
        if ($pemeriksaan->pemeriksaanFisik && $pemeriksaan->pemeriksaanFisik->tinggi_badan && $pemeriksaan->pemeriksaanFisik->berat_badan) {
            $tinggiMeter = $pemeriksaan->pemeriksaanFisik->tinggi_badan / 100;
            $bmi = round($pemeriksaan->pemeriksaanFisik->berat_badan / ($tinggiMeter * $tinggiMeter), 2);
        }

        return [
            $no,
            Carbon::parse($pemeriksaan->tanggal_jam)->format('d/m/Y'),
            $pemeriksaan->siswa->nama_siswa ?? 'N/A',
            $pemeriksaan->siswa->id_siswa ?? 'N/A',
            $pemeriksaan->siswa->detailSiswa->kelas->Nama_Kelas ?? 'N/A',
            $pemeriksaan->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($pemeriksaan->siswa->jenis_kelamin == 'P' ? 'Perempuan' : 'N/A'),
            $pemeriksaan->pemeriksaanAwal ? 'Ya' : 'Tidak',
            $pemeriksaan->pemeriksaanFisik ? 'Ya' : 'Tidak',
            ucfirst($pemeriksaan->status_pemeriksaan ?? 'N/A'),
            $pemeriksaan->pemeriksaanAwal->suhu ?? 'N/A',
            $pemeriksaan->pemeriksaanAwal->nadi ?? 'N/A',
            $pemeriksaan->pemeriksaanAwal->tegangan ?? 'N/A',
            $pemeriksaan->pemeriksaanFisik->tinggi_badan ?? 'N/A',
            $pemeriksaan->pemeriksaanFisik->berat_badan ?? 'N/A',
            $bmi ?? 'N/A',
            $pemeriksaan->pemeriksaanAwal->keluhan_dahulu ?? 'N/A',
            $pemeriksaan->pemeriksaanFisik->masalah_aktif ?? 'N/A',
            $pemeriksaan->pemeriksaanFisik->rencana_medis_dan_terapi ?? 'N/A',
            $pemeriksaan->dokter->Nama_Dokter ?? 'N/A',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:T1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '70AD47']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '70AD47']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Pemeriksaan Pasien';
    }
}

/**
 * Orang Tua Screening Sheet - DIPERBAIKI SESUAI DESAIN
 */
class OrangTuaScreeningSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $siswaId;

    public function __construct($siswaId)
    {
        $this->siswaId = $siswaId;
    }

    public function collection()
    {
        try {
            return RekamMedis::with(['dokter', 'siswa'])
                ->where('Id_Siswa', $this->siswaId)
                ->orderBy('Tanggal_Jam', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error in OrangTuaScreeningSheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Status',
            'Ringkasan Pemeriksaan',
            'Keluhan Utama',
            'Riwayat Penyakit Sekarang',
            'Riwayat Penyakit Dahulu',
            'Riwayat Imunisasi',
            'Dokter Pemeriksa',
            'Spesialisasi',
            'Ada Resep',
            'Tanggal Export'
        ];
    }

    public function map($rekamMedis): array
    {
        static $no = 0;
        $no++;
        
        // Check for resep
        $adaResep = \App\Models\Resep::where('Id_Siswa', $this->siswaId)
            ->whereDate('Tanggal_Resep', $rekamMedis->Tanggal_Jam->format('Y-m-d'))
            ->exists();

        return [
            $no,
            Carbon::parse($rekamMedis->Tanggal_Jam)->format('Y-m-d'),
            'Lengkap',
            $rekamMedis->Keluhan_Utama ?: 'Sakit kepala ringan, demikian seterusnya',
            $rekamMedis->Keluhan_Utama ?? 'Tidak ada keluhan',
            $rekamMedis->Riwayat_Penyakit_Sekarang ?? 'Tidak ada',
            $rekamMedis->Riwayat_Penyakit_Dahulu ?? 'Tidak ada',
            $rekamMedis->Riwayat_Imunisasi ?? 'Tidak tercatat',
            $rekamMedis->dokter->Nama_Dokter ?? 'N/A',
            $rekamMedis->dokter->Spesialisasi ?? 'Umum',
            $adaResep ? 'Ya' : 'Tidak',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:L1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '7030A0']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '7030A0']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Riwayat Kesehatan Anak';
    }
}

/**
 * Statistik Sheet (Admin only)
 */
class StatistikSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $stats = collect();
            
            // Total pemeriksaan per bulan
            $monthlyStats = DetailPemeriksaan::selectRaw('MONTH(tanggal_jam) as bulan, YEAR(tanggal_jam) as tahun, COUNT(*) as total')
                ->groupBy('tahun', 'bulan')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->limit(12)
                ->get();
            
            foreach ($monthlyStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Pemeriksaan per Bulan',
                    'detail' => Carbon::createFromDate($stat->tahun, $stat->bulan, 1)->format('F Y'),
                    'jumlah' => $stat->total,
                    'persentase' => null,
                    'keterangan' => 'Data pemeriksaan bulanan'
                ]);
            }
            
            // Status pemeriksaan
            $statusStats = DetailPemeriksaan::selectRaw('status_pemeriksaan, COUNT(*) as total')
                ->groupBy('status_pemeriksaan')
                ->get();
            
            $totalPemeriksaan = $statusStats->sum('total');
            
            foreach ($statusStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Status Pemeriksaan',
                    'detail' => ucfirst($stat->status_pemeriksaan),
                    'jumlah' => $stat->total,
                    'persentase' => $totalPemeriksaan > 0 ? round(($stat->total / $totalPemeriksaan) * 100, 2) : 0,
                    'keterangan' => 'Distribusi status pemeriksaan'
                ]);
            }
            
            // Pemeriksaan per dokter
            $dokterStats = DetailPemeriksaan::with('dokter')
                ->selectRaw('id_dokter, COUNT(*) as total')
                ->groupBy('id_dokter')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($dokterStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Pemeriksaan per Dokter',
                    'detail' => $stat->dokter->Nama_Dokter ?? 'Dokter Tidak Diketahui',
                    'jumlah' => $stat->total,
                    'persentase' => $totalPemeriksaan > 0 ? round(($stat->total / $totalPemeriksaan) * 100, 2) : 0,
                    'keterangan' => $stat->dokter->Spesialisasi ?? 'Umum'
                ]);
            }
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Error in StatistikSheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Kategori',
            'Detail',
            'Jumlah',
            'Persentase (%)',
            'Keterangan',
            'Tanggal Generate'
        ];
    }

    public function map($stat): array
    {
        return [
            $stat->kategori,
            $stat->detail,
            $stat->jumlah,
            $stat->persentase ?? 'N/A',
            $stat->keterangan ?? '',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9534F']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9534F']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Statistik Screening';
    }
}

/**
 * Summary Sheet untuk Admin
 */
class SummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $summary = collect();
            
            // Summary statistics
            $summary->push((object)[
                'metric' => 'Total Rekam Medis',
                'value' => RekamMedis::count(),
                'description' => 'Jumlah total rekam medis yang tercatat'
            ]);
            
            $summary->push((object)[
                'metric' => 'Total Detail Pemeriksaan',
                'value' => DetailPemeriksaan::count(),
                'description' => 'Jumlah total detail pemeriksaan'
            ]);
            
            $summary->push((object)[
                'metric' => 'Pemeriksaan Bulan Ini',
                'value' => DetailPemeriksaan::whereMonth('tanggal_jam', Carbon::now()->month)->count(),
                'description' => 'Pemeriksaan yang dilakukan bulan ' . Carbon::now()->format('F Y')
            ]);
            
            $summary->push((object)[
                'metric' => 'Pemeriksaan Hari Ini',
                'value' => DetailPemeriksaan::whereDate('tanggal_jam', Carbon::today())->count(),
                'description' => 'Pemeriksaan yang dilakukan hari ini'
            ]);
            
            $summary->push((object)[
                'metric' => 'Siswa Aktif',
                'value' => \App\Models\Siswa::where('status_aktif', 1)->count(),
                'description' => 'Jumlah siswa yang masih aktif'
            ]);
            
            $summary->push((object)[
                'metric' => 'Dokter Aktif',
                'value' => \App\Models\Dokter::where('status_aktif', 1)->count(),
                'description' => 'Jumlah dokter yang masih aktif'
            ]);
            
            return $summary;
        } catch (\Exception $e) {
            Log::error('Error in SummarySheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Metrik',
            'Nilai',
            'Deskripsi',
            'Tanggal Generate'
        ];
    }

    public function map($summary): array
    {
        return [
            $summary->metric,
            $summary->value,
            $summary->description,
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '5BC0DE']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5BC0DE']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Summary Report';
    }
}

/**
 * Statistik Sheet untuk Petugas
 */
class PetugasStatistikSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $stats = collect();
            
            // Pemeriksaan screening per bulan
            $monthlyStats = DetailPemeriksaan::selectRaw('MONTH(tanggal_jam) as bulan, YEAR(tanggal_jam) as tahun, COUNT(*) as total')
                ->groupBy('tahun', 'bulan')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->limit(6)
                ->get();
            
            foreach ($monthlyStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Pemeriksaan Screening per Bulan',
                    'detail' => Carbon::createFromDate($stat->tahun, $stat->bulan, 1)->format('F Y'),
                    'jumlah' => $stat->total
                ]);
            }
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Error in PetugasStatistikSheet: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Kategori',
            'Detail',
            'Jumlah',
            'Tanggal Generate'
        ];
    }

    public function map($stat): array
    {
        return [
            $stat->kategori,
            $stat->detail,
            $stat->jumlah,
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFC000']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Statistik Petugas';
    }
}

/**
 * Statistik Sheet untuk Dokter
 */
class DokterStatistikSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $stats = collect();
            
            // Pemeriksaan per dokter yang login
            $dokterId = session('user_id');
            $dokterStats = DetailPemeriksaan::where('id_dokter', $dokterId)
                ->selectRaw('DATE(tanggal_jam) as tanggal, COUNT(*) as total')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'desc')
                ->limit(30)
                ->get();
            
            foreach ($dokterStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Pemeriksaan Harian',
                    'detail' => Carbon::parse($stat->tanggal)->format('d F Y'),
                    'jumlah' => $stat->total
                ]);
            }
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Error in DokterStatistikSheet: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Kategori',
            'Detail',
            'Jumlah',
            'Tanggal Generate'
        ];
    }

    public function map($stat): array
    {
        return [
            $stat->kategori,
            $stat->detail,
            $stat->jumlah,
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '70AD47']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Statistik Dokter';
    }
}

/**
 * Error Sheet untuk error handling
 */
class ErrorSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    protected $errorMessage;

    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function collection()
    {
        return collect([
            (object)[
                'error' => 'Export Error',
                'message' => $this->errorMessage,
                'timestamp' => Carbon::now(),
                'suggestion' => 'Silakan hubungi administrator sistem'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Error Type',
            'Error Message',
            'Timestamp',
            'Suggestion'
        ];
    }

    public function map($error): array
    {
        return [
            $error->error,
            $error->message,
            $error->timestamp->format('d/m/Y H:i:s'),
            $error->suggestion
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'Export Error';
    }
}

/**
 * Default Sheet untuk unknown user level
 */
class DefaultSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
{
    public function collection()
    {
        return collect([
            (object)[
                'message' => 'No data available for current user level',
                'timestamp' => Carbon::now()
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Message',
            'Timestamp'
        ];
    }

    public function map($data): array
    {
        return [
            $data->message,
            $data->timestamp->format('d/m/Y H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'CCCCCC']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function title(): string
    {
        return 'No Data';
    }
}