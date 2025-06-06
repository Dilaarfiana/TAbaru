<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanFisik extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_fisiks';
    protected $primaryKey = 'id_prefisik';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_prefisik',
        'id_detprx',
        'tinggi_badan',
        'berat_badan',
        'lingkar_kepala',
        'lingkar_lengan_atas',
        'dada',
        'jantung',
        'paru',
        'perut',
        'hepar',
        'anogenital',
        'ekstremitas',
        'kepala', // Tambahkan field yang ada di migration
        'pemeriksaan_penunjang',
        'masalah_aktif',
        'rencana_medis_dan_terapi'
    ];

    protected $casts = [
        'tinggi_badan' => 'decimal:1',
        'berat_badan' => 'decimal:1',
        'lingkar_kepala' => 'decimal:1',
        'lingkar_lengan_atas' => 'decimal:1',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Generate new ID with format PF001, PF002, etc.
    public static function generateNewId()
    {
        // Gunakan raw query untuk menghindari race condition
        $lastRecord = self::selectRaw('MAX(CAST(SUBSTRING(id_prefisik, 3) AS UNSIGNED)) as max_num')
                         ->where('id_prefisik', 'REGEXP', '^PF[0-9]+$')
                         ->first();
        
        $lastNumber = $lastRecord->max_num ?? 0;
        $newNumber = $lastNumber + 1;

        return 'PF' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Relationship dengan Detail Pemeriksaan
    public function detailPemeriksaan()
    {
        return $this->belongsTo(DetailPemeriksaan::class, 'id_detprx', 'id_detprx');
    }

    // Accessor untuk menghitung BMI
    public function getBmiAttribute()
    {
        if ($this->tinggi_badan && $this->berat_badan) {
            $tinggiM = $this->tinggi_badan / 100;
            return round($this->berat_badan / ($tinggiM * $tinggiM), 2);
        }
        return null;
    }

    // Accessor untuk kategori BMI
    public function getBmiKategoriAttribute()
    {
        $bmi = $this->bmi;
        if (!$bmi) return null;

        if ($bmi < 18.5) {
            return 'Underweight';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return 'Normal';
        } elseif ($bmi >= 25 && $bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    // Accessor untuk status BMI (untuk styling)
    public function getBmiStatusAttribute()
    {
        $bmi = $this->bmi;
        if (!$bmi) return 'secondary';

        if ($bmi < 18.5) {
            return 'warning';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return 'success';
        } elseif ($bmi >= 25 && $bmi < 30) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    // Scope untuk filter BMI
    public function scopeWithBmiCategory($query, $category)
    {
        switch ($category) {
            case 'underweight':
                return $query->whereRaw('(berat_badan / POW(tinggi_badan/100, 2)) < 18.5');
            case 'normal':
                return $query->whereRaw('(berat_badan / POW(tinggi_badan/100, 2)) BETWEEN 18.5 AND 24.9');
            case 'overweight':
                return $query->whereRaw('(berat_badan / POW(tinggi_badan/100, 2)) BETWEEN 25 AND 29.9');
            case 'obese':
                return $query->whereRaw('(berat_badan / POW(tinggi_badan/100, 2)) >= 30');
            default:
                return $query;
        }
    }

    // Boot method untuk auto-generate ID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_prefisik)) {
                $model->id_prefisik = self::generateNewId();
            }
        });
    }
}