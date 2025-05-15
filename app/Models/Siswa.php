<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Siswa extends Model 
{
    use HasFactory;
    
    // Nama tabel
    protected $table = 'siswas';
    
    // Primary key
    protected $primaryKey = 'id_siswa';
    
    // Primary key adalah string dan bukan auto-increment
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Kolom yang dapat diisi
    protected $fillable = [
        'id_siswa',
        'nama_siswa',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'tanggal_masuk',
        'status_aktif',
    ];
    
    // Cast tipe data
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'status_aktif' => 'boolean',
    ];
    
    // Accessor untuk usia
    public function getUsiaAttribute()
    {
        if ($this->tanggal_lahir) {
            return Carbon::parse($this->tanggal_lahir)->age;
        }
        return null;
    }
    
    // Relasi dengan OrangTua
    public function orangTua()
    {
        return $this->hasOne(OrangTua::class, 'id_siswa', 'id_siswa');
    }
    
    // Relasi dengan DetailSiswa
    public function detailSiswa()
    {
        return $this->hasOne(DetailSiswa::class, 'id_siswa', 'id_siswa');
    }
    
    // Relasi dengan RekamMedis
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'id_siswa', 'id_siswa');
    }
    
    // Relasi dengan DetailPemeriksaan
    public function detailPemeriksaan()
    {
        return $this->hasMany(DetailPemeriksaan::class, 'id_siswa', 'id_siswa');
    }
    
    // Relasi dengan PemeriksaanHarian
    public function pemeriksaanHarian()
    {
        return $this->hasMany(PemeriksaanHarian::class, 'id_siswa', 'id_siswa');
    }
    
    // Relasi dengan Resep
    public function resep()
    {
        return $this->hasMany(Resep::class, 'id_siswa', 'id_siswa');
    }
    
    // Scope untuk siswa aktif
    public function scopeActive($query)
    {
        return $query->where('status_aktif', 1);
    }
    
    // Scope untuk siswa tidak aktif
    public function scopeInactive($query)
    {
        return $query->where('status_aktif', 0);
    }
    
    // Method untuk mendapatkan kelas melalui relasi detailSiswa
    public function getKelasAttribute()
    {
        if ($this->detailSiswa && $this->detailSiswa->kelas) {
            return $this->detailSiswa->kelas;
        }
        return null;
    }
    
    // Method untuk mendapatkan jurusan melalui relasi detailSiswa
    public function getJurusanAttribute()
    {
        if ($this->detailSiswa && $this->detailSiswa->jurusan) {
            return $this->detailSiswa->jurusan;
        }
        return null;
    }
    
    // Method untuk generate ID berikutnya
    public static function generateNextId()
    {
        $prefix = 'SI';
        $lastSiswa = self::orderBy('id_siswa', 'desc')->first();
        
        if (!$lastSiswa) {
            return $prefix . '001';
        }
        
        // Pastikan ID mengikuti format yang diharapkan
        if (strpos($lastSiswa->id_siswa, $prefix) === 0) {
            $lastId = substr($lastSiswa->id_siswa, strlen($prefix));
            $nextNumericId = intval($lastId) + 1;
            return $prefix . str_pad($nextNumericId, 3, '0', STR_PAD_LEFT);
        }
        
        // Jika format tidak sesuai, mulai dari awal
        return $prefix . '001';
    }
}