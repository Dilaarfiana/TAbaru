<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\DetailSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class JurusanController extends Controller
{
    /**
     * Menampilkan daftar jurusan.
     */
    public function index()
    {
        // Load relationships untuk menghindari N+1 query problem
        $jurusan = jurusan::with(['kelas', 'siswa'])->orderBy('Kode_Jurusan')->get();
        
        return view('jurusan.index', compact('jurusan'));
    }

    /**
     * Menampilkan form untuk membuat jurusan baru.
     */
    public function create()
    {
        // Generate ID jurusan berikutnya dengan format huruf abjad A-Z
        $nextId = Jurusan::getNewCode();
        return view('jurusan.create', compact('nextId'));
    }

    /**
     * Menyimpan jurusan baru ke database.
     */
    public function store(Request $request)
    {
        // Jika Kode_Jurusan kosong, generate yang baru berdasarkan abjad
        if (empty($request->Kode_Jurusan)) {
            $nextId = Jurusan::getNewCode();
            $request->merge(['Kode_Jurusan' => $nextId]);
        }
        
        $validator = Validator::make($request->all(), [
            'Kode_Jurusan' => 'required|string|size:1|unique:Jurusan,Kode_Jurusan|regex:/^[A-Z]$/',
            'Nama_Jurusan' => 'required|string|max:30|unique:Jurusan,Nama_Jurusan',
        ], [
            'Kode_Jurusan.regex' => 'Kode jurusan harus berupa huruf kapital A-Z',
            'Kode_Jurusan.size' => 'Kode jurusan harus 1 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->route('jurusan.create')
                ->withErrors($validator)
                ->withInput();
        }

        Jurusan::create($request->all());
        
        return redirect()->route('jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail jurusan tertentu.
     */
    public function show(string $id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            
            // Hitung jumlah siswa yang menggunakan jurusan ini dengan penanganan null
            $totalSiswa = 0;
            
            // Ambil daftar siswa dari jurusan ini dengan penanganan pagination yang aman
            $siswa = null;
            
            // Coba ambil data siswa hanya jika tabel dan kolom ada
            try {
                $siswaQuery = DetailSiswa::with('siswa')->where('kode_jurusan', $id);
                if ($siswaQuery !== null) {
                    $totalSiswa = $siswaQuery->count();
                    $siswa = $siswaQuery->paginate(10);
                }
            } catch (\Exception $e) {
                // Jika error, set nilai default
                $totalSiswa = 0;
                $siswa = collect([])->paginate(10);
            }
            
            if ($siswa === null) {
                $siswa = collect([])->paginate(10);
            }
            
            return view('jurusan.show', compact('jurusan', 'totalSiswa', 'siswa'));
        } catch (\Exception $e) {
            return redirect()->route('jurusan.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk mengedit jurusan.
     */
    public function edit(string $id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            return view('jurusan.edit', compact('jurusan'));
        } catch (\Exception $e) {
            return redirect()->route('jurusan.index')
                ->with('error', 'Jurusan tidak ditemukan');
        }
    }

    /**
     * Memperbarui jurusan di database.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'Nama_Jurusan' => 'required|string|max:30|unique:Jurusan,Nama_Jurusan,'.$id.',Kode_Jurusan',
        ]);

        if ($validator->fails()) {
            return redirect()->route('jurusan.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $jurusan = Jurusan::findOrFail($id);
            $jurusan->update($request->all());
            
            return redirect()->route('jurusan.index')
                ->with('success', 'Jurusan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('jurusan.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus jurusan dari database.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        
        try {
            // Periksa apakah jurusan digunakan oleh siswa dengan penanganan null
            $usedByStudents = false;
            
            try {
                $detailSiswaQuery = DetailSiswa::where('kode_jurusan', $id);
                if ($detailSiswaQuery !== null) {
                    $usedByStudents = $detailSiswaQuery->exists();
                }
            } catch (\Exception $e) {
                // Jika error, anggap jurusan tidak digunakan
                $usedByStudents = false;
            }
            
            if ($usedByStudents) {
                DB::rollBack();
                return redirect()->route('jurusan.index')
                    ->with('error', 'Jurusan tidak dapat dihapus karena masih digunakan oleh siswa!');
            }
            
            // Jika tidak digunakan, hapus jurusan
            $jurusan = Jurusan::findOrFail($id);
            $jurusan->delete();
            
            DB::commit();
            
            return redirect()->route('jurusan.index')
                ->with('success', 'Jurusan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('jurusan.index')
                ->with('error', 'Jurusan tidak dapat dihapus: ' . $e->getMessage());
        }
    }
}