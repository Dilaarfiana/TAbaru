@extends('layouts.app')

@section('page_title', 'Laporan Screening')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Check if we should reset filters
    $shouldReset = request()->has('reset');
    
    // Define routes based on user role
    if ($isAdmin) {
        $baseRoute = 'laporan';
    } elseif ($isPetugas) {
        $baseRoute = 'petugas.laporan';
    } elseif ($isDokter) {
        $baseRoute = 'dokter.laporan';
    } else {
        $baseRoute = 'orangtua.laporan';
    }
    
    $screeningRoute = $baseRoute . '.screening';
    $exportRoute = $baseRoute . '.screening.export';
    $pdfRoute = $baseRoute . '.screening.pdf';
    $previewRoute = $baseRoute . '.screening.preview';
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 lg:mb-0 flex items-center">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i> 
            @if($isOrangTua)
                Riwayat Pemeriksaan Anak
            @elseif($isDokter)
                Laporan Pemeriksaan Siswa
            @elseif($isPetugas)
                Pemeriksaan Screening Siswa
            @else
                Laporan Screening Kesehatan
            @endif
            
            @if($isDokter)
                <span class="ml-3 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                    <i class="fas fa-stethoscope mr-1"></i>Role: Dokter
                </span>
            @elseif($isPetugas)
                <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                    <i class="fas fa-user-tie mr-1"></i>Role: Petugas UKS
                </span>
            @elseif($isAdmin)
                <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                    <i class="fas fa-user-shield mr-1"></i>Admin Panel
                </span>
            @elseif($isOrangTua)
                <span class="ml-3 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                    <i class="fas fa-heart mr-1"></i>Role: Orang Tua
                </span>
            @endif
        </h5>
        
        <div class="flex flex-wrap gap-2">
            @if(!$isOrangTua)
                <button onclick="exportData()" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </button>
            @endif
            
            @if($isOrangTua)
                <a href="{{ route('dashboard.orangtua') }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                </a>
            @endif
        </div>
    </div>
    
    <!-- Dashboard Ringkas untuk Orang Tua -->
    @if($isOrangTua && isset($siswaInfo))
    <div class="bg-purple-50 p-6 border-b">
        <h6 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
            <i class="fas fa-user-graduate text-purple-600 mr-2"></i>
            Dashboard Ringkas
        </h6>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm text-gray-600">Nama Anak:</div>
                <div class="font-semibold text-gray-800">{{ $siswaInfo->nama_siswa }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm text-gray-600">Kelas:</div>
                <div class="font-semibold text-gray-800">{{ $siswaInfo->detailSiswa->kelas->Nama_Kelas ?? 'VIII-A' }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm text-gray-600">Status Pemeriksaan:</div>
                <div class="font-semibold">
                    @if(isset($pemeriksaanTerakhir) && $pemeriksaanTerakhir->status_pemeriksaan == 'lengkap')
                        <span class="text-green-600">Lengkap</span>
                    @else
                        <span class="text-red-600">Belum Lengkap</span>
                    @endif
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm text-gray-600">Terakhir Diperiksa:</div>
                <div class="font-semibold text-gray-800">
                    {{ isset($pemeriksaanTerakhir) && $pemeriksaanTerakhir->tanggal_jam ? \Carbon\Carbon::parse($pemeriksaanTerakhir->tanggal_jam)->format('Y-m-d') : '2025-05-04' }}
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm text-gray-600">Dokter Pemeriksa:</div>
                <div class="font-semibold text-gray-800">
                    {{ isset($pemeriksaanTerakhir) ? $pemeriksaanTerakhir->dokter->Nama_Dokter ?? 'dr. Lila' : 'dr. Lila' }}
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm text-gray-600">Petugas UKS:</div>
                <div class="font-semibold text-gray-800">
                    {{ isset($pemeriksaanTerakhir) ? $pemeriksaanTerakhir->petugasUks->nama_petugas_uks ?? 'Bu Rini' : 'Bu Rini' }}
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3 flex items-center justify-between" id="success-alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
        <button type="button" class="close-alert text-green-500 hover:text-green-600" onclick="closeAlert('success-alert')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-4 mt-3 flex items-center justify-between" id="error-alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        <button type="button" class="close-alert text-red-500 hover:text-red-600" onclick="closeAlert('error-alert')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
    
    <!-- Filter Section - SAMA UNTUK SEMUA ROLE -->
    @if(!$isOrangTua)
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex items-center mb-3">
            <i class="fas fa-filter text-green-500 mr-2"></i>
            <span class="font-medium text-gray-700">Filter Pencarian</span>
            <button type="button" onclick="toggleFilter()" class="ml-auto text-blue-600 hover:text-blue-800 text-sm cursor-pointer">
                <i class="fas fa-chevron-down transition-transform duration-200" id="filter-icon"></i> 
                <span id="filter-text">Tampilkan Filter</span>
            </button>
        </div>
        
        <form action="{{ route($screeningRoute) }}" method="GET" class="space-y-4 hidden" id="filter-section">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Tanggal Pemeriksaan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar text-purple-500 mr-1"></i>
                        Periode Pemeriksaan
                    </label>
                    <div class="flex space-x-2">
                        <input type="date" name="tanggal_dari" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm" 
                               value="{{ request('tanggal_dari') }}"
                               max="{{ date('Y-m-d') }}">
                        <span class="flex items-center text-gray-500">s/d</span>
                        <input type="date" name="tanggal_sampai" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm" 
                               value="{{ request('tanggal_sampai') }}"
                               max="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <!-- Nama Siswa -->
                <div>
                    <label for="nama_siswa" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-search text-blue-500 mr-1"></i>
                        Nama Siswa
                    </label>
                    <input type="text" name="nama_siswa" id="nama_siswa" placeholder="Cari nama siswa..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                           value="{{ request('nama_siswa') }}">
                </div>
                
                <!-- Status Pemeriksaan -->
                <div>
                    <label for="status_pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-clipboard-check text-red-500 mr-1"></i>
                        Status
                    </label>
                    <select name="status_pemeriksaan" id="status_pemeriksaan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="lengkap" {{ request('status_pemeriksaan') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                        <option value="belum lengkap" {{ request('status_pemeriksaan') == 'belum lengkap' ? 'selected' : '' }}>Belum Lengkap</option>
                    </select>
                </div>
                
                @if($isPetugas)
                <!-- Status Input - Khusus Petugas -->
                <div>
                    <label for="status_input" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-edit text-orange-500 mr-1"></i>
                        Status Input
                    </label>
                    <select name="status_input" id="status_input" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="sudah_diisi" {{ request('status_input') == 'sudah_diisi' ? 'selected' : '' }}>Sudah Diisi</option>
                        <option value="belum_diisi" {{ request('status_input') == 'belum_diisi' ? 'selected' : '' }}>Belum Diisi</option>
                    </select>
                </div>
                @elseif($isAdmin)
                <!-- Dokter & Petugas - Khusus Admin -->
                <div>
                    <label for="dokter" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-md text-green-500 mr-1"></i>
                        Dokter Pemeriksa
                    </label>
                    <select name="dokter" id="dokter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Dokter</option>
                        @if(isset($dokters))
                            @foreach($dokters as $dokter)
                                <option value="{{ $dokter->Id_Dokter }}" {{ request('dokter') == $dokter->Id_Dokter ? 'selected' : '' }}>
                                    {{ $dokter->Nama_Dokter }} - {{ $dokter->Spesialisasi }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div>
                    <label for="petugas" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-tie text-yellow-500 mr-1"></i>
                        Petugas UKS
                    </label>
                    <select name="petugas" id="petugas" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Petugas</option>
                        @if(isset($petugasUKS))
                            @foreach($petugasUKS as $petugas)
                                <option value="{{ $petugas->NIP }}" {{ request('petugas') == $petugas->NIP ? 'selected' : '' }}>
                                    {{ $petugas->nama_petugas_uks }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @endif
                
                <!-- Tombol Filter dan Reset -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition-colors duration-200 flex items-center text-sm">
                        <i class="fas fa-search mr-2"></i> Cari
                    </button>
                    @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'nama_siswa', 'dokter', 'petugas', 'status_pemeriksaan', 'status_input']))
                        <a href="{{ route($screeningRoute) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md border transition-colors duration-200 flex items-center text-sm">
                            <i class="fas fa-times mr-2"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    @endif
    
    <!-- Table Section -->
    <div class="overflow-x-auto">
        @if($isPetugas && isset($pemeriksaanData))
            <!-- Tabel Pemeriksaan Screening (Petugas) -->
            <div class="bg-yellow-50 p-3 border-b">
                <h6 class="text-sm font-medium text-yellow-800 flex items-center">
                    <i class="fas fa-table text-yellow-500 mr-2"></i> Tabel Pemeriksaan Screening ({{ $pemeriksaanData->total() ?? 0 }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksaan Awal</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksaan Fisik</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pemeriksaanData as $index => $pemeriksaan)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pemeriksaanData->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($pemeriksaan->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $pemeriksaan->nama_siswa }}
                                <div class="text-xs text-gray-500">{{ $pemeriksaan->siswa_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $pemeriksaan->kelas }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pemeriksaan->pemeriksaan_awal)
                                    <i class="fas fa-check-circle text-green-500 text-lg" title="Sudah diperiksa"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-lg" title="Belum diperiksa"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pemeriksaan->pemeriksaan_fisik)
                                    <i class="fas fa-check-circle text-green-500 text-lg" title="Sudah diperiksa"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-lg" title="Belum diperiksa"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pemeriksaan->status == 'lengkap')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Lengkap
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-clock mr-1"></i>Belum Lengkap
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="viewDetail('{{ $pemeriksaan->siswa_id }}', '{{ $pemeriksaan->id_detprx }}')" 
                                            class="text-blue-500 hover:text-blue-600 bg-blue-100 px-2 py-1 rounded" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="downloadPDF('{{ $pemeriksaan->siswa_id }}', '{{ $pemeriksaan->id_detprx }}')" 
                                            class="text-red-600 hover:text-red-800 bg-red-100 px-2 py-1 rounded" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-5 mb-4">
                                        <i class="fas fa-user-tie text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan</h3>
                                    <p class="text-gray-400">Belum ada data pemeriksaan yang tersedia</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($isOrangTua && isset($riwayatScreening))
            <!-- Tabel Riwayat Pemeriksaan Screening Anak (Orang Tua) -->
            <div class="bg-purple-50 p-3 border-b">
                <h6 class="text-sm font-medium text-purple-800 flex items-center">
                    <i class="fas fa-table text-purple-500 mr-2"></i> Tabel Riwayat Pemeriksaan Screening Anak ({{ $riwayatScreening->count() }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan Pemeriksaan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($riwayatScreening as $index => $riwayat)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $riwayat->tanggal ? \Carbon\Carbon::parse($riwayat->tanggal)->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $riwayat->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $riwayat->ringkasan }}">
                                {{ $riwayat->ringkasan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="viewDetailOrangTua('{{ $riwayat->siswa_id }}', '{{ $riwayat->id }}')" 
                                            class="text-blue-600 hover:text-blue-800 bg-blue-100 px-2 py-1 rounded" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-5 mb-4">
                                        <i class="fas fa-heart text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada riwayat pemeriksaan</h3>
                                    <p class="text-gray-400">Belum ada riwayat pemeriksaan untuk anak Anda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($isAdmin && isset($screeningData))
            <!-- Tabel Laporan Screening (Admin) -->
            <div class="bg-blue-50 p-3 border-b">
                <h6 class="text-sm font-medium text-blue-800 flex items-center">
                    <i class="fas fa-table text-blue-500 mr-2"></i> Tabel Laporan Screening ({{ $screeningData->total() ?? 0 }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($screeningData as $index => $screening)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $screeningData->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ \Carbon\Carbon::parse($screening->tanggal_jam)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($screening->tanggal_jam)->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $screening->nama_siswa }}
                                <div class="text-xs text-gray-500">{{ $screening->siswa_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $screening->kelas }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $screening->nama_petugas }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $screening->nama_dokter }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($screening->status_pemeriksaan == 'lengkap')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Lengkap
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-clock mr-1"></i>Belum Lengkap
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="viewDetail('{{ $screening->siswa_id }}', '{{ $screening->id_detprx }}')" 
                                            class="text-blue-600 hover:text-blue-800 bg-blue-100 px-2 py-1 rounded" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="downloadPDF('{{ $screening->siswa_id }}', '{{ $screening->id_detprx }}')" 
                                            class="text-red-600 hover:text-red-800 bg-red-100 px-2 py-1 rounded" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <button onclick="previewPDF('{{ $screening->siswa_id }}', '{{ $screening->id_detprx }}')" 
                                            class="text-green-600 hover:text-green-800 bg-green-100 px-2 py-1 rounded" title="Preview PDF">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-5 mb-4">
                                        <i class="fas fa-chart-line text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data screening</h3>
                                    <p class="text-gray-400">Belum ada data screening yang tersedia atau sesuai filter</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($isDokter && isset($pemeriksaanData))
            <!-- Tabel Pemeriksaan Pasien (Dokter) -->
            <div class="bg-green-50 p-3 border-b">
                <h6 class="text-sm font-medium text-green-800 flex items-center">
                    <i class="fas fa-table text-green-500 mr-2"></i> Tabel Pemeriksaan Pasien ({{ $pemeriksaanData->total() ?? 0 }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksaan Awal</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksaan Fisik</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pemeriksaanData as $index => $pemeriksaan)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pemeriksaanData->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($pemeriksaan->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $pemeriksaan->nama_siswa }}
                                <div class="text-xs text-gray-500">{{ $pemeriksaan->siswa_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $pemeriksaan->kelas }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pemeriksaan->pemeriksaan_awal)
                                    <i class="fas fa-check-circle text-green-500 text-lg" title="Sudah diperiksa"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-lg" title="Belum diperiksa"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pemeriksaan->pemeriksaan_fisik)
                                    <i class="fas fa-check-circle text-green-500 text-lg" title="Sudah diperiksa"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-lg" title="Belum diperiksa"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($pemeriksaan->status == 'lengkap')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Lengkap
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-clock mr-1"></i>Belum Lengkap
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="viewDetail('{{ $pemeriksaan->siswa_id }}', '{{ $pemeriksaan->id_detprx }}')" 
                                            class="text-blue-600 hover:text-blue-800 bg-blue-100 px-2 py-1 rounded" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="downloadPDF('{{ $pemeriksaan->siswa_id }}', '{{ $pemeriksaan->id_detprx }}')" 
                                            class="text-red-600 hover:text-red-800 bg-red-100 px-2 py-1 rounded" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-5 mb-4">
                                        <i class="fas fa-user-md text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan</h3>
                                    <p class="text-gray-400">Belum ada data pemeriksaan yang tersedia</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
    
    <!-- Pagination -->
    @php
        $paginatedData = null;
        if ($isAdmin && isset($screeningData)) {
            $paginatedData = $screeningData;
        } elseif ($isDokter && isset($pemeriksaanData)) {
            $paginatedData = $pemeriksaanData;
        } elseif ($isPetugas && isset($pemeriksaanData)) {
            $paginatedData = $pemeriksaanData;
        }
    @endphp
    
    @if($paginatedData && method_exists($paginatedData, 'total'))
    <div class="bg-white px-4 py-3 flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="mb-3 sm:mb-0">
            <p class="text-sm text-gray-700">
                Menampilkan <span class="font-medium">{{ $paginatedData->firstItem() ?? 0 }}</span> 
                sampai <span class="font-medium">{{ $paginatedData->lastItem() ?? 0 }}</span> 
                dari <span class="font-medium">{{ $paginatedData->total() }}</span> data
                @if($isDokter)
                    <span class="text-green-600">(Role: Dokter)</span>
                @elseif($isPetugas)
                    <span class="text-yellow-600">(Role: Petugas UKS)</span>
                @elseif($isAdmin)
                    <span class="text-blue-600">(Admin Panel)</span>
                @endif
            </p>
        </div>
        <div>
            {{ $paginatedData->appends(request()->except(['page', 'reset']))->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Modal Detail Pemeriksaan Screening untuk Orang Tua - SIMPLE WHITE DESIGN DENGAN ICON UNGU -->
@if($isOrangTua)
<div id="detailPemeriksaanModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
            <!-- Modal Header - White dengan Icon Ungu -->
            <div class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="mr-4">
                            <i class="fas fa-chart-line text-3xl text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Riwayat Pemeriksaan Anak</h3>
                            <p class="text-gray-600 text-sm mt-1">
                                <i class="fas fa-heart mr-1 text-purple-600"></i>
                                Sistem Informasi Kesehatan Siswa (SIHATI)
                            </p>
                        </div>
                    </div>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div id="detailContent" class="px-6 py-6 overflow-y-auto max-h-[70vh] bg-white">
                <!-- Loading State -->
                <div class="flex flex-col justify-center items-center py-16">
                    <div class="relative mb-6">
                        <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-purple-600"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-heartbeat text-purple-600"></i>
                        </div>
                    </div>
                    <span class="text-gray-700 text-lg font-medium">Memuat data pemeriksaan...</span>
                    <span class="text-gray-500 text-sm mt-2">Mohon tunggu sebentar</span>
                </div>
            </div>

            <!-- Modal Footer - Simple White -->
            <div class="bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex gap-3">
                        <button id="downloadBtn" onclick="downloadScreeningDetail('', '')" 
                                class="bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center">
                            <i class="fas fa-download mr-2 text-purple-600"></i>Download PDF
                        </button>
                    </div>
                    <button onclick="closeDetailModal()" 
                            class="bg-purple-600 text-white px-5 py-2.5 rounded-lg hover:bg-purple-700 transition-all duration-200 flex items-center">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Simple Modal Styles - White dengan Icon Ungu */
#detailPemeriksaanModal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    z-index: 9999 !important;
}

#detailPemeriksaanModal .overflow-y-auto {
    max-height: 70vh !important;
    overflow-y: auto !important;
}

/* Simple scrollbar */
#detailPemeriksaanModal .overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

#detailPemeriksaanModal .overflow-y-auto::-webkit-scrollbar-track {
    background: #f9fafb;
    border-radius: 4px;
}

#detailPemeriksaanModal .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

#detailPemeriksaanModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Simple card hover effects */
.simple-card {
    transition: all 0.2s ease;
}

.simple-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #detailPemeriksaanModal .max-w-6xl {
        max-width: 95% !important;
        margin: 0.5rem !important;
    }
    
    #detailPemeriksaanModal .max-h-\[90vh\] {
        max-height: 95vh !important;
    }
    
    #detailPemeriksaanModal .overflow-y-auto {
        max-height: 75vh !important;
    }
}

