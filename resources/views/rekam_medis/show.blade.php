{{-- File: resources/views/rekam_medis/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('page_title', 'Detail Rekam Medis')

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
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'rekam_medis' : ($isPetugas ? 'petugas.rekam_medis' : 'dokter.rekam_medis');
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';

    
    // Routes yang hanya untuk admin dan petugas
    $editRoute = null;
    $destroyRoute = null;
    
    if ($isAdmin) {
        $editRoute = 'rekam_medis.edit';
        $destroyRoute = 'rekam_medis.destroy';
    } elseif ($isPetugas) {
        $editRoute = 'petugas.rekam_medis.edit';
        // Petugas tidak bisa delete
    }
    // Dokter tidak bisa edit atau delete
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-7xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-file-medical-alt text-green-500 mr-3 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Rekam Medis</h2>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-600 mr-2">Nomor:</span>
                        <span class="bg-green-100 text-green-800 text-sm font-bold py-1 px-3 rounded-full">
                            {{ $rekamMedis->No_Rekam_Medis }}
                        </span>
                        @if($isDokter)
                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                <i class="fas fa-stethoscope mr-1"></i>Akses Dokter (Read Only)
                            </span>
                        @elseif($isPetugas)
                            <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-tie mr-1"></i>Akses Petugas (CRU)
                            </span>
                        @elseif($isAdmin)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-shield mr-1"></i>Akses Admin (Full CRUD)
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">

                
                {{-- Tombol Edit - hanya admin dan petugas --}}
                @if($editRoute)
                    <a href="{{ route($editRoute, $rekamMedis->No_Rekam_Medis) }}" 
                       class="bg-orange-500 text-white hover:bg-orange-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit{{ $isPetugas ? ' (Petugas)' : '' }}
                    </a>
                @endif
                
                <a href="{{ route($indexRoute) }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
            <!-- Access Level Info -->
            @if($isDokter)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-green-800 mb-1">Akses Dokter</h3>
                        <p class="text-sm text-green-700">
                            Anda dapat melihat dan mencetak rekam medis, namun <span class="font-semibold text-red-600">tidak dapat mengedit atau menghapus</span> data. 
                            Akses ini memberikan informasi lengkap untuk keperluan konsultasi medis.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isPetugas)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-yellow-800 mb-1">Akses Petugas UKS</h3>
                        <p class="text-sm text-yellow-700">
                            Anda dapat melihat, mengedit, dan mencetak rekam medis untuk keperluan pelayanan kesehatan siswa. 
                            <span class="font-semibold text-red-600">Akses untuk menghapus data hanya tersedia untuk Administrator.</span>
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isAdmin)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-blue-800 mb-1">Akses Administrator</h3>
                        <p class="text-sm text-blue-700">
                            Anda memiliki akses penuh untuk melihat, mengedit, menghapus, dan mencetak rekam medis. 
                            Gunakan akses ini dengan bijak untuk menjaga integritas data sistem.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Alert Messages -->
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


            <!-- Grid Informasi Utama -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <!-- Data Siswa -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm lg:col-span-2">
                    <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                        <i class="fas fa-user-graduate text-blue-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Data Siswa</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-id-card text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">ID Siswa</p>
                                    <p class="font-medium text-gray-800">{{ $rekamMedis->Id_Siswa }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-user text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Nama Siswa</p>
                                    <p class="font-medium text-gray-800">{{ $rekamMedis->siswa->nama_siswa ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if($rekamMedis->siswa && $rekamMedis->siswa->detailSiswa && $rekamMedis->siswa->detailSiswa->kelas)
                            <div class="flex items-start">
                                <i class="fas fa-graduation-cap text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Kelas</p>
                                    <p class="font-medium text-gray-800">{{ $rekamMedis->siswa->detailSiswa->kelas->Nama_Kelas }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-venus-mars text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Jenis Kelamin</p>
                                    <p class="font-medium text-gray-800">
                                        @if(isset($rekamMedis->siswa->jenis_kelamin))
                                            @if($rekamMedis->siswa->jenis_kelamin == 'L')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                    <i class="fas fa-male mr-1"></i>Laki-laki
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-pink-100 text-pink-800">
                                                    <i class="fas fa-female mr-1"></i>Perempuan
                                                </span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-birthday-cake text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Lahir</p>
                                    <p class="font-medium text-gray-800">
                                        @if(isset($rekamMedis->siswa->tanggal_lahir))
                                            {{ \Carbon\Carbon::parse($rekamMedis->siswa->tanggal_lahir)->format('d M Y') }}
                                            <span class="text-xs text-gray-500 block">
                                                ({{ \Carbon\Carbon::parse($rekamMedis->siswa->tanggal_lahir)->age }} tahun)
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Dokter -->
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                        <i class="fas fa-user-md text-green-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Dokter</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-id-badge text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">ID Dokter</p>
                                <p class="font-medium text-gray-800">{{ $rekamMedis->Id_Dokter ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-stethoscope text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Nama Dokter</p>
                                <p class="font-medium text-gray-800">{{ $rekamMedis->dokter->Nama_Dokter ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-medal text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Spesialisasi</p>
                                <p class="font-medium text-gray-800">{{ $rekamMedis->dokter->Spesialisasi ?? 'Umum' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Waktu Pemeriksaan -->
                <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-purple-200 pb-2">
                        <i class="fas fa-clock text-purple-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Waktu</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal</p>
                                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-clock text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Waktu</p>
                                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-calendar-day text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Hari</p>
                                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->locale('id')->translatedFormat('l') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-history text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Relatif</p>
                                <p class="font-medium text-gray-800 text-xs">{{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Petugas UKS Info (jika ada) -->
            @if($rekamMedis->petugasUKS)
            <div class="bg-orange-50 border border-orange-100 rounded-lg p-5 shadow-sm mb-6">
                <div class="flex items-center mb-3 border-b border-orange-200 pb-2">
                    <i class="fas fa-user-nurse text-orange-500 mr-2 text-lg"></i>
                    <h3 class="text-lg font-medium text-gray-800">Petugas UKS</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-start">
                        <i class="fas fa-id-badge text-orange-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">NIP</p>
                            <p class="font-medium text-gray-800">{{ $rekamMedis->NIP }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-user text-orange-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Nama Petugas</p>
                            <p class="font-medium text-gray-800">{{ $rekamMedis->petugasUKS->nama_petugas_uks }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-briefcase text-orange-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="font-medium text-gray-800">Petugas UKS Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Data Rekam Medis -->
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-clipboard-list text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Data Rekam Medis</h3>
                </div>
                
                <!-- Keluhan Utama -->
                <div class="bg-red-50 border border-red-100 rounded-lg p-5 shadow-sm mb-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <h4 class="font-semibold text-red-800">Keluhan Utama</h4>
                    </div>
                    <div class="bg-white p-4 rounded-md border border-red-200">
                        <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $rekamMedis->Keluhan_Utama ?? 'Tidak ada data keluhan utama' }}</p>
                    </div>
                </div>
                
                <!-- Riwayat Penyakit Sekarang -->
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-5 shadow-sm mb-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-clipboard-list text-orange-500 mr-2"></i>
                        <h4 class="font-semibold text-orange-800">Riwayat Penyakit Sekarang</h4>
                    </div>
                    <div class="bg-white p-4 rounded-md border border-orange-200">
                        <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $rekamMedis->Riwayat_Penyakit_Sekarang ?? 'Tidak ada data riwayat penyakit sekarang' }}</p>
                    </div>
                </div>
                
                <!-- Grid Riwayat -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <!-- Riwayat Penyakit Dahulu -->
                    <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-history text-yellow-600 mr-2"></i>
                            <h4 class="font-semibold text-yellow-800">Riwayat Penyakit Dahulu</h4>
                        </div>
                        <div class="bg-white p-4 rounded-md border border-yellow-200">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $rekamMedis->Riwayat_Penyakit_Dahulu ?? 'Tidak ada data riwayat penyakit dahulu' }}</p>
                        </div>
                    </div>
                    
                    <!-- Riwayat Imunisasi -->
                    <div class="bg-teal-50 border border-teal-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-syringe text-teal-600 mr-2"></i>
                            <h4 class="font-semibold text-teal-800">Riwayat Imunisasi</h4>
                        </div>
                        <div class="bg-white p-4 rounded-md border border-teal-200">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $rekamMedis->Riwayat_Imunisasi ?? 'Tidak ada data riwayat imunisasi' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Grid Keluarga -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Riwayat Penyakit Keluarga -->
                    <div class="bg-pink-50 border border-pink-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-users text-pink-600 mr-2"></i>
                            <h4 class="font-semibold text-pink-800">Riwayat Penyakit Keluarga</h4>
                        </div>
                        <div class="bg-white p-4 rounded-md border border-pink-200">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $rekamMedis->Riwayat_Penyakit_Keluarga ?? 'Tidak ada data riwayat penyakit keluarga' }}</p>
                        </div>
                    </div>
                    
                    <!-- Silsilah Keluarga -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-sitemap text-indigo-600 mr-2"></i>
                            <h4 class="font-semibold text-indigo-800">Silsilah Keluarga</h4>
                        </div>
                        <div class="bg-white p-4 rounded-md border border-indigo-200">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $rekamMedis->Silsilah_Keluarga ?? 'Tidak ada data silsilah keluarga' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pemeriksaan -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4 border-b pb-2">
                    <div class="flex items-center">
                        <i class="fas fa-notes-medical text-gray-600 mr-2 text-lg"></i>
                        <h3 class="text-xl font-semibold text-gray-800">Riwayat Pemeriksaan Siswa</h3>
                    </div>
                    <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                        {{ isset($detailPemeriksaan) ? $detailPemeriksaan->count() : 0 }} Pemeriksaan
                    </span>
                </div>
                
                @if(isset($detailPemeriksaan) && $detailPemeriksaan->count() > 0)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            Tanggal & Waktu
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-user-md mr-1"></i>
                                            Pemeriksa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-clipboard-check mr-1"></i>
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-cogs mr-1"></i>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($detailPemeriksaan as $index => $dp)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-blue-600">{{ $index + 1 }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ \Carbon\Carbon::parse($dp->tanggal_jam)->format('d M Y') }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ \Carbon\Carbon::parse($dp->tanggal_jam)->format('H:i') }} WIB
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($dp->id_dokter)
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                                <i class="fas fa-user-md text-green-600 text-xs"></i>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $dp->dokter->Nama_Dokter ?? 'N/A' }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">Dokter</div>
                                                        </div>
                                                    @elseif($dp->nip)
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                                <i class="fas fa-user-nurse text-purple-600 text-xs"></i>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $dp->petugasUks->nama_petugas_uks ?? 'N/A' }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">Petugas UKS</div>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-8 w-8">
                                                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                                    <i class="fas fa-question text-gray-400 text-xs"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm text-gray-500">N/A</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($dp->status_pemeriksaan == 'lengkap')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Lengkap
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Belum Lengkap
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($isAdmin)
                                                    <a href="{{ route('detail_pemeriksaan.show', $dp->id_detprx) }}" 
                                                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" 
                                                       title="Lihat Detail Pemeriksaan">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        Detail
                                                    </a>
                                                @elseif($isPetugas)
                                                    <a href="{{ route('petugas.detail_pemeriksaan.show', $dp->id_detprx) }}" 
                                                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" 
                                                       title="Lihat Detail Pemeriksaan">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        Detail
                                                    </a>
                                                @elseif($isDokter)
                                                    <a href="{{ route('dokter.detail_pemeriksaan.show', $dp->id_detprx) }}" 
                                                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" 
                                                       title="Lihat Detail Pemeriksaan (Read Only)">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        Detail
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                            <i class="fas fa-notes-medical text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-600 mb-2">Belum Ada Pemeriksaan</h3>
                        <p class="text-gray-500">Belum ada data pemeriksaan untuk siswa ini</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route($indexRoute) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-gray-500"></i>
                    Kembali ke Daftar
                </a>
                
                <div class="flex space-x-2">

                    {{-- Tombol Edit - hanya admin dan petugas --}}
                    @if($editRoute)
                        <a href="{{ route($editRoute, $rekamMedis->No_Rekam_Medis) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Rekam Medis
                        </a>
                    @endif
                    
                    {{-- Tombol Hapus - hanya admin --}}
                    @if($destroyRoute)
                        <form action="{{ route($destroyRoute, $rekamMedis->No_Rekam_Medis) }}" method="POST" class="inline-block" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Rekam Medis
                            </button>
                        </form>
                    @endif
                </div>
            </div>
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
    
    // Log user level for debugging
    console.log('User Level:', '{{ $userLevel }}');
    console.log('View Access:', '{{ $isAdmin ? "Admin (Full)" : ($isPetugas ? "Petugas (CRU)" : ($isDokter ? "Dokter (Read Only)" : "Unknown")) }}');
    console.log('Record ID:', '{{ $rekamMedis->No_Rekam_Medis }}');
});

function confirmDelete() {
    const rekamMedisNo = '{{ $rekamMedis->No_Rekam_Medis }}';
    const siswaName = '{{ $rekamMedis->siswa->nama_siswa ?? "Tidak Diketahui" }}';
    
    if (confirm(`PERINGATAN!\n\nApakah Anda yakin ingin menghapus rekam medis ini?\n\nNomor: ${rekamMedisNo}\nSiswa: ${siswaName}\n\nTindakan ini akan menghapus:\n- Data rekam medis lengkap\n- Semua data pemeriksaan terkait\n- Riwayat medis siswa\n\nData yang dihapus TIDAK DAPAT dikembalikan!\n\nKlik OK untuk melanjutkan atau Cancel untuk membatalkan.`)) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
@endsection