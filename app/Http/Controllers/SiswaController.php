<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SiswaController extends Controller
{
    /**
     * Get route names based on user role
     */
    private function getRouteNames()
    {
        $userLevel = session('user_level');
        
        $routes = [
            'admin' => [
                'index' => 'siswa.index',
                'create' => 'siswa.create',
                'show' => 'siswa.show',
                'edit' => 'siswa.edit',
                'destroy' => 'siswa.destroy',
                'template' => 'siswa.template'
            ],
            'petugas' => [
                'index' => 'petugas.siswa.index',
                'create' => null,
                'show' => 'petugas.siswa.show',
                'edit' => 'petugas.siswa.edit',
                'destroy' => null,
                'template' => null
            ],
            'dokter' => [
                'index' => 'dokter.siswa.index',
                'create' => null,
                'show' => 'dokter.siswa.show',
                'edit' => null,
                'destroy' => null,
                'template' => null
            ],
            'orang_tua' => [
                'index' => 'orangtua.siswa.show',
                'create' => null,
                'show' => 'orangtua.siswa.show',
                'edit' => 'orangtua.siswa.edit',
                'destroy' => null,
                'template' => null
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
            'admin' => ['index', 'create', 'show', 'edit', 'delete'],
            'petugas' => ['index', 'show', 'edit'],
            'dokter' => ['index', 'show'],
            'orang_tua' => ['show', 'edit']
        ];
        
        $userPermissions = $permissions[$userLevel] ?? [];
        return in_array($action, $userPermissions);
    }

    /**
     * Check if orang tua is accessing their own child's data
     */
    private function checkOrangTuaAccess($siswaId)
    {
        if (session('user_level') === 'orang_tua') {
            $allowedSiswaId = session('siswa_id');
            if ($allowedSiswaId !== $siswaId) {
                abort(403, 'Anda hanya dapat mengakses data anak Anda sendiri.');
            }
        }
    }

    /**
     * Get validation rules based on user level
     */
    private function getValidationRules($isUpdate = false)
    {
        $userLevel = session('user_level');
        
        $baseRules = [
            'nama_siswa' => 'required|string|max:50',
            'tempat_lahir' => 'nullable|string|max:30',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
        ];

        if ($userLevel === 'orang_tua') {
            // Orang tua: hanya field terbatas
            return $baseRules;
        }

        if ($userLevel === 'petugas') {
            // Petugas: field dasar tanpa status dan tanggal lulus
            return array_merge($baseRules, [
                'jenis_kelamin' => 'nullable|in:L,P',
                'tanggal_masuk' => 'nullable|date|before_or_equal:today',
            ]);
        }

        // Admin: semua field
        $adminRules = array_merge($baseRules, [
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_masuk' => 'nullable|date|before_or_equal:today',
            'status_aktif' => 'required|boolean',
            'tanggal_lulus' => 'nullable|date',
        ]);

        if (!$isUpdate) {
            $adminRules['id_siswa'] = 'required|string|max:10|unique:siswas,id_siswa';
        }

        return $adminRules;
    }

    /**
     * Get validation messages
     */
    private function getValidationMessages()
    {
        return [
            'id_siswa.unique' => 'ID Siswa sudah digunakan',
            'nama_siswa.required' => 'Nama siswa wajib diisi',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh lebih dari hari ini',
            'status_aktif.required' => 'Status siswa wajib dipilih',
        ];
    }

    /**
     * Handle successful operation redirect
     */
    private function handleSuccessRedirect($action, $siswaId = null, $message = null)
    {
        $routes = $this->getRouteNames();
        $userLevel = session('user_level');

        if ($userLevel === 'orang_tua') {
            return redirect()->route('orangtua.siswa.show')->with('success', $message);
        }

        switch ($action) {
            case 'create':
            case 'update':
            case 'delete':
                return redirect()->route($routes['index'])->with('success', $message);
            default:
                return redirect()->route($routes['index'])->with('success', $message);
        }
    }

    /**
     * Handle error redirect
     */
    private function handleErrorRedirect($action, $siswaId = null, $message = null, $withInput = false)
    {
        $routes = $this->getRouteNames();

        $redirect = match($action) {
            'create' => redirect()->route($routes['create']),
            'edit' => redirect()->route($routes['edit'], $siswaId),
            default => redirect()->route($routes['index'])
        };

        $redirect = $redirect->with('error', $message);
        
        if ($withInput) {
            $redirect = $redirect->withInput();
        }

        return $redirect;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$this->hasPermission('index')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar siswa.');
        }

        // Query dasar dengan eager loading relasi yang lebih spesifik
        $query = Siswa::with(['detailSiswa', 'detailSiswa.kelas', 'detailSiswa.kelas.jurusan']);
        
        // Apply filters only if not showing all or resetting
        if (!($request->has('show_all') || $request->has('reset'))) {
            $this->applyFilters($query, $request);
        }
        
        // Pengurutan
        $sortBy = $request->input('sort_by', 'id_siswa');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $siswas = $query->paginate($perPage)->appends($request->except(['page', 'reset']));
        
        // Hitung usia untuk setiap siswa
        $this->enrichSiswaData($siswas);
        
        // Menyiapkan data untuk dropdown filter
        $tahunMasuk = $this->getTahunMasukOptions();
        $jurusans = Jurusan::all();
        $kelas = Kelas::with('jurusan')->get();
            
        return view('siswa.index', compact('siswas', 'tahunMasuk', 'jurusans', 'kelas'));
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }
        
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        
        if ($request->filled('tahun_masuk')) {
            $query->whereYear('tanggal_masuk', $request->tahun_masuk);
        }
        
        if ($request->filled('keyword')) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function($q) use ($keyword) {
                $q->where('id_siswa', 'like', $keyword)
                  ->orWhere('nama_siswa', 'like', $keyword)
                  ->orWhere('tempat_lahir', 'like', $keyword);
            });
        }
    }

    /**
     * Enrich siswa data with calculated fields
     */
    private function enrichSiswaData($siswas)
    {
        foreach ($siswas as $siswa) {
            // Hitung usia
            if ($siswa->tanggal_lahir) {
                $tanggalLahir = new \DateTime($siswa->tanggal_lahir);
                $today = new \DateTime();
                $siswa->usia = $tanggalLahir->diff($today)->y;
            } else {
                $siswa->usia = null;
            }
        }
    }

    /**
     * Get tahun masuk options for filter
     */
    private function getTahunMasukOptions()
    {
        return Siswa::selectRaw('YEAR(tanggal_masuk) as tahun')
            ->distinct()
            ->whereNotNull('tanggal_masuk')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->hasPermission('create')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah siswa baru.');
        }
        
        // Generate ID berikutnya TANPA jurusan (format: 625001)
        $nextId = $this->getNextSequenceId(false);
        
        return view('siswa.create', compact('nextId'));
    }

