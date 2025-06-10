<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\OrangTua;
use App\Models\RekamMedis;
use App\Models\DetailPemeriksaan;
use App\Models\PemeriksaanAwal;
use App\Models\PemeriksaanFisik;
use App\Models\PemeriksaanHarian;
use App\Models\Resep;
use App\Models\PetugasUKS;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userLevel = session('user_level');
        
        // Redirect based on user level
        switch ($userLevel) {
            case 'admin':
                return $this->admin();
            case 'petugas':
                return $this->petugas();
            case 'dokter':
                return $this->dokter();
            case 'orang_tua':
                return $this->orangTua();
            default:
                return redirect()->route('login')->with('error', 'Akses tidak valid');
        }
    }

    /**
     * Dashboard Admin - Full Access
     */
    public function admin()
    {
        // Verify admin access
        if (session('user_level') !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak');
        }

        $data = [
            // Basic Stats
            'totalSiswa' => Siswa::count(),
            'siswaAktif' => Siswa::where('status_aktif', 1)->count(),
            'siswaTidakAktif' => Siswa::where('status_aktif', 0)->count(),
            'totalDokter' => Dokter::where('status_aktif', 1)->count(),
            'totalOrangTua' => OrangTua::count(),
            'totalRekamMedis' => RekamMedis::count(),
            'totalPemeriksaan' => $this->getTotalPemeriksaan(),
            
            // Data for tables & charts
            'pemeriksaanTerbaru' => $this->getPemeriksaanTerbaru(),
            'resepTerbaru' => $this->getResepTerbaru(),
            'chartData' => $this->getAdminChartData(),
            
            // Additional admin data
            'userRole' => 'admin',
            'totalPetugasUKS' => PetugasUKS::where('status_aktif', 1)->count(),
            'totalResep' => Resep::count(),
        ];

        return view('dashboard.admin', $data);
    }

    /**
     * Dashboard Petugas - CRU Access (No Delete)
     */
    public function petugas()
    {
        // Verify petugas access
        if (session('user_level') !== 'petugas') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak');
        }

        $nip = session('user_nip') ?? session('NIP') ?? session('user_id');
        $today = Carbon::today();

        $data = [
            // Basic Stats
            'totalSiswa' => Siswa::count(),
            'siswaAktif' => Siswa::where('status_aktif', 1)->count(),
            'siswaTidakAktif' => Siswa::where('status_aktif', 0)->count(),
            'totalRekamMedis' => RekamMedis::count(),
            'totalResep' => Resep::count(),
            
            // Petugas specific data
            'pemeriksaanHariIni' => PemeriksaanHarian::whereDate('Tanggal_Jam', $today)->count(),
            'pemeriksaanTerbaru' => $this->getPemeriksaanTerbarePetugas($nip),
            'chartData' => $this->getPetugasChartData(),
            
            // Role info
            'userRole' => 'petugas',
            'userName' => session('nama_petugas_uks') ?? session('user_name'),
        ];

        return view('dashboard.petugas', $data);
    }

    /**
     * Dashboard Dokter - Read Only Access
     */
    public function dokter()
    {
        // Verify dokter access
        if (session('user_level') !== 'dokter') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak');
        }

        $dokterId = session('user_id');
        $today = Carbon::today();

        $data = [
            // Basic Stats
            'totalSiswa' => Siswa::count(),
            'siswaAktif' => Siswa::where('status_aktif', 1)->count(),
            'siswaTidakAktif' => Siswa::where('status_aktif', 0)->count(),
            'totalRekamMedis' => RekamMedis::count(),
            'totalPemeriksaan' => $this->getTotalPemeriksaan(),
            'totalResep' => Resep::count(),
            
            // Dokter specific data
            'pemeriksaanHariIni' => $this->getPemeriksaanHariIni(),
            'rekamMedisTerbaru' => $this->getRekamMedisTerbaru(),
            'resepAktif' => Resep::whereDate('Tanggal_Resep', '>=', $today->subDays(30))->count(),
            'chartData' => $this->getDokterChartData(),
            
            // Role info
            'userRole' => 'dokter',
            'userName' => session('nama_dokter') ?? session('user_name'),
        ];

        return view('dashboard.dokter', $data);
    }

    /**
     * Dashboard Orang Tua - Limited Access to Own Child Data
     */
    public function orangTua()
    {
        // Verify orang tua access
        if (session('user_level') !== 'orang_tua') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak');
        }

        $siswaId = session('siswa_id');
        $orangTuaId = session('user_id');

        // Check if siswa data exists
        if (!$siswaId) {
            $data = [
                'siswaId' => null,
                'siswaData' => null,
                'totalRekamMedis' => 0,
                'totalResep' => 0,
                'pemeriksaanTerakhir' => null,
                'riwayatKesehatan' => [],
                'aktivitasTerbaru' => [],
                'userRole' => 'orang_tua',
            ];
            return view('dashboard.orangtua', $data);
        }

        // Get child data
        $siswaData = Siswa::with(['detailSiswa.kelas'])->find($siswaId);
        
        $data = [
            // Basic child info
            'siswaId' => $siswaId,
            'siswaData' => $siswaData,
            'namaAnak' => $siswaData->nama_siswa ?? 'Anak Anda',
            'namaOrangTua' => session('nama_orang_tua') ?? session('user_name'),
            
            // Child's health stats
            'totalRekamMedis' => RekamMedis::where('Id_Siswa', $siswaId)->count(),
            'totalResep' => Resep::where('Id_Siswa', $siswaId)->count(),
            'pemeriksaanTerakhir' => $this->getPemeriksaanTerakhirAnak($siswaId),
            
            // Child's health history
            'riwayatKesehatan' => $this->getRiwayatKesehatanAnak($siswaId),
            'aktivitasTerbaru' => $this->getAktivitasTerbaruAnak($siswaId),
            
            // Role info
            'userRole' => 'orang_tua',
        ];

        return view('dashboard.orangtua', $data);
    }

    /**
     * Private helper methods
     */
    private function getTotalPemeriksaan()
    {
        return PemeriksaanAwal::count() + 
               PemeriksaanFisik::count() + 
               PemeriksaanHarian::count();
    }

    private function getPemeriksaanTerbaru()
    {
        $pemeriksaanTerbaru = collect();

        // Get Pemeriksaan Harian
        $pemeriksaanHarian = PemeriksaanHarian::with(['siswa', 'petugasUks'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->Id_Harian,
                    'tanggal' => $item->Tanggal_Jam,
                    'siswa' => (object)[
                        'id_siswa' => $item->Id_Siswa,
                        'nama_siswa' => $item->siswa->nama_siswa ?? 'Tidak diketahui'
                    ],
                    'dokter' => (object)[
                        'Nama_Dokter' => $item->petugasUks->nama_petugas_uks ?? 'Petugas UKS'
                    ],
                    'hasil' => $item->Hasil_Pemeriksaan ?? 'Pemeriksaan Harian',
                    'jenis' => 'Harian'
                ];
            });

        // Get Detail Pemeriksaan
        $detailPemeriksaan = DetailPemeriksaan::with(['siswa', 'dokter', 'petugasUks'])
            ->orderBy('tanggal_jam', 'desc')
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id_detprx,
                    'tanggal' => $item->tanggal_jam,
                    'siswa' => (object)[
                        'id_siswa' => $item->id_siswa,
                        'nama_siswa' => $item->siswa->nama_siswa ?? 'Tidak diketahui'
                    ],
                    'dokter' => (object)[
                        'Nama_Dokter' => $item->dokter->Nama_Dokter ?? ($item->petugasUks->nama_petugas_uks ?? 'Tidak diketahui')
                    ],
                    'hasil' => ucfirst($item->status_pemeriksaan) ?? 'Pemeriksaan Detail',
                    'jenis' => 'Detail'
                ];
            });

        // Get Rekam Medis
        $rekamMedis = RekamMedis::with(['siswa', 'dokter'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(2)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->No_Rekam_Medis,
                    'tanggal' => $item->Tanggal_Jam,
                    'siswa' => (object)[
                        'id_siswa' => $item->Id_Siswa,
                        'nama_siswa' => $item->siswa->nama_siswa ?? 'Tidak diketahui'
                    ],
                    'dokter' => (object)[
                        'Nama_Dokter' => $item->dokter->Nama_Dokter ?? 'Tidak diketahui'
                    ],
                    'hasil' => \Illuminate\Support\Str::limit($item->Keluhan_Utama, 30) ?? 'Rekam Medis',
                    'jenis' => 'Rekam Medis'
                ];
            });

        // Combine and sort by date
        $pemeriksaanTerbaru = $pemeriksaanHarian
            ->concat($detailPemeriksaan)
            ->concat($rekamMedis)
            ->sortByDesc('tanggal')
            ->take(5)
            ->values();

        return $pemeriksaanTerbaru;
    }

    private function getResepTerbaru()
    {
        return Resep::with(['siswa', 'dokter'])
            ->orderBy('Tanggal_Resep', 'desc')
            ->take(5)
            ->get();
    }

    private function getAdminChartData()
    {
        $currentYear = date('Y');
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($currentYear, $month, 1)->format('M');
            
            $pemeriksaanAwal = PemeriksaanAwal::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
                
            $pemeriksaanFisik = PemeriksaanFisik::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->count();
                
            $pemeriksaanHarian = PemeriksaanHarian::whereYear('Tanggal_Jam', $currentYear)
                ->whereMonth('Tanggal_Jam', $month)
                ->count();

            $resep = Resep::whereYear('Tanggal_Resep', $currentYear)
                ->whereMonth('Tanggal_Resep', $month)
                ->count();
                
            $data[] = [
                'bulan' => $monthName,
                'pemeriksaanAwal' => $pemeriksaanAwal,
                'pemeriksaanFisik' => $pemeriksaanFisik,
                'pemeriksaanHarian' => $pemeriksaanHarian,
                'resep' => $resep,
            ];
        }

        return $data;
    }

    private function getPetugasChartData()
    {
        $currentYear = date('Y');
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($currentYear, $month, 1)->format('M');
            
            $pemeriksaanHarian = PemeriksaanHarian::whereYear('Tanggal_Jam', $currentYear)
                ->whereMonth('Tanggal_Jam', $month)
                ->count();

            $resep = Resep::whereYear('Tanggal_Resep', $currentYear)
                ->whereMonth('Tanggal_Resep', $month)
                ->count();
                
            $data[] = [
                'bulan' => $monthName,
                'pemeriksaanHarian' => $pemeriksaanHarian,
                'resep' => $resep,
            ];
        }

        return $data;
    }

    private function getDokterChartData()
    {
        $currentYear = date('Y');
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($currentYear, $month, 1)->format('M');
            
            $rekamMedis = RekamMedis::whereYear('Tanggal_Jam', $currentYear)
                ->whereMonth('Tanggal_Jam', $month)
                ->count();

            $resep = Resep::whereYear('Tanggal_Resep', $currentYear)
                ->whereMonth('Tanggal_Resep', $month)
                ->count();
                
            $data[] = [
                'bulan' => $monthName,
                'rekamMedis' => $rekamMedis,
                'resep' => $resep,
            ];
        }

        return $data;
    }

    /**
     * FIXED: Method untuk mendapatkan pemeriksaan terbaru khusus petugas
     */
    private function getPemeriksaanTerbarePetugas($nip)
    {
        try {
            // Ambil data pemeriksaan harian terbaru
            $pemeriksaanHarian = PemeriksaanHarian::with(['siswa'])
                ->orderBy('Tanggal_Jam', 'desc')
                ->take(5)
                ->get();

            // Jika ada data, format sesuai kebutuhan view
            if ($pemeriksaanHarian->isNotEmpty()) {
                return $pemeriksaanHarian->map(function($item) {
                    return [
                        'id' => $item->Id_Harian,
                        'tanggal' => $item->Tanggal_Jam,
                        'siswa' => (object)[
                            'id_siswa' => $item->Id_Siswa,
                            'nama_siswa' => $item->siswa->nama_siswa ?? 'Tidak diketahui'
                        ],
                        'hasil' => $item->Hasil_Pemeriksaan ?? 'Pemeriksaan Harian',
                        'jenis' => 'Harian'
                    ];
                });
            }

            // Jika tidak ada data pemeriksaan harian, ambil dari rekam medis terbaru
            $rekamMedis = RekamMedis::with(['siswa', 'dokter'])
                ->orderBy('Tanggal_Jam', 'desc')
                ->take(5)
                ->get();

            return $rekamMedis->map(function($item) {
                return [
                    'id' => $item->No_Rekam_Medis,
                    'tanggal' => $item->Tanggal_Jam,
                    'siswa' => (object)[
                        'id_siswa' => $item->Id_Siswa,
                        'nama_siswa' => $item->siswa->nama_siswa ?? 'Tidak diketahui'
                    ],
                    'hasil' => \Illuminate\Support\Str::limit($item->Keluhan_Utama, 30) ?? 'Rekam Medis',
                    'jenis' => 'Rekam Medis'
                ];
            });

        } catch (\Exception $e) {
            // Log error dan return empty collection
            \Log::error('Error getting pemeriksaan terbaru petugas: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getPemeriksaanHariIni()
    {
        return PemeriksaanHarian::whereDate('Tanggal_Jam', Carbon::today())->count() +
               DetailPemeriksaan::whereDate('tanggal_jam', Carbon::today())->count();
    }

    private function getRekamMedisTerbaru()
    {
        return RekamMedis::with(['siswa', 'dokter'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(5)
            ->get();
    }

    private function getPemeriksaanTerakhirAnak($siswaId)
    {
        $pemeriksaanHarian = PemeriksaanHarian::where('Id_Siswa', $siswaId)
            ->orderBy('Tanggal_Jam', 'desc')
            ->first();

        $rekamMedis = RekamMedis::where('Id_Siswa', $siswaId)
            ->orderBy('Tanggal_Jam', 'desc')
            ->first();

        // Return the most recent one
        if (!$pemeriksaanHarian && !$rekamMedis) {
            return null;
        }

        if (!$pemeriksaanHarian) {
            return $rekamMedis;
        }

        if (!$rekamMedis) {
            return $pemeriksaanHarian;
        }

        return $pemeriksaanHarian->Tanggal_Jam > $rekamMedis->Tanggal_Jam 
            ? $pemeriksaanHarian 
            : $rekamMedis;
    }

    private function getRiwayatKesehatanAnak($siswaId)
    {
        $riwayat = [];

        // Get recent medical records
        $rekamMedis = RekamMedis::where('Id_Siswa', $siswaId)
            ->with('dokter')
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(3)
            ->get();

        foreach ($rekamMedis as $rekam) {
            $riwayat[] = [
                'jenis' => 'Rekam Medis',
                'tanggal' => $rekam->Tanggal_Jam,
                'keterangan' => $rekam->Keluhan_Utama,
                'dokter' => $rekam->dokter->Nama_Dokter ?? 'Tidak diketahui'
            ];
        }

        // Get recent prescriptions
        $resep = Resep::where('Id_Siswa', $siswaId)
            ->with('dokter')
            ->orderBy('Tanggal_Resep', 'desc')
            ->take(2)
            ->get();

        foreach ($resep as $r) {
            $riwayat[] = [
                'jenis' => 'Resep Obat',
                'tanggal' => $r->Tanggal_Resep,
                'keterangan' => $r->Nama_Obat . ' - ' . $r->Dosis,
                'dokter' => $r->dokter->Nama_Dokter ?? 'Tidak diketahui'
            ];
        }

        // Sort by date descending
        usort($riwayat, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        return array_slice($riwayat, 0, 5);
    }

    private function getAktivitasTerbaruAnak($siswaId)
    {
        $aktivitas = [];

        // Recent health checkups
        $pemeriksaan = PemeriksaanHarian::where('Id_Siswa', $siswaId)
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(3)
            ->get();

        foreach ($pemeriksaan as $p) {
            $aktivitas[] = [
                'judul' => 'Pemeriksaan Kesehatan',
                'deskripsi' => $p->Hasil_Pemeriksaan,
                'tanggal' => $p->Tanggal_Jam,
                'status' => 'Selesai'
            ];
        }

        // Recent prescriptions
        $resep = Resep::where('Id_Siswa', $siswaId)
            ->orderBy('Tanggal_Resep', 'desc')
            ->take(2)
            ->get();

        foreach ($resep as $r) {
            $aktivitas[] = [
                'judul' => 'Resep Obat Baru',
                'deskripsi' => 'Diberikan resep ' . $r->Nama_Obat,
                'tanggal' => $r->Tanggal_Resep,
                'status' => 'Aktif'
            ];
        }

        // Sort by date descending
        usort($aktivitas, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        return array_slice($aktivitas, 0, 5);
    }

    /**
     * API Methods for real-time updates (if needed)
     */
    public function getStats(Request $request)
    {
        $userLevel = session('user_level');
        
        switch ($userLevel) {
            case 'admin':
                return response()->json([
                    'totalSiswa' => Siswa::count(),
                    'totalDokter' => Dokter::where('status_aktif', 1)->count(),
                    'totalPemeriksaan' => $this->getTotalPemeriksaan(),
                    'totalResep' => Resep::count(),
                ]);
                
            case 'petugas':
                return response()->json([
                    'pemeriksaanHariIni' => PemeriksaanHarian::whereDate('Tanggal_Jam', Carbon::today())->count(),
                    'totalResep' => Resep::count(),
                ]);
                
            case 'dokter':
                return response()->json([
                    'totalRekamMedis' => RekamMedis::count(),
                    'pemeriksaanHariIni' => $this->getPemeriksaanHariIni(),
                ]);
                
            case 'orang_tua':
                $siswaId = session('siswa_id');
                return response()->json([
                    'totalRekamMedis' => RekamMedis::where('Id_Siswa', $siswaId)->count(),
                    'totalResep' => Resep::where('Id_Siswa', $siswaId)->count(),
                ]);
                
            default:
                return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Method untuk debugging - bisa dihapus setelah selesai testing
     */
    public function debugPemeriksaan()
    {
        $data = [];
        
        // Check tables count
        $data['counts'] = [
            'pemeriksaan_harian' => PemeriksaanHarian::count(),
            'rekam_medis' => RekamMedis::count(),
            'detail_pemeriksaan' => DetailPemeriksaan::count(),
            'siswa' => Siswa::count(),
        ];
        
        // Get sample data
        $data['sample_pemeriksaan_harian'] = PemeriksaanHarian::with('siswa')->take(3)->get();
        $data['sample_rekam_medis'] = RekamMedis::with('siswa')->take(3)->get();
        
        return response()->json($data);
    }
}