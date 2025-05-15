<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kelas extends Model
{
    use HasFactory;
    
    protected $table = 'Kelas';
    protected $primaryKey = 'Kode_Kelas';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Matikan timestamps jika tabel tidak memiliki kolom tersebut
    public $timestamps = false;
    
    protected $fillable = [
        'Kode_Kelas',
        'Nama_Kelas',
        'Kode_Jurusan',
        'Tahun_Ajaran'
    ];
    
    // Relasi dengan jurusan
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'Kode_Jurusan', 'Kode_Jurusan');
    }
    
    // Relasi dengan siswa
    public function siswa()
    {
        return $this->hasMany(DetailSiswa::class, 'kode_kelas', 'Kode_Kelas');
    }
    
    // Accessor untuk mendapatkan jumlah siswa otomatis
    public function getJumlahSiswaAttribute()
    {
        return $this->siswa ? $this->siswa->count() : 0;
    }
    
    // Method untuk generate kode kelas baru
    public static function getNewCode()
    {
        $lastKelas = self::orderBy('Kode_Kelas', 'desc')->first();
        
        if (!$lastKelas) {
            return 'KL001';
        }
        
        $lastCode = $lastKelas->Kode_Kelas;
        
        // Ambil angka dari kode terakhir
        $number = (int) substr($lastCode, 2);
        $number++;
        
        // Format angka dengan leading zeros
        $newNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        
        return 'KL' . $newNumber;
    }
    
    // Method untuk generate tahun ajaran otomatis
    public static function getCurrentTahunAjaran()
    {
        $currentYear = Carbon::now()->year;
        $month = Carbon::now()->month;
        
        // Jika bulan >= 7 (Juli), maka tahun ajaran dimulai dari tahun ini
        // Jika bulan < 7, tahun ajaran dimulai dari tahun sebelumnya
        if ($month >= 7) {
            return $currentYear . '/' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '/' . $currentYear;
        }
    }
}