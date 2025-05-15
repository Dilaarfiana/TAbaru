<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSiswa extends Model
{
    use HasFactory;
    
    protected $table = 'detail_siswas';
    protected $primaryKey = 'id_detsiswa';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_detsiswa',
        'id_siswa',
        'kode_jurusan',
        'kode_kelas'
    ];
    
    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
    
    // Relasi dengan Jurusan
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'kode_jurusan', 'Kode_Jurusan');
    }
    
    // Relasi dengan Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'Kode_Kelas');
    }
}