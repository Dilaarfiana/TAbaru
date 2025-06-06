{{-- File: resources/views/siswa/edit.blade.php --}}
{{-- ADMIN: FULL EDIT, PETUGAS: LIMITED EDIT, ORANG TUA: MINIMAL EDIT, DOKTER: NO ACCESS --}}
@extends('layouts.app')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Block access for dokter completely
    if ($isDokter) {
        abort(403, 'Dokter tidak memiliki akses untuk mengedit data siswa.');
    }
    
    // For orang tua, check if they're editing their own child's data
    if ($isOrangTua) {
        $siswaId = session('siswa_id');
        if (!$siswaId || $siswa->id_siswa !== $siswaId) {
            abort(403, 'Anda hanya dapat mengedit data siswa Anda sendiri.');
        }
    }
    
    // Define routes based on user role
    if ($isAdmin) {
        $baseRoute = 'siswa';
        $indexRoute = 'siswa.index';
        $updateRoute = 'siswa.update';
    } elseif ($isPetugas) {
        $baseRoute = 'petugas.siswa';
        $indexRoute = 'petugas.siswa.index';
        $updateRoute = 'petugas.siswa.update';
    } else { // orang_tua
        $baseRoute = 'orangtua.siswa';
        $indexRoute = 'orangtua.siswa.show';
        $updateRoute = 'orangtua.siswa.update';
    }
@endphp

