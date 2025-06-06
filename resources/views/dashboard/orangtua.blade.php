{{-- File: resources/views/orangtua/dashboard.blade.php --}}
@extends('layouts.app')

@section('page_title', 'Dashboard Orang Tua')

@section('content')
@php
    $siswaId = session('siswa_id');
    $namaAnak = session('nama_anak') ?? 'Anak Anda';
    $namaOrangTua = session('user_name') ?? session('nama_orang_tua') ?? 'Orang Tua';
@endphp

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    Dashboard Orang Tua
                </h1>
                <p class="text-gray-600">Selamat datang, {{ $namaOrangTua }} - Informasi Kesehatan {{ $namaAnak }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex items-center space-x-3 text-sm text-gray-600">
                    <div class="bg-white px-3 py-2 rounded-md border border-gray-200">
                        <i class="fas fa-calendar mr-2"></i>
                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </div>
                    <div class="bg-white px-3 py-2 rounded-md border border-gray-200">
                        <i class="fas fa-clock mr-2"></i>
                        <span id="currentTime"></span>
                    </div>
                    <div class="bg-purple-100 text-purple-700 px-3 py-2 rounded-md border border-purple-200">
                        <i class="fas fa-user-friends mr-2"></i>
                        <span class="font-medium">Akses: Orang Tua</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Banner -->
    @if(!$siswaId)
    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-red-800">Data Anak Tidak Ditemukan</h3>
                <p class="text-sm text-red-700 mt-1">
                    Tidak ada data anak yang terkait dengan akun Anda. Silakan hubungi pihak sekolah.
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Anda dapat melihat informasi kesehatan {{ $namaAnak }} dan mengakses riwayat pemeriksaan medis.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Student Info Card -->
    @if($siswaId && isset($siswaData))
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg border border-purple-200 p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-white">{{ $siswaData->nama_siswa }}</h2>
                    <p class="text-purple-100">{{ $siswaData->id_siswa }}</p>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-purple-100">
                        <span>Kelas: {{ $siswaData->detailSiswa->kelas->Nama_Kelas ?? 'Belum ditentukan' }}</span>
                        <span>Status: 
                            @if($siswaData->status_aktif == 1)
                                <span class="text-green-300 font-medium">Aktif</span>
                            @else
                                <span class="text-red-300 font-medium">Tidak Aktif</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('orangtua.siswa.show') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Profil
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Pemeriksaan Terakhir -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Pemeriksaan Terakhir</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if(isset($pemeriksaanTerakhir))
                            {{ \Carbon\Carbon::parse($pemeriksaanTerakhir->Tanggal_Jam ?? $pemeriksaanTerakhir->tanggal_jam)->diffForHumans() }}
                        @else
                            Belum ada
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Total Rekam Medis -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-medical text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Rekam Medis</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalRekamMedis ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <!-- Resep Obat -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-prescription-bottle text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Resep Obat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalResep ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <!-- Status Kesehatan -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heart text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Status Kesehatan</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if(isset($siswaData) && $siswaData->status_aktif == 1)
                            Sehat
                        @else
                            Monitoring
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Quick Links -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-link text-blue-600 mr-2"></i>
                    Akses Cepat
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('orangtua.siswa.show') }}" class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-900">Profil Anak</span>
                        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                    </a>
                    
                    <a href="{{ route('orangtua.laporan.screening') }}" class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-file-medical text-green-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-900">Laporan Screening</span>
                        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                    </a>
                    
                    <a href="{{ route('orangtua.laporan.harian') }}" class="flex items-center p-3 border border-gray-200 rounded-md hover:bg-gray-50">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-stethoscope text-purple-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-900">Laporan Harian</span>
                        <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Health Timeline -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Riwayat Kesehatan {{ $namaAnak }}
                    </h3>
                    <a href="{{ route('orangtua.riwayat.pemeriksaan_harian') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Lihat Semua
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($riwayatKesehatan ?? [] as $riwayat)
                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-md">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-medium text-gray-900">{{ $riwayat['jenis'] }}</h4>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($riwayat['tanggal'])->format('d M Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-1">{{ $riwayat['keterangan'] }}</p>
                            @if(isset($riwayat['dokter']))
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-user-md mr-1"></i>{{ $riwayat['dokter'] }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <i class="fas fa-heartbeat text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada riwayat kesehatan</p>
                        <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah ada pemeriksaan</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activities -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        Aktivitas Terbaru
                    </h3>
                    <a href="{{ route('orangtua.riwayat.pemeriksaan_harian') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Lihat Semua
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($aktivitasTerbaru ?? [] as $aktivitas)
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-stethoscope text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $aktivitas['judul'] }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $aktivitas['deskripsi'] }}</p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($aktivitas['tanggal'])->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">{{ $aktivitas['status'] }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <i class="fas fa-clipboard-list text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada aktivitas terbaru</p>
                        <p class="text-gray-400 text-sm mt-1">Aktivitas kesehatan akan muncul di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Contact & Help -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-phone text-blue-600 mr-2"></i>
                    Kontak Sekolah
                </h3>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-clinic-medical text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">UKS Sekolah</p>
                                <p class="text-xs text-gray-500">Unit Kesehatan Sekolah</p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><i class="fas fa-phone mr-2 w-4"></i>(0274) 798021</p>
                            <p><i class="fas fa-envelope mr-2 w-4"></i>uks@slbn1bantul.sch.id</p>
                            <p><i class="fas fa-clock mr-2 w-4"></i>08:00 - 16:00 WIB</p>
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-md p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-ambulance text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Darurat Medis</p>
                                <p class="text-xs text-gray-500">24 Jam</p>
                            </div>
                        </div>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><i class="fas fa-phone mr-2 w-4"></i>119 / 118</p>
                            <p><i class="fas fa-hospital mr-2 w-4"></i>RS Bantul</p>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="font-medium text-blue-900 mb-2">
                            <i class="fas fa-lightbulb mr-2"></i>Tips Kesehatan
                        </h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Pantau kesehatan anak secara rutin</li>
                            <li>• Pastikan minum obat sesuai resep</li>
                            <li>• Hubungi UKS jika ada keluhan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update current time - simple implementation
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }
    
    // Update time immediately and then every minute
    updateTime();
    setInterval(updateTime, 60000);
});
</script>
@endpush