<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetugasUKS extends Model
{
    use HasFactory;
    
    protected $table = 'petugas_uks';
    protected $primaryKey = 'NIP';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Define fillable fields with correct column names
    protected $fillable = [
        'NIP',
        'nama_petugas_uks',
        'alamat',
        'no_telp',
        'status_aktif',
        'password'
    ];
    
    // Cast boolean field
    protected $casts = [
        'status_aktif' => 'boolean',
    ];
    
    // Mutator untuk no_telp untuk menambahkan +62 jika tidak ada
    public function setNoTelpAttribute($value)
    {
        // Jika nomor tidak diisi, simpan sebagai null
        if (empty($value)) {
            $this->attributes['no_telp'] = null;
            return;
        }
        
        // Jika nomor sudah diawali dengan +62, gunakan langsung
        if (strpos($value, '+62') === 0) {
            $this->attributes['no_telp'] = $value;
            return;
        }
        
        // Jika nomor diawali dengan 0, ganti dengan +62
        if (strpos($value, '0') === 0) {
            $this->attributes['no_telp'] = '+62' . substr($value, 1);
            return;
        }
        
        // Untuk kasus lainnya, tambahkan +62 di depan
        $this->attributes['no_telp'] = '+62' . $value;
    }
    
    // Relationships
    public function detailPemeriksaan()
    {
        return $this->hasMany(DetailPemeriksaan::class, 'NIP', 'NIP');
    }
    
    public function pemeriksaanHarian()
    {
        return $this->hasMany(PemeriksaanHarian::class, 'NIP', 'NIP');
    }
}