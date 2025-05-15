<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Query dasar dengan eager loading relasi
        $query = Siswa::with(['detailSiswa.kelas', 'detailSiswa.jurusan']);
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }
        
        // Filter berdasarkan jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        
        // Filter berdasarkan tahun masuk
        if ($request->filled('tahun_masuk')) {
            $query->whereYear('tanggal_masuk', $request->tahun_masuk);
        }
        
        // Pencarian berdasarkan keyword
        if ($request->filled('keyword')) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function($q) use ($keyword) {
                $q->where('id_siswa', 'like', $keyword)
                  ->orWhere('nama_siswa', 'like', $keyword)
                  ->orWhere('tempat_lahir', 'like', $keyword);
            });
        }
        
        // Pengurutan
        $sortBy = $request->input('sort_by', 'id_siswa');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination dengan opsi untuk mengubah jumlah item per halaman
        $perPage = $request->input('per_page', 15);
        $siswas = $query->paginate($perPage)->appends($request->query());
        
        // Hitung usia untuk setiap siswa
        foreach ($siswas as $siswa) {
            if ($siswa->tanggal_lahir) {
                $tanggalLahir = new \DateTime($siswa->tanggal_lahir);
                $today = new \DateTime();
                $siswa->usia = $tanggalLahir->diff($today)->y;
            } else {
                $siswa->usia = null;
            }
        }
        
        // Menyiapkan data untuk dropdown filter
        $tahunMasuk = Siswa::selectRaw('YEAR(tanggal_masuk) as tahun')
            ->distinct()
            ->whereNotNull('tanggal_masuk')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
            
        return view('siswa.index', compact('siswas', 'tahunMasuk'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Generate ID berikutnya
        $nextId = Siswa::generateNextId();
        
        return view('siswa.create', compact('nextId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|string|max:10|unique:siswas,id_siswa',
            'nama_siswa' => 'required|string|max:50',
            'tempat_lahir' => 'nullable|string|max:30',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_masuk' => 'nullable|date|before_or_equal:today',
            'status_aktif' => 'nullable|boolean',
        ], [
            'id_siswa.unique' => 'ID Siswa sudah digunakan',
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh lebih dari hari ini',
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Buat siswa baru dengan data yang sudah divalidasi
            $siswa = Siswa::create($validator->validated());
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('siswa.show', $siswa->id_siswa)
                ->with('success', 'Siswa berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            return redirect()->route('siswa.create')
                ->with('error', 'Terjadi kesalahan. Siswa gagal ditambahkan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Dapatkan data siswa dengan relasi yang dibutuhkan
        $siswa = Siswa::with([
                'detailSiswa.jurusan', 
                'detailSiswa.kelas',
                'orangTua'
            ])
            ->findOrFail($id);
        
        // Hitung umur siswa
        $umur = null;
        if ($siswa->tanggal_lahir) {
            $tanggalLahir = new \DateTime($siswa->tanggal_lahir);
            $today = new \DateTime();
            $umur = $tanggalLahir->diff($today)->y; // Hanya ambil tahun
        }
        
        // Hitung lama sekolah
        $lamaSekolah = null;
        if ($siswa->tanggal_masuk) {
            $tanggalMasuk = new \DateTime($siswa->tanggal_masuk);
            $today = new \DateTime();
            $lamaSekolah = $tanggalMasuk->diff($today);
        }
        
        return view('siswa.show', compact('siswa', 'umur', 'lamaSekolah'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'nama_siswa' => 'required|string|max:50',
            'tempat_lahir' => 'nullable|string|max:30',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_masuk' => 'nullable|date|before_or_equal:today',
            'status_aktif' => 'nullable|boolean',
        ], [
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh lebih dari hari ini',
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Cari siswa
            $siswa = Siswa::findOrFail($id);
            
            // Update siswa dengan data yang sudah divalidasi
            $siswa->update($validator->validated());
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('siswa.show', $siswa->id_siswa)
                ->with('success', 'Data siswa berhasil diperbarui.');
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            return redirect()->route('siswa.edit', $id)
                ->with('error', 'Terjadi kesalahan. Data siswa gagal diperbarui: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Cari dan hapus siswa
            $siswa = Siswa::findOrFail($id);
            $nama = $siswa->nama_siswa;
            $siswa->delete();
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('siswa.index')
                ->with('success', "Siswa '$nama' berhasil dihapus.");
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan. Siswa gagal dihapus: ' . $e->getMessage());
        }
    }
    
    /**
     * Export data siswa ke format Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Implementasi export data siswa
        // Bisa menggunakan package seperti maatwebsite/excel
        
        // Contoh pseudo code
        try {
            // Proses export
            
            return redirect()->route('siswa.index')
                ->with('success', 'Data siswa berhasil diexport.');
                
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
    
    /**
     * Import data siswa dari Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.index')
                ->withErrors($validator);
        }

        try {
            // Proses import
            // Menggunakan package seperti maatwebsite/excel
            
            return redirect()->route('siswa.index')
                ->with('success', 'Data siswa berhasil diimport.');
                
        } catch (\Exception $e) {
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }
    }
}