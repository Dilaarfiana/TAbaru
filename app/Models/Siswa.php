<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
        'tanggal_lulus',
    ];
    
    // Menggunakan timestamps standar Laravel
    public $timestamps = true;
    
    // Cast tipe data
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_lulus' => 'date',
        'status_aktif' => 'boolean',
    ];
    
    // Accessor untuk dibuat_pada (untuk backward compatibility dengan view)
    public function getDibuatPadaAttribute()
    {
        return $this->created_at;
    }
    
    // Accessor untuk diperbarui_pada (untuk backward compatibility dengan view)
    public function getDiperbaruiPadaAttribute()
    {
        return $this->updated_at;
    }
    
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
        
        // Jika relasi tidak berhasil, coba ambil langsung dari database
        if ($this->detailSiswa && $this->detailSiswa->kode_kelas) {
            return Kelas::where('Kode_Kelas', $this->detailSiswa->kode_kelas)->first();
        }
        
        return null;
    }
    
    // Method untuk mendapatkan jurusan melalui relasi detailSiswa
    public function getJurusanAttribute()
    {
        // Coba mendapatkan dari relasi jurusan langsung
        if ($this->detailSiswa && $this->detailSiswa->jurusan) {
            return $this->detailSiswa->jurusan;
        }
        
        // Coba mendapatkan dari relasi kelas->jurusan
        if ($this->detailSiswa && $this->detailSiswa->kelas && $this->detailSiswa->kelas->jurusan) {
            return $this->detailSiswa->kelas->jurusan;
        }
        
        // Coba mendapatkan langsung dari database
        if ($this->detailSiswa && $this->detailSiswa->kode_jurusan) {
            return Jurusan::where('Kode_Jurusan', $this->detailSiswa->kode_jurusan)->first();
        }
        
        return null;
    }
    
    /**
     * Method untuk generate ID siswa berikutnya dengan format baru:
     * kode sekolah (6) + tahun (yy) + nomor urut (001)
     * 
     * @return string
     */
    public static function generateNextId()
    {
        $tahunSekarang = date('y'); // Ambil 2 digit tahun saat ini
        $kodeSekolah = '6'; // Kode sekolah (sesuai permintaan)
        
        // Cari siswa terakhir dengan format ID tahun ini
        $prefix = $kodeSekolah . $tahunSekarang;
        $lastSiswa = self::where('id_siswa', 'like', $prefix . '%')
                        ->orderBy('id_siswa', 'desc')
                        ->first();
        
        if ($lastSiswa) {
            // Jika sudah ada siswa dengan tahun ini, ambil angka terakhirnya dan tambahkan 1
            $lastNumber = (int) substr($lastSiswa->id_siswa, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            // Jika belum ada siswa dengan tahun ini, mulai dari 1
            $nextNumber = 1;
        }
        
        // Format nomor urut dengan padding nol (001, 002, dst)
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Method untuk generate ID siswa dengan format untuk alokasi:
     * kode sekolah (6) + kode jurusan + tahun (yy) + nomor urut (001)
     * 
     * @param string $kodeJurusan - Kode jurusan siswa
     * @return string
     */
    public static function generateIdForJurusan($kodeJurusan)
    {
        // Validasi kode jurusan
        if (empty($kodeJurusan)) {
            Log::warning('Kode jurusan tidak boleh kosong. Menggunakan ID siswa tanpa jurusan.');
            return self::generateNextId();
        }
        
        $tahunSekarang = date('y'); // Ambil 2 digit tahun saat ini
        $kodeSekolah = '6'; // Kode sekolah
        
        // Cari siswa terakhir dengan format ID dan jurusan ini
        $prefix = $kodeSekolah . $kodeJurusan . $tahunSekarang;
        
        // Log untuk debugging
        Log::info("Generating ID with prefix: {$prefix}");
        
        $lastSiswa = self::where('id_siswa', 'like', $prefix . '%')
                        ->orderBy('id_siswa', 'desc')
                        ->first();
        
        if ($lastSiswa) {
            // Jika sudah ada siswa dengan tahun dan jurusan ini
            // Ekstrak nomor urut dari ID terakhir
            $lastIdLength = strlen($lastSiswa->id_siswa);
            $prefixLength = strlen($prefix);
            $digitCount = $lastIdLength - $prefixLength;
            
            // Jika panjang digit berbeda dari yang diharapkan, log warning
            if ($digitCount != 3) {
                Log::warning("Format ID siswa terakhir tidak sesuai. ID: {$lastSiswa->id_siswa}, Prefix: {$prefix}, Digit Count: {$digitCount}");
            }
            
            // Ambil nomor urut terakhir
            $lastNumber = (int) substr($lastSiswa->id_siswa, -3);
            $nextNumber = $lastNumber + 1;
            
            Log::info("Last siswa ID: {$lastSiswa->id_siswa}, Last number: {$lastNumber}, Next number: {$nextNumber}");
        } else {
            // Jika belum ada siswa dengan tahun dan jurusan ini
            $nextNumber = 1;
            Log::info("No existing siswa with prefix {$prefix}, starting with number 1");
        }
        
        // Format nomor urut dengan padding nol (001, 002, dst)
        $newId = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        Log::info("Generated new ID: {$newId}");
        
        return $newId;
    }
    
    /**
     * Method untuk mengekstrak kode jurusan dari ID siswa
     * Format ID: 6 + kode jurusan + tahun (yy) + nomor urut (001)
     * 
     * @param string $idSiswa - ID siswa
     * @return string|null - Kode jurusan atau null jika tidak valid
     */
    public static function extractJurusanFromId($idSiswa)
    {
        // Validasi format ID
        if (!$idSiswa || strlen($idSiswa) < 7) {
            return null;
        }
        
        // Format ID: 6 + kode jurusan + tahun (yy) + nomor urut (001)
        // kode jurusan bisa 1-3 karakter
        if (preg_match('/^6([A-Z]+)(\d{5})$/', $idSiswa, $matches)) {
            return $matches[1]; // Kode jurusan
        }
        
        return null;
    }
    
    /**
     * Method untuk validasi format ID siswa
     * 
     * @param string $idSiswa - ID siswa yang akan divalidasi
     * @return bool - true jika valid, false jika tidak
     */
    public static function isValidId($idSiswa)
    {
        // Format dasar: dimulai dengan 6 dan diikuti 5+ karakter
        if (!preg_match('/^6.{5,}$/', $idSiswa)) {
            return false;
        }
        
        // Format dengan jurusan: 6 + kode jurusan (A-Z) + tahun (yy) + nomor urut (001)
        if (preg_match('/^6([A-Z]+)(\d{2})(\d{3})$/', $idSiswa)) {
            return true;
        }
        
        // Format tanpa jurusan: 6 + tahun (yy) + nomor urut (001)
        if (preg_match('/^6(\d{2})(\d{3})$/', $idSiswa)) {
            return true;
        }
        
        return false;
    }
}