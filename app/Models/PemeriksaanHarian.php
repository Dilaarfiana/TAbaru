<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanHarian extends Model
{
    use HasFactory;
    
    // Nama tabel yang sesuai dengan database
    protected $table = 'pemeriksaan_harians';
    
    // Primary key
    protected $primaryKey = 'Id_Harian';
    
    // Primary key tidak auto increment dan bersifat string
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Waktu pembuatan dan pembaruan record
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';
    
    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'Id_Harian',
        'Tanggal_Jam',
        'Hasil_Pemeriksaan',
        'Id_Siswa',
        'NIP'
    ];
    
    // Relasi dengan model Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'Id_Siswa', 'id_siswa');
    }
    
    // Relasi dengan model PetugasUKS
    public function petugasUKS()
    {
        return $this->belongsTo(PetugasUKS::class, 'NIP', 'NIP');
    }
    
    // Method untuk membuat ID otomatis dengan awalan PH001
    public static function generateId()
    {
        $lastRecord = self::orderBy('Id_Harian', 'desc')->first();
        
        if (!$lastRecord) {
            return 'PH001';
        }
        
        $lastId = $lastRecord->Id_Harian;
        $lastNumber = (int) substr($lastId, 2);
        $newNumber = $lastNumber + 1;
        
        return 'PH' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}