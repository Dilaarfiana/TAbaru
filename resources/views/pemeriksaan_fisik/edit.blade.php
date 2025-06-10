@extends('layouts.app')

@section('page_title', 'Edit Pemeriksaan Fisik')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-t-md px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-edit text-white mr-3 text-xl"></i>
                <h2 class="text-xl font-bold text-white">Edit Pemeriksaan Fisik</h2>
                <span class="ml-3 px-3 py-1 bg-white text-orange-600 text-sm font-bold rounded-full">
                    {{ $pemeriksaanFisik->id_prefisik }}
                </span>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('pemeriksaan_fisik.show', $pemeriksaanFisik->id_prefisik) }}" 
                    class="bg-white text-orange-600 hover:bg-orange-50 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-eye mr-2"></i> Lihat Detail
                </a>
                <a href="{{ route('pemeriksaan_fisik.index') }}" 
                    class="bg-orange-700 text-white hover:bg-orange-800 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            <!-- Alert Messages -->
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
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
                </div>
            @endif

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

            @if(session('info'))
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">{{ session('info') }}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-blue-500 hover:text-blue-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <!-- Info Box -->
            <div class="bg-gradient-to-r from-orange-50 to-red-50 border-l-4 border-orange-500 p-5 mb-6 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-orange-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-lg font-medium text-orange-800 mb-3 flex items-center">
                            <i class="fas fa-stethoscope mr-2"></i>Informasi Pemeriksaan Fisik
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm text-orange-700">
                            <div class="bg-white p-3 rounded border border-orange-200">
                                <span class="font-medium text-orange-800 block">
                                    <i class="fas fa-hashtag text-orange-500 mr-1"></i>ID Pemeriksaan:
                                </span>
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">
                                    {{ $pemeriksaanFisik->id_prefisik }}
                                </span>
                            </div>
                            <div class="bg-white p-3 rounded border border-orange-200">
                                <span class="font-medium text-orange-800 block">
                                    <i class="fas fa-calendar-plus text-blue-500 mr-1"></i>Dibuat:
                                </span>
                                <span class="text-orange-800">{{ $pemeriksaanFisik->created_at->format('d F Y H:i') }}</span>
                            </div>
                            <div class="bg-white p-3 rounded border border-orange-200">
                                <span class="font-medium text-orange-800 block">
                                    <i class="fas fa-calendar-edit text-green-500 mr-1"></i>Terakhir Update:
                                </span>
                                <span class="text-orange-800">{{ $pemeriksaanFisik->updated_at->format('d F Y H:i') }}</span>
                            </div>
                            @if($pemeriksaanFisik->bmi)
                            <div class="bg-white p-3 rounded border border-orange-200">
                                <span class="font-medium text-orange-800 block">
                                    <i class="fas fa-calculator text-purple-500 mr-1"></i>BMI Saat Ini:
                                </span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @php
                                        $bmi = $pemeriksaanFisik->bmi;
                                        if($bmi < 18.5) echo 'bg-blue-100 text-blue-800 border border-blue-200';
                                        elseif($bmi < 25) echo 'bg-green-100 text-green-800 border border-green-200';
                                        elseif($bmi < 30) echo 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                        else echo 'bg-red-100 text-red-800 border border-red-200';
                                    @endphp">
                                    {{ number_format($pemeriksaanFisik->bmi, 1) }} - {{ $pemeriksaanFisik->bmi_kategori }}
                                </span>
                            </div>
                            @endif
                            @if($pemeriksaanFisik->detailPemeriksaan && $pemeriksaanFisik->detailPemeriksaan->siswa)
                            <div class="bg-white p-3 rounded border border-orange-200">
                                <span class="font-medium text-orange-800 block">
                                    <i class="fas fa-user text-indigo-500 mr-1"></i>Pasien:
                                </span>
                                <span class="text-orange-800">{{ $pemeriksaanFisik->detailPemeriksaan->siswa->nama_siswa }}</span>
                            </div>
                            @endif
                            @if($pemeriksaanFisik->detailPemeriksaan && $pemeriksaanFisik->detailPemeriksaan->dokter)
                            <div class="bg-white p-3 rounded border border-orange-200">
                                <span class="font-medium text-orange-800 block">
                                    <i class="fas fa-user-md text-teal-500 mr-1"></i>Dokter:
                                </span>
                                <span class="text-orange-800">{{ $pemeriksaanFisik->detailPemeriksaan->dokter->nama_dokter }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('pemeriksaan_fisik.update', $pemeriksaanFisik->id_prefisik) }}" method="POST" id="pemeriksaanFisikForm">
                @csrf
                @method('PUT')
                
                <!-- Detail Pemeriksaan -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-3">
                        <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Dasar</h3>
                        <span class="ml-2 text-xs text-gray-500">(Wajib diisi)</span>
                    </div>
                    
                    <div>
                        <label for="id_detprx" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search text-blue-500 mr-1"></i>Detail Pemeriksaan 
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <select id="id_detprx" name="id_detprx" required
                                class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 appearance-none {{ $errors->has('id_detprx') ? 'border-red-500 bg-red-50' : '' }}">
                                <option value="">-- Pilih Detail Pemeriksaan --</option>
                                @if(isset($detailPemeriksaans))
                                    @foreach($detailPemeriksaans as $detailPemeriksaan)
                                        <option value="{{ $detailPemeriksaan->id_detprx }}" 
                                                data-siswa="{{ $detailPemeriksaan->siswa->nama_siswa ?? 'N/A' }}"
                                                data-tanggal="{{ \Carbon\Carbon::parse($detailPemeriksaan->tanggal_jam)->format('d/m/Y H:i') }}"
                                                data-dokter="{{ $detailPemeriksaan->dokter->nama_dokter ?? 'N/A' }}"
                                                {{ (old('id_detprx', $pemeriksaanFisik->id_detprx) == $detailPemeriksaan->id_detprx) ? 'selected' : '' }}>
                                            {{ $detailPemeriksaan->id_detprx }} - {{ $detailPemeriksaan->siswa->nama_siswa ?? 'Nama Tidak Ditemukan' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        @if($errors->has('id_detprx'))
                            <p class="text-red-500 text-xs mt-2">{{ $errors->first('id_detprx') }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                            Pilih detail pemeriksaan yang akan dilakukan pemeriksaan fisik
                        </p>
                        
                        <!-- Detail Info Box -->
                        <div id="detail_info" class="mt-4 p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg border border-gray-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <h4 class="text-sm font-medium text-gray-800">Informasi Detail Pemeriksaan</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="bg-white p-3 rounded border">
                                    <span class="font-medium text-gray-600 block">
                                        <i class="fas fa-user text-blue-500 mr-1"></i>Siswa:
                                    </span>
                                    <span id="info_siswa" class="text-gray-800 font-medium">{{ $pemeriksaanFisik->detailPemeriksaan->siswa->nama_siswa ?? '-' }}</span>
                                </div>
                                <div class="bg-white p-3 rounded border">
                                    <span class="font-medium text-gray-600 block">
                                        <i class="fas fa-calendar text-green-500 mr-1"></i>Tanggal:
                                    </span>
                                    <span id="info_tanggal" class="text-gray-800 font-medium">{{ isset($pemeriksaanFisik->detailPemeriksaan->tanggal_jam) ? \Carbon\Carbon::parse($pemeriksaanFisik->detailPemeriksaan->tanggal_jam)->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                                <div class="bg-white p-3 rounded border">
                                    <span class="font-medium text-gray-600 block">
                                        <i class="fas fa-user-md text-purple-500 mr-1"></i>Dokter:
                                    </span>
                                    <span id="info_dokter" class="text-gray-800 font-medium">{{ $pemeriksaanFisik->detailPemeriksaan->dokter->nama_dokter ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Antropometri -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-ruler-combined text-green-500 mr-2"></i>
                        Antropometri
                        <span class="ml-2 text-xs text-gray-500">(Pengukuran Fisik)</span>
                    </h3>
                    
                    <div class="bg-gradient-to-br from-green-50 to-blue-50 border border-green-100 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-6">
                            <!-- Tinggi Badan -->
                            <div>
                                <label for="tinggi_badan" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-arrows-alt-v text-blue-500 mr-1"></i>Tinggi Badan
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-arrows-alt-v text-gray-400"></i>
                                    </div>
                                    <input type="number" step="0.1" min="50" max="250" id="tinggi_badan" name="tinggi_badan" 
                                        value="{{ old('tinggi_badan', $pemeriksaanFisik->tinggi_badan) }}"
                                        class="pl-10 pr-12 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="170.5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm font-medium">cm</span>
                                    </div>
                                </div>
                                @error('tinggi_badan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Berat Badan -->
                            <div>
                                <label for="berat_badan" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-weight text-green-500 mr-1"></i>Berat Badan
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-weight text-gray-400"></i>
                                    </div>
                                    <input type="number" step="0.1" min="10" max="200" id="berat_badan" name="berat_badan" 
                                        value="{{ old('berat_badan', $pemeriksaanFisik->berat_badan) }}"
                                        class="pl-10 pr-12 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="65.5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm font-medium">kg</span>
                                    </div>
                                </div>
                                @error('berat_badan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Lingkar Kepala -->
                            <div>
                                <label for="lingkar_kepala" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-circle text-purple-500 mr-1"></i>Lingkar Kepala
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-circle text-gray-400"></i>
                                    </div>
                                    <input type="number" step="0.1" min="30" max="70" id="lingkar_kepala" name="lingkar_kepala" 
                                        value="{{ old('lingkar_kepala', $pemeriksaanFisik->lingkar_kepala) }}"
                                        class="pl-10 pr-12 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="56.0">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm font-medium">cm</span>
                                    </div>
                                </div>
                                @error('lingkar_kepala')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Lingkar Lengan Atas -->
                            <div>
                                <label for="lingkar_lengan_atas" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-ring text-orange-500 mr-1"></i>Lingkar Lengan Atas
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-ring text-gray-400"></i>
                                    </div>
                                    <input type="number" step="0.1" min="10" max="50" id="lingkar_lengan_atas" name="lingkar_lengan_atas" 
                                        value="{{ old('lingkar_lengan_atas', $pemeriksaanFisik->lingkar_lengan_atas) }}"
                                        class="pl-10 pr-12 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        placeholder="28.5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm font-medium">cm</span>
                                    </div>
                                </div>
                                @error('lingkar_lengan_atas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- BMI Calculator -->
                        <div class="mt-6 bg-white rounded-lg p-5 border border-green-200 shadow-sm">
                            <h4 class="text-md font-medium text-green-700 mb-3 flex items-center">
                                <i class="fas fa-calculator text-green-600 mr-2"></i>
                                Kalkulator BMI (Body Mass Index)
                            </h4>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                <button type="button" onclick="calculateBMI()" 
                                    class="mb-3 sm:mb-0 bg-green-100 hover:bg-green-200 text-green-800 font-medium py-2 px-4 rounded-md transition-colors flex items-center">
                                    <i class="fas fa-calculator mr-2"></i> Hitung BMI
                                </button>
                                <div id="bmi_result" class="text-sm text-gray-600 p-3 bg-gray-50 rounded">
                                    @if($pemeriksaanFisik->bmi)
                                        <div class="flex items-center">
                                            <span class="font-medium text-lg mr-2">BMI: {{ number_format($pemeriksaanFisik->bmi, 1) }}</span>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                @php
                                                    $bmi = $pemeriksaanFisik->bmi;
                                                    if($bmi < 18.5) echo 'bg-blue-100 text-blue-800';
                                                    elseif($bmi < 25) echo 'bg-green-100 text-green-800';
                                                    elseif($bmi < 30) echo 'bg-yellow-100 text-yellow-800';
                                                    else echo 'bg-red-100 text-red-800';
                                                @endphp">
                                                {{ $pemeriksaanFisik->bmi_kategori }}
                                            </span>
                                        </div>
                                    @else
                                        <i class="fas fa-info-circle mr-1"></i>BMI akan muncul di sini setelah mengisi tinggi dan berat badan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pemeriksaan Sistem Organ -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-heartbeat text-red-500 mr-2"></i>
                        Pemeriksaan Sistem Organ
                        <span class="ml-2 text-xs text-gray-500">(Opsional)</span>
                    </h3>
                    
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 border border-red-100 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kepala -->
                            <div>
                                <label for="kepala" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-head-side-virus text-purple-500 mr-1"></i>Kepala
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-head-side-virus text-gray-400"></i>
                                    </div>
                                    <input type="text" id="kepala" name="kepala" value="{{ old('kepala', $pemeriksaanFisik->kepala) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Normocephali/Kelainan">
                                </div>
                                @error('kepala')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Dada -->
                            <div>
                                <label for="dada" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lungs text-blue-500 mr-1"></i>Dada
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lungs text-gray-400"></i>
                                    </div>
                                    <input type="text" id="dada" name="dada" value="{{ old('dada', $pemeriksaanFisik->dada) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Simetris/Asimetris">
                                </div>
                                @error('dada')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Jantung -->
                            <div>
                                <label for="jantung" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-heart text-red-500 mr-1"></i>Jantung
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-heart text-gray-400"></i>
                                    </div>
                                    <input type="text" id="jantung" name="jantung" value="{{ old('jantung', $pemeriksaanFisik->jantung) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Normal/Murmur/Aritmia">
                                </div>
                                @error('jantung')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Paru -->
                            <div>
                                <label for="paru" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lungs-virus text-cyan-500 mr-1"></i>Paru
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lungs-virus text-gray-400"></i>
                                    </div>
                                    <input type="text" id="paru" name="paru" value="{{ old('paru', $pemeriksaanFisik->paru) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Vesikuler/Ronki/Wheezing">
                                </div>
                                @error('paru')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Perut -->
                            <div>
                                <label for="perut" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-hand-paper text-yellow-500 mr-1"></i>Perut
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-hand-paper text-gray-400"></i>
                                    </div>
                                    <input type="text" id="perut" name="perut" value="{{ old('perut', $pemeriksaanFisik->perut) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Supel/Kembung/Nyeri tekan">
                                </div>
                                @error('perut')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Hepar -->
                            <div>
                                <label for="hepar" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-prescription-bottle text-green-500 mr-1"></i>Hepar
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-prescription-bottle text-gray-400"></i>
                                    </div>
                                    <input type="text" id="hepar" name="hepar" value="{{ old('hepar', $pemeriksaanFisik->hepar) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Tidak teraba/Membesar">
                                </div>
                                @error('hepar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Anogenital -->
                            <div>
                                <label for="anogenital" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-check text-indigo-500 mr-1"></i>Anogenital
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user-check text-gray-400"></i>
                                    </div>
                                    <input type="text" id="anogenital" name="anogenital" value="{{ old('anogenital', $pemeriksaanFisik->anogenital) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Normal/Kelainan">
                                </div>
                                @error('anogenital')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Ekstremitas -->
                            <div>
                                <label for="ekstremitas" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-walking text-orange-500 mr-1"></i>Ekstremitas
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-walking text-gray-400"></i>
                                    </div>
                                    <input type="text" id="ekstremitas" name="ekstremitas" value="{{ old('ekstremitas', $pemeriksaanFisik->ekstremitas) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Normal/Edema/Deformitas">
                                </div>
                                @error('ekstremitas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pemeriksaan Penunjang & Rencana -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clipboard-check text-purple-500 mr-2"></i>
                        Pemeriksaan Penunjang & Rencana
                        <span class="ml-2 text-xs text-gray-500">(Opsional)</span>
                    </h3>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-100 rounded-lg p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Pemeriksaan Penunjang -->
                            <div>
                                <label for="pemeriksaan_penunjang" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-microscope text-purple-500 mr-1"></i>Pemeriksaan Penunjang
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <i class="fas fa-microscope text-gray-400"></i>
                                    </div>
                                    <textarea id="pemeriksaan_penunjang" name="pemeriksaan_penunjang" rows="4"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                        placeholder="Lab darah, rontgen, USG, EKG, dll.">{{ old('pemeriksaan_penunjang', $pemeriksaanFisik->pemeriksaan_penunjang) }}</textarea>
                                </div>
                                @error('pemeriksaan_penunjang')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Masalah Aktif -->
                            <div>
                                <label for="masalah_aktif" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>Masalah Aktif
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-exclamation-triangle text-gray-400"></i>
                                    </div>
                                    <input type="text" id="masalah_aktif" name="masalah_aktif" value="{{ old('masalah_aktif', $pemeriksaanFisik->masalah_aktif) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                        placeholder="Diagnosis atau masalah yang ditemukan">
                                </div>
                                @error('masalah_aktif')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Rencana Medis dan Terapi -->
                            <div>
                                <label for="rencana_medis_dan_terapi" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-pills text-green-500 mr-1"></i>Rencana Medis dan Terapi
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-pills text-gray-400"></i>
                                    </div>
                                    <input type="text" id="rencana_medis_dan_terapi" name="rencana_medis_dan_terapi" value="{{ old('rencana_medis_dan_terapi', $pemeriksaanFisik->rencana_medis_dan_terapi) }}" maxlength="50"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-12 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                        placeholder="Rencana tindakan dan terapi">
                                </div>
                                @error('rencana_medis_dan_terapi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button type="button" onclick="window.location.href='{{ route('pemeriksaan_fisik.index') }}'" 
                            class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            <i class="fas fa-times mr-2 text-gray-500"></i>
                            Batal
                        </button>
                        <a href="{{ route('pemeriksaan_fisik.show', $pemeriksaanFisik->id_prefisik) }}" 
                            class="inline-flex items-center px-6 py-3 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail
                        </a>
                    </div>

                    <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Update Pemeriksaan Fisik</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // BMI calculation with better UI feedback
    function calculateBMI() {
        const height = parseFloat(document.getElementById('tinggi_badan').value);
        const weight = parseFloat(document.getElementById('berat_badan').value);
        const resultDiv = document.getElementById('bmi_result');
        
        if (!height || !weight || height <= 0 || weight <= 0) {
            resultDiv.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>Masukkan tinggi dan berat badan yang valid';
            resultDiv.className = 'text-sm text-yellow-600 p-3 bg-yellow-50 rounded border border-yellow-200';
            return;
        }
        
        // BMI = weight(kg) / (height(m))²
        const heightInMeters = height / 100;
        const bmi = weight / (heightInMeters * heightInMeters);
        const roundedBMI = Math.round(bmi * 10) / 10;
        
        let category, colorClass, status, bgClass, borderClass, icon;
        if (bmi < 18.5) {
            category = 'Berat Badan Kurang';
            colorClass = 'text-blue-700';
            bgClass = 'bg-blue-50';
            borderClass = 'border-blue-200';
            status = 'Underweight';
            icon = 'fas fa-arrow-down';
        } else if (bmi < 25) {
            category = 'Berat Badan Normal';
            colorClass = 'text-green-700';
            bgClass = 'bg-green-50';
            borderClass = 'border-green-200';
            status = 'Normal';
            icon = 'fas fa-check-circle';
        } else if (bmi < 30) {
            category = 'Berat Badan Berlebih';
            colorClass = 'text-yellow-700';
            bgClass = 'bg-yellow-50';
            borderClass = 'border-yellow-200';
            status = 'Overweight';
            icon = 'fas fa-arrow-up';
        } else {
            category = 'Obesitas';
            colorClass = 'text-red-700';
            bgClass = 'bg-red-50';
            borderClass = 'border-red-200';
            status = 'Obese';
            icon = 'fas fa-exclamation-triangle';
        }
        
        resultDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="${icon} ${colorClass} mr-2"></i>
                    <div>
                        <span class="font-bold text-lg ${colorClass}">BMI: ${roundedBMI}</span>
                        <div class="text-xs ${colorClass} font-medium">${category}</div>
                    </div>
                </div>
                <div class="px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(status)}">
                    ${status}
                </div>
            </div>
        `;
        resultDiv.className = `text-sm ${colorClass} p-3 ${bgClass} rounded border ${borderClass}`;
    }
    
    function getStatusColor(status) {
        switch(status) {
            case 'Underweight': return 'bg-blue-100 text-blue-800 border border-blue-200';
            case 'Normal': return 'bg-green-100 text-green-800 border border-green-200';
            case 'Overweight': return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
            case 'Obese': return 'bg-red-100 text-red-800 border border-red-200';
            default: return 'bg-gray-100 text-gray-800 border border-gray-200';
        }
    }
    
    // Form interactions
    document.addEventListener('DOMContentLoaded', function() {
        const heightInput = document.getElementById('tinggi_badan');
        const weightInput = document.getElementById('berat_badan');
        const detailSelect = document.getElementById('id_detprx');
        const form = document.getElementById('pemeriksaanFisikForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        // Auto-close alerts after 5 seconds
        const alerts = document.querySelectorAll('.close-alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert.parentElement) {
                    alert.parentElement.style.display = 'none';
                }
            }, 5000);
        });
        
        // Auto-calculate BMI when values change
        if (heightInput) heightInput.addEventListener('input', calculateBMI);
        if (weightInput) weightInput.addEventListener('input', calculateBMI);
        
        // Form field interactions
        const allInputs = document.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            input.addEventListener('focus', function() {
                const parent = this.closest('.relative');
                if (parent) {
                    parent.classList.add('ring-2', 'ring-orange-100', 'ring-opacity-50');
                }
            });
            
            input.addEventListener('blur', function() {
                const parent = this.closest('.relative');
                if (parent) {
                    parent.classList.remove('ring-2', 'ring-orange-100', 'ring-opacity-50');
                }
            });
        });
        
        // Detail pemeriksaan info display
        if (detailSelect) {
            detailSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const siswa = selectedOption.dataset.siswa;
                    const tanggal = selectedOption.dataset.tanggal;
                    const dokter = selectedOption.dataset.dokter;
                    
                    document.getElementById('info_siswa').textContent = siswa;
                    document.getElementById('info_tanggal').textContent = tanggal;
                    document.getElementById('info_dokter').textContent = dokter;
                }
            });
        }
        
        // Form submission handling
        if (form && submitBtn && submitText) {
            form.addEventListener('submit', function(e) {
                // Show loading state
                submitBtn.disabled = true;
                submitText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengupdate...';
                
                // Validation
                if (detailSelect && !detailSelect.value) {
                    e.preventDefault();
                    alert('❌ Silakan pilih detail pemeriksaan terlebih dahulu!');
                    submitBtn.disabled = false;
                    submitText.innerHTML = '<i class="fas fa-save mr-2"></i>Update Pemeriksaan Fisik';
                    detailSelect.focus();
                    return;
                }
            });
        }
        
        // Initialize BMI calculation with existing values
        if (heightInput && weightInput && heightInput.value && weightInput.value) {
            calculateBMI();
        }
        
        // Initialize detail info if select has value
        if (detailSelect && detailSelect.value) {
            detailSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection