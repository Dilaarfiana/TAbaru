<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orang_tuas', function (Blueprint $table) {
            $table->string('id_orang_tua', 5)->primary();
            $table->string('id_siswa')->index();
            $table->string('nama_ayah', 100)->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('pekerjaan_ayah', 50)->nullable();
            $table->string('pendidikan_ayah', 50)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('pekerjaan_ibu', 50)->nullable();
            $table->string('pendidikan_ibu', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
            
            // Buat foreign key yang sesuai dengan tipe data
            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
        });

        // Tambahkan auto-increment untuk ID orang tua
        DB::statement("ALTER TABLE orang_tuas AUTO_INCREMENT = 1;");
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tuas');
    }
};