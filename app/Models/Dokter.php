<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Dokter extends Authenticatable
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
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // PENTING: JANGAN TAMBAHKAN MUTATOR PASSWORD DI SINI
    // Karena password sudah di-hash di controller
    
    /**
     * Relationship dengan rekam medis
     */
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'Id_Dokter', 'Id_Dokter');
    }

    /**
     * Relationship dengan detail pemeriksaan
     */
    public function detailPemeriksaan()
    {
        return $this->hasMany(DetailPemeriksaan::class, 'id_dokter', 'Id_Dokter');
    }

    /**
     * Relationship dengan resep
     */
    public function resep()
    {
        return $this->hasMany(Resep::class, 'Id_Dokter', 'Id_Dokter');
    }


    /**
     * Accessor untuk status aktif dalam bentuk text
     */
    public function getStatusTextAttribute()
    {
        return $this->status_aktif ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Accessor untuk nomor telepon yang terformat
     */
    public function getFormattedPhoneAttribute()
    {
        if (!$this->No_Telp) {
            return null;
        }

        $phone = $this->No_Telp;
        
        // Jika sudah ada +62, return as is
        if (str_starts_with($phone, '+62')) {
            return $phone;
        }
        
        // Jika dimulai dengan 62, tambahkan +
        if (str_starts_with($phone, '62')) {
            return '+' . $phone;
        }
        
        // Jika dimulai dengan 0, ganti dengan +62
        if (str_starts_with($phone, '0')) {
            return '+62' . substr($phone, 1);
        }
        
        // Jika langsung angka 8 atau 9, tambahkan +62
        return '+62' . $phone;
    }

    /**
     * Scope untuk dokter aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status_aktif', 1);
    }

    /**
     * Scope untuk dokter tidak aktif
     */
    public function scopeInactive($query)
    {
        return $query->where('status_aktif', 0);
    }

    /**
     * Scope untuk filter berdasarkan spesialisasi
     */
    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('Spesialisasi', $specialization);
    }

    /**
     * Method untuk verifikasi password
     */
    public function verifyPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Method untuk update password
     * Gunakan ini jika ingin update password dari tempat lain
     */
    public function updatePassword($newPassword)
    {
        $this->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    /**
     * Method untuk reset password ke default
     */
    public function resetPasswordToDefault()
    {
        $defaultPassword = 'dokter123';
        $this->updatePassword($defaultPassword);
        return $defaultPassword;
    }
}