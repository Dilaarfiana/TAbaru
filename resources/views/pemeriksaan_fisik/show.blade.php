{{-- File: resources/views/pemeriksaan_fisik/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('page_title', 'Detail Pemeriksaan Fisik')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // BLOCK Orang tua - tidak boleh mengakses pemeriksaan fisik sama sekali
    if ($isOrangTua) {
        return redirect()->route('dashboard.orangtua')->with('error', 'Akses ditolak. Orang tua tidak memiliki akses ke pemeriksaan fisik.');
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'pemeriksaan_fisik' : ($isPetugas ? 'petugas.pemeriksaan_fisik' : 'dokter.pemeriksaan_fisik');
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';

    // Routes yang hanya untuk admin dan petugas
    $editRoute = null;
    $destroyRoute = null;
    
    if ($isAdmin) {
        $editRoute = 'pemeriksaan_fisik.edit';
        $destroyRoute = 'pemeriksaan_fisik.destroy';
    } elseif ($isPetugas) {
        $editRoute = 'petugas.pemeriksaan_fisik.edit';
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
                <i class="fas fa-eye text-purple-500 mr-3 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Pemeriksaan Fisik</h2>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-600 mr-2">ID:</span>
                        <span class="bg-purple-100 text-purple-800 text-sm font-bold py-1 px-3 rounded-full">
                            {{ $pemeriksaanFisik->id_prefisik }}
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
                    <a href="{{ route($editRoute, $pemeriksaanFisik->id_prefisik) }}" 
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
                            Anda dapat melihat dan mencetak data pemeriksaan fisik, namun <span class="font-semibold text-red-600">tidak dapat mengedit atau menghapus</span> data. 
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
                            Anda dapat melihat, mengedit, dan mencetak data pemeriksaan fisik untuk keperluan pelayanan kesehatan siswa. 
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
                            Anda memiliki akses penuh untuk melihat, mengedit, menghapus, dan mencetak data pemeriksaan fisik. 
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

            <!-- Info Banner -->
            <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-eye text-purple-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-purple-800 mb-1">Informasi Pemeriksaan Fisik</h3>
                        <p class="text-sm text-purple-700 mb-2">
                            Menampilkan detail lengkap pemeriksaan fisik termasuk antropometri dan pemeriksaan sistem organ.
                        </p>
                        
                        <!-- Metadata Info -->
                        <div class="mt-2 p-2 bg-purple-100 border border-purple-300 rounded text-xs">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <div>
                                    <span class="font-medium text-purple-800">Dibuat:</span>
                                    <span class="text-purple-700">{{ \Carbon\Carbon::parse($pemeriksaanFisik->created_at)->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-purple-800">Diperbarui:</span>
                                    <span class="text-purple-700">{{ \Carbon\Carbon::parse($pemeriksaanFisik->updated_at)->format('d/m/Y H:i') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-purple-800">Status:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-purple-800">Akses Anda:</span>
                                    <span class="text-purple-700">
                                        @if($isAdmin)
                                            Administrator (Full CRUD)
                                        @elseif($isPetugas)
                                            Petugas UKS (CRU)
                                        @elseif($isDokter)
                                            Dokter (Read Only)
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Informasi Utama -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <!-- Data Siswa -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm lg:col-span-2">
                    <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                        <i class="fas fa-user-graduate text-blue-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Data Siswa</h3>
                    </div>
                    
                    @if($pemeriksaanFisik->detailPemeriksaan && $pemeriksaanFisik->detailPemeriksaan->siswa)
                    @php $siswa = $pemeriksaanFisik->detailPemeriksaan->siswa; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-id-card text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">ID Siswa</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->id_siswa }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-user text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Nama Siswa</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->nama_siswa }}</p>
                                </div>
                            </div>
                            @if($siswa->detailSiswa && $siswa->detailSiswa->kelas)
                            <div class="flex items-start">
                                <i class="fas fa-graduation-cap text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Kelas</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->detailSiswa->kelas->Nama_Kelas }}</p>
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
                                        @if($siswa->jenis_kelamin == 'L')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                <i class="fas fa-male mr-1"></i>Laki-laki
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-pink-100 text-pink-800">
                                                <i class="fas fa-female mr-1"></i>Perempuan
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-birthday-cake text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Lahir</p>
                                    <p class="font-medium text-gray-800">
                                        @if($siswa->tanggal_lahir)
                                            {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') }}
                                            <span class="text-xs text-gray-500 block">
                                                ({{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->age }} tahun)
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">Data siswa tidak tersedia</p>
                    </div>
                    @endif
                </div>
                
                <!-- Data Dokter -->
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                        <i class="fas fa-user-md text-green-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Dokter</h3>
                    </div>
                    
                    @if($pemeriksaanFisik->detailPemeriksaan && $pemeriksaanFisik->detailPemeriksaan->dokter)
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-id-badge text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">ID Dokter</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan->dokter->Id_Dokter }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-stethoscope text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Nama Dokter</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan->dokter->Nama_Dokter }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-medal text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Spesialisasi</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan->dokter->Spesialisasi ?? 'Umum' }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">Data dokter tidak tersedia</p>
                    </div>
                    @endif
                </div>
                
                <!-- Waktu Pemeriksaan -->
                <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-purple-200 pb-2">
                        <i class="fas fa-clock text-purple-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Waktu</h3>
                    </div>
                    
                    @if($pemeriksaanFisik->detailPemeriksaan && $pemeriksaanFisik->detailPemeriksaan->tanggal_jam)
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal</p>
                                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($pemeriksaanFisik->detailPemeriksaan->tanggal_jam)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-clock text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Waktu</p>
                                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($pemeriksaanFisik->detailPemeriksaan->tanggal_jam)->format('H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-calendar-day text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Hari</p>
                                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($pemeriksaanFisik->detailPemeriksaan->tanggal_jam)->locale('id')->translatedFormat('l') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-history text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Relatif</p>
                                <p class="font-medium text-gray-800 text-xs">{{ \Carbon\Carbon::parse($pemeriksaanFisik->detailPemeriksaan->tanggal_jam)->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">Data waktu tidak tersedia</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Petugas UKS Info (jika ada) -->
            @if($pemeriksaanFisik->detailPemeriksaan && $pemeriksaanFisik->detailPemeriksaan->petugasUks)
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
                            <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan->nip }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-user text-orange-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Nama Petugas</p>
                            <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan->petugasUks->nama_petugas_uks }}</p>
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

            <!-- Antropometri -->
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-ruler-combined text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Antropometri</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Tinggi Badan -->
                    <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-arrows-alt-v text-green-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Tinggi Badan</h4>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $pemeriksaanFisik->tinggi_badan ? $pemeriksaanFisik->tinggi_badan . ' cm' : '-' }}
                        </p>
                    </div>

                    <!-- Berat Badan -->
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-weight text-blue-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Berat Badan</h4>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $pemeriksaanFisik->berat_badan ? $pemeriksaanFisik->berat_badan . ' kg' : '-' }}
                        </p>
                    </div>

                    <!-- Lingkar Kepala -->
                    <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-circle text-yellow-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Lingkar Kepala</h4>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ $pemeriksaanFisik->lingkar_kepala ? $pemeriksaanFisik->lingkar_kepala . ' cm' : '-' }}
                        </p>
                    </div>

                    <!-- Lingkar Lengan Atas -->
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-ring text-purple-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Lingkar Lengan Atas</h4>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $pemeriksaanFisik->lingkar_lengan_atas ? $pemeriksaanFisik->lingkar_lengan_atas . ' cm' : '-' }}
                        </p>
                    </div>
                </div>

                <!-- BMI Calculation -->
                @if($pemeriksaanFisik->tinggi_badan && $pemeriksaanFisik->berat_badan)
                @php
                    $heightInMeters = $pemeriksaanFisik->tinggi_badan / 100;
                    $bmi = $pemeriksaanFisik->berat_badan / ($heightInMeters * $heightInMeters);
                    $bmi = round($bmi, 1);
                    
                    if ($bmi < 18.5) {
                        $category = 'Berat Badan Kurang';
                        $colorClass = 'text-blue-600';
                        $bgClass = 'bg-blue-100';
                        $borderClass = 'border-blue-200';
                    } elseif ($bmi < 25) {
                        $category = 'Berat Badan Normal';
                        $colorClass = 'text-green-600';
                        $bgClass = 'bg-green-100';
                        $borderClass = 'border-green-200';
                    } elseif ($bmi < 30) {
                        $category = 'Berat Badan Berlebih';
                        $colorClass = 'text-yellow-600';
                        $bgClass = 'bg-yellow-100';
                        $borderClass = 'border-yellow-200';
                    } else {
                        $category = 'Obesitas';
                        $colorClass = 'text-red-600';
                        $bgClass = 'bg-red-100';
                        $borderClass = 'border-red-200';
                    }
                @endphp
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-calculator text-gray-500 mr-2"></i>
                        <h4 class="font-semibold text-gray-800">Indeks Massa Tubuh (BMI)</h4>
                    </div>
                    <div class="bg-white p-4 rounded-md border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-3xl font-bold {{ $colorClass }} mr-4">{{ $bmi }}</span>
                                <div class="px-3 py-1 rounded-full text-sm font-medium {{ $bgClass }} {{ $colorClass }} border {{ $borderClass }}">
                                    {{ $category }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 text-right">
                                <div>Tinggi: {{ $pemeriksaanFisik->tinggi_badan }} cm</div>
                                <div>Berat: {{ $pemeriksaanFisik->berat_badan }} kg</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Pemeriksaan Sistem Organ -->
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-heartbeat text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Pemeriksaan Sistem Organ</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Kepala -->
                    <div class="bg-red-50 border border-red-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-head-side-virus text-red-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Kepala</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->kepala ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dada -->
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-lungs text-blue-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Dada</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->dada ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Jantung -->
                    <div class="bg-pink-50 border border-pink-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-heart text-pink-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Jantung</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->jantung ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Paru -->
                    <div class="bg-green-50 border border-green-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-lungs-virus text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Paru</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->paru ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Perut -->
                    <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-hand-paper text-yellow-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Perut</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->perut ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hepar -->
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-prescription-bottle text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Hepar</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->hepar ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Anogenital -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-user-check text-indigo-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Anogenital</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->anogenital ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ekstremitas -->
                    <div class="bg-teal-50 border border-teal-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-walking text-teal-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Ekstremitas</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->ekstremitas ?: 'Tidak diperiksa' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pemeriksaan Penunjang & Rencana -->
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-clipboard-check text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Pemeriksaan Penunjang & Rencana</h3>
                </div>
                
                <!-- Pemeriksaan Penunjang -->
                @if($pemeriksaanFisik->pemeriksaan_penunjang)
                <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm mb-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-microscope text-purple-500 mr-2"></i>
                        <h4 class="font-semibold text-purple-800">Pemeriksaan Penunjang</h4>
                    </div>
                    <div class="bg-white p-4 rounded-md border border-purple-200">
                        <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $pemeriksaanFisik->pemeriksaan_penunjang }}</p>
                    </div>
                </div>
                @endif

                <!-- Grid Masalah & Rencana -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($pemeriksaanFisik->masalah_aktif)
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-orange-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Masalah Aktif</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->masalah_aktif }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($pemeriksaanFisik->rencana_medis_dan_terapi)
                    <div class="bg-teal-50 border border-teal-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-pills text-teal-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Rencana Medis dan Terapi</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanFisik->rencana_medis_dan_terapi }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                @if(!$pemeriksaanFisik->pemeriksaan_penunjang && !$pemeriksaanFisik->masalah_aktif && !$pemeriksaanFisik->rencana_medis_dan_terapi)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-clipboard-check text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-600 mb-2">Belum Ada Data Penunjang</h3>
                    <p class="text-gray-500">Belum ada data pemeriksaan penunjang, masalah aktif, atau rencana terapi</p>
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
                        <a href="{{ route($editRoute, $pemeriksaanFisik->id_prefisik) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Pemeriksaan
                        </a>
                    @endif
                    
                    {{-- Tombol Hapus - hanya admin --}}
                    @if($destroyRoute)
                        <form action="{{ route($destroyRoute, $pemeriksaanFisik->id_prefisik) }}" method="POST" class="inline-block" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Data
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
    console.log('Record ID:', '{{ $pemeriksaanFisik->id_prefisik }}');
});

@if($isAdmin)
function confirmDelete() {
    const pemeriksaanId = '{{ $pemeriksaanFisik->id_prefisik }}';
    const siswaName = '{{ $pemeriksaanFisik->detailPemeriksaan->siswa->nama_siswa ?? "Tidak Diketahui" }}';
    
    if (confirm(`PERINGATAN!\n\nApakah Anda yakin ingin menghapus data pemeriksaan fisik ini?\n\nID: ${pemeriksaanId}\nSiswa: ${siswaName}\n\nTindakan ini akan menghapus:\n- Data pemeriksaan fisik lengkap\n- Semua data antropometri\n- Data pemeriksaan sistem organ\n\nData yang dihapus TIDAK DAPAT dikembalikan!\n\nKlik OK untuk melanjutkan atau Cancel untuk membatalkan.`)) {
        document.getElementById('deleteForm').submit();
    }
}
@endif
</script>
@endpush
@endsection