<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\DetailSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * Menampilkan daftar kelas.
     */
    public function index()
    {
        $kelas = Kelas::with('jurusan')->orderBy('Nama_Kelas')->get();
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Menampilkan form untuk membuat kelas baru.
     */
    public function create()
    {
        $nextId = Kelas::getNewCode();
        $currentTahunAjaran = Kelas::getCurrentTahunAjaran();
        $jurusan = Jurusan::orderBy('Kode_Jurusan')->get();
        $selectedJurusan = '';
        
        return view('kelas.create', compact('nextId', 'jurusan', 'selectedJurusan', 'currentTahunAjaran'));
    }
    
    /**
     * Menyimpan kelas baru ke database.
     */
    public function store(Request $request)
    {
        // Jika Kode_Kelas kosong, generate yang baru
        if (empty($request->Kode_Kelas)) {
            $nextId = Kelas::getNewCode();
            $request->merge(['Kode_Kelas' => $nextId]);
        }
        
        // Jika Tahun_Ajaran kosong, generate yang baru
        if (empty($request->Tahun_Ajaran)) {
            $currentTahunAjaran = Kelas::getCurrentTahunAjaran();
            $request->merge(['Tahun_Ajaran' => $currentTahunAjaran]);
        }
        
        $validator = Validator::make($request->all(), [
            'Kode_Kelas' => 'required|string|max:5|unique:Kelas,Kode_Kelas',
            'Nama_Kelas' => 'required|string|max:30', // Hapus unique validation untuk fleksibilitas
            'Kode_Jurusan' => 'required|exists:jurusan,Kode_Jurusan',
            'Tahun_Ajaran' => 'nullable|string|max:10',
        ], [
            'Kode_Kelas.required' => 'Kode kelas harus diisi.',
            'Kode_Kelas.unique' => 'Kode kelas sudah ada.',
            'Nama_Kelas.required' => 'Nama kelas harus diisi.',
            'Nama_Kelas.max' => 'Nama kelas maksimal 30 karakter.',
            'Kode_Kelas' => 'required|string|max:5|unique:kelas,Kode_Kelas',
            'Kode_Jurusan' => 'required|exists:jurusan,Kode_Jurusan',
            'Tahun_Ajaran.max' => 'Tahun ajaran maksimal 10 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('kelas.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Hapus input Jumlah_Siswa dari request karena akan dihitung otomatis
            $requestData = $request->except('Jumlah_Siswa');
            
            Kelas::create($requestData);
            
            DB::commit();
            
            return redirect()->route('kelas.index')
                ->with('success', 'Kelas berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('kelas.create')
                ->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage())
                ->withInput();
        }
    }
    

    /**
     * Menampilkan detail kelas tertentu.
     */
    public function show(string $id)
    {
        $kelas = Kelas::with(['jurusan'])->findOrFail($id);
        
        // Ambil siswa yang ada di kelas ini
        $siswa = DetailSiswa::with(['siswa', 'jurusan'])
            ->where('kode_kelas', $id)
            ->paginate(10);
            
        // Hitung jumlah siswa berdasarkan jenis kelamin
        $jumlahLakiLaki = DetailSiswa::whereHas('siswa', function($q) {
                $q->where('jenis_kelamin', 'L');
            })->where('kode_kelas', $id)->count();
            
        $jumlahPerempuan = DetailSiswa::whereHas('siswa', function($q) {
                $q->where('jenis_kelamin', 'P');
            })->where('kode_kelas', $id)->count();
        
        return view('kelas.show', compact('kelas', 'siswa', 'jumlahLakiLaki', 'jumlahPerempuan'));
    }

    /**
     * Menampilkan form untuk mengedit kelas.
     */
    public function edit(string $id)
    {
        $kelas = Kelas::findOrFail($id);
        $jurusan = Jurusan::orderBy('Kode_Jurusan')->get();
        return view('kelas.edit', compact('kelas', 'jurusan'));
    }

    /**
     * Memperbarui kelas di database.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'Nama_Kelas' => 'required|string|max:30',
            'Tahun_Ajaran' => 'nullable|string|max:10',
            'Kode_Jurusan' => 'required|exists:jurusan,Kode_Jurusan',
            'Jumlah_Siswa' => 'nullable|integer|min:0',
        ], [
            'Nama_Kelas.required' => 'Nama kelas harus diisi.',
            'Nama_Kelas.max' => 'Nama kelas maksimal 30 karakter.',
            'Kode_Jurusan.required' => 'Jurusan harus dipilih.',
            'Kode_Jurusan.exists' => 'Jurusan yang dipilih tidak valid.',
            'Tahun_Ajaran.max' => 'Tahun ajaran maksimal 10 karakter.',
            'Jumlah_Siswa.integer' => 'Jumlah siswa harus berupa angka.',
            'Jumlah_Siswa.min' => 'Jumlah siswa minimal 0.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('kelas.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $kelas = Kelas::findOrFail($id);
            $kelas->update($request->all());
            
            DB::commit();
            
            return redirect()->route('kelas.index')
                ->with('success', 'Kelas berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('kelas.edit', $id)
                ->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghapus kelas dari database.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        
        try {
            // Periksa apakah kelas digunakan oleh siswa
            $usedByStudents = DetailSiswa::where('kode_kelas', $id)->exists();
            
            if ($usedByStudents) {
                DB::rollBack();
                return redirect()->route('kelas.index')
                    ->with('error', 'Kelas tidak dapat dihapus karena masih digunakan oleh siswa!');
            }
            
            $kelas = Kelas::findOrFail($id);
            $kelas->delete();
            
            DB::commit();
            
            return redirect()->route('kelas.index')
                ->with('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus: ' . $e->getMessage());
        }
    }
    
    /**
     * Menambahkan siswa ke kelas
     */
    public function addSiswa(Request $request, string $kode_kelas)
    {
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswas,id_siswa',
        ], [
            'id_siswa.required' => 'Siswa harus dipilih.',
            'id_siswa.exists' => 'Siswa tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            $kelas = Kelas::findOrFail($kode_kelas);
            
            // Periksa apakah siswa sudah ada di kelas lain
            $existingDetail = DetailSiswa::where('id_siswa', $request->id_siswa)->first();
            
            if ($existingDetail) {
                // Jika sudah ada di kelas yang sama, tidak perlu dilakukan apa-apa
                if ($existingDetail->kode_kelas == $kode_kelas) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('info', 'Siswa sudah terdaftar di kelas ini.');
                }
                
                // Jika sudah ada di kelas lain, update kelas
                $kelasLama = $existingDetail->kode_kelas;
                $existingDetail->kode_kelas = $kode_kelas;
                $existingDetail->save();
                
                // Update jumlah siswa di kelas lama
                $this->updateJumlahSiswa($kelasLama);
            } else {
                // Jika belum ada di kelas manapun, buat detail siswa baru
                
                // Generate ID untuk Detail_Siswa
                $lastId = DB::table('detail_siswas')->max('id_detsiswa');
                $newIdNumber = $lastId ? intval(substr($lastId, 2)) + 1 : 1;
                $newDetId = 'DS' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
                
                // Ambil kode jurusan dari ID siswa
                $kodeJurusan = substr($request->id_siswa, 1, 1);
                
                DetailSiswa::create([
                    'id_detsiswa' => $newDetId,
                    'id_siswa' => $request->id_siswa,
                    'kode_jurusan' => $kodeJurusan,
                    'kode_kelas' => $kode_kelas
                ]);
            }
            
            // Update jumlah siswa di kelas baru
            $this->updateJumlahSiswa($kode_kelas);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Siswa berhasil ditambahkan ke kelas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan siswa ke kelas: ' . $e->getMessage());
        }
    }
    
    /**
     * Menghapus siswa dari kelas
     */
    public function removeSiswa(string $kode_kelas, string $id_detail)
    {
        DB::beginTransaction();
        
        try {
            $detailSiswa = DetailSiswa::findOrFail($id_detail);
            
            // Hapus detail siswa
            $detailSiswa->delete();
            
            // Update jumlah siswa di kelas
            $this->updateJumlahSiswa($kode_kelas);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Siswa berhasil dihapus dari kelas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus siswa dari kelas: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper untuk update jumlah siswa di kelas
     */
    private function updateJumlahSiswa($kode_kelas)
    {
        $kelas = Kelas::find($kode_kelas);
        if ($kelas) {
            $jumlahSiswa = DetailSiswa::where('kode_kelas', $kelas->Kode_Kelas)->count();
            $kelas->Jumlah_Siswa = $jumlahSiswa;
            $kelas->save();
        }
    }

    /**
     * Fungsi untuk mendapatkan daftar kelas berdasarkan jurusan (untuk AJAX)
     */
    public function getKelasByJurusan($kode_jurusan)
    {
        try {
            $kelas = Kelas::where('Kode_Jurusan', $kode_jurusan)
                         ->orderBy('Nama_Kelas')
                         ->get(['Kode_Kelas', 'Nama_Kelas']);
            
            return response()->json([
                'success' => true,
                'data' => $kelas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kelas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Fungsi untuk cek ketersediaan nama kelas (untuk validasi AJAX)
     */
    public function checkNamaKelas(Request $request)
    {
        $namaKelas = $request->input('nama_kelas');
        $kodeJurusan = $request->input('kode_jurusan');
        $tahunAjaran = $request->input('tahun_ajaran');
        $excludeId = $request->input('exclude_id'); // untuk edit

        $query = Kelas::where('Nama_Kelas', $namaKelas)
                     ->where('Kode_Jurusan', $kodeJurusan)
                     ->where('Tahun_Ajaran', $tahunAjaran);

        if ($excludeId) {
            $query->where('Kode_Kelas', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Nama kelas sudah digunakan di jurusan dan tahun ajaran ini.' : 'Nama kelas tersedia.'
        ]);
    }
}