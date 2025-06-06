<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Siswa;
use App\Models\PemeriksaanHarian;
use App\Models\PetugasUKS;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Exports\HarianExport;

class LaporanHarianController extends Controller
{
    /**
     * Helper function untuk format tanggal dengan aman
     */
    private function formatTanggal($tanggal, $format = 'Y-m-d')
    {
        try {
            if (empty($tanggal)) {
                return 'N/A';
            }
            
            if ($tanggal instanceof Carbon) {
                return $tanggal->format($format);
            }
            
            return Carbon::parse($tanggal)->format($format);
        } catch (\Exception $e) {
            Log::warning('Error formatting date: ' . $e->getMessage(), ['tanggal' => $tanggal]);
            return 'N/A';
        }
    }

    /**
     * Halaman utama laporan pemeriksaan harian dengan role-based access
     */
    public function harian(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'tanggal_dari' => 'nullable|date|before_or_equal:tanggal_sampai',
                'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
                'nama_siswa' => 'nullable|string|max:50',
                'kelas' => 'nullable|exists:kelas,Kode_Kelas',
                'petugas' => 'nullable|exists:petugas_uks,NIP',
                'hasil_pemeriksaan' => 'nullable|in:ada,tidak_ada'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $userLevel = session('user_level');
            
            // Handle orang tua - hanya bisa lihat data anak sendiri
            if ($userLevel === 'orang_tua') {
                return $this->harianOrangTua($request);
            }
            
            // Prepare data berdasarkan role
            $data = $this->prepareHarianData($request, $userLevel);
            
            return view('laporan.harian', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in harian method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data pemeriksaan harian: ' . $e->getMessage());
        }
    }
    
