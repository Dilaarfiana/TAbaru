<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokters', function (Blueprint $table) {
            $table->string('Id_Dokter', 5)->primary();
            $table->string('Nama_Dokter', 50);
            $table->string('Spesialisasi', 25)->nullable();
            $table->string('No_Telp', 15)->nullable();
            $table->text('Alamat')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokters');
    }
};