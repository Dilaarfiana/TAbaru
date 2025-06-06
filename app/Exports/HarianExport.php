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
use App\Models\PemeriksaanHarian;

/**
 * Main Export Class with Multiple Sheets for Pemeriksaan Harian
 */
class HarianExport implements WithMultipleSheets
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
                    $sheets[] = new AdminHarianSheet($this->request);
                    $sheets[] = new StatistikHarianSheet($this->request);
                    break;
                    
                case 'petugas':
                    $sheets[] = new PetugasHarianSheet($this->request);
                    break;
                    
                case 'dokter':
                    $sheets[] = new DokterHarianSheet($this->request);
                    break;
                    
                case 'orang_tua':
                    $sheets[] = new OrangTuaHarianSheet($this->siswaId);
                    break;
                    
                default:
                    Log::warning('Unknown user level for harian export: ' . $this->userLevel);
                    $sheets[] = new DefaultHarianSheet();
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error creating harian export sheets: ' . $e->getMessage());
            $sheets[] = new ErrorHarianSheet($e->getMessage());
        }

        return $sheets;
    }
}

/**
 * Admin Harian Sheet
 */
class AdminHarianSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $query = PemeriksaanHarian::with([
                'siswa.detailSiswa.kelas.jurusan',
                'petugasUks'
            ]);

            // Apply filters
            $this->applyHarianFilters($query);

            return $query->orderBy('Tanggal_Jam', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error in AdminHarianSheet collection: ' . $e->getMessage());
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
            'Petugas UKS',
            'NIP Petugas',
            'Hasil Pemeriksaan',
            'Status Pemeriksaan',
            'Panjang Hasil (Karakter)',
            'Kategori Pemeriksaan',
            'Tanggal Dibuat',
            'Tanggal Diperbarui',
            'Tanggal Export'
        ];
    }

    public function map($harian): array
    {
        static $no = 0;
        $no++;

        // Determine kategori pemeriksaan
        $kategori = 'Normal';
        if ($harian->Hasil_Pemeriksaan) {
            $resultLower = strtolower($harian->Hasil_Pemeriksaan);
            if (strpos($resultLower, 'sakit') !== false || strpos($resultLower, 'demam') !== false) {
                $kategori = 'Sakit';
            } elseif (strpos($resultLower, 'keluhan') !== false) {
                $kategori = 'Ada Keluhan';
            } elseif (strlen($harian->Hasil_Pemeriksaan) > 100) {
                $kategori = 'Perlu Perhatian';
            }
        }

        $umur = null;
        if ($harian->siswa && $harian->siswa->tanggal_lahir) {
            $umur = Carbon::parse($harian->siswa->tanggal_lahir)->age;
        }

        return [
            $no,
            $harian->Id_Harian ?? 'N/A',
            Carbon::parse($harian->Tanggal_Jam)->format('d/m/Y'),
            Carbon::parse($harian->Tanggal_Jam)->format('H:i'),
            $harian->siswa->nama_siswa ?? 'N/A',
            $harian->siswa->id_siswa ?? 'N/A',
            $harian->siswa->detailSiswa->kelas->Nama_Kelas ?? 'N/A',
            $harian->siswa->detailSiswa->kelas->jurusan->Nama_Jurusan ?? 'N/A',
            $harian->siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($harian->siswa->jenis_kelamin == 'P' ? 'Perempuan' : 'N/A'),
            $umur ? $umur . ' tahun' : 'N/A',
            $harian->petugasUks->nama_petugas_uks ?? 'N/A',
            $harian->NIP ?? 'N/A',
            $harian->Hasil_Pemeriksaan ?: 'Belum ada hasil',
            $harian->Hasil_Pemeriksaan ? 'Lengkap' : 'Belum Lengkap',
            $harian->Hasil_Pemeriksaan ? strlen($harian->Hasil_Pemeriksaan) : 0,
            $kategori,
            $harian->dibuat_pada ? Carbon::parse($harian->dibuat_pada)->format('d/m/Y H:i:s') : 'N/A',
            $harian->diperbarui_pada ? Carbon::parse($harian->diperbarui_pada)->format('d/m/Y H:i:s') : 'N/A',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:S1')->applyFromArray([
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
                    $sheet->getStyle('A2:S' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ]);
                }
                
                // Set column widths for better readability
                $sheet->getColumnDimension('M')->setWidth(50); // Hasil Pemeriksaan
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
        return 'Data Pemeriksaan Harian Lengkap';
    }

    private function applyHarianFilters($query)
    {
        try {
            if ($this->request->filled('tanggal_dari')) {
                $query->whereDate('Tanggal_Jam', '>=', Carbon::parse($this->request->tanggal_dari));
            }
            
            if ($this->request->filled('tanggal_sampai')) {
                $query->whereDate('Tanggal_Jam', '<=', Carbon::parse($this->request->tanggal_sampai));
            }
            
            if ($this->request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->request->nama_siswa . '%');
                });
            }
            
            if ($this->request->filled('kelas')) {
                $query->whereHas('siswa.detailSiswa', function ($q) {
                    $q->where('kode_kelas', $this->request->kelas);
                });
            }
            
            if ($this->request->filled('petugas')) {
                $query->where('NIP', $this->request->petugas);
            }
            
            if ($this->request->filled('hasil_pemeriksaan')) {
                if ($this->request->hasil_pemeriksaan === 'ada') {
                    $query->whereNotNull('Hasil_Pemeriksaan')
                         ->where('Hasil_Pemeriksaan', '!=', '');
                } else {
                    $query->where(function($q) {
                        $q->whereNull('Hasil_Pemeriksaan')
                          ->orWhere('Hasil_Pemeriksaan', '');
                    });
                }
            }
        } catch (\Exception $e) {
            Log::error('Error applying harian filters in AdminHarianSheet: ' . $e->getMessage());
        }
        
        return $query;
    }
}

