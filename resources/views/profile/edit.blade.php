@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center text-lg font-semibold">
                        @php
                            $userName = session('username', 'User');
                            $initials = '';
                            $nameParts = explode(' ', $userName);
                            foreach($nameParts as $part) {
                                if(!empty($part)) {
                                    $initials .= substr($part, 0, 1);
                                    if(strlen($initials) >= 2) break;
                                }
                            }
                            echo strtoupper($initials ?: 'U');
                        @endphp
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">Edit Profil</h1>
                        <p class="text-sm text-gray-600">
                            @if($userLevel === 'admin')
                                <i class="fas fa-user-shield text-gray-500 mr-1"></i> Administrator
                            @elseif($userLevel === 'petugas')
                                <i class="fas fa-clinic-medical text-green-500 mr-1"></i> Petugas UKS
                            @elseif($userLevel === 'dokter')
                                <i class="fas fa-user-md text-blue-500 mr-1"></i> Dokter
                            @elseif($userLevel === 'orang_tua')
                                <i class="fas fa-users text-purple-500 mr-1"></i> Orang Tua
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('profile.show') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-edit text-blue-500 mr-2"></i>
                Edit Informasi Profil
            </h3>
            <p class="text-sm text-gray-600 mt-1">Perbarui informasi profil Anda di bawah ini</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="px-6 py-6">
            @csrf
            @method('PUT')

            @if($userLevel === 'admin' || $userLevel === 'petugas')
                <!-- Petugas UKS Form -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="nama_petugas_uks" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" name="nama_petugas_uks" id="nama_petugas_uks" 
                                   value="{{ old('nama_petugas_uks', $profileData->nama_petugas_uks) }}" required
                                   maxlength="50"
                                   placeholder="Masukkan nama lengkap"
                                   class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('nama_petugas_uks') border-red-300 @enderror">
                        </div>
                        @error('nama_petugas_uks')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-1">
                            No. Telepon
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <input type="text" name="no_telp" id="no_telp" 
                                   value="{{ old('no_telp', $profileData->no_telp) }}"
                                   maxlength="15"
                                   placeholder="Contoh: 081234567890"
                                   class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('no_telp') border-red-300 @enderror">
                        </div>
                        @error('no_telp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            NIP
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input type="text" value="{{ $profileData->NIP }}" disabled
                                   class="pl-10 block w-full border border-gray-300 rounded-md h-10 bg-gray-50 text-gray-500 cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">NIP tidak dapat diubah</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <textarea name="alamat" id="alamat" rows="3"
                                      placeholder="Contoh: Jl. Merdeka No. 123, Jakarta"
                                      class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none @error('alamat') border-red-300 @enderror">{{ old('alamat', $profileData->alamat) }}</textarea>
                        </div>
                        @error('alamat')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            @elseif($userLevel === 'dokter')
                <!-- Dokter Form -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="Nama_Dokter" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Dokter <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" name="Nama_Dokter" id="Nama_Dokter" 
                                   value="{{ old('Nama_Dokter', $profileData->Nama_Dokter) }}" required
                                   maxlength="50"
                                   placeholder="Masukkan nama dokter"
                                   class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('Nama_Dokter') border-red-300 @enderror">
                        </div>
                        @error('Nama_Dokter')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Spesialisasi" class="block text-sm font-medium text-gray-700 mb-1">
                            Spesialisasi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <input type="text" name="Spesialisasi" id="Spesialisasi" 
                                   value="{{ old('Spesialisasi', $profileData->Spesialisasi) }}"
                                   maxlength="25"
                                   placeholder="Contoh: Dokter Umum"
                                   class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('Spesialisasi') border-red-300 @enderror">
                        </div>
                        @error('Spesialisasi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="No_Telp" class="block text-sm font-medium text-gray-700 mb-1">
                            No. Telepon
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <input type="text" name="No_Telp" id="No_Telp" 
                                   value="{{ old('No_Telp', $profileData->No_Telp) }}"
                                   maxlength="15"
                                   placeholder="Contoh: 081234567890"
                                   class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('No_Telp') border-red-300 @enderror">
                        </div>
                        @error('No_Telp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ID Dokter
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input type="text" value="{{ $profileData->Id_Dokter }}" disabled
                                   class="pl-10 block w-full border border-gray-300 rounded-md h-10 bg-gray-50 text-gray-500 cursor-not-allowed">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">ID Dokter tidak dapat diubah</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Alamat" class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <textarea name="Alamat" id="Alamat" rows="3"
                                      placeholder="Contoh: Jl. Kesehatan No. 456, Bandung"
                                      class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none @error('Alamat') border-red-300 @enderror">{{ old('Alamat', $profileData->Alamat) }}</textarea>
                        </div>
                        @error('Alamat')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            @elseif($userLevel === 'orang_tua')
                <!-- Orang Tua Form -->
                <div class="space-y-8">
                    <!-- Data Ayah -->
                    <div>
                        <h4 class="text-md font-medium text-gray-700 mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-male text-blue-500 mr-2"></i>
                            Data Ayah
                        </h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Ayah
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="nama_ayah" id="nama_ayah" 
                                           value="{{ old('nama_ayah', $profileData->nama_ayah) }}"
                                           maxlength="100"
                                           placeholder="Contoh: Ahmad Sutrisno"
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('nama_ayah') border-red-300 @enderror">
                                </div>
                                @error('nama_ayah')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_lahir_ayah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Lahir Ayah
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 012 0v4M16 7V3a1 1 0 012 0v4M5 9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V9z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="tanggal_lahir_ayah" id="tanggal_lahir_ayah" 
                                           value="{{ old('tanggal_lahir_ayah', $profileData->tanggal_lahir_ayah) }}"
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir_ayah') border-red-300 @enderror">
                                </div>
                                @error('tanggal_lahir_ayah')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pekerjaan_ayah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pekerjaan Ayah
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 002 2h2a2 2 0 002-2V8a2 2 0 00-2-2h-2zm-8 0V4a2 2 0 012-2v2H8z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" 
                                           value="{{ old('pekerjaan_ayah', $profileData->pekerjaan_ayah) }}"
                                           maxlength="50"
                                           placeholder="Contoh: PNS"
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('pekerjaan_ayah') border-red-300 @enderror">
                                </div>
                                @error('pekerjaan_ayah')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pendidikan_ayah" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pendidikan Ayah
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                    </div>
                                    <select name="pendidikan_ayah" id="pendidikan_ayah" 
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('pendidikan_ayah') border-red-300 @enderror">
                                        <option value="">Pilih Pendidikan</option>
                                        @foreach(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $pendidikan)
                                            <option value="{{ $pendidikan }}" 
                                                    {{ old('pendidikan_ayah', $profileData->pendidikan_ayah) == $pendidikan ? 'selected' : '' }}>
                                                {{ $pendidikan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('pendidikan_ayah')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Data Ibu -->
                    <div>
                        <h4 class="text-md font-medium text-gray-700 mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-female text-pink-500 mr-2"></i>
                            Data Ibu
                        </h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Ibu
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="nama_ibu" id="nama_ibu" 
                                           value="{{ old('nama_ibu', $profileData->nama_ibu) }}"
                                           maxlength="100"
                                           placeholder="Contoh: Siti Nurhaliza"
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('nama_ibu') border-red-300 @enderror">
                                </div>
                                @error('nama_ibu')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tanggal_lahir_ibu" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Lahir Ibu
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 012 0v4M16 7V3a1 1 0 012 0v4M5 9a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V9z" />
                                        </svg>
                                    </div>
                                    <input type="date" name="tanggal_lahir_ibu" id="tanggal_lahir_ibu" 
                                           value="{{ old('tanggal_lahir_ibu', $profileData->tanggal_lahir_ibu) }}"
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir_ibu') border-red-300 @enderror">
                                </div>
                                @error('tanggal_lahir_ibu')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pekerjaan_ibu" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pekerjaan Ibu
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 002 2h2a2 2 0 002-2V8a2 2 0 00-2-2h-2zm-8 0V4a2 2 0 012-2v2H8z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" 
                                           value="{{ old('pekerjaan_ibu', $profileData->pekerjaan_ibu) }}"
                                           maxlength="50"
                                           placeholder="Contoh: Ibu Rumah Tangga"
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('pekerjaan_ibu') border-red-300 @enderror">
                                </div>
                                @error('pekerjaan_ibu')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pendidikan_ibu" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pendidikan Ibu
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                    </div>
                                    <select name="pendidikan_ibu" id="pendidikan_ibu" 
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('pendidikan_ibu') border-red-300 @enderror">
                                        <option value="">Pilih Pendidikan</option>
                                        @foreach(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $pendidikan)
                                            <option value="{{ $pendidikan }}" 
                                                    {{ old('pendidikan_ibu', $profileData->pendidikan_ibu) == $pendidikan ? 'selected' : '' }}>
                                                {{ $pendidikan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('pendidikan_ibu')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Kontak & Alamat -->
                    <div>
                        <h4 class="text-md font-medium text-gray-700 mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-home text-gray-500 mr-2"></i>
                            Kontak & Alamat
                        </h4>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="no_telp_orangtua" class="block text-sm font-medium text-gray-700 mb-1">
                                    No. Telepon
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="no_telp" id="no_telp_orangtua" 
                                           value="{{ old('no_telp', $profileData->no_telp) }}"
                                           maxlength="20"
                                           placeholder="Contoh: 081234567890"
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('no_telp') border-red-300 @enderror">
                                </div>
                                @error('no_telp')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(isset($siswaData))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Siswa
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" value="{{ $siswaData->nama_siswa ?? '-' }}" disabled
                                           class="pl-10 block w-full border border-gray-300 rounded-md h-10 bg-gray-50 text-gray-500 cursor-not-allowed">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Data siswa tidak dapat diubah</p>
                            </div>
                            @endif

                            <div class="sm:col-span-2">
                                <label for="alamat_orangtua" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <textarea name="alamat" id="alamat_orangtua" rows="3"
                                              placeholder="Contoh: Jl. Mawar No. 789, Yogyakarta"
                                              class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none @error('alamat') border-red-300 @enderror">{{ old('alamat', $profileData->alamat) }}</textarea>
                                </div>
                                @error('alamat')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('profile.show') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        <i class="fas fa-save mr-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation enhancement
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Perubahan';
                }, 3000);
            }
        });
    }

    // Phone number formatting
    const phoneInputs = document.querySelectorAll('input[name="no_telp"], input[name="No_Telp"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 15) {
                this.value = this.value.substr(0, 15);
            }
        });
    });

    // Date validation
    const birthDateInputs = document.querySelectorAll('input[name="tanggal_lahir_ayah"], input[name="tanggal_lahir_ibu"]');
    birthDateInputs.forEach(input => {
        input.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            
            if (selectedDate > today) {
                alert('Tanggal lahir tidak boleh di masa depan');
                this.value = '';
            }
        });
    });
});
</script>
@endpush
@endsection