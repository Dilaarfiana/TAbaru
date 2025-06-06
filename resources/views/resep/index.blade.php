{{-- File: resources/views/resep/index.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: REDIRECT --}}
@extends('layouts.app')

@section('page_title', 'Daftar Resep Obat')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Redirect orang tua ke halaman khusus mereka
    if ($isOrangTua) {
        header('Location: ' . route('orangtua.riwayat.resep'));
        exit;
    }
    
    // Check if user has permission to view
    if (!in_array($userLevel, ['admin', 'petugas', 'dokter'])) {
        header('Location: ' . route('dashboard'));
        exit;
    }
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-center p-6 bg-white border-b">
                <div class="flex items-center mb-3 sm:mb-0">
                    <i class="fas fa-prescription text-blue-600 mr-3 text-2xl"></i>
                    <div>
                        <h5 class="text-2xl font-bold text-gray-800 flex items-center">
                            Daftar Resep Obat
                            @if($isDokter)
                                <span class="ml-3 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                    <i class="fas fa-stethoscope mr-1"></i>Akses Dokter
                                </span>
                            @elseif($isPetugas)
                                <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                    <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                                </span>
                            @elseif($isAdmin)
                                <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                    <i class="fas fa-user-shield mr-1"></i>Akses Admin
                                </span>
                            @endif
                        </h5>
                        <div class="flex items-center mt-1">
                            <span class="text-sm text-gray-600 mr-2">Sistem Manajemen Resep</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    @if($isAdmin || $isPetugas)
                    <a href="@if($isAdmin){{ route('resep.create') }}@else{{ route('petugas.resep.create') }}@endif" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center shadow-sm">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Resep Baru
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Info Access Level -->
            @if($isDokter)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            Anda mengakses data resep dengan <strong>Akses Dokter</strong>. 
                            Anda dapat melihat semua data resep namun tidak dapat mengubah atau menghapus data.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isPetugas)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-6 mt-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Anda mengakses data resep dengan <strong>Akses Petugas</strong>. 
                            Anda dapat menambah, melihat dan mengedit data resep, namun tidak dapat menghapus data.
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Alert Messages -->
            @if(session('success'))
            <div id="notification" class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-4 flex items-center justify-between rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" id="closeNotification" class="text-green-500 hover:text-green-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-4 flex items-center justify-between rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-red-500 hover:text-red-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            @if(session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-6 mt-4 flex items-center justify-between rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 font-medium">{!! session('warning') !!}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-yellow-500 hover:text-yellow-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            <!-- Info Banner -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-6 mt-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-sm font-medium text-blue-800 mb-1">Informasi Sistem Resep</h3>
                        <p class="text-sm text-blue-700 mb-2">
                            Kelola resep obat untuk siswa dengan sistem terintegrasi. Total data: <span class="font-bold">{{ $reseps instanceof \Illuminate\Pagination\LengthAwarePaginator ? $reseps->total() : $reseps->count() }}</span> resep.
                        </p>
                        
                        <!-- Role Information -->
                        <div class="mt-2 p-2 bg-blue-100 border border-blue-300 rounded">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-user-tag mr-1"></i>
                                <strong>Akses Anda:</strong> 
                                @if($isAdmin)
                                    Administrator - Dapat mengelola semua data resep obat termasuk menghapus data
                                @elseif($isPetugas)
                                    Petugas UKS - Dapat menambah, melihat, dan mengedit data resep obat
                                @elseif($isDokter)
                                    Dokter - Dapat melihat semua data resep obat (hanya baca)
                                @else
                                    Guest - Hanya dapat melihat daftar resep obat
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter & Search -->
            <div class="bg-gray-50 p-4 border-b mx-6 mt-4 rounded-md">
                <form action="@if($isAdmin){{ route('resep.index') }}@elseif($isPetugas){{ route('petugas.resep.index') }}@else{{ route('dokter.resep.index') }}@endif" method="GET">
                    <div class="flex flex-col md:flex-row md:items-end gap-4">
                        <!-- Tanggal Dari -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>Tanggal Dari
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_dari" 
                                value="{{ request('tanggal_dari') }}"
                                class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <!-- Tanggal Sampai -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-calendar-check text-blue-500 mr-1"></i>Tanggal Sampai
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_sampai" 
                                value="{{ request('tanggal_sampai') }}"
                                class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        <!-- Filter Dokter -->
                        @if(isset($dokterList) && $dokterList->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-user-md text-green-500 mr-1"></i>Dokter
                            </label>
                            <select name="dokter_filter" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Dokter</option>
                                @foreach($dokterList as $dokter)
                                    <option value="{{ $dokter->Id_Dokter }}" {{ request('dokter_filter') == $dokter->Id_Dokter ? 'selected' : '' }}>
                                        {{ $dokter->Nama_Dokter }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <!-- Pencarian -->
                        <div class="flex-grow">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-search text-gray-500 mr-1"></i>Pencarian
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    name="keyword" 
                                    placeholder="Cari ID resep, nama siswa, dokter, atau obat..." 
                                    value="{{ request('keyword') }}"
                                    class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>
                        </div>

                        <!-- Tombol -->
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition-colors duration-200 flex items-center">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                            
                            @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'dokter_filter']))
                                <a href="@if($isAdmin){{ route('resep.index') }}@elseif($isPetugas){{ route('petugas.resep.index') }}@else{{ route('dokter.resep.index') }}@endif" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md border transition-colors duration-200 flex items-center">
                                    <i class="fas fa-times-circle mr-2"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Active Filters Section -->
            @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'dokter_filter']))
            <div class="flex justify-between bg-blue-50 px-4 py-2 mx-6 items-center rounded">
                <div class="text-sm text-blue-700">
                    <i class="fas fa-filter mr-1"></i> Filter aktif: 
                    @if(request('tanggal_dari'))
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Dari: {{ request('tanggal_dari') }}</span>
                    @endif
                    @if(request('tanggal_sampai'))
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Sampai: {{ request('tanggal_sampai') }}</span>
                    @endif
                    @if(request('dokter_filter'))
                        @php
                            $selectedDokter = $dokterList->firstWhere('Id_Dokter', request('dokter_filter'));
                        @endphp
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                            Dokter: {{ $selectedDokter->Nama_Dokter ?? request('dokter_filter') }}
                        </span>
                    @endif
                    @if(request('keyword'))
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Pencarian: {{ request('keyword') }}</span>
                    @endif
                </div>
                <a href="@if($isAdmin){{ route('resep.index') }}@elseif($isPetugas){{ route('petugas.resep.index') }}@else{{ route('dokter.resep.index') }}@endif" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-times-circle mr-1"></i> Hapus Semua Filter
                </a>
            </div>
            @endif
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6">
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="bg-blue-500 p-3 rounded-full mr-4">
                            <i class="fas fa-prescription text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Total Resep</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $reseps instanceof \Illuminate\Pagination\LengthAwarePaginator ? $reseps->total() : $reseps->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="bg-green-500 p-3 rounded-full mr-4">
                            <i class="fas fa-calendar-day text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-800">Hari Ini</p>
                            <p class="text-2xl font-bold text-green-900">{{ $reseps->where('Tanggal_Resep', '>=', today())->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="bg-yellow-500 p-3 rounded-full mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Siswa Unik</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ $reseps->pluck('Id_Siswa')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="bg-purple-500 p-3 rounded-full mr-4">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-800">Dokter Aktif</p>
                            <p class="text-2xl font-bold text-purple-900">{{ $reseps->pluck('Id_Dokter')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table Section -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID Resep
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Siswa
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dokter
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Resep
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Informasi Obat
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="resepTableBody">
                        @forelse($reseps as $resep)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-prescription text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                            {{ $resep->Id_Resep }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $resep->siswa->nama_siswa ?? 'Data tidak tersedia' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: {{ $resep->Id_Siswa }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-teal-100 flex items-center justify-center">
                                            <i class="fas fa-user-md text-teal-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $resep->dokter->Nama_Dokter ?? 'Data tidak tersedia' }}</div>
                                        <div class="text-xs text-gray-500">{{ $resep->dokter->Spesialisasi ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                            <i class="fas fa-calendar text-amber-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->locale('id')->translatedFormat('l') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fas fa-pills text-green-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $resep->Nama_Obat }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                                <i class="fas fa-syringe mr-1"></i>{{ $resep->Dosis }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-clock mr-1"></i>{{ $resep->Durasi }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-1">
                                    {{-- Tombol Detail - semua role bisa akses --}}
                                    <a href="@if($isAdmin){{ route('resep.show', $resep->Id_Resep) }}@elseif($isPetugas){{ route('petugas.resep.show', $resep->Id_Resep) }}@else{{ route('dokter.resep.show', $resep->Id_Resep) }}@endif" 
                                       class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200 shadow-sm" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Tombol Edit - hanya admin dan petugas --}}
                                    @if($isAdmin || $isPetugas)
                                    <a href="@if($isAdmin){{ route('resep.edit', $resep->Id_Resep) }}@else{{ route('petugas.resep.edit', $resep->Id_Resep) }}@endif" 
                                        class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200"
                                       title="Edit Resep">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    {{-- Tombol Cetak - semua role --}}
                                    @php
                                        $cetakRoute = '';
                                        if($isAdmin) {
                                            if(Route::has('resep.cetak')) {
                                                $cetakRoute = route('resep.cetak', $resep->Id_Resep);
                                            }
                                        } elseif($isPetugas) {
                                            if(Route::has('petugas.resep.cetak')) {
                                                $cetakRoute = route('petugas.resep.cetak', $resep->Id_Resep);
                                            }
                                        } else {
                                            if(Route::has('dokter.resep.cetak')) {
                                                $cetakRoute = route('dokter.resep.cetak', $resep->Id_Resep);
                                            }
                                        }
                                    @endphp
                                    
                                    @if($cetakRoute)
                                        <a href="{{ $cetakRoute }}" 
                                           target="_blank" 
                                           class="text-white bg-purple-500 hover:bg-purple-600 rounded-md p-2 transition-colors duration-200 shadow-sm" 
                                           title="Cetak Resep">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    @endif
                                    
                                    {{-- Tombol Hapus - hanya admin --}}
                                    @if($isAdmin)
                                    <form action="{{ route('resep.destroy', $resep->Id_Resep) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200 shadow-sm" 
                                                title="Hapus Resep" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus resep ini?\n\nID: {{ $resep->Id_Resep }}\nSiswa: {{ $resep->siswa->nama_siswa ?? 'N/A' }}\nObat: {{ $resep->Nama_Obat }}\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-8 mb-4">
                                        <i class="fas fa-prescription text-6xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-xl font-medium text-gray-600 mb-2">
                                        @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'dokter_filter']))
                                            Tidak ada resep yang sesuai dengan filter
                                        @else
                                            Belum Ada Data Resep
                                        @endif
                                    </h3>
                                    <p class="text-gray-500 mb-6 max-w-md">
                                        @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'dokter_filter']))
                                            Tidak ada resep obat yang sesuai dengan filter yang diterapkan. Coba ubah atau hapus filter untuk melihat data lainnya.
                                        @else
                                            Belum ada data resep obat yang tersedia dalam sistem. Mulai tambahkan resep untuk siswa yang memerlukan pengobatan.
                                        @endif
                                    </p>
                                    @if($isAdmin || $isPetugas)
                                    <div class="flex space-x-3">
                                        <a href="@if($isAdmin){{ route('resep.create') }}@else{{ route('petugas.resep.create') }}@endif" 
                                           class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-plus-circle mr-2"></i> 
                                            @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'dokter_filter']))
                                                Tambah Resep Baru
                                            @else
                                                Tambah Resep Pertama
                                            @endif
                                        </a>
                                    </div>
                                    @elseif(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'dokter_filter']))
                                    <div>
                                        <a href="@if($isAdmin){{ route('resep.index') }}@elseif($isPetugas){{ route('petugas.resep.index') }}@else{{ route('dokter.resep.index') }}@endif" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-times-circle mr-2"></i> Hapus Filter
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-200">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        @if(isset($reseps) && method_exists($reseps, 'total'))
                        <p class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">{{ $reseps->firstItem() ?? 0 }}</span> 
                            sampai <span class="font-medium">{{ $reseps->lastItem() ?? 0 }}</span> 
                            dari <span class="font-medium">{{ $reseps->total() }}</span> data resep
                            @if($isDokter)
                                <span class="text-green-600">(Akses Dokter)</span>
                            @elseif($isPetugas)
                                <span class="text-yellow-600">(Akses Petugas)</span>
                            @endif
                        </p>
                        @endif
                    </div>
                    <div>
                        @if(isset($reseps) && method_exists($reseps, 'links'))
                            {{ $reseps->appends(request()->except(['page', 'reset']))->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // PERBAIKAN: Hapus auto-close alerts - notifikasi akan tetap muncul sampai user menutup manual
    // Sekarang notifikasi success/error/warning akan tetap muncul dan tidak hilang otomatis
    
    // Tooltip untuk tombol aksi berdasarkan role
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'petugas') {
        // Add tooltip for petugas edit button
        const editButtons = document.querySelectorAll('a[title="Edit Resep"]');
        editButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                button.title = 'Edit resep (akses petugas)';
            });
        });
    } else if (userLevel === 'dokter') {
        // Add tooltip for dokter - no edit access
        const detailButtons = document.querySelectorAll('a[title="Lihat Detail"]');
        detailButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                button.title = 'Lihat detail resep (akses dokter - hanya baca)';
            });
        });
    }
    
    // Live search functionality untuk table
    const keywordInput = document.querySelector('input[name="keyword"]');
    if (keywordInput) {
        let searchTimeout;
        keywordInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const filter = keywordInput.value.toUpperCase();
                const tbody = document.getElementById('resepTableBody');
                const rows = tbody.getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].getElementsByTagName('td').length > 1) {
                        const idCell = rows[i].getElementsByTagName('td')[0];
                        const siswaCell = rows[i].getElementsByTagName('td')[1];
                        const dokterCell = rows[i].getElementsByTagName('td')[2];
                        const obatCell = rows[i].getElementsByTagName('td')[4];
                        
                        if (idCell && siswaCell && dokterCell && obatCell) {
                            const idValue = idCell.textContent || idCell.innerText;
                            const siswaValue = siswaCell.textContent || siswaCell.innerText;
                            const dokterValue = dokterCell.textContent || dokterCell.innerText;
                            const obatValue = obatCell.textContent || obatCell.innerText;
                            
                            if (idValue.toUpperCase().indexOf(filter) > -1 || 
                                siswaValue.toUpperCase().indexOf(filter) > -1 ||
                                dokterValue.toUpperCase().indexOf(filter) > -1 ||
                                obatValue.toUpperCase().indexOf(filter) > -1) {
                                rows[i].style.display = '';
                            } else {
                                rows[i].style.display = 'none';
                            }
                        }
                    }
                }
            }, 300); // Debounce 300ms
        });
    }
});
</script>
@endpush
@endsection