/**
 * Petugas Harian Sheet
 */
class PetugasHarianSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $query = PemeriksaanHarian::with([
                'siswa:id_siswa,nama_siswa'
            ]);

            // Apply filters for petugas - only their own data
            $query->where('NIP', session('user_id'));

            if ($this->request->filled('tanggal_dari')) {
                $query->whereDate('Tanggal_Jam', '>=', Carbon::parse($this->request->tanggal_dari));
            }
            
            if ($this->request->filled('tanggal_sampai')) {
                $query->whereDate('Tanggal_Jam', '<=', Carbon::parse($this->request->tanggal_sampai));
            }
            
            if ($this->request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->request->nama_siswa . '%');
                });
            }

            return $query->orderBy('Tanggal_Jam', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error in PetugasHarianSheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu',
            'Nama Siswa',
            'NIS',
            'Hasil Pemeriksaan',
            'Panjang Hasil',
            'Status',
            'Kategori',
            'Tanggal Input',
            'Tanggal Export'
        ];
    }

    public function map($harian): array
    {
        static $no = 0;
        $no++;
        
        // Determine kategori
        $kategori = 'Normal';
        if ($harian->Hasil_Pemeriksaan) {
            $resultLower = strtolower($harian->Hasil_Pemeriksaan);
            if (strpos($resultLower, 'sakit') !== false || strpos($resultLower, 'demam') !== false) {
                $kategori = 'Sakit';
            } elseif (strpos($resultLower, 'keluhan') !== false) {
                $kategori = 'Ada Keluhan';
            } elseif (strlen($harian->Hasil_Pemeriksaan) > 100) {
                $kategori = 'Perlu Perhatian';
            }
        }

        return [
            $no,
            Carbon::parse($harian->Tanggal_Jam)->format('d/m/Y'),
            Carbon::parse($harian->Tanggal_Jam)->format('H:i'),
            $harian->siswa->nama_siswa ?? 'N/A',
            $harian->siswa->id_siswa ?? 'N/A',
            $harian->Hasil_Pemeriksaan ?: 'Belum diisi',
            $harian->Hasil_Pemeriksaan ? strlen($harian->Hasil_Pemeriksaan) : 0,
            $harian->Hasil_Pemeriksaan ? 'Sudah Diisi' : 'Belum Diisi',
            $kategori,
            $harian->dibuat_pada ? Carbon::parse($harian->dibuat_pada)->format('d/m/Y H:i:s') : 'N/A',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:K1')->applyFromArray([
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
                
                // Set column width for hasil pemeriksaan
                $sheet->getColumnDimension('F')->setWidth(50);
                
                // Set data style
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > 1) {
                    $sheet->getStyle('A2:K' . $lastRow)->applyFromArray([
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
        return 'Pemeriksaan Harian Petugas';
    }
}

/**
 * Dokter Harian Sheet
 */
class DokterHarianSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        try {
            $query = PemeriksaanHarian::with([
                'siswa:id_siswa,nama_siswa',
                'petugasUks:NIP,nama_petugas_uks'
            ]);

            // Apply filters for dokter
            if ($this->request->filled('tanggal_dari')) {
                $query->whereDate('Tanggal_Jam', '>=', Carbon::parse($this->request->tanggal_dari));
            }
            
            if ($this->request->filled('tanggal_sampai')) {
                $query->whereDate('Tanggal_Jam', '<=', Carbon::parse($this->request->tanggal_sampai));
            }
            
            if ($this->request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) {
                    $q->where('nama_siswa', 'like', '%' . $this->request->nama_siswa . '%');
                });
            }

            return $query->orderBy('Tanggal_Jam', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error in DokterHarianSheet collection: ' . $e->getMessage());
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
            'Petugas UKS',
            'Ringkasan Pemeriksaan',
            'Kategori Kondisi',
            'Rekomendasi Tindak Lanjut',
            'Status Pemeriksaan',
            'Tanggal Export'
        ];
    }

    public function map($harian): array
    {
        static $no = 0;
        $no++;
        
        // Determine kategori dan rekomendasi
        $kategori = 'Normal';
        $rekomendasi = 'Lanjutkan aktivitas normal';
        
        if ($harian->Hasil_Pemeriksaan) {
            $resultLower = strtolower($harian->Hasil_Pemeriksaan);
            if (strpos($resultLower, 'sakit') !== false || strpos($resultLower, 'demam') !== false) {
                $kategori = 'Sakit';
                $rekomendasi = 'Perlu pemantauan medis dan istirahat';
            } elseif (strpos($resultLower, 'keluhan') !== false) {
                $kategori = 'Ada Keluhan';
                $rekomendasi = 'Observasi lanjutan diperlukan';
            } elseif (strlen($harian->Hasil_Pemeriksaan) > 100) {
                $kategori = 'Perlu Perhatian';
                $rekomendasi = 'Evaluasi kondisi siswa lebih detail';
            }
        }

        return [
            $no,
            Carbon::parse($harian->Tanggal_Jam)->format('d/m/Y'),
            $harian->siswa->nama_siswa ?? 'N/A',
            $harian->siswa->id_siswa ?? 'N/A',
            $harian->petugasUks->nama_petugas_uks ?? 'N/A',
            $harian->Hasil_Pemeriksaan ?: 'Belum ada hasil pemeriksaan',
            $kategori,
            $rekomendasi,
            $harian->Hasil_Pemeriksaan ? 'Lengkap' : 'Belum Lengkap',
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:J1')->applyFromArray([
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
                
                // Set column widths
                $sheet->getColumnDimension('F')->setWidth(40); // Ringkasan
                $sheet->getColumnDimension('H')->setWidth(35); // Rekomendasi
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
        return 'Pemeriksaan Harian Siswa';
    }
}

