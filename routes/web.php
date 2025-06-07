<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\PetugasUKSController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\AlokasiController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\PemeriksaanAwalController;
use App\Http\Controllers\PemeriksaanFisikController;
use App\Http\Controllers\PemeriksaanHarianController;
use App\Http\Controllers\DetailSiswaController;
use App\Http\Controllers\DetailPemeriksaanController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanHarianController;

// =================== ROOT REDIRECT ===================
Route::get('/', function () {
    return redirect('/login');
});

// =================== AUTHENTICATION ROUTES ===================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// =================== API ROUTES - PUBLIC ACCESS ===================
// API Routes untuk modal dan AJAX - POSISI PENTING DI ATAS
Route::middleware(['auth.custom'])->prefix('api')->name('api.')->group(function () {
    
    // TEST API ROUTE - UNTUK DEBUGGING
    Route::get('test', [LaporanController::class, 'testApi'])->name('test');
    
    // API Detail Pemeriksaan - ROUTE UTAMA UNTUK MODAL
    Route::get('pemeriksaan/detail/{siswaId}/{rekamMedisId}', [LaporanController::class, 'getDetailPemeriksaan'])
        ->name('pemeriksaan.detail');
        
    
    // Screening API
    Route::get('screening/history/{siswaId}', [LaporanController::class, 'getScreeningHistory'])
        ->name('screening.history');
    Route::get('screening/comprehensive/{siswaId}', [LaporanController::class, 'getComprehensiveReport'])
        ->name('screening.comprehensive');
    
    // Pemeriksaan Harian API
    Route::get('harian/history/{siswaId}', function($siswaId) {
        try {
            $pemeriksaanHarian = \App\Models\PemeriksaanHarian::with(['petugasUks'])
                ->where('Id_Siswa', $siswaId)
                ->orderBy('Tanggal_Jam', 'desc')
                ->limit(20)
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $pemeriksaanHarian
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    })->name('harian.history');
    
    Route::get('harian/detail/{siswaId}/{harianId}', [LaporanHarianController::class, 'getDetailHarian'])
        ->name('harian.detail');
    
    // Siswa API for autocomplete/select
    Route::get('siswa/search', function(\Illuminate\Http\Request $request) {
        try {
            $search = $request->get('q');
            $siswa = \App\Models\Siswa::where('nama_siswa', 'like', '%' . $search . '%')
                ->orWhere('id_siswa', 'like', '%' . $search . '%')
                ->limit(20)
                ->get(['id_siswa', 'nama_siswa']);
                
            return response()->json($siswa);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    })->name('siswa.search');
    
    // Dashboard Stats API
    Route::get('dashboard/stats', function() {
        try {
            $userLevel = session('user_level');
            $siswaId = session('siswa_id');
            
            $stats = [];
            
            if ($userLevel === 'orang_tua' && $siswaId) {
                $stats = [
                    'total_pemeriksaan' => \App\Models\DetailPemeriksaan::where('id_siswa', $siswaId)->count(),
                    'pemeriksaan_bulan_ini' => \App\Models\DetailPemeriksaan::where('id_siswa', $siswaId)
                        ->whereMonth('tanggal_jam', now()->month)->count(),
                    'total_resep' => \App\Models\Resep::where('Id_Siswa', $siswaId)->count(),
                    'total_rekam_medis' => \App\Models\RekamMedis::where('Id_Siswa', $siswaId)->count()
                ];
            } else {
                $stats = [
                    'total_siswa' => \App\Models\Siswa::where('status_aktif', 1)->count(),
                    'total_pemeriksaan' => \App\Models\DetailPemeriksaan::count(),
                    'total_dokter' => \App\Models\Dokter::where('status_aktif', 1)->count(),
                    'total_petugas' => \App\Models\PetugasUKS::where('status_aktif', 1)->count()
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    })->name('dashboard.stats');

    // NOTIFICATION API ROUTES - Only for Orang Tua
    Route::middleware(['role:orang_tua'])->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('index');
        Route::get('/count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('count');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark_all_read');
    });
});

// =================== PROTECTED ROUTES ===================
Route::middleware(['auth.custom'])->group(function () {
    
    // =================== DASHBOARD ROUTES ===================
    Route::get('/dashboard', function () {
        $userLevel = session('user_level');
                 
        if ($userLevel === 'admin') {
            return redirect()->route('dashboard.admin');
        } elseif ($userLevel === 'petugas') {
            return redirect()->route('dashboard.petugas');
        } elseif ($userLevel === 'dokter') {
            return redirect()->route('dashboard.dokter');
        } elseif ($userLevel === 'orang_tua') {
            return redirect()->route('dashboard.orangtua');
        }
                 
        return redirect('/login');
    })->name('dashboard');

    // =================== PROFILE ROUTES - ALL USERS ===================
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =================== CHANGE PASSWORD ROUTES - ALL USERS ===================
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('change.password');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('change.password.update');

    // =================== ADMIN ROUTES - FULL ACCESS ===================
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.admin');
        
        // =================== ADMIN - DATA MASTER ===================
        
        // ADMIN - SISWA (Full CRUD + Import/Export)
        Route::prefix('siswa')->name('siswa.')->group(function () {
            Route::get('/', [SiswaController::class, 'index'])->name('index');
            Route::get('create', [SiswaController::class, 'create'])->name('create');
            Route::post('/', [SiswaController::class, 'store'])->name('store');
            Route::get('{siswa}', [SiswaController::class, 'show'])->name('show');
            Route::get('{siswa}/edit', [SiswaController::class, 'edit'])->name('edit');
            Route::put('{siswa}', [SiswaController::class, 'update'])->name('update');
            Route::delete('{siswa}', [SiswaController::class, 'destroy'])->name('destroy');
            
            
            // API routes
            Route::get('api/jurusan', [SiswaController::class, 'getJurusan'])->name('api.jurusan');
            Route::get('api/kelas', [SiswaController::class, 'getKelasByJurusan'])->name('api.kelas');
            
            // DETAIL SCREENING UNTUK ADMIN
            Route::get('{siswaId}/screening/{detailPemeriksaanId?}', [LaporanController::class, 'showScreeningDetail'])
                ->name('screening.detail');
        });

        // ADMIN - ORANG TUA (Full CRUD + Import/Export)
        Route::prefix('orangtua')->name('orangtua.')->group(function () {
            Route::get('/', [OrangTuaController::class, 'index'])->name('index');
            Route::get('create', [OrangTuaController::class, 'create'])->name('create');
            Route::post('/', [OrangTuaController::class, 'store'])->name('store');
            Route::get('{orangtua}', [OrangTuaController::class, 'show'])->name('show');
            Route::get('{orangtua}/edit', [OrangTuaController::class, 'edit'])->name('edit');
            Route::put('{orangtua}', [OrangTuaController::class, 'update'])->name('update');
            Route::delete('{orangtua}', [OrangTuaController::class, 'destroy'])->name('destroy');
          
        });
                
        // ADMIN - PETUGAS UKS (Full CRUD + Import/Export)
        Route::prefix('petugasuks')->name('petugasuks.')->group(function () {
            Route::get('/', [PetugasUKSController::class, 'index'])->name('index');
            Route::get('create', [PetugasUKSController::class, 'create'])->name('create');
            Route::post('/', [PetugasUKSController::class, 'store'])->name('store');
            Route::get('{petugasuks}', [PetugasUKSController::class, 'show'])->name('show');
            Route::get('{petugasuks}/edit', [PetugasUKSController::class, 'edit'])->name('edit');
            Route::put('{petugasuks}', [PetugasUKSController::class, 'update'])->name('update');
            Route::delete('{petugasuks}', [PetugasUKSController::class, 'destroy'])->name('destroy');

        });
        
        // ADMIN - DOKTER (Full CRUD)
        Route::resource('dokter', DokterController::class);

        // ADMIN - KELAS (Full CRUD)
        Route::resource('kelas', KelasController::class);

        // ADMIN - JURUSAN (Full CRUD)
        Route::resource('jurusan', JurusanController::class);

        // =================== ADMIN - PEMERIKSAAN ===================
        
        // Detail Pemeriksaan - Full CRUD for Admin
        Route::resource('detail_pemeriksaan', DetailPemeriksaanController::class);
        
        // Pemeriksaan Awal - Full CRUD for Admin
        Route::resource('pemeriksaan_awal', PemeriksaanAwalController::class);
        
        // Pemeriksaan Fisik - Full CRUD for Admin
        Route::resource('pemeriksaan_fisik', PemeriksaanFisikController::class);
        
        // Rekam Medis - Full CRUD for Admin
        Route::prefix('rekam_medis')->name('rekam_medis.')->group(function () {
            Route::get('/', [RekamMedisController::class, 'index'])->name('index');
            Route::get('create', [RekamMedisController::class, 'create'])->name('create');
            Route::post('/', [RekamMedisController::class, 'store'])->name('store');
            Route::get('{rekam_medis}', [RekamMedisController::class, 'show'])->name('show');
            Route::get('{rekam_medis}/edit', [RekamMedisController::class, 'edit'])->name('edit');
            Route::put('{rekam_medis}', [RekamMedisController::class, 'update'])->name('update');
            Route::delete('{rekam_medis}', [RekamMedisController::class, 'destroy'])->name('destroy');
        });
        
        // Pemeriksaan Harian - Full CRUD for Admin
        Route::resource('pemeriksaan_harian', PemeriksaanHarianController::class);
        
        // =================== ADMIN - RESEP (FULL CRUD) ===================
        Route::prefix('resep')->name('resep.')->group(function () {
            Route::get('/', [ResepController::class, 'index'])->name('index');
            Route::get('create', [ResepController::class, 'create'])->name('create');
            Route::post('/', [ResepController::class, 'store'])->name('store');
            Route::get('{id}', [ResepController::class, 'show'])->name('show');
            Route::get('{id}/edit', [ResepController::class, 'edit'])->name('edit');
            Route::put('{id}', [ResepController::class, 'update'])->name('update');
            Route::delete('{id}', [ResepController::class, 'destroy'])->name('destroy');
            
            // Document & Print Routes
            Route::get('{id}/view-dokumen', [ResepController::class, 'viewDokumen'])->name('view-dokumen');
            Route::get('{id}/download-dokumen', [ResepController::class, 'downloadDokumen'])->name('download-dokumen');
            Route::get('{id}/cetak', [ResepController::class, 'cetak'])->name('cetak');
            
            // Export - Admin Only
            Route::get('export', [ResepController::class, 'export'])->name('export');
        });

        // =================== ADMIN - LAPORAN ===================
        Route::prefix('laporan')->name('laporan.')->group(function () {
            // Screening Routes
            Route::get('/screening', [LaporanController::class, 'screening'])->name('screening');
            Route::post('/screening/export', [LaporanController::class, 'exportScreening'])->name('screening.export');
            Route::get('/screening/pdf/{siswaId}', [LaporanController::class, 'generateScreeningPDF'])->name('screening.pdf');
            Route::get('/screening/preview/{siswaId}', [LaporanController::class, 'previewScreeningPDF'])->name('screening.preview');
            Route::post('/screening/bulk-pdf', [LaporanController::class, 'bulkGenerateScreeningPDF'])->name('screening.bulk-pdf');
            Route::get('/screening/history/{siswaId}', [LaporanController::class, 'getScreeningHistory'])->name('screening.history');
            Route::get('/screening/detail', [LaporanController::class, 'screeningDetail'])->name('screening.detail');
            
            // Pemeriksaan Harian Routes - Admin
            Route::get('/harian', [LaporanHarianController::class, 'harian'])->name('harian');
            Route::post('/harian/export', [LaporanHarianController::class, 'exportHarian'])->name('harian.export');
            Route::get('/harian/pdf/{siswaId}', [LaporanHarianController::class, 'generateHarianPDF'])->name('harian.pdf');
            Route::get('/harian/detail/{siswaId}/{harianId}', [LaporanHarianController::class, 'getDetailHarian'])->name('harian.detail');
            // =================== TAMBAHAN ROUTE HARIAN DETAIL - ADMIN ===================
            Route::get('/harian/detail/{siswaId}/{harianId}', [LaporanHarianController::class, 'showHarianDetail'])->name('harian.detail.show');
            
            // Legacy Routes untuk kompatibilitas
            Route::get('/pemeriksaan-harian', [LaporanHarianController::class, 'harian'])->name('pemeriksaan_harian');
            Route::post('/pemeriksaan-harian/export', [LaporanHarianController::class, 'exportHarian'])->name('pemeriksaan_harian.export');
            Route::get('/pemeriksaan-harian/pdf/{siswaId}', [LaporanHarianController::class, 'generateHarianPDF'])->name('pemeriksaan_harian.pdf');
            Route::get('/pemeriksaan-harian/preview/{siswaId}', function(\Illuminate\Http\Request $request, $siswaId) {
                $pdf = app(LaporanHarianController::class)->generateHarianPDF($request, $siswaId);
                return $pdf->stream('preview_pemeriksaan_harian.pdf');
            })->name('pemeriksaan_harian.preview');
            
            // Rekam Medis Routes
            Route::get('/rekam-medis', [LaporanController::class, 'rekamMedis'])->name('rekam_medis');
            Route::post('/rekam-medis/export', [LaporanController::class, 'exportRekamMedis'])->name('rekam_medis.export');
            Route::get('/rekam-medis/pdf/{siswaId}', [LaporanController::class, 'generateRekamMedisPDF'])->name('rekam_medis.pdf');
            
            // Statistik Routes
            Route::get('/statistik', [LaporanController::class, 'statistik'])->name('statistik');
            Route::post('/statistik/export', [LaporanController::class, 'exportStatistik'])->name('statistik.export');
            
            // Dashboard Analytics Routes
            Route::get('/analytics', [LaporanController::class, 'analytics'])->name('analytics');
            Route::get('/analytics/chart-data', [LaporanController::class, 'getChartData'])->name('analytics.chart');
            
            // Bulk Operations (Admin Only)
            Route::post('/bulk-export-all', [LaporanController::class, 'bulkExportAll'])->name('bulk.export.all');
            Route::get('/export-template', [LaporanController::class, 'downloadExportTemplate'])->name('export.template');
        });

        // =================== ADMIN - ALOKASI ===================
        Route::prefix('alokasi')->name('alokasi.')->group(function () {
            Route::get('/', [AlokasiController::class, 'index'])->name('index');
            Route::get('unallocated', [AlokasiController::class, 'unallocated'])->name('unallocated');
            Route::get('allocated', [AlokasiController::class, 'allocated'])->name('allocated');
            Route::get('filter', [AlokasiController::class, 'filter'])->name('filter');
            Route::post('process', [AlokasiController::class, 'alokasi'])->name('process');
            Route::post('multiple', [AlokasiController::class, 'allocateMultiple'])->name('multiple');
            Route::post('kembalikan', [AlokasiController::class, 'kembalikan'])->name('kembalikan');
            Route::post('pindah', [AlokasiController::class, 'pindah'])->name('pindah');
            Route::get('kenaikan', [AlokasiController::class, 'kenaikanForm'])->name('kenaikanForm');
            Route::post('proses-kenaikan', [AlokasiController::class, 'prosesKenaikanKelas'])->name('proses-kenaikan');
            Route::get('lulus', [AlokasiController::class, 'lulusForm'])->name('lulusForm');
            Route::post('proses-kelulusan', [AlokasiController::class, 'prosesKelulusan'])->name('proses-kelulusan');
        });

        // =================== ADMIN - DETAIL SISWA ===================
        Route::prefix('detailsiswa')->name('detailsiswa.')->group(function () {
            Route::get('cleanup', [DetailSiswaController::class, 'cleanup'])->name('cleanup');
            Route::get('cleanup-duplicates', [DetailSiswaController::class, 'cleanupDuplicates'])->name('cleanup-duplicates');
        });
        Route::resource('detailsiswa', DetailSiswaController::class);
    });

    // =================== PETUGAS ROUTES - LIMITED ACCESS ===================
    Route::middleware(['role:petugas'])->prefix('petugas')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.petugas');

        // =================== PETUGAS - DATA SISWA (Read + Update Only, NO Import/Export/Delete) ===================
        Route::prefix('siswa')->name('petugas.siswa.')->group(function () {
            Route::get('/', [SiswaController::class, 'index'])->name('index');
            Route::get('{siswa}', [SiswaController::class, 'show'])->name('show');
            Route::get('{siswa}/edit', [SiswaController::class, 'edit'])->name('edit');
            Route::put('{siswa}', [SiswaController::class, 'update'])->name('update');
            
            // ❌ NO CREATE, DELETE, IMPORT, EXPORT routes for petugas
            
            // API routes untuk dropdown data
            Route::get('api/jurusan', [SiswaController::class, 'getJurusan'])->name('api.jurusan');
            Route::get('api/kelas', [SiswaController::class, 'getKelasByJurusan'])->name('api.kelas');
            
            // DETAIL SCREENING UNTUK PETUGAS
            Route::get('{siswaId}/screening/{detailPemeriksaanId?}', [LaporanController::class, 'showScreeningDetail'])
                ->name('screening.detail');
        });

        // =================== PETUGAS - PEMERIKSAAN (CRU without Delete) ===================
        
        // Detail Pemeriksaan
        Route::prefix('detail_pemeriksaan')->name('petugas.detail_pemeriksaan.')->group(function () {
            Route::get('/', [DetailPemeriksaanController::class, 'index'])->name('index');
            Route::get('create', [DetailPemeriksaanController::class, 'create'])->name('create');
            Route::post('/', [DetailPemeriksaanController::class, 'store'])->name('store');
            Route::get('{detail_pemeriksaan}', [DetailPemeriksaanController::class, 'show'])->name('show');
            Route::get('{detail_pemeriksaan}/edit', [DetailPemeriksaanController::class, 'edit'])->name('edit');
            Route::put('{detail_pemeriksaan}', [DetailPemeriksaanController::class, 'update'])->name('update');
            // ❌ NO DELETE route
        });
        
        // Pemeriksaan Awal
        Route::prefix('pemeriksaan_awal')->name('petugas.pemeriksaan_awal.')->group(function () {
            Route::get('/', [PemeriksaanAwalController::class, 'index'])->name('index');
            Route::get('create', [PemeriksaanAwalController::class, 'create'])->name('create');
            Route::post('/', [PemeriksaanAwalController::class, 'store'])->name('store');
            Route::get('{pemeriksaan_awal}', [PemeriksaanAwalController::class, 'show'])->name('show');
            Route::get('{pemeriksaan_awal}/edit', [PemeriksaanAwalController::class, 'edit'])->name('edit');
            Route::put('{pemeriksaan_awal}', [PemeriksaanAwalController::class, 'update'])->name('update');
            // ❌ NO DELETE route
        });

        // Pemeriksaan Fisik
        Route::prefix('pemeriksaan_fisik')->name('petugas.pemeriksaan_fisik.')->group(function () {
            Route::get('/', [PemeriksaanFisikController::class, 'index'])->name('index');
            Route::get('create', [PemeriksaanFisikController::class, 'create'])->name('create');
            Route::post('/', [PemeriksaanFisikController::class, 'store'])->name('store');
            Route::get('{pemeriksaan_fisik}', [PemeriksaanFisikController::class, 'show'])->name('show');
            Route::get('{pemeriksaan_fisik}/edit', [PemeriksaanFisikController::class, 'edit'])->name('edit');
            Route::put('{pemeriksaan_fisik}', [PemeriksaanFisikController::class, 'update'])->name('update');
            // ❌ NO DELETE route
        });

        // Rekam Medis
        Route::prefix('rekam_medis')->name('petugas.rekam_medis.')->group(function () {
            Route::get('/', [RekamMedisController::class, 'index'])->name('index');
            Route::get('create', [RekamMedisController::class, 'create'])->name('create');
            Route::post('/', [RekamMedisController::class, 'store'])->name('store');
            Route::get('{rekam_medis}', [RekamMedisController::class, 'show'])->name('show');
            Route::get('{rekam_medis}/edit', [RekamMedisController::class, 'edit'])->name('edit');
            Route::put('{rekam_medis}', [RekamMedisController::class, 'update'])->name('update');
            // ❌ NO DELETE route
        });

        // Pemeriksaan Harian
        Route::prefix('pemeriksaan_harian')->name('petugas.pemeriksaan_harian.')->group(function () {
            Route::get('/', [PemeriksaanHarianController::class, 'index'])->name('index');
            Route::get('create', [PemeriksaanHarianController::class, 'create'])->name('create');
            Route::post('/', [PemeriksaanHarianController::class, 'store'])->name('store');
            Route::get('{pemeriksaan_harian}', [PemeriksaanHarianController::class, 'show'])->name('show');
            Route::get('{pemeriksaan_harian}/edit', [PemeriksaanHarianController::class, 'edit'])->name('edit');
            Route::put('{pemeriksaan_harian}', [PemeriksaanHarianController::class, 'update'])->name('update');
            // ❌ NO DELETE route
        });

        // =================== PETUGAS - RESEP (CRU without Delete) ===================
        Route::prefix('resep')->name('petugas.resep.')->group(function () {
            Route::get('/', [ResepController::class, 'index'])->name('index');
            Route::get('create', [ResepController::class, 'create'])->name('create');
            Route::post('/', [ResepController::class, 'store'])->name('store');
            Route::get('{id}', [ResepController::class, 'show'])->name('show');
            Route::get('{id}/edit', [ResepController::class, 'edit'])->name('edit');
            Route::put('{id}', [ResepController::class, 'update'])->name('update');
            // ❌ NO DELETE route for petugas
            
            // Document & Print Routes - Petugas dapat akses
            Route::get('{id}/view-dokumen', [ResepController::class, 'viewDokumen'])->name('view-dokumen');
            Route::get('{id}/download-dokumen', [ResepController::class, 'downloadDokumen'])->name('download-dokumen');
            Route::get('{id}/cetak', [ResepController::class, 'cetak'])->name('cetak');
            // ❌ NO EXPORT route for petugas
        });

        // =================== PETUGAS - LAPORAN (Limited Access) ===================
        Route::prefix('laporan')->name('petugas.laporan.')->group(function () {
            // Screening Routes
            Route::get('/screening', [LaporanController::class, 'screening'])->name('screening');
            Route::post('/screening/export', [LaporanController::class, 'exportScreening'])->name('screening.export');
            Route::get('/screening/pdf/{siswaId}', [LaporanController::class, 'generateScreeningPDF'])->name('screening.pdf');
            Route::get('/screening/preview/{siswaId}', [LaporanController::class, 'previewScreeningPDF'])->name('screening.preview');
            Route::get('/screening/history/{siswaId}', [LaporanController::class, 'getScreeningHistory'])->name('screening.history');
            
            // Pemeriksaan Harian Routes (Petugas Focus)
            Route::get('/harian', [LaporanHarianController::class, 'harian'])->name('harian');
            Route::post('/harian/export', [LaporanHarianController::class, 'exportHarian'])->name('harian.export');
            Route::get('/harian/pdf/{siswaId}', [LaporanHarianController::class, 'generateHarianPDF'])->name('harian.pdf');
            // =================== TAMBAHAN ROUTE HARIAN DETAIL - PETUGAS ===================
            Route::get('/harian/detail/{siswaId}/{harianId}', [LaporanHarianController::class, 'showHarianDetail'])->name('harian.detail.show');
            
            // Legacy Routes untuk kompatibilitas  
            Route::get('/pemeriksaan-harian', [LaporanHarianController::class, 'harian'])->name('pemeriksaan_harian');
            Route::post('/pemeriksaan-harian/export', [LaporanHarianController::class, 'exportHarian'])->name('pemeriksaan_harian.export');
            Route::get('/pemeriksaan-harian/pdf/{siswaId}', [LaporanHarianController::class, 'generateHarianPDF'])->name('pemeriksaan_harian.pdf');
            Route::get('/pemeriksaan-harian/preview/{siswaId}', function(\Illuminate\Http\Request $request, $siswaId) {
                $pdf = app(LaporanHarianController::class)->generateHarianPDF($request, $siswaId);
                return $pdf->stream('preview_pemeriksaan_harian.pdf');
            })->name('pemeriksaan_harian.preview');
            
            // Statistik Terbatas untuk Petugas
            Route::get('/statistik-harian', [LaporanController::class, 'statistikHarian'])->name('statistik_harian');
            Route::post('/statistik-harian/export', [LaporanController::class, 'exportStatistikHarian'])->name('statistik_harian.export');
            
            // Dashboard Petugas
            Route::get('/dashboard-petugas', [LaporanController::class, 'dashboardPetugas'])->name('dashboard');
        });

        // AJAX Routes untuk Petugas (untuk modal tambah data)
        Route::post('/harian/add', [LaporanHarianController::class, 'addHarian'])->name('petugas.harian.add');
    });

    // =================== DOKTER ROUTES - READ ONLY ACCESS ===================
    Route::middleware(['role:dokter'])->prefix('dokter')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.dokter');
        
        // =================== DOKTER - DATA SISWA (Read Only) ===================
        Route::prefix('siswa')->name('dokter.siswa.')->group(function () {
            Route::get('/', [SiswaController::class, 'index'])->name('index');
            Route::get('{siswa}', [SiswaController::class, 'show'])->name('show');
            // ❌ NO CREATE, EDIT, UPDATE, DELETE routes
            
            // DETAIL SCREENING UNTUK DOKTER
            Route::get('{siswaId}/screening/{detailPemeriksaanId?}', [LaporanController::class, 'showScreeningDetail'])
                ->name('screening.detail');
        });
        
        // =================== DOKTER - PEMERIKSAAN (Read Only) ===================
        Route::get('/rekam_medis', [RekamMedisController::class, 'index'])->name('dokter.rekam_medis.index');
        Route::get('/rekam_medis/{rekam_medis}', [RekamMedisController::class, 'show'])->name('dokter.rekam_medis.show');
        
        Route::get('/pemeriksaan_awal', [PemeriksaanAwalController::class, 'index'])->name('dokter.pemeriksaan_awal.index');
        Route::get('/pemeriksaan_awal/{pemeriksaan_awal}', [PemeriksaanAwalController::class, 'show'])->name('dokter.pemeriksaan_awal.show');
        
        Route::get('/pemeriksaan_fisik', [PemeriksaanFisikController::class, 'index'])->name('dokter.pemeriksaan_fisik.index');
        Route::get('/pemeriksaan_fisik/{pemeriksaan_fisik}', [PemeriksaanFisikController::class, 'show'])->name('dokter.pemeriksaan_fisik.show');
        
        // =================== DOKTER - RESEP (Read Only) ===================
        Route::prefix('resep')->name('dokter.resep.')->group(function () {
            Route::get('/', [ResepController::class, 'index'])->name('index');
            Route::get('{id}', [ResepController::class, 'show'])->name('show');
            // ❌ NO CREATE, EDIT, UPDATE, DELETE routes for dokter
            
            // Document & Print Routes - Dokter dapat akses
            Route::get('{id}/view-dokumen', [ResepController::class, 'viewDokumen'])->name('view-dokumen');
            Route::get('{id}/download-dokumen', [ResepController::class, 'downloadDokumen'])->name('download-dokumen');
            Route::get('{id}/cetak', [ResepController::class, 'cetak'])->name('cetak');
            // ❌ NO EXPORT route for dokter
        });
        
        Route::get('/detail_pemeriksaan', [DetailPemeriksaanController::class, 'index'])->name('dokter.detail_pemeriksaan.index');
        Route::get('/detail_pemeriksaan/{detail_pemeriksaan}', [DetailPemeriksaanController::class, 'show'])->name('dokter.detail_pemeriksaan.show');

        // =================== DOKTER - LAPORAN (Read Only + Export) ===================
        Route::prefix('laporan')->name('dokter.laporan.')->group(function () {
            // Screening Routes (Dokter Focus)
            Route::get('/screening', [LaporanController::class, 'screening'])->name('screening');
            Route::post('/screening/export', [LaporanController::class, 'exportScreening'])->name('screening.export');
            Route::get('/screening/pdf/{siswaId}', [LaporanController::class, 'generateScreeningPDF'])->name('screening.pdf');
            Route::get('/screening/preview/{siswaId}', [LaporanController::class, 'previewScreeningPDF'])->name('screening.preview');
            Route::get('/screening/history/{siswaId}', [LaporanController::class, 'getScreeningHistory'])->name('screening.history');
            
            // Pemeriksaan Harian Routes (Dokter - Read Only)
            Route::get('/harian', [LaporanHarianController::class, 'harian'])->name('harian');
            Route::post('/harian/export', [LaporanHarianController::class, 'exportHarian'])->name('harian.export');
            Route::get('/harian/pdf/{siswaId}', [LaporanHarianController::class, 'generateHarianPDF'])->name('harian.pdf');
            // =================== TAMBAHAN ROUTE HARIAN DETAIL - DOKTER ===================
            Route::get('/harian/detail/{siswaId}/{harianId}', [LaporanHarianController::class, 'showHarianDetail'])->name('harian.detail.show');
            
            // Rekam Medis Routes (Dokter Focus)
            Route::get('/rekam-medis', [LaporanController::class, 'rekamMedis'])->name('rekam_medis');
            Route::post('/rekam-medis/export', [LaporanController::class, 'exportRekamMedis'])->name('rekam_medis.export');
            Route::get('/rekam-medis/pdf/{siswaId}', [LaporanController::class, 'generateRekamMedisPDF'])->name('rekam_medis.pdf');
            Route::get('/rekam-medis/preview/{siswaId}', [LaporanController::class, 'previewRekamMedisPDF'])->name('rekam_medis.preview');
            
            // Pemeriksaan Fisik & Awal (Dokter dapat akses)
            Route::get('/pemeriksaan-fisik', [LaporanController::class, 'pemeriksaanFisik'])->name('pemeriksaan_fisik');
            Route::post('/pemeriksaan-fisik/export', [LaporanController::class, 'exportPemeriksaanFisik'])->name('pemeriksaan_fisik.export');
            
            // Resep Obat (Dokter Focus)
            Route::get('/resep', [LaporanController::class, 'resepObat'])->name('resep');
            Route::post('/resep/export', [LaporanController::class, 'exportResepObat'])->name('resep.export');
            Route::get('/resep/pdf/{siswaId}', [LaporanController::class, 'generateResepPDF'])->name('resep.pdf');
            
            // Statistik untuk Dokter
            Route::get('/statistik-medis', [LaporanController::class, 'statistikMedis'])->name('statistik_medis');
            Route::post('/statistik-medis/export', [LaporanController::class, 'exportStatistikMedis'])->name('statistik_medis.export');
            
            // Dashboard Dokter
            Route::get('/dashboard-dokter', [LaporanController::class, 'dashboardDokter'])->name('dashboard');
        });
    });

    // =================== ORANG TUA ROUTES - VERY LIMITED ACCESS ===================
    Route::middleware(['role:orang_tua'])->prefix('orangtua')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.orangtua');
        
        // NOTIFICATIONS
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('orangtua.notifications.index');

        // =================== ORANG TUA - DATA SISWA SAYA ===================
        Route::prefix('siswa')->name('orangtua.siswa.')->group(function () {
            Route::get('/', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $siswa = \App\Models\Siswa::with(['detailSiswa', 'detailSiswa.kelas', 'detailSiswa.kelas.jurusan', 'orangTua'])
                    ->findOrFail($siswaId);
                    
                return view('orangtua.siswa.show', compact('siswa'));
            })->name('show');
            
            Route::get('/edit', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $siswa = \App\Models\Siswa::with(['detailSiswa', 'detailSiswa.kelas', 'detailSiswa.kelas.jurusan', 'orangTua'])
                    ->findOrFail($siswaId);
                    
                return view('orangtua.siswa.edit', compact('siswa'));
            })->name('edit');
            
            Route::put('/', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $siswa = \App\Models\Siswa::findOrFail($siswaId);
                
                // Update hanya field yang diizinkan untuk orang tua (limited fields)
                $allowedFields = ['nama_siswa', 'tempat_lahir', 'tanggal_lahir'];
                $updateData = $request->only($allowedFields);
                
                $siswa->update($updateData);
                
                return redirect()->route('orangtua.siswa.show')->with('success', 'Data siswa berhasil diperbarui');
            })->name('update');
            
            // DETAIL SCREENING UNTUK ORANG TUA
            Route::get('{siswaId}/screening/{detailPemeriksaanId?}', [LaporanController::class, 'showScreeningDetail'])
                ->name('screening.detail');
        });
        
        // =================== ORANG TUA - RIWAYAT PEMERIKSAAN ===================
        Route::prefix('riwayat')->name('orangtua.riwayat.')->group(function () {
            // Rekam Medis
            Route::get('/rekam_medis', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $rekamMedis = \App\Models\RekamMedis::with(['dokter'])
                    ->where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Jam', 'desc')
                    ->paginate(10);
                    
                return view('orangtua.riwayat.rekam_medis', compact('rekamMedis'));
            })->name('rekam_medis');
            
            // Pemeriksaan Awal
            Route::get('/pemeriksaan_awal', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $pemeriksaanAwal = \App\Models\PemeriksaanAwal::with(['detailPemeriksaan', 'detailPemeriksaan.dokter', 'detailPemeriksaan.petugasUks'])
                    ->whereHas('detailPemeriksaan', function($query) use ($siswaId) {
                        $query->where('id_siswa', $siswaId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    
                return view('orangtua.riwayat.pemeriksaan_awal', compact('pemeriksaanAwal'));
            })->name('pemeriksaan_awal');
            
            // Pemeriksaan Fisik
            Route::get('/pemeriksaan_fisik', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $pemeriksaanFisik = \App\Models\PemeriksaanFisik::with(['detailPemeriksaan', 'detailPemeriksaan.dokter', 'detailPemeriksaan.petugasUks'])
                    ->whereHas('detailPemeriksaan', function($query) use ($siswaId) {
                        $query->where('id_siswa', $siswaId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    
                return view('orangtua.riwayat.pemeriksaan_fisik', compact('pemeriksaanFisik'));
            })->name('pemeriksaan_fisik');
            
            // Pemeriksaan Harian
            Route::get('/pemeriksaan_harian', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $pemeriksaanHarian = \App\Models\PemeriksaanHarian::with(['petugasUks'])
                    ->where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Jam', 'desc')
                    ->paginate(15);
                    
                return view('orangtua.riwayat.pemeriksaan_harian', compact('pemeriksaanHarian'));
            })->name('pemeriksaan_harian');
            
            // Resep Obat
            Route::get('/resep', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $resep = \App\Models\Resep::with(['dokter'])
                    ->where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->paginate(10);
                    
                return view('orangtua.riwayat.resep', compact('resep'));
            })->name('resep');
        });

        // =================== ORANG TUA - LAPORAN (Child Data Only) ===================
        Route::prefix('laporan')->name('orangtua.laporan.')->group(function () {
            // Screening Routes (Child Only)
            Route::get('/screening', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->screening($request, $siswaId);
            })->name('screening');
            
            Route::post('/screening/export', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->exportScreening($request, $siswaId);
            })->name('screening.export');
            
            Route::get('/screening/pdf', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->generateScreeningPDF($request, $siswaId);
            })->name('screening.pdf');
            
            Route::get('/screening/preview', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->previewScreeningPDF($request, $siswaId);
            })->name('screening.preview');
            
            Route::get('/screening/history', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan'], 404);
                }
                return app(LaporanController::class)->getScreeningHistory($siswaId);
            })->name('screening.history');
            
            // Pemeriksaan Harian Routes (Child Only)
            Route::get('/harian', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanHarianController::class)->harian($request);
            })->name('harian');
            
            Route::post('/harian/export', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanHarianController::class)->exportHarian($request, $siswaId);
            })->name('harian.export');
            
            Route::get('/harian/pdf/{pemeriksaanHarianId?}', function(\Illuminate\Http\Request $request, $pemeriksaanHarianId = null) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                if ($pemeriksaanHarianId) {
                    $request->merge(['pemeriksaan_harian_id' => $pemeriksaanHarianId]);
                }
                
                return app(LaporanHarianController::class)->generateHarianPDF($request, $siswaId);
            })->name('harian.pdf');
            
            Route::get('/harian/detail/{harianId}', function(\Illuminate\Http\Request $request, $harianId) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan'], 404);
                }
                return app(LaporanHarianController::class)->getDetailHarian($siswaId, $harianId);
            })->name('harian.detail');
            
            // =================== TAMBAHAN ROUTE HARIAN DETAIL - ORANG TUA ===================
            Route::get('/harian/detail/{siswaId}/{harianId}', [LaporanHarianController::class, 'showHarianDetail'])->name('harian.detail.show');
            
            // Legacy Routes untuk kompatibilitas
            Route::get('/pemeriksaan-harian', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanHarianController::class)->harian($request);
            })->name('pemeriksaan_harian');
            
            Route::post('/pemeriksaan-harian/export', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanHarianController::class)->exportHarian($request, $siswaId);
            })->name('pemeriksaan_harian.export');
            
            Route::get('/pemeriksaan-harian/pdf', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanHarianController::class)->generateHarianPDF($request, $siswaId);
            })->name('pemeriksaan_harian.pdf');
            
            // Rekam Medis Routes (Child Only)
            Route::get('/rekam-medis', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->rekamMedis($request, $siswaId);
            })->name('rekam_medis');
            
            Route::post('/rekam-medis/export', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->exportRekamMedis($request, $siswaId);
            })->name('rekam_medis.export');
            
            Route::get('/rekam-medis/pdf', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->generateRekamMedisPDF($request, $siswaId);
            })->name('rekam_medis.pdf');
            
            // Resep Obat Routes (Child Only)
            Route::get('/resep', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->resepObat($request, $siswaId);
            })->name('resep');
            
            Route::post('/resep/export', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->exportResepObat($request, $siswaId);
            })->name('resep.export');
            
            Route::get('/resep/pdf', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                return app(LaporanController::class)->generateResepPDF($request, $siswaId);
            })->name('resep.pdf');
            
            // Ringkasan Kesehatan Anak (Khusus Orang Tua)
            Route::get('/ringkasan-kesehatan', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $siswa = \App\Models\Siswa::with([
                    'detailSiswa.kelas.jurusan',
                    'orangTua'
                ])->findOrFail($siswaId);
                
                $rekamMedis = \App\Models\RekamMedis::where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Jam', 'desc')
                    ->limit(5)
                    ->get();
                
                $pemeriksaanHarian = \App\Models\PemeriksaanHarian::where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Jam', 'desc')
                    ->limit(10)
                    ->get();
                
                $resep = \App\Models\Resep::where('Id_Siswa', $siswaId)
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->limit(5)
                    ->get();
                
                return view('orangtua.laporan.ringkasan_kesehatan', compact('siswa', 'rekamMedis', 'pemeriksaanHarian', 'resep'));
            })->name('ringkasan_kesehatan');
            
            Route::get('/ringkasan-kesehatan/pdf', function(\Illuminate\Http\Request $request) {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $siswa = \App\Models\Siswa::with([
                    'detailSiswa.kelas.jurusan',
                    'orangTua'
                ])->findOrFail($siswaId);
                
                $rekamMedis = \App\Models\RekamMedis::where('Id_Siswa', $siswaId)->get();
                $pemeriksaanHarian = \App\Models\PemeriksaanHarian::where('Id_Siswa', $siswaId)->get();
                $resep = \App\Models\Resep::where('Id_Siswa', $siswaId)->get();
                
                $pdf = \PDF::loadView('orangtua.laporan.ringkasan_kesehatan_pdf', compact('siswa', 'rekamMedis', 'pemeriksaanHarian', 'resep'));
                
                $filename = 'Ringkasan_Kesehatan_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $siswa->nama_siswa) . '_' . date('Y-m-d') . '.pdf';
                
                return $pdf->download($filename);
            })->name('ringkasan_kesehatan.pdf');
            
            // Dashboard Orang Tua
            Route::get('/dashboard-orangtua', function() {
                $siswaId = session('siswa_id');
                if (!$siswaId) {
                    return redirect()->route('dashboard.orangtua')->with('error', 'Data siswa tidak ditemukan');
                }
                
                $totalPemeriksaan = \App\Models\PemeriksaanHarian::where('Id_Siswa', $siswaId)->count();
                $pemeriksaanBulanIni = \App\Models\PemeriksaanHarian::where('Id_Siswa', $siswaId)
                    ->whereMonth('Tanggal_Jam', now()->month)
                    ->whereYear('Tanggal_Jam', now()->year)
                    ->count();
                $totalRekamMedis = \App\Models\RekamMedis::where('Id_Siswa', $siswaId)->count();
                $totalResep = \App\Models\Resep::where('Id_Siswa', $siswaId)->count();
                
                return view('orangtua.laporan.dashboard', compact('totalPemeriksaan', 'pemeriksaanBulanIni', 'totalRekamMedis', 'totalResep'));
            })->name('dashboard');
        });
    });

    // =================== UTILITY ROUTES - ALL AUTHENTICATED USERS ===================
    Route::prefix('utils')->name('utils.')->group(function () {
        // Template Downloads
        Route::get('/template/screening-export', [LaporanController::class, 'downloadScreeningTemplate'])->name('template.screening');
        Route::get('/template/import-siswa', [LaporanController::class, 'downloadImportTemplate'])->name('template.import');
        Route::get('/template/harian-export', [LaporanHarianController::class, 'downloadHarianTemplate'])->name('template.harian');
        
        // Documentation
        Route::get('/help/laporan', [LaporanController::class, 'showLaporanHelp'])->name('help.laporan');
        Route::get('/help/export', [LaporanController::class, 'showExportHelp'])->name('help.export');
        Route::get('/help/harian', function() {
            return view('help.laporan_harian');
        })->name('help.harian');
        
        // System Info - ADMIN ONLY
        Route::get('/system/info', [LaporanController::class, 'getSystemInfo'])->name('system.info')->middleware('role:admin');
    });

    // =================== SHARED ROUTES - ALL AUTHENTICATED USERS ===================
    Route::prefix('shared')->name('shared.')->group(function () {
        // Get data referensi untuk dropdown dll
        Route::get('/jurusan', function() {
            $jurusan = \App\Models\Jurusan::all();
            return response()->json($jurusan);
        })->name('jurusan');
        
        Route::get('/kelas/{jurusanId?}', function($jurusanId = null) {
            $query = \App\Models\Kelas::with('jurusan');
            if ($jurusanId) {
                $query->where('Kode_Jurusan', $jurusanId);
            }
            $kelas = $query->get();
            return response()->json($kelas);
        })->name('kelas');
        
        Route::get('/petugas', function() {
            $petugas = \App\Models\PetugasUKS::where('status_aktif', 1)
                ->get(['NIP', 'nama_petugas_uks']);
            return response()->json($petugas);
        })->name('petugas');
        
        Route::get('/dokter', function() {
            $dokter = \App\Models\Dokter::where('status_aktif', 1)
                ->get(['Id_Dokter', 'Nama_Dokter', 'Spesialisasi']);
            return response()->json($dokter);
        })->name('dokter');
    });
});

// =================== FALLBACK ROUTES ===================
// Handle undefined routes
Route::fallback(function () {
    $userLevel = session('user_level');
    
    if (!$userLevel) {
        return redirect('/login')->with('error', 'Silakan login terlebih dahulu');
    }
    
    // Redirect ke dashboard sesuai role
    switch ($userLevel) {
        case 'admin':
            return redirect()->route('dashboard.admin')->with('error', 'Halaman tidak ditemukan');
        case 'petugas':
            return redirect()->route('dashboard.petugas')->with('error', 'Halaman tidak ditemukan');
        case 'dokter':
            return redirect()->route('dashboard.dokter')->with('error', 'Halaman tidak ditemukan');
        case 'orang_tua':
            return redirect()->route('dashboard.orangtua')->with('error', 'Halaman tidak ditemukan');
        default:
            return redirect('/login')->with('error', 'Sesi tidak valid');
    }
});