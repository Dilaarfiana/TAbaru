<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanHarian;
use App\Models\Siswa;
use App\Models\Dokter;
use App\Models\PetugasUKS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemeriksaanHarianController extends Controller
{
    /**
     * Menampilkan daftar pemeriksaan harian
     */
    public function index()
    {
        $pemeriksaanHarian = PemeriksaanHarian::with(['siswa', 'dokter', 'petugasUKS'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->paginate(10);
            
        return view('pemeriksaan_harian.index', compact('pemeriksaanHarian'));
    }

    /**
     * Menampilkan form untuk membuat data baru
     */
    public function create()
    {
        $siswaList = Siswa::where('Status_Aktif', 1)
            ->orderBy('Nama_Siswa')
            ->get();
            
        $dokterList = Dokter::orderBy('Nama_Dokter')->get();
        $petugasList = PetugasUKS::orderBy('Nama_Petugas_UKS')->get();
        
        // Generate ID baru
        $newId = PemeriksaanHarian::generateId();
        
        return view('pemeriksaan_harian.create', compact('siswaList', 'dokterList', 'petugasList', 'newId'));
    }

    /**
     * Menyimpan data baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'Id_Harian' => 'required|unique:pemeriksaan_harian,Id_Harian',
            'Tanggal_Jam' => 'required|date',
            'Hasil_Pemeriksaan' => 'required',
            'Id_Siswa' => 'required|exists:siswas,id_siswa',
            'Id_Dokter' => 'required|exists:dokters,Id_Dokter',
            'NIP' => 'required|exists:petugas_uks,NIP',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('pemeriksaan-harian.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Simpan data
        PemeriksaanHarian::create($request->all());

        return redirect()
            ->route('pemeriksaan-harian.index')
            ->with('success', 'Data pemeriksaan harian berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail pemeriksaan harian
     */
    public function show($id)
    {
        $pemeriksaanHarian = PemeriksaanHarian::with(['siswa', 'dokter', 'petugasUKS'])
            ->findOrFail($id);
            
        return view('pemeriksaan_harian.show', compact('pemeriksaanHarian'));
    }

    /**
     * Menampilkan form untuk mengedit data
     */
    public function edit($id)
    {
        $pemeriksaanHarian = PemeriksaanHarian::findOrFail($id);
        
        $siswaList = Siswa::where('Status_Aktif', 1)
            ->orderBy('Nama_Siswa')
            ->get();
            
        $dokterList = Dokter::orderBy('Nama_Dokter')->get();
        $petugasList = PetugasUKS::orderBy('Nama_Petugas_UKS')->get();
        
        return view('pemeriksaan_harian.edit', compact('pemeriksaanHarian', 'siswaList', 'dokterList', 'petugasList'));
    }

    /**
     * Mengupdate data di database
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'Tanggal_Jam' => 'required|date',
            'Hasil_Pemeriksaan' => 'required',
            'Id_Siswa' => 'required|exists:siswas,id_siswa',
            'Id_Dokter' => 'required|exists:dokters,Id_Dokter',
            'NIP' => 'required|exists:petugas_uks,NIP',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('pemeriksaan-harian.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        // Update data
        $pemeriksaanHarian = PemeriksaanHarian::findOrFail($id);
        $pemeriksaanHarian->update($request->all());

        return redirect()
            ->route('pemeriksaan-harian.index')
            ->with('success', 'Data pemeriksaan harian berhasil diperbarui!');
    }

    /**
     * Menghapus data dari database
     */
    public function destroy($id)
    {
        $pemeriksaanHarian = PemeriksaanHarian::findOrFail($id);
        $pemeriksaanHarian->delete();

        return redirect()
            ->route('pemeriksaan-harian.index')
            ->with('success', 'Data pemeriksaan harian berhasil dihapus!');
    }
}