    /**
     * Laporan harian khusus untuk orang tua
     */
    private function harianOrangTua(Request $request)
    {
        $siswaId = session('siswa_id');
        
        if (!$siswaId) {
            return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
        }
        
        try {
            // Ambil data siswa lengkap
            $siswaInfo = Siswa::with(['detailSiswa.kelas.jurusan'])
                ->findOrFail($siswaId);
            
            // Ambil pemeriksaan harian anak
            $pemeriksaanHarian = PemeriksaanHarian::with(['petugasUks'])
                ->where('Id_Siswa', $siswaId)
                ->orderBy('Tanggal_Jam', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($harian) {
                    return (object) [
                        'id' => $harian->Id_Harian,
                        'tanggal' => $this->formatTanggal($harian->Tanggal_Jam, 'Y-m-d'),
                        'tanggal_jam' => $harian->Tanggal_Jam, // Keep original for other uses
                        'petugas_uks' => $harian->petugasUks->nama_petugas_uks ?? 'Tidak ada petugas',
                        'ringkasan_pemeriksaan' => $harian->Hasil_Pemeriksaan ?: 'Belum ada hasil pemeriksaan',
                    ];
                });
            
            // Statistik untuk dashboard
            $totalPemeriksaan = PemeriksaanHarian::where('Id_Siswa', $siswaId)->count();
            $pemeriksaanBulanIni = PemeriksaanHarian::where('Id_Siswa', $siswaId)
                ->whereMonth('Tanggal_Jam', Carbon::now()->month)
                ->whereYear('Tanggal_Jam', Carbon::now()->year)
                ->count();
            
            return view('laporan.harian', [
                'siswaInfo' => $siswaInfo,
                'pemeriksaanHarian' => $pemeriksaanHarian,
                'totalPemeriksaan' => $totalPemeriksaan,
                'pemeriksaanBulanIni' => $pemeriksaanBulanIni,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in harianOrangTua: ' . $e->getMessage());
            return redirect()->route('dashboard.orangtua')->with('error', 'Gagal memuat data pemeriksaan harian anak');
        }
    }
    
    /**
     * Prepare data pemeriksaan harian berdasarkan role
     */
    private function prepareHarianData(Request $request, $userLevel)
    {
        $data = [];
        
        // Data filter options dengan caching
        $data['siswas'] = Cache::remember('filter_siswas_harian', 3600, function() {
            return Siswa::select('id_siswa', 'nama_siswa')->where('status_aktif', 1)->get();
        });
        
        $data['petugasUKS'] = Cache::remember('filter_petugas_harian', 3600, function() {
            return PetugasUKS::where('status_aktif', 1)->get();
        });
        
        $data['kelasList'] = Cache::remember('filter_kelas_harian', 3600, function() {
            return Kelas::with('jurusan')->get();
        });
        
        // Prepare query berdasarkan role
        switch ($userLevel) {
            case 'admin':
                $data = array_merge($data, $this->getAdminHarianData($request));
                break;
                
            case 'petugas':
                $data = array_merge($data, $this->getPetugasHarianData($request));
                break;
                
            case 'dokter':
                $data = array_merge($data, $this->getDokterHarianData($request));
                break;
        }
        
        return $data;
    }
    
    /**
     * Data pemeriksaan harian untuk Admin
     */
    private function getAdminHarianData(Request $request)
    {
        $query = PemeriksaanHarian::with([
            'siswa:id_siswa,nama_siswa',
            'siswa.detailSiswa.kelas:Kode_Kelas,Nama_Kelas',
            'siswa.detailSiswa.kelas.jurusan:Kode_Jurusan,Nama_Jurusan',
            'petugasUks:NIP,nama_petugas_uks'
        ]);
        
        // Apply filters
        $this->applyHarianFilters($query, $request);
        
        // Get paginated data
        $harianData = $query->orderBy('Tanggal_Jam', 'desc')->paginate(15);
        
        // Transform data untuk admin view
        $harianData->getCollection()->transform(function ($item) {
            return (object) [
                'id' => $item->Id_Harian,
                'tanggal' => $this->formatTanggal($item->Tanggal_Jam, 'Y-m-d H:i'),
                'tanggal_jam' => $item->Tanggal_Jam, // Keep original
                'nama_siswa' => $item->siswa->nama_siswa ?? 'N/A',
                'kelas' => $item->siswa->detailSiswa->kelas->Nama_Kelas ?? 'N/A',
                'jurusan' => $item->siswa->detailSiswa->kelas->jurusan->Nama_Jurusan ?? 'N/A',
                'petugas' => $item->petugasUks->nama_petugas_uks ?? 'N/A',
                'hasil_pemeriksaan' => $item->Hasil_Pemeriksaan ?: 'Belum ada hasil',
                'siswa_id' => $item->Id_Siswa,
            ];
        });
        
        return [
            'harianData' => $harianData,
            'totalPemeriksaan' => PemeriksaanHarian::count(),
            'pemeriksaanBulanIni' => PemeriksaanHarian::whereMonth('Tanggal_Jam', Carbon::now()->month)->count(),
        ];
    }
    
    /**
     * Data pemeriksaan harian untuk Petugas UKS
     */
    private function getPetugasHarianData(Request $request)
    {
        $query = PemeriksaanHarian::with([
            'siswa:id_siswa,nama_siswa',
            'siswa.detailSiswa.kelas:Kode_Kelas,Nama_Kelas'
        ]);
        
        // Apply filters for petugas
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('Tanggal_Jam', '>=', $request->tanggal_dari);
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('Tanggal_Jam', '<=', $request->tanggal_sampai);
        }
        
        if ($request->filled('nama_siswa')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->nama_siswa . '%');
            });
        }
        
        if ($request->filled('hasil_pemeriksaan')) {
            if ($request->hasil_pemeriksaan === 'ada') {
                $query->whereNotNull('Hasil_Pemeriksaan')
                     ->where('Hasil_Pemeriksaan', '!=', '');
            } else {
                $query->where(function($q) {
                    $q->whereNull('Hasil_Pemeriksaan')
                      ->orWhere('Hasil_Pemeriksaan', '');
                });
            }
        }
        
        $harianData = $query->orderBy('Tanggal_Jam', 'desc')->paginate(15);
        
        // Transform data untuk petugas view
        $harianData->getCollection()->transform(function ($item) {
            return (object) [
                'id' => $item->Id_Harian,
                'tanggal' => $this->formatTanggal($item->Tanggal_Jam, 'Y-m-d H:i'),
                'tanggal_jam' => $item->Tanggal_Jam, // Keep original
                'nama_siswa' => $item->siswa->nama_siswa ?? 'N/A',
                'hasil_pemeriksaan' => $item->Hasil_Pemeriksaan ?: 'Belum diisi',
                'bisa_edit' => true, // Petugas bisa edit semua data
                'siswa_id' => $item->Id_Siswa,
            ];
        });
        
        return [
            'harianData' => $harianData,
            'totalPemeriksaan' => PemeriksaanHarian::count(),
            'pemeriksaanBulanIni' => PemeriksaanHarian::whereMonth('Tanggal_Jam', Carbon::now()->month)->count(),
        ];
    }
    
    /**
     * Data pemeriksaan harian untuk Dokter
     */
    private function getDokterHarianData(Request $request)
    {
        $query = PemeriksaanHarian::with([
            'siswa:id_siswa,nama_siswa',
            'siswa.detailSiswa.kelas:Kode_Kelas,Nama_Kelas',
            'petugasUks:NIP,nama_petugas_uks'
        ]);
        
        // Apply filters for dokter
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('Tanggal_Jam', '>=', $request->tanggal_dari);
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('Tanggal_Jam', '<=', $request->tanggal_sampai);
        }
        
        if ($request->filled('nama_siswa')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->nama_siswa . '%');
            });
        }
        
        $harianData = $query->orderBy('Tanggal_Jam', 'desc')->paginate(15);
        
        // Transform data untuk dokter view
        $harianData->getCollection()->transform(function ($item) {
            return (object) [
                'id' => $item->Id_Harian,
                'tanggal' => $this->formatTanggal($item->Tanggal_Jam, 'Y-m-d H:i'),
                'tanggal_jam' => $item->Tanggal_Jam, // Keep original
                'nama_siswa' => $item->siswa->nama_siswa ?? 'N/A',
                'petugas' => $item->petugasUks->nama_petugas_uks ?? 'N/A',
                'ringkasan' => $item->Hasil_Pemeriksaan ?: 'Belum ada hasil pemeriksaan',
                'siswa_id' => $item->Id_Siswa,
            ];
        });
        
        return [
            'harianData' => $harianData,
            'totalPemeriksaan' => PemeriksaanHarian::count(),
            'pemeriksaanBulanIni' => PemeriksaanHarian::whereMonth('Tanggal_Jam', Carbon::now()->month)->count(),
        ];
    }
    
    /**
     * Apply filters to query
     */
    private function applyHarianFilters($query, Request $request)
    {
        try {
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('Tanggal_Jam', '>=', Carbon::parse($request->tanggal_dari));
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('Tanggal_Jam', '<=', Carbon::parse($request->tanggal_sampai));
            }
            
            if ($request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) use ($request) {
                    $q->where('nama_siswa', 'like', '%' . $request->nama_siswa . '%');
                });
            }
            
            if ($request->filled('kelas')) {
                $query->whereHas('siswa.detailSiswa', function ($q) use ($request) {
                    $q->where('kode_kelas', $request->kelas);
                });
            }
            
            if ($request->filled('petugas')) {
                $query->where('NIP', $request->petugas);
            }
            
            if ($request->filled('hasil_pemeriksaan')) {
                if ($request->hasil_pemeriksaan === 'ada') {
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
            Log::error('Error applying harian filters: ' . $e->getMessage());
        }
        
        return $query;
    }

    /**
     * BARU: Menampilkan halaman detail pemeriksaan harian
     */
    public function showHarianDetail(Request $request, $siswaId, $harianId)
    {
        try {
            $userLevel = session('user_level');
            
            // Check access permission untuk orang tua
            if ($userLevel === 'orang_tua' && $siswaId != session('siswa_id')) {
                return redirect()->route('dashboard.orangtua')->with('error', 'Akses ditolak. Anda hanya dapat melihat data anak sendiri.');
            }
            
            // Ambil data siswa dengan relasi lengkap
            $siswa = Siswa::with([
                'detailSiswa.kelas.jurusan',
                'orangTua'
            ])->find($siswaId);
            
            if (!$siswa) {
                return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
            }
            
            // Ambil data pemeriksaan harian spesifik
            $pemeriksaanHarian = PemeriksaanHarian::with(['petugasUks'])
                ->where('Id_Harian', $harianId)
                ->where('Id_Siswa', $siswaId)
                ->first();
            
            if (!$pemeriksaanHarian) {
                return redirect()->back()->with('error', 'Data pemeriksaan harian tidak ditemukan');
            }
            
            // Ambil riwayat pemeriksaan harian lainnya (untuk konteks)
            $riwayatHarian = PemeriksaanHarian::with(['petugasUks'])
                ->where('Id_Siswa', $siswaId)
                ->where('Id_Harian', '!=', $harianId)
                ->orderBy('Tanggal_Jam', 'desc')
                ->limit(5)
                ->get();
            
            // Data untuk view
            $data = [
                'siswa' => $siswa,
                'pemeriksaanHarian' => $pemeriksaanHarian,
                'riwayatHarian' => $riwayatHarian,
                'userLevel' => $userLevel,
                'isAdmin' => $userLevel === 'admin',
                'isPetugas' => $userLevel === 'petugas',
                'isDokter' => $userLevel === 'dokter',
                'isOrangTua' => $userLevel === 'orang_tua',
            ];
            
            // Define routes based on user role
            if ($userLevel === 'admin') {
                $data['backRoute'] = 'laporan.harian';
                $data['pdfRoute'] = 'laporan.harian.pdf';
            } elseif ($userLevel === 'petugas') {
                $data['backRoute'] = 'petugas.laporan.harian';
                $data['pdfRoute'] = 'petugas.laporan.harian.pdf';
            } elseif ($userLevel === 'dokter') {
                $data['backRoute'] = 'dokter.laporan.harian';
                $data['pdfRoute'] = 'dokter.laporan.harian.pdf';
            } else {
                $data['backRoute'] = 'orangtua.laporan.harian';
                $data['pdfRoute'] = 'orangtua.laporan.harian.pdf';
            }
            
            return view('laporan.harian_detail', $data);
            
        } catch (\Exception $e) {
            Log::error('Error showing harian detail: ' . $e->getMessage(), [
                'siswa_id' => $siswaId,
                'harian_id' => $harianId,
                'user_level' => session('user_level')
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail pemeriksaan harian: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate PDF laporan pemeriksaan harian
     */
    public function generateHarianPDF(Request $request, $siswaId = null)
    {
        try {
            // Validasi input
            if (!$siswaId && $request->filled('siswa_id')) {
                $siswaId = $request->siswa_id;
            }
            
            // Handle orang tua
            if (session('user_level') === 'orang_tua') {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
                }
            }
            
            if (!$siswaId) {
                return redirect()->back()->with('error', 'ID Siswa harus disediakan');
            }
            
            // Ambil data siswa dengan relasi
            $siswa = Siswa::with([
                'detailSiswa.kelas.jurusan',
                'orangTua'
            ])->find($siswaId);
            
            if (!$siswa) {
                return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
            }
            
            // Ambil pemeriksaan harian
            $pemeriksaanHarianId = $request->get('pemeriksaan_harian_id');
            
            if ($pemeriksaanHarianId) {
                $pemeriksaanHarian = PemeriksaanHarian::with(['petugasUks'])
                    ->where('Id_Harian', $pemeriksaanHarianId)
                    ->where('Id_Siswa', $siswaId)
                    ->first();
            } else {
                $pemeriksaanHarian = PemeriksaanHarian::with(['petugasUks'])
                    ->where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Jam', 'desc')
                    ->first();
            }
            
            if (!$pemeriksaanHarian) {
                return redirect()->back()->with('error', 'Data pemeriksaan harian tidak ditemukan untuk siswa ini');
            }
            
            // Data untuk view
            $data = [
                'siswa' => $siswa,
                'pemeriksaanHarian' => $pemeriksaanHarian,
                'tanggalCetak' => Carbon::now(),
                'tanggalPemeriksaan' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'd F Y'),
                'waktuPemeriksaan' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'H:i'),
            ];
            
            // Generate PDF dengan error handling
            try {
                $pdf = PDF::loadView('laporan.harian_pdf', $data);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'defaultFont' => 'DejaVu Sans',
                    'dpi' => 96,
                    'defaultPaperSize' => 'A4',
                    'margin-top' => 15,
                    'margin-right' => 15,
                    'margin-bottom' => 20,
                    'margin-left' => 15,
                ]);
            } catch (\Exception $e) {
                Log::error('PDF generation error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal membuat PDF. Silakan coba lagi.');
            }
            
            // Generate filename yang aman
            $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $siswa->nama_siswa);
            $filename = 'Pemeriksaan_Harian_' . $safeName . '_' . 
                       $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'Y-m-d') . '.pdf';
            
            // Log activity
            Log::info('Harian PDF generated successfully', [
                'user_level' => session('user_level'),
                'user_id' => session('user_id'),
                'siswa_id' => $siswaId,
                'filename' => $filename
            ]);
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error('Error generating harian PDF: ' . $e->getMessage(), [
                'siswa_id' => $siswaId,
                'user_level' => session('user_level')
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghasilkan laporan PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Export pemeriksaan harian data to Excel
     */
    public function exportHarian(Request $request, $siswaId = null)
    {
        try {
            $userLevel = session('user_level');
            
            // Handle orang tua
            if ($userLevel === 'orang_tua') {
                $siswaId = session('siswa_id');
                return $this->exportHarianOrangTua($siswaId, $request);
            }
            
            // Export berdasarkan role
            switch ($userLevel) {
                case 'admin':
                    return $this->exportHarianAdmin($request);
                case 'petugas':
                    return $this->exportHarianPetugas($request);
                case 'dokter':
                    return $this->exportHarianDokter($request);
                default:
                    return redirect()->back()->with('error', 'Role tidak dikenali');
            }
            
        } catch (\Exception $e) {
            Log::error('Error exporting harian: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Export untuk Admin
     */
    private function exportHarianAdmin(Request $request)
    {
        try {
            $filename = 'Laporan_Pemeriksaan_Harian_Admin_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new HarianExport($request, 'admin'), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting admin harian: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Export untuk Petugas
     */
    private function exportHarianPetugas(Request $request)
    {
        try {
            $filename = 'Laporan_Pemeriksaan_Harian_Petugas_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new HarianExport($request, 'petugas'), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting petugas harian: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Export untuk Dokter
     */
    private function exportHarianDokter(Request $request)
    {
        try {
            $filename = 'Laporan_Pemeriksaan_Harian_Dokter_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new HarianExport($request, 'dokter'), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting dokter harian: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Export untuk Orang Tua
     */
    private function exportHarianOrangTua($siswaId, Request $request)
    {
        try {
            if (!$siswaId) {
                throw new \Exception('ID Siswa tidak ditemukan');
            }
            
            // Ambil nama siswa untuk filename
            $siswa = Siswa::find($siswaId);
            $namaSiswa = $siswa ? preg_replace('/[^A-Za-z0-9_.-]/', '_', $siswa->nama_siswa) : 'Unknown';
            
            $filename = 'Pemeriksaan_Harian_' . $namaSiswa . '_' . Carbon::now()->format('Y-m-d') . '.xlsx';
            
            return Excel::download(new HarianExport($request, 'orang_tua', $siswaId), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting orang tua harian: ' . $e->getMessage(), [
                'siswa_id' => $siswaId
            ]);
            throw $e;
        }
    }
    
    /**
     * Get detail pemeriksaan harian for modal/API
     */
    public function getDetailHarian($siswaId, $harianId)
    {
        try {
            $userLevel = session('user_level');
            
            // Check access permission
            if ($userLevel === 'orang_tua' && $siswaId != session('siswa_id')) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $pemeriksaanHarian = PemeriksaanHarian::with(['petugasUks', 'siswa'])
                ->where('Id_Harian', $harianId)
                ->where('Id_Siswa', $siswaId)
                ->first();
            
            if (!$pemeriksaanHarian) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_harian' => $pemeriksaanHarian->Id_Harian,
                    'tanggal' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'd F Y'),
                    'waktu' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'H:i'),
                    'tanggal_lengkap' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'l, d F Y'),
                    'waktu_lengkap' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'H:i') . ' WIB',
                    'hasil_pemeriksaan' => $pemeriksaanHarian->Hasil_Pemeriksaan ?: 'Belum ada hasil pemeriksaan',
                    'hasil_pemeriksaan_html' => nl2br(e($pemeriksaanHarian->Hasil_Pemeriksaan ?: 'Belum ada hasil pemeriksaan')),
                    'petugas_uks' => $pemeriksaanHarian->petugasUks->nama_petugas_uks ?? 'Tidak ada petugas',
                    'nip_petugas' => $pemeriksaanHarian->NIP ?? 'N/A',
                    'nama_siswa' => $pemeriksaanHarian->siswa->nama_siswa ?? 'N/A',
                    'id_siswa' => $pemeriksaanHarian->Id_Siswa,
                    'dibuat_pada' => $this->formatTanggal($pemeriksaanHarian->dibuat_pada ?? $pemeriksaanHarian->created_at, 'd F Y H:i'),
                    'diperbarui_pada' => $this->formatTanggal($pemeriksaanHarian->diperbarui_pada ?? $pemeriksaanHarian->updated_at, 'd F Y H:i'),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching detail harian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching detail harian: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Add new pemeriksaan harian (Petugas only)
     */
    public function addHarian(Request $request)
    {
        try {
            if (session('user_level') !== 'petugas') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'siswa_id' => 'required|exists:siswas,id_siswa',
                'tanggal_jam' => 'required|date',
                'hasil_pemeriksaan' => 'required|string|max:1000'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Data tidak valid', 'errors' => $validator->errors()], 422);
            }
            
            // Generate ID
            $lastHarian = PemeriksaanHarian::orderBy('Id_Harian', 'desc')->first();
            $newId = $lastHarian ? 'H' . str_pad((intval(substr($lastHarian->Id_Harian, 1)) + 1), 4, '0', STR_PAD_LEFT) : 'H0001';
            
            // Parse tanggal untuk memastikan format yang benar
            $tanggalJam = Carbon::parse($request->tanggal_jam);
            
            $pemeriksaanHarian = PemeriksaanHarian::create([
                'Id_Harian' => $newId,
                'Tanggal_Jam' => $tanggalJam,
                'Hasil_Pemeriksaan' => $request->hasil_pemeriksaan,
                'Id_Siswa' => $request->siswa_id,
                'NIP' => session('user_id'), // NIP petugas yang login
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pemeriksaan harian berhasil ditambahkan',
                'data' => [
                    'id' => $pemeriksaanHarian->Id_Harian,
                    'tanggal_jam' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'Y-m-d H:i'),
                    'hasil_pemeriksaan' => $pemeriksaanHarian->Hasil_Pemeriksaan,
                    'siswa_id' => $pemeriksaanHarian->Id_Siswa,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error adding harian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambah data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing pemeriksaan harian (Petugas only)
     */
    public function updateHarian(Request $request, $harianId)
    {
        try {
            if (session('user_level') !== 'petugas') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $validator = Validator::make($request->all(), [
                'tanggal_jam' => 'sometimes|required|date',
                'hasil_pemeriksaan' => 'sometimes|required|string|max:1000'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Data tidak valid', 'errors' => $validator->errors()], 422);
            }
            
            $pemeriksaanHarian = PemeriksaanHarian::find($harianId);
            
            if (!$pemeriksaanHarian) {
                return response()->json(['success' => false, 'message' => 'Data pemeriksaan harian tidak ditemukan'], 404);
            }
            
            // Update data
            if ($request->filled('tanggal_jam')) {
                $pemeriksaanHarian->Tanggal_Jam = Carbon::parse($request->tanggal_jam);
            }
            
            if ($request->filled('hasil_pemeriksaan')) {
                $pemeriksaanHarian->Hasil_Pemeriksaan = $request->hasil_pemeriksaan;
            }
            
            $pemeriksaanHarian->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Pemeriksaan harian berhasil diperbarui',
                'data' => [
                    'id' => $pemeriksaanHarian->Id_Harian,
                    'tanggal_jam' => $this->formatTanggal($pemeriksaanHarian->Tanggal_Jam, 'Y-m-d H:i'),
                    'hasil_pemeriksaan' => $pemeriksaanHarian->Hasil_Pemeriksaan,
                    'siswa_id' => $pemeriksaanHarian->Id_Siswa,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating harian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete pemeriksaan harian (Petugas only)
     */
    public function deleteHarian($harianId)
    {
        try {
            if (session('user_level') !== 'petugas') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $pemeriksaanHarian = PemeriksaanHarian::find($harianId);
            
            if (!$pemeriksaanHarian) {
                return response()->json(['success' => false, 'message' => 'Data pemeriksaan harian tidak ditemukan'], 404);
            }
            
            $pemeriksaanHarian->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Pemeriksaan harian berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting harian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getHarianStatistics()
    {
        try {
            $userLevel = session('user_level');
            $siswaId = session('siswa_id');
            
            $stats = [];
            
            if ($userLevel === 'orang_tua' && $siswaId) {
                // Statistik untuk orang tua
                $stats = [
                    'total_pemeriksaan' => PemeriksaanHarian::where('Id_Siswa', $siswaId)->count(),
                    'pemeriksaan_bulan_ini' => PemeriksaanHarian::where('Id_Siswa', $siswaId)
                        ->whereMonth('Tanggal_Jam', Carbon::now()->month)
                        ->whereYear('Tanggal_Jam', Carbon::now()->year)
                        ->count(),
                    'pemeriksaan_minggu_ini' => PemeriksaanHarian::where('Id_Siswa', $siswaId)
                        ->whereBetween('Tanggal_Jam', [
                            Carbon::now()->startOfWeek(),
                            Carbon::now()->endOfWeek()
                        ])
                        ->count(),
                ];
            } else {
                // Statistik untuk admin, petugas, dokter
                $stats = [
                    'total_pemeriksaan' => PemeriksaanHarian::count(),
                    'pemeriksaan_bulan_ini' => PemeriksaanHarian::whereMonth('Tanggal_Jam', Carbon::now()->month)
                        ->whereYear('Tanggal_Jam', Carbon::now()->year)
                        ->count(),
                    'pemeriksaan_minggu_ini' => PemeriksaanHarian::whereBetween('Tanggal_Jam', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])->count(),
                    'total_siswa_diperiksa' => PemeriksaanHarian::distinct('Id_Siswa')->count(),
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting harian statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik'
            ], 500);
        }
    }

    /**
     * Get recent pemeriksaan harian
     */
    public function getRecentHarian(Request $request)
    {
        try {
            $userLevel = session('user_level');
            $limit = $request->get('limit', 10);
            
            $query = PemeriksaanHarian::with(['siswa', 'petugasUks']);
            
            if ($userLevel === 'orang_tua') {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan'], 404);
                }
                $query->where('Id_Siswa', $siswaId);
            }
            
            $recentHarian = $query->orderBy('Tanggal_Jam', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->Id_Harian,
                        'tanggal' => $this->formatTanggal($item->Tanggal_Jam, 'd M Y H:i'),
                        'nama_siswa' => $item->siswa->nama_siswa ?? 'N/A',
                        'petugas' => $item->petugasUks->nama_petugas_uks ?? 'N/A',
                        'hasil_pemeriksaan' => $item->Hasil_Pemeriksaan ? 
                            (strlen($item->Hasil_Pemeriksaan) > 50 ? 
                                substr($item->Hasil_Pemeriksaan, 0, 50) . '...' : 
                                $item->Hasil_Pemeriksaan) : 
                            'Belum ada hasil',
                        'siswa_id' => $item->Id_Siswa,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $recentHarian
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting recent harian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pemeriksaan terbaru'
            ], 500);
        }
    }
}