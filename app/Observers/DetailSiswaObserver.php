<?php

namespace App\Observers;

use App\Models\DetailSiswa;
use App\Models\Kelas;

class DetailSiswaObserver
{
    /**
     * Handle the DetailSiswa "updated" event.
     */
    public function updated(DetailSiswa $detailSiswa): void
    {
        // Jika kelas berubah, update jumlah siswa di kelas lama dan baru
        if ($detailSiswa->wasChanged('kode_kelas')) {
            // Update jumlah di kelas lama jika ada
            if ($detailSiswa->getOriginal('kode_kelas')) {
                $this->updateJumlahSiswa($detailSiswa->getOriginal('kode_kelas'));
            }
            
            // Update jumlah di kelas baru jika ada
            if ($detailSiswa->kode_kelas) {
                $this->updateJumlahSiswa($detailSiswa->kode_kelas);
            }
        }
    }

    /**
     * Handle the DetailSiswa "deleted" event.
     */
    public function deleted(DetailSiswa $detailSiswa): void
    {
        // Jika siswa dihapus dari kelas, kurangi jumlah siswa
        if ($detailSiswa->kode_kelas) {
            $this->updateJumlahSiswa($detailSiswa->kode_kelas);
        }
    }
    
    /**
     * Update jumlah siswa di kelas tertentu
     */
    private function updateJumlahSiswa($kodeKelas): void
    {
        $kelas = Kelas::find($kodeKelas);
        if ($kelas) {
            $jumlahSiswa = DetailSiswa::where('kode_kelas', $kelas->Kode_Kelas)->count();
            $kelas->Jumlah_Siswa = $jumlahSiswa;
            $kelas->save();
        }
    }
}