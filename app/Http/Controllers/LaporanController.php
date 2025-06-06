<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use PDF; // atau use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Siswa;
use App\Models\DetailPemeriksaan;
use App\Models\RekamMedis;
use App\Models\PemeriksaanAwal;
use App\Models\PemeriksaanFisik;
use App\Models\PemeriksaanHarian;
use App\Models\Resep;
use App\Models\Dokter;
use App\Models\PetugasUKS;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Exports\ScreeningExport;

class LaporanController extends Controller
{
    /**
     * Halaman utama screening dengan role-based access
     */
    public function screening(Request $request, $siswaId = null)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'tanggal_dari' => 'nullable|date|before_or_equal:tanggal_sampai',
                'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
                'nama_siswa' => 'nullable|string|max:50',
                'dokter' => 'nullable|exists:dokters,Id_Dokter',
                'petugas' => 'nullable|exists:petugas_uks,NIP',
                'status_pemeriksaan' => 'nullable|in:lengkap,belum lengkap',
                'status_input' => 'nullable|in:sudah_diisi,belum_diisi',
                'kelas' => 'nullable|exists:kelas,Kode_Kelas'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $userLevel = session('user_level');
            
            // Handle orang tua - hanya bisa lihat data anak sendiri
            if ($userLevel === 'orang_tua') {
                return $this->screeningOrangTua($request);
            }
            
            // Prepare data berdasarkan role
            $data = $this->prepareScreeningData($request, $userLevel, $siswaId);
            
