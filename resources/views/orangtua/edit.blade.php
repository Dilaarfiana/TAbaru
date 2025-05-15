@extends('layouts.admin')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-white px-6 py-4 border-b flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Edit Data Orang Tua</h2>
            </div>
            <a href="{{ route('orangtua.index') }}" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('orangtua.update', $orangTua->id_orang_tua) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Kolom Kiri: Informasi Dasar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- ID dan Siswa -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-5 rounded-lg border border-blue-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-blue-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                Informasi Dasar
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="group">
                                    <label for="id_siswa" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        Pilih Siswa <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <select id="id_siswa" name="id_siswa" required
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300 bg-white appearance-none">
                                            <option value="">-- Pilih Siswa --</option>
                                            @foreach($siswas as $siswa)
                                                <option value="{{ $siswa->id_siswa }}" {{ $orangTua->id_siswa == $siswa->id_siswa ? 'selected' : '' }}>
                                                    {{ $siswa->id_siswa }} - {{ $siswa->nama_siswa }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="group">
                                    <label for="id_orang_tua" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        ID Orang Tua
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-hover:text-blue-500 transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                            </svg>
                                        </div>
                                        <input type="text" id="id_orang_tua" value="{{ $orangTua->id_orang_tua }}" 
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 shadow-sm transition duration-200 hover:border-blue-300"
                                            readonly>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">ID dibuat otomatis dan tidak dapat diubah</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Akun -->
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-5 rounded-lg border border-purple-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-purple-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-600 mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                Informasi Akun
                            </h3>
                            
                            <div class="group">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-purple-600 transition duration-200">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-purple-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm transition duration-200 hover:border-purple-300"
                                        placeholder="Masukkan password baru">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                            </div>
                        </div>

                        <!-- Informasi Kontak -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-5 rounded-lg border border-green-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-green-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600 mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                Informasi Kontak
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="group">
                                    <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-green-600 transition duration-200">
                                        No. Telepon
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-green-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp', $orangTua->no_telp) }}"
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition duration-200 hover:border-green-300"
                                            placeholder="Contoh: +628123456789">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Format: +62 diikuti dengan nomor tanpa spasi</p>
                                </div>

                                <div class="group">
                                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-green-600 transition duration-200">
                                        Alamat Lengkap
                                    </label>
                                    <div class="relative">
                                        <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-green-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <textarea id="alamat" name="alamat" rows="3"
                                            class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition duration-200 hover:border-green-300"
                                            placeholder="Masukkan alamat lengkap">{{ old('alamat', $orangTua->alamat) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kolom Tengah & Kanan: Data Ayah & Ibu -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Data Ayah -->
                        <div class="bg-gradient-to-br from-blue-50 to-sky-50 p-5 rounded-lg border border-blue-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-blue-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                Data Ayah
                            </h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                <div class="group sm:col-span-2">
                                    <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        Nama Lengkap Ayah
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $orangTua->nama_ayah) }}"
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300"
                                            placeholder="Masukkan nama lengkap ayah">
                                    </div>
                                </div>
                                
                                <div class="group">
                                    <label for="tanggal_lahir_ayah" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        Tanggal Lahir
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="date" id="tanggal_lahir_ayah" name="tanggal_lahir_ayah" value="{{ old('tanggal_lahir_ayah', $orangTua->tanggal_lahir_ayah) }}"
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300">
                                    </div>
                                </div>
                                
                                <div class="group">
                                    <label for="pendidikan_ayah" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        Pendidikan Terakhir
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                            </svg>
                                        </div>
                                        <select id="pendidikan_ayah" name="pendidikan_ayah" 
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300 appearance-none">
                                            <option value="">-- Pilih Pendidikan --</option>
                                            <option value="SD" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'SD' ? 'selected' : '' }}>SD/Sederajat</option>
                                            <option value="SMP" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'SMP' ? 'selected' : '' }}>SMP/Sederajat</option>
                                            <option value="SMA" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                                            <option value="D1" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'D1' ? 'selected' : '' }}>D1</option>
                                            <option value="D2" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'D2' ? 'selected' : '' }}>D2</option>
                                            <option value="D3" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="D4/S1" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                            <option value="S2" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3" {{ old('pendidikan_ayah', $orangTua->pendidikan_ayah) == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="group">
                                    <label for="pekerjaan_ayah" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        Pekerjaan
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="pekerjaan_ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $orangTua->pekerjaan_ayah) }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300"
                                            placeholder="Masukkan pekerjaan ayah">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Data Ibu -->
                        <div class="bg-gradient-to-br from-pink-50 to-rose-50 p-5 rounded-lg border border-pink-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-pink-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-pink-100 text-pink-600 mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            Data Ibu
                        </h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="group sm:col-span-2">
                                <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-pink-600 transition duration-200">
                                    Nama Lengkap Ibu
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-pink-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $orangTua->nama_ibu) }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition duration-200 hover:border-pink-300"
                                        placeholder="Masukkan nama lengkap ibu">
                                </div>
                            </div>
                            
                            <div class="group">
                                <label for="tanggal_lahir_ibu" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-pink-600 transition duration-200">
                                    Tanggal Lahir
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-pink-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" id="tanggal_lahir_ibu" name="tanggal_lahir_ibu" value="{{ old('tanggal_lahir_ibu', $orangTua->tanggal_lahir_ibu) }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition duration-200 hover:border-pink-300">
                                </div>
                            </div>
                            
                            <div class="group">
                                <label for="pendidikan_ibu" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-pink-600 transition duration-200">
                                    Pendidikan Terakhir
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-pink-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998a12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                        </svg>
                                    </div>
                                    <select id="pendidikan_ibu" name="pendidikan_ibu" 
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition duration-200 hover:border-pink-300 appearance-none">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        <option value="SD" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'SD' ? 'selected' : '' }}>SD/Sederajat</option>
                                        <option value="SMP" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'SMP' ? 'selected' : '' }}>SMP/Sederajat</option>
                                        <option value="SMA" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                                        <option value="D1" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'D1' ? 'selected' : '' }}>D1</option>
                                        <option value="D2" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'D2' ? 'selected' : '' }}>D2</option>
                                        <option value="D3" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'D3' ? 'selected' : '' }}>D3</option>
                                        <option value="D4/S1" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                        <option value="S2" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ old('pendidikan_ibu', $orangTua->pendidikan_ibu) == 'S3' ? 'selected' : '' }}>S3</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="group">
                                <label for="pekerjaan_ibu" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-pink-600 transition duration-200">
                                    Pekerjaan
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 group-hover:text-pink-500 transition duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="pekerjaan_ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $orangTua->pekerjaan_ibu) }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition duration-200 hover:border-pink-300"
                                        placeholder="Masukkan pekerjaan ibu">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tips & Informasi -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-5 rounded-lg border border-amber-100 shadow-sm">
                        <h3 class="text-md font-semibold text-gray-800 mb-2 flex items-center">
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-600 mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            Informasi Penting
                        </h3>
                        
                        <div class="ml-10 text-sm text-gray-600 space-y-2">
                            <p class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Kolom bertanda <span class="text-red-500 font-medium">*</span> wajib diisi
                            </p>
                            <p class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                ID Orang Tua tidak dapat diubah
                            </p>
                            <p class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Kosongkan password jika tidak ingin mengubahnya
                            </p>
                        </div>
                    </div>
                </div>
                </div>
                
                <!-- Form Actions -->
                <div class="mt-8 border-t pt-6 flex justify-between items-center">
                    <div class="text-xs text-gray-500 italic">
                        <span class="inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Data disimpan dengan aman
                        </span>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="window.location.href='{{ route('orangtua.index') }}'" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Batal
                        </button>
                        <button type="submit" 
                            class="inline-flex items-center px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animasi label saat hover dan focus pada grup
        const groups = document.querySelectorAll('.group');
        groups.forEach(group => {
            const input = group.querySelector('input, select, textarea');
            const label = group.querySelector('label');
            
            if (input && label) {
                input.addEventListener('focus', () => {
                    const parentSection = group.closest('[class*="from-"]');
                    let colorClass = 'text-blue-600';
                    
                    if (parentSection) {
                        if (parentSection.classList.contains('from-blue-50')) colorClass = 'text-blue-600';
                        else if (parentSection.classList.contains('from-green-50')) colorClass = 'text-green-600';
                        else if (parentSection.classList.contains('from-purple-50')) colorClass = 'text-purple-600';
                        else if (parentSection.classList.contains('from-pink-50')) colorClass = 'text-pink-600';
                    }
                    
                    label.className = label.className.replace(/text-\w+-\d+/g, '');
                    label.classList.add(colorClass);
                });
                
                input.addEventListener('blur', () => {
                    if (!group.matches(':hover')) {
                        label.className = label.className.replace(/text-\w+-\d+/g, 'text-gray-700');
                    }
                });
            }
        });
    });
</script>
@endsection