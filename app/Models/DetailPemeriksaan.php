<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemeriksaan extends Model
{
    use HasFactory;
    
    protected $table = 'detail_pemeriksaans';
    protected $primaryKey = 'id_detprx';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id_detprx',
        'tanggal_jam',
        'id_siswa',
        'status_pemeriksaan',
        'id_dokter',
        'nip'
    ];

    protected $casts = [
        'tanggal_jam' => 'datetime',
        'status_pemeriksaan' => 'string'
    ];

    /**
     * Boot function untuk menangani event model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Generate ID jika belum diset
            if (!$model->id_detprx) {
                $model->id_detprx = self::generateNewId();
            }
        });
    }

    /**
     * Generate ID baru dengan format DP001, DP002, dll.
     */
    public static function generateNewId()
    {
        $lastRecord = self::orderBy('id_detprx', 'desc')->first();
        
        if (!$lastRecord) {
            return 'DP001';
        }
        
        $lastId = $lastRecord->id_detprx;
        $lastNumber = (int) substr($lastId, 2);
        $newNumber = $lastNumber + 1;
        
        return 'DP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // PERBAIKAN: Relasi dengan tabel lain - sesuaikan dengan schema database
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // PERBAIKAN: Foreign key harus sesuai dengan schema database
    // detail_pemeriksaans.id_dokter -> dokters.Id_Dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'id_dokter', 'Id_Dokter');
    }

    // PERBAIKAN: Nama method konsisten dengan konvensi Laravel
    public function petugasUks()
    {
        return $this->belongsTo(PetugasUKS::class, 'nip', 'NIP');
    }

    public function pemeriksaanFisik()
    {
        return $this->hasOne(PemeriksaanFisik::class, 'id_detprx', 'id_detprx');
    }

    public function pemeriksaanAwal()
    {
        return $this->hasOne(PemeriksaanAwal::class, 'id_detprx', 'id_detprx');
    }

    // Scope untuk filter berdasarkan status
    public function scopeBelumLengkap($query)
    {
        return $query->where('status_pemeriksaan', 'belum lengkap');
    }

    public function scopeLengkap($query)
    {
        return $query->where('status_pemeriksaan', 'lengkap');
    }

    // Accessor untuk status dalam bahasa Indonesia
    public function getStatusPemeriksaanTextAttribute()
    {
        return $this->status_pemeriksaan === 'lengkap' ? 'Lengkap' : 'Belum Lengkap';
    }
}