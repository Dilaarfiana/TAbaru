<!-- resources/views/pemeriksaan_fisik/create.blade.php -->
@extends('layouts.admin')

@section('page_title', 'Tambah Pemeriksaan Fisik')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Tambah Pemeriksaan Fisik</h2>
            </div>
            <a href="{{ route('pemeriksaan_fisik.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
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
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Info Box dengan Preview ID -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-blue-800 mb-1">Informasi Pembuatan ID</h3>
                        <p class="text-sm text-blue-700 mb-2">
                            ID pemeriksaan akan dibuat otomatis saat menyimpan data. Pastikan data terisi dengan benar.
                        </p>
                        
                        <!-- Preview ID Box -->
                        <div class="mt-3 bg-white rounded-md border border-blue-200 p-3 flex items-center">
                            <div class="mr-3 bg-blue-100 rounded-full p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">ID pemeriksaan yang akan dibuat:</div>
                                <div class="font-mono font-medium text-blue-700 text-lg tracking-wide">
                                    {{ $previewId ?? 'PF001' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('pemeriksaan_fisik.store') }}" method="POST">
                @csrf
                
                <!-- Informasi Pemeriksaan -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Informasi Pemeriksaan
                    </h3>
                    
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-5">
                        <!-- Tanggal Pemeriksaan -->
                        <div>
                            <label for="tanggal_pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pemeriksaan <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" value="{{ old('tanggal_pemeriksaan', date('Y-m-d')) }}" required
                                    class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            @error('tanggal_pemeriksaan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Data Fisik -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Fisik
                    </h3>
                    
                    <div class="bg-green-50 border border-green-100 rounded-lg p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">
                            <!-- Tinggi Badan -->
                            <div>
                                <label for="tinggi_badan" class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                    <input type="number" step="0.1" min="0" max="300" id="tinggi_badan" name="tinggi_badan" value="{{ old('tinggi_badan') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                        placeholder="cm">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">cm</span>
                                    </div>
                                </div>
                                @error('tinggi_badan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Berat Badan -->
                            <div>
                                <label for="berat_badan" class="block text-sm font-medium text-gray-700 mb-1">Berat Badan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                        </svg>
                                    </div>
                                    <input type="number" step="0.1" min="0" max="500" id="berat_badan" name="berat_badan" value="{{ old('berat_badan') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                        placeholder="kg">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">kg</span>
                                    </div>
                                </div>
                                @error('berat_badan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Suhu Badan -->
                            <div>
                                <label for="suhu_badan" class="block text-sm font-medium text-gray-700 mb-1">Suhu Badan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <input type="number" step="0.1" min="20" max="45" id="suhu_badan" name="suhu_badan" value="{{ old('suhu_badan') }}"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                        placeholder="36.5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">°C</span>
                                    </div>
                                </div>
                                @error('suhu_badan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Tekanan Darah -->
                            <div>
                                <label for="tekanan_darah" class="block text-sm font-medium text-gray-700 mb-1">Tekanan Darah</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="tekanan_darah" name="tekanan_darah" value="{{ old('tekanan_darah') }}" maxlength="10"
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                        placeholder="120/80">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">mmHg</span>
                                    </div>
                                </div>
                                @error('tekanan_darah')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- BMI Calculator -->
                        <div class="mt-5 bg-white rounded-lg p-4 border border-green-200">
                            <h4 class="text-sm font-medium text-green-700 mb-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Kalkulator BMI
                            </h4>
                            <div class="flex items-center">
                                <button type="button" onclick="calculateBMI()" 
                                    class="text-xs bg-green-100 hover:bg-green-200 text-green-800 font-medium py-1 px-2 rounded-md mr-3">
                                    Hitung BMI
                                </button>
                                <div id="bmi_result" class="text-sm text-gray-600">BMI akan muncul di sini</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Keluhan & Hasil -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Keluhan & Hasil Pemeriksaan
                    </h3>
                    
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-5">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Keluhan -->
                            <div>
                                <label for="keluhan" class="block text-sm font-medium text-gray-700 mb-1">Keluhan Pasien</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                    </div>
                                    <textarea id="keluhan" name="keluhan" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500"
                                        placeholder="Deskripsikan keluhan yang dirasakan pasien">{{ old('keluhan') }}</textarea>
                                </div>
                                @error('keluhan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Hasil Pemeriksaan -->
                            <div>
                                <label for="hasil_pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">Hasil Pemeriksaan</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <textarea id="hasil_pemeriksaan" name="hasil_pemeriksaan" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-purple-500 focus:border-purple-500"
                                        placeholder="Tuliskan hasil pemeriksaan secara detail">{{ old('hasil_pemeriksaan') }}</textarea>
                                </div>
                                @error('hasil_pemeriksaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='{{ route('pemeriksaan_fisik.index') }}'" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="reset" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </button>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function calculateBMI() {
        const height = parseFloat(document.getElementById('tinggi_badan').value);
        const weight = parseFloat(document.getElementById('berat_badan').value);
        const resultDiv = document.getElementById('bmi_result');
        
        if (!height || !weight || height <= 0 || weight <= 0) {
            resultDiv.textContent = 'Masukkan tinggi dan berat badan yang valid';
            resultDiv.className = 'text-sm text-red-600';
            return;
        }
        
        // BMI = weight(kg) / (height(m))²
        const heightInMeters = height / 100;
        const bmi = weight / (heightInMeters * heightInMeters);
        const roundedBMI = Math.round(bmi * 10) / 10;
        
        let category, colorClass;
        if (bmi < 18.5) {
            category = 'Berat Badan Kurang';
            colorClass = 'text-blue-600';
        } else if (bmi < 25) {
            category = 'Berat Badan Normal';
            colorClass = 'text-green-600';
        } else if (bmi < 30) {
            category = 'Berat Badan Berlebih';
            colorClass = 'text-yellow-600';
        } else {
            category = 'Obesitas';
            colorClass = 'text-red-600';
        }
        
        resultDiv.textContent = `BMI: ${roundedBMI} (${category})`;
        resultDiv.className = `text-sm font-medium ${colorClass}`;
    }
</script>
@endpush
@endsection