<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;
    
    // Nama tabel yang sesuai dengan database
    protected $table = 'resep';
    
    // Primary key
    protected $primaryKey = 'Id_Resep';
    
    // Primary key tidak auto increment dan bersifat string
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Waktu pembuatan dan pembaruan record
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';
    
    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'Id_Resep',
        'Id_Siswa',
        'Id_Dokter',
        'Tanggal_Resep',
        'Nama_Obat',
        'Dosis',
        'Durasi',
        'Dokumen'
    ];
    
    /**
     * Relasi dengan model Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'Id_Siswa', 'id_siswa');
    }
    
    /**
     * Relasi dengan model Dokter
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'Id_Dokter', 'Id_Dokter');
    }
    
    /**
     * Method untuk membuat ID otomatis dengan awalan RP001
     */
    public static function generateId()
    {
        $lastRecord = self::orderBy('Id_Resep', 'desc')->first();
                
        if (!$lastRecord) {
            return 'RP001';
        }
                
        $lastId = $lastRecord->Id_Resep;
        $lastNumber = (int) substr($lastId, 2);
        $newNumber = $lastNumber + 1;
                
        return 'RP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}