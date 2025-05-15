<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->string('id_siswa', 10)->primary();  // ID Siswa sebagai primary key
            $table->string('nama_siswa', 50)->notNullable();  // Memastikan nama siswa tidak null
            $table->string('tempat_lahir', 30)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tanggal_masuk')->nullable()->useCurrent();  // Menggunakan tanggal saat ini jika tidak diisi
            $table->boolean('status_aktif')->default(true);  // Status aktif default true
            $table->timestamps();  // created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};