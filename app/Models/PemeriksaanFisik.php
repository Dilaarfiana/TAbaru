<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanFisik extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'id_pasien',
        'tanggal_pemeriksaan',
        'tinggi_badan',
        'berat_badan',
        'suhu_badan',
        'tekanan_darah',
        'keluhan',
        'hasil_pemeriksaan'
    ];

    // Generate new ID with format PF001, PF002, etc.
    public static function generateNewId()
    {
        $lastRecord = self::orderBy('id', 'desc')->first();
        
        if (!$lastRecord) {
            return 'PF001';
        }
        
        $lastId = $lastRecord->id;
        $lastNumber = intval(substr($lastId, 2));
        $newNumber = $lastNumber + 1;
        
        return 'PF' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
    
}