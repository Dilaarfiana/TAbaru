<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    
    protected $table = 'jurusan';
    protected $primaryKey = 'Kode_Jurusan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'Kode_Jurusan',
        'Nama_Jurusan',
    ];
    
    // Relationship dengan kelas
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'Kode_Jurusan', 'Kode_Jurusan');
    }
    
    // Relationship dengan siswa (melalui DetailSiswa)
    public function siswa()
    {
        return $this->hasMany(DetailSiswa::class, 'kode_jurusan', 'Kode_Jurusan');
    }
    
    // Method untuk mendapatkan kode jurusan baru (A-Z)
    public static function getNewCode()
    {
        $lastJurusan = self::orderBy('Kode_Jurusan', 'desc')->first();
        
        if (!$lastJurusan) {
            return 'A'; // Jika tidak ada jurusan, mulai dari Ati
        }
        
        $lastCode = $lastJurusan->Kode_Jurusan;
        
        // Jika sudah mencapai Z, kembali ke A
        if ($lastCode == 'Z') {
            return 'A';
        }
        
        // Increment kode jurusan
        return chr(ord($lastCode) + 1);
    }
}