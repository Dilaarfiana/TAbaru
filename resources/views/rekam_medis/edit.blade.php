{{-- File: resources/views/rekam_medis/edit.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // BLOCK Orang tua - tidak boleh mengakses rekam medis sama sekali
    if ($isOrangTua) {
        return redirect()->route('dashboard.orangtua')->with('error', 'Akses ditolak. Orang tua tidak memiliki akses ke rekam medis. Silakan gunakan menu "Ringkasan Kesehatan" untuk melihat informasi kesehatan anak Anda.');
    }
    
    // BLOCK Dokter - hanya read only, tidak bisa edit
    if ($isDokter) {
        return redirect()->route('dokter.rekam_medis.index')->with('error', 'Akses ditolak. Dokter hanya memiliki akses baca (read only) untuk rekam medis. Tidak dapat mengedit data.');
    }
    
    // Only admin and petugas can edit rekam medis
    if (!$isAdmin && !$isPetugas) {
        abort(403, 'Anda tidak memiliki akses untuk mengedit rekam medis.');
    }
    
    // Define routes based on user role
    if ($isAdmin) {
        $baseRoute = 'rekam_medis';
    } elseif ($isPetugas) {
        $baseRoute = 'petugas.rekam_medis';
    } else {
        // Fallback, though shouldn't reach here due to access check above
        $baseRoute = 'rekam_medis';
    }
    
    $indexRoute = $baseRoute . '.index';
    $createRoute = $baseRoute . '.create';
    $storeRoute = $baseRoute . '.store';
    $showRoute = $baseRoute . '.show';
    $editRoute = $baseRoute . '.edit';
    $updateRoute = $baseRoute . '.update';
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-6xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-edit text-orange-500 mr-3 text-xl"></i>
                <h2 class="text-xl font-bold text-gray-800">Edit Rekam Medis</h2>
                @if($isPetugas)
                    <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                        <i class="fas fa-user-tie mr-1"></i>Akses Petugas (CRU)
                    </span>
                @elseif($isAdmin)
                    <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        <i class="fas fa-user-shield mr-1"></i>Akses Admin (Full CRUD)
                    </span>
                @endif
            </div>
            <div class="flex items-center space-x-2">
                @if(isset($rekamMedis))
                <span class="bg-orange-100 text-orange-800 text-sm font-bold py-1 px-3 rounded-full flex items-center">
                    <i class="fas fa-tag mr-1"></i>
                    {{ $rekamMedis->No_Rekam_Medis }}
                </span>
                @endif
                <a href="{{ route($showRoute, $rekamMedis->No_Rekam_Medis ?? '#') }}" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail
                </a>
                <a href="{{ route($indexRoute) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            <!-- Access Level Info -->
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-orange-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-orange-800 mb-1">Edit Rekam Medis</h3>
                        <p class="text-sm text-orange-700 mb-2">
                            Anda sedang mengedit rekam medis <strong>{{ $rekamMedis->No_Rekam_Medis ?? 'N/A' }}</strong>. 
                            Pastikan semua perubahan data sudah benar sebelum menyimpan. 
                            Riwayat perubahan akan tercatat dalam sistem.
                        </p>
                        
                        <!-- Role Information -->
                        <div class="mt-2 p-2 bg-orange-100 border border-orange-300 rounded">
                            <p class="text-sm text-orange-800">
                                <i class="fas fa-user-tag mr-1"></i>
                                <strong>Akses Anda:</strong> 
                                @if($isAdmin)
                                    Administrator - Dapat mengelola semua data rekam medis (Tambah, Edit, Lihat, Hapus, Cetak, Export)
                                @elseif($isPetugas)
                                    Petugas UKS - Dapat menambah, mengedit, melihat, dan mencetak rekam medis. <span class="text-red-600 font-semibold">Tidak dapat menghapus data.</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Mohon perbaiki kesalahan berikut:</p>
                            <ul class="text-sm text-red-700 list-disc list-inside mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
                <button type="button" class="close-alert text-red-500 hover:text-red-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            @if(session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            {!! session('warning') !!}
                        </p>
                    </div>
                </div>
                <button type="button" class="close-alert text-yellow-500 hover:text-yellow-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            @if(session('info'))
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            {{ session('info') }}
                        </p>
                    </div>
                </div>
                <button type="button" class="close-alert text-blue-500 hover:text-blue-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            <form action="{{ route($updateRoute, $rekamMedis->No_Rekam_Medis ?? '#') }}" method="POST" id="editRekamMedisForm">
                @csrf
                @method('PUT')
                
                <!-- Hidden ID -->
                <input type="hidden" name="No_Rekam_Medis" value="{{ $rekamMedis->No_Rekam_Medis ?? '' }}">
                
                <!-- Informasi Waktu -->
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <i class="fas fa-clock text-blue-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Waktu Pemeriksaan</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal dan Jam -->
                        <div>
                            <label for="Tanggal_Jam" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>
                                Tanggal & Jam Pemeriksaan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="datetime-local" 
                                    id="Tanggal_Jam" 
                                    name="Tanggal_Jam" 
                                    value="{{ old('Tanggal_Jam', isset($rekamMedis) ? \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Masukkan tanggal dan waktu pemeriksaan dilakukan</p>
                            @error('Tanggal_Jam')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle text-gray-500 mr-1"></i>
                                Preview Waktu
                            </h4>
                            <div id="timePreview" class="text-sm text-gray-600">
                                Pilih tanggal dan waktu untuk melihat preview
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid untuk Siswa, Dokter, dan Petugas -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Kolom 1: Siswa -->
                    <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                            <i class="fas fa-user-graduate text-green-500 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-800">Data Siswa</h3>
                        </div>
                        
                        <div>
                            <label for="Id_Siswa" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Siswa <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <select id="Id_Siswa" name="Id_Siswa" required
                                    class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 appearance-none">
                                    <option value="">-- Pilih Siswa --</option>
                                    @if(isset($siswas))
                                        @foreach($siswas as $siswa)
                                            <option value="{{ $siswa->id_siswa }}" 
                                                {{ old('Id_Siswa', $rekamMedis->Id_Siswa ?? '') == $siswa->id_siswa ? 'selected' : '' }}>
                                                {{ $siswa->id_siswa }} - {{ $siswa->nama_siswa }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih siswa yang akan diperiksa</p>
                            @error('Id_Siswa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Siswa Info Preview -->
                        <div id="siswaInfo" class="mt-4 p-3 bg-white rounded-md border border-green-200">
                            <h4 class="text-sm font-medium text-green-700 mb-2">
                                <i class="fas fa-user text-green-600 mr-1"></i>
                                Informasi Siswa
                            </h4>
                            <div id="siswaDetails" class="text-sm text-gray-600">
                                @if(isset($rekamMedis) && $rekamMedis->siswa)
                                    <div class="grid grid-cols-1 gap-1">
                                        <div><strong>ID:</strong> {{ $rekamMedis->siswa->id_siswa }}</div>
                                        <div><strong>Nama:</strong> {{ $rekamMedis->siswa->nama_siswa }}</div>
                                    </div>
                                @else
                                    Pilih siswa untuk melihat informasi
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2: Dokter -->
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                            <i class="fas fa-user-md text-blue-500 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-800">Data Dokter</h3>
                        </div>
                        
                        <div>
                            <label for="Id_Dokter" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Dokter <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-stethoscope text-gray-400"></i>
                                </div>
                                <select id="Id_Dokter" name="Id_Dokter" required
                                    class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                    <option value="">-- Pilih Dokter --</option>
                                    @if(isset($dokters))
                                        @foreach($dokters as $dokter)
                                            <option value="{{ $dokter->Id_Dokter }}" 
                                                {{ old('Id_Dokter', $rekamMedis->Id_Dokter ?? '') == $dokter->Id_Dokter ? 'selected' : '' }}>
                                                {{ $dokter->Id_Dokter }} - {{ $dokter->Nama_Dokter }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih dokter yang melakukan pemeriksaan</p>
                            @error('Id_Dokter')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dokter Info Preview -->
                        <div id="dokterInfo" class="mt-4 p-3 bg-white rounded-md border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-700 mb-2">
                                <i class="fas fa-user-md text-blue-600 mr-1"></i>
                                Informasi Dokter
                            </h4>
                            <div id="dokterDetails" class="text-sm text-gray-600">
                                @if(isset($rekamMedis) && $rekamMedis->dokter)
                                    <div class="grid grid-cols-1 gap-1">
                                        <div><strong>ID:</strong> {{ $rekamMedis->dokter->Id_Dokter }}</div>
                                        <div><strong>Nama:</strong> {{ $rekamMedis->dokter->Nama_Dokter }}</div>
                                    </div>
                                @else
                                    Pilih dokter untuk melihat informasi
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 3: Petugas UKS -->
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-purple-200 pb-2">
                            <i class="fas fa-user-nurse text-purple-500 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-800">Petugas UKS</h3>
                        </div>
                        
                        <div>
                            <label for="NIP" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Petugas <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-badge text-gray-400"></i>
                                </div>
                                <select id="NIP" name="NIP" required
                                    class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500 appearance-none">
                                    <option value="">-- Pilih Petugas UKS --</option>
                                    @if(isset($petugasUKS))
                                        @foreach($petugasUKS as $petugas)
                                            <option value="{{ $petugas->NIP }}" 
                                                {{ old('NIP', $rekamMedis->NIP ?? '') == $petugas->NIP ? 'selected' : '' }}>
                                                {{ $petugas->NIP }} - {{ $petugas->nama_petugas_uks }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih petugas UKS yang melakukan pemeriksaan</p>
                            @error('NIP')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Petugas Info Preview -->
                        <div id="petugasInfo" class="mt-4 p-3 bg-white rounded-md border border-purple-200">
                            <h4 class="text-sm font-medium text-purple-700 mb-2">
                                <i class="fas fa-user-nurse text-purple-600 mr-1"></i>
                                Informasi Petugas
                            </h4>
                            <div id="petugasDetails" class="text-sm text-gray-600">
                                @if(isset($rekamMedis) && $rekamMedis->petugasUKS)
                                    <div class="grid grid-cols-1 gap-1">
                                        <div><strong>NIP:</strong> {{ $rekamMedis->petugasUKS->NIP }}</div>
                                        <div><strong>Nama:</strong> {{ $rekamMedis->petugasUKS->nama_petugas_uks }}</div>
                                    </div>
                                @else
                                    Pilih petugas untuk melihat informasi
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keluhan Utama -->
                <div class="bg-red-50 border border-red-100 rounded-lg p-5 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b border-red-200 pb-2">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Keluhan Utama</h3>
                    </div>
                    
                    <div>
                        <label for="Keluhan_Utama" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-notes-medical text-red-500 mr-1"></i>
                            Detail Keluhan Utama <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <textarea id="Keluhan_Utama" name="Keluhan_Utama" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500"
                                placeholder="Masukkan keluhan utama pasien dengan detail...">{{ old('Keluhan_Utama', $rekamMedis->Keluhan_Utama ?? '') }}</textarea>
                            <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                <span id="keluhanCharCount">0</span> karakter
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Deskripsikan keluhan utama yang dialami siswa dengan lengkap</p>
                        @error('Keluhan_Utama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Template Keluhan -->
                    <div class="mt-4 p-3 bg-white rounded-md border border-red-200">
                        <h4 class="text-sm font-medium text-red-700 mb-2">
                            <i class="fas fa-lightbulb text-red-600 mr-1"></i>
                            Template Keluhan Umum
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <button type="button" class="keluhan-template-btn text-left p-2 text-xs bg-gray-50 hover:bg-red-100 rounded border transition-colors" 
                                data-template="Siswa mengeluh sakit kepala sejak pagi hari, intensitas nyeri sedang, tidak disertai mual atau muntah">
                                <i class="fas fa-head-side-cough text-red-500 mr-1"></i> Sakit Kepala
                            </button>
                            <button type="button" class="keluhan-template-btn text-left p-2 text-xs bg-gray-50 hover:bg-red-100 rounded border transition-colors"
                                data-template="Siswa mengeluh demam disertai menggigil, suhu badan terasa panas, lemas dan tidak nafsu makan">
                                <i class="fas fa-thermometer-half text-red-500 mr-1"></i> Demam
                            </button>
                            <button type="button" class="keluhan-template-btn text-left p-2 text-xs bg-gray-50 hover:bg-red-100 rounded border transition-colors"
                                data-template="Siswa mengeluh nyeri perut, mual, kembung, BAB tidak lancar sejak kemarin">
                                <i class="fas fa-hand-holding-medical text-red-500 mr-1"></i> Sakit Perut
                            </button>
                            <button type="button" class="keluhan-template-btn text-left p-2 text-xs bg-gray-50 hover:bg-red-100 rounded border transition-colors"
                                data-template="Siswa terjatuh saat bermain, mengalami luka lecet/robek di area [sebutkan lokasi], berdarah">
                                <i class="fas fa-band-aid text-red-500 mr-1"></i> Luka Akibat Jatuh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Medis -->
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-5 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b border-orange-200 pb-2">
                        <i class="fas fa-history text-orange-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Riwayat Medis</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Riwayat Penyakit Sekarang -->
                        <div>
                            <label for="Riwayat_Penyakit_Sekarang" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-clipboard-list text-orange-500 mr-1"></i>
                                Riwayat Penyakit Sekarang
                            </label>
                            <div class="relative">
                                <textarea id="Riwayat_Penyakit_Sekarang" name="Riwayat_Penyakit_Sekarang" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Riwayat penyakit yang sedang dialami...">{{ old('Riwayat_Penyakit_Sekarang', $rekamMedis->Riwayat_Penyakit_Sekarang ?? '') }}</textarea>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Jelaskan kondisi penyakit yang sedang dialami</p>
                        </div>
                        
                        <!-- Riwayat Penyakit Dahulu -->
                        <div>
                            <label for="Riwayat_Penyakit_Dahulu" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-clock text-orange-500 mr-1"></i>
                                Riwayat Penyakit Dahulu
                            </label>
                            <div class="relative">
                                <textarea id="Riwayat_Penyakit_Dahulu" name="Riwayat_Penyakit_Dahulu" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Riwayat penyakit yang pernah dialami...">{{ old('Riwayat_Penyakit_Dahulu', $rekamMedis->Riwayat_Penyakit_Dahulu ?? '') }}</textarea>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Jelaskan penyakit yang pernah dialami sebelumnya</p>
                        </div>
                        
                        <!-- Riwayat Imunisasi -->
                        <div>
                            <label for="Riwayat_Imunisasi" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-syringe text-orange-500 mr-1"></i>
                                Riwayat Imunisasi
                            </label>
                            <div class="relative">
                                <textarea id="Riwayat_Imunisasi" name="Riwayat_Imunisasi" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Catatan imunisasi yang sudah diberikan...">{{ old('Riwayat_Imunisasi', $rekamMedis->Riwayat_Imunisasi ?? '') }}</textarea>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Catat riwayat imunisasi yang telah diterima</p>
                        </div>
                        
                        <!-- Riwayat Penyakit Keluarga -->
                        <div>
                            <label for="Riwayat_Penyakit_Keluarga" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-users text-orange-500 mr-1"></i>
                                Riwayat Penyakit Keluarga
                            </label>
                            <div class="relative">
                                <textarea id="Riwayat_Penyakit_Keluarga" name="Riwayat_Penyakit_Keluarga" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                                    placeholder="Riwayat penyakit yang ada pada keluarga...">{{ old('Riwayat_Penyakit_Keluarga', $rekamMedis->Riwayat_Penyakit_Keluarga ?? '') }}</textarea>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Jelaskan riwayat penyakit keturunan dalam keluarga</p>
                        </div>
                    </div>
                </div>

                <!-- Silsilah Keluarga -->
                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-5 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b border-indigo-200 pb-2">
                        <i class="fas fa-sitemap text-indigo-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Silsilah Keluarga</h3>
                    </div>
                    
                    <div>
                        <label for="Silsilah_Keluarga" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-family text-indigo-500 mr-1"></i>
                            Informasi Silsilah Keluarga
                        </label>
                        <div class="relative">
                            <textarea id="Silsilah_Keluarga" name="Silsilah_Keluarga" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Masukkan informasi silsilah keluarga...">{{ old('Silsilah_Keluarga', $rekamMedis->Silsilah_Keluarga ?? '') }}</textarea>
                            <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                <span id="silsilahCharCount">0</span> karakter
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Jelaskan struktur dan informasi penting tentang keluarga</p>
                        @error('Silsilah_Keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.location.href='{{ route($indexRoute) }}'" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <i class="fas fa-times mr-2 text-gray-500"></i>
                        Batal
                    </button>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Update Rekam Medis</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.close-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });

    // Date validation - prevent future dates
    const dateTimeInput = document.getElementById('Tanggal_Jam');
    if (dateTimeInput) {
        const today = new Date();
        const formattedDate = today.toISOString().slice(0, 16);
        dateTimeInput.max = formattedDate;
        
        // Update time preview
        dateTimeInput.addEventListener('change', updateTimePreview);
        updateTimePreview(); // Initial update
    }
    
    function updateTimePreview() {
        const timePreview = document.getElementById('timePreview');
        if (dateTimeInput.value && timePreview) {
            const date = new Date(dateTimeInput.value);
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            timePreview.textContent = date.toLocaleDateString('id-ID', options);
        }
    }

    // Character counters for textareas
    const keluhanTextarea = document.getElementById('Keluhan_Utama');
    const keluhanCharCount = document.getElementById('keluhanCharCount');
    const silsilahTextarea = document.getElementById('Silsilah_Keluarga');
    const silsilahCharCount = document.getElementById('silsilahCharCount');
    
    if (keluhanTextarea && keluhanCharCount) {
        keluhanTextarea.addEventListener('input', function() {
            keluhanCharCount.textContent = this.value.length;
        });
        // Initialize counter
        keluhanCharCount.textContent = keluhanTextarea.value.length;
    }
    
    if (silsilahTextarea && silsilahCharCount) {
        silsilahTextarea.addEventListener('input', function() {
            silsilahCharCount.textContent = this.value.length;
        });
        // Initialize counter
        silsilahCharCount.textContent = silsilahTextarea.value.length;
    }

    // Template buttons for keluhan
    const keluhanTemplateBtns = document.querySelectorAll('.keluhan-template-btn');
    keluhanTemplateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const template = this.dataset.template;
            if (keluhanTextarea && template) {
                if (confirm('Apakah Anda yakin ingin mengganti keluhan dengan template ini? Data yang sudah ada akan terhapus.')) {
                    keluhanTextarea.value = template;
                    keluhanTextarea.focus();
                    if (keluhanCharCount) {
                        keluhanCharCount.textContent = keluhanTextarea.value.length;
                    }
                }
            }
        });
    });

    // Siswa selection handler
    const siswaSelect = document.getElementById('Id_Siswa');
    const siswaInfo = document.getElementById('siswaInfo');
    const siswaDetails = document.getElementById('siswaDetails');
    
    if (siswaSelect && siswaInfo && siswaDetails) {
        siswaSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const text = selectedOption.textContent;
                const [id, nama] = text.split(' - ');
                
                siswaDetails.innerHTML = `
                    <div class="grid grid-cols-1 gap-1">
                        <div><strong>ID:</strong> ${id}</div>
                        <div><strong>Nama:</strong> ${nama}</div>
                    </div>
                `;
                siswaInfo.classList.remove('hidden');
            } else {
                siswaInfo.classList.add('hidden');
            }
        });
    }

    // Dokter selection handler
    const dokterSelect = document.getElementById('Id_Dokter');
    const dokterInfo = document.getElementById('dokterInfo');
    const dokterDetails = document.getElementById('dokterDetails');
    
    if (dokterSelect && dokterInfo && dokterDetails) {
        dokterSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const text = selectedOption.textContent;
                const [id, nama] = text.split(' - ');
                
                dokterDetails.innerHTML = `
                    <div class="grid grid-cols-1 gap-1">
                        <div><strong>ID:</strong> ${id}</div>
                        <div><strong>Nama:</strong> ${nama}</div>
                    </div>
                `;
                dokterInfo.classList.remove('hidden');
            } else {
                dokterInfo.classList.add('hidden');
            }
        });
    }

    // Petugas selection handler
    const petugasSelect = document.getElementById('NIP');
    const petugasInfo = document.getElementById('petugasInfo');
    const petugasDetails = document.getElementById('petugasDetails');
    
    if (petugasSelect && petugasInfo && petugasDetails) {
        petugasSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const text = selectedOption.textContent;
                const [nip, nama] = text.split(' - ');
                
                petugasDetails.innerHTML = `
                    <div class="grid grid-cols-1 gap-1">
                        <div><strong>NIP:</strong> ${nip}</div>
                        <div><strong>Nama:</strong> ${nama}</div>
                    </div>
                `;
                petugasInfo.classList.remove('hidden');
            } else {
                petugasInfo.classList.add('hidden');
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('editRekamMedisForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    
    if (form && submitBtn && submitText) {
        form.addEventListener('submit', function(event) {
            const siswaSelect = document.getElementById('Id_Siswa');
            const dokterSelect = document.getElementById('Id_Dokter');
            const petugasSelect = document.getElementById('NIP');
            const keluhanTextarea = document.getElementById('Keluhan_Utama');
            
            let isValid = true;
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            
            // Check required fields
            if (siswaSelect && !siswaSelect.value) {
                markInvalid(siswaSelect, 'Siswa harus dipilih');
                isValid = false;
            } else if (siswaSelect) {
                markValid(siswaSelect);
            }
            
            if (dokterSelect && !dokterSelect.value) {
                markInvalid(dokterSelect, 'Dokter harus dipilih');
                isValid = false;
            } else if (dokterSelect) {
                markValid(dokterSelect);
            }
            
            if (petugasSelect && !petugasSelect.value) {
                markInvalid(petugasSelect, 'Petugas UKS harus dipilih');
                isValid = false;
            } else if (petugasSelect) {
                markValid(petugasSelect);
            }
            
            if (keluhanTextarea && !keluhanTextarea.value.trim()) {
                markInvalid(keluhanTextarea, 'Keluhan Utama harus diisi');
                isValid = false;
            } else if (keluhanTextarea) {
                markValid(keluhanTextarea);
            }
            
            if (!isValid) {
                event.preventDefault();
                submitBtn.disabled = false;
                submitText.innerHTML = '<i class="fas fa-save mr-2"></i>Update Rekam Medis';
                
                // Scroll to first error
                const firstError = document.querySelector('.text-red-600');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
    
    function markInvalid(element, message) {
        element.classList.add('border-red-500');
        element.classList.remove('border-gray-300');
        
        // Add error message if not exists
        const parent = element.parentNode.parentNode;
        let errorElement = parent.querySelector('.text-red-600');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'mt-1 text-sm text-red-600';
            parent.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }
    
    function markValid(element) {
        element.classList.remove('border-red-500');
        element.classList.add('border-gray-300');
        
        // Remove error message if exists
        const parent = element.parentNode.parentNode;
        const errorElement = parent.querySelector('.text-red-600');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    // Log user level for debugging
    console.log('User Level:', '{{ $userLevel }}');
    console.log('Access Level:', '{{ $isAdmin ? "Admin (Full CRUD)" : ($isPetugas ? "Petugas (CRU)" : "Unknown") }}');
    console.log('Editing Record:', '{{ $rekamMedis->No_Rekam_Medis ?? "N/A" }}');
});
</script>
@endpush
@endsection