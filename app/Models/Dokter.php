<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'dokters';
    protected $primaryKey = 'Id_Dokter';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'Id_Dokter',
        'Nama_Dokter',
        'Spesialisasi',
        'No_Telp',
        'Alamat',
        'status_aktif',
        'password'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    protected $appends = [
        'formatted_phone',
    ];

    // Validation rules
    public static function rules($id = null)
    {
        return [
            'Id_Dokter' => 'required|string|max:5|unique:dokters,Id_Dokter,' . $id,
            'Nama_Dokter' => 'required|string|max:50',
            'Spesialisasi' => 'nullable|string|max:25',
            'No_Telp' => 'nullable|string|max:15',
            'Alamat' => 'nullable|string',
            'status_aktif' => 'boolean',
            'password' => 'nullable|string|min:6'
        ];
    }

    // Method untuk mendapatkan ID dokter berikutnya
    public static function getNextId()
    {
        $lastDokter = self::orderBy('Id_Dokter', 'desc')->first();

        if (!$lastDokter) {
            return 'DO001';
        }

        $lastId = $lastDokter->Id_Dokter;
        $numberPart = intval(substr($lastId, 2));
        $nextNumber = $numberPart + 1;

        return 'DO' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Boot method untuk set ID otomatis jika tidak disediakan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->Id_Dokter)) {
                $model->Id_Dokter = self::getNextId();
            }
        });
    }

    // Mutator untuk meng-hash password otomatis
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    // Accessor untuk format No_Telp
    public function getFormattedPhoneAttribute()
    {
        if (empty($this->No_Telp)) {
            return '';
        }

        $phone = preg_replace('/[^0-9]/', '', $this->No_Telp);
        
        if (str_starts_with($phone, '62')) {
            return '0' . substr($phone, 2);
        }
        
        return $phone;
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function scopeDenganSpesialisasi($query, $spesialisasi)
    {
        return $query->where('Spesialisasi', $spesialisasi);
    }

    // Relasi dengan tabel Rekam_Medis
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'Id_Dokter', 'Id_Dokter');
    }

    // Relasi dengan tabel Detail_Pemeriksaan
    public function detailPemeriksaan()
    {
        return $this->hasMany(DetailPemeriksaan::class, 'id_dokter', 'Id_Dokter');
    }

    // Relasi dengan tabel Resep
    public function resep()
    {
        return $this->hasMany(Resep::class, 'Id_Dokter', 'Id_Dokter');
    }

    // Helper methods
    public function isAktif()
    {
        return $this->status_aktif;
    }

    public function getTotalPasien()
    {
        return $this->rekamMedis()->distinct('Id_Siswa')->count();
    }

    public function getTotalPemeriksaan()
    {
        return $this->detailPemeriksaan()->count();
    }
}