{{-- BLOCK ACCESS FOR DOKTER --}}
@if($isDokter)
    <div class="p-4 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
            <div class="bg-red-50 border-l-4 border-red-500 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-red-800">Akses Ditolak</h3>
                        <p class="text-sm text-red-700 mt-2">
                            Sebagai dokter, Anda tidak memiliki izin untuk mengedit data siswa. 
                            Anda hanya dapat melihat data siswa untuk keperluan medis.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('dokter.siswa.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-user-graduate mr-2"></i>
                                Lihat Daftar Siswa
                            </a>
                            <a href="{{ route('dashboard.dokter') }}" 
                               class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-home mr-2"></i>
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@else
{{-- NORMAL CONTENT FOR ADMIN, PETUGAS, AND ORANG TUA --}}

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">
                    @if($isOrangTua)
                        Edit Data Siswa Saya
                    @else
                        Edit Data Siswa
                    @endif
                    @if($isOrangTua)
                        <span class="ml-3 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                            <i class="fas fa-users mr-1"></i>Akses Orang Tua
                        </span>
                    @elseif($isPetugas)
                        <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                            <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                        </span>
                    @elseif($isAdmin)
                        <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                            <i class="fas fa-user-shield mr-1"></i>Akses Admin
                        </span>
                    @endif
                </h2>
            </div>
            <a href="{{ route($indexRoute) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                @if($isOrangTua)
                    Kembali ke Profil
                @else
                    Kembali
                @endif
            </a>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Access Level Info -->
            @if($isOrangTua)
            <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-purple-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-purple-700">
                            <strong>Akses Orang Tua:</strong> Anda dapat mengedit data pribadi siswa Anda yang terbatas.
                            Data yang dapat diubah: nama dan tempat/tanggal lahir saja. 
                            Untuk perubahan data lainnya, silakan hubungi sekolah.
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
                        <p class="text-sm text-yellow-700">
                            <strong>Akses Petugas:</strong> Anda dapat mengedit data dasar siswa, namun tidak dapat mengubah status aktif atau tanggal lulus.
                            Data yang dapat diubah: nama, tempat/tanggal lahir, jenis kelamin, dan tanggal masuk.
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Info Box -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            @if($isOrangTua)
                                Anda sedang mengubah data siswa Anda dengan ID <span class="font-mono font-medium">{{ $siswa->id_siswa }}</span>. 
                                Pastikan data yang diubah sudah benar sebelum menyimpan. Perubahan akan dicatat dalam sistem.
                            @else
                                Anda sedang mengubah data siswa dengan ID <span class="font-mono font-medium">{{ $siswa->id_siswa }}</span>. 
                                @php
                                    // Cek apakah ID sesuai format baru
                                    $idFormat = '';
                                    if (strlen($siswa->id_siswa) >= 6) {
                                        if (substr($siswa->id_siswa, 0, 1) == '6') {
                                            if ($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan) {
                                                $idFormat = 'Sudah dialokasi ke jurusan';
                                            } else {
                                                $idFormat = 'Belum dialokasi ke jurusan';
                                            }
                                        } else {
                                            $idFormat = 'Format ID lama';
                                        }
                                    }
                                @endphp
                                @if($idFormat)
                                    <span class="font-medium text-{{ $idFormat == 'Format ID lama' ? 'orange' : ($idFormat == 'Sudah dialokasi ke jurusan' ? 'green' : 'yellow') }}-600">
                                        ({{ $idFormat }})
                                    </span>
                                @endif
                                <br>
                                Pastikan data yang diubah sudah benar sebelum menyimpan.
                            @endif
                            Data yang wajib diisi ditandai dengan <span class="text-red-500">*</span>.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route($updateRoute, $siswa->id_siswa) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <!-- ID Siswa (read-only for all) -->
                    <div>
                        <label for="id_siswa_display" class="block text-sm font-medium text-gray-700 mb-1">
                            ID Siswa
                        </label>
                        <div class="relative rounded-md shadow-sm bg-gray-50 border border-gray-300">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input type="text" 
                                id="id_siswa_display" 
                                value="{{ $siswa->id_siswa }}" 
                                class="pl-10 block w-full bg-gray-50 border-0 rounded-md h-10 focus:ring-0 focus:outline-none"
                                readonly disabled>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            ID siswa tidak dapat diubah
                        </p>
                    </div>
                    
                    <!-- Nama Siswa - Editable for all roles -->
                    <div>
                        <label for="nama_siswa" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Siswa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" 
                                id="nama_siswa" 
                                name="nama_siswa" 
                                value="{{ old('nama_siswa', $siswa->nama_siswa) }}" 
                                required 
                                maxlength="50" 
                                placeholder="Masukkan nama lengkap siswa"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('nama_siswa') border-red-300 @enderror">
                        </div>
                    </div>

                    <!-- Tempat Lahir - Editable for all roles -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                            Tempat Lahir
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <input type="text" 
                                id="tempat_lahir" 
                                name="tempat_lahir" 
                                value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" 
                                maxlength="30" 
                                placeholder="Contoh: Magelang"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tempat_lahir') border-red-300 @enderror">
                        </div>
                    </div>
                    
                    <!-- Tanggal Lahir - Editable for all roles -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Lahir
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" 
                                id="tanggal_lahir" 
                                name="tanggal_lahir" 
                                value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ? date('Y-m-d', strtotime($siswa->tanggal_lahir)) : '') }}" 
                                max="{{ date('Y-m-d') }}"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir') border-red-300 @enderror">
                        </div>
                    </div>
                    
                    <!-- Jenis Kelamin - Admin & Petugas can edit, Orang Tua read-only -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Kelamin
                        </label>
                        @if($isOrangTua)
                        <!-- Read-only for orang tua -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                @if($siswa->jenis_kelamin == 'L')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @elseif($siswa->jenis_kelamin == 'P')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @endif
                            </div>
                            <input type="text" 
                                value="{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : 'Tidak diset') }}" 
                                class="pl-10 block w-full bg-gray-50 border border-gray-300 rounded-md h-10 focus:ring-0 focus:outline-none"
                                readonly disabled>
                            <input type="hidden" name="jenis_kelamin" value="{{ $siswa->jenis_kelamin }}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Hubungi sekolah untuk mengubah jenis kelamin
                        </p>
                        @else
                        <!-- Editable for admin and petugas -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <select id="jenis_kelamin" 
                                name="jenis_kelamin" 
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none @error('jenis_kelamin') border-red-300 @enderror">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ (old('jenis_kelamin', $siswa->jenis_kelamin) == 'L') ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ (old('jenis_kelamin', $siswa->jenis_kelamin) == 'P') ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Tanggal Masuk - Admin & Petugas can edit, Orang Tua read-only -->
                    <div>
                        <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Masuk
                        </label>
                        @if($isOrangTua)
                        <!-- Read-only for orang tua -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" 
                                value="{{ $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->format('d F Y') : 'Tidak diset' }}" 
                                class="pl-10 block w-full bg-gray-50 border border-gray-300 rounded-md h-10 focus:ring-0 focus:outline-none"
                                readonly disabled>
                            <input type="hidden" name="tanggal_masuk" value="{{ $siswa->tanggal_masuk }}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Hubungi sekolah untuk mengubah tanggal masuk
                        </p>
                        @else
                        <!-- Editable for admin and petugas -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" 
                                id="tanggal_masuk" 
                                name="tanggal_masuk" 
                                value="{{ old('tanggal_masuk', $siswa->tanggal_masuk ? date('Y-m-d', strtotime($siswa->tanggal_masuk)) : date('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_masuk') border-red-300 @enderror">
                        </div>
                        @endif
                    </div>
                    
                    <!-- Status Aktif - Hanya Admin yang bisa edit -->
                    @if($isAdmin)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Status Siswa
                        </label>
                        <div class="flex space-x-4">
                            <!-- Radio untuk Aktif -->
                            <div class="w-1/2">
                                <input type="radio" id="status_aktif_1" name="status_aktif" value="1" class="hidden" 
                                    {{ (old('status_aktif', $siswa->status_aktif) == '1' || old('status_aktif', $siswa->status_aktif) == 1) ? 'checked' : '' }}>
                                <label for="status_aktif_1" 
                                    class="cursor-pointer flex items-center p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200 block w-full status-radio-aktif">
                                    <div class="mr-3 h-5 w-5 rounded-full border border-gray-300 flex items-center justify-center bg-white status-dot-aktif">
                                        <div class="h-2.5 w-2.5 rounded-full bg-green-500 hidden"></div>
                                    </div>
                                    <div>
                                        <span class="flex items-center text-sm font-medium text-gray-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Aktif
                                        </span>
                                        <span class="mt-1 block text-xs text-gray-500">Siswa aktif bersekolah</span>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Radio untuk Tidak Aktif -->
                            <div class="w-1/2">
                                <input type="radio" id="status_aktif_0" name="status_aktif" value="0" class="hidden"
                                    {{ (old('status_aktif', $siswa->status_aktif) == '0' || old('status_aktif', $siswa->status_aktif) == 0) ? 'checked' : '' }}>
                                <label for="status_aktif_0" 
                                    class="cursor-pointer flex items-center p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200 block w-full status-radio-nonaktif">
                                    <div class="mr-3 h-5 w-5 rounded-full border border-gray-300 flex items-center justify-center bg-white status-dot-nonaktif">
                                        <div class="h-2.5 w-2.5 rounded-full bg-red-500 hidden"></div>
                                    </div>
                                    <div>
                                        <span class="flex items-center text-sm font-medium text-gray-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Tidak Aktif
                                        </span>
                                        <span class="mt-1 block text-xs text-gray-500">Siswa tidak aktif</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Status Read-Only untuk Petugas dan Orang Tua -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status Siswa
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                @if($siswa->status_aktif)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            <input type="text" 
                                value="{{ $siswa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}" 
                                class="pl-10 block w-full bg-gray-50 border border-gray-300 rounded-md h-10 focus:ring-0 focus:outline-none"
                                readonly disabled>
                            <input type="hidden" name="status_aktif" value="{{ $siswa->status_aktif }}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($isOrangTua)
                                Hubungi sekolah untuk mengubah status siswa
                            @else
                                Hanya admin yang dapat mengubah status siswa
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <!-- Field Tanggal Lulus - Hanya Admin yang bisa edit -->
                    @if($isAdmin)
                    <div>
                        <label for="tanggal_lulus" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Lulus
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" 
                                id="tanggal_lulus" 
                                name="tanggal_lulus" 
                                value="{{ old('tanggal_lulus', $siswa->tanggal_lulus ? date('Y-m-d', strtotime($siswa->tanggal_lulus)) : '') }}" 
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lulus') border-red-300 @enderror">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Kosongkan jika siswa belum lulus
                        </p>
                    </div>
                    @else
                    <!-- Tanggal Lulus Read-Only untuk Petugas dan Orang Tua -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Lulus
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" 
                                value="{{ $siswa->tanggal_lulus ? \Carbon\Carbon::parse($siswa->tanggal_lulus)->format('d F Y') : 'Belum lulus' }}" 
                                class="pl-10 block w-full bg-gray-50 border border-gray-300 rounded-md h-10 focus:ring-0 focus:outline-none"
                                readonly disabled>
                            <input type="hidden" name="tanggal_lulus" value="{{ $siswa->tanggal_lulus }}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($isOrangTua)
                                Hubungi sekolah untuk informasi tanggal lulus
                            @else
                                Hanya admin yang dapat mengubah tanggal lulus
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Alokasi Button - Hanya Admin -->
                @if($isAdmin && substr($siswa->id_siswa, 0, 1) == '6' && (!$siswa->detailSiswa || !$siswa->detailSiswa->kode_jurusan))
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Siswa belum dialokasikan ke jurusan
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>
                                    Siswa ini belum dialokasikan ke jurusan dan kelas. Alokasikan siswa untuk mengubah ID menjadi format baru sesuai jurusan.
                                </p>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('alokasi.unallocated') }}" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <i class="fas fa-user-check mr-2"></i> Alokasikan Siswa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='{{ route($indexRoute) }}'" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Information Section -->
    @if(!$isOrangTua)
    <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-history mr-2 text-blue-500"></i>Riwayat Perubahan Data
            </h2>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 rounded-lg p-5 text-blue-800 border-l-4 border-blue-400 shadow-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-2xl text-blue-500 mr-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base mb-2">Informasi Data Siswa</h3>
                        <ul class="text-sm space-y-2">
                            <li><span class="font-semibold">ID Siswa:</span> {{ $siswa->id_siswa }}</li>
                            <li>
                                <span class="font-semibold">Format ID:</span> 
                                @if(substr($siswa->id_siswa, 0, 1) == '6')
                                    @if($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan)
                                        Format baru (Sudah dialokasi)
                                    @else
                                        Format baru (Belum dialokasi)
                                    @endif
                                @else
                                    Format lama
                                @endif
                            </li>
                            <li><span class="font-semibold">Dibuat pada:</span> {{ $siswa->created_at ? $siswa->created_at->format('d/m/Y H:i') : 'Tidak ada data' }}</li>
                            <li><span class="font-semibold">Terakhir diperbarui:</span> {{ $siswa->updated_at ? $siswa->updated_at->format('d/m/Y H:i') : 'Tidak ada data' }}</li>
                            <li><span class="font-semibold">Perubahan sebelumnya:</span> {{ $siswa->updated_at && $siswa->updated_at != $siswa->created_at ? 'Ya' : 'Belum pernah diubah' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Double check access level - prevent any bypass attempts
    document.addEventListener('DOMContentLoaded', function() {
        const userLevel = '{{ $userLevel }}';
        if (userLevel === 'dokter') {
            console.warn('Access violation detected: Doctor trying to access edit student form');
            window.location.href = '{{ route("dokter.siswa.index") }}';
            return;
        }

        // Script untuk form dan handling radio button
        // Autoformat nama siswa (kapitalisasi setiap kata)
        const namaSiswaInput = document.getElementById('nama_siswa');
        if (namaSiswaInput) {
            namaSiswaInput.addEventListener('blur', function() {
                this.value = this.value.replace(/\w\S*/g, function(txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            });
        }
        
        // Radio button aktif/nonaktif styling - hanya untuk admin
        @if($isAdmin)
        function updateRadioStatus() {
            // Status Aktif
            const radioAktif = document.getElementById('status_aktif_1');
            const labelAktif = document.querySelector('.status-radio-aktif');
            const dotAktif = document.querySelector('.status-dot-aktif div');
            
            if (radioAktif && radioAktif.checked) {
                labelAktif.classList.add('border-2', 'border-green-500');
                dotAktif.classList.remove('hidden');
            } else if (radioAktif) {
                labelAktif.classList.remove('border-2', 'border-green-500');
                dotAktif.classList.add('hidden');
            }
            
            // Status Non-Aktif
            const radioNonaktif = document.getElementById('status_aktif_0');
            const labelNonaktif = document.querySelector('.status-radio-nonaktif');
            const dotNonaktif = document.querySelector('.status-dot-nonaktif div');
            
            if (radioNonaktif && radioNonaktif.checked) {
                labelNonaktif.classList.add('border-2', 'border-red-500');
                dotNonaktif.classList.remove('hidden');
            } else if (radioNonaktif) {
                labelNonaktif.classList.remove('border-2', 'border-red-500');
                dotNonaktif.classList.add('hidden');
            }
        }
        
        // Set status awal
        updateRadioStatus();
        
        // Listener untuk perubahan radio
        const radioAktif = document.getElementById('status_aktif_1');
        const radioNonaktif = document.getElementById('status_aktif_0');
        
        if (radioAktif) radioAktif.addEventListener('change', updateRadioStatus);
        if (radioNonaktif) radioNonaktif.addEventListener('change', updateRadioStatus);
        
        // Toggle field tanggal lulus berdasarkan status aktif
        const tanggalLulusField = document.getElementById('tanggal_lulus');
        
        function updateTanggalLulusVisibility() {
            if (radioNonaktif && radioNonaktif.checked && tanggalLulusField) {
                tanggalLulusField.required = true;
                tanggalLulusField.parentElement.parentElement.classList.remove('opacity-50');
                tanggalLulusField.parentElement.parentElement.querySelector('p').textContent = 'Wajib diisi jika status tidak aktif';
            } else if (tanggalLulusField) {
                tanggalLulusField.required = false;
                tanggalLulusField.parentElement.parentElement.classList.add('opacity-50');
                tanggalLulusField.parentElement.parentElement.querySelector('p').textContent = 'Kosongkan jika siswa belum lulus';
            }
        }
        
        // Set visibility awal
        updateTanggalLulusVisibility();
        
        // Listener untuk perubahan radio yang mempengaruhi tanggal lulus
        if (radioAktif) radioAktif.addEventListener('change', updateTanggalLulusVisibility);
        if (radioNonaktif) radioNonaktif.addEventListener('change', updateTanggalLulusVisibility);
        @endif
    });
</script>
@endpush

@endif {{-- End of access control for dokter --}}
@endsection