<?php

namespace App\Http\Controllers;

use App\Models\DetailPemeriksaan;
use App\Models\Siswa;
use App\Models\Dokter;
use App\Models\PetugasUKS;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetailPemeriksaanController extends Controller
{
    /**
     * Menampilkan daftar detail pemeriksaan
     */
    public function index(Request $request)
    {
        $query = DetailPemeriksaan::with(['siswa', 'dokter', 'petugasUKS']);
        
        // Pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_siswa', 'like', "%{$search}%")
                  ->orWhere('id_siswa', 'like', "%{$search}%");
            })->orWhereHas('dokter', function($q) use ($search) {
                $q->where('nama_dokter', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan status pemeriksaan
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status_pemeriksaan', $request->status);
        }
        
        // Filter tanggal
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('tanggal_jam', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('tanggal_jam', '<=', $request->date_to);
        }
        
        // Hitung statistik
        $totalHariIni = DetailPemeriksaan::whereDate('tanggal_jam', Carbon::today())->count();
        $totalSiswaTerlayani = DetailPemeriksaan::distinct('id_siswa')->count('id_siswa');
        $totalBelumLengkap = DetailPemeriksaan::where('status_pemeriksaan', 'belum lengkap')->count();
        $totalLengkap = DetailPemeriksaan::where('status_pemeriksaan', 'lengkap')->count();
        
        // Jalankan query dengan pagination
        $detailPemeriksaans = $query->latest('tanggal_jam')->paginate(10);
        
        return view('detail_pemeriksaan.index', compact(
            'detailPemeriksaans', 
            'totalHariIni', 
            'totalSiswaTerlayani',
            'totalBelumLengkap',
            'totalLengkap'
        ));
    }

    /**
     * Menampilkan form untuk membuat detail pemeriksaan baru
     */
    public function create()
    {
        $siswas = Siswa::where('status_aktif', 1)->get();
        $dokters = Dokter::all();
        $petugasUKS = PetugasUKS::all();
        
        return view('detail_pemeriksaan.create', compact('siswas', 'dokters', 'petugasUKS'));
    }

    /**
     * Menyimpan detail pemeriksaan baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'id_dokter' => 'required|exists:dokters,id_dokter',
            'nip' => 'required|exists:petugas_uks,nip',
            'tanggal_jam' => 'required|date',
            'status_pemeriksaan' => 'required|in:belum lengkap,lengkap'
        ]);

        try {
            DB::beginTransaction();

            // Model sudah handle auto-generate ID, tidak perlu manual
            $detailPemeriksaan = DetailPemeriksaan::create([
                'tanggal_jam' => $request->tanggal_jam ?? now(),
                'id_siswa' => $request->id_siswa,
                'status_pemeriksaan' => $request->status_pemeriksaan ?? 'belum lengkap',
                'id_dokter' => $request->id_dokter,
                'nip' => $request->nip,
            ]);

            DB::commit();

            return redirect()->route('detail_pemeriksaan.index')
                ->with('success', 'Detail pemeriksaan berhasil dibuat dengan ID: ' . $detailPemeriksaan->id_detprx);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan detail pemeriksaan: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail dari pemeriksaan tertentu
     */
    public function show(string $detail_pemeriksaan)
    {
        $detailPemeriksaan = DetailPemeriksaan::with([
                'siswa', 
                'dokter', 
                'petugasUKS', 
                'pemeriksaanFisik', 
                'pemeriksaanAwal'
            ])
            ->where('id_detprx', $detail_pemeriksaan)
            ->firstOrFail();
            
        return view('detail_pemeriksaan.show', compact('detailPemeriksaan'));
    }

    /**
     * Menampilkan form untuk mengedit detail pemeriksaan
     */
    public function edit(string $detail_pemeriksaan)
    {
        $detailPemeriksaan = DetailPemeriksaan::where('id_detprx', $detail_pemeriksaan)->firstOrFail();
        $siswas = Siswa::where('status_aktif', 1)->get();
        $dokters = Dokter::all();
        $petugasUKS = PetugasUKS::all();
        
        return view('detail_pemeriksaan.edit', compact('detailPemeriksaan', 'siswas', 'dokters', 'petugasUKS'));
    }

    /**
     * Mengupdate detail pemeriksaan di database
     */
    public function update(Request $request, string $detail_pemeriksaan)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'id_dokter' => 'required|exists:dokters,id_dokter',
            'nip' => 'required|exists:petugas_uks,nip',
            'tanggal_jam' => 'required|date',
            'status_pemeriksaan' => 'required|in:belum lengkap,lengkap'
        ]);

        try {
            DB::beginTransaction();

            $detailPemeriksaan = DetailPemeriksaan::where('id_detprx', $detail_pemeriksaan)->firstOrFail();
            
            $detailPemeriksaan->update([
                'tanggal_jam' => $request->tanggal_jam,
                'id_siswa' => $request->id_siswa,
                'status_pemeriksaan' => $request->status_pemeriksaan,
                'id_dokter' => $request->id_dokter,
                'nip' => $request->nip,
            ]);

            DB::commit();

            return redirect()->route('detail_pemeriksaan.index')
                ->with('success', 'Detail pemeriksaan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus detail pemeriksaan dari database
     */
    public function destroy(string $detail_pemeriksaan)
    {
        try {
            DB::beginTransaction();

            $detailPemeriksaan = DetailPemeriksaan::where('id_detprx', $detail_pemeriksaan)->firstOrFail();
            $detailPemeriksaan->delete();

            DB::commit();

            return redirect()->route('detail_pemeriksaan.index')
                ->with('success', 'Detail pemeriksaan berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status pemeriksaan
     */
    public function updateStatus(Request $request, string $detail_pemeriksaan)
    {
        $request->validate([
            'status_pemeriksaan' => 'required|in:belum lengkap,lengkap'
        ]);

        try {
            $detailPemeriksaan = DetailPemeriksaan::where('id_detprx', $detail_pemeriksaan)->firstOrFail();
            $detailPemeriksaan->update(['status_pemeriksaan' => $request->status_pemeriksaan]);

            return response()->json([
                'success' => true,
                'message' => 'Status pemeriksaan berhasil diperbarui',
                'status' => $detailPemeriksaan->status_pemeriksaan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghasilkan detail pemeriksaan secara otomatis 
     * Method ini dipanggil dari RekamMedisController
     */
    public function autoGenerateDetailPemeriksaan($idSiswa, $idDokter, $nip, $tanggalJam = null)
    {
        try {
            Log::info("autoGenerateDetailPemeriksaan dipanggil", [
                'idSiswa' => $idSiswa,
                'idDokter' => $idDokter, 
                'nip' => $nip,
                'tanggalJam' => $tanggalJam
            ]);

            // Cek apakah semua ID yang diperlukan ada
            $siswa = Siswa::where('id_siswa', $idSiswa)->first();
            $dokter = Dokter::where('id_dokter', $idDokter)->first();
            $petugasUKS = PetugasUKS::where('nip', $nip)->first();
            
            if (!$siswa || !$dokter || !$petugasUKS) {
                Log::error("Data tidak ditemukan", [
                    'siswa' => $siswa ? 'found' : 'not found',
                    'dokter' => $dokter ? 'found' : 'not found',
                    'petugasUKS' => $petugasUKS ? 'found' : 'not found'
                ]);
                return null;
            }
            
            // Model sudah handle auto-generate ID, tidak perlu manual
            $detailPemeriksaan = DetailPemeriksaan::create([
                'tanggal_jam' => $tanggalJam ?? now(),
                'id_siswa' => $idSiswa,
                'status_pemeriksaan' => 'belum lengkap',
                'id_dokter' => $idDokter,
                'nip' => $nip,
            ]);
            
            Log::info("Detail pemeriksaan berhasil dibuat otomatis: " . $detailPemeriksaan->id_detprx);
            
            return $detailPemeriksaan;

        } catch (\Exception $e) {
            Log::error('Error auto generating detail pemeriksaan: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * API endpoint untuk mendapatkan statistik
     */
    public function getStatistics()
    {
        $statistics = [
            'total_hari_ini' => DetailPemeriksaan::whereDate('tanggal_jam', Carbon::today())->count(),
            'total_siswa_terlayani' => DetailPemeriksaan::distinct('id_siswa')->count('id_siswa'),
            'total_belum_lengkap' => DetailPemeriksaan::where('status_pemeriksaan', 'belum lengkap')->count(),
            'total_lengkap' => DetailPemeriksaan::where('status_pemeriksaan', 'lengkap')->count(),
            'pemeriksaan_per_bulan' => DetailPemeriksaan::selectRaw('MONTH(tanggal_jam) as bulan, COUNT(*) as total')
                ->whereYear('tanggal_jam', Carbon::now()->year)
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get()
        ];

        return response()->json($statistics);
    }
}