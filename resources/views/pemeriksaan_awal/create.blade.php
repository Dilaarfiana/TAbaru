@extends('layouts.admin')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Tambah Pemeriksaan Awal Baru</h2>
            </div>
            <div class="flex items-center space-x-2">
                <span class="bg-blue-100 text-blue-800 text-sm font-medium py-1 px-2 rounded-md">
                    ID: {{ $id }}
                </span>
                <a href="{{ route('pemeriksaan_awal.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
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
                            Pemeriksaan awal akan dicatat dengan ID: <span class="font-mono font-medium">{{ $id }}</span>. Harap isi seluruh data yang diperlukan dengan lengkap dan akurat.
                        </p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('pemeriksaan_awal.store') }}" method="POST">
                @csrf
                
                <input type="hidden" id="Id_PreAwal" name="Id_PreAwal" value="{{ $id }}">
                
                <!-- Detail Pemeriksaan -->
                <div class="bg-white p-5 rounded-lg mb-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Dasar</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="Id_DetPrx" class="block text-sm font-medium text-gray-700 mb-1">Detail Pemeriksaan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <select id="Id_DetPrx" name="Id_DetPrx" required
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                    <option value="">-- Pilih Detail Pemeriksaan --</option>
                                    @foreach($detailPemeriksaans as $detailPemeriksaan)
                                        <option value="{{ $detailPemeriksaan->Id_DetPrx }}" {{ old('Id_DetPrx') == $detailPemeriksaan->Id_DetPrx ? 'selected' : '' }}>
                                            {{ $detailPemeriksaan->Id_DetPrx }} - {{ \Carbon\Carbon::parse($detailPemeriksaan->Tanggal_Jam)->format('d/m/Y H:i') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih detail pemeriksaan yang terkait dengan pemeriksaan awal ini</p>
                        </div>
                    </div>
                </div>

                <!-- Grid untuk Detail Pemeriksaan dan Tanda Vital -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Kolom Kiri: Detail Pemeriksaan -->
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center mb-4 border-b pb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-800">Detail Pemeriksaan</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="Pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">Pemeriksaan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <textarea id="Pemeriksaan" name="Pemeriksaan" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Masukkan detail pemeriksaan">{{ old('Pemeriksaan') }}</textarea>
                                </div>
                            </div>

                            <div>
                                <label for="Keluhan_Dahulu" class="block text-sm font-medium text-gray-700 mb-1">Keluhan Dahulu</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="Keluhan_Dahulu" name="Keluhan_Dahulu" value="{{ old('Keluhan_Dahulu') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                        placeholder="Riwayat keluhan pasien">
                                </div>
                            </div>
                            
                            <div>
                                <label for="Tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe Pemeriksaan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <select id="Tipe" name="Tipe"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500 appearance-none">
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="1" {{ old('Tipe') == '1' ? 'selected' : '' }}>1 - Umum</option>
                                        <option value="2" {{ old('Tipe') == '2' ? 'selected' : '' }}>2 - Khusus</option>
                                        <option value="3" {{ old('Tipe') == '3' ? 'selected' : '' }}>3 - Darurat</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Tanda Vital -->
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex items-center mb-4 border-b pb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-800">Tanda Vital</h3>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="Suhu" class="block text-sm font-medium text-gray-700 mb-1">Suhu (°C)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                    </div>
                                    <input type="number" step="0.1" id="Suhu" name="Suhu" value="{{ old('Suhu') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                        placeholder="36.5">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Normal: 36.1°C - 37.2°C</p>
                            </div>
                            
                            <div>
                                <label for="Nadi" class="block text-sm font-medium text-gray-700 mb-1">Nadi (bpm)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <input type="number" id="Nadi" name="Nadi" value="{{ old('Nadi') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                        placeholder="80">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Normal: 60-100 bpm</p>
                            </div>
                            
                            <div>
                                <label for="Tegangan" class="block text-sm font-medium text-gray-700 mb-1">Tegangan (mmHg)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="Tegangan" name="Tegangan" value="{{ old('Tegangan') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                        placeholder="120/80">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Format: Sistol/Diastol</p>
                            </div>
                            
                            <div>
                                <label for="Pernapasan" class="block text-sm font-medium text-gray-700 mb-1">Pernapasan (rpm)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                    </div>
                                    <input type="number" id="Pernapasan" name="Pernapasan" value="{{ old('Pernapasan') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                        placeholder="16">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Normal: 12-20 rpm</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Nyeri -->
                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Nyeri</h3>
                    </div>
                    
                    <div class="mb-4">
                        <label for="Status_Nyeri" class="block text-sm font-medium text-gray-700 mb-1">Status Nyeri</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <select id="Status_Nyeri" name="Status_Nyeri"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 appearance-none">
                                <option value="">-- Pilih Status Nyeri --</option>
                                <option value="0" {{ old('Status_Nyeri') == '0' ? 'selected' : '' }}>0 - Tidak Ada</option>
                                <option value="1" {{ old('Status_Nyeri') == '1' ? 'selected' : '' }}>1 - Ringan</option>
                                <option value="2" {{ old('Status_Nyeri') == '2' ? 'selected' : '' }}>2 - Sedang</option>
                                <option value="3" {{ old('Status_Nyeri') == '3' ? 'selected' : '' }}>3 - Berat</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div id="nyeriDetails" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div>
                            <label for="Karakteristik" class="block text-sm font-medium text-gray-700 mb-1">Karakteristik</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                </div>
                                <input type="text" id="Karakteristik" name="Karakteristik" value="{{ old('Karakteristik') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="Nyeri tumpul/tajam/berdenyut">
                            </div>
                        </div>
                        
                        <div>
                            <label for="Lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="Lokasi" name="Lokasi" value="{{ old('Lokasi') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="Bagian tubuh yang terasa nyeri">
                            </div>
                        </div>
                        
                        <div>
                            <label for="Durasi" class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="Durasi" name="Durasi" value="{{ old('Durasi') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="Lama nyeri (menit/jam/hari)">
                            </div>
                        </div>
                        
                        <div>
                            <label for="Frekuensi" class="block text-sm font-medium text-gray-700 mb-1">Frekuensi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <input type="text" id="Frekuensi" name="Frekuensi" value="{{ old('Frekuensi') }}"
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500"
                                    placeholder="Seberapa sering terjadi">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='{{ route('pemeriksaan_awal.index') }}'" 
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
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status nyeri dependencies
        const statusNyeriSelect = document.getElementById('Status_Nyeri');
        const nyeriDetails = document.getElementById('nyeriDetails');
        
        // Change styling based on pain level
        statusNyeriSelect.addEventListener('change', function() {
            const nyeriValue = this.value;
            
            if (nyeriValue === '' || nyeriValue === '0') {
                nyeriDetails.classList.add('opacity-50', 'bg-gray-50');
                nyeriDetails.classList.remove('bg-blue-50', 'bg-yellow-50', 'bg-red-50', 'border-l-4', 'border-blue-500', 'border-yellow-500', 'border-red-500');
                nyeriDetails.querySelectorAll('input').forEach(input => {
                    input.classList.add('bg-gray-100');
                    input.disabled = true;
                });
            } else {
                nyeriDetails.classList.remove('opacity-50');
                nyeriDetails.querySelectorAll('input').forEach(input => {
                    input.classList.remove('bg-gray-100');
                    input.disabled = false;
                });
                
                // Apply appropriate styling based on pain level
                nyeriDetails.classList.remove('bg-gray-50', 'bg-blue-50', 'bg-yellow-50', 'bg-red-50', 'border-l-4', 'border-blue-500', 'border-yellow-500', 'border-red-500');
                
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
        const suhuInput = document.getElementById('Suhu');
        suhuInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            this.classList.remove('border-red-500', 'bg-red-50', 'border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
            
            if (!value) return;
            
            if (value > 37.5) {
                this.classList.add('border-red-500', 'bg-red-50');
            } else if (value < 35.0) {
                this.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                this.classList.add('border-green-500', 'bg-green-50');
            }
        });
        
        const nadiInput = document.getElementById('Nadi');
        nadiInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            this.classList.remove('border-red-500', 'bg-red-50', 'border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
            
            if (!value) return;
            
            if (value > 100) {
                this.classList.add('border-red-500', 'bg-red-50');
            } else if (value < 60) {
                this.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                this.classList.add('border-green-500', 'bg-green-50');
            }
        });
        
        const pernapasanInput = document.getElementById('Pernapasan');
        pernapasanInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            this.classList.remove('border-red-500', 'bg-red-50', 'border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
            
            if (!value) return;
            
            if (value > 20) {
                this.classList.add('border-red-500', 'bg-red-50');
            } else if (value < 12) {
                this.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                this.classList.add('border-green-500', 'bg-green-50');
            }
        });
        
        // Enhanced form field visual feedback
        const allInputs = document.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            // Add visual feedback on focus
            input.addEventListener('focus', function() {
                this.closest('.relative').classList.add('ring-2', 'ring-blue-100', 'ring-opacity-50');
            });
            
            input.addEventListener('blur', function() {
                this.closest('.relative').classList.remove('ring-2', 'ring-blue-100', 'ring-opacity-50');
            });
        });
        
        // Initialize form state
        statusNyeriSelect.dispatchEvent(new Event('change'));
        if (suhuInput.value) suhuInput.dispatchEvent(new Event('input'));
        if (nadiInput.value) nadiInput.dispatchEvent(new Event('input'));
        if (pernapasanInput.value) pernapasanInput.dispatchEvent(new Event('input'));
    });
</script>
@endpush
@endsection