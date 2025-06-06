<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanFisik;
use App\Models\DetailPemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PemeriksaanFisikController extends Controller
{
    /**
     * Get route names based on user role
     */
    private function getRouteNames()
    {
        $userLevel = session('user_level');
        
        $routes = [
            'admin' => [
                'index' => 'pemeriksaan_fisik.index',
                'create' => 'pemeriksaan_fisik.create',
                'show' => 'pemeriksaan_fisik.show',
                'edit' => 'pemeriksaan_fisik.edit',
                'destroy' => 'pemeriksaan_fisik.destroy',
            ],
            'petugas' => [
                'index' => 'petugas.pemeriksaan_fisik.index',
                'create' => 'petugas.pemeriksaan_fisik.create',
                'show' => 'petugas.pemeriksaan_fisik.show',
                'edit' => 'petugas.pemeriksaan_fisik.edit',
                'destroy' => null,
            ],
            'dokter' => [
                'index' => 'dokter.pemeriksaan_fisik.index',
                'create' => null,
                'show' => 'dokter.pemeriksaan_fisik.show',
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
            'orang_tua' => []
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
            Log::warning('Orang tua mencoba mengakses PemeriksaanFisik controller', [
                'user_id' => session('user_id'),
                'user_name' => session('user_name'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'timestamp' => now()
            ]);
            
            abort(403, 'Akses ditolak. Orang tua tidak memiliki izin untuk mengakses fitur pemeriksaan fisik.');
        }
    }

    /**
     * Display a listing of the pemeriksaan fisik.
     */
    public function index(Request $request)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('index')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar pemeriksaan fisik.');
        }

        // Debug log
        Log::info("PemeriksaanFisik Index dipanggil dengan parameter: ", $request->all());

        // Membuat query dasar dengan relasi
        $query = PemeriksaanFisik::with(['detailPemeriksaan.siswa', 'detailPemeriksaan.dokter']);
        
        // Jika parameter show_all atau reset ada, tampilkan semua data tanpa filter
        if (!$request->has('show_all') && !$request->has('reset')) {
            // Pencarian berdasarkan keyword
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id_prefisik', 'like', "%{$search}%")
                      ->orWhere('id_detprx', 'like', "%{$search}%")
                      ->orWhere('masalah_aktif', 'like', "%{$search}%")
                      ->orWhere('rencana_medis_dan_terapi', 'like', "%{$search}%")
                      ->orWhereHas('detailPemeriksaan.siswa', function($q) use ($search) {
                          $q->where('nama_siswa', 'like', "%{$search}%");
                      });
                });
            }

            // Filter berdasarkan tinggi badan
            if ($request->filled('tinggi_filter')) {
                switch ($request->tinggi_filter) {
                    case 'kurang':
                        $query->where('tinggi_badan', '<', 150);
                        break;
                    case 'normal':
                        $query->whereBetween('tinggi_badan', [150, 180]);
                        break;
                    case 'tinggi':
                        $query->where('tinggi_badan', '>', 180);
                        break;
                }
            }

            // Filter berdasarkan berat badan
            if ($request->filled('berat_filter')) {
                switch ($request->berat_filter) {
                    case 'kurang':
                        $query->where('berat_badan', '<', 50);
                        break;
                    case 'normal':
                        $query->whereBetween('berat_badan', [50, 80]);
                        break;
                    case 'lebih':
                        $query->where('berat_badan', '>', 80);
                        break;
                }
            }

            // Filter berdasarkan BMI menggunakan scope (jika ada)
            if ($request->filled('bmi_filter')) {
                // Jika ada method withBmiCategory di model
                if (method_exists(PemeriksaanFisik::class, 'withBmiCategory')) {
                    $query->withBmiCategory($request->bmi_filter);
                }
            }

            // Filter berdasarkan tanggal
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        // Menambahkan pengurutan
        $query->orderBy('created_at', 'desc');

        // Mengambil data dengan pagination
        $pemeriksaanFisiks = $query->paginate(10);
        $pemeriksaanFisiks->appends($request->except(['reset', 'show_all']));

        // Statistik untuk dashboard
        $statistics = $this->getStatisticsData();

        return view('pemeriksaan_fisik.index', compact('pemeriksaanFisiks', 'statistics'));
    }

    /**
     * Show the form for creating a new pemeriksaan fisik.
     */
    public function create()
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('create')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah pemeriksaan fisik baru.');
        }
        
        $detailPemeriksaans = DetailPemeriksaan::with('siswa')
            ->whereDoesntHave('pemeriksaanFisik')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('pemeriksaan_fisik.create', compact('detailPemeriksaans'));
    }

    /**
     * Store a newly created pemeriksaan fisik in storage.
     */
    public function store(Request $request)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('store')) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan pemeriksaan fisik.');
        }
        
        $validator = $this->validatePemeriksaanFisik($request->all());

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['create'])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            Log::info('Menyimpan pemeriksaan fisik baru', [
                'id_detprx' => $request->id_detprx,
                'user_level' => session('user_level')
            ]);

            // Model akan auto-generate ID
            $pemeriksaanFisik = PemeriksaanFisik::create($request->all());

            // Update status pemeriksaan jika diperlukan
            $this->updateDetailPemeriksaanStatus($request->id_detprx);

            DB::commit();

            // Redirect ke index berdasarkan role
            $routes = $this->getRouteNames();

            return redirect()->route($routes['index'])
                ->with('success', 'Pemeriksaan Fisik berhasil ditambahkan dengan ID: ' . $pemeriksaanFisik->id_prefisik);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan pemeriksaan fisik: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['create'])
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified pemeriksaan fisik.
     */
    public function show(string $id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('show')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail pemeriksaan fisik.');
        }
        
        $pemeriksaanFisik = PemeriksaanFisik::with([
            'detailPemeriksaan.siswa', 
            'detailPemeriksaan.dokter',
            'detailPemeriksaan.petugasUKS'
        ])->where('id_prefisik', $id)->firstOrFail();
        
        return view('pemeriksaan_fisik.show', compact('pemeriksaanFisik'));
    }

    /**
     * Show the form for editing the specified pemeriksaan fisik.
     */
    public function edit(string $id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pemeriksaan fisik.');
        }
        
        $pemeriksaanFisik = PemeriksaanFisik::where('id_prefisik', $id)->firstOrFail();
        $detailPemeriksaans = DetailPemeriksaan::with('siswa')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('pemeriksaan_fisik.edit', compact('pemeriksaanFisik', 'detailPemeriksaans'));
    }

    /**
     * Update the specified pemeriksaan fisik in storage.
     */
    public function update(Request $request, string $id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('update')) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui pemeriksaan fisik.');
        }
        
        $pemeriksaanFisik = PemeriksaanFisik::where('id_prefisik', $id)->firstOrFail();

        $validator = $this->validatePemeriksaanFisik($request->all(), $pemeriksaanFisik->id_prefisik);

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['edit'], $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            Log::info('Memperbarui pemeriksaan fisik', [
                'id_prefisik' => $id,
                'user_level' => session('user_level')
            ]);

            $pemeriksaanFisik->update($request->all());

            DB::commit();

            // Redirect ke index berdasarkan role
            $routes = $this->getRouteNames();

            return redirect()->route($routes['index'])
                ->with('success', 'Pemeriksaan Fisik berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat update pemeriksaan fisik: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['edit'], $id)
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified pemeriksaan fisik from storage.
     */
    public function destroy(string $id)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check permission
        if (!$this->hasPermission('delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pemeriksaan fisik.');
        }
        
        try {
            DB::beginTransaction();

            $pemeriksaanFisik = PemeriksaanFisik::where('id_prefisik', $id)->firstOrFail();
            $idPrefisik = $pemeriksaanFisik->id_prefisik;
            
            // Update status detail pemeriksaan jika perlu
            $detailPemeriksaan = $pemeriksaanFisik->detailPemeriksaan;
            if ($detailPemeriksaan && $detailPemeriksaan->status_pemeriksaan == 'lengkap') {
                // Cek apakah masih ada pemeriksaan awal
                if ($detailPemeriksaan->pemeriksaanAwal) {
                    $detailPemeriksaan->update(['status_pemeriksaan' => 'belum lengkap']);
                } else {
                    $detailPemeriksaan->update(['status_pemeriksaan' => 'belum dimulai']);
                }
            }
            
            $pemeriksaanFisik->delete();

            DB::commit();

            Log::info('Pemeriksaan fisik berhasil dihapus', [
                'id_prefisik' => $idPrefisik,
                'user_level' => session('user_level')
            ]);

            return redirect()->route('pemeriksaan_fisik.index')
                ->with('success', "Pemeriksaan fisik '{$idPrefisik}' berhasil dihapus.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus pemeriksaan fisik: ' . $e->getMessage());
            return redirect()->route('pemeriksaan_fisik.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get recent pemeriksaan fisik for dashboard
     */
    public function getRecent()
    {
        try {
            // Block orang_tua access
            $this->blockOrangTuaAccess();
            
            // Check basic permission
            if (!$this->hasPermission('index')) {
                return response()->json(['error' => 'Unauthorized access denied'], 403);
            }

            $recentPemeriksaan = PemeriksaanFisik::with('detailPemeriksaan.siswa')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    $bmi = null;
                    $bmiKategori = null;
                    
                    if ($item->tinggi_badan && $item->berat_badan) {
                        $tinggiM = $item->tinggi_badan / 100;
                        $bmi = round($item->berat_badan / ($tinggiM * $tinggiM), 2);
                        $bmiKategori = $this->getBMICategory($bmi);
                    }
                    
                    return [
                        'id_prefisik' => $item->id_prefisik,
                        'nama_siswa' => $item->detailPemeriksaan->siswa->nama_siswa ?? 'N/A',
                        'tinggi_badan' => $item->tinggi_badan,
                        'berat_badan' => $item->berat_badan,
                        'bmi' => $bmi,
                        'bmi_kategori' => $bmiKategori,
                        'created_at' => $item->created_at->format('d/m/Y H:i'),
                    ];
                });
                
            return response()->json($recentPemeriksaan);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data pemeriksaan fisik terbaru: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data'], 500);
        }
    }

    /**
     * Calculate BMI for patient via AJAX
     */
    public function calculateBMI(Request $request)
    {
        // Block orang_tua access
        $this->blockOrangTuaAccess();
        
        // Check basic permission
        if (!$this->hasPermission('index')) {
            return response()->json(['error' => 'Unauthorized access denied'], 403);
        }

        $validator = Validator::make($request->all(), [
            'tinggi_badan' => 'required|numeric|between:50,250',
            'berat_badan' => 'required|numeric|between:10,200'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Data tidak valid'], 400);
        }

        $tinggi = $request->tinggi_badan / 100;
        $berat = $request->berat_badan;
        
        $bmi = $berat / ($tinggi * $tinggi);
        $kategori = $this->getBMICategory($bmi);
        
        return response()->json([
            'bmi' => round($bmi, 2),
            'kategori' => $kategori,
            'status' => $this->getBMIStatus($bmi)
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            // Block orang_tua access
            $this->blockOrangTuaAccess();
            
            // Check basic permission
            if (!$this->hasPermission('index')) {
                return response()->json(['error' => 'Unauthorized access denied'], 403);
            }

            return response()->json($this->getStatisticsData());
        } catch (\Exception $e) {
            Log::error('Error saat mengambil statistik pemeriksaan fisik: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil statistik'], 500);
        }
    }

    /**
     * Private method untuk validasi pemeriksaan fisik
     */
    private function validatePemeriksaanFisik(array $data, string $excludeId = null)
    {
        $uniqueRule = 'unique:pemeriksaan_fisiks,id_detprx';
        if ($excludeId) {
            $uniqueRule .= ',' . $excludeId . ',id_prefisik';
        }

        return Validator::make($data, [
            'id_detprx' => 'required|string|exists:detail_pemeriksaans,id_detprx|' . $uniqueRule,
            'tinggi_badan' => 'nullable|numeric|between:50,250',
            'berat_badan' => 'nullable|numeric|between:10,200',
            'lingkar_kepala' => 'nullable|numeric|between:30,70',
            'lingkar_lengan_atas' => 'nullable|numeric|between:10,50',
            'dada' => 'nullable|string|max:50',
            'jantung' => 'nullable|string|max:50',
            'paru' => 'nullable|string|max:50',
            'perut' => 'nullable|string|max:50',
            'hepar' => 'nullable|string|max:50',
            'anogenital' => 'nullable|string|max:50',
            'ekstremitas' => 'nullable|string|max:50',
            'kepala' => 'nullable|string|max:50',
            'pemeriksaan_penunjang' => 'nullable|string',
            'masalah_aktif' => 'nullable|string|max:50',
            'rencana_medis_dan_terapi' => 'nullable|string|max:50',
        ], [
            'id_detprx.required' => 'Detail Pemeriksaan wajib dipilih',
            'id_detprx.exists' => 'Detail Pemeriksaan tidak valid',
            'id_detprx.unique' => 'Detail Pemeriksaan sudah memiliki pemeriksaan fisik',
            'tinggi_badan.between' => 'Tinggi badan harus antara 50-250 cm',
            'berat_badan.between' => 'Berat badan harus antara 10-200 kg',
            'lingkar_kepala.between' => 'Lingkar kepala harus antara 30-70 cm',
            'lingkar_lengan_atas.between' => 'Lingkar lengan atas harus antara 10-50 cm',
        ]);
    }

    /**
     * Private method untuk update status detail pemeriksaan
     */
    private function updateDetailPemeriksaanStatus(string $idDetprx)
    {
        $detailPemeriksaan = DetailPemeriksaan::find($idDetprx);
        if ($detailPemeriksaan && $detailPemeriksaan->status_pemeriksaan == 'belum lengkap') {
            // Check jika sudah ada pemeriksaan awal dan fisik, ubah status menjadi lengkap
            if ($detailPemeriksaan->pemeriksaanAwal) {
                $detailPemeriksaan->update(['status_pemeriksaan' => 'lengkap']);
            }
        }
    }

    /**
     * Private method untuk mendapatkan data statistik
     */
    private function getStatisticsData()
    {
        $distribusiBmi = [
            'underweight' => 0,
            'normal' => 0,
            'overweight' => 0,
            'obese' => 0,
        ];

        // Jika ada method withBmiCategory di model, gunakan itu
        if (method_exists(PemeriksaanFisik::class, 'withBmiCategory')) {
            $distribusiBmi = [
                'underweight' => PemeriksaanFisik::withBmiCategory('underweight')->count(),
                'normal' => PemeriksaanFisik::withBmiCategory('normal')->count(),
                'overweight' => PemeriksaanFisik::withBmiCategory('overweight')->count(),
                'obese' => PemeriksaanFisik::withBmiCategory('obese')->count(),
            ];
        } else {
            // Hitung manual jika tidak ada method di model
            $pemeriksaanFisiks = PemeriksaanFisik::whereNotNull('tinggi_badan')
                ->whereNotNull('berat_badan')
                ->get();
            
            foreach ($pemeriksaanFisiks as $pf) {
                $tinggiM = $pf->tinggi_badan / 100;
                $bmi = $pf->berat_badan / ($tinggiM * $tinggiM);
                $kategori = strtolower($this->getBMICategory($bmi));
                
                if (isset($distribusiBmi[$kategori])) {
                    $distribusiBmi[$kategori]++;
                }
            }
        }

        return [
            'total_pemeriksaan' => PemeriksaanFisik::count(),
            'pemeriksaan_hari_ini' => PemeriksaanFisik::whereDate('created_at', today())->count(),
            'rata_rata_tinggi' => round(PemeriksaanFisik::whereNotNull('tinggi_badan')->avg('tinggi_badan'), 1),
            'rata_rata_berat' => round(PemeriksaanFisik::whereNotNull('berat_badan')->avg('berat_badan'), 1),
            'distribusi_bmi' => $distribusiBmi
        ];
    }

    /**
     * Helper method to get BMI category
     */
    private function getBMICategory($bmi)
    {
        if ($bmi < 18.5) {
            return 'Underweight';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return 'Normal';
        } elseif ($bmi >= 25 && $bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    /**
     * Helper method to get BMI status color
     */
    private function getBMIStatus($bmi)
    {
        if ($bmi < 18.5) {
            return 'warning';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            return 'success';
        } elseif ($bmi >= 25 && $bmi < 30) {
            return 'warning';
        } else {
            return 'danger';
        }
    }
}