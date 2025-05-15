<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SiswaImport extends DefaultValueBinder implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnFailure, 
    WithBatchInserts, 
    WithChunkReading,
    SkipsEmptyRows,
    WithCustomValueBinder
{
    use Importable, SkipsFailures;

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
     * Prepare row data before validation
     *
     * @param array $row
     * @param int $index
     * @return array
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
        }

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
            
            // Format yang diharapkan untuk import: 6 + Tahun (2 digit) + Nomor Urut (3 digit)
            // Contoh: 625001
            
            // Periksa apakah format tidak sesuai dengan 6YYNNN
            if (!preg_match('/^6\d{5}$/', $idSiswa)) {
                // Ambil tahun dari tanggal masuk jika ada, atau gunakan tahun saat ini
                $tahun = '';
                if (!empty($row['tanggal_masuk'])) {
                    try {
                        $tahun = Carbon::parse($row['tanggal_masuk'])->format('y');
                    } catch (\Exception $e) {
                        $tahun = date('y');
                    }
                } else {
                    $tahun = date('y');
                }
                
                // Ekstrak nomor urut jika ada
                if (preg_match('/(\d{3})$/', $idSiswa, $matches)) {
                    $urut = $matches[1];
                } else {
                    // Jika tidak bisa extract nomor urut, gunakan default
                    $urut = '001';
                }
                
                // Buat ID yang benar dengan format 6 + Tahun + Nomor Urut
                $idBenar = "6{$tahun}{$urut}";
                
                Log::info("Reformatted ID Siswa from '{$idSiswa}' to '{$idBenar}'");
                $row['id_siswa'] = $idBenar;
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
        
        // Jika id_siswa tidak diisi, generate ID otomatis menggunakan method di model Siswa
        $idSiswa = null;
        if (!empty($row['id_siswa'])) {
            $idSiswa = $row['id_siswa'];
            
            // Final check untuk format ID
            if (!preg_match('/^6\d{5}$/', $idSiswa)) {
                // Generate ID otomatis jika format masih tidak sesuai
                $idSiswa = Siswa::generateGenericId($tanggalMasuk);
                Log::info("Generated new ID: {$idSiswa} to replace invalid ID: {$row['id_siswa']}");
            }
        } else {
            // Generate ID otomatis jika tidak ada
            $idSiswa = Siswa::generateGenericId($tanggalMasuk);
            Log::info("Generated new ID: {$idSiswa} for row without ID");
        }
        
        try {
            return new Siswa([
                'id_siswa'     => $idSiswa,
                'nama_siswa'   => $row['nama_siswa'],
                'tempat_lahir' => $row['tempat_lahir'] ?? null,
                'tanggal_lahir' => $tanggalLahir,
                'jenis_kelamin' => $jenisKelamin,
                'tanggal_masuk' => $tanggalMasuk,
                'status_aktif'  => $statusAktif,
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating Siswa from row: " . json_encode($row) . " | Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id_siswa' => [
                'nullable',
                'unique:siswas,id_siswa',
                // Format yang diharapkan: 6 + Tahun (2 digit) + Nomor Urut (3 digit)
                // Contoh: 625001
                'regex:/^6\d{5}$/'
            ],
            'nama_siswa' => 'required|string|max:100',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable',
            'jenis_kelamin' => 'nullable|string|max:1',
            'tanggal_masuk' => 'nullable',
            'status_aktif' => 'nullable',
        ];
    }
    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'id_siswa.unique' => 'ID Siswa sudah digunakan',
            'id_siswa.regex' => 'Format ID Siswa tidak valid. Format yang benar: 6 + Tahun (2 digit) + Nomor Urut (3 digit). Contoh: 625001',
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
    public function batchSize(): int
    {
        return 100; // Jumlah record per batch insert
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100; // Jumlah record per chunk reading
    }
}