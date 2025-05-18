<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Observers\SiswaObserver;
use App\Observers\DetailSiswaObserver;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Writer;

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
        
        Siswa::observe(SiswaObserver::class);
        DetailSiswa::observe(DetailSiswaObserver::class);
        
        // Konfigurasi Laravel Excel global untuk mengurangi penggunaan memory
        config(['excel.exports.store.disk' => 'local']); // Gunakan disk local untuk caching
        config(['excel.exports.store.path' => storage_path('app/excel')]); // Set path penyimpanan
        
        // Pastikan directory ada
        if (!file_exists(storage_path('app/excel'))) {
            mkdir(storage_path('app/excel'), 0755, true);
        }
    }
}