{{-- File: resources/views/siswa/create.blade.php --}}
{{-- HANYA ADMIN YANG BOLEH AKSES CREATE SISWA --}}
@extends('layouts.app')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
@endphp

{{-- BLOCK ACCESS FOR NON-ADMIN USERS --}}
@if(!$isAdmin)
    <div class="p-4 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
            <div class="bg-red-50 border-l-4 border-red-500 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-red-800">Akses Ditolak</h3>
                        @if($isOrangTua)
                            <p class="text-sm text-red-700 mt-2">
                                Anda tidak memiliki izin untuk menambah data siswa. 
                                Sebagai orang tua, Anda hanya dapat mengakses dan mengedit data siswa Anda sendiri.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('orangtua.siswa.show') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                    <i class="fas fa-child mr-2"></i>
                                    Lihat Data Siswa Saya
                                </a>
                                <a href="{{ route('dashboard.orangtua') }}" 
                                   class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-home mr-2"></i>
                                    Kembali ke Dashboard
                                </a>
                            </div>
                        @elseif($isPetugas)
                            <p class="text-sm text-red-700 mt-2">
                                Anda tidak memiliki izin untuk menambah data siswa baru. 
                                Sebagai petugas, Anda hanya dapat melihat dan mengedit data siswa yang sudah ada.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('petugas.siswa.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                    <i class="fas fa-user-graduate mr-2"></i>
                                    Lihat Daftar Siswa
                                </a>
                                <a href="{{ route('dashboard.petugas') }}" 
                                   class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-home mr-2"></i>
                                    Kembali ke Dashboard
                                </a>
                            </div>
                        @elseif($isDokter)
                            <p class="text-sm text-red-700 mt-2">
                                Anda tidak memiliki izin untuk menambah data siswa baru. 
                                Sebagai dokter, Anda hanya dapat melihat data siswa untuk keperluan medis.
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
                        @else
                            <p class="text-sm text-red-700 mt-2">
                                Anda tidak memiliki izin untuk mengakses halaman ini.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('dashboard') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-home mr-2"></i>
                                    Kembali ke Dashboard
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Auto redirect berdasarkan role
    setTimeout(function() {
        @if($isOrangTua)
            window.location.href = '{{ route("orangtua.siswa.show") }}';
        @elseif($isPetugas)
            window.location.href = '{{ route("petugas.siswa.index") }}';
        @elseif($isDokter)
            window.location.href = '{{ route("dokter.siswa.index") }}';
        @else
            window.location.href = '{{ route("dashboard") }}';
        @endif
    }, 3000);
    
    // Show countdown
    let countdown = 3;
    const countdownElement = document.createElement('div');
    countdownElement.className = 'mt-4 text-sm text-red-600';
    countdownElement.innerHTML = '<i class="fas fa-clock mr-1"></i>Akan dialihkan dalam <span id="countdown">3</span> detik...';
    document.querySelector('.border-red-500 .ml-4').appendChild(countdownElement);
    
    const countdownTimer = setInterval(function() {
        countdown--;
        document.getElementById('countdown').textContent = countdown;
        if (countdown <= 0) {
            clearInterval(countdownTimer);
        }
    }, 1000);
    </script>
    @endpush

@else
{{-- NORMAL CONTENT FOR ADMIN ONLY --}}

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Admin Access Indicator -->
    <div class="max-w-5xl mx-auto mb-4">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-shield text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Akses Administrator:</strong> Anda dapat menambah siswa baru ke sistem. 
                        Setelah siswa ditambahkan, lakukan alokasi ke jurusan dan kelas melalui menu Alokasi.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Tambah Siswa Baru</h2>
                <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                    <i class="fas fa-user-shield mr-1"></i>Admin Only
                </span>
            </div>
            <a href="{{ route('siswa.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
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
                            ID Siswa akan dibuat otomatis dengan format <span class="font-mono">6 + tahun (yy) + nomor urut (001)</span>.
                            Setelah dialokasikan ke jurusan, ID akan berubah menjadi <span class="font-mono">6 + kode jurusan + tahun (yy) + nomor urut (001)</span>.
                            Pastikan mengisi nama siswa dengan lengkap sesuai dokumen resmi.
                            Data yang wajib diisi ditandai dengan <span class="text-red-500">*</span>.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf
                <!-- Hidden field for id_siswa -->
                <input type="hidden" name="id_siswa" value="{{ $nextId }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <!-- ID Siswa (Auto-generated, read-only) -->
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
                                value="{{ $nextId }}" 
                                class="pl-10 block w-full bg-gray-50 border-0 rounded-md h-10 focus:ring-0 focus:outline-none"
                                readonly disabled>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            ID dibuat otomatis dengan format: 6 + tahun (yy) + nomor urut (001)
                        </p>
                    </div>
                    
                    <!-- Nama Siswa -->
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
                                value="{{ old('nama_siswa') }}" 
                                required 
                                maxlength="50" 
                                placeholder="Masukkan nama lengkap siswa"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('nama_siswa') border-red-300 @enderror">
                        </div>
                    </div>

                    <!-- Tempat Lahir -->
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
                                value="{{ old('tempat_lahir') }}" 
                                maxlength="30" 
                                placeholder="Contoh: Magelang"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tempat_lahir') border-red-300 @enderror">
                        </div>
                    </div>
                    
                    <!-- Tanggal Lahir -->
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
                                value="{{ old('tanggal_lahir') }}" 
                                max="{{ date('Y-m-d') }}"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir') border-red-300 @enderror">
                        </div>
                    </div>
                    
                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Kelamin
                        </label>
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
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tanggal Masuk -->
                    <div>
                        <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Masuk
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" 
                                id="tanggal_masuk" 
                                name="tanggal_masuk" 
                                value="{{ old('tanggal_masuk', date('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_masuk') border-red-300 @enderror">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Default: tanggal hari ini
                        </p>
                    </div>
                    
                    <!-- Status Aktif (Design Sederhana yang Dapat Diklik) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Status Siswa
                        </label>
                        <div class="flex space-x-4">
                            <!-- Radio untuk Aktif -->
                            <div class="w-1/2">
                                <input type="radio" id="status_aktif_1" name="status_aktif" value="1" class="hidden" 
                                    {{ old('status_aktif', '1') == '1' ? 'checked' : '' }}>
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
                                    {{ old('status_aktif') == '0' ? 'checked' : '' }}>
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
                        <p class="text-xs text-gray-500 mt-2">
                            Default: Status "Aktif" untuk siswa baru
                        </p>
                    </div>
                    
                    <!-- Field Tanggal Lulus -->
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
                                value="{{ old('tanggal_lulus') }}" 
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lulus') border-red-300 @enderror">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Kosongkan jika siswa belum lulus
                        </p>
                    </div>
                </div>
                
                <!-- Form Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='{{ route('siswa.index') }}'" 
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
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-book mr-2 text-blue-500"></i>Panduan Pengisian Data Siswa
            </h2>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 rounded-lg p-5 text-blue-800 border-l-4 border-blue-400 shadow-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-lightbulb text-2xl text-blue-500 mr-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base mb-2">Tips Pengisian Form Siswa</h3>
                        <ul class="list-disc list-inside text-sm space-y-2 ml-2">
                            <li>ID Siswa akan terisi otomatis dengan format 6 + tahun (yy) + nomor urut (001).</li>
                            <li>Setelah dialokasikan ke jurusan, ID akan berubah menjadi 6 + kode jurusan + tahun (yy) + nomor urut (001).</li>
                            <li>Nama Siswa sebaiknya diisi dengan nama lengkap sesuai dokumen resmi.</li>
                            <li>Tanggal Lahir membantu dalam perhitungan usia dan administrasi lainnya.</li>
                            <li>Tanggal Masuk menunjukkan kapan siswa mulai terdaftar di sekolah.</li>
                            <li>Tanggal Lulus diisi jika siswa sudah lulus atau tidak aktif.</li>
                            <li>Status Aktif default adalah "Aktif" untuk siswa baru yang mendaftar.</li>
                            <li>Data yang diisi dengan lengkap akan memudahkan dalam pencarian dan pelaporan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Double check access level - prevent any bypass attempts
    document.addEventListener('DOMContentLoaded', function() {
        const userLevel = '{{ $userLevel }}';
        if (userLevel !== 'admin') {
            console.warn('Access violation detected: Non-admin trying to access create student form');
            @if($isOrangTua)
                window.location.href = '{{ route("orangtua.siswa.show") }}';
            @elseif($isPetugas)
                window.location.href = '{{ route("petugas.siswa.index") }}';
            @elseif($isDokter)
                window.location.href = '{{ route("dokter.siswa.index") }}';
            @else
                window.location.href = '{{ route("dashboard") }}';
            @endif
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
        
        // Radio button aktif/nonaktif styling
        function updateRadioStatus() {
            // Status Aktif
            const radioAktif = document.getElementById('status_aktif_1');
            const labelAktif = document.querySelector('.status-radio-aktif');
            const dotAktif = document.querySelector('.status-dot-aktif div');
            
            if (radioAktif.checked) {
                labelAktif.classList.add('border-2', 'border-green-500');
                dotAktif.classList.remove('hidden');
            } else {
                labelAktif.classList.remove('border-2', 'border-green-500');
                dotAktif.classList.add('hidden');
            }
            
            // Status Non-Aktif
            const radioNonaktif = document.getElementById('status_aktif_0');
            const labelNonaktif = document.querySelector('.status-radio-nonaktif');
            const dotNonaktif = document.querySelector('.status-dot-nonaktif div');
            
            if (radioNonaktif.checked) {
                labelNonaktif.classList.add('border-2', 'border-red-500');
                dotNonaktif.classList.remove('hidden');
            } else {
                labelNonaktif.classList.remove('border-2', 'border-red-500');
                dotNonaktif.classList.add('hidden');
            }
        }
        
        // Set status awal
        updateRadioStatus();
        
        // Listener untuk perubahan radio
        document.getElementById('status_aktif_1').addEventListener('change', updateRadioStatus);
        document.getElementById('status_aktif_0').addEventListener('change', updateRadioStatus);
        
        // Toggle field tanggal lulus berdasarkan status aktif
        const radioAktif = document.getElementById('status_aktif_1');
        const radioNonaktif = document.getElementById('status_aktif_0');
        const tanggalLulusField = document.getElementById('tanggal_lulus');
        
        function updateTanggalLulusVisibility() {
            if (radioNonaktif.checked) {
                tanggalLulusField.required = true;
                tanggalLulusField.parentElement.parentElement.classList.remove('opacity-50');
                tanggalLulusField.parentElement.parentElement.querySelector('p').textContent = 'Wajib diisi jika status tidak aktif';
            } else {
                tanggalLulusField.required = false;
                tanggalLulusField.parentElement.parentElement.classList.add('opacity-50');
                tanggalLulusField.parentElement.parentElement.querySelector('p').textContent = 'Kosongkan jika siswa belum lulus';
            }
        }
        
        // Set visibility awal
        updateTanggalLulusVisibility();
        
        // Listener untuk perubahan radio yang mempengaruhi tanggal lulus
       radioAktif.addEventListener('change', updateTanggalLulusVisibility);
       radioNonaktif.addEventListener('change', updateTanggalLulusVisibility);
   });
</script>
@endpush

@endif {{-- End of access control for non-admin --}}
@endsection