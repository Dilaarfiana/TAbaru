<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\OrangTua;
use App\Models\RekamMedis;
use App\Models\DetailPemeriksaan;
use App\Models\PemeriksaanHarian;
use App\Models\Resep;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik untuk kartu
        $data = [
            'totalSiswa' => Siswa::count(),
            'siswaAktif' => Siswa::where('Status_Aktif', 1)->count(),
            'siswaTidakAktif' => Siswa::where('Status_Aktif', 0)->count(),
            'totalDokter' => Dokter::count(),
            'totalJurusan' => Jurusan::count(),
            'totalOrangTua' => OrangTua::count(),
            'totalRekamMedis' => RekamMedis::count(),
            'totalPemeriksaan' => DetailPemeriksaan::count() + PemeriksaanHarian::count(),
            
            // Data untuk tabel & grafik
            'pemeriksaanTerbaru' => $this->getPemeriksaanTerbaru(),
            'resepTerbaru' => $this->getResepTerbaru(),
            'chartData' => $this->getChartData(),
            
            // Siswa & dokter terbaru
            'siswaTerbaru' => Siswa::orderBy('created_at', 'desc')->take(5)->get(),
            'dokterList' => Dokter::orderBy('Nama_Dokter')->take(5)->get(),
        ];
        
        return view('dashboard', $data);
    }
    
    private function getPemeriksaanTerbaru()
    {
        // Gabungkan data pemeriksaan harian dan detail pemeriksaan
        $pemeriksaanHarian = PemeriksaanHarian::with(['siswa', 'dokter'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->Tanggal_Jam,
                    'siswa' => $item->siswa,
                    'dokter' => $item->dokter,
                    'hasil' => $item->Hasil_Pemeriksaan,
                    'jenis' => 'Harian',
                    'id' => $item->Id_Harian
                ];
            });
            
        $detailPemeriksaan = DetailPemeriksaan::with(['siswa', 'dokter'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => $item->Tanggal_Jam,
                    'siswa' => $item->siswa,
                    'dokter' => $item->dokter,
                    'hasil' => $item->Hasil_Pemeriksaan,
                    'jenis' => 'Detail',
                    'id' => $item->Id_DetPrx
                ];
            });
            
        // Gabungkan dan sorting berdasarkan tanggal terbaru
        return $pemeriksaanHarian->concat($detailPemeriksaan)
            ->sortByDesc('tanggal')
            ->take(3);
    }
    
    private function getResepTerbaru()
    {
        return Resep::with(['siswa', 'dokter'])
            ->orderBy('Tanggal_Resep', 'desc')
            ->take(3)
            ->get();
    }
    
    private function getChartData()
    {
        // Mendapatkan data pemeriksaan per bulan untuk tahun ini
        $currentYear = date('Y');
        $data = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthName = date('M', mktime(0, 0, 0, $month, 1));
            
            // Hitung jumlah pemeriksaan untuk setiap bulan
            $pemeriksaanAwal = RekamMedis::whereYear('Tanggal_Jam', $currentYear)
                ->whereMonth('Tanggal_Jam', $month)
                ->count();
                
            $pemeriksaanFisik = DetailPemeriksaan::whereYear('Tanggal_Jam', $currentYear)
                ->whereMonth('Tanggal_Jam', $month)
                ->count();
                
            $pemeriksaanHarian = PemeriksaanHarian::whereYear('Tanggal_Jam', $currentYear)
                ->whereMonth('Tanggal_Jam', $month)
                ->count();
                
            $data[] = [
                'bulan' => $monthName,
                'pemeriksaanAwal' => $pemeriksaanAwal,
                'pemeriksaanFisik' => $pemeriksaanFisik,
                'pemeriksaanHarian' => $pemeriksaanHarian,
            ];
        }
        
        return $data;
    }
}