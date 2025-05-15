<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Dokter extends Authenticatable 
{
    use HasFactory, Notifiable;

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
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'formatted_phone',
    ];

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
            if (!$model->Id_Dokter) {
                $model->Id_Dokter = self::getNextId();
            }
        });
    }

    // Mutator untuk meng-hash password otomatis
    public function setPasswordAttribute($value)
    {
        // Gunakan Hash facade untuk konsistensi
        $this->attributes['password'] = bcrypt($value);
    }

    // Accessor untuk format No_Telp
    public function getFormattedPhoneAttribute()
    {
        if (empty($this->No_Telp)) {
            return '';
        }

        // Gunakan metode yang lebih robust untuk format telepon
        $phone = preg_replace('/[^0-9]/', '', $this->No_Telp);
        
        if (Str::startsWith($phone, '62')) {
            return '0' . substr($phone, 2);
        }

        if (Str::startsWith($phone, '+62')) {
            return '0' . substr($phone, 3);
        }

        return $phone;
    }

    // Relasi dengan tabel Rekam_Medis
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'Id_Dokter', 'Id_Dokter');
    }

    // Relasi dengan tabel Detail_Pemeriksaan
    public function detailPemeriksaan()
    {
        return $this->hasMany(DetailPemeriksaan::class, 'Id_Dokter', 'Id_Dokter');
    }

    // Relasi dengan tabel Pemeriksaan_Harian
    public function pemeriksaanHarian()
    {
        return $this->hasMany(PemeriksaanHarian::class, 'Id_Dokter', 'Id_Dokter');
    }

    // Relasi dengan tabel Resep
    public function resep()
    {
        return $this->hasMany(Resep::class, 'Id_Dokter', 'Id_Dokter');
    }
}