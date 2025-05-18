<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        // Query dasar dengan eager loading relasi yang lebih spesifik
        $query = Siswa::with(['detailSiswa', 'detailSiswa.kelas', 'detailSiswa.kelas.jurusan']);
        
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
            
            // Debug info - tampilkan di log
            if ($siswa->detailSiswa) {
                Log::info('Detail Siswa ditemukan untuk: ' . $siswa->id_siswa, [
                    'kode_jurusan' => $siswa->detailSiswa->kode_jurusan ?? 'tidak ada',
                    'kode_kelas' => $siswa->detailSiswa->kode_kelas ?? 'tidak ada',
                    'has_kelas' => ($siswa->detailSiswa->kelas) ? 'Ya' : 'Tidak',
                    'has_jurusan' => ($siswa->detailSiswa->kelas && $siswa->detailSiswa->kelas->jurusan) ? 'Ya' : 'Tidak'
                ]);
            } else {
                Log::info('Tidak ada DetailSiswa untuk: ' . $siswa->id_siswa);
            }
        }
        
        // Menyiapkan data untuk dropdown filter dan alokasi
        $tahunMasuk = Siswa::selectRaw('YEAR(tanggal_masuk) as tahun')
            ->distinct()
            ->whereNotNull('tanggal_masuk')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        
        // Ambil data jurusan dan kelas untuk modal alokasi
        $jurusans = Jurusan::all();
        $kelas = Kelas::with('jurusan')->get();
            
        return view('siswa.index', compact('siswas', 'tahunMasuk', 'jurusans', 'kelas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Generate ID berikutnya TANPA jurusan (format: 625001)
        $nextId = $this->getNextSequenceId(false);
        
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
            'status_aktif' => 'required|boolean',
        ], [
            'id_siswa.unique' => 'ID Siswa sudah digunakan',
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh lebih dari hari ini',
            'status_aktif.required' => 'Status siswa wajib dipilih',
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Persiapkan data siswa
            $siswaData = $request->all();
            
            // Pastikan nilai default untuk status_aktif
            if (!isset($siswaData['status_aktif'])) {
                $siswaData['status_aktif'] = 1; // Default: Aktif
            }
            
            // Timestamps akan diatur otomatis oleh Laravel jika $timestamps = true di model
            
            // Buat siswa baru
            $siswa = Siswa::create($siswaData);
            
            // Commit transaction
            DB::commit();
            
            // Redirect ke index, bukan show
            return redirect()->route('siswa.index')
                ->with('success', 'Siswa berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error saat menambahkan siswa: ' . $e->getMessage());
            
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
                'detailSiswa', 
                'detailSiswa.kelas', 
                'detailSiswa.kelas.jurusan',
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
            'status_aktif' => 'required|boolean',
        ], [
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh lebih dari hari ini',
            'status_aktif.required' => 'Status siswa wajib dipilih',
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
            
            // Persiapkan data untuk update
            $updateData = $request->all();
            
            // Timestamps akan diperbarui otomatis oleh Laravel jika $timestamps = true di model
            
            // Update siswa dengan data yang sudah disiapkan
            $siswa->update($updateData);
            
            // Commit transaction
            DB::commit();
            
            // Redirect ke index, bukan show
            return redirect()->route('siswa.index')
                ->with('success', 'Data siswa berhasil diperbarui.');
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error saat memperbarui siswa: ' . $e->getMessage());
            
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
            
            // Log error
            Log::error('Error saat menghapus siswa: ' . $e->getMessage());
            
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan. Siswa gagal dihapus: ' . $e->getMessage());
        }
    }
    
    /**
     * Show form for importing data
     *
     * @return \Illuminate\Http\Response
     */
    public function importForm()
    {
        return view('siswa.import');
    }
    
    /**
     * Export data siswa ke format Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        try {
            // Buat query dasar
            $query = Siswa::query();
            
            // Terapkan filter jika ada
            if ($request->filled('status')) {
                $query->where('status_aktif', $request->status);
            }
            
            if ($request->filled('jenis_kelamin')) {
                $query->where('jenis_kelamin', $request->jenis_kelamin);
            }
            
            if ($request->filled('tahun_masuk')) {
                $query->whereYear('tanggal_masuk', $request->tahun_masuk);
            }
            
            if ($request->filled('keyword')) {
                $keyword = '%' . $request->keyword . '%';
                $query->where(function($q) use ($keyword) {
                    $q->where('id_siswa', 'like', $keyword)
                    ->orWhere('nama_siswa', 'like', $keyword)
                    ->orWhere('tempat_lahir', 'like', $keyword);
                });
            }
            
            // Pengurutan
            $sortBy = $request->input('sort_by', 'nama_siswa');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Ambil data sesuai filter
            $siswas = $query->get();
            
            // Jika tidak ada data yang akan diekspor
            if ($siswas->isEmpty()) {
                return redirect()->route('siswa.index')
                    ->with('warning', 'Tidak ada data siswa yang ditemukan untuk diekspor.');
            }
            
            // Ekspor data dengan nama file yang dinamis
            $filename = 'Data_Siswa_' . date('d-m-Y_H-i-s') . '.xlsx';
            
            // Log informasi ekspor
            Log::info('Mulai ekspor data siswa', [
                'count' => $siswas->count(),
                'filename' => $filename,
                'filters' => $request->all()
            ]);
            
            // Perbaikan: Gunakan Excel::download tanpa chaining setUseDiskCaching
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\SiswaExport($siswas),
                $filename
            );
            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Tangani error validasi Excel
            Log::error('Error validasi saat export data: ' . $e->getMessage());
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan validasi saat export data.');
                
        } catch (\Exception $e) {
            // Log error dengan detail
            Log::error('Error saat export data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
    
    /**
     * Process import from Excel file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importProcess(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Maks 5MB
        ], [
            'file.required' => 'File import tidak boleh kosong',
            'file.file' => 'Data harus berupa file',
            'file.mimes' => 'Format file harus xlsx, xls, atau csv',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.import')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Log awal proses import
            Log::info('Mulai proses import data siswa');
            
            $file = $request->file('file');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Ambil header dan cek format header
            $header = $rows[0];
            $expectedHeader = ['ID_SISWA', 'NAMA_SISWA', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR', 
                            'JENIS_KELAMIN', 'TANGGAL_MASUK', 'STATUS_AKTIF'];
            
            // Validasi header dengan toleransi huruf besar/kecil
            $validHeader = true;
            if (count($header) < count($expectedHeader)) {
                $validHeader = false;
            } else {
                for ($i = 0; $i < count($expectedHeader); $i++) {
                    if (strtoupper(trim($header[$i])) != $expectedHeader[$i]) {
                        $validHeader = false;
                        break;
                    }
                }
            }
            
            if (!$validHeader) {
                Log::error('Format header tidak sesuai', [
                    'expected' => $expectedHeader,
                    'actual' => $header
                ]);
                return redirect()->route('siswa.import')
                    ->with('error', 'Format header tidak sesuai template. Silahkan download template terlebih dahulu.');
            }
            
            // Mulai transaction database
            DB::beginTransaction();
            
            // Count untuk statistik
            $totalRows = count($rows) - 1; // Kurangi header
            $successCount = 0;
            $errorRows = [];
            
            // Proses setiap baris data (mulai dari index 1, setelah header)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip jika baris kosong atau nama siswa kosong
                if (empty($row[1])) {
                    continue;
                }
                
                try {
                    // Format tanggal dari DD-MM-YYYY ke YYYY-MM-DD untuk database
                    $tanggalLahir = null;
                    if (!empty($row[3])) {
                        $dateParts = explode('-', $row[3]);
                        if (count($dateParts) === 3) {
                            $tanggalLahir = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                        }
                    }
                    
                    $tanggalMasuk = null;
                    if (!empty($row[5])) {
                        $dateParts = explode('-', $row[5]);
                        if (count($dateParts) === 3) {
                            $tanggalMasuk = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                        }
                    }
                    
                    // Status aktif default = 1 jika tidak diisi
                    $statusAktif = isset($row[6]) ? $row[6] : 1;
                    
                    // Jika ID kosong, generate ID otomatis TANPA format jurusan
                    $idSiswa = !empty($row[0]) ? trim($row[0]) : $this->getNextSequenceId(false);
                    
                    // Cek apakah ID sudah ada
                    $existingSiswa = Siswa::find($idSiswa);
                    
                    if ($existingSiswa) {
                        // Update data siswa yang sudah ada
                        $existingSiswa->nama_siswa = $row[1];
                        $existingSiswa->tempat_lahir = $row[2] ?? null;
                        $existingSiswa->tanggal_lahir = $tanggalLahir;
                        $existingSiswa->jenis_kelamin = $row[4] ?? null;
                        $existingSiswa->tanggal_masuk = $tanggalMasuk;
                        $existingSiswa->status_aktif = $statusAktif;
                        $existingSiswa->save();
                        
                        Log::info('Data siswa berhasil diupdate', ['id_siswa' => $idSiswa]);
                    } else {
                        // Buat siswa baru
                        $siswa = new Siswa();
                        $siswa->id_siswa = $idSiswa;
                        $siswa->nama_siswa = $row[1];
                        $siswa->tempat_lahir = $row[2] ?? null;
                        $siswa->tanggal_lahir = $tanggalLahir;
                        $siswa->jenis_kelamin = $row[4] ?? null;
                        $siswa->tanggal_masuk = $tanggalMasuk;
                        $siswa->status_aktif = $statusAktif;
                        $siswa->save();
                        
                        Log::info('Siswa baru berhasil ditambahkan', ['id_siswa' => $idSiswa]);
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    // Catat baris yang error
                    $errorRows[] = [
                        'row' => $i + 1,
                        'data' => implode(', ', array_filter($row)),
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Error saat memproses baris ke-' . ($i + 1), [
                        'data' => $row,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Jika ada error pada beberapa baris, tapi tidak semua
            if (count($errorRows) > 0 && $successCount > 0) {
                DB::commit(); // Commit data yang berhasil
                
                // Siapkan pesan error
                $errorMessage = "Berhasil import {$successCount} dari {$totalRows} data. Terdapat " . count($errorRows) . " baris yang gagal diimport:";
                foreach ($errorRows as $index => $error) {
                    if ($index < 5) { // Batasi hanya 5 error yang ditampilkan
                        $errorMessage .= "<br>- Baris {$error['row']}: {$error['error']}";
                    } else {
                        $errorMessage .= "<br>- ... dan " . (count($errorRows) - 5) . " error lainnya";
                        break;
                    }
                }
                
                return redirect()->route('siswa.index')
                    ->with('warning', $errorMessage);
            } 
            // Jika semua baris error
            else if (count($errorRows) > 0 && $successCount == 0) {
                DB::rollBack();
                return redirect()->route('siswa.import')
                    ->with('error', 'Import gagal. Tidak ada data yang berhasil diimport. Error: ' . $errorRows[0]['error']);
            } 
            // Jika semua berhasil
            else if ($successCount > 0) {
                DB::commit();
                Log::info('Import data siswa berhasil', ['total' => $successCount]);
                return redirect()->route('siswa.index')
                    ->with('success', "Berhasil import {$successCount} data siswa.");
            }
            // Jika tidak ada data yang diproses
            else {
                DB::rollBack();
                return redirect()->route('siswa.import')
                    ->with('error', 'Import gagal. Tidak ada data yang valid dalam file Excel.');
            }
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error dengan detail
            Log::error('Error saat import data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('siswa.import')
                ->with('error', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
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
            
            // Ambil nilai sequence saat ini dalam lock
            $sequence = DB::table('sequence_ids')
                ->where('sequence_name', 'siswa_id')
                ->lockForUpdate()
                ->first();
            
            // Jika sequence belum ada, buat baru
            if (!$sequence) {
                DB::table('sequence_ids')->insert([
                    'sequence_name' => 'siswa_id',
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
                ->where('sequence_name', 'siswa_id')
                ->update(['current_value' => $nextValue]);
            
            // Format ID sesuai kebutuhan:
            $tahun = date('y');
            
            if ($withJurusan && $kodeJurusan) {
                // Format dengan jurusan: 6 + kode jurusan + tahun (yy) + nomor urut (3 digit)
                $formattedId = "6{$kodeJurusan}{$tahun}" . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
            } else {
                // Format tanpa jurusan: 6 + tahun (yy) + nomor urut (3 digit) 
                $formattedId = "6{$tahun}" . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
            }
            
            // Commit transaction
            DB::commit();
            
            Log::info('Generated next sequence ID', [
                'id' => $formattedId, 
                'sequence_value' => $nextValue,
                'with_jurusan' => $withJurusan
            ]);
            
            return $formattedId;
            
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error generating sequence ID: ' . $e->getMessage());
            
            // Fallback ke metode lama jika terjadi error
            $tahun = date('y');
            
            if ($withJurusan && $kodeJurusan) {
                // Fallback dengan format jurusan
                $lastId = Siswa::where('id_siswa', 'like', "6{$kodeJurusan}{$tahun}%")
                    ->orderBy('id_siswa', 'desc')
                    ->first();
            } else {
                // Fallback dengan format tanpa jurusan
                $lastId = Siswa::where('id_siswa', 'like', "6{$tahun}%")
                    ->orderBy('id_siswa', 'desc')
                    ->first();
            }
            
            if ($lastId) {
                $lastNumber = intval(substr($lastId->id_siswa, -3));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            if ($withJurusan && $kodeJurusan) {
                return "6{$kodeJurusan}{$tahun}{$formattedNumber}";
            } else {
                return "6{$tahun}{$formattedNumber}";
            }
        }
    }
    
    /**
     * Download template for import
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        try {
            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Template Siswa');
            
            // Header kolom sesuai format yang ditentukan
            $headers = [
                'ID_SISWA', 'NAMA_SISWA', 'TEMPAT_LAHIR', 'TANGGAL_LAHIR', 
                'JENIS_KELAMIN', 'TANGGAL_MASUK', 'STATUS_AKTIF'
            ];
            
            // Set header
            foreach ($headers as $index => $header) {
                $column = chr(65 + $index); // A, B, C, ...
                $sheet->setCellValue($column . '1', $header);
            }
            
            // Format header
            $sheet->getStyle('A1:G1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            
            // Tambahkan contoh data
            $exampleData = [
                ['625001', 'Contoh Nama', 'Jakarta', '01-01-2010', 'L', '15-07-2025', '1'],
            ];
            
            $row = 2;
            foreach ($exampleData as $data) {
                foreach ($data as $index => $value) {
                    $column = chr(65 + $index); // A, B, C, ...
                    $sheet->setCellValue($column . $row, $value);
                }
                $row++;
            }
            
            // Set panjang kolom otomatis
            foreach (range('A', 'G') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Tambahkan keterangan format
            $row = 4;
            $sheet->setCellValue('A' . $row, 'Keterangan:');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $keterangan = [
                'Format ID Siswa awal: 6 + tahun (yy) + nomor urut (001), contoh: 625001',
                'Format ID Siswa setelah alokasi jurusan: 6 + Kode Jurusan + Tahun (yy) + Nomor Urut (001), contoh: 6A25001',
                'Format Tanggal: DD-MM-YYYY (contoh: 01-01-2025)',
                'Jenis Kelamin: L untuk Laki-laki, P untuk Perempuan',
                'Status Aktif: 1 untuk Aktif, 0 untuk Tidak Aktif'
            ];
            
            foreach ($keterangan as $ket) {
                $sheet->setCellValue('A' . $row, $ket);
                $sheet->mergeCells('A' . $row . ':G' . $row);
                $row++;
            }
            
            // Format area keterangan
            $sheet->getStyle('A4:G' . ($row-1))->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DDEBF7']
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '4472C4']
                    ]
                ]
            ]);
            
            // Membuat file Excel
            $writer = new Xlsx($spreadsheet);
            $filename = 'Template_Import_Siswa_' . date('Ymd') . '.xlsx';
            $temp_file = tempnam(sys_get_temp_dir(), $filename);
            $writer->save($temp_file);
            
            // Log berhasil membuat template
            Log::info('Template siswa berhasil dibuat', ['filename' => $filename]);
            
            // Return file untuk di-download
            return response()->download($temp_file, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            // Log error
            Log::error('Error saat membuat template siswa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect dengan pesan error
            return redirect()->route('siswa.import')
                ->with('error', 'Terjadi kesalahan saat membuat template: ' . $e->getMessage());
        }
    }
    
    /**
     * Get jurusan data (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJurusan()
    {
        try {
            $jurusans = Jurusan::select('kode_jurusan', 'nama_jurusan')->get();
            return response()->json($jurusans);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data jurusan: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data jurusan'], 500);
        }
    }
    
    /**
     * Get kelas data by jurusan (API)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKelasByJurusan(Request $request)
    {
        try {
            $kodeJurusan = $request->kode_jurusan;
            
            // Pastikan parameter kode_jurusan ada
            if (!$kodeJurusan) {
                return response()->json(['error' => 'Parameter kode_jurusan diperlukan'], 400);
            }
            
            // Tampilkan detail untuk debugging
            Log::info('Mengambil kelas untuk jurusan:', ['kode_jurusan' => $kodeJurusan]);
            
            // Gunakan parameter yang benar sesuai model Kelas
            $kelas = Kelas::where('Kode_Jurusan', $kodeJurusan)
                    ->select('Kode_Kelas', 'Nama_Kelas', 'Tahun_Ajaran')
                    ->get();
            
            // Tampilkan hasil untuk debugging
            Log::info('Hasil query kelas:', ['count' => $kelas->count(), 'data' => $kelas->toArray()]);
            
            return response()->json($kelas);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data kelas: ' . $e->getMessage(), [
                'kode_jurusan' => $request->kode_jurusan ?? 'tidak ada',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data kelas: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Allocate student to class and jurusan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function alokasi(Request $request)
    {
        // Tambahkan log untuk debugging
        Log::info('Method alokasi dipanggil dengan data:', $request->all());
        
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
                
                Log::info('Detail siswa sudah ada, akan diupdate.', [
                    'id_siswa' => $newIdSiswa,
                    'kelas_lama' => $kelasLama
                ]);
            } else {
                Log::info('Detail siswa belum ada, akan dibuat baru.', [
                    'id_siswa' => $newIdSiswa
                ]);
            }
            
            // Buat atau perbarui detail siswa
            if ($detailSiswa) {
                // Update detail siswa yang sudah ada - tanpa mengubah ID
                $detailSiswa->kode_jurusan = $request->kode_jurusan;
                $detailSiswa->kode_kelas = $request->kode_kelas;
                $detailSiswa->save();
                
                Log::info('Detail siswa berhasil diupdate', [
                    'id_detsiswa' => $detailSiswa->id_detsiswa,
                    'id_siswa' => $newIdSiswa,
                    'kode_jurusan' => $request->kode_jurusan,
                    'kode_kelas' => $request->kode_kelas
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
            $kelasBaru = Kelas::find($request->kode_kelas);
            if ($kelasBaru) {
                $jumlahSiswaBaru = DetailSiswa::where('kode_kelas', $request->kode_kelas)->count();
                $kelasBaru->update(['jumlah_siswa' => $jumlahSiswaBaru]);
                
                Log::info('Jumlah siswa di kelas baru diupdate', [
                    'kode_kelas' => $request->kode_kelas,
                    'jumlah_siswa' => $jumlahSiswaBaru
                ]);
            }
            
            // Update jumlah siswa di kelas lama jika berbeda
            if ($kelasLama && $kelasLama != $request->kode_kelas) {
                $kelasLamaObj = Kelas::find($kelasLama);
                if ($kelasLamaObj) {
                    $jumlahSiswaLama = DetailSiswa::where('kode_kelas', $kelasLama)->count();
                    $kelasLamaObj->update(['jumlah_siswa' => $jumlahSiswaLama]);
                    
                    Log::info('Jumlah siswa di kelas lama diupdate', [
                        'kode_kelas' => $kelasLama,
                        'jumlah_siswa' => $jumlahSiswaLama
                    ]);
                }
            }
            
            // Update semua referensi ke ID siswa lama di tabel lain yang terkait
            $this->updateRelatedTables($oldIdSiswa, $newIdSiswa);
            
            // Commit transaction
            DB::commit();
            
            Log::info('Alokasi siswa berhasil dengan ID baru', [
                'old_id' => $oldIdSiswa,
                'new_id' => $newIdSiswa,
                'kode_kelas' => $request->kode_kelas,
                'kode_jurusan' => $request->kode_jurusan
            ]);
            
            return redirect()->route('siswa.index')
                ->with('success', 'Siswa berhasil dialokasikan ke kelas dengan ID baru: ' . $newIdSiswa);
                
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
            
            return redirect()->route('siswa.index')
                ->with('error', 'Terjadi kesalahan saat mengalokasikan siswa: ' . $e->getMessage());
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
    
    /**
     * Method allocateSiswa() dibuat sebagai alias untuk method alokasi()
     * agar tetap kompatibel jika ada tempat lain yang memanggil method ini
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function allocateSiswa(Request $request)
    {
        return $this->alokasi($request);
    }
}