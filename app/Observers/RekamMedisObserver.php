<?php

namespace App\Observers;

use App\Models\RekamMedis;
use App\Services\NotificationService;

class RekamMedisObserver
{
    public function created(RekamMedis $rekamMedis)
    {
        NotificationService::createMedicalNotification(
            'rekam_medis',
            $rekamMedis->Id_Siswa,
            [
                'record_id' => $rekamMedis->No_Rekam_Medis,
                'keluhan' => $rekamMedis->Keluhan_Utama,
                'additional_info' => [
                    'dokter' => $rekamMedis->dokter->Nama_Dokter ?? null,
                    'tanggal' => $rekamMedis->Tanggal_Jam
                ]
            ]
        );
    }
}