            return view('laporan.screening', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in screening method: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data screening: ' . $e->getMessage());
        }
    }
    
    /**
     * Screening khusus untuk orang tua
     */
    private function screeningOrangTua(Request $request)
    {
        $siswaId = session('siswa_id');
        
        if (!$siswaId) {
            return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
        }
        
        try {
            $siswaInfo = Siswa::with(['detailSiswa.kelas.jurusan', 'orangTua'])
                ->findOrFail($siswaId);
            
            $pemeriksaanTerakhir = DetailPemeriksaan::with(['dokter', 'petugasUks'])
                ->where('id_siswa', $siswaId)
                ->orderBy('tanggal_jam', 'desc')
                ->first();
            
            $riwayatScreening = RekamMedis::with(['dokter'])
                ->where('Id_Siswa', $siswaId)
                ->orderBy('Tanggal_Jam', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($rekam) use ($siswaId) {
                    // Pastikan tanggal sudah jadi Carbon
                    $tanggal = $rekam->Tanggal_Jam ? Carbon::parse($rekam->Tanggal_Jam)->format('Y-m-d') : null;
                    $hasResep = false;
                    if ($tanggal) {
                        $hasResep = Resep::where('Id_Siswa', $siswaId)
                            ->whereDate('Tanggal_Resep', $tanggal)
                            ->exists();
                    }
                    return (object) [
                        'id' => $rekam->No_Rekam_Medis,
                        'tanggal' => $tanggal,
                        'status' => 'Lengkap',
                        'ringkasan' => $rekam->Keluhan_Utama ?: 'Pemeriksaan rutin screening kesehatan',
                        'ada_resep' => $hasResep,
                        'dokter' => $rekam->dokter->Nama_Dokter ?? 'Tidak ada dokter',
                        'siswa_id' => $siswaId,
                    ];
                });
            
            $totalRekamMedis = RekamMedis::where('Id_Siswa', $siswaId)->count();
            $totalRekamMedisBulanIni = RekamMedis::where('Id_Siswa', $siswaId)
                ->whereMonth('Tanggal_Jam', Carbon::now()->month)
                ->whereYear('Tanggal_Jam', Carbon::now()->year)
                ->count();
            
            return view('laporan.screening', [
                'siswaInfo' => $siswaInfo,
                'pemeriksaanTerakhir' => $pemeriksaanTerakhir,
                'riwayatScreening' => $riwayatScreening,
                'totalRekamMedis' => $totalRekamMedis,
                'totalRekamMedisBulanIni' => $totalRekamMedisBulanIni,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in screeningOrangTua: ' . $e->getMessage());
            return redirect()->route('dashboard.orangtua')->with('error', 'Gagal memuat data screening anak');
        }
    }
    
    /**
     * Prepare data screening berdasarkan role
     */
    private function prepareScreeningData(Request $request, $userLevel, $siswaId = null)
    {
        $data = [];
        
        // Data filter options dengan caching
        $data['siswas'] = Cache::remember('filter_siswas', 3600, function() {
            return Siswa::select('id_siswa', 'nama_siswa')->where('status_aktif', 1)->get();
        });
        
        $data['dokters'] = Cache::remember('filter_dokters', 3600, function() {
            return Dokter::where('status_aktif', 1)->get();
        });
        
        $data['petugasUKS'] = Cache::remember('filter_petugas', 3600, function() {
            return PetugasUKS::where('status_aktif', 1)->get();
        });
        
        $data['kelasList'] = Cache::remember('filter_kelas', 3600, function() {
            return Kelas::with('jurusan')->get();
        });
        
        // Prepare query berdasarkan role
        switch ($userLevel) {
            case 'admin':
                $data = array_merge($data, $this->getAdminScreeningData($request));
                break;
                
            case 'petugas':
                $data = array_merge($data, $this->getPetugasScreeningData($request));
                break;
                
            case 'dokter':
                $data = array_merge($data, $this->getDokterScreeningData($request));
                break;
        }
        
        return $data;
    }
    
    /**
     * Data screening untuk Admin - DIPERBAIKI UNTUK MENAMPILKAN NAMA PETUGAS DAN DOKTER
     */
    private function getAdminScreeningData(Request $request)
    {
        // Query dengan JOIN yang lebih eksplisit untuk memastikan data ter-load
        $query = DB::table('detail_pemeriksaans as dp')
            ->leftJoin('siswas as s', 'dp.id_siswa', '=', 's.id_siswa')
            ->leftJoin('detail_siswas as ds', 's.id_siswa', '=', 'ds.id_siswa')
            ->leftJoin('kelas as k', 'ds.kode_kelas', '=', 'k.Kode_Kelas')
            ->leftJoin('dokters as d', 'dp.id_dokter', '=', 'd.Id_Dokter')
            ->leftJoin('petugas_uks as pu', 'dp.nip', '=', 'pu.NIP')
            ->leftJoin('pemeriksaan_awals as pa', 'dp.id_detprx', '=', 'pa.id_detprx')
            ->leftJoin('pemeriksaan_fisiks as pf', 'dp.id_detprx', '=', 'pf.id_detprx')
            ->select([
                'dp.id_detprx',
                'dp.tanggal_jam',
                'dp.status_pemeriksaan',
                'dp.id_siswa as siswa_id',
                's.nama_siswa',
                'k.Nama_Kelas as kelas',
                'd.Nama_Dokter as nama_dokter',
                'd.Spesialisasi as spesialisasi_dokter',
                'pu.nama_petugas_uks as nama_petugas',
                'pu.level as level_petugas',
                DB::raw('CASE WHEN pa.id_preawal IS NOT NULL THEN 1 ELSE 0 END as pemeriksaan_awal'),
                DB::raw('CASE WHEN pf.id_prefisik IS NOT NULL THEN 1 ELSE 0 END as pemeriksaan_fisik')
            ])
            ->where('s.status_aktif', 1);
        
        // Apply filters dengan modifikasi untuk DB query
        $this->applyFiltersToDBQuery($query, $request);
        
        // Get paginated data
        $results = $query->orderBy('dp.tanggal_jam', 'desc')->paginate(15);
        
        // Transform ke format yang dibutuhkan view
        $screeningData = $results;
        $screeningData->getCollection()->transform(function ($item) {
            return (object) [
                'id_detprx' => $item->id_detprx,
                'tanggal_jam' => $item->tanggal_jam,
                'nama_siswa' => $item->nama_siswa ?: 'Data tidak tersedia',
                'kelas' => $item->kelas ?: 'Belum ditentukan',
                'nama_petugas' => $item->nama_petugas ?: 'Belum ditentukan',
                'nama_dokter' => $item->nama_dokter ?: 'Belum ditentukan',
                'status_pemeriksaan' => $item->status_pemeriksaan,
                'siswa_id' => $item->siswa_id,
                'pemeriksaan_awal' => $item->pemeriksaan_awal,
                'pemeriksaan_fisik' => $item->pemeriksaan_fisik,
            ];
        });
        
        return [
            'screeningData' => $screeningData,
            'totalRekamMedis' => RekamMedis::count(),
            'totalRekamMedisBulanIni' => RekamMedis::whereMonth('Tanggal_Jam', Carbon::now()->month)->count(),
        ];
    }
    
    /**
     * Data screening untuk Petugas - DIPERBAIKI SESUAI DESAIN
     */
    private function getPetugasScreeningData(Request $request)
    {
        // Query dengan JOIN yang eksplisit
        $query = DB::table('detail_pemeriksaans as dp')
            ->leftJoin('siswas as s', 'dp.id_siswa', '=', 's.id_siswa')
            ->leftJoin('detail_siswas as ds', 's.id_siswa', '=', 'ds.id_siswa')
            ->leftJoin('kelas as k', 'ds.kode_kelas', '=', 'k.Kode_Kelas')
            ->leftJoin('dokters as d', 'dp.id_dokter', '=', 'd.Id_Dokter')
            ->leftJoin('petugas_uks as pu', 'dp.nip', '=', 'pu.NIP')
            ->leftJoin('pemeriksaan_awals as pa', 'dp.id_detprx', '=', 'pa.id_detprx')
            ->leftJoin('pemeriksaan_fisiks as pf', 'dp.id_detprx', '=', 'pf.id_detprx')
            ->select([
                'dp.id_detprx',
                'dp.tanggal_jam',
                'dp.status_pemeriksaan',
                'dp.id_siswa as siswa_id',
                's.nama_siswa',
                'k.Nama_Kelas as kelas',
                'd.Nama_Dokter as nama_dokter',
                'pu.nama_petugas_uks as nama_petugas',
                DB::raw('CASE WHEN pa.id_preawal IS NOT NULL THEN 1 ELSE 0 END as pemeriksaan_awal'),
                DB::raw('CASE WHEN pf.id_prefisik IS NOT NULL THEN 1 ELSE 0 END as pemeriksaan_fisik'),
                DB::raw('DATE(dp.tanggal_jam) as tanggal')
            ])
            ->where('s.status_aktif', 1);
        
        // Apply filters untuk petugas
        $this->applyFiltersToDBQuery($query, $request);
        
        // Filter khusus untuk status input petugas
        if ($request->filled('status_input')) {
            if ($request->status_input === 'sudah_diisi') {
                $query->where(function($q) {
                    $q->whereNotNull('pa.id_preawal')
                      ->orWhereNotNull('pf.id_prefisik');
                });
            } else {
                $query->whereNull('pa.id_preawal')
                     ->whereNull('pf.id_prefisik');
            }
        }
        
        $results = $query->orderBy('dp.tanggal_jam', 'desc')->paginate(15);
        
        // Transform data untuk petugas view
        $pemeriksaanData = $results;
        $pemeriksaanData->getCollection()->transform(function ($item) {
            return (object) [
                'id_detprx' => $item->id_detprx,
                'tanggal' => $item->tanggal_jam,
                'nama_siswa' => $item->nama_siswa ?: 'Data tidak tersedia',
                'kelas' => $item->kelas ?: 'Belum ditentukan',
                'nama_dokter' => $item->nama_dokter ?: 'Belum ditentukan',
                'nama_petugas' => $item->nama_petugas ?: 'Belum ditentukan',
                'pemeriksaan_awal' => $item->pemeriksaan_awal ? true : false,
                'pemeriksaan_fisik' => $item->pemeriksaan_fisik ? true : false,
                'status' => $item->status_pemeriksaan,
                'siswa_id' => $item->siswa_id,
            ];
        });
        
        return [
            'pemeriksaanData' => $pemeriksaanData,
            'totalRekamMedis' => DetailPemeriksaan::count(),
            'totalRekamMedisBulanIni' => DetailPemeriksaan::whereMonth('tanggal_jam', Carbon::now()->month)->count(),
        ];
    }
    
    /**
     * Data screening untuk Dokter - DIPERBAIKI DENGAN JOIN EKSPLISIT
     */
    private function getDokterScreeningData(Request $request)
    {
        // Query dengan JOIN yang eksplisit
        $query = DB::table('detail_pemeriksaans as dp')
            ->leftJoin('siswas as s', 'dp.id_siswa', '=', 's.id_siswa')
            ->leftJoin('detail_siswas as ds', 's.id_siswa', '=', 'ds.id_siswa')
            ->leftJoin('kelas as k', 'ds.kode_kelas', '=', 'k.Kode_Kelas')
            ->leftJoin('dokters as d', 'dp.id_dokter', '=', 'd.Id_Dokter')
            ->leftJoin('petugas_uks as pu', 'dp.nip', '=', 'pu.NIP')
            ->leftJoin('pemeriksaan_awals as pa', 'dp.id_detprx', '=', 'pa.id_detprx')
            ->leftJoin('pemeriksaan_fisiks as pf', 'dp.id_detprx', '=', 'pf.id_detprx')
            ->select([
                'dp.id_detprx',
                'dp.tanggal_jam',
                'dp.status_pemeriksaan',
                'dp.id_siswa as siswa_id',
                's.nama_siswa',
                'k.Nama_Kelas as kelas',
                'd.Nama_Dokter as nama_dokter',
                'pu.nama_petugas_uks as nama_petugas',
                DB::raw('CASE WHEN pa.id_preawal IS NOT NULL THEN 1 ELSE 0 END as pemeriksaan_awal'),
                DB::raw('CASE WHEN pf.id_prefisik IS NOT NULL THEN 1 ELSE 0 END as pemeriksaan_fisik'),
                DB::raw('DATE(dp.tanggal_jam) as tanggal')
            ])
            ->where('s.status_aktif', 1);
        
        // Filter berdasarkan dokter yang login (jika ada session)
        $dokterId = session('user_id');
        if ($dokterId && session('user_level') === 'dokter') {
            $query->where('dp.id_dokter', $dokterId);
        }
        
        // Apply filters untuk dokter
        $this->applyFiltersToDBQuery($query, $request);
        
        $results = $query->orderBy('dp.tanggal_jam', 'desc')->paginate(15);
        
        // Transform data untuk dokter view
        $pemeriksaanData = $results;
        $pemeriksaanData->getCollection()->transform(function ($item) {
            return (object) [
                'id_detprx' => $item->id_detprx,
                'tanggal' => $item->tanggal_jam,
                'nama_siswa' => $item->nama_siswa ?: 'Data tidak tersedia',
                'kelas' => $item->kelas ?: 'Belum ditentukan',
                'nama_dokter' => $item->nama_dokter ?: 'Belum ditentukan',
                'nama_petugas' => $item->nama_petugas ?: 'Belum ditentukan',
                'pemeriksaan_awal' => $item->pemeriksaan_awal ? true : false,
                'pemeriksaan_fisik' => $item->pemeriksaan_fisik ? true : false,
                'status' => $item->status_pemeriksaan,
                'siswa_id' => $item->siswa_id,
            ];
        });
        
        return [
            'pemeriksaanData' => $pemeriksaanData,
            'totalRekamMedis' => DetailPemeriksaan::count(),
            'totalRekamMedisBulanIni' => DetailPemeriksaan::whereMonth('tanggal_jam', Carbon::now()->month)->count(),
        ];
    }
    
    /**
     * Apply filters to DB query (untuk query builder)
     */
    private function applyFiltersToDBQuery($query, Request $request)
    {
        try {
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('dp.tanggal_jam', '>=', Carbon::parse($request->tanggal_dari));
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('dp.tanggal_jam', '<=', Carbon::parse($request->tanggal_sampai));
            }
            
            if ($request->filled('nama_siswa')) {
                $query->where('s.nama_siswa', 'like', '%' . $request->nama_siswa . '%');
            }
            
            if ($request->filled('dokter')) {
                $query->where('dp.id_dokter', $request->dokter);
            }
            
            if ($request->filled('petugas')) {
                $query->where('dp.nip', $request->petugas);
            }
            
            if ($request->filled('status_pemeriksaan')) {
                $query->where('dp.status_pemeriksaan', $request->status_pemeriksaan);
            }
        } catch (\Exception $e) {
            Log::error('Error applying filters to DB query: ' . $e->getMessage());
        }
        
        return $query;
    }
    
    /**
     * Apply filters to query (untuk Eloquent)
     */
    private function applyFilters($query, Request $request)
    {
        try {
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_jam', '>=', Carbon::parse($request->tanggal_dari));
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_jam', '<=', Carbon::parse($request->tanggal_sampai));
            }
            
            if ($request->filled('nama_siswa')) {
                $query->whereHas('siswa', function ($q) use ($request) {
                    $q->where('nama_siswa', 'like', '%' . $request->nama_siswa . '%');
                });
            }
            
            if ($request->filled('dokter')) {
                $query->where('id_dokter', $request->dokter);
            }
            
            if ($request->filled('petugas')) {
                $query->where('nip', $request->petugas);
            }
            
            if ($request->filled('status_pemeriksaan')) {
                $query->where('status_pemeriksaan', $request->status_pemeriksaan);
            }
        } catch (\Exception $e) {
            Log::error('Error applying filters: ' . $e->getMessage());
        }
        
        return $query;
    }

    /**
     * Show detail screening - DITAMBAHKAN UNTUK ROUTING ICON MATA
     */
    public function showScreeningDetail(Request $request, $siswaId, $detailPemeriksaanId = null)
    {
        try {
            $userLevel = session('user_level');
            
            // Check access permission untuk orang tua
            if ($userLevel === 'orang_tua' && $siswaId != session('siswa_id')) {
                return redirect()->back()->with('error', 'Akses ditolak');
            }
            
            // Find siswa
            $siswa = Siswa::with([
                'detailSiswa.kelas.jurusan',
                'orangTua'
            ])->findOrFail($siswaId);
            
            // Find detail pemeriksaan
            if ($detailPemeriksaanId) {
                $detailPemeriksaan = DetailPemeriksaan::with([
                    'dokter', 
                    'petugasUks',
                    'pemeriksaanAwal',
                    'pemeriksaanFisik'
                ])->where('id_detprx', $detailPemeriksaanId)
                  ->where('id_siswa', $siswaId)
                  ->firstOrFail();
            } else {
                $detailPemeriksaan = DetailPemeriksaan::with([
                    'dokter', 
                    'petugasUks',
                    'pemeriksaanAwal',
                    'pemeriksaanFisik'
                ])->where('id_siswa', $siswaId)
                  ->orderBy('tanggal_jam', 'desc')
                  ->firstOrFail();
            }
            
            // Find related rekam medis
            $tanggalPemeriksaan = $detailPemeriksaan->tanggal_jam ? 
                Carbon::parse($detailPemeriksaan->tanggal_jam)->format('Y-m-d') : null;
            
            $rekamMedis = null;
            if ($tanggalPemeriksaan) {
                $rekamMedis = RekamMedis::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereDate('Tanggal_Jam', $tanggalPemeriksaan)
                    ->first();
            }
            
            return view('laporan.screening_detail', [
                'siswa' => $siswa,
                'detailPemeriksaan' => $detailPemeriksaan,
                'rekamMedis' => $rekamMedis,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing screening detail: ' . $e->getMessage(), [
                'siswa_id' => $siswaId,
                'detail_pemeriksaan_id' => $detailPemeriksaanId,
                'user_level' => session('user_level')
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail screening: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate PDF laporan screening - DIPERBAIKI DENGAN PENCARIAN RESEP YANG LEBIH FLEKSIBEL
     */
    public function generateScreeningPDF(Request $request, $siswaId = null)
    {
        try {
            // Log awal untuk debugging
            Log::info('Starting PDF generation', [
                'siswa_id' => $siswaId,
                'request_data' => $request->all(),
                'user_level' => session('user_level')
            ]);

            // Determine siswa ID
            if (!$siswaId && $request->filled('siswa_id')) {
                $siswaId = $request->siswa_id;
            }
            if (session('user_level') === 'orang_tua') {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
                }
            }
            if (!$siswaId) {
                return redirect()->back()->with('error', 'ID Siswa harus disediakan');
            }

            // Get siswa data
            $siswa = Siswa::with([
                'detailSiswa.kelas.jurusan',
                'orangTua'
            ])->find($siswaId);

            if (!$siswa) {
                return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
            }

            Log::info('Siswa found', ['siswa_name' => $siswa->nama_siswa]);

            // Get detail pemeriksaan
            $detailPemeriksaanId = $request->get('detail_pemeriksaan_id');
            if ($detailPemeriksaanId) {
                $detailPemeriksaan = DetailPemeriksaan::with(['dokter', 'petugasUks'])
                    ->where('id_detprx', $detailPemeriksaanId)
                    ->where('id_siswa', $siswaId)
                    ->first();
            } else {
                $detailPemeriksaan = DetailPemeriksaan::with(['dokter', 'petugasUks'])
                    ->where('id_siswa', $siswaId)
                    ->orderBy('tanggal_jam', 'desc')
                    ->first();
            }

            if (!$detailPemeriksaan) {
                return redirect()->back()->with('error', 'Data pemeriksaan tidak ditemukan untuk siswa ini');
            }

            Log::info('Detail pemeriksaan found', [
                'id_detprx' => $detailPemeriksaan->id_detprx,
                'tanggal_jam' => $detailPemeriksaan->tanggal_jam
            ]);

            // Get tanggal pemeriksaan
            $tanggalPemeriksaan = $detailPemeriksaan->tanggal_jam ? 
                Carbon::parse($detailPemeriksaan->tanggal_jam)->format('Y-m-d') : null;

            // Get rekam medis
            $rekamMedis = null;
            if ($tanggalPemeriksaan) {
                $rekamMedis = RekamMedis::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereDate('Tanggal_Jam', $tanggalPemeriksaan)
                    ->first();
            }

            // Get pemeriksaan awal
            $pemeriksaanAwal = PemeriksaanAwal::where('id_detprx', $detailPemeriksaan->id_detprx)->first();

            // Get pemeriksaan fisik
            $pemeriksaanFisik = PemeriksaanFisik::where('id_detprx', $detailPemeriksaan->id_detprx)->first();

            // ========== PERBAIKAN UTAMA: PENCARIAN RESEP YANG LEBIH FLEKSIBEL ==========
            $resepObat = collect();

            // 1. Cari resep berdasarkan tanggal pemeriksaan yang sama
            if ($tanggalPemeriksaan) {
                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereDate('Tanggal_Resep', $tanggalPemeriksaan)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();

                Log::info('Resep search by exact date', [
                    'siswa_id' => $siswaId,
                    'tanggal_pemeriksaan' => $tanggalPemeriksaan,
                    'resep_count' => $resepObat->count()
                ]);
            }

            // 2. Jika tidak ada resep di tanggal yang sama, cari dalam rentang ±3 hari
            if ($resepObat->isEmpty() && $tanggalPemeriksaan) {
                $tanggalMulai = Carbon::parse($tanggalPemeriksaan)->subDays(3)->format('Y-m-d');
                $tanggalAkhir = Carbon::parse($tanggalPemeriksaan)->addDays(3)->format('Y-m-d');

                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereBetween('Tanggal_Resep', [$tanggalMulai, $tanggalAkhir])
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();

                Log::info('Resep search by date range (±3 days)', [
                    'siswa_id' => $siswaId,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_akhir' => $tanggalAkhir,
                    'resep_count' => $resepObat->count()
                ]);
            }

            // 3. Jika masih tidak ada, cari dalam rentang ±7 hari
            if ($resepObat->isEmpty() && $tanggalPemeriksaan) {
                $tanggalMulai = Carbon::parse($tanggalPemeriksaan)->subDays(7)->format('Y-m-d');
                $tanggalAkhir = Carbon::parse($tanggalPemeriksaan)->addDays(7)->format('Y-m-d');

                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereBetween('Tanggal_Resep', [$tanggalMulai, $tanggalAkhir])
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();

                Log::info('Resep search by wider date range (±7 days)', [
                    'siswa_id' => $siswaId,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_akhir' => $tanggalAkhir,
                    'resep_count' => $resepObat->count()
                ]);
            }

            // 4. Jika masih tidak ada, ambil semua resep siswa dalam bulan yang sama
            if ($resepObat->isEmpty() && $tanggalPemeriksaan) {
                $tanggalCarbon = Carbon::parse($tanggalPemeriksaan);
                
                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereMonth('Tanggal_Resep', $tanggalCarbon->month)
                    ->whereYear('Tanggal_Resep', $tanggalCarbon->year)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->limit(5) // Batasi maksimal 5 resep
                    ->get();

                Log::info('Resep search by same month', [
                    'siswa_id' => $siswaId,
                    'month' => $tanggalCarbon->month,
                    'year' => $tanggalCarbon->year,
                    'resep_count' => $resepObat->count()
                ]);
            }

            // 5. Sebagai fallback terakhir, ambil resep terbaru untuk siswa ini
            if ($resepObat->isEmpty()) {
                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->limit(3) // Batasi maksimal 3 resep terbaru
                    ->get();

                Log::info('Resep search - fallback to latest prescriptions', [
                    'siswa_id' => $siswaId,
                    'resep_count' => $resepObat->count()
                ]);
            }

            // Debug info: Total resep untuk siswa ini
            $totalResepSiswa = Resep::where('Id_Siswa', $siswaId)->count();
            Log::info('Total prescriptions for student', [
                'siswa_id' => $siswaId,
                'total_resep_count' => $totalResepSiswa,
                'found_resep_count' => $resepObat->count(),
                'resep_ids' => $resepObat->pluck('Id_Resep')->toArray()
            ]);

            // Prepare data for PDF
            $data = [
                'siswa' => $siswa,
                'detailPemeriksaan' => $detailPemeriksaan,
                'rekamMedis' => $rekamMedis,
                'pemeriksaanAwal' => $pemeriksaanAwal,
                'pemeriksaanFisik' => $pemeriksaanFisik,
                'resepObat' => $resepObat, // PASTIKAN DATA RESEP TER-PASS
                'tanggalCetak' => Carbon::now(),
                'tanggalPemeriksaan' => $tanggalPemeriksaan
            ];

            // Log data yang akan di-pass ke PDF
            Log::info('Data prepared for PDF', [
                'has_siswa' => !is_null($data['siswa']),
                'has_rekam_medis' => !is_null($data['rekamMedis']),
                'has_pemeriksaan_awal' => !is_null($data['pemeriksaanAwal']),
                'has_pemeriksaan_fisik' => !is_null($data['pemeriksaanFisik']),
                'resep_count' => $data['resepObat']->count(),
                'resep_data' => $data['resepObat']->map(function($resep) {
                    return [
                        'id' => $resep->Id_Resep,
                        'nama_obat' => $resep->Nama_Obat,
                        'tanggal' => $resep->Tanggal_Resep
                    ];
                })->toArray()
            ]);

            try {
                // Generate PDF
                $pdf = PDF::loadView('laporan.screening_pdf', $data);
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

            // Generate filename
            $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $siswa->nama_siswa);
            $filename = 'Laporan_Screening_' . $safeName . '_' . ($tanggalPemeriksaan ?: date('Y-m-d')) . '.pdf';

            Log::info('PDF generated successfully', [
                'user_level' => session('user_level'),
                'user_id' => session('user_id'),
                'siswa_id' => $siswaId,
                'filename' => $filename,
                'resep_included' => $resepObat->count() > 0
            ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating screening PDF: ' . $e->getMessage(), [
                'siswa_id' => $siswaId,
                'user_level' => session('user_level'),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghasilkan laporan PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Preview PDF sebelum download - DIPERBAIKI LENGKAP DENGAN RESEP FLEKSIBEL
     */
    public function previewScreeningPDF(Request $request, $siswaId = null)
    {
        try {
            // Determine siswa ID
            if (!$siswaId && $request->filled('siswa_id')) {
                $siswaId = $request->siswa_id;
            }
            if (session('user_level') === 'orang_tua') {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
                }
            }
            if (!$siswaId) {
                return redirect()->back()->with('error', 'ID Siswa harus disediakan');
            }

            // Get siswa data
            $siswa = Siswa::with([
                'detailSiswa.kelas.jurusan',
                'orangTua'
            ])->find($siswaId);

            if (!$siswa) {
                return redirect()->back()->with('error', 'Data siswa tidak ditemukan');
            }

            // Get detail pemeriksaan
            $detailPemeriksaanId = $request->get('detail_pemeriksaan_id');
            if ($detailPemeriksaanId) {
                $detailPemeriksaan = DetailPemeriksaan::with(['dokter', 'petugasUks'])
                    ->where('id_detprx', $detailPemeriksaanId)
                    ->where('id_siswa', $siswaId)
                    ->first();
            } else {
                $detailPemeriksaan = DetailPemeriksaan::with(['dokter', 'petugasUks'])
                    ->where('id_siswa', $siswaId)
                    ->orderBy('tanggal_jam', 'desc')
                    ->first();
            }

            if (!$detailPemeriksaan) {
                return redirect()->back()->with('error', 'Data pemeriksaan tidak ditemukan untuk siswa ini');
            }

            // Get tanggal pemeriksaan
            $tanggalPemeriksaan = $detailPemeriksaan->tanggal_jam ? 
                Carbon::parse($detailPemeriksaan->tanggal_jam)->format('Y-m-d') : null;

            // Get rekam medis
            $rekamMedis = null;
            if ($tanggalPemeriksaan) {
                $rekamMedis = RekamMedis::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereDate('Tanggal_Jam', $tanggalPemeriksaan)
                    ->first();
            }

            // Get pemeriksaan awal dan fisik
            $pemeriksaanAwal = PemeriksaanAwal::where('id_detprx', $detailPemeriksaan->id_detprx)->first();
            $pemeriksaanFisik = PemeriksaanFisik::where('id_detprx', $detailPemeriksaan->id_detprx)->first();

            // ========== PENCARIAN RESEP YANG SAMA DENGAN generateScreeningPDF ==========
            $resepObat = collect();

            // 1. Cari resep berdasarkan tanggal pemeriksaan yang sama
            if ($tanggalPemeriksaan) {
                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereDate('Tanggal_Resep', $tanggalPemeriksaan)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();
            }

            // 2. Jika tidak ada resep di tanggal yang sama, cari dalam rentang ±3 hari
            if ($resepObat->isEmpty() && $tanggalPemeriksaan) {
                $tanggalMulai = Carbon::parse($tanggalPemeriksaan)->subDays(3)->format('Y-m-d');
                $tanggalAkhir = Carbon::parse($tanggalPemeriksaan)->addDays(3)->format('Y-m-d');

                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereBetween('Tanggal_Resep', [$tanggalMulai, $tanggalAkhir])
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();
            }

            // 3. Jika masih tidak ada, cari dalam rentang ±7 hari
            if ($resepObat->isEmpty() && $tanggalPemeriksaan) {
                $tanggalMulai = Carbon::parse($tanggalPemeriksaan)->subDays(7)->format('Y-m-d');
                $tanggalAkhir = Carbon::parse($tanggalPemeriksaan)->addDays(7)->format('Y-m-d');

                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereBetween('Tanggal_Resep', [$tanggalMulai, $tanggalAkhir])
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();
            }

            // 4. Fallback: ambil resep terbaru
            if ($resepObat->isEmpty()) {
                $resepObat = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->limit(3)
                    ->get();
            }

            // Prepare data
            $data = [
                'siswa' => $siswa,
                'detailPemeriksaan' => $detailPemeriksaan,
                'rekamMedis' => $rekamMedis,
                'pemeriksaanAwal' => $pemeriksaanAwal,
                'pemeriksaanFisik' => $pemeriksaanFisik,
                'resepObat' => $resepObat,
                'tanggalCetak' => Carbon::now(),
                'tanggalPemeriksaan' => $tanggalPemeriksaan
            ];

            // Generate PDF
            $pdf = PDF::loadView('laporan.screening_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'defaultPaperSize' => 'A4',
            ]);

            $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $siswa->nama_siswa);
            $filename = 'Preview_Screening_' . $safeName . '_' . ($tanggalPemeriksaan ?: date('Y-m-d')) . '.pdf';

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('Error previewing screening PDF: ' . $e->getMessage(), [
                'siswa_id' => $siswaId,
                'user_level' => session('user_level')
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menampilkan preview PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Export screening data to Excel
     */
    public function exportScreening(Request $request, $siswaId = null)
    {
        try {
            $userLevel = session('user_level');
            
            // Handle orang tua
            if ($userLevel === 'orang_tua') {
                $siswaId = session('siswa_id');
                return $this->exportScreeningOrangTua($siswaId, $request);
            }
            
            // Export berdasarkan role
            switch ($userLevel) {
                case 'admin':
                    return $this->exportScreeningAdmin($request);
                case 'petugas':
                    return $this->exportScreeningPetugas($request);
                case 'dokter':
                    return $this->exportScreeningDokter($request);
                default:
                    return redirect()->back()->with('error', 'Role tidak dikenali');
            }
            
        } catch (\Exception $e) {
            Log::error('Error exporting screening: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Export untuk Admin
     */
    private function exportScreeningAdmin(Request $request)
    {
        try {
            $filename = 'Laporan_Screening_Admin_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new ScreeningExport($request, 'admin'), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting admin screening: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Export untuk Petugas
     */
    private function exportScreeningPetugas(Request $request)
    {
        try {
            $filename = 'Laporan_Pemeriksaan_Petugas_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new ScreeningExport($request, 'petugas'), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting petugas screening: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Export untuk Dokter
     */
    private function exportScreeningDokter(Request $request)
    {
        try {
            $filename = 'Laporan_Pemeriksaan_Dokter_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new ScreeningExport($request, 'dokter'), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting dokter screening: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Export untuk Orang Tua - DIPERBAIKI LENGKAP
     */
    private function exportScreeningOrangTua($siswaId, Request $request)
    {
        try {
            if (!$siswaId) {
                throw new \Exception('ID Siswa tidak ditemukan');
            }
            
            // Ambil nama siswa untuk filename
            $siswa = Siswa::find($siswaId);
            $namaSiswa = $siswa ? preg_replace('/[^A-Za-z0-9_.-]/', '_', $siswa->nama_siswa) : 'Unknown';
            
            $filename = 'Riwayat_Kesehatan_' . $namaSiswa . '_' . Carbon::now()->format('Y-m-d') . '.xlsx';
            
            return Excel::download(new ScreeningExport($request, 'orang_tua', $siswaId), $filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting orang tua screening: ' . $e->getMessage(), [
                'siswa_id' => $siswaId
            ]);
            throw $e;
        }
    }
    
    /**
     * Get screening history for AJAX
     */
    public function getScreeningHistory($siswaId)
    {
        try {
            $userLevel = session('user_level');
            
            // Check access permission
            if ($userLevel === 'orang_tua' && $siswaId != session('siswa_id')) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            $screeningHistory = DetailPemeriksaan::with(['dokter'])
                ->where('id_siswa', $siswaId)
                ->orderBy('tanggal_jam', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id_detprx,
                        'tanggal' => Carbon::parse($item->tanggal_jam)->format('d F Y'),
                        'waktu' => Carbon::parse($item->tanggal_jam)->format('H:i'),
                        'dokter' => $item->dokter->Nama_Dokter ?? 'Tidak ada dokter',
                        'status' => $item->status_pemeriksaan,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $screeningHistory
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching screening history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching screening history: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get detail pemeriksaan for API (khusus untuk modal orang tua) - DIPERBAIKI LENGKAP UNTUK RESEP ISSUE
     */
    public function getDetailPemeriksaan($siswaId, $rekamMedisId)
    {
        try {
            // Log untuk debugging
            Log::info('API getDetailPemeriksaan called', [
                'siswa_id' => $siswaId,
                'rekam_medis_id' => $rekamMedisId,
                'user_level' => session('user_level'),
                'user_id' => session('user_id')
            ]);
            
            $userLevel = session('user_level');
            
            // Check access permission untuk orang tua
            if ($userLevel === 'orang_tua' && $siswaId != session('siswa_id')) {
                Log::warning('Access denied - wrong siswa_id', [
                    'requested_siswa_id' => $siswaId,
                    'session_siswa_id' => session('siswa_id')
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Akses ditolak - Anda hanya dapat melihat data anak sendiri'
                ], 403);
            }
            
            // Pastikan siswa ada
            $siswa = Siswa::with(['detailSiswa.kelas.jurusan'])->find($siswaId);
            if (!$siswa) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }
            
            // Cari rekam medis
            $rekamMedis = RekamMedis::with(['dokter'])
                ->where('No_Rekam_Medis', $rekamMedisId)
                ->where('Id_Siswa', $siswaId)
                ->first();
            
            if (!$rekamMedis) {
                Log::warning('Rekam medis not found', [
                    'rekam_medis_id' => $rekamMedisId,
                    'siswa_id' => $siswaId
                ]);
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Data pemeriksaan tidak ditemukan'
                ], 404);
            }
            
            // Get tanggal rekam medis
            $tanggalRekam = $rekamMedis->Tanggal_Jam ? Carbon::parse($rekamMedis->Tanggal_Jam) : null;
            
            // Cari detail pemeriksaan terkait berdasarkan tanggal
            $detailPemeriksaan = DetailPemeriksaan::with(['pemeriksaanAwal', 'pemeriksaanFisik', 'petugasUks'])
                ->where('id_siswa', $siswaId)
                ->when($tanggalRekam, function($query) use ($tanggalRekam) {
                    $query->whereDate('tanggal_jam', $tanggalRekam->format('Y-m-d'));
                })
                ->first();
            
            // ========== PERBAIKAN UTAMA: PENCARIAN RESEP YANG SAMA DENGAN PDF ==========
            $resep = collect();
            
            // 1. Cari resep di tanggal yang sama terlebih dahulu
            if ($tanggalRekam) {
                $resep = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereDate('Tanggal_Resep', $tanggalRekam->format('Y-m-d'))
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();
                
                Log::info('Resep search by exact date', [
                    'siswa_id' => $siswaId,
                    'tanggal_rekam' => $tanggalRekam->format('Y-m-d'),
                    'resep_count' => $resep->count()
                ]);
            }
            
            // 2. Jika tidak ada resep di tanggal yang sama, cari dalam rentang 3 hari
            if ($resep->isEmpty() && $tanggalRekam) {
                $tanggalMulai = $tanggalRekam->copy()->subDays(3);
                $tanggalAkhir = $tanggalRekam->copy()->addDays(3);
                
                $resep = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereBetween('Tanggal_Resep', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();
                
                Log::info('Resep search by date range (±3 days)', [
                    'siswa_id' => $siswaId,
                    'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                    'tanggal_akhir' => $tanggalAkhir->format('Y-m-d'),
                    'resep_count' => $resep->count()
                ]);
            }
            
            // 3. Jika masih tidak ada, cari dalam rentang 7 hari
            if ($resep->isEmpty() && $tanggalRekam) {
                $tanggalMulai = $tanggalRekam->copy()->subDays(7);
                $tanggalAkhir = $tanggalRekam->copy()->addDays(7);
                
                $resep = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->whereBetween('Tanggal_Resep', [$tanggalMulai->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();
                
                Log::info('Resep search by wider date range (±7 days)', [
                    'siswa_id' => $siswaId,
                    'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                    'tanggal_akhir' => $tanggalAkhir->format('Y-m-d'),
                    'resep_count' => $resep->count()
                ]);
            }
            
            // 4. Jika masih tidak ada, ambil semua resep siswa (maksimal 5 terakhir)
            if ($resep->isEmpty()) {
                $resep = Resep::with('dokter')
                    ->where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->limit(5)
                    ->get();
                
                Log::info('Resep search all student prescriptions', [
                    'siswa_id' => $siswaId,
                    'resep_count' => $resep->count()
                ]);
            }
            
            // Debug total resep untuk siswa ini
            $totalResepSiswa = Resep::where('Id_Siswa', $siswaId)->count();
            Log::info('Total resep untuk siswa', [
                'siswa_id' => $siswaId,
                'total_resep_count' => $totalResepSiswa
            ]);
            
            // Helper function untuk format nullable value
            $formatValue = function($value, $default = null) {
                return $value && $value !== '' ? $value : $default;
            };
            
            // Prepare response data - LENGKAP SESUAI STRUKTUR FRONTEND
            $responseData = [
                // Basic Info
                'nama_siswa' => $siswa->nama_siswa,
                'kelas' => $siswa->detailSiswa->kelas->Nama_Kelas ?? 'N/A',
                'tanggal_pemeriksaan' => $tanggalRekam ? $tanggalRekam->format('d F Y') : null,
                'nama_dokter' => $rekamMedis->dokter->Nama_Dokter ?? 'Belum ditentukan',
                
                // Rekam Medis - SEMUA FIELD SESUAI DATABASE
                'rekam_medis' => [
                    'keluhan_utama' => $formatValue($rekamMedis->Keluhan_Utama),
                    'riwayat_penyakit_sekarang' => $formatValue($rekamMedis->Riwayat_Penyakit_Sekarang),
                    'riwayat_penyakit_dahulu' => $formatValue($rekamMedis->Riwayat_Penyakit_Dahulu),
                    'riwayat_imunisasi' => $formatValue($rekamMedis->Riwayat_Imunisasi),
                    'riwayat_penyakit_keluarga' => $formatValue($rekamMedis->Riwayat_Penyakit_Keluarga),
                    'silsilah_keluarga' => $formatValue($rekamMedis->Silsilah_Keluarga)
                ],
                
                // Pemeriksaan Awal - SEMUA FIELD SESUAI DATABASE
                'pemeriksaan_awal' => null,
                
                // Pemeriksaan Fisik - SEMUA FIELD SESUAI DATABASE  
                'pemeriksaan_fisik' => null,
                
                // Resep Obat - DIPERBAIKI DENGAN STRUKTUR YANG BENAR
                'resep' => $resep->map(function($r) {
                    return [
                        'id_resep' => $r->Id_Resep,
                        'nama_obat' => $r->Nama_Obat,
                        'dosis' => $r->Dosis,
                        'durasi' => $r->Durasi,
                        'tanggal_resep' => Carbon::parse($r->Tanggal_Resep)->format('d/m/Y'),
                        'dokter' => $r->dokter->Nama_Dokter ?? 'N/A'
                    ];
                })->toArray()
            ];
            
            // Tambahkan data pemeriksaan awal jika ada - LENGKAP SEMUA FIELD
            if ($detailPemeriksaan && $detailPemeriksaan->pemeriksaanAwal) {
                $pemeriksaanAwal = $detailPemeriksaan->pemeriksaanAwal;
                $responseData['pemeriksaan_awal'] = [
                    // Tanda Vital
                    'suhu' => $formatValue($pemeriksaanAwal->suhu),
                    'nadi' => $formatValue($pemeriksaanAwal->nadi),
                    'tegangan' => $formatValue($pemeriksaanAwal->tegangan),
                    'pernapasan' => $formatValue($pemeriksaanAwal->pernapasan),
                    
                    // Detail Pemeriksaan
                    'pemeriksaan' => $formatValue($pemeriksaanAwal->pemeriksaan),
                    'keluhan_dahulu' => $formatValue($pemeriksaanAwal->keluhan_dahulu),
                    
                    // Pain Assessment
                    'tipe' => $formatValue($pemeriksaanAwal->tipe),
                    'status_nyeri' => $formatValue($pemeriksaanAwal->status_nyeri),
                    'karakteristik' => $formatValue($pemeriksaanAwal->karakteristik),
                    'lokasi' => $formatValue($pemeriksaanAwal->lokasi),
                    'durasi' => $formatValue($pemeriksaanAwal->durasi),
                    'frekuensi' => $formatValue($pemeriksaanAwal->frekuensi)
                ];
            }
            
            // Tambahkan data pemeriksaan fisik jika ada - LENGKAP SEMUA FIELD
            if ($detailPemeriksaan && $detailPemeriksaan->pemeriksaanFisik) {
                $pemeriksaanFisik = $detailPemeriksaan->pemeriksaanFisik;
                $responseData['pemeriksaan_fisik'] = [
                    // Antropometri
                    'tinggi_badan' => $formatValue($pemeriksaanFisik->tinggi_badan),
                    'berat_badan' => $formatValue($pemeriksaanFisik->berat_badan),
                    'lingkar_kepala' => $formatValue($pemeriksaanFisik->lingkar_kepala),
                    'lingkar_lengan_atas' => $formatValue($pemeriksaanFisik->lingkar_lengan_atas),
                    
                    // Pemeriksaan Organ
                    'dada' => $formatValue($pemeriksaanFisik->dada),
                    'jantung' => $formatValue($pemeriksaanFisik->jantung),
                    'paru' => $formatValue($pemeriksaanFisik->paru),
                    'perut' => $formatValue($pemeriksaanFisik->perut),
                    'hepar' => $formatValue($pemeriksaanFisik->hepar),
                    'anogenital' => $formatValue($pemeriksaanFisik->anogenital),
                    'ekstremitas' => $formatValue($pemeriksaanFisik->ekstremitas),
                    'kepala' => $formatValue($pemeriksaanFisik->kepala),
                    
                    // Pemeriksaan Lanjutan
                    'pemeriksaan_penunjang' => $formatValue($pemeriksaanFisik->pemeriksaan_penunjang),
                    'masalah_aktif' => $formatValue($pemeriksaanFisik->masalah_aktif),
                    'rencana_medis_dan_terapi' => $formatValue($pemeriksaanFisik->rencana_medis_dan_terapi)
                ];
                
                // Hitung BMI jika ada data tinggi dan berat badan
                if ($pemeriksaanFisik->tinggi_badan && $pemeriksaanFisik->berat_badan) {
                    $tinggi_m = $pemeriksaanFisik->tinggi_badan / 100;
                    $bmi = round($pemeriksaanFisik->berat_badan / ($tinggi_m * $tinggi_m), 1);
                    
                    // Kategori BMI berdasarkan WHO
                    if ($bmi < 18.5) {
                        $bmi_status = 'Underweight';
                        $bmi_color = 'blue';
                    } elseif ($bmi < 25) {
                        $bmi_status = 'Normal';
                        $bmi_color = 'green';
                    } elseif ($bmi < 30) {
                        $bmi_status = 'Overweight';
                        $bmi_color = 'yellow';
                    } else {
                        $bmi_status = 'Obese';
                        $bmi_color = 'red';
                    }
                    
                    $responseData['pemeriksaan_fisik']['bmi'] = $bmi;
                    $responseData['pemeriksaan_fisik']['bmi_status'] = $bmi_status;
                    $responseData['pemeriksaan_fisik']['bmi_color'] = $bmi_color;
                }
            }
            
            // Tambahkan informasi detail pemeriksaan jika ada
            if ($detailPemeriksaan) {
                $responseData['detail_pemeriksaan'] = [
                    'id_detprx' => $detailPemeriksaan->id_detprx,
                    'tanggal_jam' => Carbon::parse($detailPemeriksaan->tanggal_jam)->format('d F Y, H:i'),
                    'status_pemeriksaan' => $detailPemeriksaan->status_pemeriksaan,
                    'petugas_uks' => $detailPemeriksaan->petugasUks->nama_petugas_uks ?? 'N/A'
                ];
            }
            
            Log::info('API response prepared successfully', [
                'siswa_id' => $siswaId,
                'rekam_medis_id' => $rekamMedisId,
                'resep_count' => $resep->count(),
                'has_pemeriksaan_fisik' => !is_null($responseData['pemeriksaan_fisik']),
                'has_pemeriksaan_awal' => !is_null($responseData['pemeriksaan_awal']),
                'resep_ids' => $resep->pluck('Id_Resep')->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimuat',
                'data' => $responseData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getDetailPemeriksaan API: ' . $e->getMessage(), [
                'siswa_id' => $siswaId,
                'rekam_medis_id' => $rekamMedisId,
                'user_level' => session('user_level'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail pemeriksaan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Test API endpoint untuk debugging
     */
    public function testApi()
    {
        return response()->json([
            'success' => true,
            'message' => 'API endpoint working',
            'timestamp' => now()->toDateTimeString(),
            'session_data' => [
                'user_id' => session('user_id'),
                'user_level' => session('user_level'),
                'siswa_id' => session('siswa_id')
            ]
        ]);
    }

    /**
     * Get comprehensive screening report for a student - BONUS METHOD
     */
    public function getComprehensiveReport($siswaId)
    {
        try {
            $userLevel = session('user_level');
            
            // Check access permission
            if ($userLevel === 'orang_tua' && $siswaId != session('siswa_id')) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            
            // Get student data
            $siswa = Siswa::with(['detailSiswa.kelas.jurusan', 'orangTua'])->findOrFail($siswaId);
            
            // Get all screening records
            $allScreenings = DetailPemeriksaan::with([
                'dokter', 
                'petugasUks', 
                'pemeriksaanAwal', 
                'pemeriksaanFisik'
            ])->where('id_siswa', $siswaId)
              ->orderBy('tanggal_jam', 'desc')
              ->get();
            
            // Get all medical records
            $allRekamMedis = RekamMedis::with('dokter')
                ->where('Id_Siswa', $siswaId)
                ->orderBy('Tanggal_Jam', 'desc')
                ->get();
            
            // Get all prescriptions
            $allResep = Resep::with('dokter')
                ->where('Id_Siswa', $siswaId)
                ->orderBy('Tanggal_Resep', 'desc')
                ->get();
            
            // Calculate health statistics
            $healthStats = [
                'total_screenings' => $allScreenings->count(),
                'completed_screenings' => $allScreenings->where('status_pemeriksaan', 'lengkap')->count(),
                'total_medical_records' => $allRekamMedis->count(),
                'total_prescriptions' => $allResep->count(),
                'last_screening_date' => $allScreenings->first() ? 
                    Carbon::parse($allScreenings->first()->tanggal_jam)->format('d F Y') : null,
                'health_status' => $this->calculateHealthStatus($allScreenings, $allRekamMedis)
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'siswa' => $siswa,
                    'health_stats' => $healthStats,
                    'screenings' => $allScreenings,
                    'medical_records' => $allRekamMedis,
                    'prescriptions' => $allResep
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching comprehensive report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching comprehensive report: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate health status based on screening data
     */
    private function calculateHealthStatus($screenings, $rekamMedis)
    {
        $score = 100; // Start with perfect score
        
        // Deduct points for incomplete screenings
        $totalScreenings = $screenings->count();
        $completedScreenings = $screenings->where('status_pemeriksaan', 'lengkap')->count();
        
        if ($totalScreenings > 0) {
            $completionRate = ($completedScreenings / $totalScreenings) * 100;
            if ($completionRate < 80) {
                $score -= (80 - $completionRate) * 0.5;
            }
        }
        
        // Check for recent medical issues
        $recentIssues = $rekamMedis->filter(function($record) {
            return Carbon::parse($record->Tanggal_Jam)->diffInDays(Carbon::now()) <= 30;
        });
        
        if ($recentIssues->count() > 2) {
            $score -= ($recentIssues->count() - 2) * 10;
        }
        
        // Determine status
        if ($score >= 90) {
            return ['status' => 'Excellent', 'color' => 'green', 'score' => round($score)];
        } elseif ($score >= 75) {
            return ['status' => 'Good', 'color' => 'blue', 'score' => round($score)];
        } elseif ($score >= 60) {
            return ['status' => 'Fair', 'color' => 'yellow', 'score' => round($score)];
        } else {
            return ['status' => 'Needs Attention', 'color' => 'red', 'score' => round($score)];
        }
    }
}