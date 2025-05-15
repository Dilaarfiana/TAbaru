<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\PetugasUKSController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\DetailSiswaController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\PemeriksaanAwalController;
use App\Http\Controllers\PemeriksaanFisikController;
use App\Http\Controllers\AlokasiController;

// Redirect dari halaman utama ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Siswa Route
Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::get('import', [SiswaController::class, 'importForm'])->name('import');
    Route::post('import', [SiswaController::class, 'import'])->name('import.process');
    Route::get('template', [SiswaController::class, 'template'])->name('template');
    Route::get('export', [SiswaController::class, 'export'])->name('export');
});
Route::resource('siswa', SiswaController::class);

// Alokasi routes
Route::resource('alokasi', AlokasiController::class);
Route::get('alokasi-unallocated', [AlokasiController::class, 'unallocated'])->name('alokasi.unallocated');
Route::post('alokasi-allocate-multiple', [AlokasiController::class, 'allocateMultiple'])->name('alokasi.allocate-multiple');

// Orang Tua Route
Route::prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('import', [OrangTuaController::class, 'importForm'])->name('import');
    Route::post('import', [OrangTuaController::class, 'import'])->name('import.process');
    Route::get('template', [OrangTuaController::class, 'template'])->name('template');
    Route::get('export', [OrangTuaController::class, 'export'])->name('export');
});
Route::resource('orangtua', OrangTuaController::class);

// Jurusan Route
Route::resource('jurusan', JurusanController::class);

// Dokter Route
Route::resource('dokter', DokterController::class);

// Kelas Route
Route::resource('kelas', KelasController::class);

// Petugas UKS Route
Route::prefix('petugasuks')->name('petugasuks.')->group(function () {
    Route::get('export', [PetugasUKSController::class, 'export'])->name('export');
    Route::get('import', [PetugasUKSController::class, 'importForm'])->name('import');
    Route::post('import', [PetugasUKSController::class, 'import'])->name('import.process');
});
Route::resource('petugasuks', PetugasUKSController::class);

// Detail Siswa Route
Route::resource('detailsiswa', DetailSiswaController::class)->except(['create', 'store']);

// Rekam Medis Route - Fixed to use underscore instead of hyphen
Route::prefix('rekam_medis')->name('rekam_medis.')->group(function () {
    Route::get('{id}/cetak', [RekamMedisController::class, 'cetak'])->name('cetak');
    Route::get('export', [RekamMedisController::class, 'export'])->name('export');
});
Route::resource('rekam_medis', RekamMedisController::class);

// Pemeriksaan Awal Route - Using underscore for consistency
Route::resource('pemeriksaan_awal', PemeriksaanAwalController::class);

// Pemeriksaan Fisik Route - Using underscore for consistency
Route::resource('pemeriksaan_fisik', PemeriksaanFisikController::class);