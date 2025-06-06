<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dokter::query();
        
        // Filter berdasarkan nama atau spesialisasi
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Nama_Dokter', 'like', "%{$search}%")
                  ->orWhere('Spesialisasi', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan spesialisasi
        if ($request->has('spesialis') && $request->spesialis) {
            $query->where('Spesialisasi', $request->spesialis);
        }
        
        // Filter berdasarkan status aktif (tambahan sesuai database)
        if ($request->has('status_aktif') && $request->status_aktif !== '') {
            $query->where('status_aktif', $request->status_aktif);
        }
        
        $dokters = $query->orderBy('Nama_Dokter')->paginate(10);
        
        // Dapatkan semua spesialisasi unik untuk dropdown filter
        $spesialisasi = Dokter::select('Spesialisasi')
                            ->whereNotNull('Spesialisasi')
                            ->where('Spesialisasi', '!=', '')
                            ->distinct()
                            ->orderBy('Spesialisasi')
                            ->pluck('Spesialisasi');
        
        return view('dokter.index', compact('dokters', 'spesialisasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Dapatkan ID dokter berikutnya untuk ditampilkan di form
        $nextId = $this->getNextDokterKey();
        return view('dokter.create', compact('nextId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Id_Dokter' => 'nullable|string|max:5|unique:dokters,Id_Dokter',
            'Nama_Dokter' => 'required|string|max:50',
            'Spesialisasi' => 'nullable|string|max:25',
            'No_Telp' => 'nullable|string|max:15',
            'Alamat' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',
            'password' => 'nullable|string|min:6|max:20',
        ]);

        $data = $validatedData;
        
        // Jika ID kosong, generate ID otomatis
        if (empty($data['Id_Dokter'])) {
            $data['Id_Dokter'] = $this->getNextDokterKey();
        }
        
        // Set default status_aktif jika tidak ada
        if (!isset($data['status_aktif'])) {
            $data['status_aktif'] = 1; // Default aktif
        }
        
        // Format nomor telepon jika belum menggunakan format +62
        if (!empty($data['No_Telp']) && !str_starts_with($data['No_Telp'], '+62')) {
            // Hapus angka 0 di depan jika ada
            if (str_starts_with($data['No_Telp'], '0')) {
                $data['No_Telp'] = substr($data['No_Telp'], 1);
            }
            
            // Jika belum ada prefix +62, tambahkan
            $data['No_Telp'] = '+62' . $data['No_Telp'];
        }
        
        // Jika ada password, hash password tersebut
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        Dokter::create($data);

        return redirect()->route('dokter.index')
            ->with('success', 'Data dokter berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dokter = Dokter::where('Id_Dokter', $id)->firstOrFail();
        
        // Load related data untuk ditampilkan
        $dokter->load([
            'rekamMedis' => function($query) {
                $query->with('siswa')->orderBy('Tanggal_Jam', 'desc');
            },
            'detailPemeriksaan' => function($query) {
                $query->with('siswa')->orderBy('tanggal_jam', 'desc');
            },
            'resep' => function($query) {
                $query->with('siswa')->orderBy('Tanggal_Resep', 'desc');
            }
        ]);

        return view('dokter.show', compact('dokter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dokter = Dokter::where('Id_Dokter', $id)->firstOrFail();
        return view('dokter.edit', compact('dokter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dokter = Dokter::where('Id_Dokter', $id)->firstOrFail();
        
        $validatedData = $request->validate([
            'Id_Dokter' => [
                'required',
                'string',
                'max:5',
                Rule::unique('dokters', 'Id_Dokter')->ignore($dokter->Id_Dokter, 'Id_Dokter'),
            ],
            'Nama_Dokter' => 'required|string|max:50',
            'Spesialisasi' => 'nullable|string|max:25',
            'No_Telp' => 'nullable|string|max:15',
            'Alamat' => 'nullable|string',
            'status_aktif' => 'nullable|boolean',
            'password' => 'nullable|string|min:6|max:20',
        ]);

        $data = $validatedData;
        
        // Set default status_aktif jika tidak ada
        if (!isset($data['status_aktif'])) {
            $data['status_aktif'] = $dokter->status_aktif; // Pertahankan status sebelumnya
        }
        
        // Format nomor telepon jika belum menggunakan format +62
        if (!empty($data['No_Telp']) && !str_starts_with($data['No_Telp'], '+62')) {
            // Hapus angka 0 di depan jika ada
            if (str_starts_with($data['No_Telp'], '0')) {
                $data['No_Telp'] = substr($data['No_Telp'], 1);
            }
            
            // Jika belum ada prefix +62, tambahkan
            $data['No_Telp'] = '+62' . $data['No_Telp'];
        }
        
        // Jika ada password baru, hash password tersebut
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $dokter->update($data);

        return redirect()->route('dokter.index')
            ->with('success', 'Data dokter berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dokter = Dokter::where('Id_Dokter', $id)->firstOrFail();
        
        // Cek apakah dokter memiliki data terkait berdasarkan foreign key di database
        $relatedRecordsCount = 0;
        
        // Cek rekam medis
        if ($dokter->rekamMedis()->exists()) {
            $relatedRecordsCount++;
        }
        
        // Cek detail pemeriksaan
        if ($dokter->detailPemeriksaan()->exists()) {
            $relatedRecordsCount++;
        }
        
        // Cek resep
        if ($dokter->resep()->exists()) {
            $relatedRecordsCount++;
        }

        if ($relatedRecordsCount > 0) {
            return redirect()->route('dokter.index')
                ->with('error', 'Dokter tidak dapat dihapus karena masih memiliki data terkait (rekam medis, pemeriksaan, atau resep).');
        }
        
        $dokter->delete();

        return redirect()->route('dokter.index')
            ->with('success', 'Data dokter berhasil dihapus.');
    }
    
    /**
     * Soft delete - Nonaktifkan dokter alih-alih menghapus
     */
    public function deactivate(string $id)
    {
        $dokter = Dokter::where('Id_Dokter', $id)->firstOrFail();
        
        $dokter->update(['status_aktif' => 0]);
        
        return redirect()->route('dokter.index')
            ->with('success', 'Dokter berhasil dinonaktifkan.');
    }
    
    /**
     * Aktifkan kembali dokter yang dinonaktifkan
     */
    public function activate(string $id)
    {
        $dokter = Dokter::where('Id_Dokter', $id)->firstOrFail();
        
        $dokter->update(['status_aktif' => 1]);
        
        return redirect()->route('dokter.index')
            ->with('success', 'Dokter berhasil diaktifkan kembali.');
    }
    
    /**
     * Generate ID dokter berikutnya
     */
    private function getNextDokterKey()
    {
        $lastDokter = Dokter::orderBy('Id_Dokter', 'desc')->first();
        
        if (!$lastDokter) {
            return 'DO001';
        }
        
        // Extract angka dari ID terakhir (DO001 -> 001)
        $lastNumber = (int) substr($lastDokter->Id_Dokter, 2);
        $nextNumber = $lastNumber + 1;
        
        return 'DO' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}