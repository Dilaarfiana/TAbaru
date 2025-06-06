<?php

namespace App\Observers;

use App\Models\Resep;
use App\Services\NotificationService;

class ResepObserver
{
    public function created(Resep $resep)
    {
        NotificationService::createMedicalNotification(
            'resep',
            $resep->Id_Siswa,
            [
                'record_id' => $resep->Id_Resep,
                'additional_info' => [
                    'nama_obat' => $resep->Nama_Obat,
                    'dosis' => $resep->Dosis,
                    'durasi' => $resep->Durasi,
                    'tanggal' => $resep->Tanggal_Resep
                ]
            ]
        );
    }
}