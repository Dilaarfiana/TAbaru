<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis';
    protected $primaryKey = 'No_Rekam_Medis';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'No_Rekam_Medis',
        'Id_Siswa',
        'Id_Dokter',
        'Tanggal_Jam',
        'Keluhan_Utama',
        'Riwayat_Penyakit_Sekarang',
        'Riwayat_Penyakit_Dahulu',
        'Riwayat_Imunisasi',
        'Riwayat_Penyakit_Keluarga',
        'Silsilah_Keluarga'
    ];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'Id_Siswa', 'id_siswa');
    }

    // Relasi ke dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'Id_Dokter', 'Id_Dokter');
    }

    // Relasi ke detail pemeriksaan
    public function detailPemeriksaan()
    {
        return $this->hasMany(DetailPemeriksaan::class, 'Id_Siswa', 'Id_Siswa');
    }

    // Tambahkan di bawah method lainnya di RekamMedis.php
public function petugasUKS()
{
    return $this->belongsTo(PetugasUKS::class, 'Id_Petugas_UKS', 'Id_Petugas_UKS');
}


}