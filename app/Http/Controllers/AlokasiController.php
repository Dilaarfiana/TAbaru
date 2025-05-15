<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AlokasiController extends Controller
{
    // Konstanta kode sekolah
    private const KODE_SEKOLAH = '6';
    
    /**
     * Tampilkan halaman alokasi
     */
    public function index()
    {
        // Ambil data untuk dropdown
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelas = Kelas::orderBy('Nama_Kelas')->get();
        
        // Ambil data siswa yang belum dialokasi
        $siswa = Siswa::whereDoesntHave('detailSiswa')->orderBy('nama_siswa')->get();
        
        return view('siswa.alokasi.index', compact('jurusans', 'kelas', 'siswa'));
    }
    
    /**
     * Tampilkan siswa yang belum dialokasi
     */
    public function unallocated()
    {
        // Ambil data untuk dropdown
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelas = Kelas::orderBy('Nama_Kelas')->get();
        
        // Ambil data siswa yang belum dialokasi
        $siswa = Siswa::whereDoesntHave('detailSiswa')
            ->orderBy('nama_siswa')
            ->paginate(10);
            
        return view('siswa.alokasi.unallocated', compact('jurusans', 'kelas', 'siswa'));
    }
    
    /**
     * Mengalokasikan siswa ke jurusan dan kelas
     */
    public function alokasi(Request $request)
    {
        // Ambil id siswa dari request
        $id_siswa = $request->input('siswa_id');
        
        Log::info("Memulai alokasi siswa", [
            "ID Siswa" => $id_siswa,
            "Request data" => $request->all()
        ]);
        
        // Validasi request
        $validator = Validator::make($request->all(), [
            'kode_jurusan' => 'required|exists:Jurusan,Kode_Jurusan',
            'kode_kelas' => 'required|exists:Kelas,Kode_Kelas',
        ]);
        
        if ($validator->fails()) {
            Log::error("Validasi gagal di alokasi", [
                "errors" => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Cek keberadaan siswa
        $siswa = Siswa::findOrFail($id_siswa);
        
        // Cek apakah kelas sesuai dengan jurusan
        $kelas = Kelas::where('Kode_Kelas', $request->kode_kelas)->first();
        
        if (strtoupper($kelas->Kode_Jurusan) !== strtoupper($request->kode_jurusan)) {
            Log::error("Kelas tidak sesuai dengan jurusan", [
                'kelas_jurusan' => $kelas->Kode_Jurusan,
                'request_jurusan' => $request->kode_jurusan
            ]);
            return redirect()->back()
                ->with('error', 'Kelas yang dipilih tidak sesuai dengan jurusan')
                ->withInput();
        }
        
        DB::beginTransaction();
        try {
            // Cek apakah siswa sudah memiliki detail
            $detailSiswa = DetailSiswa::where('id_siswa', $id_siswa)->first();
            $oldJurusan = $detailSiswa ? $detailSiswa->kode_jurusan : null;
            $oldKelas = $detailSiswa ? $detailSiswa->kode_kelas : null;
            
            // Jika jurusan berubah, update ID siswa
            $idLama = $siswa->id_siswa;
            
            // Log format ID lama untuk analisis
            Log::info("Format ID lama", [
                "ID" => $idLama,
                "Memenuhi format?" => $this->checkIdFormat($idLama)
            ]);
            
            // Selalu generate ID baru jika jurusan berubah atau ID tidak valid
            if (!$oldJurusan || strtoupper($oldJurusan) !== strtoupper($request->kode_jurusan) || !$this->checkIdFormat($idLama)) {
                // Generate ID baru berdasarkan format yang benar
                $idBaru = $this->generateNewSiswaId($siswa, $request->kode_jurusan);
                
                // Update ID siswa
                $siswa->id_siswa = $idBaru;
                $siswa->save();
                
                Log::info("ID siswa diperbarui", [
                    "ID Lama" => $idLama,
                    "ID Baru" => $idBaru,
                    "Format ID Baru Valid" => $this->checkIdFormat($idBaru)
                ]);
            } else {
                $idBaru = $idLama; // Tidak ada perubahan ID
            }
            
            // Update atau buat detail siswa
            if ($detailSiswa) {
                // Update detail yang sudah ada
                $detailSiswa->kode_jurusan = $request->kode_jurusan;
                $detailSiswa->kode_kelas = $request->kode_kelas;
                $detailSiswa->id_siswa = $siswa->id_siswa; // Update jika ID berubah
                $detailSiswa->save();
            } else {
                // Buat detail baru dengan format ID yang benar
                $idDetailSiswa = $this->generateDetailSiswaId();
                
                $newDetail = new DetailSiswa();
                $newDetail->id_detsiswa = $idDetailSiswa; 
                $newDetail->id_siswa = $siswa->id_siswa;
                $newDetail->kode_jurusan = $request->kode_jurusan;
                $newDetail->kode_kelas = $request->kode_kelas;
                $newDetail->save();
            }
            
            // Update jumlah siswa di kelas lama (jika ada) dan kelas baru
            if ($oldKelas && $oldKelas != $request->kode_kelas) {
                $this->updateJumlahSiswa($oldKelas);
            }
            $this->updateJumlahSiswa($request->kode_kelas);
            
            DB::commit();
            
            Log::info("Alokasi siswa berhasil", [
                "ID Siswa" => $siswa->id_siswa,
                "Nama Siswa" => $siswa->nama_siswa,
                "Jurusan" => $request->kode_jurusan,
                "Kelas" => $kelas->Nama_Kelas
            ]);
            
            $message = $idLama !== $idBaru 
                ? "Siswa {$siswa->nama_siswa} berhasil dialokasikan ke kelas {$kelas->Nama_Kelas}. ID diperbarui dari {$idLama} menjadi {$idBaru}"
                : "Siswa {$siswa->nama_siswa} berhasil dialokasikan ke kelas {$kelas->Nama_Kelas}";
                
            return redirect()->back()->with('success', $message);
        } 
        catch (\Exception $e) {
            DB::rollback();
            Log::error("Error dalam alokasi siswa: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()
                ->with('error', 'Gagal mengalokasikan siswa: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Alokasi siswa secara massal
     */
    public function massAlokasi(Request $request)
    {
        Log::info("Memulai massAlokasi", [
            "Request data" => $request->all()
        ]);
        
        $validator = Validator::make($request->all(), [
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswas,id_siswa',
            'kode_jurusan' => 'required|exists:Jurusan,Kode_Jurusan',
            'kode_kelas' => 'required|exists:Kelas,Kode_Kelas',
        ]);
        
        if ($validator->fails()) {
            Log::error("Validasi gagal dalam massAlokasi", [
                "errors" => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Cek apakah kelas sesuai dengan jurusan
        $kelas = Kelas::where('Kode_Kelas', $request->kode_kelas)->first();
        
        if (!$kelas || strtoupper($kelas->Kode_Jurusan) !== strtoupper($request->kode_jurusan)) {
            Log::error("Kelas tidak sesuai dengan jurusan dalam massAlokasi", [
                'kelas_jurusan' => $kelas ? $kelas->Kode_Jurusan : 'Kelas tidak ditemukan',
                'request_jurusan' => $request->kode_jurusan
            ]);
            return redirect()->back()
                ->with('error', 'Kelas yang dipilih tidak sesuai dengan jurusan')
                ->withInput();
        }
        
        DB::beginTransaction();
        try {
            $successCount = 0;
            $updatedIds = [];
            
            foreach ($request->siswa_ids as $id_siswa) {
                $siswa = Siswa::find($id_siswa);
                
                if (!$siswa) {
                    Log::warning("Siswa dengan ID {$id_siswa} tidak ditemukan saat mass alokasi");
                    continue;
                }
                
                // Cek apakah siswa sudah memiliki detail
                $detailSiswa = DetailSiswa::where('id_siswa', $id_siswa)->first();
                $oldJurusan = $detailSiswa ? $detailSiswa->kode_jurusan : null;
                $oldKelas = $detailSiswa ? $detailSiswa->kode_kelas : null;
                
                // Jika jurusan berubah, update ID siswa
                $idLama = $siswa->id_siswa;
                
                // Periksa format ID lama dan updatekan jika perlu
                if (!$oldJurusan || strtoupper($oldJurusan) !== strtoupper($request->kode_jurusan) || !$this->checkIdFormat($idLama)) {
                    // Generate ID baru
                    $idBaru = $this->generateNewSiswaId($siswa, $request->kode_jurusan);
                    
                    // Update ID siswa
                    $siswa->id_siswa = $idBaru;
                    $siswa->save();
                    
                    Log::info("ID siswa diperbarui dalam mass alokasi", [
                        "ID Lama" => $idLama,
                        "ID Baru" => $idBaru,
                        "Nama Siswa" => $siswa->nama_siswa
                    ]);
                    
                    // Simpan perubahan ID untuk pesan
                    $updatedIds[] = [
                        'name' => $siswa->nama_siswa,
                        'old' => $idLama,
                        'new' => $idBaru,
                    ];
                }
                
                // Update atau buat detail siswa
                if ($detailSiswa) {
                    // Update detail yang sudah ada
                    $detailSiswa->kode_jurusan = $request->kode_jurusan;
                    $detailSiswa->kode_kelas = $request->kode_kelas;
                    $detailSiswa->id_siswa = $siswa->id_siswa; // Gunakan ID baru jika diubah
                    $detailSiswa->save();
                } else {
                    // Buat detail baru
                    $idDetailSiswa = $this->generateDetailSiswaId();
                    
                    $newDetail = new DetailSiswa();
                    $newDetail->id_detsiswa = $idDetailSiswa;
                    $newDetail->id_siswa = $siswa->id_siswa;
                    $newDetail->kode_jurusan = $request->kode_jurusan;
                    $newDetail->kode_kelas = $request->kode_kelas;
                    $newDetail->save();
                }
                
                // Update jumlah siswa di kelas lama (jika ada)
                if ($oldKelas && $oldKelas != $request->kode_kelas) {
                    $this->updateJumlahSiswa($oldKelas);
                }
                
                $successCount++;
            }
            
            // Update jumlah siswa di kelas baru
            $this->updateJumlahSiswa($request->kode_kelas);
            
            DB::commit();
            
            // Buat pesan sukses
            $message = "{$successCount} siswa berhasil dialokasikan ke kelas {$kelas->Nama_Kelas}.";
            
            if (count($updatedIds) > 0) {
                $message .= " ID yang diperbarui: ";
                foreach ($updatedIds as $idx => $change) {
                    if ($idx < 5) {
                        $message .= "{$change['name']} ({$change['old']} → {$change['new']}), ";
                    }
                }
                $message = rtrim($message, ', ');
                
                if (count($updatedIds) > 5) {
                    $message .= " dan " . (count($updatedIds) - 5) . " siswa lainnya";
                }
            }
            
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error dalam mass alokasi siswa: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()
                ->with('error', 'Gagal mengalokasikan siswa secara massal: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Generate ID siswa baru dengan format yang benar
     * Format: 6 + Kode Jurusan (1 huruf) + Tahun (2 digit) + Nomor Urut (3 digit)
     */
    private function generateNewSiswaId($siswa, $kodeJurusan)
    {
        // Ambil 1 karakter pertama dari kode jurusan
        $kodeJurusanChar = strtoupper(substr($kodeJurusan, 0, 1));
        
        // Ambil tahun dari tanggal masuk siswa atau gunakan tahun saat ini
        $tahun = null;
        if ($siswa->tanggal_masuk) {
            $tahun = Carbon::parse($siswa->tanggal_masuk)->format('y');
        } else {
            $tahun = date('y'); // Tahun saat ini (2 digit)
        }
        
        // Buat prefix ID: 6 + Kode Jurusan + Tahun
        $prefix = self::KODE_SEKOLAH . $kodeJurusanChar . $tahun;
        
        Log::debug("Generating new ID with prefix: {$prefix}", [
            'kode_jurusan' => $kodeJurusan,
            'kode_jurusan_char' => $kodeJurusanChar,
            'tahun' => $tahun
        ]);
        
        // Cari nomor urut terakhir untuk prefix ini
        $lastSiswa = Siswa::where('id_siswa', 'like', $prefix . '%')
            ->orderBy('id_siswa', 'desc')
            ->first();
            
        $newNumber = 1;
        
        if ($lastSiswa) {
            // Ambil 3 digit terakhir dari ID terakhir
            $lastDigits = substr($lastSiswa->id_siswa, -3);
            
            // Cek apakah string tersebut adalah angka
            if (is_numeric($lastDigits)) {
                $lastNumber = (int) $lastDigits;
                $newNumber = $lastNumber + 1;
            }
            
            Log::debug("Last siswa found with ID: {$lastSiswa->id_siswa}, extracted last number: {$lastNumber}, new number: {$newNumber}");
        } else {
            Log::debug("No existing siswa found with prefix: {$prefix}, starting from 001");
        }
        
        // Format dengan leading zeros untuk mendapatkan 3 digit
        $newId = $prefix . sprintf('%03d', $newNumber);
        
        Log::info("Generated new ID: {$newId} for siswa: {$siswa->nama_siswa}");
        
        return $newId;
    }
    
    /**
     * Fungsi untuk memeriksa format ID
     * Format yang benar: 6 + Kode Jurusan (1 huruf) + Tahun (2 digit) + Nomor Urut (3 digit)
     */
    private function checkIdFormat($id)
    {
        // Format yang benar: 6A25001
        $pattern = '/^6[A-Z]\d{2}\d{3}$/';
        $result = preg_match($pattern, $id);
        
        Log::debug("Checking ID format for: {$id}, result: " . ($result ? 'valid' : 'invalid'));
        
        return $result;
    }
    
    /**
     * Generate ID untuk detail siswa
     */
    private function generateDetailSiswaId()
    {
        // Format: DET + Timestamp
        $prefix = 'DET';
        $timestamp = Carbon::now()->format('YmdHis');
        
        // Cek apakah ID sudah digunakan
        $idDetailSiswa = $prefix . $timestamp;
        $existing = DetailSiswa::where('id_detsiswa', $idDetailSiswa)->first();
        
        // Jika sudah ada, tambahkan random number
        if ($existing) {
            $idDetailSiswa .= rand(10, 99);
        }
        
        return $idDetailSiswa;
    }
    
    /**
     * Update jumlah siswa di kelas
     */
    private function updateJumlahSiswa($kodeKelas)
    {
        // Hitung jumlah siswa dalam kelas
        $jumlahSiswa = DetailSiswa::where('kode_kelas', $kodeKelas)->count();
        
        // Update jumlah siswa dalam tabel kelas
        Kelas::where('Kode_Kelas', $kodeKelas)->update([
            'Jumlah_Siswa' => $jumlahSiswa
        ]);
        
        Log::info("Updated Jumlah_Siswa for kelas {$kodeKelas} to {$jumlahSiswa}");
        
        return $jumlahSiswa;
    }
    
    /**
     * Kembalikan siswa dari alokasi (hapus dari jurusan & kelas)
     */
    public function kembalikan(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required|exists:siswas,id_siswa',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        $id_siswa = $request->siswa_id;
        
        // Ambil data siswa dan detail siswa
        $siswa = Siswa::findOrFail($id_siswa);
        $detailSiswa = DetailSiswa::where('id_siswa', $id_siswa)->first();
        
        if (!$detailSiswa) {
            return redirect()->back()->with('error', 'Siswa tidak memiliki alokasi');
        }
        
        DB::beginTransaction();
        
        try {
            // Simpan kelas lama untuk update jumlah siswa
            $oldKelas = $detailSiswa->kode_kelas;
            
            // Hapus detail siswa
            $detailSiswa->delete();
            
            // Update jumlah siswa di kelas lama
            $this->updateJumlahSiswa($oldKelas);
            
            DB::commit();
            
            return redirect()->back()->with('success', "Siswa {$siswa->nama_siswa} berhasil dikembalikan dari alokasi");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error dalam mengembalikan siswa: " . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal mengembalikan siswa: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan siswa yang sudah dialokasi
     */
    public function allocated(Request $request)
    {
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelas = Kelas::orderBy('Nama_Kelas')->get();
        
        $query = Siswa::join('detail_siswa', 'siswas.id_siswa', '=', 'detail_siswa.id_siswa')
                      ->join('kelas', 'detail_siswa.kode_kelas', '=', 'kelas.Kode_Kelas')
                      ->join('jurusan', 'detail_siswa.kode_jurusan', '=', 'jurusan.Kode_Jurusan')
                      ->select('siswas.*', 'detail_siswa.kode_jurusan', 'detail_siswa.kode_kelas', 
                               'kelas.Nama_Kelas', 'jurusan.Nama_Jurusan');
                               
        // Filter berdasarkan jurusan jika ada
        if ($request->has('jurusan') && !empty($request->jurusan)) {
            $query->where('detail_siswa.kode_jurusan', $request->jurusan);
        }
        
        // Filter berdasarkan kelas jika ada
        if ($request->has('kelas') && !empty($request->kelas)) {
            $query->where('detail_siswa.kode_kelas', $request->kelas);
        }
        
        // Filter berdasarkan nama siswa jika ada
        if ($request->has('nama') && !empty($request->nama)) {
            $query->where('siswas.nama_siswa', 'like', '%' . $request->nama . '%');
        }
        
        $siswa = $query->orderBy('siswas.nama_siswa')->paginate(10);
        
        return view('siswa.alokasi.allocated', compact('jurusans', 'kelas', 'siswa'));
    }
    
    /**
     * Menampilkan hasil filter pada alokasi
     */
    public function filter(Request $request)
    {
        return $this->allocated($request);
    }
    
    /**
     * Pindahkan siswa ke kelas lain
     */
    public function pindah(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required|exists:siswas,id_siswa',
            'kode_kelas' => 'required|exists:Kelas,Kode_Kelas',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $id_siswa = $request->siswa_id;
        
        // Ambil data siswa dan detail siswa
        $siswa = Siswa::findOrFail($id_siswa);
        $detailSiswa = DetailSiswa::where('id_siswa', $id_siswa)->first();
        
        if (!$detailSiswa) {
            return redirect()->back()->with('error', 'Siswa tidak memiliki alokasi');
        }
        
        // Ambil data kelas baru
        $kelasBaru = Kelas::where('Kode_Kelas', $request->kode_kelas)->first();
        
        // Cek apakah kelas sesuai dengan jurusan
        if (strtoupper($kelasBaru->Kode_Jurusan) !== strtoupper($detailSiswa->kode_jurusan)) {
            return redirect()->back()
                ->with('error', 'Kelas yang dipilih tidak sesuai dengan jurusan siswa')
                ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Simpan kelas lama untuk update jumlah siswa
            $oldKelas = $detailSiswa->kode_kelas;
            
            // Update kelas di detail siswa
            $detailSiswa->kode_kelas = $request->kode_kelas;
            $detailSiswa->save();
            
            // Update jumlah siswa di kelas lama dan kelas baru
            $this->updateJumlahSiswa($oldKelas);
            $this->updateJumlahSiswa($request->kode_kelas);
            
            DB::commit();
            
            return redirect()->back()->with('success', "Siswa {$siswa->nama_siswa} berhasil dipindahkan ke kelas {$kelasBaru->Nama_Kelas}");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error dalam memindahkan siswa: " . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal memindahkan siswa: ' . $e->getMessage());
        }
    }
}