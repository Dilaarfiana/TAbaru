<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\Siswa;
use App\Models\Dokter;
use App\Models\DetailPemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekamMedisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RekamMedis::with(['siswa', 'dokter']);

        // Filter berdasarkan siswa
        if ($request->filled('siswa')) {
            $query->where('Id_Siswa', $request->siswa);
        }

        // Filter berdasarkan dokter
        if ($request->filled('dokter')) {
            $query->where('Id_Dokter', $request->dokter);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('Tanggal_Jam', $request->tanggal);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('Nama_Siswa', 'like', '%' . $request->search . '%');
            })
            ->orWhere('No_Rekam_Medis', 'like', '%' . $request->search . '%')
            ->orWhere('Keluhan_Utama', 'like', '%' . $request->search . '%');
        }

        $rekamMedis = $query->orderBy('Tanggal_Jam', 'desc')->paginate(10);
        $siswas = Siswa::orderBy('Nama_Siswa')->get();
        $dokters = Dokter::orderBy('Nama_Dokter')->get();

        $totalRekamMedis = RekamMedis::count();
        $totalRekamMedisBulanIni = RekamMedis::whereMonth('Tanggal_Jam', now()->month)
            ->whereYear('Tanggal_Jam', now()->year)
            ->count();

        return view('rekam_medis.index', compact(
            'rekamMedis',
            'siswas',
            'dokters',
            'totalRekamMedis',
            'totalRekamMedisBulanIni'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $siswas = Siswa::orderBy('Nama_Siswa')->get();
        $dokters = Dokter::orderBy('Nama_Dokter')->get();
        
        return view('rekam_medis.create', compact('siswas', 'dokters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $request->validate([
        'Id_Siswa' => 'required|exists:siswas,id_siswa',
        'Id_Dokter' => 'required|exists:dokters,Id_Dokter',
        'Tanggal_Jam' => 'required|date',
        'Keluhan_Utama' => 'required|string',
    ]);
    
    // Generate ID untuk Rekam Medis
    $lastId = DB::table('rekam_medis')->max('No_Rekam_Medis');
    
    if (!$lastId) {
        // Jika belum ada data, gunakan RM001
        $newRecId = 'RM001';
    } else {
        // Jika sudah ada data, increment nomor terakhir
        $newIdNumber = intval(substr($lastId, 2)) + 1;
        $newRecId = 'RM' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
    }
    
    // Mulai transaksi database
    DB::beginTransaction();
    
    try {
        // Buat rekam medis
        RekamMedis::create([
            'No_Rekam_Medis' => $newRecId,
            'Id_Siswa' => $request->Id_Siswa,
            'Id_Dokter' => $request->Id_Dokter,
            'Tanggal_Jam' => $request->Tanggal_Jam,
            'Keluhan_Utama' => $request->Keluhan_Utama,
            'Riwayat_Penyakit_Sekarang' => $request->Riwayat_Penyakit_Sekarang,
            'Riwayat_Penyakit_Dahulu' => $request->Riwayat_Penyakit_Dahulu,
            'Riwayat_Imunisasi' => $request->Riwayat_Imunisasi,
            'Riwayat_Penyakit_Keluarga' => $request->Riwayat_Penyakit_Keluarga,
            'Silsilah_Keluarga' => $request->Silsilah_Keluarga
        ]);
        
        // Commit transaksi
        DB::commit();
        
        return redirect()->route('rekam_medis.index')->with('success', 'Rekam medis berhasil ditambahkan.');
        
    } catch (\Exception $e) {
        // Rollback transaksi jika terjadi error
        DB::rollback();
        return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rekamMedis = RekamMedis::with(['siswa', 'dokter'])->findOrFail($id);
        $detailPemeriksaan = DetailPemeriksaan::where('Id_Siswa', $rekamMedis->Id_Siswa)
            ->orderBy('Tanggal_Jam', 'desc')
            ->get();
            
        return view('rekam_medis.show', compact('rekamMedis', 'detailPemeriksaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $siswas = Siswa::orderBy('Nama_Siswa')->get();
        $dokters = Dokter::orderBy('Nama_Dokter')->get();
        
        return view('rekam_medis.edit', compact('rekamMedis', 'siswas', 'dokters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'Id_Dokter' => 'required|exists:dokters,Id_Dokter',
            'Tanggal_Jam' => 'required|date',
            'Keluhan_Utama' => 'required|string',
        ]);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            $rekamMedis = RekamMedis::findOrFail($id);
            
            // Update rekam medis
            $rekamMedis->update([
                'Id_Dokter' => $request->Id_Dokter,
                'Tanggal_Jam' => $request->Tanggal_Jam,
                'Keluhan_Utama' => $request->Keluhan_Utama,
                'Riwayat_Penyakit_Sekarang' => $request->Riwayat_Penyakit_Sekarang,
                'Riwayat_Penyakit_Dahulu' => $request->Riwayat_Penyakit_Dahulu,
                'Riwayat_Imunisasi' => $request->Riwayat_Imunisasi,
                'Riwayat_Penyakit_Keluarga' => $request->Riwayat_Penyakit_Keluarga,
                'Silsilah_Keluarga' => $request->Silsilah_Keluarga
            ]);
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('rekam_medis.index')->with('success', 'Rekam medis berhasil diperbarui.');
            
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            $rekamMedis = RekamMedis::findOrFail($id);
            
            // Hapus rekam medis
            $rekamMedis->delete();
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('rekam_medis.index')->with('success', 'Rekam medis berhasil dihapus.');
            
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Cetak rekam medis
     */
    public function cetak($id)
    {
        $rekamMedis = RekamMedis::with(['siswa', 'dokter'])->findOrFail($id);
        $detailPemeriksaan = DetailPemeriksaan::where('Id_Siswa', $rekamMedis->Id_Siswa)
            ->orderBy('Tanggal_Jam', 'desc')
            ->get();
            
        return view('rekam_medis.cetak', compact('rekamMedis', 'detailPemeriksaan'));
    }
    
    /**
     * Histori rekam medis siswa
     */
    public function histori($id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);
        $rekamMedis = RekamMedis::where('Id_Siswa', $id_siswa)
            ->with('dokter')
            ->orderBy('Tanggal_Jam', 'desc')
            ->get();
        $detailPemeriksaan = DetailPemeriksaan::where('Id_Siswa', $id_siswa)
            ->with(['dokter', 'petugasUks'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->get();
            
        return view('rekam_medis.histori', compact('siswa', 'rekamMedis', 'detailPemeriksaan'));
    }
}