/* Simple animation for modal */
@keyframes simpleSlideIn {
    from {
        opacity: 0;
        transform: scale(0.98);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

#detailPemeriksaanModal:not(.hidden) > div > div {
    animation: simpleSlideIn 0.2s ease-out;
}
</style>
@endif

@push('scripts')
<script>
// ============= JAVASCRIPT UNTUK MODAL & FUNCTIONS =============

document.addEventListener('DOMContentLoaded', function() {
    // Handle reset parameter
    const shouldReset = {{ $shouldReset ? 'true' : 'false' }};
    if (shouldReset && window.history.pushState) {
        const url = new URL(window.location.href);
        url.searchParams.delete('reset');
        window.history.pushState({}, '', url);
    }
    
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('[id$="-alert"]');
        alerts.forEach(function(alert) {
            if (alert) {
                alert.style.display = 'none';
            }
        });
    }, 5000);
    
    // Show filter if there are active filters
    const hasActiveFilters = {{ request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'nama_siswa', 'dokter', 'petugas', 'status_pemeriksaan']) ? 'true' : 'false' }};
    if (hasActiveFilters) {
        const filterSection = document.getElementById('filter-section');
        const filterIcon = document.getElementById('filter-icon');
        const filterText = document.getElementById('filter-text');
        
        if (filterSection) {
            filterSection.classList.remove('hidden');
        }
        if (filterIcon) {
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
        }
        if (filterText) {
            filterText.textContent = 'Sembunyikan Filter';
        }
    }
});

