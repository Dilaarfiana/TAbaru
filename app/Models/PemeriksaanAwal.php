<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanAwal extends Model
{
    use HasFactory;
    
    protected $table = 'pemeriksaan_awals';
    protected $primaryKey = 'id_preawal'; // Sesuai dengan migration
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_preawal',    // Sesuai dengan migration (lowercase)
        'id_detprx',     // Sesuai dengan migration (lowercase)
        'pemeriksaan',
        'keluhan_dahulu',
        'suhu',
        'nadi',
        'tegangan',
        'pernapasan',
        'tipe',
        'status_nyeri',
        'karakteristik',
        'lokasi',
        'durasi',
        'frekuensi'
    ];
    
    // Relasi dengan DetailPemeriksaan
    public function detailPemeriksaan()
    {
        return $this->belongsTo(DetailPemeriksaan::class, 'id_detprx', 'id_detprx');
    }
}