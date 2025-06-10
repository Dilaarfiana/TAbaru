{{-- File: resources/views/siswa/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: LIMITED ACCESS, DOKTER: READ ONLY, ORANGTUA: OWN CHILD ONLY (NO PARENT DATA) --}}
@extends('layouts.app')

@section('page_title', 'Detail Siswa')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Check if orang tua is accessing their own child's data
    if ($isOrangTua) {
        $siswaId = session('siswa_id');
        if ($siswaId !== $siswa->id_siswa) {
            abort(403, 'Anda hanya dapat melihat data anak Anda sendiri.');
        }
    }
    
    // Define routes based on user role
    if ($isAdmin) {
        $baseRoute = 'siswa';
        $indexRoute = 'siswa.index';
        $editRoute = 'siswa.edit';
    } elseif ($isPetugas) {
        $baseRoute = 'petugas.siswa';
        $indexRoute = 'petugas.siswa.index';
        $editRoute = 'petugas.siswa.edit';
    } elseif ($isDokter) {
        $baseRoute = 'dokter.siswa';
        $indexRoute = 'dokter.siswa.index';
        $editRoute = null; // Dokter tidak bisa edit
    } else { // orang_tua
        $baseRoute = 'orangtua.siswa';
        $indexRoute = 'orangtua.siswa.show';
        $editRoute = 'orangtua.siswa.edit';
    }
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-7xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-user-graduate text-indigo-500 mr-3 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        @if($isOrangTua)
                            Data Anak Anda
                        @else
                            Detail Siswa
                        @endif
                    </h2>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-600 mr-2">{{ $siswa->nama_siswa }} • ID:</span>
                        <span class="bg-indigo-100 text-indigo-800 text-sm font-bold py-1 px-3 rounded-full">
                            {{ $siswa->id_siswa }}
                        </span>
                        @if($isDokter)
                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                <i class="fas fa-stethoscope mr-1"></i>Akses Dokter (Read Only)
                            </span>
                        @elseif($isPetugas)
                            <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-tie mr-1"></i>Akses Petugas (Limited)
                            </span>
                        @elseif($isAdmin)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-shield mr-1"></i>Akses Admin (Full CRUD)
                            </span>
                        @elseif($isOrangTua)
                            <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                <i class="fas fa-heart mr-1"></i>Data Anak
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                {{-- Tombol Edit - hanya admin, petugas, dan orang tua --}}
                @if($editRoute)
                    <a href="{{ route($editRoute, $siswa->id_siswa) }}" 
                       class="bg-orange-500 text-white hover:bg-orange-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                        <i class="fas fa-edit mr-2"></i> 
                        @if($isOrangTua)
                            Edit Data
                        @elseif($isPetugas)
                            Edit (Terbatas)
                        @else
                            Edit Data
                        @endif
                    </a>
                @endif
                
                @if(!$isOrangTua)
                    <a href="{{ route($indexRoute) }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                @else
                    <a href="{{ route('dashboard.orangtua') }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                @endif
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
                            Anda dapat melihat seluruh data siswa untuk keperluan medis, namun <span class="font-semibold text-red-600">tidak dapat mengubah data pribadi</span>.
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
                            Anda dapat melihat dan mengedit data dasar siswa untuk keperluan administrasi UKS. <span class="font-semibold text-red-600">Data orang tua hanya bisa dilihat admin.</span>
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
                            Anda memiliki akses penuh untuk melihat, mengedit, menghapus, dan mengelola semua data siswa serta orang tua.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isOrangTua)
            <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-heart text-purple-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-purple-800 mb-1">Data Anak Anda</h3>
                        <p class="text-sm text-purple-700">
                            Anda dapat melihat informasi lengkap anak Anda dan memperbarui data yang diizinkan untuk orang tua.
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
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-graduate text-indigo-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-indigo-800 mb-1">Informasi Siswa</h3>
                        <p class="text-sm text-indigo-700 mb-2">
                            Menampilkan detail lengkap data {{ $isOrangTua ? 'anak Anda' : 'siswa' }} termasuk informasi akademik dan data orang tua.
                        </p>
                        
                        <!-- Metadata Info -->
                        <div class="mt-2 p-2 bg-indigo-100 border border-indigo-300 rounded text-xs">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <div>
                                    <span class="font-medium text-indigo-800">Status:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $siswa->status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas {{ $siswa->status_aktif ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                        {{ $siswa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-indigo-800">Alokasi:</span>
                                    <span class="text-indigo-700">
                                        {{ $siswa->detailSiswa ? 'Teralokasi' : 'Belum Alokasi' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-indigo-800">Orang Tua:</span>
                                    <span class="text-indigo-700">
                                        {{ $siswa->orangTua ? 'Lengkap' : 'Belum Lengkap' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-indigo-800">Akses Anda:</span>
                                    <span class="text-indigo-700">
                                        @if($isAdmin)
                                            Administrator (Full)
                                        @elseif($isPetugas)
                                            Petugas UKS (Limited)
                                        @elseif($isDokter)
                                            Dokter (Read Only)
                                        @elseif($isOrangTua)
                                            Orang Tua (Child Only)
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Informasi Utama -->
            <div class="grid grid-cols-1 {{ $isOrangTua ? 'lg:grid-cols-1' : 'lg:grid-cols-4' }} gap-6 mb-6">
                <!-- Data Siswa -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm {{ $isOrangTua ? 'col-span-1' : 'lg:col-span-3' }}">
                    <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                        <i class="fas fa-user-graduate text-blue-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi {{ $isOrangTua ? 'Anak' : 'Siswa' }}</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-id-card text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">ID Siswa</p>
                                    <p class="font-mono font-bold text-gray-800 cursor-pointer" onclick="copyToClipboard('{{ $siswa->id_siswa }}')" title="Klik untuk menyalin">
                                        {{ $siswa->id_siswa }}
                                        <i class="fas fa-copy text-gray-400 text-xs ml-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-user text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Nama Lengkap</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->nama_siswa }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Tempat Lahir</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->tempat_lahir ?? '-' }}</p>
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
                                        @elseif($siswa->jenis_kelamin == 'P')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-pink-100 text-pink-800">
                                                <i class="fas fa-female mr-1"></i>Perempuan
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-calendar-plus text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Masuk</p>
                                    <p class="font-medium text-gray-800">
                                        {{ $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->format('d M Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-toggle-{{ $siswa->status_aktif ? 'on' : 'off' }} text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Status</p>
                                    <p class="font-medium text-gray-800">
                                        @if($siswa->status_aktif)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                <div class="w-2 h-2 bg-red-500 rounded-full mr-1"></div>
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($siswa->tanggal_lulus && !$isOrangTua)
                            <div class="flex items-start">
                                <i class="fas fa-graduation-cap text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Lulus</p>
                                    <p class="font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($siswa->tanggal_lulus)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if(!$isOrangTua)
                <!-- Quick Stats -->
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                        <i class="fas fa-chart-pie text-green-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Ringkasan</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-{{ $siswa->detailSiswa ? 'check-circle' : 'times-circle' }} text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Alokasi Kelas</p>
                                <p class="font-medium text-gray-800">{{ $siswa->detailSiswa ? 'Teralokasi' : 'Belum Alokasi' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-{{ $siswa->orangTua ? 'check-circle' : 'times-circle' }} text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Data Orang Tua</p>
                                <p class="font-medium text-gray-800">{{ $siswa->orangTua ? 'Lengkap' : 'Belum Lengkap' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-id-badge text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Format ID</p>
                                <p class="font-medium text-gray-800">
                                    {{ substr($siswa->id_siswa, 0, 1) == '6' ? 'Format Baru' : 'Format Lama' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-clock text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Lama Sekolah</p>
                                <p class="font-medium text-gray-800">
                                    @if($siswa->tanggal_masuk)
                                        @php
                                            $masuk = \Carbon\Carbon::parse($siswa->tanggal_masuk);
                                            $sekarang = \Carbon\Carbon::now();
                                            $diff = $masuk->diff($sekarang);
                                        @endphp
                                        {{ $diff->y }} tahun {{ $diff->m }} bulan
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Data Akademik -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm mb-6">
                <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-graduation-cap text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Data Akademik</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kelas -->
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-chalkboard text-indigo-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Kelas</p>
                                @if($siswa->detailSiswa && $siswa->detailSiswa->kelas)
                                    <p class="font-medium text-gray-800">{{ $siswa->detailSiswa->kelas->Nama_Kelas }}</p>
                                    @if($siswa->detailSiswa->kelas->Tahun_Ajaran)
                                        <p class="text-xs text-gray-500">Tahun Ajaran {{ $siswa->detailSiswa->kelas->Tahun_Ajaran }}</p>
                                    @endif
                                @else
                                    <p class="font-medium text-red-600">Belum Dialokasikan</p>
                                    <p class="text-xs text-gray-500">Siswa belum ditempatkan di kelas</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Jurusan -->
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-book text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Jurusan</p>
                                @php
                                    $namaJurusan = null;
                                    $kodeJurusan = null;
                                    
                                    // Get jurusan info
                                    if($siswa->detailSiswa && $siswa->detailSiswa->kelas && isset($siswa->detailSiswa->kelas->jurusan)) {
                                        $namaJurusan = $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan;
                                        $kodeJurusan = $siswa->detailSiswa->kelas->jurusan->Kode_Jurusan;
                                    } elseif($siswa->detailSiswa && isset($siswa->detailSiswa->jurusan)) {
                                        $namaJurusan = $siswa->detailSiswa->jurusan->Nama_Jurusan;
                                        $kodeJurusan = $siswa->detailSiswa->jurusan->Kode_Jurusan;
                                    } elseif($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan) {
                                        $jurusan = \App\Models\Jurusan::where('Kode_Jurusan', $siswa->detailSiswa->kode_jurusan)->first();
                                        if($jurusan) {
                                            $namaJurusan = $jurusan->Nama_Jurusan;
                                            $kodeJurusan = $jurusan->Kode_Jurusan;
                                        }
                                    }
                                @endphp
                                
                                @if($namaJurusan && $kodeJurusan)
                                    <p class="font-medium text-gray-800">{{ $namaJurusan }}</p>
                                    <p class="text-xs text-gray-500">Kode: {{ $kodeJurusan }}</p>
                                @else
                                    <p class="font-medium text-red-600">Belum Dialokasikan</p>
                                    <p class="text-xs text-gray-500">Siswa belum ditempatkan di jurusan</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Allocation Alert -->
                @if(!$siswa->detailSiswa && $isAdmin)
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Perlu Alokasi</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                Siswa ini belum dialokasikan ke kelas dan jurusan. Silakan lakukan alokasi untuk melengkapi data akademik.
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('alokasi.unallocated') }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Alokasikan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Parent Information Card - Hidden for orang_tua role -->
            @if(!$isOrangTua)
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm mb-6">
                <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-users text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Informasi Orang Tua</h3>
                </div>
                
                @if($siswa->orangTua)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Father Info -->
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start">
                                <i class="fas fa-male text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Data Ayah</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->orangTua->nama_ayah ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $siswa->orangTua->pekerjaan_ayah ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $siswa->orangTua->pendidikan_ayah ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Mother Info -->
                        <div class="bg-pink-50 border border-pink-100 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start">
                                <i class="fas fa-female text-pink-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Data Ibu</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->orangTua->nama_ibu ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $siswa->orangTua->pekerjaan_ibu ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $siswa->orangTua->pendidikan_ibu ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="md:col-span-2 bg-gray-50 border border-gray-100 rounded-lg p-4 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-gray-600 mr-2 w-4 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Alamat</p>
                                        <p class="font-medium text-gray-800">{{ $siswa->orangTua->alamat ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-phone text-gray-600 mr-2 w-4 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">No. Telepon</p>
                                        <p class="font-mono font-medium text-gray-800">{{ $siswa->orangTua->no_telp ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($isAdmin)
                        <div class="md:col-span-2 pt-4 border-t border-gray-200">
                            <a href="{{ route('orangtua.edit', ['orangtua' => $siswa->orangTua->id_orang_tua]) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Data Orang Tua
                            </a>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                            <i class="fas fa-users text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-600 mb-2">Data Orang Tua Belum Ada</h3>
                        <p class="text-gray-500 mb-4">Belum ada informasi orang tua untuk siswa ini.</p>
                        @if($isAdmin)
                        <a href="{{ route('orangtua.create', ['siswa_id' => $siswa->id_siswa]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Data Orang Tua
                        </a>
                        @endif
                    </div>
                @endif
            </div>
            @endif

            <!-- System Information -->
            @if(!$isOrangTua)
            <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 shadow-sm mb-6">
                <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-cog text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Informasi Sistem</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-start">
                        <i class="fas fa-calendar-plus text-gray-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Dibuat</p>
                            <p class="font-medium text-gray-800">
                                {{ $siswa->created_at ? $siswa->created_at->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-calendar-check text-gray-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
                            <p class="font-medium text-gray-800">
                                {{ $siswa->updated_at ? $siswa->updated_at->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-history text-gray-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Relatif</p>
                            <p class="font-medium text-gray-800">
                                {{ $siswa->updated_at ? $siswa->updated_at->diffForHumans() : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                @if(!$isOrangTua)
                    <a href="{{ route($indexRoute) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2 text-gray-500"></i>
                        Kembali ke Daftar
                    </a>
                @else
                    <a href="{{ route('dashboard.orangtua') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-home mr-2 text-gray-500"></i>
                        Kembali ke Dashboard
                    </a>
                @endif
                
                <div class="flex space-x-2">
                    {{-- Tombol Edit --}}
                    @if($editRoute)
                        <a href="{{ route($editRoute, $siswa->id_siswa) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Data {{ $isOrangTua ? 'Anak' : 'Siswa' }}
                        </a>
                    @endif
                    
                    {{-- Tombol Hapus - hanya admin --}}
                    @if($isAdmin)
                        <form action="{{ route('siswa.destroy', $siswa->id_siswa) }}" method="POST" class="inline-block" id="deleteForm">
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
// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('ID Siswa berhasil disalin!', 'success');
        }).catch(() => {
            showToast('Gagal menyalin ID Siswa', 'error');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToast('ID Siswa berhasil disalin!', 'success');
        } catch (err) {
            showToast('Gagal menyalin ID Siswa', 'error');
        } finally {
            textArea.remove();
        }
    }
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    toast.className = `${bgColor} text-white px-4 py-2 rounded-lg shadow-lg`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : 'times'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Simple toast positioning
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

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
    
    // Security check for parent access
    const userLevel = '{{ $userLevel }}';
    const allowedSiswaId = '{{ session("siswa_id") }}';
    const currentSiswaId = '{{ $siswa->id_siswa }}';
    
    if (userLevel === 'orang_tua' && allowedSiswaId !== currentSiswaId) {
        console.warn('Access violation detected: Parent trying to access unauthorized student data');
        showToast('Akses ditolak: Anda hanya dapat melihat data anak Anda sendiri', 'error');
        setTimeout(() => {
            window.location.href = '{{ route("orangtua.siswa.show") }}';
        }, 2000);
        return;
    }
    
    // Log user level for debugging
    console.log('User Level:', '{{ $userLevel }}');
    console.log('Student ID:', '{{ $siswa->id_siswa }}');
});

@if($isAdmin)
function confirmDelete() {
    const siswaName = '{{ $siswa->nama_siswa }}';
    const siswaId = '{{ $siswa->id_siswa }}';
    
    if (confirm(`PERINGATAN!\n\nApakah Anda yakin ingin menghapus data siswa ini?\n\nNama: ${siswaName}\nID: ${siswaId}\n\nTindakan ini akan menghapus:\n- Data siswa lengkap\n- Data akademik\n- Data orang tua terkait\n- Semua data terkait lainnya\n\nData yang dihapus TIDAK DAPAT dikembalikan!\n\nKlik OK untuk melanjutkan atau Cancel untuk membatalkan.`)) {
        document.getElementById('deleteForm').submit();
    }
}
@endif
</script>
@endpush
@endsection