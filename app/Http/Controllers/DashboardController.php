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

        $nip = session('user_nip');
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
            'userName' => session('nama_petugas_uks'),
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
            'userName' => session('nama_dokter'),
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
            'namaOrangTua' => session('nama_orang_tua'),
            
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
        $pemeriksaanHarian = PemeriksaanHarian::with(['siswa'])
            ->select('Id_Harian as id', 'Tanggal_Jam as tanggal_jam', 'Id_Siswa', 'Hasil_Pemeriksaan as hasil', DB::raw("'Harian' as jenis"))
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(3);

        $detailPemeriksaan = DetailPemeriksaan::with(['siswa', 'dokter'])
            ->select('id_detprx as id', 'tanggal_jam', 'id_siswa', 'status_pemeriksaan as hasil', DB::raw("'Detail' as jenis"))
            ->orderBy('tanggal_jam', 'desc')
            ->take(3);

        return $pemeriksaanHarian->union($detailPemeriksaan)
            ->orderBy('tanggal_jam', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal_jam,
                    'siswa' => $item->siswa,
                    'dokter' => $item->dokter ?? (object)['Nama_Dokter' => 'Petugas UKS'],
                    'hasil' => $item->hasil,
                    'jenis' => $item->jenis
                ];
            });
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

    private function getPemeriksaanTerbarePetugas($nip)
    {
        return PemeriksaanHarian::with(['siswa'])
            ->where('NIP', $nip)
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->Id_Harian,
                    'tanggal' => $item->Tanggal_Jam,
                    'siswa' => $item->siswa,
                    'hasil' => $item->Hasil_Pemeriksaan,
                ];
            });
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
}