private function getNextSequenceId($includeJurusan = false)
{
    $lastId = \DB::table('siswas')->max('id_siswa');

    // Jika null atau bukan angka, mulai dari 625001
    if (!is_numeric($lastId)) {
        return 625003;
    }

    return (int)$lastId + 1;
}


    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->hasPermission('create')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah siswa baru.');
        }
        
        // Validasi request
        $validator = Validator::make(
            $request->all(), 
            $this->getValidationRules(false), 
            $this->getValidationMessages()
        );

        if ($validator->fails()) {
            return $this->handleErrorRedirect('create', null, null, true)
                ->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            
            // Persiapkan data siswa
            $siswaData = $request->all();
            
            // Pastikan nilai default untuk status_aktif
            if (!isset($siswaData['status_aktif'])) {
                $siswaData['status_aktif'] = 1;
            }
            
            // Buat siswa baru
            $siswa = Siswa::create($siswaData);
            
            DB::commit();
            
            return $this->handleSuccessRedirect('create', $siswa->id_siswa, 'Siswa berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menambahkan siswa: ' . $e->getMessage());
            
            return $this->handleErrorRedirect('create', null, 'Terjadi kesalahan. Siswa gagal ditambahkan: ' . $e->getMessage(), true);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (!$this->hasPermission('show')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail siswa.');
        }

        $this->checkOrangTuaAccess($id);
        
        // Dapatkan data siswa dengan relasi yang dibutuhkan
        $siswa = Siswa::with([
                'detailSiswa', 
                'detailSiswa.kelas', 
                'detailSiswa.kelas.jurusan',
                'orangTua'
            ])
            ->findOrFail($id);
        
        // Hitung umur siswa
        $umur = null;
        if ($siswa->tanggal_lahir) {
            $tanggalLahir = new \DateTime($siswa->tanggal_lahir);
            $today = new \DateTime();
            $umur = $tanggalLahir->diff($today)->y;
        }
        
        // Hitung lama sekolah
        $lamaSekolah = null;
        if ($siswa->tanggal_masuk) {
            $tanggalMasuk = new \DateTime($siswa->tanggal_masuk);
            $today = new \DateTime();
            $lamaSekolah = $tanggalMasuk->diff($today);
        }
        
        return view('siswa.show', compact('siswa', 'umur', 'lamaSekolah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!$this->hasPermission('edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data siswa.');
        }

        $this->checkOrangTuaAccess($id);
        
        $siswa = Siswa::findOrFail($id);
        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (!$this->hasPermission('edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data siswa.');
        }

        $this->checkOrangTuaAccess($id);
        
        // Validasi berdasarkan user level
        $validator = Validator::make(
            $request->all(), 
            $this->getValidationRules(true), 
            $this->getValidationMessages()
        );

        if ($validator->fails()) {
            return $this->handleErrorRedirect('edit', $id, null, true)
                ->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            
            $siswa = Siswa::findOrFail($id);
            
            // Get allowed fields based on user level
            $updateData = $this->getAllowedUpdateFields($request);
            
            $siswa->update($updateData);
            
            DB::commit();
            
            return $this->handleSuccessRedirect('update', $id, 'Data siswa berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui siswa: ' . $e->getMessage());
            
            return $this->handleErrorRedirect('edit', $id, 'Terjadi kesalahan. Data siswa gagal diperbarui: ' . $e->getMessage(), true);
        }
    }

    /**
     * Get allowed update fields based on user level
     */
    private function getAllowedUpdateFields(Request $request)
    {
        $userLevel = session('user_level');
        
        if ($userLevel === 'orang_tua') {
            return $request->only(['nama_siswa', 'tempat_lahir', 'tanggal_lahir']);
        }
        
        if ($userLevel === 'petugas') {
            return $request->only([
                'nama_siswa', 'tempat_lahir', 'tanggal_lahir', 
                'jenis_kelamin', 'tanggal_masuk'
            ]);
        }
        
        // Admin: semua field
        return $request->all();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!$this->hasPermission('delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data siswa.');
        }
        
        try {
            DB::beginTransaction();
            
            $siswa = Siswa::findOrFail($id);
            $nama = $siswa->nama_siswa;
            $siswa->delete();
            
            DB::commit();
            
            Log::info('Siswa berhasil dihapus', [
                'id_siswa' => $id,
                'nama_siswa' => $nama,
            ]);
            
            return $this->handleSuccessRedirect('delete', null, "Siswa '$nama' berhasil dihapus.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus siswa: ' . $e->getMessage());
            
            return $this->handleErrorRedirect('delete', null, 'Terjadi kesalahan. Siswa gagal dihapus: ' . $e->getMessage());
        }
    }
}