// Global variables for current detail
let currentSiswaId = '';
let currentRekamMedisId = '';

// Close alert function
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.display = 'none';
    }
}

// Toggle filter function
function toggleFilter() {
    const filterSection = document.getElementById('filter-section');
    const filterIcon = document.getElementById('filter-icon');
    const filterText = document.getElementById('filter-text');
    
    if (filterSection && filterSection.classList.contains('hidden')) {
        filterSection.classList.remove('hidden');
        if (filterIcon) {
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
        }
        if (filterText) {
            filterText.textContent = 'Sembunyikan Filter';
        }
    } else if (filterSection) {
        filterSection.classList.add('hidden');
        if (filterIcon) {
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        }
        if (filterText) {
            filterText.textContent = 'Tampilkan Filter';
        }
    }
}

// Export function
function exportData() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route($exportRoute) }}';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add current filter parameters
    const params = new URLSearchParams(window.location.search);
    for (const [key, value] of params) {
        if (key !== 'page' && key !== 'reset') {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// View detail function
function viewDetail(siswaId, detailPemeriksaanId) {
    const userLevel = '{{ $userLevel }}';
    let detailUrl = '';
    
    // Generate URL berdasarkan role
    if (userLevel === 'admin') {
        detailUrl = `/admin/siswa/${siswaId}/screening/${detailPemeriksaanId}`;
    } else if (userLevel === 'petugas') {
        detailUrl = `/petugas/siswa/${siswaId}/screening/${detailPemeriksaanId}`;
    } else if (userLevel === 'dokter') {
        detailUrl = `/dokter/siswa/${siswaId}/screening/${detailPemeriksaanId}`;
    } else {
        detailUrl = `/orangtua/siswa/${siswaId}/screening/${detailPemeriksaanId}`;
    }
    
    // Redirect to detail page
    window.location.href = detailUrl;
}

// Download PDF function
function downloadPDF(siswaId, detailPemeriksaanId) {
    const url = `{{ route($pdfRoute, ['siswaId' => '__SISWA_ID__']) }}?detail_pemeriksaan_id=${detailPemeriksaanId}`.replace('__SISWA_ID__', siswaId);
    window.open(url, '_blank');
}

// Preview PDF function
function previewPDF(siswaId, detailPemeriksaanId) {
    const url = `{{ route($previewRoute, ['siswaId' => '__SISWA_ID__']) }}?detail_pemeriksaan_id=${detailPemeriksaanId}`.replace('__SISWA_ID__', siswaId);
    window.open(url, '_blank');
}

@if($isOrangTua)
/**
 * View detail pemeriksaan untuk orang tua - DIPERBAIKI LENGKAP UNTUK RESEP ISSUE
 */
function viewDetailOrangTua(siswaId, rekamMedisId) {
    console.log('Opening detail modal for:', { siswaId, rekamMedisId });
    
    // Set global variables
    currentSiswaId = siswaId;
    currentRekamMedisId = rekamMedisId;
    
    // Show loading state
    showLoadingState();
    
    // Show modal
    const modal = document.getElementById('detailPemeriksaanModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Update download button
    updateDownloadButton(siswaId, rekamMedisId);
    
    // Fetch data from API
    fetchPemeriksaanDetail(siswaId, rekamMedisId);
}

/**
 * Show loading state - SIMPLE WHITE DESIGN
 */
function showLoadingState() {
    const detailContent = document.getElementById('detailContent');
    if (detailContent) {
        detailContent.innerHTML = `
            <div class="flex flex-col justify-center items-center py-16">
                <div class="relative mb-6">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-200 border-t-purple-600"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-heartbeat text-purple-600"></i>
                    </div>
                </div>
                <span class="text-gray-700 text-lg font-medium">Memuat data pemeriksaan...</span>
                <span class="text-gray-500 text-sm mt-2">Mohon tunggu sebentar</span>
            </div>
        `;
    }
}

/**
 * Fetch pemeriksaan detail from API - DIPERBAIKI DENGAN ERROR HANDLING YANG LEBIH BAIK
 */
function fetchPemeriksaanDetail(siswaId, rekamMedisId) {
    const apiUrl = `/api/pemeriksaan/detail/${siswaId}/${rekamMedisId}`;
    
    console.log('Fetching from API:', apiUrl);
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('API Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('API Response data:', data);
        if (data.success) {
            renderDetailContent(data.data, siswaId, rekamMedisId);
        } else {
            throw new Error(data.message || 'Gagal memuat detail pemeriksaan');
        }
    })
    .catch(error => {
        console.error('Error fetching detail:', error);
        showErrorContent(error.message, siswaId, rekamMedisId);
    });
}

/**
 * Render detail content - SIMPLE WHITE DESIGN DENGAN ICON UNGU SAJA
 */
function renderDetailContent(data, siswaId, rekamMedisId) {
    const detailContent = document.getElementById('detailContent');
    if (!detailContent) return;
    
    // Helper function untuk format value
    const formatValue = (value, defaultText = 'Tidak ada data') => {
        return value && value !== null && value !== '' ? value : defaultText;
    };
    
    // Debug resep data
    console.log('=== RESEP DEBUG INFO ===');
    console.log('Resep data from API:', data.resep);
    console.log('Resep count:', data.resep ? data.resep.length : 0);
    console.log('Resep is array:', Array.isArray(data.resep));
    console.log('=========================');
    
    detailContent.innerHTML = `
        <!-- Dashboard Ringkas Header -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user-graduate text-purple-600 mr-3 text-3xl"></i>
                Dashboard Ringkas
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white simple-card p-5 rounded-lg shadow border border-gray-200">
                    <div class="text-sm text-gray-600 mb-1">Nama Anak:</div>
                    <div class="font-bold text-gray-800 text-lg">${formatValue(data.nama_siswa)}</div>
                </div>
                <div class="bg-white simple-card p-5 rounded-lg shadow border border-gray-200">
                    <div class="text-sm text-gray-600 mb-1">Tanggal Pemeriksaan:</div>
                    <div class="font-semibold text-gray-800">${formatValue(data.tanggal_pemeriksaan)}</div>
                </div>
                <div class="bg-white simple-card p-5 rounded-lg shadow border border-gray-200">
                    <div class="text-sm text-gray-600 mb-1">Dokter Pemeriksa:</div>
                    <div class="font-semibold text-gray-800">${formatValue(data.nama_dokter)}</div>
                </div>
            </div>
        </div>

        <!-- REKAM MEDIS -->
        <div class="bg-white simple-card rounded-lg p-6 mb-8 border border-gray-200 shadow">
            <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center border-b border-gray-200 pb-3">
                <i class="fas fa-file-medical text-purple-600 text-xl mr-3"></i>
                Rekam Medis
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-exclamation-circle text-purple-600 mr-2"></i>
                        Keluhan Utama
                    </div>
                    <div class="text-gray-800">${formatValue(data.rekam_medis?.keluhan_utama)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-history text-purple-600 mr-2"></i>
                        Riwayat Penyakit Sekarang
                    </div>
                    <div class="text-gray-800">${formatValue(data.rekam_medis?.riwayat_penyakit_sekarang)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        Riwayat Penyakit Dahulu
                    </div>
                    <div class="text-gray-800">${formatValue(data.rekam_medis?.riwayat_penyakit_dahulu)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-shield-alt text-purple-600 mr-2"></i>
                        Riwayat Imunisasi
                    </div>
                    <div class="text-gray-800">${formatValue(data.rekam_medis?.riwayat_imunisasi)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-users text-purple-600 mr-2"></i>
                        Riwayat Penyakit Keluarga
                    </div>
                    <div class="text-gray-800">${formatValue(data.rekam_medis?.riwayat_penyakit_keluarga)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-sitemap text-purple-600 mr-2"></i>
                        Silsilah Keluarga
                    </div>
                    <div class="text-gray-800">${formatValue(data.rekam_medis?.silsilah_keluarga)}</div>
                </div>
            </div>
        </div>

        <!-- PEMERIKSAAN AWAL -->
        ${data.pemeriksaan_awal ? `
        <div class="bg-white simple-card rounded-lg p-6 mb-8 border border-gray-200 shadow">
            <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center border-b border-gray-200 pb-3">
                <i class="fas fa-heartbeat text-purple-600 text-xl mr-3"></i>
                Pemeriksaan Awal
            </h4>
            
            <!-- Tanda Vital -->
            <div class="mb-8">
                <h5 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>
                    Tanda Vital
                </h5>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_awal.suhu, '-')}${data.pemeriksaan_awal.suhu ? '°C' : ''}</div>
                        <div class="text-sm text-gray-600 font-medium">Suhu Badan</div>
                    </div>
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_awal.nadi, '-')}${data.pemeriksaan_awal.nadi ? '/mnt' : ''}</div>
                        <div class="text-sm text-gray-600 font-medium">Nadi</div>
                    </div>
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_awal.tegangan, '-')}</div>
                        <div class="text-sm text-gray-600 font-medium">Tekanan Darah</div>
                    </div>
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_awal.pernapasan, '-')}${data.pemeriksaan_awal.pernapasan ? '/mnt' : ''}</div>
                        <div class="text-sm text-gray-600 font-medium">Pernapasan</div>
                    </div>
                </div>
            </div>
            
            <!-- Detail Pemeriksaan Awal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Hasil Pemeriksaan</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.pemeriksaan)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Keluhan Dahulu</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.keluhan_dahulu)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Tipe Nyeri</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.tipe)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Status Nyeri (Skala 1-10)</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.status_nyeri)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Karakteristik Nyeri</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.karakteristik)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Lokasi Nyeri</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.lokasi)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Durasi Nyeri</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.durasi)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Frekuensi Nyeri</div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_awal.frekuensi)}</div>
                </div>
            </div>
        </div>
        ` : ''}

        <!-- PEMERIKSAAN FISIK -->
        ${data.pemeriksaan_fisik ? `
        <div class="bg-white simple-card rounded-lg p-6 mb-8 border border-gray-200 shadow">
            <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center border-b border-gray-200 pb-3">
                <i class="fas fa-ruler text-purple-600 text-xl mr-3"></i>
                Pemeriksaan Fisik
            </h4>
            
            <!-- Pengukuran Antropometri -->
            <div class="mb-8">
                <h5 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-weight text-purple-600 mr-2"></i>
                    Pengukuran Antropometri
                </h5>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_fisik.tinggi_badan, '-')}${data.pemeriksaan_fisik.tinggi_badan ? ' cm' : ''}</div>
                        <div class="text-sm text-gray-600 font-medium">Tinggi Badan</div>
                    </div>
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_fisik.berat_badan, '-')}${data.pemeriksaan_fisik.berat_badan ? ' kg' : ''}</div>
                        <div class="text-sm text-gray-600 font-medium">Berat Badan</div>
                    </div>
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_fisik.lingkar_kepala, '-')}${data.pemeriksaan_fisik.lingkar_kepala ? ' cm' : ''}</div>
                        <div class="text-sm text-gray-600 font-medium">Lingkar Kepala</div>
                    </div>
                    <div class="bg-white simple-card p-4 rounded-lg text-center border border-gray-200">
                        <div class="text-2xl font-bold text-gray-800 mb-1">${formatValue(data.pemeriksaan_fisik.lingkar_lengan_atas, '-')}${data.pemeriksaan_fisik.lingkar_lengan_atas ? ' cm' : ''}</div>
                        <div class="text-sm text-gray-600 font-medium">Lingkar Lengan</div>
                    </div>
                </div>
            </div>
            
            <!-- Pemeriksaan Organ -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-lungs text-purple-600 mr-2"></i>
                        Pemeriksaan Dada
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.dada)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-heartbeat text-purple-600 mr-2"></i>
                        Pemeriksaan Jantung
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.jantung)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-wind text-purple-600 mr-2"></i>
                        Pemeriksaan Paru
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.paru)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-circle text-purple-600 mr-2"></i>
                        Pemeriksaan Perut
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.perut)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-liver text-purple-600 mr-2"></i>
                        Pemeriksaan Hepar
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.hepar)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-user-md text-purple-600 mr-2"></i>
                        Pemeriksaan Anogenital
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.anogenital)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-hand-paper text-purple-600 mr-2"></i>
                        Pemeriksaan Ekstremitas
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.ekstremitas)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-head-side-mask text-purple-600 mr-2"></i>
                        Pemeriksaan Kepala
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.kepala)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg md:col-span-2 border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-microscope text-purple-600 mr-2"></i>
                        Pemeriksaan Penunjang
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.pemeriksaan_penunjang)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle text-purple-600 mr-2"></i>
                        Masalah Aktif
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.masalah_aktif)}</div>
                </div>
                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                    <div class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                        Rencana Medis & Terapi
                    </div>
                    <div class="text-gray-800">${formatValue(data.pemeriksaan_fisik.rencana_medis_dan_terapi)}</div>
                </div>
            </div>
        </div>
        ` : ''}

        <!-- RESEP OBAT - SIMPLE WHITE DESIGN -->
        <div class="bg-white simple-card rounded-lg p-6 border border-gray-200 shadow">
            <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center border-b border-gray-200 pb-3">
                <i class="fas fa-pills text-purple-600 text-xl mr-3"></i>
                Resep Obat
                <span class="ml-3 px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full font-semibold">
                    ${data.resep && Array.isArray(data.resep) ? data.resep.length : 0} obat
                </span>
            </h4>
            ${data.resep && Array.isArray(data.resep) && data.resep.length > 0 ? `
                <div class="space-y-4">
                    ${data.resep.map((resep, index) => `
                        <div class="bg-white simple-card border border-gray-200 p-5 rounded-lg">
                            <div class="flex justify-between items-start mb-4">
                                <div class="font-bold text-gray-800 text-lg flex items-center">
                                    <span class="bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm mr-3 font-bold">${index + 1}</span>
                                    <div class="flex flex-col">
                                        <span class="text-lg text-gray-800">${formatValue(resep.nama_obat, 'Nama obat tidak tersedia')}</span>
                                        <span class="text-xs text-gray-500 font-normal mt-1">ID Resep: ${resep.id_resep || 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full font-medium">
                                    <i class="fas fa-calendar mr-1 text-purple-600"></i>
                                    ${formatValue(resep.tanggal_resep, 'Tanggal tidak tersedia')}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                                    <span class="text-sm font-semibold text-gray-600 block mb-2 flex items-center">
                                        <i class="fas fa-prescription-bottle text-purple-600 mr-2"></i>Dosis Obat:
                                    </span>
                                    <span class="text-gray-800 font-medium">${formatValue(resep.dosis, 'Tidak ada informasi dosis')}</span>
                                </div>
                                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                                    <span class="text-sm font-semibold text-gray-600 block mb-2 flex items-center">
                                        <i class="fas fa-clock text-purple-600 mr-2"></i>Durasi Pengobatan:
                                    </span>
                                    <span class="text-gray-800 font-medium">${formatValue(resep.durasi, 'Tidak ada informasi durasi')}</span>
                                </div>
                                <div class="bg-white simple-card p-4 rounded-lg border border-gray-200">
                                    <span class="text-sm font-semibold text-gray-600 block mb-2 flex items-center">
                                        <i class="fas fa-user-md text-purple-600 mr-2"></i>Dokter Pemberi:
                                    </span>
                                    <span class="text-gray-800 font-medium">${formatValue(resep.dokter, 'Dokter tidak tersedia')}</span>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="text-sm text-gray-700 flex items-start">
                                    <i class="fas fa-info-circle mr-2 mt-0.5 text-purple-600"></i>
                                    <div>
                                        <strong>Petunjuk:</strong> Konsumsi obat sesuai dosis dan durasi yang telah ditentukan. Jangan menghentikan pengobatan tanpa konsultasi dokter.
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                <!-- Catatan Penting untuk Resep -->
                <div class="mt-6 bg-gray-50 border border-gray-200 p-5 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-purple-600 mr-3 mt-1 text-lg"></i>
                        <div class="text-sm">
                            <strong class="text-gray-800 text-base block mb-3">Catatan Penting Penggunaan Obat:</strong>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-white simple-card p-3 rounded border border-gray-200">
                                    <strong class="text-gray-700 block mb-1 flex items-center">
                                        <i class="fas fa-clock mr-2 text-purple-600"></i>Dosis & Waktu:
                                    </strong>
                                    <span class="text-gray-600 text-sm">Gunakan obat sesuai dosis dan waktu yang ditentukan dokter</span>
                                </div>
                                <div class="bg-white simple-card p-3 rounded border border-gray-200">
                                    <strong class="text-gray-700 block mb-1 flex items-center">
                                        <i class="fas fa-comments mr-2 text-purple-600"></i>Konsultasi:
                                    </strong>
                                    <span class="text-gray-600 text-sm">Jangan hentikan pengobatan tanpa konsultasi dokter</span>
                                </div>
                                <div class="bg-white simple-card p-3 rounded border border-gray-200">
                                    <strong class="text-gray-700 block mb-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2 text-purple-600"></i>Efek Samping:
                                    </strong>
                                    <span class="text-gray-600 text-sm">Hubungi dokter jika mengalami efek samping</span>
                                </div>
                                <div class="bg-white simple-card p-3 rounded border border-gray-200">
                                    <strong class="text-gray-700 block mb-1 flex items-center">
                                        <i class="fas fa-home mr-2 text-purple-600"></i>Penyimpanan:
                                    </strong>
                                    <span class="text-gray-600 text-sm">Simpan obat di tempat aman dan sejuk</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ` : `
                <div class="text-center py-16">
                    <div class="bg-gray-100 rounded-full p-12 mx-auto mb-6 w-32 h-32 flex items-center justify-center">
                        <i class="fas fa-pills text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-3">Tidak Ada Resep Obat</h3>
                    <p class="text-gray-500 text-base mb-6">Tidak ada resep obat yang diberikan untuk pemeriksaan ini</p>
                    <div class="bg-gray-50 rounded-lg p-4 mx-auto max-w-lg border border-gray-200">
                        <div class="text-sm text-gray-700 flex items-start">
                            <i class="fas fa-info-circle mr-2 mt-0.5 text-purple-600"></i>
                            <div>
                                <strong>Informasi:</strong> Jika memerlukan pengobatan, silakan konsultasikan dengan dokter yang menangani pemeriksaan Anda.
                            </div>
                        </div>
                    </div>
                </div>
            `}
        </div>
    `;
    
    console.log('Detail content rendered successfully with resep count:', data.resep ? data.resep.length : 0);
}

/**
 * Show error content - SIMPLE WHITE DESIGN
 */
function showErrorContent(errorMessage, siswaId, rekamMedisId) {
    const detailContent = document.getElementById('detailContent');
    if (!detailContent) return;
    
    detailContent.innerHTML = `
        <div class="text-center py-16">
            <div class="bg-white border border-gray-200 rounded-lg p-8 max-w-md mx-auto">
                <div class="bg-red-100 rounded-full p-3 w-12 h-12 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Gagal Memuat Data Pemeriksaan</h3>
                <p class="text-gray-600 mb-6">${errorMessage}</p>
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <button onclick="viewDetailOrangTua('${siswaId}', '${rekamMedisId}')" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>Coba Lagi
                    </button>
                    <button onclick="closeDetailModal()" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    `;
}

/**
 * Update download button
 */
function updateDownloadButton(siswaId, rekamMedisId) {
    const downloadBtn = document.getElementById('downloadBtn');
    if (downloadBtn) {
        downloadBtn.setAttribute('onclick', `downloadScreeningDetail('${siswaId}', '${rekamMedisId}')`);
    }
}

/**
 * Download screening detail PDF
 */
function downloadScreeningDetail(siswaId, rekamMedisId) {
    const url = `{{ route('orangtua.laporan.screening.pdf') }}?rekam_medis_id=${rekamMedisId}`;
    window.open(url, '_blank');
}

/**
 * Close modal function
 */
function closeDetailModal() {
    const modal = document.getElementById('detailPemeriksaanModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Reset global variables
    currentSiswaId = '';
    currentRekamMedisId = '';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('detailPemeriksaanModal');
    if (event.target == modal) {
        closeDetailModal();
    }
}

// ESC key to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailModal();
    }
});
@endif
</script>
@endpush

@endsection