{{-- File: resources/views/pemeriksaan_awal/create.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: NO ACCESS, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('page_title', 'Tambah Pemeriksaan Awal')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // BLOCK Orang tua - tidak boleh mengakses create sama sekali
    if ($isOrangTua) {
        return redirect()->route('dashboard.orangtua')
            ->with('error', 'Akses ditolak. Orang tua tidak memiliki akses untuk menambah pemeriksaan awal. Silakan gunakan menu "Ringkasan Kesehatan" untuk melihat informasi kesehatan anak Anda.');
    }
    
    // BLOCK Dokter - hanya read only, tidak bisa create
    if ($isDokter) {
        return redirect()->route('dokter.pemeriksaan_awal.index')
            ->with('error', 'Akses ditolak. Dokter hanya memiliki akses baca (read only) untuk pemeriksaan awal. Tidak dapat menambah data baru.');
    }
    
    // Only admin and petugas can create
    if (!$isAdmin && !$isPetugas) {
        return redirect()->route('dashboard')
            ->with('error', 'Anda tidak memiliki akses untuk menambah pemeriksaan awal.');
    }
    
    // Define routes based on user role
    $routes = [
        'admin' => [
            'index' => 'pemeriksaan_awal.index',
            'create' => 'pemeriksaan_awal.create',
            'store' => 'pemeriksaan_awal.store',
            'show' => 'pemeriksaan_awal.show',
            'edit' => 'pemeriksaan_awal.edit'
        ],
        'petugas' => [
            'index' => 'petugas.pemeriksaan_awal.index',
            'create' => 'petugas.pemeriksaan_awal.create',
            'store' => 'petugas.pemeriksaan_awal.store',
            'show' => 'petugas.pemeriksaan_awal.show',
            'edit' => 'petugas.pemeriksaan_awal.edit'
        ]
        // REMOVED: dokter and orang_tua routes for create - mereka tidak boleh create
    ];
    
    $currentRoutes = $routes[$userLevel];
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-white rounded-t-lg px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-clipboard-check text-blue-500 h-6 w-6 mr-3"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Tambah Pemeriksaan Awal Baru</h2>
                    <div class="flex items-center mt-1">
                        @if($isPetugas)
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                <i class="fas fa-user-tie mr-1"></i>Akses Petugas (CRU)
                            </span>
                        @elseif($isAdmin)
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                <i class="fas fa-user-shield mr-1"></i>Akses Admin (Full CRUD)
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @if(isset($id))
                <span class="bg-blue-100 text-blue-800 text-sm font-medium py-1 px-3 rounded-full">
                    <i class="fas fa-hashtag mr-1"></i> ID: {{ $id }}
                </span>
                @endif
                <a href="{{ route($currentRoutes['index']) }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            <!-- Access Level Info -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Informasi Hak Akses</h4>
                        <p class="text-xs text-blue-600 mt-1">
                            @if($isAdmin)
                                <strong>Administrator:</strong> Anda memiliki akses penuh untuk menambah pemeriksaan awal. 
                                Data ini akan menjadi bagian dari rekam medis siswa dan dapat diakses oleh tenaga medis lainnya.
                            @elseif($isPetugas)
                                <strong>Petugas UKS:</strong> Anda dapat menambah pemeriksaan awal baru untuk mendukung pelayanan kesehatan siswa. 
                                Pastikan data vital signs diisi dengan akurat untuk diagnosis yang tepat.
                            @endif
                        </p>
                        
                        <!-- Role-specific guidance -->
                        <div class="mt-2 p-2 bg-blue-100 border border-blue-300 rounded text-xs">
                            <p class="text-blue-800">
                                <i class="fas fa-lightbulb mr-1"></i>
                                <strong>Panduan:</strong> 
                                @if($isPetugas)
                                    Sebagai petugas UKS, fokus pada tanda vital dasar dan keluhan utama pasien untuk pemeriksaan awal yang efektif.
                                @elseif($isAdmin)
                                    Sebagai administrator, pastikan data lengkap dan akurat untuk mendukung sistem informasi kesehatan sekolah.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 flex items-center justify-between">
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
                    <button type="button" class="close-alert text-red-500 hover:text-red-600" onclick="this.parentElement.style.display='none'">
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
            
            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clipboard-check text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Informasi Pemeriksaan Awal</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Pemeriksaan awal akan dicatat dengan ID: <span class="font-mono font-bold bg-white px-2 py-1 rounded border">{{ $id ?? 'AUTO-GENERATE' }}</span>. 
                            Harap isi seluruh data yang diperlukan dengan lengkap dan akurat untuk mendukung diagnosis yang tepat.
                        </p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route($currentRoutes['store']) }}" method="POST" id="pemeriksaanForm">
                @csrf
                
                <!-- Fixed field names to match validation -->
                @if(isset($id))
                <input type="hidden" id="id_preawal" name="id_preawal" value="{{ $id }}">
                @endif
                
                <!-- Detail Pemeriksaan -->
                <div class="bg-white p-5 rounded-lg mb-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-gray-100 pb-3">
                        <i class="fas fa-file-medical text-blue-500 mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                        <span class="ml-auto text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            {{ $isAdmin ? 'Admin' : 'Petugas' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="id_detprx" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-link text-blue-500 mr-1"></i>
                                Detail Pemeriksaan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                
                                <select id="id_detprx" name="id_detprx" required
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none {{ $errors->has('id_detprx') ? 'border-red-500 bg-red-50' : '' }}">
                                    <option value="">-- Pilih Detail Pemeriksaan --</option>
                                    @if(isset($detailPemeriksaans))
                                        @foreach($detailPemeriksaans as $detailPemeriksaan)
                                            <option value="{{ $detailPemeriksaan->id_detprx }}" 
                                                    data-siswa="{{ $detailPemeriksaan->siswa->nama_siswa ?? 'N/A' }}"
                                                    data-tanggal="{{ \Carbon\Carbon::parse($detailPemeriksaan->tanggal_jam)->format('d/m/Y H:i') }}"
                                                    data-dokter="{{ $detailPemeriksaan->dokter->nama_dokter ?? 'N/A' }}"
                                                    data-status="{{ $detailPemeriksaan->status_pemeriksaan }}"
                                                    {{ old('id_detprx') == $detailPemeriksaan->id_detprx ? 'selected' : '' }}>
                                                {{ $detailPemeriksaan->id_detprx }} - {{ $detailPemeriksaan->siswa->nama_siswa ?? 'Nama Tidak Ditemukan' }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Tidak ada data detail pemeriksaan tersedia</option>
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            @if($errors->has('id_detprx'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('id_detprx') }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">Pilih detail pemeriksaan yang terkait dengan pemeriksaan awal ini</p>
                            
                            <!-- Detail Info Box (akan muncul setelah memilih) -->
                            <div id="detailInfo" class="hidden mt-3 p-3 bg-gray-50 rounded-md border">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <span class="font-medium text-gray-600">👤 Siswa:</span>
                                        <span id="infoSiswa" class="text-gray-800"></span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">📅 Tanggal:</span>
                                        <span id="infoTanggal" class="text-gray-800"></span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">👨‍⚕️ Dokter:</span>
                                        <span id="infoDokter" class="text-gray-800"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid untuk Detail Pemeriksaan dan Tanda Vital -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Kolom Kiri: Detail Pemeriksaan -->
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-gray-100 pb-3">
                            <i class="fas fa-clipboard-list text-green-500 mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Detail Pemeriksaan</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="pemeriksaan" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-stethoscope text-green-500 mr-1"></i>Pemeriksaan
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 pointer-events-none">
                                        <i class="fas fa-stethoscope text-gray-400"></i>
                                    </div>
                                    <textarea id="pemeriksaan" name="pemeriksaan" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('pemeriksaan') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="Masukkan detail pemeriksaan yang dilakukan...">{{ old('pemeriksaan') }}</textarea>
                                </div>
                                @if($errors->has('pemeriksaan'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('pemeriksaan') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="keluhan_dahulu" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-history text-green-500 mr-1"></i>Keluhan Dahulu
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-history text-gray-400"></i>
                                    </div>
                                    <input type="text" id="keluhan_dahulu" name="keluhan_dahulu" value="{{ old('keluhan_dahulu') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('keluhan_dahulu') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="Riwayat keluhan pasien sebelumnya">
                                </div>
                                @if($errors->has('keluhan_dahulu'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('keluhan_dahulu') }}</p>
                                @endif
                            </div>
                            
                            <div>
                                <label for="tipe" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag text-green-500 mr-1"></i>Tipe Pemeriksaan
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400"></i>
                                    </div>
                                    <select id="tipe" name="tipe"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none {{ $errors->has('tipe') ? 'border-red-500 bg-red-50' : '' }}">
                                        <option value="">-- Pilih Tipe Pemeriksaan --</option>
                                        <option value="1" {{ old('tipe') == '1' ? 'selected' : '' }}>1 - Umum</option>
                                        <option value="2" {{ old('tipe') == '2' ? 'selected' : '' }}>2 - Khusus</option>
                                        <option value="3" {{ old('tipe') == '3' ? 'selected' : '' }}>3 - Darurat</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                @if($errors->has('tipe'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('tipe') }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">
                                    Umum: pemeriksaan rutin | Khusus: pemeriksaan lanjutan | Darurat: kondisi urgent
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Tanda Vital -->
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-gray-100 pb-3">
                            <i class="fas fa-heartbeat text-red-500 mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Tanda Vital</h3>
                            <span class="ml-auto text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                Critical
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="suhu" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-thermometer-half text-red-500 mr-1"></i>Suhu (°C)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-thermometer-half text-gray-400"></i>
                                    </div>
                                    <input type="number" step="0.1" id="suhu" name="suhu" value="{{ old('suhu') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-red-500 focus:border-red-500 {{ $errors->has('suhu') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="36.5" min="30" max="45">
                                </div>
                                @if($errors->has('suhu'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('suhu') }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">Normal: 36.1°C - 37.2°C</p>
                            </div>
                            
                            <div>
                                <label for="nadi" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-heartbeat text-red-500 mr-1"></i>Nadi (bpm)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-heartbeat text-gray-400"></i>
                                    </div>
                                    <input type="number" id="nadi" name="nadi" value="{{ old('nadi') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-red-500 focus:border-red-500 {{ $errors->has('nadi') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="80" min="40" max="200">
                                </div>
                                @if($errors->has('nadi'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('nadi') }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">Normal: 60-100 bpm</p>
                            </div>
                            
                            <div>
                                <label for="tegangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tachometer-alt text-red-500 mr-1"></i>Tegangan (mmHg)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tachometer-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" id="tegangan" name="tegangan" value="{{ old('tegangan') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-red-500 focus:border-red-500 {{ $errors->has('tegangan') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="120/80" pattern="[0-9]{2,3}/[0-9]{2,3}">
                                </div>
                                @if($errors->has('tegangan'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('tegangan') }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">Format: Sistol/Diastol (contoh: 120/80)</p>
                            </div>
                            
                            <div>
                                <label for="pernapasan" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lungs text-red-500 mr-1"></i>Pernapasan (rpm)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lungs text-gray-400"></i>
                                    </div>
                                    <input type="number" id="pernapasan" name="pernapasan" value="{{ old('pernapasan') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-red-500 focus:border-red-500 {{ $errors->has('pernapasan') ? 'border-red-500 bg-red-50' : '' }}"
                                        placeholder="16" min="8" max="40">
                                </div>
                                @if($errors->has('pernapasan'))
                                    <p class="text-red-500 text-xs mt-1">{{ $errors->first('pernapasan') }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">Normal: 12-20 rpm</p>
                            </div>
                        </div>
                        
                        <!-- Status Indicator -->
                        <div id="vitalStatus" class="mt-4 p-3 rounded-md border hidden">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span class="text-sm font-medium" id="statusText"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Nyeri -->
                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b border-gray-100 pb-3">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Nyeri</h3>
                        <span class="ml-auto text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                            Pain Assessment
                        </span>
                    </div>
                    
                    <div class="mb-6">
                        <label for="status_nyeri" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hand-holding-medical text-yellow-500 mr-1"></i>Status Nyeri
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-pain text-gray-400"></i>
                            </div>
                            <select id="status_nyeri" name="status_nyeri"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 appearance-none {{ $errors->has('status_nyeri') ? 'border-red-500 bg-red-50' : '' }}">
                                <option value="">-- Pilih Level Nyeri --</option>
                                <option value="0" {{ old('status_nyeri') == '0' ? 'selected' : '' }}>0 - Tidak Ada Nyeri 😊</option>
                                <option value="1" {{ old('status_nyeri') == '1' ? 'selected' : '' }}>1 - Nyeri Ringan 😐</option>
                                <option value="2" {{ old('status_nyeri') == '2' ? 'selected' : '' }}>2 - Nyeri Sedang 😣</option>
                                <option value="3" {{ old('status_nyeri') == '3' ? 'selected' : '' }}>3 - Nyeri Berat 😭</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        @if($errors->has('status_nyeri'))
                            <p class="text-red-500 text-xs mt-1">{{ $errors->first('status_nyeri') }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">Gunakan skala 0-3 untuk menilai tingkat nyeri pasien</p>
                    </div>
                    
                    <div id="nyeriDetails" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-lg bg-gray-50 transition-all duration-300">
                        <div>
                            <label for="karakteristik" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info text-yellow-500 mr-1"></i>Karakteristik Nyeri
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-info text-gray-400"></i>
                                </div>
                                <input type="text" id="karakteristik" name="karakteristik" value="{{ old('karakteristik') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 {{ $errors->has('karakteristik') ? 'border-red-500 bg-red-50' : '' }}"
                                    placeholder="Tumpul, tajam, berdenyut, dll.">
                            </div>
                            @if($errors->has('karakteristik'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('karakteristik') }}</p>
                            @endif
                        </div>
                        
                        <div>
                            <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-yellow-500 mr-1"></i>Lokasi Nyeri
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 {{ $errors->has('lokasi') ? 'border-red-500 bg-red-50' : '' }}"
                                    placeholder="Bagian tubuh yang terasa nyeri">
                            </div>
                            @if($errors->has('lokasi'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('lokasi') }}</p>
                            @endif
                        </div>
                        
                        <div>
                            <label for="durasi" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock text-yellow-500 mr-1"></i>Durasi Nyeri
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-clock text-gray-400"></i>
                                </div>
                                <input type="text" id="durasi" name="durasi" value="{{ old('durasi') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 {{ $errors->has('durasi') ? 'border-red-500 bg-red-50' : '' }}"
                                    placeholder="Contoh: 2 jam, 30 menit">
                            </div>
                            @if($errors->has('durasi'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('durasi') }}</p>
                            @endif
                        </div>
                        
                        <div>
                            <label for="frekuensi" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-repeat text-yellow-500 mr-1"></i>Frekuensi Nyeri
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-repeat text-gray-400"></i>
                                </div>
                                <input type="text" id="frekuensi" name="frekuensi" value="{{ old('frekuensi') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-11 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 {{ $errors->has('frekuensi') ? 'border-red-500 bg-red-50' : '' }}"
                                    placeholder="Seberapa sering terjadi">
                            </div>
                            @if($errors->has('frekuensi'))
                                <p class="text-red-500 text-xs mt-1">{{ $errors->first('frekuensi') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Form Buttons -->
                <div class="mt-8 flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.location.href='{{ route($currentRoutes['index']) }}'" 
                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-times mr-2 text-gray-500"></i>
                        Batal
                    </button>
                    <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn">
                        <i class="fas fa-check mr-2"></i>
                        <span id="submitText">Simpan Pemeriksaan</span>
                    </button>
                </div>
            </form>
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

        // Detail pemeriksaan selection
        const detprxSelect = document.getElementById('id_detprx');
        const detailInfo = document.getElementById('detailInfo');
        
        if (detprxSelect && detailInfo) {
            detprxSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    document.getElementById('infoSiswa').textContent = selectedOption.dataset.siswa || 'N/A';
                    document.getElementById('infoTanggal').textContent = selectedOption.dataset.tanggal || 'N/A';
                    document.getElementById('infoDokter').textContent = selectedOption.dataset.dokter || 'N/A';
                    detailInfo.classList.remove('hidden');
                } else {
                    detailInfo.classList.add('hidden');
                }
            });
        }
        
        // Status nyeri dependencies
        const statusNyeriSelect = document.getElementById('status_nyeri');
        const nyeriDetails = document.getElementById('nyeriDetails');
        const nyeriInputs = nyeriDetails.querySelectorAll('input');
        
        // Change styling based on pain level
        statusNyeriSelect.addEventListener('change', function() {
            const nyeriValue = this.value;
            
            // Reset all styles
            nyeriDetails.classList.remove('opacity-50', 'bg-gray-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50', 'border-l-4', 'border-blue-500', 'border-yellow-500', 'border-red-500');
            
            if (nyeriValue === '' || nyeriValue === '0') {
                nyeriDetails.classList.add('opacity-50', 'bg-gray-50');
                nyeriInputs.forEach(input => {
                    input.classList.add('bg-gray-100');
                    input.disabled = true;
                    input.value = '';
                });
            } else {
                nyeriDetails.classList.remove('opacity-50');
                nyeriInputs.forEach(input => {
                    input.classList.remove('bg-gray-100');
                    input.disabled = false;
                });
                
                // Apply appropriate styling based on pain level
                if (nyeriValue === '1') {
                    nyeriDetails.classList.add('bg-blue-50', 'border-l-4', 'border-blue-500');
                } else if (nyeriValue === '2') {
                    nyeriDetails.classList.add('bg-yellow-50', 'border-l-4', 'border-yellow-500');
                } else if (nyeriValue === '3') {
                    nyeriDetails.classList.add('bg-red-50', 'border-l-4', 'border-red-500');
                }
            }
        });
        
        // Visual feedback for vital signs
        const vitalStatus = document.getElementById('vitalStatus');
        const statusText = document.getElementById('statusText');
        
        function updateVitalStatus() {
            const suhu = parseFloat(document.getElementById('suhu').value);
            const nadi = parseFloat(document.getElementById('nadi').value);
            const pernapasan = parseFloat(document.getElementById('pernapasan').value);
            
            let alerts = [];
            let normalCount = 0;
            
            if (suhu) {
                if (suhu > 37.5) alerts.push('🌡️ Demam tinggi');
                else if (suhu < 35.0) alerts.push('🥶 Hipotermia');
                else normalCount++;
            }
            
            if (nadi) {
                if (nadi > 100) alerts.push('💓 Takikardia');
                else if (nadi < 60) alerts.push('💙 Bradikardia');
                else normalCount++;
            }
            
            if (pernapasan) {
                if (pernapasan > 20) alerts.push('🫁 Takipnea');
                else if (pernapasan < 12) alerts.push('😴 Bradipnea');
                else normalCount++;
            }
            
            if (alerts.length > 0) {
                statusText.textContent = 'Perhatian: ' + alerts.join(', ');
                vitalStatus.className = 'mt-4 p-3 rounded-md border bg-yellow-50 border-yellow-200 text-yellow-800';
                vitalStatus.classList.remove('hidden');
            } else if (normalCount > 0) {
                statusText.textContent = '✅ Tanda vital dalam batas normal';
                vitalStatus.className = 'mt-4 p-3 rounded-md border bg-green-50 border-green-200 text-green-800';
                vitalStatus.classList.remove('hidden');
            } else {
                vitalStatus.classList.add('hidden');
            }
        }
        
        // Individual vital sign feedback
        const suhuInput = document.getElementById('suhu');
        suhuInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            this.classList.remove('border-red-500', 'bg-red-50', 'border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
            
            if (value) {
                if (value > 37.5) {
                    this.classList.add('border-red-500', 'bg-red-50');
                } else if (value < 35.0) {
                    this.classList.add('border-blue-500', 'bg-blue-50');
                } else if (value >= 36.1 && value <= 37.2) {
                    this.classList.add('border-green-500', 'bg-green-50');
                }
            }
            updateVitalStatus();
        });
        
        const nadiInput = document.getElementById('nadi');
        nadiInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            this.classList.remove('border-red-500', 'bg-red-50', 'border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
            
            if (value) {
                if (value > 100) {
                    this.classList.add('border-red-500', 'bg-red-50');
                } else if (value < 60) {
                    this.classList.add('border-blue-500', 'bg-blue-50');
                } else {
                    this.classList.add('border-green-500', 'bg-green-50');
                }
            }
            updateVitalStatus();
        });
        
        const pernapasanInput = document.getElementById('pernapasan');
        pernapasanInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            this.classList.remove('border-red-500', 'bg-red-50', 'border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
            
            if (value) {
                if (value > 20) {
                    this.classList.add('border-red-500', 'bg-red-50');
                } else if (value < 12) {
                    this.classList.add('border-blue-500', 'bg-blue-50');
                } else {
                    this.classList.add('border-green-500', 'bg-green-50');
                }
            }
            updateVitalStatus();
        });
        
        // Enhanced form field visual feedback
        const allInputs = document.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            // Add visual feedback on focus
            input.addEventListener('focus', function() {
                const container = this.closest('.relative');
                if (container) {
                    container.classList.add('ring-2', 'ring-blue-100', 'ring-opacity-50');
                }
            });
            
            input.addEventListener('blur', function() {
                const container = this.closest('.relative');
                if (container) {
                    container.classList.remove('ring-2', 'ring-blue-100', 'ring-opacity-50');
                }
            });
        });
        
        // Form validation before submit
        const form = document.getElementById('pemeriksaanForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        if (form && submitBtn && submitText) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('input[required], select[required]');
                let isValid = true;
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500', 'bg-red-50');
                        isValid = false;
                    } else {
                        field.classList.remove('border-red-500', 'bg-red-50');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    submitBtn.disabled = false;
                    submitText.innerHTML = '<i class="fas fa-check mr-2"></i>Simpan Pemeriksaan';
                    alert('Harap lengkapi semua field yang wajib diisi!');
                    
                    // Scroll to first error
                    const firstError = document.querySelector('.border-red-500');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
        }
        
        // Initialize form state
        if (statusNyeriSelect) statusNyeriSelect.dispatchEvent(new Event('change'));
        if (detprxSelect) detprxSelect.dispatchEvent(new Event('change'));
        if (suhuInput.value) suhuInput.dispatchEvent(new Event('input'));
        if (nadiInput.value) nadiInput.dispatchEvent(new Event('input'));
        if (pernapasanInput.value) pernapasanInput.dispatchEvent(new Event('input'));
        
        // Log user level untuk debugging
        console.log('User Level:', '{{ $userLevel }}');
        console.log('Access Level:', '{{ $isAdmin ? "Admin (Full CRUD)" : ($isPetugas ? "Petugas (CRU)" : "Unknown") }}');
    });
</script>
@endpush
@endsection