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
use Illuminate\Support\Facades\Schema;

class AlokasiController extends Controller
{
    /**
     * Tampilkan halaman alokasi
     */
    public function index()
    {
        // Ambil data untuk dropdown
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelass = Kelas::with('jurusan')->orderBy('Nama_Kelas')->get();
        
        // Tambahkan informasi jurusan untuk setiap kelas (pastikan data lengkap untuk JavaScript)
        $kelass = $kelass->map(function ($kelas) {
            // Pastikan atribut Kode_Jurusan tersedia untuk digunakan oleh JavaScript
            $kelas->jurusan_nama = optional($kelas->jurusan)->Nama_Jurusan;
            return $kelas;
        });
        
        // Log data untuk debugging
        Log::info('Jurusan data structure:', $jurusans->toArray());
        Log::info('Kelas data structure:', $kelass->toArray());
        
        // Ambil data siswa yang belum dialokasi
        $siswas = Siswa::with(['detailSiswa', 'detailSiswa.kelas', 'detailSiswa.kelas.jurusan'])
            ->orderBy('nama_siswa')
            ->paginate(10);
        
        return view('alokasi.index', compact('jurusans', 'kelass', 'siswas'));
    }
    
    /**
     * Tampilkan siswa yang belum dialokasi
     */
    public function unallocated()
    {
        // Ambil data untuk dropdown dengan informasi lengkap
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelass = Kelas::with('jurusan')->orderBy('Nama_Kelas')->get();
        
        // Tambahkan informasi jurusan untuk setiap kelas
        $kelass = $kelass->map(function ($kelas) {
            $kelas->jurusan_nama = optional($kelas->jurusan)->Nama_Jurusan;
            return $kelas;
        });
        
        // PERBAIKAN: Gunakan query yang lebih tepat untuk siswa belum dialokasi
        // Ambil siswa yang tidak memiliki detail siswa ATAU memiliki detail siswa tetapi tanpa kelas/jurusan
        $siswas = Siswa::where(function($query) {
                $query->whereDoesntHave('detailSiswa')
                    ->orWhereHas('detailSiswa', function($q) {
                        $q->whereNull('kode_kelas')->orWhereNull('kode_jurusan');
                    });
            })
            ->where('status_aktif', 1) // Tambahkan filter hanya siswa aktif
            ->orderBy('nama_siswa')
            ->paginate(10);
        
        Log::info('Query siswa belum dialokasi', [
            'count' => $siswas->count(),
            'total' => $siswas->total()
        ]);
            
        return view('alokasi.partials.unallocated', compact('jurusans', 'kelass', 'siswas'));
    }
    
