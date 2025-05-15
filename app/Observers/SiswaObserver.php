<?php

namespace App\Observers;

use App\Models\Siswa;
use App\Models\DetailSiswa;

class SiswaObserver
{
    /**
     * Handle the Siswa "created" event.
     */
    public function created(Siswa $siswa): void
    {
        // Cek apakah ini adalah siswa pertama, jika ya gunakan DS001
        $detailSiswaCount = DetailSiswa::count();
        
        if ($detailSiswaCount == 0) {
            $newId = 'DS001'; // ID awal karena belum ada data
        } else {
            // Cari ID detail siswa terakhir dan increment
            $lastId = DetailSiswa::orderBy('id_detsiswa', 'desc')->first()->id_detsiswa;
            $lastNumber = (int) substr($lastId, 2);
            $newId = 'DS' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }
        
        // Buat entri detail_siswa otomatis
        DetailSiswa::create([
            'id_detsiswa' => $newId,
            'id_siswa' => $siswa->id_siswa,
            'kode_jurusan' => null, // Awalnya kosong
            'kode_kelas' => null    // Awalnya kosong
        ]);
    }
}