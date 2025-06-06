{{-- File: resources/views/resep/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: REDIRECT --}}
@extends('layouts.app')

@section('page_title', 'Detail Resep Obat')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Redirect orang tua ke halaman khusus mereka
    if ($isOrangTua) {
        header('Location: ' . route('orangtua.riwayat.resep'));
        exit;
    }
    
    // Check if user has permission to view
    if (!in_array($userLevel, ['admin', 'petugas', 'dokter'])) {
        header('Location: ' . route('dashboard'));
        exit;
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'resep' : ($isPetugas ? 'petugas.resep' : 'dokter.resep');
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';
    $cetakRoute = $baseRoute . '.cetak';
    $viewDokumenRoute = $baseRoute . '.view-dokumen';
    $downloadDokumenRoute = $baseRoute . '.download-dokumen';
    
    // Routes yang hanya untuk admin dan petugas
    if ($isAdmin) {
        $createRoute = 'resep.create';
        $editRoute = 'resep.edit';
        $destroyRoute = 'resep.destroy';
        $exportRoute = 'resep.export';
    } elseif ($isPetugas) {
        $createRoute = 'petugas.resep.create';
        $editRoute = 'petugas.resep.edit';
    } else {
        // Dokter hanya read-only
        $createRoute = null;
        $editRoute = null;
    }
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="border-b border-gray-200 bg-white px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-prescription text-2xl text-indigo-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detail Resep Obat</h2>
                        <div class="flex items-center mt-1">
                            <span class="bg-indigo-100 text-indigo-800 text-sm font-bold py-1 px-3 rounded-full">
                                {{ $resep->Id_Resep }}
                            </span>
                            @if($isDokter)
                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-stethoscope mr-1"></i>Akses Dokter
                                </span>
                            @elseif($isPetugas)
                                <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                                </span>
                            @elseif($isAdmin)
                                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    <i class="fas fa-user-shield mr-1"></i>Akses Admin
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
                    {{-- Tombol Edit - hanya admin dan petugas --}}
                    @if($isAdmin || $isPetugas)
                        <a href="{{ route($editRoute, $resep->Id_Resep) }}" 
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            <i class="fas fa-edit mr-1"></i> Edit Resep
                        </a>
                    @endif
                    
                    {{-- Tombol Lihat Dokumen - semua role --}}
                    @if($resep->Dokumen)
                        <a href="{{ route($viewDokumenRoute, $resep->Id_Resep) }}" 
                           target="_blank" 
                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <i class="fas fa-file-medical mr-1"></i> Lihat Dokumen
                        </a>
                    @endif
                    
                    {{-- Tombol Cetak - semua role --}}
                    <a href="{{ route($cetakRoute, $resep->Id_Resep) }}" 
                       target="_blank" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-print mr-1"></i> Cetak Resep
                    </a>
                    
                    {{-- Tombol Hapus - hanya admin --}}
                    @if($isAdmin)
                        <form action="{{ route($destroyRoute, $resep->Id_Resep) }}" method="POST" class="inline-block" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    onclick="confirmDelete()" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                    title="Hapus Resep (Hanya Admin)">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route($indexRoute) }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-4 flex items-center justify-between rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-green-500 hover:text-green-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-4 flex items-center justify-between rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-red-500 hover:text-red-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            <!-- Info Banner dengan Role Specific Information -->
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mx-6 mt-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-indigo-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-sm font-medium text-indigo-800 mb-1">Informasi Resep Obat</h3>
                        <p class="text-sm text-indigo-700 mb-2">
                            Menampilkan detail lengkap resep obat untuk siswa termasuk informasi dokter dan riwayat pengobatan.
                        </p>
                        
                        <!-- Role Information dengan warna yang berbeda -->
                        <div class="mt-2 p-2 {{ $isAdmin ? 'bg-blue-100 border-blue-300' : ($isPetugas ? 'bg-yellow-100 border-yellow-300' : 'bg-green-100 border-green-300') }} border rounded">
                            <p class="text-sm {{ $isAdmin ? 'text-blue-800' : ($isPetugas ? 'text-yellow-800' : 'text-green-800') }}">
                                <i class="fas fa-user-tag mr-1"></i>
                                <strong>Akses Anda:</strong> 
                                @if($isAdmin)
                                    Administrator - Dapat melihat, mengedit, menghapus, dan export resep obat
                                @elseif($isPetugas)
                                    Petugas UKS - Dapat melihat dan mengedit resep obat (tidak dapat menghapus)
                                @elseif($isDokter)
                                    Dokter - Dapat melihat semua resep obat (hanya baca, tidak dapat mengedit atau menghapus)
                                @else
                                    Guest - Hanya dapat melihat resep obat
                                @endif
                            </p>
                        </div>

                        <!-- Access Limitations Notice untuk non-admin -->
                        @if($isPetugas)
                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-xs text-yellow-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Catatan:</strong> Sebagai petugas UKS, Anda dapat mengedit resep namun tidak dapat menghapus data.
                            </p>
                        </div>
                        @elseif($isDokter)
                        <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded">
                            <p class="text-xs text-green-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Catatan:</strong> Sebagai dokter, Anda memiliki akses baca untuk melihat semua resep namun tidak dapat mengubah data.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Timestamp Information -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>
                            <span class="text-sm">
                                Tanggal Resep: 
                                <strong>{{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->locale('id')->translatedFormat('d F Y') }}</strong>
                            </span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-calendar-week text-blue-500 mr-2"></i>
                            <span class="text-sm">
                                Hari: <strong>{{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->locale('id')->translatedFormat('l') }}</strong>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center mt-2 md:mt-0">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-clock text-gray-500 mr-2"></i>
                            <span class="text-xs">
                                Dibuat: {{ \Carbon\Carbon::parse($resep->created_at ?? $resep->dibuat_pada ?? $resep->Tanggal_Resep)->format('d/m/Y H:i') }}
                                @if(isset($resep->updated_at) && $resep->created_at != $resep->updated_at)
                                | Diperbarui: {{ \Carbon\Carbon::parse($resep->updated_at)->format('d/m/Y H:i') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <!-- Kolom Kiri - Info Siswa & Dokter -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Informasi Siswa -->
                    <div class="bg-green-50 border border-green-100 rounded-lg shadow-sm">
                        <div class="px-5 py-4 border-b border-green-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <i class="fas fa-user-graduate text-green-500 mr-2"></i>
                                Informasi Siswa
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 rounded-full bg-green-100 mb-3 flex items-center justify-center">
                                    <i class="fas fa-user text-3xl text-green-500"></i>
                                </div>
                                
                                <h4 class="text-lg font-bold text-gray-900 mb-1 text-center">
                                    {{ $resep->siswa->nama_siswa ?? 'Data tidak tersedia' }}
                                </h4>
                                <p class="text-gray-500 text-sm mb-4 text-center">
                                    ID: {{ $resep->Id_Siswa }}
                                </p>
                                
                                @if($resep->siswa)
                                <div class="w-full space-y-3">
                                    <div class="flex items-center justify-between py-2 border-b border-green-100">
                                        <span class="text-gray-600 text-sm font-medium">Jenis Kelamin</span>
                                        <span class="font-medium text-sm">
                                            @if(isset($resep->siswa->jenis_kelamin))
                                                @if($resep->siswa->jenis_kelamin == 'L')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Laki-laki</span>
                                                @elseif($resep->siswa->jenis_kelamin == 'P')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-pink-100 text-pink-800">Perempuan</span>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if(isset($resep->siswa->tanggal_lahir))
                                    <div class="flex items-center justify-between py-2 border-b border-green-100">
                                        <span class="text-gray-600 text-sm font-medium">Tanggal Lahir</span>
                                        <div class="text-right">
                                            <span class="font-medium text-sm block">
                                                {{ \Carbon\Carbon::parse($resep->siswa->tanggal_lahir)->format('d/m/Y') }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($resep->siswa->tanggal_lahir)->age }} tahun
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if(isset($resep->siswa->tempat_lahir))
                                    <div class="flex items-center justify-between py-2 border-b border-green-100">
                                        <span class="text-gray-600 text-sm font-medium">Tempat Lahir</span>
                                        <span class="font-medium text-sm">{{ $resep->siswa->tempat_lahir }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-gray-600 text-sm font-medium">Status</span>
                                        <span class="font-medium text-sm">
                                            @if(isset($resep->siswa->status_aktif) && $resep->siswa->status_aktif == 1)
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Tidak Aktif</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Dokter -->
                    <div class="bg-teal-50 border border-teal-100 rounded-lg shadow-sm">
                        <div class="px-5 py-4 border-b border-teal-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <i class="fas fa-user-md text-teal-500 mr-2"></i>
                                Dokter Penanggung Jawab
                            </h3>
                        </div>
                        <div class="p-5">
                            @if($resep->dokter)
                            <div class="flex items-start space-x-3">
                                <div class="h-12 w-12 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-stethoscope text-teal-500"></i>
                                </div>
                                <div class="flex-1">
                                    <h5 class="text-base font-semibold text-gray-900">{{ $resep->dokter->Nama_Dokter }}</h5>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($resep->dokter->Spesialisasi)
                                            <i class="fas fa-medal text-teal-500 mr-1"></i>
                                            {{ $resep->dokter->Spesialisasi }}
                                        @else
                                            <i class="fas fa-user-md text-teal-500 mr-1"></i>
                                            Dokter Umum
                                        @endif
                                    </p>
                                    @if(isset($resep->dokter->No_Telp))
                                    <p class="text-sm text-gray-600 mt-2">
                                        <i class="fas fa-phone-alt text-teal-500 mr-1"></i> 
                                        {{ $resep->dokter->No_Telp }}
                                    </p>
                                    @endif
                                    @if(isset($resep->dokter->Email))
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-envelope text-teal-500 mr-1"></i> 
                                        {{ $resep->dokter->Email }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-md text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">Data dokter tidak tersedia</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Kolom Kanan - Detail Resep -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Detail Resep Obat -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <i class="fas fa-prescription-bottle text-green-500 mr-2"></i>
                                Detail Resep Obat
                            </h3>
                        </div>
                        <div class="p-5">
                            <!-- Informasi Obat -->
                            <div class="border-b border-gray-200 pb-6 mb-6">
                                <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-pills text-green-500 mr-2"></i>
                                    Informasi Obat
                                </h4>
                                
                                <div class="bg-green-50 p-5 rounded-lg border border-green-200">
                                    <div class="flex items-center mb-4">
                                        <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                            <i class="fas fa-capsules text-green-500 text-lg"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-xl font-bold text-gray-900">{{ $resep->Nama_Obat }}</h5>
                                            <p class="text-sm text-gray-600">Obat yang diresepkan</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div class="bg-white p-4 rounded-md border border-green-200">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-syringe text-green-600 mr-2"></i>
                                                <span class="text-sm font-medium text-gray-700">Dosis</span>
                                            </div>
                                            <p class="text-lg font-semibold text-gray-900">{{ $resep->Dosis }}</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-md border border-green-200">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-clock text-green-600 mr-2"></i>
                                                <span class="text-sm font-medium text-gray-700">Durasi</span>
                                            </div>
                                            <p class="text-lg font-semibold text-gray-900">{{ $resep->Durasi }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dokumen Resep -->
                            @if($resep->Dokumen)
                            <div class="mb-6">
                                <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-file-medical text-purple-500 mr-2"></i>
                                    Dokumen Resep
                                </h4>
                                
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-medical text-purple-500 text-xl mr-3"></i>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Dokumen Tersedia</p>
                                                <p class="text-xs text-gray-600">File resep yang diunggah oleh dokter</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route($viewDokumenRoute, $resep->Id_Resep) }}" 
                                               target="_blank" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                            <a href="{{ route($downloadDokumenRoute, $resep->Id_Resep) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                                                <i class="fas fa-download mr-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Petunjuk Penggunaan -->
                            <div class="bg-yellow-50 p-5 rounded-lg border border-yellow-200 mb-6">
                                <h4 class="text-base font-semibold text-yellow-800 flex items-center mb-4">
                                    <i class="fas fa-exclamation-circle text-yellow-600 mr-2"></i>
                                    Petunjuk Penggunaan Obat
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <ul class="text-sm text-gray-800 space-y-2">
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                            <span>Konsumsi obat sesuai dosis yang ditentukan</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                            <span>Konsultasikan dengan dokter jika menggunakan obat lain</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                            <span>Simpan di tempat sejuk dan kering</span>
                                        </li>
                                    </ul>
                                    <ul class="text-sm text-gray-800 space-y-2">
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                            <span>Perhatikan tanggal kadaluarsa</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                            <span>Jauhkan dari jangkauan anak-anak</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                                            <span>Konsultasi jika ada efek samping</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <div class="text-center">
                                <a href="{{ route($cetakRoute, $resep->Id_Resep) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                    <i class="fas fa-print mr-2"></i>
                                    Cetak Resep Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Riwayat Resep Terbaru -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 flex items-center">
                                <i class="fas fa-history text-amber-500 mr-2"></i>
                                Riwayat Resep Terbaru Siswa
                            </h3>
                        </div>
                        <div class="p-5">
                            @php
                                // Mengambil riwayat resep terbaru untuk siswa ini
                                $riwayatResep = \App\Models\Resep::with(['dokter'])
                                    ->where('Id_Siswa', $resep->Id_Siswa)
                                    ->where('Id_Resep', '!=', $resep->Id_Resep)
                                    ->orderBy('Tanggal_Resep', 'desc')
                                    ->limit(3)
                                    ->get();
                            @endphp
                            
                            @if($riwayatResep->count() > 0)
                                <div class="flow-root">
                                    <ul class="-mb-8">
                                        @foreach($riwayatResep as $index => $riwayat)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex items-start space-x-3">
                                                    <div class="relative">
                                                        <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center ring-8 ring-white">
                                                            <span class="text-sm font-bold text-amber-600">{{ $index + 1 }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div>
                                                            <div class="text-sm">
                                                                <a href="{{ route($showRoute, $riwayat->Id_Resep) }}" 
                                                                   class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                                                                    Resep {{ $riwayat->Id_Resep }}
                                                                </a>
                                                            </div>
                                                            <p class="mt-0.5 text-xs text-gray-500">
                                                                <i class="fas fa-calendar mr-1"></i>
                                                                {{ \Carbon\Carbon::parse($riwayat->Tanggal_Resep)->locale('id')->translatedFormat('d M Y') }} oleh 
                                                                <span class="font-medium">{{ $riwayat->dokter->Nama_Dokter ?? 'Dokter' }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="mt-2 text-sm text-gray-700">
                                                            <div class="bg-gray-50 p-3 rounded-md border">
                                                                <p class="font-medium">{{ $riwayat->Nama_Obat }}</p>
                                                                <p class="text-xs text-gray-600 mt-1">
                                                                    <i class="fas fa-pills mr-1"></i>{{ $riwayat->Dosis }} - 
                                                                    <i class="fas fa-clock mr-1"></i>{{ $riwayat->Durasi }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                                    <a href="{{ route($indexRoute) }}?keyword={{ urlencode($resep->siswa->nama_siswa ?? '') }}" 
                                       class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        Lihat semua resep siswa ini
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-prescription-bottle-alt text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-medium text-gray-900 mb-2">Belum Ada Riwayat Resep Lain</h3>
                                    <p class="text-sm text-gray-500 mb-4">Siswa ini belum memiliki resep obat lainnya dalam sistem.</p>
                                    
                                    {{-- Tombol buat resep baru hanya untuk admin dan petugas --}}
                                    @if($isAdmin || $isPetugas)
                                    <a href="{{ route($createRoute) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Buat Resep Baru
                                    </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
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
    
    // Enhance tooltips for access restrictions
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'petugas') {
        const editButton = document.querySelector('a[href*="edit"]');
        if (editButton) {
            editButton.title = 'Edit resep (akses petugas - tidak dapat menghapus)';
        }
    } else if (userLevel === 'dokter') {
        // Add visual indication for read-only access
        const actionButtons = document.querySelectorAll('a[href*="cetak"], a[href*="view-dokumen"]');
        actionButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                if (button.href.includes('cetak')) {
                    button.title = 'Cetak resep (akses dokter - hanya baca)';
                } else if (button.href.includes('view-dokumen')) {
                    button.title = 'Lihat dokumen (akses dokter - hanya baca)';
                }
            });
        });
    }
});

// Function untuk konfirmasi delete - hanya untuk admin
@if($isAdmin)
function confirmDelete() {
    const resepId = '{{ $resep->Id_Resep }}';
    const siswaName = '{{ $resep->siswa->nama_siswa ?? "N/A" }}';
    const namaObat = '{{ $resep->Nama_Obat }}';
    
    if (confirm(`Apakah Anda yakin ingin menghapus resep obat ini?\n\nID: ${resepId}\nSiswa: ${siswaName}\nObat: ${namaObat}\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.`)) {
        document.getElementById('deleteForm').submit();
    }
}
@endif
</script>
@endpush
@endsection