/**
 * Orang Tua Harian Sheet
 */
class OrangTuaHarianSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
{
    protected $siswaId;

    public function __construct($siswaId)
    {
        $this->siswaId = $siswaId;
    }

    public function collection()
    {
        try {
            return PemeriksaanHarian::with(['petugasUks', 'siswa'])
                ->where('Id_Siswa', $this->siswaId)
                ->orderBy('Tanggal_Jam', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error in OrangTuaHarianSheet collection: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu',
            'Petugas UKS',
            'Ringkasan Pemeriksaan',
            'Kondisi Anak',
            'Rekomendasi untuk Orang Tua',
            'Status Tindak Lanjut',
            'Tanggal Export'
        ];
    }

    public function map($harian): array
    {
        static $no = 0;
        $no++;
        
        // Determine kondisi dan rekomendasi untuk orang tua
        $kondisi = 'Sehat';
        $rekomendasiOrtu = 'Lanjutkan pola hidup sehat di rumah';
        $statusTindakLanjut = 'Tidak diperlukan';
        
        if ($harian->Hasil_Pemeriksaan) {
            $resultLower = strtolower($harian->Hasil_Pemeriksaan);
            if (strpos($resultLower, 'sakit') !== false || strpos($resultLower, 'demam') !== false) {
                $kondisi = 'Kurang Sehat';
                $rekomendasiOrtu = 'Berikan istirahat cukup, perhatikan asupan nutrisi, dan pantau kondisi anak';
                $statusTindakLanjut = 'Perlu pemantauan';
            } elseif (strpos($resultLower, 'keluhan') !== false) {
                $kondisi = 'Ada Keluhan';
                $rekomendasiOrtu = 'Perhatikan keluhan anak dan konsultasi jika berlanjut';
                $statusTindakLanjut = 'Observasi di rumah';
            } elseif (strlen($harian->Hasil_Pemeriksaan) > 50) {
                $kondisi = 'Perlu Perhatian';
                $rekomendasiOrtu = 'Komunikasi dengan petugas UKS untuk info lebih lanjut';
                $statusTindakLanjut = 'Koordinasi dengan sekolah';
            }
        }

        return [
            $no,
            Carbon::parse($harian->Tanggal_Jam)->format('d/m/Y'),
            Carbon::parse($harian->Tanggal_Jam)->format('H:i'),
            $harian->petugasUks->nama_petugas_uks ?? 'N/A',
            $harian->Hasil_Pemeriksaan ?: 'Kondisi normal, tidak ada keluhan khusus',
            $kondisi,
            $rekomendasiOrtu,
            $statusTindakLanjut,
            Carbon::now()->format('d/m/Y H:i:s')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set header style
                $sheet->getStyle('A1:I1')->applyFromArray([
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
                
                // Set column widths
                $sheet->getColumnDimension('E')->setWidth(40); // Ringkasan
                $sheet->getColumnDimension('G')->setWidth(45); // Rekomendasi
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
        return 'Pemeriksaan Harian Anak';
    }
}

/**
 * Statistik Harian Sheet (Admin only)
 */
class StatistikHarianSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping, WithEvents
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
            
            // Total pemeriksaan harian per bulan
            $monthlyStats = PemeriksaanHarian::selectRaw('MONTH(Tanggal_Jam) as bulan, YEAR(Tanggal_Jam) as tahun, COUNT(*) as total')
                ->groupBy('tahun', 'bulan')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->limit(12)
                ->get();
            
            foreach ($monthlyStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Pemeriksaan Harian per Bulan',
                    'detail' => Carbon::createFromDate($stat->tahun, $stat->bulan, 1)->format('F Y'),
                    'jumlah' => $stat->total,
                    'persentase' => null,
                    'keterangan' => 'Data pemeriksaan harian bulanan'
                ]);
            }
            
            // Status pemeriksaan harian
            $statusStats = collect([
                (object)['status' => 'Lengkap', 'total' => PemeriksaanHarian::whereNotNull('Hasil_Pemeriksaan')->where('Hasil_Pemeriksaan', '!=', '')->count()],
                (object)['status' => 'Belum Lengkap', 'total' => PemeriksaanHarian::where(function($q) { $q->whereNull('Hasil_Pemeriksaan')->orWhere('Hasil_Pemeriksaan', ''); })->count()]
            ]);
            
            $totalPemeriksaan = $statusStats->sum('total');
            
            foreach ($statusStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Status Pemeriksaan Harian',
                    'detail' => $stat->status,
                    'jumlah' => $stat->total,
                    'persentase' => $totalPemeriksaan > 0 ? round(($stat->total / $totalPemeriksaan) * 100, 2) : 0,
                    'keterangan' => 'Distribusi status pemeriksaan harian'
                ]);
            }
            
            // Pemeriksaan per petugas
            $petugasStats = PemeriksaanHarian::with('petugasUks')
                ->selectRaw('NIP, COUNT(*) as total')
                ->groupBy('NIP')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($petugasStats as $stat) {
                $stats->push((object)[
                    'kategori' => 'Pemeriksaan per Petugas',
                    'detail' => $stat->petugasUks->nama_petugas_uks ?? 'Petugas Tidak Diketahui',
                    'jumlah' => $stat->total,
                    'persentase' => $totalPemeriksaan > 0 ? round(($stat->total / $totalPemeriksaan) * 100, 2) : 0,
                    'keterangan' => 'NIP: ' . ($stat->NIP ?? 'N/A')
                ]);
            }
            
            return $stats;
        } catch (\Exception $e) {
            Log::error('Error in StatistikHarianSheet collection: ' . $e->getMessage());
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
        return 'Statistik Pemeriksaan Harian';
    }
}

/**
 * Error Sheet untuk error handling
 */
class ErrorHarianSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
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
class DefaultHarianSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithMapping
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