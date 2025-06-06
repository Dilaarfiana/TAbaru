{{-- File: resources/views/resep/edit.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: REDIRECT, ORANG TUA: REDIRECT --}}
@extends('layouts.app')

@section('page_title', 'Edit Resep Obat')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Redirect dokter dan orang tua karena tidak boleh edit
    if ($isDokter) {
        header('Location: ' . route('dokter.resep.show', $resep->Id_Resep));
        exit;
    }
    
    if ($isOrangTua) {
        header('Location: ' . route('orangtua.riwayat.resep'));
        exit;
    }
    
    // Check if user has permission to edit
    if (!in_array($userLevel, ['admin', 'petugas'])) {
        header('Location: ' . route('dashboard'));
        exit;
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'resep' : 'petugas.resep';
    $indexRoute = $baseRoute . '.index';
    $createRoute = $baseRoute . '.create';
    $storeRoute = $baseRoute . '.store';
    $showRoute = $baseRoute . '.show';
    $editRoute = $baseRoute . '.edit';
    $updateRoute = $baseRoute . '.update';
    $viewDokumenRoute = $baseRoute . '.view-dokumen';
    $downloadDokumenRoute = $baseRoute . '.download-dokumen';
    
    // Admin-only routes
    if ($isAdmin) {
        $destroyRoute = 'resep.destroy';
        $exportRoute = 'resep.export';
        $cetakRoute = 'resep.cetak';
    } else {
        $cetakRoute = 'petugas.resep.cetak';
    }
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-6xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-edit text-orange-500 mr-3 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Edit Resep Obat</h2>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-600 mr-2">Perubahan Data Resep</span>
                        @if($isPetugas)
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                            </span>
                        @elseif($isAdmin)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-shield mr-1"></i>Akses Admin
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="bg-orange-100 text-orange-800 text-sm font-bold py-1 px-3 rounded-full flex items-center">
                    <i class="fas fa-tag mr-1"></i>
                    {{ $resep->Id_Resep }}
                </span>
                <a href="{{ route($indexRoute) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
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
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
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
                        <p class="text-sm text-yellow-700">{!! session('warning') !!}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-yellow-500 hover:text-yellow-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-green-500 hover:text-green-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            <!-- Info Box dengan Hak Akses -->
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-orange-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-orange-800 mb-1">Edit Resep Obat</h3>
                        <p class="text-sm text-orange-700 mb-2">
                            Anda sedang mengedit resep obat dengan ID: <span class="font-mono font-medium bg-white px-2 py-1 rounded border">{{ $resep->Id_Resep }}</span>. Perubahan akan disimpan setelah Anda klik tombol Perbarui.
                        </p>
                        
                        <!-- Role Information dengan warna yang berbeda -->
                        <div class="mt-2 p-2 {{ $isAdmin ? 'bg-blue-100 border-blue-300' : 'bg-yellow-100 border-yellow-300' }} border rounded">
                            <p class="text-sm {{ $isAdmin ? 'text-blue-800' : 'text-yellow-800' }}">
                                <i class="fas fa-user-tag mr-1"></i>
                                <strong>Akses Anda:</strong> 
                                @if($isAdmin)
                                    Administrator - Dapat mengelola semua data resep obat termasuk menghapus dan export
                                @elseif($isPetugas)
                                    Petugas UKS - Dapat menambah, melihat dan mengedit data resep obat (tidak dapat menghapus)
                                @endif
                            </p>
                        </div>

                        <!-- Batasan Akses untuk Petugas -->
                        @if($isPetugas)
                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-xs text-yellow-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Catatan:</strong> Sebagai petugas UKS, Anda dapat mengedit resep namun tidak dapat menghapus data.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form hanya untuk Admin dan Petugas -->
            @if($isAdmin || $isPetugas)
            <form action="{{ route($updateRoute, $resep->Id_Resep) }}" method="POST" enctype="multipart/form-data" id="resepEditForm">
                @csrf
                @method('PUT')
                
                <!-- Informasi Tanggal -->
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Tanggal Resep</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal Resep -->
                        <div>
                            <label for="Tanggal_Resep" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-calendar text-blue-500 mr-1"></i>
                                Tanggal Resep <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="date" 
                                    id="Tanggal_Resep" 
                                    name="Tanggal_Resep" 
                                    value="{{ old('Tanggal_Resep', $resep->Tanggal_Resep) }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Tanggal penerbitan resep obat</p>
                            @error('Tanggal_Resep')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview Tanggal -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle text-gray-500 mr-1"></i>
                                Preview Tanggal
                            </h4>
                            <div id="datePreview" class="text-sm text-gray-600">
                                Pilih tanggal untuk melihat preview
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid untuk Siswa dan Dokter -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Kolom Kiri: Siswa -->
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
                                    @if(isset($siswaList))
                                        @foreach($siswaList as $siswa)
                                            <option value="{{ $siswa->id_siswa }}" {{ (old('Id_Siswa', $resep->Id_Siswa) == $siswa->id_siswa) ? 'selected' : '' }}>
                                                {{ $siswa->id_siswa }} - {{ $siswa->nama_siswa }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih siswa yang akan diberikan resep</p>
                            @error('Id_Siswa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Siswa Info Preview -->
                        <div id="siswaInfo" class="mt-4 p-3 bg-white rounded-md border border-green-200 hidden">
                            <h4 class="text-sm font-medium text-green-700 mb-2">
                                <i class="fas fa-user text-green-600 mr-1"></i>
                                Informasi Siswa
                            </h4>
                            <div id="siswaDetails" class="text-sm text-gray-600">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Dokter -->
                    <div class="bg-teal-50 border border-teal-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-teal-200 pb-2">
                            <i class="fas fa-user-md text-teal-500 mr-2"></i>
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
                                    class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 appearance-none">
                                    <option value="">-- Pilih Dokter --</option>
                                    @if(isset($dokterList))
                                        @foreach($dokterList as $dokter)
                                            <option value="{{ $dokter->Id_Dokter }}" {{ (old('Id_Dokter', $resep->Id_Dokter) == $dokter->Id_Dokter) ? 'selected' : '' }}>
                                                {{ $dokter->Nama_Dokter }} {{ $dokter->Spesialisasi ? '- '.$dokter->Spesialisasi : '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih dokter yang mengeluarkan resep</p>
                            @error('Id_Dokter')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dokter Info Preview -->
                        <div id="dokterInfo" class="mt-4 p-3 bg-white rounded-md border border-teal-200 hidden">
                            <h4 class="text-sm font-medium text-teal-700 mb-2">
                                <i class="fas fa-user-md text-teal-600 mr-1"></i>
                                Informasi Dokter
                            </h4>
                            <div id="dokterDetails" class="text-sm text-gray-600">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Obat -->
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                        <i class="fas fa-pills text-green-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Obat</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Nama Obat -->
                        <div>
                            <label for="Nama_Obat" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-capsules text-green-500 mr-1"></i>
                                Nama Obat <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="Nama_Obat" 
                                       name="Nama_Obat" 
                                       value="{{ old('Nama_Obat', $resep->Nama_Obat) }}" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                                       placeholder="Masukkan nama obat yang diresepkan..." 
                                       maxlength="30">
                                <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                    <span id="namaObatCharCount">0</span>/30
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Masukkan nama obat yang akan diresepkan untuk siswa</p>
                            @error('Nama_Obat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Grid untuk Dosis dan Durasi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Dosis -->
                            <div>
                                <label for="Dosis" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-syringe text-green-500 mr-1"></i>
                                    Dosis <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="Dosis" 
                                           name="Dosis" 
                                           value="{{ old('Dosis', $resep->Dosis) }}" 
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                                           placeholder="Contoh: 3x1 tablet sehari" 
                                           maxlength="30">
                                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                        <span id="dosisCharCount">0</span>/30
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Aturan pakai dan dosis penggunaan</p>
                                @error('Dosis')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Durasi -->
                            <div>
                                <label for="Durasi" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-clock text-green-500 mr-1"></i>
                                    Durasi <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="Durasi" 
                                           name="Durasi" 
                                           value="{{ old('Durasi', $resep->Durasi) }}" 
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                                           placeholder="Contoh: 5 hari" 
                                           maxlength="30">
                                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                        <span id="durasiCharCount">0</span>/30
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Lama penggunaan obat</p>
                                @error('Durasi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Template Dosis -->
                        <div class="mt-4 p-3 bg-white rounded-md border border-green-200">
                            <h4 class="text-sm font-medium text-green-700 mb-2">
                                <i class="fas fa-lightbulb text-green-600 mr-1"></i>
                                Template Dosis Umum
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <button type="button" class="dosis-template-btn text-left p-2 text-xs bg-gray-50 hover:bg-green-100 rounded border transition-colors" 
                                    data-dosis="1x1 tablet sehari" data-durasi="7 hari">
                                    <i class="fas fa-pills text-green-500 mr-1"></i> 1x sehari
                                </button>
                                <button type="button" class="dosis-template-btn text-left p-2 text-xs bg-gray-50 hover:bg-green-100 rounded border transition-colors"
                                    data-dosis="2x1 tablet sehari" data-durasi="5 hari">
                                    <i class="fas fa-pills text-green-500 mr-1"></i> 2x sehari
                                </button>
                                <button type="button" class="dosis-template-btn text-left p-2 text-xs bg-gray-50 hover:bg-green-100 rounded border transition-colors"
                                    data-dosis="3x1 tablet sehari" data-durasi="3 hari">
                                    <i class="fas fa-pills text-green-500 mr-1"></i> 3x sehari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Dokumen -->
                <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b border-purple-200 pb-2">
                        <i class="fas fa-file-medical text-purple-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Dokumen Resep</h3>
                        <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Opsional</span>
                    </div>
                    
                    <!-- Existing Document Display -->
                    @if($resep->Dokumen)
                    <div class="mb-4 p-4 bg-blue-50 rounded-md border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-file-medical text-blue-500 mr-3 text-lg"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Dokumen Tersedia</p>
                                    <p class="text-xs text-gray-500">Dokumen resep yang sudah diunggah sebelumnya</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route($viewDokumenRoute, $resep->Id_Resep) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    Lihat
                                </a>
                                <a href="{{ route($downloadDokumenRoute, $resep->Id_Resep) }}" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- File Upload Area -->
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-purple-300 border-dashed rounded-md hover:border-purple-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-purple-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="Dokumen" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-upload mr-1"></i>
                                        Upload dokumen {{ $resep->Dokumen ? 'baru' : '' }}
                                    </span>
                                    <input id="Dokumen" name="Dokumen" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-purple-600">
                                <i class="fas fa-info-circle mr-1"></i>
                                PDF, JPG, JPEG, PNG hingga 2MB
                            </p>
                        </div>
                    </div>
                    
                    <!-- File Preview -->
                    <div id="file-preview" class="mt-4 hidden">
                        <div class="bg-white p-3 rounded-md border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file text-purple-500 mr-2"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">File yang dipilih:</p>
                                        <p id="file-name" class="text-sm text-gray-600"></p>
                                    </div>
                                </div>
                                <button type="button" id="remove-file" class="text-red-500 hover:text-red-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    @error('Dokumen')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                        @if($resep->Dokumen)
                            Jika Anda mengunggah file baru, dokumen yang lama akan tergantikan.
                        @else
                            Unggah scan dokumen resep asli untuk dokumentasi digital (opsional)
                        @endif
                    </p>
                </div>
                
                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.location.href='{{ route($indexRoute) }}'" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-times mr-2 text-gray-500"></i>
                        Batal
                    </button>
                    <button type="submit" 
 class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center shadow-sm"                        id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Perbarui Resep</span>
                    </button>
                </div>
            </form>

            @else
            <!-- Pesan Akses Terbatas (untuk role yang tidak boleh edit) -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                <i class="fas fa-lock text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-600 mb-2">Akses Terbatas</h3>
                <p class="text-gray-500 mb-4">Anda tidak memiliki izin untuk mengedit data resep obat.</p>
                
                @if($isDokter)
                    <p class="text-sm text-green-600 mb-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Sebagai dokter, Anda hanya dapat melihat data resep. Untuk melihat detail resep ini, gunakan tombol di bawah.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="{{ route('dokter.resep.show', $resep->Id_Resep) }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail Resep
                        </a>
                        <a href="{{ route('dokter.resep.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                        </a>
                    </div>
                @elseif($isOrangTua)
                    <p class="text-sm text-blue-600 mb-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Sebagai orang tua, Anda dapat melihat riwayat resep anak Anda di halaman khusus.
                    </p>
                    <a href="{{ route('orangtua.riwayat.resep') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-arrow-left mr-2"></i> Ke Riwayat Resep Anak
                    </a>
                @else
                    <a href="{{ route($indexRoute) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                    </a>
                @endif
            </div>
            @endif
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
                alert.parentElement.style.opacity = '0';
                setTimeout(function() {
                    alert.parentElement.style.display = 'none';
                }, 500);
            }
        }, 5000);
        if (alert.parentElement) {
            alert.parentElement.style.transition = 'opacity 0.5s ease-in-out';
        }
    });

    // Only run form-related scripts if user has edit access
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'admin' || userLevel === 'petugas') {
        
        // Date validation and preview
        const dateInput = document.getElementById('Tanggal_Resep');
        if (dateInput) {
            const today = new Date();
            const maxDate = new Date();
            maxDate.setDate(today.getDate() + 7); // Maksimal 7 hari ke depan
            const minDate = new Date();
            minDate.setFullYear(today.getFullYear() - 1); // 1 tahun ke belakang
            
            dateInput.max = maxDate.toISOString().split('T')[0];
            dateInput.min = minDate.toISOString().split('T')[0];
            
            // Update date preview
            dateInput.addEventListener('change', updateDatePreview);
            updateDatePreview(); // Initial update
        }
        
        function updateDatePreview() {
            const datePreview = document.getElementById('datePreview');
            if (dateInput.value && datePreview) {
                const date = new Date(dateInput.value);
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric'
                };
                datePreview.textContent = date.toLocaleDateString('id-ID', options);
            }
        }

        // Character counters
        const namaObatInput = document.getElementById('Nama_Obat');
        const namaObatCharCount = document.getElementById('namaObatCharCount');
        const dosisInput = document.getElementById('Dosis');
        const dosisCharCount = document.getElementById('dosisCharCount');
        const durasiInput = document.getElementById('Durasi');
        const durasiCharCount = document.getElementById('durasiCharCount');
        
        if (namaObatInput && namaObatCharCount) {
            namaObatInput.addEventListener('input', function() {
                namaObatCharCount.textContent = this.value.length;
            });
            // Initialize
            namaObatCharCount.textContent = namaObatInput.value.length;
        }
        
        if (dosisInput && dosisCharCount) {
            dosisInput.addEventListener('input', function() {
                dosisCharCount.textContent = this.value.length;
            });
            // Initialize
            dosisCharCount.textContent = dosisInput.value.length;
        }
        
        if (durasiInput && durasiCharCount) {
            durasiInput.addEventListener('input', function() {
                durasiCharCount.textContent = this.value.length;
            });
            // Initialize
            durasiCharCount.textContent = durasiInput.value.length;
        }

        // Template buttons for dosis
        const dosisTemplateBtns = document.querySelectorAll('.dosis-template-btn');
        dosisTemplateBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const dosis = this.dataset.dosis;
                const durasi = this.dataset.durasi;
                
                if (dosisInput && dosis) {
                    dosisInput.value = dosis;
                    dosisInput.focus();
                    if (dosisCharCount) {
                        dosisCharCount.textContent = dosisInput.value.length;
                    }
                }
                
                if (durasiInput && durasi) {
                    durasiInput.value = durasi;
                    if (durasiCharCount) {
                        durasiCharCount.textContent = durasiInput.value.length;
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
            
            // Initialize if siswa is already selected
            if (siswaSelect.value) {
                siswaSelect.dispatchEvent(new Event('change'));
            }
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
                    const parts = text.split(' - ');
                    const nama = parts[0];
                    const spesialisasi = parts[1] || 'Tidak ada spesialisasi';
                    
                    dokterDetails.innerHTML = `
                        <div class="grid grid-cols-1 gap-1">
                            <div><strong>Nama:</strong> ${nama}</div>
                            <div><strong>Spesialisasi:</strong> ${spesialisasi}</div>
                        </div>
                    `;
                    dokterInfo.classList.remove('hidden');
                } else {
                    dokterInfo.classList.add('hidden');
                }
            });
            
            // Initialize if dokter is already selected
            if (dokterSelect.value) {
                dokterSelect.dispatchEvent(new Event('change'));
            }
        }
        
        // File upload handling
        const fileInput = document.getElementById('Dokumen');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const removeFileBtn = document.getElementById('remove-file');
        
        if (fileInput && filePreview && fileName && removeFileBtn) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    
                    // Validasi ukuran file (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 2MB.');
                        this.value = '';
                        filePreview.classList.add('hidden');
                        return;
                    }
                    
                    // Tampilkan nama file
                    fileName.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                    filePreview.classList.remove('hidden');
                } else {
                    filePreview.classList.add('hidden');
                }
            });
            
            removeFileBtn.addEventListener('click', function() {
                fileInput.value = '';
                filePreview.classList.add('hidden');
            });
        }
        
        // Form validation
        const form = document.getElementById('resepEditForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        if (form && submitBtn && submitText) {
            form.addEventListener('submit', function(event) {
                const siswaSelect = document.getElementById('Id_Siswa');
                const dokterSelect = document.getElementById('Id_Dokter');
                const namaObatInput = document.getElementById('Nama_Obat');
                const dosisInput = document.getElementById('Dosis');
                const durasiInput = document.getElementById('Durasi');
                
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
                
                if (namaObatInput && !namaObatInput.value.trim()) {
                    markInvalid(namaObatInput, 'Nama Obat harus diisi');
                    isValid = false;
                } else if (namaObatInput) {
                    markValid(namaObatInput);
                }
                
                if (dosisInput && !dosisInput.value.trim()) {
                    markInvalid(dosisInput, 'Dosis harus diisi');
                    isValid = false;
                } else if (dosisInput) {
                    markValid(dosisInput);
                }
                
                if (durasiInput && !durasiInput.value.trim()) {
                    markInvalid(durasiInput, 'Durasi harus diisi');
                    isValid = false;
                } else if (durasiInput) {
                    markValid(durasiInput);
                }
                
                if (!isValid) {
                    event.preventDefault();
                    submitBtn.disabled = false;
                    submitText.innerHTML = '<i class="fas fa-save mr-2"></i>Perbarui Resep';
                    
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
    }
});
</script>
@endpush
@endsection