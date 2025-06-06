<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Siswa;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResepController extends Controller
{
    /**
     * Get route names based on user role
     */
    private function getRouteNames()
    {
        $userLevel = session('user_level');
        
        $routes = [
            'admin' => [
                'index' => 'resep.index',
                'create' => 'resep.create',
                'show' => 'resep.show',
                'edit' => 'resep.edit',
                'destroy' => 'resep.destroy',
                'export' => 'resep.export'
            ],
            'petugas' => [
                'index' => 'petugas.resep.index',
                'create' => 'petugas.resep.create',
                'show' => 'petugas.resep.show',
                'edit' => 'petugas.resep.edit',
                'destroy' => null, // Petugas tidak bisa delete
                'export' => null // Petugas tidak bisa export
            ],
            'dokter' => [
                'index' => 'dokter.resep.index',
                'create' => null,
                'show' => 'dokter.resep.show',
                'edit' => null,
                'destroy' => null,
                'export' => null
            ],
            'orang_tua' => [
                'index' => 'orangtua.riwayat.resep',
                'create' => null,
                'show' => null,
                'edit' => null,
                'destroy' => null,
                'export' => null
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
            'admin' => ['index', 'create', 'store', 'show', 'edit', 'update', 'delete', 'export'],
            'petugas' => ['index', 'create', 'store', 'show', 'edit', 'update'], // Tidak ada delete dan export
            'dokter' => ['index', 'show'], // Read only
            'orang_tua' => ['show'] // Hanya show data anak sendiri via redirect
        ];
        
        $userPermissions = $permissions[$userLevel] ?? [];
        return in_array($action, $userPermissions);
    }

    /**
     * Check if user should be redirected (for orang_tua)
     */
    private function checkRedirectForOrangTua()
    {
        if (session('user_level') === 'orang_tua') {
            // Redirect orang tua ke halaman riwayat resep anak mereka
            return redirect()->route('orangtua.riwayat.resep')
                ->with('info', 'Anda telah diarahkan ke halaman riwayat resep anak Anda.');
        }
        return null;
    }

    /**
     * Menampilkan daftar resep
     */
    public function index(Request $request)
    {
        // Check permission
        if (!$this->hasPermission('index')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar resep.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;

        // Debugging: Log parameter yang masuk
        Log::info("Resep Index dipanggil dengan parameter: ", $request->all());
        
        // Debug session
        Log::info('Session Debug di Resep Index', [
            'user_id' => session('user_id'),
            'user_level' => session('user_level'),
            'user_name' => session('user_name'),
            'has_session' => session()->has('user_id')
        ]);

        // Query dasar dengan eager loading relasi
        $query = Resep::with(['siswa', 'dokter']);
        
        // Jika parameter show_all atau reset ada, tampilkan semua data tanpa filter
        if ($request->has('show_all') || $request->has('reset')) {
            Log::info("Menampilkan semua data resep tanpa filter karena parameter show_all/reset ada");
        } else {
            // Filter berdasarkan parameter pencarian
            if ($request->filled('siswa')) {
                $query->whereHas('siswa', function($q) use ($request) {
                    $q->where('nama_siswa', 'like', '%' . $request->siswa . '%');
                });
            }
            
            if ($request->filled('dokter')) {
                $query->whereHas('dokter', function($q) use ($request) {
                    $q->where('Nama_Dokter', 'like', '%' . $request->dokter . '%');
                });
            }
            
            if ($request->filled('tanggal')) {
                $query->whereDate('Tanggal_Resep', $request->tanggal);
            }
            
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('Tanggal_Resep', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('Tanggal_Resep', '<=', $request->tanggal_sampai);
            }
            
            if ($request->filled('obat')) {
                $query->where('Nama_Obat', 'like', '%' . $request->obat . '%');
            }

            // Pencarian berdasarkan keyword
            if ($request->filled('keyword')) {
                $keyword = '%' . $request->keyword . '%';
                $query->where(function($q) use ($keyword) {
                    $q->where('Id_Resep', 'like', $keyword)
                      ->orWhere('Nama_Obat', 'like', $keyword)
                      ->orWhere('Dosis', 'like', $keyword)
                      ->orWhere('Durasi', 'like', $keyword)
                      ->orWhereHas('siswa', function($q) use ($keyword) {
                          $q->where('nama_siswa', 'like', $keyword);
                      })
                      ->orWhereHas('dokter', function($q) use ($keyword) {
                          $q->where('Nama_Dokter', 'like', $keyword);
                      });
                });
            }
        }
        
        // Log jumlah data sebelum pagination
        $totalCount = $query->count();
        Log::info("Total data resep sebelum pagination: {$totalCount}");
        
        // Pengurutan
        $sortBy = $request->input('sort_by', 'Tanggal_Resep');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination dengan opsi untuk mengubah jumlah item per halaman
        $perPage = $request->input('per_page', 15);
        $reseps = $query->paginate($perPage)->appends($request->except(['reset', 'show_all']));
        
        // Log jumlah data setelah pagination
        Log::info("Pagination Resep: {$reseps->count()} ditampilkan dari {$reseps->total()}");
        
        // Data untuk filter dropdown
        $dokterList = Dokter::where('status_aktif', 1)
            ->orderBy('Nama_Dokter')
            ->get();
        $siswaList = Siswa::where('status_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();
            
        return view('resep.index', compact('reseps', 'dokterList', 'siswaList'));
    }

    /**
     * Menampilkan form untuk membuat data baru
     */
    public function create()
    {
        // Check permission
        if (!$this->hasPermission('create')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah resep baru.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;
        
        $siswaList = Siswa::where('status_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();
            
        $dokterList = Dokter::where('status_aktif', 1)
            ->orderBy('Nama_Dokter')
            ->get();
        
        // Generate ID berikutnya
        $id = $this->getNextSequenceId();
        
        return view('resep.create', compact('siswaList', 'dokterList', 'id'));
    }

    /**
     * Menyimpan data baru ke database
     */
    public function store(Request $request)
    {
        // Check permission
        if (!$this->hasPermission('store')) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan resep.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;
        
        // Validasi request
        $validator = Validator::make($request->all(), [
            'Id_Resep' => 'required|string|max:10|unique:resep,Id_Resep',
            'Id_Siswa' => 'required|string|exists:siswas,id_siswa',
            'Id_Dokter' => 'required|string|exists:dokters,Id_Dokter',
            'Tanggal_Resep' => 'required|date',
            'Nama_Obat' => 'required|string|max:30',
            'Dosis' => 'required|string|max:30',
            'Durasi' => 'required|string|max:30',
            'Dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ], [
            'Id_Resep.unique' => 'ID Resep sudah digunakan',
            'Id_Resep.required' => 'ID Resep wajib diisi',
            'Id_Siswa.required' => 'Siswa wajib dipilih',
            'Id_Siswa.exists' => 'Siswa tidak valid',
            'Id_Dokter.required' => 'Dokter wajib dipilih',
            'Id_Dokter.exists' => 'Dokter tidak valid',
            'Tanggal_Resep.required' => 'Tanggal resep wajib diisi',
            'Tanggal_Resep.date' => 'Format tanggal tidak valid',
            'Nama_Obat.required' => 'Nama obat wajib diisi',
            'Nama_Obat.max' => 'Nama obat maksimal 30 karakter',
            'Dosis.required' => 'Dosis wajib diisi',
            'Dosis.max' => 'Dosis maksimal 30 karakter',
            'Durasi.required' => 'Durasi wajib diisi',
            'Durasi.max' => 'Durasi maksimal 30 karakter',
            'Dokumen.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG',
            'Dokumen.max' => 'Ukuran dokumen maksimal 10MB',
        ]);

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['create'])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Persiapkan data resep
            $resepData = $request->except(['Dokumen']);
            
            // Proses upload dokumen jika ada
            if ($request->hasFile('Dokumen')) {
                $file = $request->file('Dokumen');
                
                // Validasi ukuran file
                if ($file->getSize() > 10485760) { // 10MB
                    $routes = $this->getRouteNames();
                    return redirect()->route($routes['create'])
                        ->withErrors(['Dokumen' => 'Ukuran file terlalu besar. Maksimal 10MB.'])
                        ->withInput();
                }
                
                $dokumen = file_get_contents($file->getRealPath());
                $mimeType = $file->getMimeType();
                
                $resepData['Dokumen'] = $dokumen;
                
                // Log info untuk debugging
                Log::info('File uploaded untuk resep', [
                    'Id_Resep' => $resepData['Id_Resep'],
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $mimeType,
                    'size' => $file->getSize(),
                    'user_level' => session('user_level')
                ]);
            }
            
            // Log data sebelum simpan
            Log::info('Menyimpan resep baru', [
                'Id_Resep' => $resepData['Id_Resep'],
                'Id_Siswa' => $resepData['Id_Siswa'],
                'Id_Dokter' => $resepData['Id_Dokter'],
                'user_level' => session('user_level')
            ]);

            // Buat resep baru
            $resep = Resep::create($resepData);
            
            // Commit transaction
            DB::commit();
            
            // Redirect ke index berdasarkan role
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['index'])
                ->with('success', 'Resep berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error saat menambahkan resep: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['create'])
                ->with('error', 'Terjadi kesalahan. Resep gagal ditambahkan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail resep
     */
    public function show($id)
    {
        // Check permission
        if (!$this->hasPermission('show')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail resep.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;
        
        $resep = Resep::with(['siswa', 'dokter'])
            ->where('Id_Resep', $id)
            ->firstOrFail();
            
        return view('resep.show', compact('resep'));
    }

    /**
     * Menampilkan form untuk mengedit data
     */
    public function edit($id)
    {
        // Check permission
        if (!$this->hasPermission('edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit resep.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;
        
        $resep = Resep::where('Id_Resep', $id)->firstOrFail();
        
        $siswaList = Siswa::where('status_aktif', 1)
            ->orderBy('nama_siswa')
            ->get();
            
        $dokterList = Dokter::where('status_aktif', 1)
            ->orderBy('Nama_Dokter')
            ->get();
        
        return view('resep.edit', compact('resep', 'siswaList', 'dokterList'));
    }

    /**
     * Mengupdate data di database
     */
    public function update(Request $request, $id)
    {
        // Check permission
        if (!$this->hasPermission('update')) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui resep.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;
        
        // Validasi request
        $validator = Validator::make($request->all(), [
            'Id_Siswa' => 'required|string|exists:siswas,id_siswa',
            'Id_Dokter' => 'required|string|exists:dokters,Id_Dokter',
            'Tanggal_Resep' => 'required|date',
            'Nama_Obat' => 'required|string|max:30',
            'Dosis' => 'required|string|max:30',
            'Durasi' => 'required|string|max:30',
            'Dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ], [
            'Id_Siswa.required' => 'Siswa wajib dipilih',
            'Id_Siswa.exists' => 'Siswa tidak valid',
            'Id_Dokter.required' => 'Dokter wajib dipilih',
            'Id_Dokter.exists' => 'Dokter tidak valid',
            'Tanggal_Resep.required' => 'Tanggal resep wajib diisi',
            'Tanggal_Resep.date' => 'Format tanggal tidak valid',
            'Nama_Obat.required' => 'Nama obat wajib diisi',
            'Nama_Obat.max' => 'Nama obat maksimal 30 karakter',
            'Dosis.required' => 'Dosis wajib diisi',
            'Dosis.max' => 'Dosis maksimal 30 karakter',
            'Durasi.required' => 'Durasi wajib diisi',
            'Durasi.max' => 'Durasi maksimal 30 karakter',
            'Dokumen.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG',
            'Dokumen.max' => 'Ukuran dokumen maksimal 10MB',
        ]);

        if ($validator->fails()) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['edit'], $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Cari resep
            $resep = Resep::where('Id_Resep', $id)->firstOrFail();
            
            // Persiapkan data untuk update
            $updateData = $request->except(['Dokumen']);
            
            // Proses upload dokumen jika ada
            if ($request->hasFile('Dokumen')) {
                $file = $request->file('Dokumen');
                
                // Validasi ukuran file
                if ($file->getSize() > 10485760) { // 10MB
                    $routes = $this->getRouteNames();
                    return redirect()->route($routes['edit'], $id)
                        ->withErrors(['Dokumen' => 'Ukuran file terlalu besar. Maksimal 10MB.'])
                        ->withInput();
                }
                
                $dokumen = file_get_contents($file->getRealPath());
                $updateData['Dokumen'] = $dokumen;
                
                Log::info('File updated untuk resep', [
                    'Id_Resep' => $id,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'user_level' => session('user_level')
                ]);
            }
            
            // Log data sebelum update
            Log::info('Memperbarui resep', [
                'Id_Resep' => $id,
                'Id_Siswa' => $updateData['Id_Siswa'],
                'Id_Dokter' => $updateData['Id_Dokter'],
                'user_level' => session('user_level')
            ]);
            
            // Update resep dengan data yang sudah disiapkan
            $resep->update($updateData);
            
            // Commit transaction
            DB::commit();
            
            // Redirect ke index berdasarkan role
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['index'])
                ->with('success', 'Data resep berhasil diperbarui.');
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error saat memperbarui resep: ' . $e->getMessage());
            
            $routes = $this->getRouteNames();
            
            return redirect()->route($routes['edit'], $id)
                ->with('error', 'Terjadi kesalahan. Data resep gagal diperbarui: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghapus data dari database
     * Hanya admin yang bisa hapus
     */
    public function destroy($id)
    {
        // Check permission
        if (!$this->hasPermission('delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus resep.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;
        
        try {
            // Mulai transaction database
            DB::beginTransaction();
            
            // Cari dan hapus resep
            $resep = Resep::where('Id_Resep', $id)->firstOrFail();
            $idResep = $resep->Id_Resep;
            $resep->delete();
            
            // Commit transaction
            DB::commit();

            Log::info('Resep berhasil dihapus', [
                'Id_Resep' => $idResep,
                'user_level' => session('user_level')
            ]);
            
            return redirect()->route('resep.index')
                ->with('success', "Resep '{$idResep}' berhasil dihapus.");
                
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error saat menghapus resep: ' . $e->getMessage());
            
            return redirect()->route('resep.index')
                ->with('error', 'Terjadi kesalahan. Resep gagal dihapus: ' . $e->getMessage());
        }
    }
    
    /**
     * Export data resep ke format Excel - Admin only
     */
    public function export(Request $request)
    {
        // Check permission
        if (!$this->hasPermission('export')) {
            abort(403, 'Anda tidak memiliki akses untuk export data resep.');
        }

        // Check redirect for orang_tua
        $redirect = $this->checkRedirectForOrangTua();
        if ($redirect) return $redirect;
        
        try {
            // Buat query dasar
            $query = Resep::with(['siswa', 'dokter']);
            
            // Terapkan filter jika ada
            if ($request->filled('siswa')) {
                $query->whereHas('siswa', function($q) use ($request) {
                    $q->where('nama_siswa', 'like', '%' . $request->siswa . '%');
                });
            }
            
            if ($request->filled('dokter')) {
                $query->whereHas('dokter', function($q) use ($request) {
                    $q->where('Nama_Dokter', 'like', '%' . $request->dokter . '%');
                });
            }
            
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('Tanggal_Resep', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('Tanggal_Resep', '<=', $request->tanggal_sampai);
            }
            
            if ($request->filled('obat')) {
                $query->where('Nama_Obat', 'like', '%' . $request->obat . '%');
            }
            
            if ($request->filled('keyword')) {
                $keyword = '%' . $request->keyword . '%';
                $query->where(function($q) use ($keyword) {
                    $q->where('Id_Resep', 'like', $keyword)
                      ->orWhere('Nama_Obat', 'like', $keyword)
                      ->orWhere('Dosis', 'like', $keyword);
                });
            }
            
            // Pengurutan
            $sortBy = $request->input('sort_by', 'Tanggal_Resep');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Ambil data sesuai filter
            $reseps = $query->get();
            
            // Jika tidak ada data yang akan diekspor
            if ($reseps->isEmpty()) {
                return redirect()->route('resep.index')
                    ->with('warning', 'Tidak ada data resep yang ditemukan untuk diekspor.');
            }
            
            // Ekspor data dengan nama file yang dinamis
            $filename = 'Data_Resep_' . date('d-m-Y_H-i-s') . '.xlsx';
            
            // Log informasi ekspor
            Log::info('Mulai ekspor data resep', [
                'count' => $reseps->count(),
                'filename' => $filename,
                'filters' => $request->all(),
                'user_level' => session('user_level')
            ]);
            
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\ResepExport($reseps),
                $filename
            );
            
        } catch (\Exception $e) {
            // Log error dengan detail
            Log::error('Error saat export data resep: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('resep.index')
                ->with('error', 'Terjadi kesalahan saat export data: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan dokumen resep
     */
    public function viewDokumen($id)
    {
        try {
            // Check basic permission
            if (!$this->hasPermission('show')) {
                abort(403, 'Anda tidak memiliki akses untuk melihat dokumen resep.');
            }

            $resep = Resep::where('Id_Resep', $id)->firstOrFail();
            
            if (!$resep->Dokumen) {
                return abort(404, 'Dokumen tidak ditemukan');
            }
            
            // Deteksi tipe file berdasarkan magic bytes
            $mimeType = $this->detectMimeType($resep->Dokumen);
            
            $filename = "dokumen_resep_{$resep->Id_Resep}";
            
            return response($resep->Dokumen, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error viewing document: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan saat menampilkan dokumen');
        }
    }
    
    /**
     * Download dokumen resep
     */
    public function downloadDokumen($id)
    {
        try {
            // Check basic permission
            if (!$this->hasPermission('show')) {
                abort(403, 'Anda tidak memiliki akses untuk mendownload dokumen resep.');
            }

            $resep = Resep::where('Id_Resep', $id)->firstOrFail();
            
            if (!$resep->Dokumen) {
                return abort(404, 'Dokumen tidak ditemukan');
            }
            
            // Deteksi tipe file dan extension
            $mimeType = $this->detectMimeType($resep->Dokumen);
            $extension = $this->getExtensionFromMimeType($mimeType);
            
            $filename = "Resep_{$resep->Id_Resep}_{$resep->Tanggal_Resep}.{$extension}";
            
            return response($resep->Dokumen, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($resep->Dokumen)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan saat mendownload dokumen');
        }
    }
    
    /**
     * Cetak resep
     */
    public function cetak($id)
    {
        // Check basic permission
        if (!$this->hasPermission('show')) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak resep.');
        }

        $resep = Resep::with(['siswa', 'dokter'])
            ->where('Id_Resep', $id)
            ->firstOrFail();
            
        return view('resep.cetak', compact('resep'));
    }

    /**
     * Generate next sequence ID untuk resep
     */
    private function getNextSequenceId()
    {
        try {
            // Mulai transaction
            DB::beginTransaction();
            
            // Ambil nilai sequence saat ini dalam lock
            $sequence = DB::table('sequence_ids')
                ->where('sequence_name', 'resep_id')
                ->lockForUpdate()
                ->first();
            
            // Jika sequence belum ada, buat baru
            if (!$sequence) {
                DB::table('sequence_ids')->insert([
                    'sequence_name' => 'resep_id',
                    'current_value' => 0
                ]);
                $currentValue = 0;
            } else {
                $currentValue = $sequence->current_value;
            }
            
            // Increment nilai sequence
            $nextValue = $currentValue + 1;
            
            // Update nilai sequence
            DB::table('sequence_ids')
                ->where('sequence_name', 'resep_id')
                ->update(['current_value' => $nextValue]);
            
            // Format ID: R + nomor urut (4 digit)
            $formattedId = "R" . str_pad($nextValue, 4, '0', STR_PAD_LEFT);
            
            // Commit transaction
            DB::commit();
            
            Log::info('Generated next sequence ID untuk resep', [
                'id' => $formattedId, 
                'sequence_value' => $nextValue
            ]);
            
            return $formattedId;
            
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error generating sequence ID untuk resep: ' . $e->getMessage());
            
            // Fallback ke metode lama jika terjadi error
            $lastId = Resep::orderBy('Id_Resep', 'desc')->first();
            
            if ($lastId) {
                $lastNumber = intval(substr($lastId->Id_Resep, 1));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            return "R" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Get recent resep for dashboard (API)
     */
    public function getRecent()
    {
        try {
            // Check basic permission
            if (!$this->hasPermission('index')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $recentResep = Resep::with(['siswa', 'dokter'])
                ->orderBy('Tanggal_Resep', 'desc')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'Id_Resep' => $item->Id_Resep,
                        'nama_siswa' => $item->siswa->nama_siswa ?? 'N/A',
                        'nama_dokter' => $item->dokter->Nama_Dokter ?? 'N/A',
                        'Tanggal_Resep' => $item->Tanggal_Resep->format('d/m/Y'),
                        'Nama_Obat' => $item->Nama_Obat,
                        'Dosis' => $item->Dosis,
                        'Durasi' => $item->Durasi,
                    ];
                });
                
            return response()->json($recentResep);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data resep terbaru: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data'], 500);
        }
    }
    
    /**
     * Get statistics untuk dashboard (API)
     */
    public function getStatistics()
    {
        try {
            // Check basic permission
            if (!$this->hasPermission('index')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $statistics = [
                'total_resep' => Resep::count(),
                'resep_hari_ini' => Resep::whereDate('Tanggal_Resep', today())->count(),
                'resep_minggu_ini' => Resep::whereBetween('Tanggal_Resep', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'resep_bulan_ini' => Resep::whereMonth('Tanggal_Resep', now()->month)
                    ->whereYear('Tanggal_Resep', now()->year)
                    ->count(),
                'obat_terpopuler' => Resep::select('Nama_Obat', DB::raw('count(*) as total'))
                    ->groupBy('Nama_Obat')
                    ->orderBy('total', 'desc')
                    ->take(5)
                    ->get(),
            ];
                
            return response()->json($statistics);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil statistik resep: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil statistik'], 500);
        }
    }
    
    /**
     * Deteksi MIME type dari binary data
     */
    private function detectMimeType($binaryData)
    {
        // Coba gunakan finfo terlebih dahulu
        if (function_exists('finfo_buffer')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detected = $finfo->buffer($binaryData);
            if ($detected && $detected !== 'application/octet-stream') {
                return $detected;
            }
        }
        
        // Fallback: deteksi berdasarkan signature bytes
        $signature = substr($binaryData, 0, 10);
        
        // JPEG
        if (substr($signature, 0, 3) === "\xFF\xD8\xFF") {
            return 'image/jpeg';
        }
        
        // PNG
        if (substr($signature, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") {
            return 'image/png';
        }
        
        // PDF
        if (substr($signature, 0, 4) === "%PDF") {
            return 'application/pdf';
        }
        
        // GIF
        if (substr($signature, 0, 6) === "GIF87a" || substr($signature, 0, 6) === "GIF89a") {
            return 'image/gif';
        }
        
        // WebP
        if (substr($signature, 0, 4) === "RIFF" && substr($signature, 8, 4) === "WEBP") {
            return 'image/webp';
        }
        
        // Default fallback
        return 'application/octet-stream';
    }
    
    /**
     * Mendapatkan extension dari MIME type
     */
    private function getExtensionFromMimeType($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'text/plain' => 'txt',
        ];
        
        return $extensions[$mimeType] ?? 'bin';
    }
    
    /**
     * Cek apakah file adalah gambar
     */
    public function isImage($mimeType)
    {
        return strpos($mimeType, 'image/') === 0;
    }
    
    /**
     * Cek apakah file adalah PDF
     */
    public function isPdf($mimeType)
    {
        return $mimeType === 'application/pdf';
    }
}