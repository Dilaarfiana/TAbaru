<?php

namespace App\Observers;

use App\Models\PemeriksaanAwal;
use App\Services\NotificationService;

class PemeriksaanAwalObserver
{
    public function created(PemeriksaanAwal $pemeriksaanAwal)
    {
        $detailPemeriksaan = $pemeriksaanAwal->detailPemeriksaan;
        
        if ($detailPemeriksaan) {
            NotificationService::createMedicalNotification(
                'pemeriksaan_awal',
                $detailPemeriksaan->id_siswa,
                [
                    'record_id' => $pemeriksaanAwal->id_preawal,
                    'keluhan' => $pemeriksaanAwal->keluhan_dahulu,
                    'additional_info' => [
                        'suhu' => $pemeriksaanAwal->suhu,
                        'nadi' => $pemeriksaanAwal->nadi,
                        'tensi' => $pemeriksaanAwal->tegangan
                    ]
                ]
            );
        }
    }
}