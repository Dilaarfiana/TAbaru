<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DetailSiswaController extends Controller
{
    // Kode sekolah yang tetap
    private const KODE_SEKOLAH = '6';
    
    /**
     * Tampilkan halaman detail siswa
     */
    public function index(Request $request)
    {
        $query = DetailSiswa::with(['siswa', 'jurusan', 'kelas']);
        
        // Filter status alokasi
        if ($request->has('alokasi_status')) {
            if ($request->alokasi_status == 'unallocated') {
                $query->whereNull('kode_kelas');
            } elseif ($request->alokasi_status == 'allocated') {
                $query->whereNotNull('kode_kelas');
            }
        }
        
        // Filter berdasarkan jurusan
        if ($request->filled('jurusan')) {
            $query->where('kode_jurusan', $request->jurusan);
        }
        
        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->where('kode_kelas', $request->kelas);
        }
        
        // Filter berdasarkan pencarian nama atau ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('id_siswa', 'like', '%' . $search . '%');
            });
        }
        
        $detailSiswas = $query->orderBy('id_detsiswa')->paginate(10);
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelass = Kelas::orderBy('Nama_Kelas')->get();
        
        // Data untuk info cards
        $totalSiswa = Siswa::count();
        $totalTeralokasi = DetailSiswa::whereNotNull('kode_kelas')->count();
        $totalBelumTeralokasi = Siswa::count() - $totalTeralokasi;
        $totalKelas = Kelas::count();
        
        // Jika ini adalah request Ajax, kembalikan hanya data tabel
        if ($request->ajax()) {
            return view('detailsiswa.partials.table', compact('detailSiswas'))->render();
        }
        
        return view('detailsiswa.index', compact(
            'detailSiswas', 
            'jurusans', 
            'kelass',
            'totalSiswa',
            'totalTeralokasi',
            'totalBelumTeralokasi',
            'totalKelas'
        ));
    }
    
    /**
     * Menampilkan detail dari detail siswa
     */
    public function show($id)
    {
        $detailSiswa = DetailSiswa::with(['siswa', 'jurusan', 'kelas'])->findOrFail($id);
        return view('detailsiswa.show', compact('detailSiswa'));
    }
    
    /**
     * Menampilkan form untuk mengedit detail siswa
     * Hanya menampilkan opsi untuk memilih kelas dalam jurusan yang sama
     */
    public function edit($id)
    {
        $detailSiswa = DetailSiswa::with(['siswa', 'jurusan', 'kelas'])->findOrFail($id);
        
        // Hanya ambil kelas-kelas yang terkait dengan jurusan siswa
        $kelass = Kelas::where('Kode_Jurusan', $detailSiswa->kode_jurusan)
            ->orderBy('Nama_Kelas')
            ->get();
        
        // Untuk informasi saja, tidak perlu untuk select
        $jurusan = Jurusan::where('Kode_Jurusan', $detailSiswa->kode_jurusan)->first();
        
        return view('detailsiswa.edit', compact('detailSiswa', 'jurusan', 'kelass'));
    }
    
    /**
     * Update detail siswa - hanya memperbarui kelas
     * (Hanya untuk kompatibilitas dengan UI yang ada)
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_kelas' => 'required|exists:Kelas,Kode_Kelas',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $detailSiswa = DetailSiswa::findOrFail($id);
        
        // Validasi apakah kelas sesuai dengan jurusan
        $kelas = Kelas::find($request->kode_kelas);
        if (strtoupper($kelas->Kode_Jurusan) != strtoupper($detailSiswa->kode_jurusan)) {
            return back()->with('error', 'Kelas tidak sesuai dengan jurusan yang dipilih!');
        }
        
        try {
            DB::beginTransaction();
            
            // Simpan kode_kelas sebelumnya untuk update jumlah siswa
            $oldKodeKelas = $detailSiswa->kode_kelas;
            
            // Update hanya kelas, tidak mengubah jurusan
            $detailSiswa->kode_kelas = $request->kode_kelas;
            $detailSiswa->save();
            
            // Update jumlah siswa di kelas lama dan baru
            $this->updateJumlahSiswa($oldKodeKelas);
            $this->updateJumlahSiswa($request->kode_kelas);
            
            DB::commit();
            
            return redirect()->route('detailsiswa.index')
                ->with('success', 'Kelas siswa berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kelas siswa: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Hapus detail siswa dan reset ID siswa
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $detailSiswa = DetailSiswa::findOrFail($id);
            $kodeKelas = $detailSiswa->kode_kelas;
            $idSiswa = $detailSiswa->id_siswa;
            
            // Hapus detail siswa
            $detailSiswa->delete();
            
            // Reset ID siswa ke format tanpa jurusan
            $siswa = Siswa::find($idSiswa);
            if ($siswa) {
                $idLama = $siswa->id_siswa;
                $idBaru = Siswa::generateGenericId($siswa->tanggal_masuk);
                
                $siswa->id_siswa = $idBaru;
                $siswa->save();
                
                \Log::info("ID siswa direset setelah hapus detail", [
                    "ID Lama" => $idLama,
                    "ID Baru" => $idBaru
                ]);
            }
            
            // Update jumlah siswa di kelas
            $this->updateJumlahSiswa($kodeKelas);
            
            DB::commit();
            
            return redirect()->route('detailsiswa.index')
                ->with('success', 'Detail siswa berhasil dihapus dan ID siswa direset!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menghapus detail siswa: ' . $e->getMessage());
        }
    }
    
    /**
     * Proses kenaikan kelas (tanpa mengubah jurusan)
     */
    public function kenaikanKelas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_asal' => 'required|exists:Kelas,Kode_Kelas',
            'kelas_tujuan' => 'required|exists:Kelas,Kode_Kelas',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Ambil data siswa dari kelas asal
            $detailSiswas = DetailSiswa::where('kode_kelas', $request->kelas_asal)->get();
            
            // Validasi jumlah siswa
            if ($detailSiswas->count() == 0) {
                return back()->with('error', 'Tidak ada siswa di kelas asal!');
            }
            
            // Ambil kelas tujuan untuk mendapatkan kode jurusan
            $kelasTujuan = Kelas::find($request->kelas_tujuan);
            $kelasAsal = Kelas::find($request->kelas_asal);
            
            // Validasi jurusan kelas asal dan tujuan
            if (strtoupper($kelasAsal->Kode_Jurusan) !== strtoupper($kelasTujuan->Kode_Jurusan)) {
                return back()->with('error', 'Kelas tujuan harus memiliki jurusan yang sama dengan kelas asal!');
            }
            
            // Update kelas untuk semua siswa tersebut
            foreach ($detailSiswas as $detail) {
                // Update kelas saja, tanpa mengubah jurusan
                $detail->kode_kelas = $request->kelas_tujuan;
                $detail->save();
            }
            
            // Update jumlah siswa di kelas asal dan tujuan
            $this->updateJumlahSiswa($request->kelas_asal);
            $this->updateJumlahSiswa($request->kelas_tujuan);
            
            DB::commit();
            return back()->with('success', 'Kenaikan kelas berhasil dilakukan untuk ' . $detailSiswas->count() . ' siswa!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Bersihkan data duplikat
     */
    public function cleanupDuplicates()
    {
        $duplicateIds = DB::table('detail_siswas')
            ->select('id_siswa')
            ->groupBy('id_siswa')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->pluck('id_siswa');
        
        $counter = 0;
        
        DB::beginTransaction();
        try {
            foreach ($duplicateIds as $id) {
                $records = DetailSiswa::where('id_siswa', $id)
                    ->orderBy('id_detsiswa', 'asc')
                    ->get();
                
                if ($records->count() <= 1) continue;
                
                // Simpan record pertama
                $keepRecord = $records->shift();
                $kodeKelas = $keepRecord->kode_kelas;
                
                // Hapus record lainnya
                foreach ($records as $record) {
                    $record->delete();
                    $counter++;
                }
                
                // Update jumlah siswa dalam kelas
                $this->updateJumlahSiswa($kodeKelas);
            }
            
            DB::commit();
            
            if ($counter == 0) {
                return redirect()->route('detailsiswa.index')
                    ->with('success', "Tidak ditemukan data duplikat");
            } else {
                return redirect()->route('detailsiswa.index')
                    ->with('success', "Berhasil membersihkan $counter data duplikat");
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('detailsiswa.index')
                ->with('error', 'Gagal membersihkan data: ' . $e->getMessage());
        }
    }
    
    /**
     * Membersihkan data detail siswa yang tidak valid
     *
     * @return \Illuminate\Http\Response
     */
    public function cleanup()
    {
        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Cari detail siswa yang tidak memiliki relasi ke siswa yang valid
            $invalidDetails = DetailSiswa::whereNotIn('id_siswa', function($query) {
                $query->select('id_siswa')->from('siswas');
            })->get();
            
            $count = $invalidDetails->count();
            
            if ($count > 0) {
                // Log detail yang akan dihapus
                foreach ($invalidDetails as $detail) {
                    Log::info('Menghapus detail siswa tidak valid:', [
                        'id_detsiswa' => $detail->id_detsiswa,
                        'id_siswa' => $detail->id_siswa
                    ]);
                }
                
                // Hapus detail yang tidak valid
                DetailSiswa::whereNotIn('id_siswa', function($query) {
                    $query->select('id_siswa')->from('siswas');
                })->delete();
            }
            
            // Commit transaction
            DB::commit();
            
            return redirect()->route('siswa.index')
                ->with('success', "Pembersihan data selesai. $count data detail siswa yang tidak valid berhasil dihapus.");
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error saat membersihkan data detail siswa: ' . $e->getMessage());
            
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan saat membersihkan data: ' . $e->getMessage());
        }
    }
    
    /**
     * Arahkan ke AlokasiController (untuk kompatibilitas dengan URL yang sudah ada)
     * PERUBAHAN: Menghilangkan parameter $id dan menggunakan siswa_id dari request
     */
    public function alokasi(Request $request)
    {
        \Log::info("DetailSiswaController@alokasi dipanggil, meneruskan ke AlokasiController", [
            "ID Siswa" => $request->input('siswa_id'),
            "Request" => $request->all()
        ]);
        
        // Redirect ke controller AlokasiController
        $controller = app()->make(AlokasiController::class);
        return $controller->alokasi($request);
    }
    
    /**
     * Method untuk backward compatibility
     */
    public function updateAlokasi(Request $request)
    {
        \Log::info("DetailSiswaController@updateAlokasi dipanggil, meneruskan ke AlokasiController", [
            "Request" => $request->all()
        ]);
        
        // Redirect ke controller AlokasiController
        $controller = app()->make(AlokasiController::class);
        return $controller->alokasi($request);
    }
    
    /**
     * Method untuk backward compatibility
     */
    public function updateKelas(Request $request)
    {
        \Log::info("DetailSiswaController@updateKelas dipanggil, meneruskan ke AlokasiController", [
            "Request" => $request->all()
        ]);
        
        // Redirect ke controller AlokasiController
        $controller = app()->make(AlokasiController::class);
        return $controller->alokasi($request);
    }
    
    /**
     * Method untuk backward compatibility
     */
    public function massAlokasi(Request $request)
    {
        \Log::info("DetailSiswaController@massAlokasi dipanggil, meneruskan ke AlokasiController", [
            "Request" => $request->all()
        ]);
        
        // Redirect ke controller AlokasiController
        $controller = app()->make(AlokasiController::class);
        return $controller->massAlokasi($request);
    }
    
    /**
     * Update jumlah siswa dalam kelas
     */
    private function updateJumlahSiswa($kode_kelas)
    {
        if (!$kode_kelas) return;
        
        $kelas = Kelas::find($kode_kelas);
        if ($kelas) {
            $jumlahSiswa = DetailSiswa::where('kode_kelas', $kelas->Kode_Kelas)->count();
            $kelas->jumlah_siswa = $jumlahSiswa;
            $kelas->save();
            
            \Log::info("Jumlah siswa di kelas diperbarui", [
                'kelas' => $kelas->Kode_Kelas,
                'jumlah_siswa' => $jumlahSiswa
            ]);
        }
    }
}