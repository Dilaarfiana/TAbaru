<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
// WithBatchInserts dihapus
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SiswaImport extends DefaultValueBinder implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnFailure, 
    // WithBatchInserts dihapus
    WithChunkReading,
    SkipsEmptyRows,
    WithCustomValueBinder
{
    use Importable, SkipsFailures;
    
    // Menambahkan properti untuk menyimpan jumlah siswa yang berhasil diimport
    private $importedCount = 0;
    
    /**
     * Get the count of imported records
     */
    public function getImportedCount()
    {
        return $this->importedCount;
    }

    /**
     * Pastikan cell ID siswa dibaca sebagai string
     */
    public function bindValue(Cell $cell, $value)
    {
        // Jika kolom pertama (ID Siswa), konversi menjadi string
        if ($cell->getColumn() === 'A' && $cell->getRow() > 1) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * Normalize column names and prepare data
     */
    public function prepareForValidation($row, $index)
    {
        // Debugging untuk melihat data row yang diproses
        Log::debug("Processing row {$index}: ", $row);

        // Cek apakah row ini kosong
        $isEmpty = true;
        foreach ($row as $value) {
            if (!empty($value)) {
                $isEmpty = false;
                break;
            }
        }

        // Jika row kosong, tambahkan log
        if ($isEmpty) {
            Log::warning("Empty row detected at index {$index}");
            return []; // Return empty array to be skipped
        }

        // Normalize column names (lowercase and trim)
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim($key));
            $normalizedRow[$normalizedKey] = $value;
        }
        $row = $normalizedRow;

        // Pastikan nama_siswa selalu ada untuk validasi
        if (!isset($row['nama_siswa'])) {
            $row['nama_siswa'] = null;
        }

        // PERBAIKAN ID SISWA: Cek dan perbaiki format ID siswa jika perlu
        if (isset($row['id_siswa']) && !empty($row['id_siswa'])) {
            // Konversi ID ke string dan hapus whitespace
            $idSiswa = trim((string) $row['id_siswa']);
            
            // Debug original ID
            Log::debug("Original ID Siswa: '{$idSiswa}' (Type: " . gettype($row['id_siswa']) . ", Length: " . strlen($idSiswa) . ")");
            
            // Cek apakah ID memiliki format dengan kode jurusan
            if (preg_match('/^6([A-Z]+)(\d{2})(\d{3})$/', $idSiswa, $matches)) {
                // Sudah format dengan jurusan, validasi kode_jurusan jika ada
                $kodeJurusanDariId = $matches[1];
                
                if (!empty($row['kode_jurusan']) && $row['kode_jurusan'] !== $kodeJurusanDariId) {
                    Log::warning("Kode jurusan di ID ({$kodeJurusanDariId}) berbeda dengan di kolom kode_jurusan ({$row['kode_jurusan']}). Menggunakan kode dari ID.");
                }
                
                // Set kode_jurusan sesuai dengan ID
                $row['kode_jurusan'] = $kodeJurusanDariId;
            }
            // Format tanpa kode jurusan, tapi dengan struktur yang benar
            elseif (preg_match('/^6(\d{5})$/', $idSiswa)) {
                // Format 6 + tahun + nomor urut, tidak ada kode jurusan
                Log::info("ID tanpa kode jurusan: {$idSiswa}");
            }
            // Format yang salah, perlu regenerasi
            else {
                Log::warning("Format ID tidak valid: {$idSiswa}. Akan digenerate otomatis.");
                $row['id_siswa'] = null; // Set null untuk digenerate nanti
            }
        }

        return $row;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Jika seluruh row kosong, skip
        if (empty(array_filter($row))) {
            return null;
        }

        // Konversi format tanggal lahir jika ada
        $tanggalLahir = null;
        if (!empty($row['tanggal_lahir'])) {
            try {
                $tanggalLahir = Carbon::createFromFormat('d-m-Y', $row['tanggal_lahir'])->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    // Coba format lain jika format pertama gagal
                    $tanggalLahir = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalLahir = null;
                    Log::warning("Failed to parse tanggal_lahir: {$row['tanggal_lahir']}");
                }
            }
        }
        
        // Konversi format tanggal masuk jika ada
        $tanggalMasuk = null;
        if (!empty($row['tanggal_masuk'])) {
            try {
                $tanggalMasuk = Carbon::createFromFormat('d-m-Y', $row['tanggal_masuk'])->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    // Coba format lain jika format pertama gagal
                    $tanggalMasuk = Carbon::parse($row['tanggal_masuk'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalMasuk = null;
                    Log::warning("Failed to parse tanggal_masuk: {$row['tanggal_masuk']}");
                }
            }
        }
        
        // Konversi status aktif - menggunakan status_aktif sesuai nama kolom di Excel
        $statusAktif = 1; // default aktif
        if (isset($row['status_aktif'])) {
            $status = is_string($row['status_aktif']) ? strtolower($row['status_aktif']) : $row['status_aktif'];
            if ($status === 'tidak aktif' || $status === '0' || $status === 0) {
                $statusAktif = 0;
            }
        }
        
        // Konversi jenis kelamin
        $jenisKelamin = null;
        if (isset($row['jenis_kelamin'])) {
            $jk = is_string($row['jenis_kelamin']) ? strtolower($row['jenis_kelamin']) : $row['jenis_kelamin'];
            if ($jk === 'l' || $jk === 'laki-laki' || $jk === 'laki laki') {
                $jenisKelamin = 'L';
            } elseif ($jk === 'p' || $jk === 'perempuan') {
                $jenisKelamin = 'P';
            } else {
                $jenisKelamin = $row['jenis_kelamin']; // Gunakan nilai asli jika tidak match
            }
        }
        
        // Menangani ID siswa dan alokasi jurusan
        $idSiswa = null;
        $kodeJurusan = isset($row['kode_jurusan']) ? $row['kode_jurusan'] : null;
        $kodeKelas = isset($row['kode_kelas']) ? $row['kode_kelas'] : null;
        
        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // PERUBAHAN: Preferably let's use empty ID and generate a new one
            if (empty($row['id_siswa'])) {
                // Generate ID otomatis jika tidak ada
                $idSiswa = empty($kodeJurusan) 
                    ? Siswa::generateNextId() 
                    : Siswa::generateIdForJurusan($kodeJurusan);
                    
                Log::info("Generated new ID: {$idSiswa} for row without ID");
            } else {
                // Jika ID diisi, cek duplikasi
                $idSiswa = $row['id_siswa'];
                
                // Cek apakah ID sudah ada di database
                $existingSiswa = Siswa::where('id_siswa', $idSiswa)->first();
                if ($existingSiswa) {
                    // Generate ID baru jika duplikat
                    $idSiswa = empty($kodeJurusan) 
                        ? Siswa::generateNextId() 
                        : Siswa::generateIdForJurusan($kodeJurusan);
                    
                    Log::warning("ID Siswa already exists: {$row['id_siswa']}, generating new ID: {$idSiswa}");
                }
            }
            
            // Simpan data siswa menggunakan updateOrCreate untuk menghindari duplikasi
            $siswa = Siswa::updateOrCreate(
                ['id_siswa' => $idSiswa],
                [
                    'nama_siswa'   => $row['nama_siswa'],
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $jenisKelamin,
                    'tanggal_masuk' => $tanggalMasuk,
                    'status_aktif'  => $statusAktif,
                ]
            );
            
            // Increment counter dan log
            $this->importedCount++;
            Log::info("Successfully imported student: {$idSiswa} - {$row['nama_siswa']}");
            
            // Jika ada kode jurusan, buat atau update DetailSiswa
            if (!empty($kodeJurusan)) {
                // Cek apakah DetailSiswa sudah ada
                $detailSiswa = DetailSiswa::where('id_siswa', $idSiswa)->first();
                
                if ($detailSiswa) {
                    // Update detail yang sudah ada
                    $detailSiswa->kode_jurusan = $kodeJurusan;
                    if (!empty($kodeKelas)) {
                        $detailSiswa->kode_kelas = $kodeKelas;
                    }
                    $detailSiswa->save();
                    
                    Log::info("Updated DetailSiswa for {$idSiswa} with jurusan: {$kodeJurusan}");
                } else {
                    // Generate ID DetailSiswa baru
                    $lastDetailSiswa = DetailSiswa::orderBy('id_detsiswa', 'desc')->first();
                    $nextNumber = 1;
                    
                    if ($lastDetailSiswa) {
                        preg_match('/DS(\d+)/', $lastDetailSiswa->id_detsiswa, $matches);
                        if (isset($matches[1])) {
                            $nextNumber = (int)$matches[1] + 1;
                        }
                    }
                    
                    $idDetailSiswa = 'DS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    
                    // Buat detail baru
                    $detailData = [
                        'id_detsiswa' => $idDetailSiswa,
                        'id_siswa' => $idSiswa,
                        'kode_jurusan' => $kodeJurusan
                    ];
                    
                    if (!empty($kodeKelas)) {
                        $detailData['kode_kelas'] = $kodeKelas;
                    }
                    
                    DetailSiswa::create($detailData);
                    
                    Log::info("Created DetailSiswa for {$idSiswa} with jurusan: {$kodeJurusan}");
                }
                
                // Update jumlah siswa di kelas jika ada
                if (!empty($kodeKelas)) {
                    $kelasBaru = Kelas::find($kodeKelas);
                    if ($kelasBaru) {
                        $jumlahSiswaBaru = DetailSiswa::where('kode_kelas', $kodeKelas)->count();
                        $kelasBaru->update(['jumlah_siswa' => $jumlahSiswaBaru]);
                        
                        Log::info("Updated jumlah siswa in kelas {$kodeKelas}: {$jumlahSiswaBaru}");
                    }
                }
            }
            
            // Commit transaction
            DB::commit();
            
            return $siswa;
            
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            Log::error("Error creating Siswa from row: " . json_encode($row) . " | Error: " . $e->getMessage());
            
            // Throw exception agar row ini discarded dan import tetap berjalan untuk row lain
            throw new \Exception("Failed to import row: " . $e->getMessage());
        }
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_siswa' => 'required|string|max:100',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable',
            'jenis_kelamin' => 'nullable|string|max:1',
            'tanggal_masuk' => 'nullable',
            'status_aktif' => 'nullable',
            // Kolom tambahan untuk detail siswa
            'kode_jurusan' => 'nullable|string',
            'kode_kelas' => 'nullable|string',
        ];
    }
    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nama_siswa.required' => 'Nama Siswa wajib diisi',
            'nama_siswa.max' => 'Nama Siswa maksimal 100 karakter',
            'tempat_lahir.max' => 'Tempat Lahir maksimal 50 karakter',
            'jenis_kelamin.max' => 'Jenis Kelamin harus 1 karakter (L/P)',
        ];
    }

    /**
     * Method untuk menangani error validasi
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $rowIndex = $failure->row();
            $attribute = $failure->attribute();
            $errors = implode(', ', $failure->errors());
            
            Log::error("Import validation error - Row: {$rowIndex}, Attribute: {$attribute}, Errors: {$errors}");
        }
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 50; // Jumlah record per chunk reading
    }
}