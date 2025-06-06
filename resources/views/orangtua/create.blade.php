@extends('layouts.app')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-white px-6 py-4 border-b flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-user-friends text-blue-500 mr-3 text-xl"></i>
                <h2 class="text-xl font-medium text-gray-800">Tambah Data Orang Tua</h2>
            </div>
            <a href="{{ route('orangtua.index') }}" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
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
            
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('orangtua.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Kolom Kiri: Informasi Dasar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- ID dan Siswa -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-5 rounded-lg border border-blue-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-blue-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600 mr-2">
                                    <i class="fas fa-info-circle"></i>
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
                                            <i class="fas fa-user-graduate text-gray-400 group-hover:text-blue-500 transition duration-200"></i>
                                        </div>
                                        <select id="id_siswa" name="id_siswa" required
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300 bg-white appearance-none">
                                            <option value="">-- Pilih Siswa --</option>
                                            @foreach($siswas->sortBy('nama_siswa') as $siswa)
                                                <option 
                                                    value="{{ $siswa->id_siswa }}" 
                                                    data-tanggal="{{ $siswa->tanggal_lahir ? date('dmY', strtotime($siswa->tanggal_lahir)) : '' }}"
                                                    data-tanggal-format="{{ $siswa->tanggal_lahir ? date('d/m/Y', strtotime($siswa->tanggal_lahir)) : '' }}"
                                                    {{ old('id_siswa') == $siswa->id_siswa ? 'selected' : '' }}
                                                >
                                                    {{ $siswa->id_siswa }} - {{ $siswa->nama_siswa }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-blue-600">Password login akan otomatis dibuat dari tanggal lahir siswa</p>
                                </div>

                                <div class="group">
                                    <label for="id_orang_tua" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        ID Orang Tua
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none group-hover:text-blue-500 transition duration-200">
                                            <i class="fas fa-id-card text-gray-400 group-hover:text-blue-500 transition duration-200"></i>
                                        </div>
                                        <input type="text" id="id_orang_tua" name="id_orang_tua" value="{{ $nextId ?? old('id_orang_tua') }}" 
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm transition duration-200 hover:border-blue-300"
                                            placeholder="OT001" maxlength="10" readonly>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">ID dibuat otomatis (format: OTxxx)</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Akun -->
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-5 rounded-lg border border-purple-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-purple-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-purple-100 text-purple-600 mr-2">
                                    <i class="fas fa-lock"></i>
                                </span>
                                Informasi Akun
                            </h3>
                            
                            <div class="space-y-3">
                                <div class="group">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-purple-600 transition duration-200">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-key text-gray-400 group-hover:text-purple-500 transition duration-200"></i>
                                        </div>
                                        <input type="password" id="password" name="password" 
                                            class="pl-10 pr-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm transition duration-200 hover:border-purple-300"
                                            placeholder="Otomatis dari tanggal lahir siswa">
                                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none">
                                            <i class="fas fa-eye text-gray-400 hover:text-purple-500"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-blue-50 p-3 rounded-md border border-blue-100 text-sm">
                                    <div class="flex">
                                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2 flex-shrink-0"></i>
                                        <div>
                                            <p class="text-blue-800 font-medium text-xs">Password Otomatis</p>
                                            <p class="text-blue-700 text-xs mt-1">Password akan otomatis dibuat dari tanggal lahir siswa dengan format DDMMYYYY.</p>
                                            <p id="password-info" class="text-blue-700 text-xs mt-1">Contoh: Jika tanggal lahir siswa adalah 15 Maret 2010, maka password-nya adalah <strong>15032010</strong></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center mt-2">
                                    <label class="flex items-center text-sm text-gray-600">
                                        <input type="checkbox" id="use-custom-password" name="use_custom_password" class="mr-2 h-4 w-4 text-purple-600 focus:ring-purple-500">
                                        Gunakan password kustom
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kontak -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-5 rounded-lg border border-green-100 shadow-sm">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 pb-2 border-b border-green-200 flex items-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600 mr-2">
                                    <i class="fas fa-address-book"></i>
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
                                            <i class="fas fa-phone text-gray-400 group-hover:text-green-500 transition duration-200"></i>
                                        </div>
                                        <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp', '+62') }}"
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
                                            <i class="fas fa-map-marker-alt text-gray-400 group-hover:text-green-500 transition duration-200"></i>
                                        </div>
                                        <textarea id="alamat" name="alamat" rows="3"
                                            class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition duration-200 hover:border-green-300"
                                            placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
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
                                    <i class="fas fa-male"></i>
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
                                            <i class="fas fa-user text-gray-400 group-hover:text-blue-500 transition duration-200"></i>
                                        </div>
                                        <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}"
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
                                            <i class="fas fa-calendar-alt text-gray-400 group-hover:text-blue-500 transition duration-200"></i>
                                        </div>
                                        <input type="date" id="tanggal_lahir_ayah" name="tanggal_lahir_ayah" value="{{ old('tanggal_lahir_ayah') }}"
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300">
                                    </div>
                                </div>
                                
                                <div class="group">
                                    <label for="pendidikan_ayah" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        Pendidikan Terakhir
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-graduation-cap text-gray-400 group-hover:text-blue-500 transition duration-200"></i>
                                        </div>
                                        <select id="pendidikan_ayah" name="pendidikan_ayah" 
                                            class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition duration-200 hover:border-blue-300 appearance-none">
                                            <option value="">-- Pilih Pendidikan --</option>
                                            <option value="SD" {{ old('pendidikan_ayah') == 'SD' ? 'selected' : '' }}>SD/Sederajat</option>
                                            <option value="SMP" {{ old('pendidikan_ayah') == 'SMP' ? 'selected' : '' }}>SMP/Sederajat</option>
                                            <option value="SMA" {{ old('pendidikan_ayah') == 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                                            <option value="D1" {{ old('pendidikan_ayah') == 'D1' ? 'selected' : '' }}>D1</option>
                                            <option value="D2" {{ old('pendidikan_ayah') == 'D2' ? 'selected' : '' }}>D2</option>
                                            <option value="D3" {{ old('pendidikan_ayah') == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="D4/S1" {{ old('pendidikan_ayah') == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                            <option value="S2" {{ old('pendidikan_ayah') == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3" {{ old('pendidikan_ayah') == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="group">
                                    <label for="pekerjaan_ayah" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-blue-600 transition duration-200">
                                        Pekerjaan
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-briefcase text-gray-400 group-hover:text-blue-500 transition duration-200"></i>
                                        </div>
                                        <input type="text" id="pekerjaan_ayah" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}"
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
                                <i class="fas fa-female"></i>
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
                                        <i class="fas fa-user text-gray-400 group-hover:text-pink-500 transition duration-200"></i>
                                    </div>
                                    <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}"
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
                                        <i class="fas fa-calendar-alt text-gray-400 group-hover:text-pink-500 transition duration-200"></i>
                                    </div>
                                    <input type="date" id="tanggal_lahir_ibu" name="tanggal_lahir_ibu" value="{{ old('tanggal_lahir_ibu') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition duration-200 hover:border-pink-300">
                                </div>
                            </div>
                            
                            <div class="group">
                                <label for="pendidikan_ibu" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-pink-600 transition duration-200">
                                    Pendidikan Terakhir
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-graduation-cap text-gray-400 group-hover:text-pink-500 transition duration-200"></i>
                                    </div>
                                    <select id="pendidikan_ibu" name="pendidikan_ibu" 
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition duration-200 hover:border-pink-300 appearance-none">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        <option value="SD" {{ old('pendidikan_ibu') == 'SD' ? 'selected' : '' }}>SD/Sederajat</option>
                                        <option value="SMP" {{ old('pendidikan_ibu') == 'SMP' ? 'selected' : '' }}>SMP/Sederajat</option>
                                        <option value="SMA" {{ old('pendidikan_ibu') == 'SMA' ? 'selected' : '' }}>SMA/Sederajat</option>
                                        <option value="D1" {{ old('pendidikan_ibu') == 'D1' ? 'selected' : '' }}>D1</option>
                                        <option value="D2" {{ old('pendidikan_ibu') == 'D2' ? 'selected' : '' }}>D2</option>
                                        <option value="D3" {{ old('pendidikan_ibu') == 'D3' ? 'selected' : '' }}>D3</option>
                                        <option value="D4/S1" {{ old('pendidikan_ibu') == 'D4/S1' ? 'selected' : '' }}>D4/S1</option>
                                        <option value="S2" {{ old('pendidikan_ibu') == 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ old('pendidikan_ibu') == 'S3' ? 'selected' : '' }}>S3</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="group">
                                <label for="pekerjaan_ibu" class="block text-sm font-medium text-gray-700 mb-1 group-hover:text-pink-600 transition duration-200">
                                    Pekerjaan
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-briefcase text-gray-400 group-hover:text-pink-500 transition duration-200"></i>
                                    </div>
                                    <input type="text" id="pekerjaan_ibu" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}"
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
                                <i class="fas fa-info-circle"></i>
                            </span>
                            Informasi Penting
                        </h3>
                        
                        <div class="ml-10 text-sm text-gray-600 space-y-2">
                            <p class="flex items-start">
                                <i class="fas fa-check-circle text-amber-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                Kolom bertanda <span class="text-red-500 font-medium">*</span> wajib diisi
                            </p>
                            <p class="flex items-start">
                                <i class="fas fa-check-circle text-amber-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                ID Orang Tua dibuat otomatis
                            </p>
                            <p class="flex items-start">
                                <i class="fas fa-check-circle text-amber-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                Password default menggunakan tanggal lahir siswa (format: DDMMYYYY)
                            </p>
                            <p class="flex items-start">
                                <i class="fas fa-check-circle text-amber-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                Centang "Gunakan password kustom" untuk mengganti password default
                            </p>
                            <p class="flex items-start">
                                <i class="fas fa-check-circle text-amber-500 mr-2 mt-0.5 flex-shrink-0"></i>
                                Data yang telah disimpan dapat diubah melalui menu edit
                            </p>
                        </div>
                    </div>
                </div>
                </div>
                
                <!-- Form Actions -->
                <div class="mt-8 border-t pt-6 flex justify-between items-center">
                    <div class="text-xs text-gray-500 italic">
                        <span class="inline-flex items-center">
                            <i class="fas fa-lock text-gray-400 mr-1"></i>
                            Data disimpan dengan aman
                        </span>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="window.location.href='{{ route('orangtua.index') }}'" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-times mr-2 text-gray-500"></i>
                            Batal
                        </button>
                        <button type="submit" 
                            class="inline-flex items-center px-5 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get references to the DOM elements
        const siswaSelect = document.getElementById('id_siswa');
        const passwordInput = document.getElementById('password');
        const passwordInfo = document.getElementById('password-info');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const useCustomPasswordCheckbox = document.getElementById('use-custom-password');
        
        // Function to get formatted date text
        function formatDateText(date) {
            if (!date) return '';
            const parts = date.split('/');
            if (parts.length !== 3) return date;
            
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const day = parseInt(parts[0]);
            const month = parseInt(parts[1]) - 1;
            const year = parts[2];
            
            return `${day} ${months[month]} ${year}`;
        }
        
        // Function to set password based on selected student's birthdate
        function setPasswordFromBirthdate() {
            const selectedOption = siswaSelect.options[siswaSelect.selectedIndex];
            const birthdate = selectedOption.getAttribute('data-tanggal');
            const birthdateFormat = selectedOption.getAttribute('data-tanggal-format');
            
            if (birthdate && !useCustomPasswordCheckbox.checked) {
                passwordInput.value = birthdate;
                passwordInput.readOnly = true;
                passwordInput.classList.add('bg-blue-50', 'border-blue-300');
                
                if (birthdateFormat) {
                    const formattedDate = formatDateText(birthdateFormat);
                    passwordInfo.innerHTML = `Password: <strong>${birthdate}</strong> (dari tanggal lahir siswa: ${formattedDate})`;
                }
            } else if (!useCustomPasswordCheckbox.checked) {
                passwordInput.value = '';
                passwordInput.readOnly = true;
                passwordInput.placeholder = 'Tanggal lahir siswa tidak tersedia';
                passwordInput.classList.remove('bg-blue-50', 'border-blue-300');
                passwordInfo.innerHTML = 'Password tidak dapat dibuat otomatis karena tanggal lahir siswa tidak tersedia';
            }
        }
        
        // Initial setup for password field
        setPasswordFromBirthdate();
        
        // Update password when student selection changes
        siswaSelect.addEventListener('change', setPasswordFromBirthdate);
        
        // Toggle password visibility
        togglePasswordBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Update icon
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Toggle custom password
        useCustomPasswordCheckbox.addEventListener('change', function() {
            if (this.checked) {
                passwordInput.readOnly = false;
                passwordInput.value = '';
                passwordInput.placeholder = 'Masukkan password kustom';
                passwordInput.classList.remove('bg-blue-50', 'border-blue-300');
                passwordInput.focus();
            } else {
                setPasswordFromBirthdate();
            }
        });
        
        // Animasi label saat hover dan focus pada grup
        const groups = document.querySelectorAll('.group');
        groups.forEach(group => {
            const input = group.querySelector('input, select, textarea');
            const label = group.querySelector('label');
            
            if (input && label) {
                input.addEventListener('focus', () => {
                    const parentSection = group.closest('[class*="from-"]');
                    const colorClass = 
                        parentSection.classList.contains('from-blue-50') ? 'text-blue-600' : 
                        parentSection.classList.contains('from-green-50') ? 'text-green-600' : 
                        parentSection.classList.contains('from-purple-50') ? 'text-purple-600' : 
                        parentSection.classList.contains('from-pink-50') ? 'text-pink-600' : 
                        'text-blue-600';
                    
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