    /**
     * Mengalokasikan siswa ke jurusan dan kelas
     */
    public function alokasi(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'kode_jurusan' => 'required|exists:jurusan,kode_jurusan',
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
        ], [
            'id_siswa.required' => 'ID Siswa wajib diisi',
            'id_siswa.exists' => 'ID Siswa tidak valid',
            'kode_jurusan.required' => 'Jurusan wajib dipilih',
            'kode_jurusan.exists' => 'Jurusan tidak valid',
            'kode_kelas.required' => 'Kelas wajib dipilih',
            'kode_kelas.exists' => 'Kelas tidak valid',
        ]);

        if ($validator->fails()) {
            Log::warning('Validasi gagal:', ['errors' => $validator->errors()->all()]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . implode(', ', $validator->errors()->all()));
        }

        // Cek apakah kelas sesuai dengan jurusan
        $kelas = Kelas::where('Kode_Kelas', $request->kode_kelas)->first();
        
        if (!$kelas || strtoupper($kelas->Kode_Jurusan) !== strtoupper($request->kode_jurusan)) {
            Log::error("Kelas tidak sesuai dengan jurusan", [
                'kelas_jurusan' => $kelas ? $kelas->Kode_Jurusan : 'Kelas tidak ditemukan',
                'request_jurusan' => $request->kode_jurusan
            ]);
            return redirect()->back()
                ->with('error', 'Kelas yang dipilih tidak sesuai dengan jurusan')
                ->withInput();
        }

        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Ambil data siswa
            $siswa = Siswa::findOrFail($request->id_siswa);
            
            // Generate ID siswa baru DENGAN format jurusan
            $newIdSiswa = $this->getNextSequenceId(true, $request->kode_jurusan);
            
            // Simpan ID siswa lama untuk log
            $oldIdSiswa = $siswa->id_siswa;
            
            // Update ID siswa
            $siswa->id_siswa = $newIdSiswa;
            $siswa->save();
            
            Log::info('ID Siswa berhasil diupdate', [
                'old_id' => $oldIdSiswa,
                'new_id' => $newIdSiswa
            ]);
            
            // Cek apakah siswa sudah memiliki detail
            $detailSiswa = DetailSiswa::where('id_siswa', $oldIdSiswa)->first();
            
            // Simpan kelas lama (jika ada) untuk update jumlah siswa
            $kelasLama = null;
            if ($detailSiswa) {
                $kelasLama = $detailSiswa->kode_kelas;
                // Update referensi ke ID siswa yang baru
                $detailSiswa->id_siswa = $newIdSiswa;
                $detailSiswa->kode_jurusan = $request->kode_jurusan;
                $detailSiswa->kode_kelas = $request->kode_kelas;
                $detailSiswa->save();
                
                Log::info('Detail siswa sudah ada, telah diupdate.', [
                    'id_siswa' => $newIdSiswa,
                    'kelas_lama' => $kelasLama,
                    'kelas_baru' => $request->kode_kelas
                ]);
            } else {
                // Buat detail siswa baru dengan ID yang diambil dari sequence
                $idDetailSiswa = $this->getNextDetailSiswaId();
                
                // Buat detail siswa baru
                $newDetail = DetailSiswa::create([
                    'id_detsiswa' => $idDetailSiswa,
                    'id_siswa' => $newIdSiswa,
                    'kode_jurusan' => $request->kode_jurusan,
                    'kode_kelas' => $request->kode_kelas,
                ]);
                
                Log::info('Detail siswa baru berhasil dibuat', [
                    'id_detsiswa' => $idDetailSiswa,
                    'id_siswa' => $newIdSiswa,
                    'kode_jurusan' => $request->kode_jurusan,
                    'kode_kelas' => $request->kode_kelas
                ]);
            }
            
            // Update jumlah siswa di kelas baru
            $this->updateJumlahSiswa($request->kode_kelas);
            
            // Update jumlah siswa di kelas lama jika berbeda
            if ($kelasLama && $kelasLama != $request->kode_kelas) {
                $this->updateJumlahSiswa($kelasLama);
            }
            
            // Update semua referensi ke ID siswa lama di tabel lain yang terkait
            $this->updateRelatedTables($oldIdSiswa, $newIdSiswa);
            
            // Commit transaction
            DB::commit();
            
            $message = "Siswa {$siswa->nama_siswa} berhasil dialokasikan ke kelas {$kelas->Nama_Kelas}. ID diperbarui dari {$oldIdSiswa} menjadi {$newIdSiswa}";
            
            return redirect()->back()->with('success', $message);
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error dengan lebih detail
            Log::error('Error saat mengalokasikan siswa: ' . $e->getMessage(), [
                'id_siswa' => $request->id_siswa,
                'kode_kelas' => $request->kode_kelas,
                'kode_jurusan' => $request->kode_jurusan,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengalokasikan siswa: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Alokasi siswa secara massal
     */
    public function allocateMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected_siswa' => 'required|array',
            'selected_siswa.*' => 'exists:siswas,id_siswa',
            'kode_jurusan' => 'required|exists:jurusan,kode_jurusan',
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
        ]);
        
        if ($validator->fails()) {
            Log::error("Validasi gagal dalam alokasi massal", [
                "errors" => $validator->errors()->toArray()
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Cek apakah kelas sesuai dengan jurusan
        $kelas = Kelas::where('Kode_Kelas', $request->kode_kelas)->first();
        
        if (!$kelas || strtoupper($kelas->Kode_Jurusan) !== strtoupper($request->kode_jurusan)) {
            Log::error("Kelas tidak sesuai dengan jurusan dalam alokasi massal", [
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
            $kelasesYangPerluUpdate = [];
            
            foreach ($request->selected_siswa as $id_siswa) {
                $siswa = Siswa::find($id_siswa);
                
                if (!$siswa) {
                    Log::warning("Siswa dengan ID {$id_siswa} tidak ditemukan");
                    continue;
                }
                
                // Simpan ID siswa lama
                $oldIdSiswa = $siswa->id_siswa;
                
                // Generate ID siswa baru dengan format jurusan
                $newIdSiswa = $this->getNextSequenceId(true, $request->kode_jurusan);
                
                // Update ID siswa
                $siswa->id_siswa = $newIdSiswa;
                $siswa->save();
                
                // Cek apakah siswa sudah memiliki detail
                $detailSiswa = DetailSiswa::where('id_siswa', $oldIdSiswa)->first();
                
                if ($detailSiswa) {
                    $kelasLama = $detailSiswa->kode_kelas;
                    // Tambahkan kelas lama ke daftar yang perlu diupdate
                    if (!in_array($kelasLama, $kelasesYangPerluUpdate)) {
                        $kelasesYangPerluUpdate[] = $kelasLama;
                    }
                    
                    // Update referensi ke ID siswa yang baru
                    $detailSiswa->id_siswa = $newIdSiswa;
                    $detailSiswa->kode_jurusan = $request->kode_jurusan;
                    $detailSiswa->kode_kelas = $request->kode_kelas;
                    $detailSiswa->save();
                } else {
                    // Buat detail siswa baru
                    $idDetailSiswa = $this->getNextDetailSiswaId();
                    
                    $newDetail = DetailSiswa::create([
                        'id_detsiswa' => $idDetailSiswa,
                        'id_siswa' => $newIdSiswa,
                        'kode_jurusan' => $request->kode_jurusan,
                        'kode_kelas' => $request->kode_kelas,
                    ]);
                }
                
                // Update semua referensi ke ID siswa lama di tabel lain
                $this->updateRelatedTables($oldIdSiswa, $newIdSiswa);
                
                // Tambahkan ke array perubahan ID
                $updatedIds[] = [
                    'name' => $siswa->nama_siswa,
                    'old' => $oldIdSiswa,
                    'new' => $newIdSiswa,
                ];
                
                $successCount++;
            }
            
            // Update jumlah siswa di kelas baru
            $this->updateJumlahSiswa($request->kode_kelas);
            
            // Update jumlah siswa di semua kelas lama
            foreach ($kelasesYangPerluUpdate as $kelasLama) {
                $this->updateJumlahSiswa($kelasLama);
            }
            
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
            Log::error("Error dalam alokasi siswa massal: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()
                ->with('error', 'Gagal mengalokasikan siswa secara massal: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Kembalikan siswa dari alokasi (hapus dari jurusan & kelas)
     */
    public function kembalikan(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswas,id_siswa',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        $id_siswa = $request->id_siswa;
        
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
        // Ambil data untuk dropdown dengan memastikan semua informasi tersedia untuk JavaScript
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelass = Kelas::with('jurusan')->orderBy('Nama_Kelas')->get();
        
        // Tambahkan informasi jurusan untuk setiap kelas
        $kelass = $kelass->map(function ($kelas) {
            $kelas->jurusan_nama = optional($kelas->jurusan)->Nama_Jurusan;
            return $kelas;
        });
        
        // PERBAIKAN: Gunakan nama tabel yang benar sesuai dengan model
        $query = Siswa::join('detail_siswas', 'siswas.id_siswa', '=', 'detail_siswas.id_siswa')
                      ->join('kelas', 'detail_siswas.kode_kelas', '=', 'kelas.Kode_Kelas')
                      ->join('jurusan', 'detail_siswas.kode_jurusan', '=', 'jurusan.Kode_Jurusan')
                      ->select('siswas.*', 'detail_siswas.kode_jurusan', 'detail_siswas.kode_kelas', 
                               'kelas.Nama_Kelas', 'jurusan.Nama_Jurusan');
                               
        // Filter berdasarkan jurusan jika ada
        if ($request->has('jurusan') && !empty($request->jurusan)) {
            $query->where('detail_siswas.kode_jurusan', $request->jurusan);
            
            // Log untuk debugging filter jurusan
            Log::info('Filtering by jurusan:', ['jurusan' => $request->jurusan]);
        }
        
        // Filter berdasarkan kelas jika ada
        if ($request->has('kelas') && !empty($request->kelas)) {
            $query->where('detail_siswas.kode_kelas', $request->kelas);
            
            // Log untuk debugging filter kelas
            Log::info('Filtering by kelas:', ['kelas' => $request->kelas]);
        }
        
        // Filter berdasarkan nama siswa jika ada
        if ($request->has('nama') && !empty($request->nama)) {
            $query->where('siswas.nama_siswa', 'like', '%' . $request->nama . '%');
            
            // Log untuk debugging filter nama
            Log::info('Filtering by nama:', ['nama' => $request->nama]);
        }
        
        $siswas = $query->orderBy('siswas.nama_siswa')->paginate(10);
        
        // Pastikan URL pagination menyertakan parameter filter
        $siswas->appends($request->all());
        
        return view('alokasi.allocated', compact('jurusans', 'kelass', 'siswas'));
    }
    
    /**
     * Menampilkan hasil filter pada alokasi
     */
    public function filter(Request $request)
    {
        // Log informasi request untuk debugging
        Log::info('Filter request received:', $request->all());
        
        return $this->allocated($request);
    }
    
    /**
     * Pindahkan siswa ke kelas lain
     */
    public function pindah(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $id_siswa = $request->id_siswa;
        
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
    
    /**
     * Menampilkan form kenaikan kelas
     *
     * @return \Illuminate\Http\Response
     */
    public function kenaikanForm()
    {
        // Ambil data untuk dropdown
        $jurusans = Jurusan::orderBy('Nama_Jurusan')->get();
        $kelass = Kelas::with('jurusan')->orderBy('Nama_Kelas')->get();
        
        // Tambahkan informasi jurusan untuk setiap kelas
        $kelass = $kelass->map(function ($kelas) {
            $kelas->jurusan_nama = optional($kelas->jurusan)->Nama_Jurusan;
            return $kelas;
        });
        
        // Tambah data tahun ajaran untuk dropdown
        $tahunSekarang = date('Y');
        $tahunAjaran = [];
        for ($i = 0; $i < 5; $i++) {
            $tahun = $tahunSekarang + $i;
            $tahunAjaran[$tahun . '/' . ($tahun + 1)] = $tahun . '/' . ($tahun + 1);
        }
        
        return view('alokasi.kenaikan', compact('jurusans', 'kelass', 'tahunAjaran'));
    }

    /**
     * Proses kenaikan kelas massal
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function prosesKenaikanKelas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_asal' => 'required|exists:kelas,kode_kelas',
            'kelas_tujuan' => 'required|exists:kelas,kode_kelas',
            'tahun_ajaran' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Ambil data kelas
        $kelasAsal = Kelas::findOrFail($request->kelas_asal);
        $kelasTujuan = Kelas::findOrFail($request->kelas_tujuan);
        
        // Cek apakah kelas tujuan memiliki jurusan yang berbeda dengan kelas asal
        $gantijurusan = strtoupper($kelasAsal->Kode_Jurusan) !== strtoupper($kelasTujuan->Kode_Jurusan);
        
        // PERBAIKAN: Gunakan nama tabel yang benar
        // Ambil semua siswa dari kelas asal dengan status aktif
        $siswaDiKelasAsal = DetailSiswa::where('kode_kelas', $request->kelas_asal)
            ->join('siswas', 'detail_siswas.id_siswa', '=', 'siswas.id_siswa')
            ->where('siswas.status_aktif', 1)
            ->select('detail_siswas.*', 'siswas.nama_siswa')
            ->get();
        
        if ($siswaDiKelasAsal->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa aktif di kelas asal');
        }
        
        DB::beginTransaction();
        try {
            $successCount = 0;
            $updatedIds = [];
            
            foreach ($siswaDiKelasAsal as $detailSiswa) {
                // Ambil data siswa
                $siswa = Siswa::find($detailSiswa->id_siswa);
                if (!$siswa) continue;
                
                $idLama = $siswa->id_siswa;
                
                // Jika ganti jurusan, update ID siswa
                if ($gantijurusan) {
                    // Generate ID baru dengan format jurusan tujuan
                    $idBaru = $this->getNextSequenceId(true, $kelasTujuan->Kode_Jurusan);
                    
                    // Update ID siswa
                    $siswa->id_siswa = $idBaru;
                    $siswa->save();
                    
                    // Update referensi di detail siswa
                    $detailSiswa->id_siswa = $idBaru;
                    
                    // Catat perubahan ID
                    $updatedIds[] = [
                        'name' => $siswa->nama_siswa,
                        'old' => $idLama,
                        'new' => $idBaru,
                    ];
                    
                    // Update referensi di tabel lain
                    $this->updateRelatedTables($idLama, $idBaru);
                }
                
                // Ubah kelas dan jurusan pada detail siswa
                $detailSiswa->kode_kelas = $request->kelas_tujuan;
                $detailSiswa->kode_jurusan = $kelasTujuan->Kode_Jurusan;
                $detailSiswa->save();
                
                $successCount++;
            }
            
            // Update jumlah siswa di kelas asal dan tujuan
            $this->updateJumlahSiswa($request->kelas_asal);
            $this->updateJumlahSiswa($request->kelas_tujuan);
            
            // Update tahun ajaran kelas tujuan jika diperlukan
            if ($request->filled('update_tahun_ajaran') && $request->update_tahun_ajaran) {
                $kelasTujuan->Tahun_Ajaran = $request->tahun_ajaran;
                $kelasTujuan->save();
            }
            
            DB::commit();
            
            // Buat pesan sukses
            $message = "{$successCount} siswa berhasil dinaikkan dari kelas {$kelasAsal->Nama_Kelas} ke {$kelasTujuan->Nama_Kelas}.";
            
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
           
           return redirect()->route('alokasi.index')->with('success', $message);
           
       } catch (\Exception $e) {
           DB::rollback();
           Log::error("Error dalam proses kenaikan kelas: " . $e->getMessage(), [
               'exception' => $e
           ]);
           return redirect()->back()
               ->with('error', 'Gagal melakukan kenaikan kelas: ' . $e->getMessage())
               ->withInput();
       }
   }
   
   /**
    * Menampilkan form untuk lulus/alumni
    *
    * @return \Illuminate\Http\Response
    */
   public function lulusForm()
   {
       // Ambil data untuk dropdown
       $kelass = Kelas::with('jurusan')->orderBy('Nama_Kelas')->get();
       
       // Tambahkan informasi jurusan untuk setiap kelas
       $kelass = $kelass->map(function ($kelas) {
           $kelas->jurusan_nama = optional($kelas->jurusan)->Nama_Jurusan;
           return $kelas;
       });
       
       return view('alokasi.lulus', compact('kelass'));
   }
   
   /**
    * Proses kelulusan siswa massal
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function prosesKelulusan(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'kelas_id' => 'required|exists:kelas,kode_kelas',
           'tanggal_lulus' => 'required|date',
       ]);

       if ($validator->fails()) {
           return redirect()->back()->withErrors($validator)->withInput();
       }

       // Ambil data kelas
       $kelas = Kelas::findOrFail($request->kelas_id);
       
       // PERBAIKAN: Gunakan nama tabel yang benar
       // Ambil semua siswa dari kelas dengan status aktif
       $siswaDiKelas = DetailSiswa::where('kode_kelas', $request->kelas_id)
           ->join('siswas', 'detail_siswas.id_siswa', '=', 'siswas.id_siswa')
           ->where('siswas.status_aktif', 1)
           ->select('detail_siswas.*', 'siswas.id_siswa')
           ->get();
       
       if ($siswaDiKelas->isEmpty()) {
           return redirect()->back()->with('error', 'Tidak ada siswa aktif di kelas yang dipilih');
       }
       
       DB::beginTransaction();
       try {
           $successCount = 0;
           
           foreach ($siswaDiKelas as $detailSiswa) {
               // Ambil data siswa
               $siswa = Siswa::find($detailSiswa->id_siswa);
               if (!$siswa) continue;
               
               // Ubah status siswa menjadi tidak aktif (lulus/alumni)
               $siswa->status_aktif = 0;
               $siswa->tanggal_lulus = $request->tanggal_lulus;
               $siswa->save();
               
               $successCount++;
           }
           
           // Update jumlah siswa di kelas
           $this->updateJumlahSiswa($request->kelas_id);
           
           DB::commit();
           
           return redirect()->route('alokasi.index')
               ->with('success', "{$successCount} siswa dari kelas {$kelas->Nama_Kelas} berhasil diluluskan.");
           
       } catch (\Exception $e) {
           DB::rollback();
           Log::error("Error dalam proses kelulusan: " . $e->getMessage(), [
               'exception' => $e
           ]);
           return redirect()->back()
               ->with('error', 'Gagal melakukan proses kelulusan: ' . $e->getMessage())
               ->withInput();
       }
   }
   
   /**
    * Tabel untuk menyimpan sequence ID
    * Jika belum ada, buat tabel dengan struktur:
    * - CREATE TABLE sequence_ids (sequence_name VARCHAR(30) PRIMARY KEY, current_value INT NOT NULL);
    * - INSERT INTO sequence_ids VALUES ('siswa_id', 0);
    * 
    * @param bool $withJurusan Jika true, gunakan format dengan jurusan (6A25001), jika false gunakan format tanpa jurusan (625001)
    * @param string $kodeJurusan Kode jurusan (A, B, C, dll) - hanya digunakan jika $withJurusan = true
    * @return string ID Siswa yang dihasilkan dengan format yang sesuai
    */
   private function getNextSequenceId($withJurusan = false, $kodeJurusan = null)
{
    try {
        // Mulai transaction
        DB::beginTransaction();
        
        $tahun = date('y'); // Tahun 2 digit (25 untuk 2025)
        
        if ($withJurusan && $kodeJurusan) {
            // Untuk siswa dengan jurusan, gunakan sequence terpisah per jurusan
            $sequenceName = "siswa_jurusan_{$kodeJurusan}";
            
            // Ambil nilai sequence untuk jurusan ini
            $sequence = DB::table('sequence_ids')
                ->where('sequence_name', $sequenceName)
                ->lockForUpdate()
                ->first();
            
            // Jika sequence belum ada untuk jurusan ini, buat baru dimulai dari 0
            if (!$sequence) {
                DB::table('sequence_ids')->insert([
                    'sequence_name' => $sequenceName,
                    'current_value' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $currentValue = 0;
            } else {
                $currentValue = $sequence->current_value;
            }
            
            // Increment nilai sequence
            $nextValue = $currentValue + 1;
            
            // Update nilai sequence
            DB::table('sequence_ids')
                ->where('sequence_name', $sequenceName)
                ->update([
                    'current_value' => $nextValue,
                    'updated_at' => now()
                ]);
            
            // Format ID dengan jurusan: 6 + kode jurusan + tahun (yy) + nomor urut (3 digit)
            $formattedId = "6{$kodeJurusan}{$tahun}" . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
            
        } else {
            // Untuk siswa tanpa jurusan, gunakan sequence biasa
            $sequenceName = 'siswa_id';
            
            $sequence = DB::table('sequence_ids')
                ->where('sequence_name', $sequenceName)
                ->lockForUpdate()
                ->first();
            
            if (!$sequence) {
                // Cari ID siswa terakhir untuk menentukan nilai awal
                $maxId = Siswa::where('id_siswa', 'like', "6{$tahun}%")
                    ->where('id_siswa', 'not like', "6__{$tahun}%") // Exclude yang ada kode jurusan
                    ->max('id_siswa');
                
                if ($maxId && is_numeric(substr($maxId, 3))) {
                    $currentValue = (int)substr($maxId, 3);
                } else {
                    $currentValue = 0;
                }
                
                DB::table('sequence_ids')->insert([
                    'sequence_name' => $sequenceName,
                    'current_value' => $currentValue,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $currentValue = $sequence->current_value;
            }
            
            // Increment nilai sequence
            $nextValue = $currentValue + 1;
            
            // Update nilai sequence
            DB::table('sequence_ids')
                ->where('sequence_name', $sequenceName)
                ->update([
                    'current_value' => $nextValue,
                    'updated_at' => now()
                ]);
            
            // Format ID tanpa jurusan: 6 + tahun (yy) + nomor urut (3 digit)
            $formattedId = "6{$tahun}" . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
        }
        
        // Commit transaction
        DB::commit();
        
        Log::info('Generated next sequence ID', [
            'id' => $formattedId,
            'sequence_value' => $nextValue,
            'with_jurusan' => $withJurusan,
            'kode_jurusan' => $kodeJurusan,
            'sequence_name' => $sequenceName ?? 'siswa_id'
        ]);
        
        return $formattedId;
        
    } catch (\Exception $e) {
        // Rollback transaction jika terjadi error
        DB::rollBack();
        
        Log::error('Error generating sequence ID: ' . $e->getMessage());
        
        // Fallback method
        $tahun = date('y');
        
        if ($withJurusan && $kodeJurusan) {
            // Fallback untuk format dengan jurusan
            $lastId = Siswa::where('id_siswa', 'like', "6{$kodeJurusan}{$tahun}%")
                ->orderBy('id_siswa', 'desc')
                ->first();
            
            if ($lastId) {
                $lastNumber = intval(substr($lastId->id_siswa, -3));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            return "6{$kodeJurusan}{$tahun}" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
        } else {
            // Fallback untuk format tanpa jurusan
            $lastId = Siswa::where('id_siswa', 'like', "6{$tahun}%")
                ->where('id_siswa', 'not like', "6__{$tahun}%") // Exclude yang ada kode jurusan
                ->orderBy('id_siswa', 'desc')
                ->first();
            
            if ($lastId) {
                $lastNumber = intval(substr($lastId->id_siswa, 3));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            return "6{$tahun}" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
    }
}
   
   /**
    * Mendapatkan ID DetailSiswa berikutnya dari sequence
    * 
    * @return string ID DetailSiswa dengan format DS001, DS002, dst.
    */
   private function getNextDetailSiswaId()
   {
       try {
           // Mulai transaction
           DB::beginTransaction();
           
           // Ambil nilai sequence saat ini dalam lock
           $sequence = DB::table('sequence_ids')
               ->where('sequence_name', 'detsiswa_id')
               ->lockForUpdate()
               ->first();
           
           // Jika sequence belum ada, buat baru
           if (!$sequence) {
               DB::table('sequence_ids')->insert([
                   'sequence_name' => 'detsiswa_id',
                   'current_value' => 0
               ]);
               $currentValue = 0;
           } else {
               $currentValue = $sequence->current_value;
           }
           
           // Increment nilai sequence
           $nextValue = $currentValue + 1;
           
           // Update nilai sequence
           DB::table('sequence_ids')
               ->where('sequence_name', 'detsiswa_id')
               ->update(['current_value' => $nextValue]);
           
           // Format ID: DS + nomor urut (3 digit)
           $formattedId = "DS" . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
           
           // Commit transaction
           DB::commit();
           
           Log::info('Generated next detail siswa ID', [
               'id' => $formattedId, 
               'sequence_value' => $nextValue
           ]);
           
           return $formattedId;
           
       } catch (\Exception $e) {
           // Rollback transaction jika terjadi error
           DB::rollBack();
           
           // Log error
           Log::error('Error generating detail siswa ID: ' . $e->getMessage());
           
           // Fallback ke metode lama jika terjadi error
           $lastDetailSiswa = DetailSiswa::orderBy('id_detsiswa', 'desc')->first();
           $lastNumber = 0;
           
           if ($lastDetailSiswa) {
               preg_match('/DS(\d+)/', $lastDetailSiswa->id_detsiswa, $matches);
               if (isset($matches[1])) {
                   $lastNumber = (int)$matches[1];
               }
           }
           
           $nextNumber = $lastNumber + 1;
           return 'DS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
       }
   }
   
   /**
    * Update jumlah siswa di kelas
    */
   private function updateJumlahSiswa($kodeKelas)
   {
       // PERBAIKAN: Gunakan nama tabel yang benar sesuai dengan model
       // Hitung jumlah siswa dalam kelas
       $jumlahSiswa = DetailSiswa::where('kode_kelas', $kodeKelas)
           ->join('siswas', 'detail_siswas.id_siswa', '=', 'siswas.id_siswa')
           ->where('siswas.status_aktif', 1)
           ->count();
       
       // Update jumlah siswa dalam tabel kelas
       Kelas::where('Kode_Kelas', $kodeKelas)->update([
           'Jumlah_Siswa' => $jumlahSiswa
       ]);
       
       Log::info("Updated Jumlah_Siswa for kelas {$kodeKelas} to {$jumlahSiswa}");
       
       return $jumlahSiswa;
   }
   
   /**
    * Update semua referensi ke ID siswa lama di tabel lain yang terkait
    * 
    * @param string $oldId ID siswa lama
    * @param string $newId ID siswa baru
    * @return void
    */
   private function updateRelatedTables($oldId, $newId)
   {
       try {
           // List semua tabel yang memiliki referensi ke id_siswa
           $tables = [
               'detail_siswas' => 'id_siswa',
               'orang_tuas' => 'id_siswa',
               'prestasis' => 'id_siswa',
               'pembayarans' => 'id_siswa',
               'rekam_medis' => 'id_siswa',
               'detail_pemeriksaan' => 'id_siswa',
               'pemeriksaan_harian' => 'id_siswa',
               'pemeriksaan_awal' => 'id_siswa',
               'pemeriksaan_fisik' => 'id_siswa',
               'resep' => 'id_siswa',
               // Tambahkan tabel lain jika diperlukan
           ];
           
           foreach ($tables as $table => $column) {
               // Cek apakah tabel ada
               if (Schema::hasTable($table)) {
                   // Update ID siswa di tabel terkait
                   $updated = DB::table($table)
                       ->where($column, $oldId)
                       ->update([$column => $newId]);
                   
                   Log::info("Update referensi di tabel $table", [
                       'old_id' => $oldId,
                       'new_id' => $newId,
                       'rows_updated' => $updated
                   ]);
               }
           }
       } catch (\Exception $e) {
           Log::error('Error updating related tables: ' . $e->getMessage(), [
               'old_id' => $oldId,
               'new_id' => $newId,
               'trace' => $e->getTraceAsString()
           ]);
           throw $e;
       }
   }
}