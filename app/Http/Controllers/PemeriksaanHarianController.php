<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanHarian;
use App\Models\Siswa;
use App\Models\PetugasUKS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PemeriksaanHarianController extends Controller
{
    /**
     * Get route names based on user role
     */
    private function getRouteNames()
    {
        $userLevel = session('user_level');
        
        $routes = [
            'admin' => [
                'index' => 'pemeriksaan_harian.index',
                'create' => 'pemeriksaan_harian.create',
                'show' => 'pemeriksaan_harian.show',
                'edit' => 'pemeriksaan_harian.edit',
                'destroy' => 'pemeriksaan_harian.destroy',
            ],
            'petugas' => [
                'index' => 'petugas.pemeriksaan_harian.index',
                'create' => 'petugas.pemeriksaan_harian.create',
                'show' => 'petugas.pemeriksaan_harian.show',
                'edit' => 'petugas.pemeriksaan_harian.edit',
                'destroy' => null,
            ],
            'dokter' => [
                'index' => 'dokter.pemeriksaan_harian.index',
                'create' => null,
                'show' => 'dokter.pemeriksaan_harian.show',
                'edit' => null,
                'destroy' => null,
            ],
            'orang_tua' => [
                'index' => 'orangtua.riwayat.pemeriksaan_harian',
                'create' => null,
                'show' => null,
                'edit' => null,
                'destroy' => null,
            ]
        ];
        
        return $routes[$userLevel] ?? $routes['admin'];
    }

    /**
     * Check if current user has permission for specific action
     */
    private function hasPermission($action)
    {
        $userLevel = session('user_level');
        
        $permissions = [
            'admin' => ['index', 'create', 'store', 'show', 'edit', 'update', 'delete'],
            'petugas' => ['index', 'create', 'store', 'show', 'edit', 'update'],
            'dokter' => ['index', 'show'],
            'orang_tua' => ['index', 'show'] // Limited to own child's data
        ];
        
        $userPermissions = $permissions[$userLevel] ?? [];
        return in_array($action, $userPermissions);
    }

    /**
     * Apply data filter based on user role
     */
    private function applyRoleFilter($query)
    {
        $userLevel = session('user_level');
        
        if ($userLevel === 'orang_tua') {
            $siswaId = session('siswa_id');
            if ($siswaId) {
                $query->where('Id_Siswa', $siswaId);
            }
        }
        
        return $query;
    }

    /**
     * Menampilkan daftar pemeriksaan harian
     */
    public function index(Request $request)
    {
        // Check permission
        if (!$this->hasPermission('index')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar pemeriksaan harian.');
        }

        // Query dasar
        $query = PemeriksaanHarian::with(['siswa', 'petugasUKS']);
        
        // Apply role-based filter
        $query = $this->applyRoleFilter($query);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Id_Harian', 'like', "%{$search}%")
                  ->orWhere('Hasil_Pemeriksaan', 'like', "%{$search}%")
                  ->orWhereHas('siswa', function($q) use ($search) {
                      $q->where('nama_siswa', 'like', "%{$search}%");
                  });
            });
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('Tanggal_Jam', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('Tanggal_Jam', '<=', $request->date_to);
        }

        // Petugas filter (for admin and others)
        if ($request->filled('petugas_id') && session('user_level') !== 'orang_tua') {
            $query->where('NIP', $request->petugas_id);
        }

        $pemeriksaanHarian = $query->orderBy('Tanggal_Jam', 'desc')->paginate(15);
        $pemeriksaanHarian->appends($request->all());

        // Additional data for filters
        $petugasList = session('user_level') !== 'orang_tua' ? 
            PetugasUKS::orderBy('nama_petugas_uks')->get() : collect();

        return view('pemeriksaan_harian.index', compact('pemeriksaanHarian', 'petugasList'));
    }

    /**
     * Menampilkan form untuk membuat data baru
     */
    public function create()
    {
        // Check permission
        if (!$this->hasPermission('create')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah pemeriksaan harian baru.');
        }

        $siswaList = Siswa::where('status_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();
            
        $petugasList = PetugasUKS::where('status_aktif', 1)
            ->orderBy('nama_petugas_uks')
            ->get();
        
        // Generate ID baru - PERBAIKAN: gunakan variabel $id bukan $newId
        $id = $this->generateNewId();
        
        return view('pemeriksaan_harian.create', compact('siswaList', 'petugasList', 'id'));
    }

    /**
     * Menyimpan data baru ke database
     */
    public function store(Request $request)
    {
        // Check permission
        if (!$this->hasPermission('store')) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan pemeriksaan harian.');
        }

        // Validasi input - PERBAIKAN: buat Id_Harian tidak perlu unique karena sudah di-generate
        $validator = Validator::make($request->all(), [
            'Id_Harian' => 'required|max:5',
            'Tanggal_Jam' => 'required|date',
            'Hasil_Pemeriksaan' => 'required|string',
            'Id_Siswa' => 'required|exists:siswas,id_siswa',
            'NIP' => 'required|exists:petugas_uks,NIP',
        ], [
            'Id_Harian.required' => 'ID Harian wajib diisi',
            'Tanggal_Jam.required' => 'Tanggal dan jam pemeriksaan wajib diisi',
            'Hasil_Pemeriksaan.required' => 'Hasil pemeriksaan wajib diisi',
            'Id_Siswa.required' => 'Siswa wajib dipilih',
            'Id_Siswa.exists' => 'Siswa tidak valid',
            'NIP.required' => 'Petugas UKS wajib dipilih',
            'NIP.exists' => 'Petugas UKS tidak valid',
        ]);

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['create'])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // PERBAIKAN: Cek apakah ID sudah ada, jika ya generate ulang
            $idHarian = $request->Id_Harian;
            if (PemeriksaanHarian::where('Id_Harian', $idHarian)->exists()) {
                $idHarian = $this->generateNewId();
            }

            // Simpan data dengan ID yang sudah dipastikan unik
            $data = $request->all();
            $data['Id_Harian'] = $idHarian;
            
            PemeriksaanHarian::create($data);

            DB::commit();

            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('success', 'Data pemeriksaan harian berhasil ditambahkan dengan ID: ' . $idHarian);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menyimpan pemeriksaan harian: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['create'])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail pemeriksaan harian
     */
    public function show($id)
    {
        // Check permission
        if (!$this->hasPermission('show')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail pemeriksaan harian.');
        }

        $query = PemeriksaanHarian::with(['siswa', 'petugasUKS']);
        
        // Apply role-based filter
        $query = $this->applyRoleFilter($query);
        
        $pemeriksaanHarian = $query->where('Id_Harian', $id)->firstOrFail();
            
        return view('pemeriksaan_harian.show', compact('pemeriksaanHarian'));
    }

    /**
     * Menampilkan form untuk mengedit data
     */
    public function edit($id)
    {
        // Check permission
        if (!$this->hasPermission('edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pemeriksaan harian.');
        }

        $pemeriksaanHarian = PemeriksaanHarian::where('Id_Harian', $id)->firstOrFail();
        
        $siswaList = Siswa::where('status_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();
            
        $petugasList = PetugasUKS::where('status_aktif', 1)
            ->orderBy('nama_petugas_uks')
            ->get();
        
        return view('pemeriksaan_harian.edit', compact('pemeriksaanHarian', 'siswaList', 'petugasList'));
    }

    /**
     * Mengupdate data di database
     */
    public function update(Request $request, $id)
    {
        // Check permission
        if (!$this->hasPermission('update')) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui pemeriksaan harian.');
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'Tanggal_Jam' => 'required|date',
            'Hasil_Pemeriksaan' => 'required|string',
            'Id_Siswa' => 'required|exists:siswas,id_siswa',
            'NIP' => 'required|exists:petugas_uks,NIP',
        ]);

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['edit'], $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update data
            $pemeriksaanHarian = PemeriksaanHarian::where('Id_Harian', $id)->firstOrFail();
            $pemeriksaanHarian->update($request->all());

            DB::commit();

            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('success', 'Data pemeriksaan harian berhasil diperbarui!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error mengupdate pemeriksaan harian: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['edit'], $id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghapus data dari database
     */
    public function destroy($id)
    {
        // Check permission
        if (!$this->hasPermission('delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pemeriksaan harian.');
        }

        try {
            DB::beginTransaction();

            $pemeriksaanHarian = PemeriksaanHarian::where('Id_Harian', $id)->firstOrFail();
            $pemeriksaanHarian->delete();

            DB::commit();

            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('success', 'Data pemeriksaan harian berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menghapus pemeriksaan harian: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate new ID - PERBAIKAN: tambah pengecekan yang lebih robust
     */
    private function generateNewId()
    {
        // Cari record terakhir berdasarkan ID yang paling tinggi
        $lastRecord = PemeriksaanHarian::orderByRaw('CAST(SUBSTRING(Id_Harian, 3) AS UNSIGNED) DESC')->first();
        
        if (!$lastRecord) {
            return 'PH001';
        }
        
        // Extract nomor dari ID terakhir (misal PH005 -> 5)
        $lastNumber = intval(substr($lastRecord->Id_Harian, 2));
        $newNumber = $lastNumber + 1;
        
        // Format dengan 3 digit (001, 002, dst)
        return 'PH' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * API method untuk mendapatkan detail pemeriksaan harian
     */
    public function getDetailHarian($siswaId, $harianId)
    {
        try {
            if (!$this->hasPermission('show')) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            $query = PemeriksaanHarian::with(['siswa', 'petugasUKS']);
            
            // Apply role-based filter
            $query = $this->applyRoleFilter($query);
            
            $pemeriksaan = $query->where('Id_Harian', $harianId)
                ->where('Id_Siswa', $siswaId)
                ->first();
            
            if (!$pemeriksaan) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $pemeriksaan
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error get detail harian: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Method untuk halaman laporan pemeriksaan harian
     */
    public function harian(Request $request)
    {
        // Check permission
        if (!$this->hasPermission('index')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan pemeriksaan harian.');
        }

        // Query data dengan filter
        $query = PemeriksaanHarian::with(['siswa', 'petugasUKS']);
        
        // Apply role-based filter
        $query = $this->applyRoleFilter($query);
        
        // Apply filters from request
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Id_Harian', 'like', "%{$search}%")
                  ->orWhere('Hasil_Pemeriksaan', 'like', "%{$search}%")
                  ->orWhereHas('siswa', function($q) use ($search) {
                      $q->where('nama_siswa', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('Tanggal_Jam', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('Tanggal_Jam', '<=', $request->date_to);
        }
        
        if ($request->filled('petugas_id')) {
            $query->where('NIP', $request->petugas_id);
        }
        
        $pemeriksaanHarian = $query->orderBy('Tanggal_Jam', 'desc')->paginate(15);
        $pemeriksaanHarian->appends($request->all());
        
        // Data untuk filter
        $petugasList = session('user_level') !== 'orang_tua' ? 
            PetugasUKS::where('status_aktif', 1)->orderBy('nama_petugas_uks')->get() : collect();
        
        return view('laporan.pemeriksaan_harian', compact('pemeriksaanHarian', 'petugasList'));
    }
}