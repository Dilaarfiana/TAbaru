<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PetugasUKS extends Model
{
    use HasFactory;
    
    protected $table = 'petugas_uks';
    protected $primaryKey = 'NIP';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Define fillable fields including the missing 'level' field
    protected $fillable = [
        'NIP',
        'nama_petugas_uks',
        'alamat',
        'no_telp',
        'status_aktif',
        'level',
        'password'
    ];
    
    // Cast boolean field
    protected $casts = [
        'status_aktif' => 'boolean',
    ];
    
    // Mutator for password to ensure it's hashed
    public function setPasswordAttribute($value)
    {
        // Only hash the password if it's not already hashed
        if ($value && !Hash::info($value)['algo']) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
    
    // Mutator for no_telp to add +62 if not present
    public function setNoTelpAttribute($value)
    {
        // If number is empty, save as null
        if (empty($value)) {
            $this->attributes['no_telp'] = null;
            return;
        }
        
        // If number already starts with +62, use directly
        if (strpos($value, '+62') === 0) {
            $this->attributes['no_telp'] = $value;
            return;
        }
        
        // If number starts with 0, replace with +62
        if (strpos($value, '0') === 0) {
            $this->attributes['no_telp'] = '+62' . substr($value, 1);
            return;
        }
        
        // For other cases, add +62 in front
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