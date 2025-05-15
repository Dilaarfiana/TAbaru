@extends('layouts.admin')

@section('page_title', 'Edit Rekam Medis')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Edit Rekam Medis</h2>
            </div>
            <a href="{{ route('rekam_medis.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
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
                            Anda sedang mengedit rekam medis dengan nomor: <span class="font-mono font-medium">{{ $rekamMedis->No_Rekam_Medis }}</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('rekam_medis.update', $rekamMedis->No_Rekam_Medis) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Data Pasien dan Dokter -->
                <div class="rounded-lg border border-gray-200 shadow-sm mb-6">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                        <h3 class="text-md font-medium text-gray-700">Informasi Pasien & Dokter</h3>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <!-- Siswa (readonly) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Siswa</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" value="{{ $rekamMedis->siswa->Nama_Siswa }}" 
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 bg-gray-100" readonly>
                                    <input type="hidden" name="Id_Siswa" value="{{ $rekamMedis->Id_Siswa }}">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Data siswa tidak dapat diubah</p>
                            </div>
                            
                            <!-- Pilih Dokter -->
                            <div>
                                <label for="Id_Dokter" class="block text-sm font-medium text-gray-700 mb-1">Dokter <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <select id="Id_Dokter" name="Id_Dokter" required
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                        <option value="">Pilih Dokter</option>
                                        @foreach($dokters as $dokter)
                                            <option value="{{ $dokter->Id_Dokter }}" {{ (old('Id_Dokter') ?? $rekamMedis->Id_Dokter) == $dokter->Id_Dokter ? 'selected' : '' }}>
                                                {{ $dokter->Id_Dokter }} - {{ $dokter->Nama_Dokter }}
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
                            
                            <!-- Tanggal & Waktu -->
                            <div>
                                <label for="Tanggal_Jam" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="datetime-local" id="Tanggal_Jam" name="Tanggal_Jam" value="{{ old('Tanggal_Jam') ?? \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('Y-m-d\TH:i') }}" required
                                        class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rekam Medis -->
                <div class="rounded-lg border border-gray-200 shadow-sm mb-6">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                        <h3 class="text-md font-medium text-gray-700">Data Rekam Medis</h3>
                    </div>
                    <div class="p-4">
                        <!-- Keluhan Utama -->
                        <div class="mb-5">
                            <label for="Keluhan_Utama" class="block text-sm font-medium text-gray-700 mb-1">Keluhan Utama <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <textarea id="Keluhan_Utama" name="Keluhan_Utama" rows="3" required
                                    class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                    placeholder="Masukkan keluhan utama pasien">{{ old('Keluhan_Utama') ?? $rekamMedis->Keluhan_Utama }}</textarea>
                            </div>
                        </div>
                        
                        <!-- Riwayat Penyakit Sekarang -->
                        <div class="mb-5">
                            <label for="Riwayat_Penyakit_Sekarang" class="block text-sm font-medium text-gray-700 mb-1">Riwayat Penyakit Sekarang</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <textarea id="Riwayat_Penyakit_Sekarang" name="Riwayat_Penyakit_Sekarang" rows="3"
                                    class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                    placeholder="Riwayat penyakit yang sedang dialami">{{ old('Riwayat_Penyakit_Sekarang') ?? $rekamMedis->Riwayat_Penyakit_Sekarang }}</textarea>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            <!-- Riwayat Penyakit Dahulu -->
                            <div>
                                <label for="Riwayat_Penyakit_Dahulu" class="block text-sm font-medium text-gray-700 mb-1">Riwayat Penyakit Dahulu</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <textarea id="Riwayat_Penyakit_Dahulu" name="Riwayat_Penyakit_Dahulu" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Riwayat penyakit yang pernah dialami">{{ old('Riwayat_Penyakit_Dahulu') ?? $rekamMedis->Riwayat_Penyakit_Dahulu }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Riwayat Imunisasi -->
                            <div>
                                <label for="Riwayat_Imunisasi" class="block text-sm font-medium text-gray-700 mb-1">Riwayat Imunisasi</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <textarea id="Riwayat_Imunisasi" name="Riwayat_Imunisasi" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Catatan imunisasi yang sudah diberikan">{{ old('Riwayat_Imunisasi') ?? $rekamMedis->Riwayat_Imunisasi }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Riwayat Penyakit Keluarga -->
                            <div>
                                <label for="Riwayat_Penyakit_Keluarga" class="block text-sm font-medium text-gray-700 mb-1">Riwayat Penyakit Keluarga</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <textarea id="Riwayat_Penyakit_Keluarga" name="Riwayat_Penyakit_Keluarga" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Riwayat penyakit yang ada pada keluarga">{{ old('Riwayat_Penyakit_Keluarga') ?? $rekamMedis->Riwayat_Penyakit_Keluarga }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Silsilah Keluarga -->
                            <div>
                                <label for="Silsilah_Keluarga" class="block text-sm font-medium text-gray-700 mb-1">Silsilah Keluarga</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <textarea id="Silsilah_Keluarga" name="Silsilah_Keluarga" rows="3"
                                        class="pl-10 block w-full border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                        placeholder="Informasi silsilah keluarga">{{ old('Silsilah_Keluarga') ?? $rekamMedis->Silsilah_Keluarga }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='{{ route('rekam_medis.index') }}'" 
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
</div>
@endsection