<?php

namespace App\Observers;

use App\Models\PemeriksaanHarian;
use App\Services\NotificationService;

class PemeriksaanHarianObserver
{
    public function created(PemeriksaanHarian $pemeriksaanHarian)
    {
        NotificationService::createMedicalNotification(
            'pemeriksaan_harian',
            $pemeriksaanHarian->Id_Siswa,
            [
                'record_id' => $pemeriksaanHarian->Id_Harian,
                'keluhan' => substr($pemeriksaanHarian->Hasil_Pemeriksaan, 0, 100),
                'additional_info' => [
                    'tanggal' => $pemeriksaanHarian->Tanggal_Jam
                ]
            ]
        );
    }
}