<?php

namespace App\Imports;

use App\Models\OrangTua;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class OrangTuaImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnError, 
    SkipsOnFailure,
    SkipsEmptyRows,
    WithChunkReading
{
    private $rowCount = 0;
    private $updateCount = 0;
    private $errors = [];
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if siswa exists
        $siswa = Siswa::find($row['id_siswa']);
        if (!$siswa) {
            $this->errors[] = "Siswa dengan ID {$row['id_siswa']} tidak ditemukan";
            return null;
        }
        
        // Check if orang tua already exists for this siswa
        $existingOrangTua = OrangTua::where('id_siswa', $row['id_siswa'])->first();
        if ($existingOrangTua) {
            // Update existing data
            $existingOrangTua->update([
                'nama_ayah' => $row['nama_ayah'],
                'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? $existingOrangTua->pekerjaan_ayah,
                'nama_ibu' => $row['nama_ibu'],
                'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? $existingOrangTua->pekerjaan_ibu,
                'alamat' => $row['alamat'],
                'no_telp' => $row['no_hp'] ?? $row['no_telp'] ?? $existingOrangTua->no_telp,
            ]);
            $this->updateCount++;
            return null;
        }
        
        // If not exists, create new data
        $this->rowCount++;
        return new OrangTua([
            'id_siswa' => $row['id_siswa'],
            'nama_ayah' => $row['nama_ayah'],
            'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? null,
            'nama_ibu' => $row['nama_ibu'],
            'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? null,
            'alamat' => $row['alamat'],
            'no_telp' => $row['no_hp'] ?? $row['no_telp'] ?? null,
        ]);
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id_siswa' => 'required|exists:siswas,id_siswa',
            'nama_ayah' => 'required|string|max:100',
            'nama_ibu' => 'required|string|max:100',
            '*.no_hp' => 'nullable|string|max:15',
            '*.no_telp' => 'nullable|string|max:15',
            'alamat' => 'required|string',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
        ];
    }
    
    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'id_siswa.required' => 'ID Siswa tidak boleh kosong',
            'id_siswa.exists' => 'ID Siswa tidak terdaftar dalam sistem',
            'nama_ayah.required' => 'Nama Ayah harus diisi',
            'nama_ayah.max' => 'Nama Ayah maksimal 100 karakter',
            'nama_ibu.required' => 'Nama Ibu harus diisi',
            'nama_ibu.max' => 'Nama Ibu maksimal 100 karakter',
            '*.no_hp.max' => 'Nomor HP maksimal 15 karakter',
            '*.no_telp.max' => 'Nomor Telepon maksimal 15 karakter',
            'alamat.required' => 'Alamat harus diisi',
            'pekerjaan_ayah.max' => 'Pekerjaan Ayah maksimal 100 karakter',
            'pekerjaan_ibu.max' => 'Pekerjaan Ibu maksimal 100 karakter',
        ];
    }
    
    /**
     * @param \Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    /**
     * @param array $row
     * @param \Maatwebsite\Excel\Validators\Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $row = $failure->row();
            $errors = $failure->errors();
            $this->errors[] = "Baris {$row}: " . implode(", ", $errors);
        }
    }
    
    /**
     * Get jumlah baris yang berhasil diimport (data baru)
     *
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }
    
    /**
     * Get jumlah data yang diupdate
     *
     * @return int
     */
    public function getUpdateCount()
    {
        return $this->updateCount;
    }
    
    /**
     * Get error messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 50;
    }
}