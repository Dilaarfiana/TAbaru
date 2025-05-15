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
        
        $dokters = $query->orderBy('Nama_Dokter')->paginate(10);
        
        // Dapatkan semua spesialisasi unik untuk dropdown filter
        $spesialisasi = Dokter::select('Spesialisasi')
                            ->whereNotNull('Spesialisasi')
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
        $nextId = Dokter::getNextId();
        return view('dokter.create', compact('nextId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Id_Dokter' => 'nullable|string|max:5|unique:dokters',
            'Nama_Dokter' => 'required|string|max:50',
            'Spesialisasi' => 'nullable|string|max:25',
            'No_Telp' => 'nullable|string|max:15',
            'Alamat' => 'nullable|string',
            'password' => 'nullable|string|min:6|max:20',
        ]);

        $data = $validatedData;
        
        // Jika ID kosong, hapus dari data agar model bisa menghasilkan ID otomatis
        if (empty($data['Id_Dokter'])) {
            unset($data['Id_Dokter']);
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
    public function show(Dokter $dokter)
    {
        return view('dokter.show', compact('dokter'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dokter $dokter)
    {
        return view('dokter.edit', compact('dokter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dokter $dokter)
    {
        $validatedData = $request->validate([
            'Id_Dokter' => [
                'required',
                'string',
                'max:5',
                Rule::unique('dokters')->ignore($dokter->Id_Dokter, 'Id_Dokter'),
            ],
            'Nama_Dokter' => 'required|string|max:50',
            'Spesialisasi' => 'nullable|string|max:25',
            'No_Telp' => 'nullable|string|max:15',
            'Alamat' => 'nullable|string',
            'password' => 'nullable|string|min:6|max:20',
        ]);

        $data = $validatedData;
        
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
    public function destroy(Dokter $dokter)
    {
        // Cek apakah dokter memiliki data terkait
        $relatedRecordsExist = $dokter->rekamMedis()->exists() || 
            $dokter->detailPemeriksaan()->exists() || 
            $dokter->pemeriksaanHarian()->exists() || 
            $dokter->resep()->exists();

        if ($relatedRecordsExist) {
            return redirect()->route('dokter.index')
                ->with('error', 'Dokter tidak dapat dihapus karena masih memiliki data terkait.');
        }
        
        $dokter->delete();

        return redirect()->route('dokter.index')
            ->with('success', 'Data dokter berhasil dihapus.');
    }
}