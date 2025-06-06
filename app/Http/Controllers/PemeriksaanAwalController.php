<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanAwal;
use App\Models\DetailPemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PemeriksaanAwalController extends Controller
{
    /**
     * Get route names based on user role
     */
    private function getRouteNames()
    {
        $userLevel = session('user_level');
        
        $routes = [
            'admin' => [
                'index' => 'pemeriksaan_awal.index',
                'create' => 'pemeriksaan_awal.create',
                'show' => 'pemeriksaan_awal.show',
                'edit' => 'pemeriksaan_awal.edit',
                'destroy' => 'pemeriksaan_awal.destroy',
            ],
            'petugas' => [
                'index' => 'petugas.pemeriksaan_awal.index',
                'create' => 'petugas.pemeriksaan_awal.create',
                'show' => 'petugas.pemeriksaan_awal.show',
                'edit' => 'petugas.pemeriksaan_awal.edit',
                'destroy' => null, // Petugas tidak bisa delete
            ],
            'dokter' => [
                'index' => 'dokter.pemeriksaan_awal.index',
                'create' => null,
                'show' => 'dokter.pemeriksaan_awal.show',
                'edit' => null,
                'destroy' => null,
            ],
            'orang_tua' => [
                'index' => null,
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
            'orang_tua' => [] // No access
        ];
        
        $userPermissions = $permissions[$userLevel] ?? [];
        return in_array($action, $userPermissions);
    }

    /**
     * Block access for orang_tua
     */
    private function blockOrangTuaAccess()
    {
        if (session('user_level') === 'orang_tua') {
            Log::warning('Orang tua mencoba mengakses PemeriksaanAwal controller', [
                'user_id' => session('user_id'),
                'user_name' => session('user_name'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'timestamp' => now()
            ]);
            
            abort(403, 'Akses ditolak. Orang tua tidak memiliki izin untuk mengakses fitur pemeriksaan awal.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('index')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar pemeriksaan awal.');
        }

        // Debug log
        Log::info("PemeriksaanAwal Index dipanggil dengan parameter: ", $request->all());
        
        // Query dasar dengan eager loading relasi
        $query = PemeriksaanAwal::with([
            'detailPemeriksaan', 
            'detailPemeriksaan.siswa', 
            'detailPemeriksaan.dokter'
        ]);
        
        // Jika parameter show_all atau reset ada, tampilkan semua data tanpa filter
        if (!$request->has('show_all') && !$request->has('reset')) {
            // Filter berdasarkan status nyeri
            if ($request->filled('status_nyeri')) {
                $query->where('status_nyeri', $request->status_nyeri);
            }
            
            // Filter berdasarkan suhu
            if ($request->filled('suhu')) {
                if ($request->suhu == 'normal') {
                    $query->whereBetween('suhu', [36.0, 37.5]);
                } elseif ($request->suhu == 'tinggi') {
                    $query->where('suhu', '>', 37.5);
                } elseif ($request->suhu == 'rendah') {
                    $query->where('suhu', '<', 36.0);
                }
            }
            
            // Filter berdasarkan tanggal
            if ($request->filled('tanggal')) {
                $query->whereDate('created_at', $request->tanggal);
            }
            
            // Pencarian berdasarkan keyword
            if ($request->filled('keyword')) {
                $keyword = '%' . $request->keyword . '%';
                $query->where(function($q) use ($keyword) {
                    $q->where('id_preawal', 'like', $keyword)
                      ->orWhere('id_detprx', 'like', $keyword)
                      ->orWhere('pemeriksaan', 'like', $keyword)
                      ->orWhere('keluhan_dahulu', 'like', $keyword)
                      ->orWhere('karakteristik', 'like', $keyword)
                      ->orWhere('lokasi', 'like', $keyword);
                });
            }
        }
        
        // Pengurutan
        $sortBy = $request->input('sort_by', 'id_preawal');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->input('per_page', 15);
        $pemeriksaanAwals = $query->paginate($perPage)->appends($request->except(['reset', 'show_all']));
        
        return view('pemeriksaan_awal.index', compact('pemeriksaanAwals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('create')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah pemeriksaan awal baru.');
        }
        
        // Ambil semua detail pemeriksaan yang tersedia
        $detailPemeriksaans = DetailPemeriksaan::with(['siswa', 'dokter'])
            ->orderBy('tanggal_jam', 'desc')
            ->get();
        
        // Generate ID berikutnya
        $id = $this->getNextSequenceId();
        
        return view('pemeriksaan_awal.create', compact('detailPemeriksaans', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('store')) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan pemeriksaan awal.');
        }
        
        // Validasi request
        $validator = Validator::make($request->all(), [
            'id_preawal' => 'required|string|max:10|unique:pemeriksaan_awals,id_preawal',
            'id_detprx' => 'required|string|exists:detail_pemeriksaans,id_detprx',
            'pemeriksaan' => 'nullable|string',
            'keluhan_dahulu' => 'nullable|string|max:255',
            'suhu' => 'nullable|numeric|between:30,45',
            'nadi' => 'nullable|numeric|between:0,300',
            'tegangan' => 'nullable|string|max:10',
            'pernapasan' => 'nullable|integer|between:0,100',
            'tipe' => 'nullable|integer|in:1,2,3',
            'status_nyeri' => 'nullable|integer|in:0,1,2,3',
            'karakteristik' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:100',
            'durasi' => 'nullable|string|max:50',
            'frekuensi' => 'nullable|string|max:50',
        ], [
            'id_preawal.unique' => 'ID Pemeriksaan Awal sudah digunakan',
            'id_detprx.required' => 'Detail Pemeriksaan wajib dipilih',
            'id_detprx.exists' => 'Detail Pemeriksaan tidak valid',
            'suhu.between' => 'Suhu harus antara 30-45°C',
            'nadi.between' => 'Nadi harus antara 0-300 bpm',
            'pernapasan.between' => 'Pernapasan harus antara 0-100 rpm',
            'tipe.in' => 'Tipe pemeriksaan tidak valid',
            'status_nyeri.in' => 'Status nyeri tidak valid',
        ]);

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['create'])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $pemeriksaanData = $request->all();
            
            Log::info('Menyimpan pemeriksaan awal baru', [
                'id_preawal' => $pemeriksaanData['id_preawal'],
                'id_detprx' => $pemeriksaanData['id_detprx'],
                'user_level' => session('user_level')
            ]);
            
            $pemeriksaanAwal = PemeriksaanAwal::create($pemeriksaanData);
            
            DB::commit();
            
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['index'])
                ->with('success', 'Pemeriksaan awal berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saat menambahkan pemeriksaan awal: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['create'])
                ->with('error', 'Terjadi kesalahan. Pemeriksaan awal gagal ditambahkan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('show')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail pemeriksaan awal.');
        }
        
        $pemeriksaanAwal = PemeriksaanAwal::with([
                'detailPemeriksaan', 
                'detailPemeriksaan.siswa', 
                'detailPemeriksaan.dokter'
            ])
            ->findOrFail($id);
        
        return view('pemeriksaan_awal.show', compact('pemeriksaanAwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pemeriksaan awal.');
        }
        
        $pemeriksaanAwal = PemeriksaanAwal::findOrFail($id);
        $detailPemeriksaans = DetailPemeriksaan::with(['siswa', 'dokter'])
            ->orderBy('tanggal_jam', 'desc')
            ->get();
            
        return view('pemeriksaan_awal.edit', compact('pemeriksaanAwal', 'detailPemeriksaans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('update')) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui pemeriksaan awal.');
        }
        
        $validator = Validator::make($request->all(), [
            'id_detprx' => 'required|string|exists:detail_pemeriksaans,id_detprx',
            'pemeriksaan' => 'nullable|string',
            'keluhan_dahulu' => 'nullable|string|max:255',
            'suhu' => 'nullable|numeric|between:30,45',
            'nadi' => 'nullable|numeric|between:0,300',
            'tegangan' => 'nullable|string|max:10',
            'pernapasan' => 'nullable|integer|between:0,100',
            'tipe' => 'nullable|integer|in:1,2,3',
            'status_nyeri' => 'nullable|integer|in:0,1,2,3',
            'karakteristik' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:100',
            'durasi' => 'nullable|string|max:50',
            'frekuensi' => 'nullable|string|max:50',
        ], [
            'id_detprx.required' => 'Detail Pemeriksaan wajib dipilih',
            'id_detprx.exists' => 'Detail Pemeriksaan tidak valid',
            'suhu.between' => 'Suhu harus antara 30-45°C',
            'nadi.between' => 'Nadi harus antara 0-300 bpm',
            'pernapasan.between' => 'Pernapasan harus antara 0-100 rpm',
            'tipe.in' => 'Tipe pemeriksaan tidak valid',
            'status_nyeri.in' => 'Status nyeri tidak valid',
        ]);

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['edit'], $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $pemeriksaanAwal = PemeriksaanAwal::findOrFail($id);
            $updateData = $request->all();
            
            Log::info('Memperbarui pemeriksaan awal', [
                'id_preawal' => $id,
                'id_detprx' => $updateData['id_detprx'],
                'user_level' => session('user_level')
            ]);
            
            $pemeriksaanAwal->update($updateData);
            
            DB::commit();
            
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['index'])
                ->with('success', 'Data pemeriksaan awal berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saat memperbarui pemeriksaan awal: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['edit'], $id)
                ->with('error', 'Terjadi kesalahan. Data pemeriksaan awal gagal diperbarui: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pemeriksaan awal.');
        }
        
        try {
            DB::beginTransaction();
            
            $pemeriksaanAwal = PemeriksaanAwal::findOrFail($id);
            $idPreawal = $pemeriksaanAwal->id_preawal;
            $pemeriksaanAwal->delete();
            
            DB::commit();

            Log::info('Pemeriksaan awal berhasil dihapus', [
                'id_preawal' => $idPreawal,
                'user_level' => session('user_level')
            ]);
            
            return redirect()->route('pemeriksaan_awal.index')
                ->with('success', "Pemeriksaan awal '{$idPreawal}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saat menghapus pemeriksaan awal: ' . $e->getMessage());
            
            return redirect()->route('pemeriksaan_awal.index')
                ->with('error', 'Terjadi kesalahan. Pemeriksaan awal gagal dihapus: ' . $e->getMessage());
        }
    }

    /**
     * Generate next sequence ID for pemeriksaan awal
     */
    private function getNextSequenceId()
    {
        try {
            DB::beginTransaction();
            
            $sequence = DB::table('sequence_ids')
                ->where('sequence_name', 'pemeriksaan_awal_id')
                ->lockForUpdate()
                ->first();
            
            if (!$sequence) {
                DB::table('sequence_ids')->insert([
                    'sequence_name' => 'pemeriksaan_awal_id',
                    'current_value' => 0
                ]);
                $currentValue = 0;
            } else {
                $currentValue = $sequence->current_value;
            }
            
            $nextValue = $currentValue + 1;
            
            DB::table('sequence_ids')
                ->where('sequence_name', 'pemeriksaan_awal_id')
                ->update(['current_value' => $nextValue]);
            
            $formattedId = "PA" . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
            
            DB::commit();
            
            return $formattedId;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error generating sequence ID untuk pemeriksaan awal: ' . $e->getMessage());
            
            // Fallback method
            $lastId = PemeriksaanAwal::orderBy('id_preawal', 'desc')->first();
            
            if ($lastId) {
                $lastNumber = intval(substr($lastId->id_preawal, 2));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            return "PA" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Get recent pemeriksaan awal for dashboard (API)
     */
    public function getRecent()
    {
        try {
            // Block orang_tua access
            $this->blockOrangTuaAccess();
            
            if (!$this->hasPermission('index')) {
                return response()->json(['error' => 'Unauthorized access denied'], 403);
            }

            $recentPemeriksaan = PemeriksaanAwal::with(['detailPemeriksaan.siswa'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
            return response()->json($recentPemeriksaan);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data pemeriksaan awal terbaru: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data'], 500);
        }
    }
    
    /**
     * Get detail pemeriksaan data (API)
     */
    public function getDetailPemeriksaan(Request $request)
    {
        try {
            // Block orang_tua access
            $this->blockOrangTuaAccess();
            
            if (!$this->hasPermission('index')) {
                return response()->json(['error' => 'Unauthorized access denied'], 403);
            }

            $detailPemeriksaans = DetailPemeriksaan::with(['siswa', 'dokter'])
                ->orderBy('tanggal_jam', 'desc')
                ->get();
                
            return response()->json($detailPemeriksaans);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data detail pemeriksaan: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data detail pemeriksaan'], 500);
        }
    }
}