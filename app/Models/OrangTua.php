<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrangTua extends Model
{
    use HasFactory;
    
    protected $table = 'orang_tuas';
    protected $primaryKey = 'id_orang_tua';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_orang_tua', 'id_siswa', 'nama_ayah', 'tanggal_lahir_ayah', 'pekerjaan_ayah', 'pendidikan_ayah',
        'nama_ibu', 'tanggal_lahir_ibu', 'pekerjaan_ibu', 'pendidikan_ibu', 'alamat', 'no_telp', 'password'
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Generate ID jika belum diisi
            if (empty($model->id_orang_tua)) {
                $model->id_orang_tua = static::generateNewId();
            }
        });
    }
    
    /**
     * Generate ID baru dengan format OT001, OT002, dst.
     */
    public static function generateNewId()
    {
        $lastId = static::orderBy('id_orang_tua', 'desc')->value('id_orang_tua');
        
        if (!$lastId) {
            return 'OT001';
        }
        
        // Ambil angka dari ID terakhir
        $lastNumber = (int) substr($lastId, 2);
        $newNumber = $lastNumber + 1;
        
        // Format dengan leading zeros
        return 'OT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get the student associated with the parent.
     */
    public function siswa()
    {
        // Perubahan di sini: menentukan tabel siswas sebagai tabel foreign key
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
    
    /**
     * Get the formatted father's birth date.
     */
    public function getTanggalLahirAyahFormattedAttribute()
    {
        return $this->tanggal_lahir_ayah ? date('d-m-Y', strtotime($this->tanggal_lahir_ayah)) : '';
    }
    
    /**
     * Get the formatted mother's birth date.
     */
    public function getTanggalLahirIbuFormattedAttribute()
    {
        return $this->tanggal_lahir_ibu ? date('d-m-Y', strtotime($this->tanggal_lahir_ibu)) : '';
    }
    
    /**
     * Set password attribute with automatic hashing.
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }
}