@extends('layouts.admin')

@section('page_title', 'Detail Pemeriksaan Fisik')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Detail Pemeriksaan Fisik</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pemeriksaan_fisik.edit', $pemeriksaanFisik->id) }}" class="bg-yellow-500 text-white hover:bg-yellow-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('pemeriksaan_fisik.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
        
        <!-- Badge ID Pemeriksaan -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b border-blue-100">
            <div class="flex justify-center">
                <div class="bg-white py-2 px-4 rounded-full shadow-sm border border-blue-200 flex items-center">
                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">ID Pemeriksaan</div>
                        <div class="font-mono font-bold text-xl text-blue-600">{{ $pemeriksaanFisik->id }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Informasi Pemeriksaan -->
                <div class="bg-blue-50 rounded-lg border border-blue-100 p-5 shadow-sm h-full">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-blue-800">Informasi Pemeriksaan</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-white rounded-md p-3 border border-blue-200">
                            <div class="text-sm text-gray-500 mb-1">Tanggal Pemeriksaan</div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($pemeriksaanFisik->tanggal_pemeriksaan)->format('d F Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-3 border border-blue-200">
                            <div class="text-sm text-gray-500 mb-1">Tanggal Dibuat</div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($pemeriksaanFisik->created_at)->format('d F Y - H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-3 border border-blue-200">
                            <div class="text-sm text-gray-500 mb-1">Terakhir Diperbarui</div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($pemeriksaanFisik->updated_at)->format('d F Y - H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Fisik & BMI -->
                <div class="bg-green-50 rounded-lg border border-green-100 p-5 shadow-sm h-full">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-green-800">Data Fisik</h3>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-white rounded-md p-3 border border-green-200">
                            <div class="text-sm text-gray-500 mb-1">Tinggi Badan</div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->tinggi_badan ? $pemeriksaanFisik->tinggi_badan . ' cm' : '-' }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-3 border border-green-200">
                            <div class="text-sm text-gray-500 mb-1">Berat Badan</div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->berat_badan ? $pemeriksaanFisik->berat_badan . ' kg' : '-' }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-3 border border-green-200">
                            <div class="text-sm text-gray-500 mb-1">Suhu Badan</div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->suhu_badan ? $pemeriksaanFisik->suhu_badan . ' °C' : '-' }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-3 border border-green-200">
                            <div class="text-sm text-gray-500 mb-1">Tekanan Darah</div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->tekanan_darah ? $pemeriksaanFisik->tekanan_darah . ' mmHg' : '-' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- BMI Display -->
                    @if($pemeriksaanFisik->tinggi_badan && $pemeriksaanFisik->berat_badan)
                    <div class="bg-white rounded-lg border border-green-200 p-4 mt-2">
                        <div class="flex">
                            <div class="mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8
                                @if($pemeriksaanFisik->bmi < 18.5) text-blue-500
                                @elseif($pemeriksaanFisik->bmi < 25) text-green-500
                                @elseif($pemeriksaanFisik->bmi < 30) text-yellow-500
                                @else text-red-500
                                @endif" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Indeks Massa Tubuh (BMI)</h4>
                                <div class="flex items-baseline">
                                    <span class="text-2xl font-bold 
                                    @if($pemeriksaanFisik->bmi < 18.5) text-blue-600
                                    @elseif($pemeriksaanFisik->bmi < 25) text-green-600
                                    @elseif($pemeriksaanFisik->bmi < 30) text-yellow-600
                                    @else text-red-600
                                    @endif">{{ number_format($pemeriksaanFisik->bmi, 1) }}</span>
                                    <span class="text-sm ml-2 
                                    @if($pemeriksaanFisik->bmi < 18.5) text-blue-500
                                    @elseif($pemeriksaanFisik->bmi < 25) text-green-500
                                    @elseif($pemeriksaanFisik->bmi < 30) text-yellow-500
                                    @else text-red-500
                                    @endif">{{ $pemeriksaanFisik->bmi_category }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Keluhan & Hasil -->
            <div class="bg-purple-50 rounded-lg border border-purple-100 p-5 shadow-sm mb-6">
                <div class="flex items-center mb-4">
                    <div class="h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-purple-800">Keluhan & Hasil Pemeriksaan</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg p-4 border border-purple-200 shadow-sm">
                        <div class="flex items-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <h4 class="text-md font-medium text-purple-700">Keluhan Pasien</h4>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-md min-h-[120px]">
                            <p class="text-gray-800">{{ $pemeriksaanFisik->keluhan ?? 'Tidak ada keluhan tercatat' }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 border border-purple-200 shadow-sm">
                        <div class="flex items-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h4 class="text-md font-medium text-purple-700">Hasil Pemeriksaan</h4>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-md min-h-[120px]">
                            <p class="text-gray-800">{{ $pemeriksaanFisik->hasil_pemeriksaan ?? 'Tidak ada hasil tercatat' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('pemeriksaan_fisik.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-4 rounded-md text-center flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Lihat Semua Pemeriksaan
                </a>
                <a href="{{ route('pemeriksaan_fisik.edit', $pemeriksaanFisik->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2.5 px-4 rounded-md text-center flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Pemeriksaan
                </a>
                <form action="{{ route('pemeriksaan_fisik.destroy', $pemeriksaanFisik->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2.5 px-4 rounded-md flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Pemeriksaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection