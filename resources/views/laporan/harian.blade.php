@extends('layouts.app')

@section('page_title', 'Laporan Pemeriksaan Harian')

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
        $detailRoute = 'laporan.harian.detail';
    } elseif ($isPetugas) {
        $baseRoute = 'petugas.laporan';
        $detailRoute = 'petugas.laporan.harian.detail';
    } elseif ($isDokter) {
        $baseRoute = 'dokter.laporan';
        $detailRoute = 'dokter.laporan.harian.detail';
    } else {
        $baseRoute = 'orangtua.laporan';
        $detailRoute = 'orangtua.laporan.harian.detail';
    }
    
    $harianRoute = $baseRoute . '.harian';
    $exportRoute = $baseRoute . '.harian.export';
    $pdfRoute = $baseRoute . '.harian.pdf';
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="p-5 bg-white border-b">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <h5 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-check text-blue-500 mr-2"></i> 
                    Laporan Pemeriksaan Harian
                </h5>
                
                @if($isDokter)
                    <span class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                        <i class="fas fa-stethoscope mr-1"></i>Dokter
                    </span>
                @elseif($isPetugas)
                    <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                        <i class="fas fa-user-tie mr-1"></i>Petugas UKS
                    </span>
                @elseif($isAdmin)
                    <span class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        <i class="fas fa-user-shield mr-1"></i>Admin
                    </span>
                @elseif($isOrangTua)
                    <span class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                        <i class="fas fa-heart mr-1"></i>Orang Tua
                    </span>
                @endif
            </div>
            
            <div class="flex flex-wrap gap-2">
                @if(!$isOrangTua)
                    <button onclick="exportData()" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center text-sm">
                        <i class="fas fa-file-excel mr-2"></i> Ekspor Excel
                    </button>
                @endif
                
                @if($isOrangTua)
                    <a href="{{ route('dashboard.orangtua') }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center text-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Role-specific Information -->
    @if($isAdmin)
    <!-- Informasi Admin -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Admin</h4>
                <div class="text-xs text-blue-600 mt-2">
                    <div><strong>Fitur:</strong></div>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>Filter berdasarkan:</strong> Tanggal, Kelas, Petugas UKS, Nama Siswa</li>
                        <li><strong>Melihat semua data pemeriksaan</strong></li>
                        <li><strong>Ekspor Excel / PDF</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @elseif($isPetugas)
    <!-- Informasi Petugas UKS -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-yellow-800">Petugas UKS</h4>
                <div class="text-xs text-yellow-600 mt-2">
                    <div><strong>Fitur:</strong></div>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>Hanya dapat melihat dan mengelola data input sendiri</strong></li>
                        <li><strong>Dapat melihat detail pemeriksaan</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @elseif($isDokter)
    <!-- Informasi Dokter -->
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-green-800">Dokter</h4>
                <div class="text-xs text-green-600 mt-2">
                    <div><strong>Fitur:</strong></div>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>Melihat hasil pemeriksaan siswa yang ditugaskan</strong></li>
                        <li><strong>Tidak dapat edit/hapus data</strong></li>
                        <li><strong>Dapat memberi komentar (opsional)</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @elseif($isOrangTua && isset($siswaInfo))
    <!-- Dashboard Ringkas untuk Orang Tua - DESIGN BARU YANG RAPI -->
    <div class="bg-purple-50 p-6 border-b">
        <div class="mb-4">
            <h4 class="text-lg font-semibold text-purple-800 flex items-center">
                <i class="fas fa-user-graduate text-purple-600 mr-2"></i>
                Dashboard Ringkas - Riwayat Pemeriksaan Anak
            </h4>
            <p class="text-sm text-purple-600 mt-1">Sistem Informasi Kesehatan Siswa (SIHATI)</p>
        </div>
        
        <!-- Grid Cards untuk Info Anak -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Info Anak -->
            <div class="bg-white rounded-lg p-5 shadow-sm border border-purple-100 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-600 mb-2 flex items-center">
                            <i class="fas fa-user text-purple-600 mr-2"></i>
                            Nama Anak
                        </div>
                        <div class="text-lg font-bold text-gray-800 leading-tight">
                            {{ $siswaInfo->nama_siswa }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            NIS: {{ $siswaInfo->id_siswa }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="bg-purple-100 rounded-full p-2">
                            <i class="fas fa-id-card text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card 2: Kelas -->
            <div class="bg-white rounded-lg p-5 shadow-sm border border-purple-100 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-600 mb-2 flex items-center">
                            <i class="fas fa-school text-purple-600 mr-2"></i>
                            Kelas
                        </div>
                        <div class="text-lg font-bold text-gray-800">
                            {{ $siswaInfo->detailSiswa->kelas->Nama_Kelas ?? 'Belum ada kelas' }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="bg-purple-100 rounded-full p-2">
                            <i class="fas fa-graduation-cap text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card 3: Total Pemeriksaan -->
            <div class="bg-white rounded-lg p-5 shadow-sm border border-purple-100 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-600 mb-2 flex items-center">
                            <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                            Total Pemeriksaan
                        </div>
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $totalPemeriksaan ?? 0 }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Keseluruhan waktu
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="bg-purple-100 rounded-full p-2">
                            <i class="fas fa-chart-bar text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card 4: Pemeriksaan Bulan Ini -->
            <div class="bg-white rounded-lg p-5 shadow-sm border border-purple-100 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-600 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                            Bulan Ini
                        </div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ $pemeriksaanBulanIni ?? 0 }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::now()->format('F Y') }}
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="bg-green-100 rounded-full p-2">
                            <i class="fas fa-calendar-check text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Tambahan -->
        <div class="mt-6 bg-white rounded-lg p-4 border border-purple-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-2 mr-3">
                        <i class="fas fa-info-circle text-purple-600"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-800">Fitur yang Tersedia:</div>
                        <div class="text-xs text-gray-600">Melihat riwayat pemeriksaan harian anak Anda</div>
                    </div>
                </div>
                <div class="text-xs text-gray-500">
                    <i class="fas fa-heart text-purple-600 mr-1"></i>
                    SIHATI - Sistem Informasi Kesehatan Siswa
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
    
    <!-- Filter Section untuk Admin -->
    @if($isAdmin)
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex items-center mb-3">
            <i class="fas fa-filter text-green-500 mr-2"></i>
            <span class="font-medium text-gray-700">Filter berdasarkan</span>
        </div>
        
        <form action="{{ route($harianRoute) }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Tanggal -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar text-purple-500 mr-1"></i>
                        Tanggal
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
                
                <!-- Kelas / Jurusan -->
                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-school text-orange-500 mr-1"></i>
                        Kelas
                    </label>
                    <select name="kelas" id="kelas" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua Kelas</option>
                        @if(isset($kelasList))
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->Kode_Kelas }}" {{ request('kelas') == $kelas->Kode_Kelas ? 'selected' : '' }}>
                                    {{ $kelas->Nama_Kelas }} - {{ $kelas->jurusan->Nama_Jurusan ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <!-- Petugas UKS -->
                <div>
                    <label for="petugas" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-tie text-yellow-500 mr-1"></i>
                        Petugas
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
                
                <!-- Nama Siswa -->
                <div>
                    <label for="nama_siswa" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-graduate text-blue-500 mr-1"></i>
                        Siswa
                    </label>
                    <input type="text" name="nama_siswa" id="nama_siswa" placeholder="Cari nama siswa..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                           value="{{ request('nama_siswa') }}">
                </div>
                
                <!-- Hasil Pemeriksaan -->
                <div>
                    <label for="hasil_pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-clipboard-check text-red-500 mr-1"></i>
                        Status
                    </label>
                    <select name="hasil_pemeriksaan" id="hasil_pemeriksaan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Semua</option>
                        <option value="ada" {{ request('hasil_pemeriksaan') == 'ada' ? 'selected' : '' }}>Ada Hasil</option>
                        <option value="tidak_ada" {{ request('hasil_pemeriksaan') == 'tidak_ada' ? 'selected' : '' }}>Belum Ada Hasil</option>
                    </select>
                </div>
                
                <!-- Tombol -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition-colors duration-200 flex items-center text-sm">
                        <i class="fas fa-search mr-2"></i> Cari
                    </button>
                    @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'nama_siswa', 'kelas', 'petugas', 'hasil_pemeriksaan']))
                        <a href="{{ route($harianRoute) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md border transition-colors duration-200 flex items-center text-sm">
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
        @if($isAdmin && isset($harianData))
            <!-- Tabel Admin -->
            <div class="bg-blue-50 p-3 border-b">
                <h6 class="text-sm font-medium text-blue-800 flex items-center">
                    <i class="fas fa-table text-blue-500 mr-2"></i> Data Pemeriksaan Harian ({{ $harianData->total() ?? 0 }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($harianData as $index => $harian)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $harianData->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($harian->tanggal)->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($harian->tanggal)->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $harian->nama_siswa }}
                                <div class="text-xs text-gray-500">{{ $harian->siswa_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $harian->kelas }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $harian->petugas }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $harian->hasil_pemeriksaan }}">
                                {{ $harian->hasil_pemeriksaan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-1">
                                    <a href="javascript:void(0)" onclick="viewDetail('{{ $harian->siswa_id }}', '{{ $harian->id }}')" 
                                       class="text-white px-2 py-1 rounded bg-blue-500 hover:bg-blue-700" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="downloadPDF('{{ $harian->siswa_id }}', '{{ $harian->id }}')" 
                                       class="text-white px-2 py-1 rounded bg-red-500 hover:bg-red-700" title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-5 mb-4">
                                        <i class="fas fa-calendar-check text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan harian</h3>
                                    <p class="text-gray-400">Belum ada data pemeriksaan harian yang tersedia</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($isPetugas && isset($harianData))
            <!-- Tabel Petugas UKS -->
            <div class="bg-yellow-50 p-3 border-b">
                <h6 class="text-sm font-medium text-yellow-800 flex items-center">
                    <i class="fas fa-table text-yellow-500 mr-2"></i> Petugas UKS ({{ $harianData->total() ?? 0 }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($harianData as $index => $harian)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $harianData->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($harian->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $harian->nama_siswa }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $harian->hasil_pemeriksaan }}">
                                {{ $harian->hasil_pemeriksaan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-1">
                                    <a href="javascript:void(0)" onclick="viewDetail('{{ $harian->siswa_id }}', '{{ $harian->id }}')" 
                                       class="text-white px-2 py-1 rounded bg-blue-500 hover:bg-blue-700" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-5 mb-4">
                                        <i class="fas fa-user-tie text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-1">Belum ada data yang Anda input</h3>
                                    <p class="text-gray-400">Silakan tambah data pemeriksaan harian baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($isDokter && isset($harianData))
            <!-- Tabel Dokter -->
            <div class="bg-green-50 p-3 border-b">
                <h6 class="text-sm font-medium text-green-800 flex items-center">
                    <i class="fas fa-table text-green-500 mr-2"></i> Dokter ({{ $harianData->total() ?? 0 }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($harianData as $index => $harian)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $harianData->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($harian->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $harian->nama_siswa }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $harian->petugas }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $harian->ringkasan }}">
                                {{ $harian->ringkasan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-1">
                                    <a href="javascript:void(0)" onclick="viewDetail('{{ $harian->siswa_id }}', '{{ $harian->id }}')" 
                                       class="text-white px-2 py-1 rounded bg-blue-500 hover:bg-blue-700" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-5 mb-4">
                                        <i class="fas fa-stethoscope text-4xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan harian</h3>
                                    <p class="text-gray-400">Belum ada data pemeriksaan harian yang tersedia</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif($isOrangTua && isset($pemeriksaanHarian))
            <!-- Tabel Orang Tua -->
            <div class="bg-purple-50 p-3 border-b">
                <h6 class="text-sm font-medium text-purple-800 flex items-center">
                    <i class="fas fa-table text-purple-500 mr-2"></i> Riwayat Pemeriksaan Harian Anak ({{ $pemeriksaanHarian->count() }} data)
                </h6>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas UKS</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan Pemeriksaan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pemeriksaanHarian as $index => $harian)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($harian->tanggal)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($harian->tanggal)->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex items-center">
                                    <div class="bg-yellow-100 rounded-full p-1 mr-2">
                                        <i class="fas fa-user-tie text-yellow-600 text-xs"></i>
                                    </div>
                                    {{ $harian->petugas_uks }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="max-w-xs">
                                    <div class="truncate" title="{{ $harian->ringkasan_pemeriksaan }}">
                                        {{ $harian->ringkasan_pemeriksaan }}
                                    </div>
                                    @if(strlen($harian->ringkasan_pemeriksaan) > 50)
                                        <button onclick="showFullText('{{ $harian->id }}')" class="text-purple-600 hover:text-purple-800 text-xs mt-1">
                                            Lihat selengkapnya...
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Selesai
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="javascript:void(0)" onclick="viewDetail('{{ session('siswa_id') }}', '{{ $harian->id }}')" 
                                   class="text-white px-2 py-1 rounded bg-blue-500 hover:bg-blue-700" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-purple-100 rounded-full p-8 mb-6">
                                        <i class="fas fa-heart text-4xl text-purple-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-500 mb-2">Tidak ada pemeriksaan harian</h3>
                                    <p class="text-gray-400 text-sm">Belum ada riwayat pemeriksaan harian untuk {{ $siswaInfo->nama_siswa ?? 'anak Anda' }}</p>
                                    <div class="mt-4 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Data akan muncul setelah petugas UKS melakukan pemeriksaan harian
                                    </div>
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
        if (isset($harianData) && method_exists($harianData, 'total')) {
            $paginatedData = $harianData;
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
                @elseif($isOrangTua)
                    <span class="text-purple-600">(Orang Tua)</span>
                @endif
            </p>
        </div>
        <div>
            {{ $paginatedData->appends(request()->except(['page', 'reset']))->links() }}
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
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
});

// Close alert function
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.display = 'none';
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

// View detail function - mengarahkan ke halaman detail
function viewDetail(siswaId, harianId) {
    @if($isAdmin)
        // Admin route: /admin/laporan/harian/detail/{siswa}/{harian}
        window.location.href = `/admin/laporan/harian/detail/${siswaId}/${harianId}`;
    @elseif($isPetugas)
        // Petugas route: /petugas/laporan/harian/detail/{siswa}/{harian}
        window.location.href = `/petugas/laporan/harian/detail/${siswaId}/${harianId}`;
    @elseif($isDokter)
        // Dokter route: /dokter/laporan/harian/detail/{siswa}/{harian}
        window.location.href = `/dokter/laporan/harian/detail/${siswaId}/${harianId}`;
    @elseif($isOrangTua)
        // Orang Tua route: /orangtua/laporan/harian/detail/{siswa}/{harian}
        window.location.href = `/orangtua/laporan/harian/detail/${siswaId}/${harianId}`;
    @endif
}

// Download PDF function
function downloadPDF(siswaId, harianId) {
    const url = `{{ route($pdfRoute, ['siswaId' => '__SISWA_ID__']) }}?pemeriksaan_harian_id=${harianId}`.replace('__SISWA_ID__', siswaId);
    window.open(url, '_blank');
}

// Show full text function (untuk orang tua)
function showFullText(harianId) {
    // Implementasi untuk menampilkan teks lengkap dalam modal atau expand
    alert('Fitur untuk menampilkan teks lengkap');
}
</script>
@endpush
@endsection