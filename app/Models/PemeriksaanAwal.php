<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanAwal extends Model
{
    use HasFactory;
    
    protected $table = 'pemeriksaan_awals';
    protected $primaryKey = 'Id_PreAwal';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'Id_PreAwal',
        'Id_DetPrx',
        'Pemeriksaan',
        'Keluhan_Dahulu',
        'Suhu',
        'Nadi',
        'Tegangan',
        'Pernapasan',
        'Tipe',
        'Status_Nyeri',
        'Karakteristik',
        'Lokasi',
        'Durasi',
        'Frekuensi'
    ];
    
    // Relasi dengan DetailPemeriksaan
    public function detailPemeriksaan()
    {
        return $this->belongsTo(DetailPemeriksaan::class, 'Id_DetPrx', 'Id_DetPrx');
    }
}