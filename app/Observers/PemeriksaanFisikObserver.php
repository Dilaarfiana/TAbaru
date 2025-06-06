<?php

namespace App\Observers;

use App\Models\PemeriksaanFisik;
use App\Services\NotificationService;

class PemeriksaanFisikObserver
{
    public function created(PemeriksaanFisik $pemeriksaanFisik)
    {
        $detailPemeriksaan = $pemeriksaanFisik->detailPemeriksaan;
        
        if ($detailPemeriksaan) {
            NotificationService::createMedicalNotification(
                'pemeriksaan_fisik',
                $detailPemeriksaan->id_siswa,
                [
                    'record_id' => $pemeriksaanFisik->id_prefisik,
                    'additional_info' => [
                        'tinggi_badan' => $pemeriksaanFisik->tinggi_badan,
                        'berat_badan' => $pemeriksaanFisik->berat_badan,
                        'masalah_aktif' => $pemeriksaanFisik->masalah_aktif
                    ]
                ]
            );
        }
    }
}