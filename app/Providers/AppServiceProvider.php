<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Observers\SiswaObserver;
use App\Observers\DetailSiswaObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Maatwebsite\Excel\Writer;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::disableForeignKeyConstraints();
        
        // ========== EXISTING OBSERVERS ==========
        Siswa::observe(SiswaObserver::class);
        DetailSiswa::observe(DetailSiswaObserver::class);
        
        // ========== NOTIFICATION OBSERVERS ==========
        // Pastikan observer notifikasi terdaftar untuk sistem notifikasi medis
        try {
            \App\Models\RekamMedis::observe(\App\Observers\RekamMedisObserver::class);
            \Log::info('RekamMedisObserver registered successfully');
        } catch (\Exception $e) {
            \Log::warning('Failed to register RekamMedisObserver: ' . $e->getMessage());
        }
        
        try {
            \App\Models\PemeriksaanAwal::observe(\App\Observers\PemeriksaanAwalObserver::class);
            \Log::info('PemeriksaanAwalObserver registered successfully');
        } catch (\Exception $e) {
            \Log::warning('Failed to register PemeriksaanAwalObserver: ' . $e->getMessage());
        }
        
        try {
            \App\Models\PemeriksaanFisik::observe(\App\Observers\PemeriksaanFisikObserver::class);
            \Log::info('PemeriksaanFisikObserver registered successfully');
        } catch (\Exception $e) {
            \Log::warning('Failed to register PemeriksaanFisikObserver: ' . $e->getMessage());
        }
        
        try {
            \App\Models\PemeriksaanHarian::observe(\App\Observers\PemeriksaanHarianObserver::class);
            \Log::info('PemeriksaanHarianObserver registered successfully');
        } catch (\Exception $e) {
            \Log::warning('Failed to register PemeriksaanHarianObserver: ' . $e->getMessage());
        }
        
        try {
            \App\Models\Resep::observe(\App\Observers\ResepObserver::class);
            \Log::info('ResepObserver registered successfully');
        } catch (\Exception $e) {
            \Log::warning('Failed to register ResepObserver: ' . $e->getMessage());
        }
        
        // ========== EXCEL CONFIGURATION ==========
        // Konfigurasi Laravel Excel global untuk mengurangi penggunaan memory
        config(['excel.exports.store.disk' => 'local']); // Gunakan disk local untuk caching
        config(['excel.exports.store.path' => storage_path('app/excel')]); // Set path penyimpanan
        
        // Pastikan directory ada
        if (!file_exists(storage_path('app/excel'))) {
            mkdir(storage_path('app/excel'), 0755, true);
        }
        
        // ========== NOTIFICATIONS TABLE CHECK ==========
        // Pastikan tabel notifications ada untuk sistem notifikasi
        if (!Schema::hasTable('notifications')) {
            try {
                Schema::create('notifications', function (Blueprint $table) {
                    $table->id();
                    $table->string('id_orang_tua', 5);
                    $table->string('id_siswa', 10);
                    $table->string('type', 50); // rekam_medis, pemeriksaan_awal, dll
                    $table->string('title');
                    $table->text('message');
                    $table->json('data')->nullable(); // additional data
                    $table->boolean('is_read')->default(false);
                    $table->string('created_by')->nullable();
                    $table->string('created_by_role')->nullable();
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();

                    // Indexes untuk performa
                    $table->index(['id_orang_tua', 'is_read']);
                    $table->index(['id_siswa']);
                    $table->index(['type']);
                    $table->index(['created_at']);
                });
                
                // Tambahkan foreign keys setelah tabel dibuat
                try {
                    Schema::table('notifications', function (Blueprint $table) {
                        $table->foreign('id_orang_tua')->references('id_orang_tua')->on('orang_tuas')->onDelete('cascade');
                        $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                    \Log::warning('Error adding foreign keys to notifications table: ' . $e->getMessage());
                }
                
                \Log::info('Notifications table created successfully');
            } catch (\Exception $e) {
                \Log::error('Error creating notifications table: ' . $e->getMessage());
            }
        }

        // ========== EXISTING DETAIL PEMERIKSAAN TABLE CHECK ==========
        // Cek apakah tabel Detail_Pemeriksaan sudah ada, jika belum buat
        if (!Schema::hasTable('Detail_Pemeriksaan')) {
            try {
                Schema::create('Detail_Pemeriksaan', function (Blueprint $table) {
                    $table->string('Id_DetPrx', 5)->primary();
                    $table->dateTime('Tanggal_Jam');
                    $table->string('Id_Siswa', 10);
                    $table->string('Id_Dokter', 5);
                    $table->string('NIP', 18);
                    
                    // Mencoba menambahkan foreign key
                    try {
                        $table->foreign('Id_Siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
                        $table->foreign('Id_Dokter')->references('Id_Dokter')->on('Dokters')->onDelete('cascade');
                        $table->foreign('NIP')->references('NIP')->on('Petugas_UKS')->onDelete('cascade');
                    } catch (\Exception $e) {
                        // Abaikan error foreign key jika tabel referensi belum ada
                        \Log::warning('Error adding foreign keys to Detail_Pemeriksaan: ' . $e->getMessage());
                    }
                });
                \Log::info('Detail_Pemeriksaan table created successfully');
            } catch (\Exception $e) {
                \Log::error('Error creating Detail_Pemeriksaan table: ' . $e->getMessage());
            }
        }
        
        // ========== LOG NOTIFICATION SYSTEM STATUS ==========
        \Log::info('Notification system initialized', [
            'observers_registered' => [
                'RekamMedis' => class_exists('\App\Observers\RekamMedisObserver'),
                'PemeriksaanAwal' => class_exists('\App\Observers\PemeriksaanAwalObserver'),
                'PemeriksaanFisik' => class_exists('\App\Observers\PemeriksaanFisikObserver'),
                'PemeriksaanHarian' => class_exists('\App\Observers\PemeriksaanHarianObserver'),
                'Resep' => class_exists('\App\Observers\ResepObserver')
            ],
            'tables_exist' => [
                'notifications' => Schema::hasTable('notifications'),
                'orang_tuas' => Schema::hasTable('orang_tuas'),
                'siswas' => Schema::hasTable('siswas')
            ]
        ]);
    }
}