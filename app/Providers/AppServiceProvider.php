<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Siswa;
use App\Models\DetailSiswa;
use App\Observers\SiswaObserver;
use App\Observers\DetailSiswaObserver;
use Illuminate\Support\Facades\Schema;

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
    }
}