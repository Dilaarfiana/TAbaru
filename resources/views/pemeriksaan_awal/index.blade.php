{{-- File: resources/views/pemeriksaan_awal/index.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('page_title', 'Pemeriksaan Awal')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // BLOCK Orang tua - tidak boleh mengakses pemeriksaan awal sama sekali
    if ($isOrangTua) {
        return redirect()->route('dashboard.orangtua')->with('error', 'Akses ditolak. Orang tua tidak memiliki akses ke data pemeriksaan awal. Silakan gunakan menu "Ringkasan Kesehatan" untuk melihat informasi kesehatan anak Anda.');
    }
    
    // Define routes based on user role
    $routes = [
        'admin' => [
            'index' => 'pemeriksaan_awal.index',
            'create' => 'pemeriksaan_awal.create',
            'show' => 'pemeriksaan_awal.show',
            'edit' => 'pemeriksaan_awal.edit',
            'destroy' => 'pemeriksaan_awal.destroy'
        ],
        'petugas' => [
            'index' => 'petugas.pemeriksaan_awal.index',
            'create' => 'petugas.pemeriksaan_awal.create',
            'show' => 'petugas.pemeriksaan_awal.show',
            'edit' => 'petugas.pemeriksaan_awal.edit',
            'destroy' => null // Petugas tidak bisa delete
        ],
        'dokter' => [
            'index' => 'dokter.pemeriksaan_awal.index',
            'create' => null, // Dokter tidak bisa create
            'show' => 'dokter.pemeriksaan_awal.show',
            'edit' => null, // Dokter tidak bisa edit
            'destroy' => null // Dokter tidak bisa delete
        ]
        // REMOVED: orang_tua routes - mereka tidak boleh akses sama sekali
    ];
    
    $currentRoutes = $routes[$userLevel];
    
    // Permission checks
    $canCreate = !is_null($currentRoutes['create']) && ($isAdmin || $isPetugas);
    $canEdit = !is_null($currentRoutes['edit']) && ($isAdmin || $isPetugas);
    $canDelete = !is_null($currentRoutes['destroy']) && $isAdmin; // Hanya admin yang bisa hapus
    $canFilter = true; // Semua role yang bisa akses bisa filter
    
    // Check if we should reset filters
    $shouldReset = request()->has('reset');
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-clipboard-check text-blue-500 mr-2"></i> Daftar Pemeriksaan Awal
            
            {{-- Role Badge --}}
            @if($isDokter)
                <span class="ml-3 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                    <i class="fas fa-stethoscope mr-1"></i>Akses Dokter (Read Only)
                </span>
            @elseif($isPetugas)
                <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                    <i class="fas fa-user-tie mr-1"></i>Akses Petugas (CRU)
                </span>
            @elseif($isAdmin)
                <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                    <i class="fas fa-user-shield mr-1"></i>Akses Admin (Full CRUD)
                </span>
            @endif
        </h5>
        
        <div class="flex space-x-2">
            @if($canCreate)
            <a href="{{ route($currentRoutes['create']) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan
            </a>
            @endif
        </div>
    </div>
    
    <!-- Access Level Info -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Informasi Hak Akses</h4>
                <p class="text-xs text-blue-600 mt-1">
                    @if($isAdmin)
                        <strong>Administrator:</strong> Akses penuh - Dapat menambah, melihat, mengedit, dan menghapus semua data pemeriksaan awal
                    @elseif($isPetugas)
                        <strong>Petugas UKS:</strong> Dapat menambah, melihat, dan mengedit data pemeriksaan awal. 
                        <span class="text-red-600 font-semibold">Tidak dapat menghapus data.</span>
                    @elseif($isDokter)
                        <strong>Dokter:</strong> Hanya dapat melihat data pemeriksaan awal. 
                        <span class="text-yellow-600 font-semibold">Tidak dapat menambah, mengedit, atau menghapus data.</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
    
    <!-- Filter & Search -->
    @if($canFilter)
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex flex-col md:flex-row gap-3 justify-between">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <form id="searchForm" action="{{ route($currentRoutes['index']) }}" method="GET" class="inline">
                    <input id="searchInput" name="keyword" type="text" placeholder="Cari ID pemeriksaan atau detail..." class="pl-10 pr-10 py-2 border border-gray-300 rounded-md w-full md:w-80 focus:ring-blue-500 focus:border-blue-500" value="{{ request('keyword') }}">
                    <button type="button" id="clearSearchBtn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" style="display: {{ request('keyword') ? 'block' : 'none' }};">
                        &times;
                    </button>
                </form>
            </div>
            
            <div class="flex space-x-2">              
                <button id="showFilterModal" class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center">
                    <i class="fas fa-filter text-blue-500 mr-2"></i> Filter
                </button>
            </div>
        </div>
    </div>
    
    <!-- Active Filters Section -->
    @if(request()->hasAny(['keyword', 'suhu', 'status_nyeri', 'tanggal']) && !$shouldReset)
    <div class="flex justify-between bg-blue-50 px-4 py-2 items-center">
        <div class="text-sm text-blue-700">
            <i class="fas fa-filter mr-1"></i> Filter aktif: 
            @if(request('keyword'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Pencarian: {{ request('keyword') }}</span>
            @endif
            @if(request('suhu'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                    Suhu: {{ request('suhu') == 'normal' ? 'Normal' : (request('suhu') == 'tinggi' ? 'Tinggi' : 'Rendah') }}
                </span>
            @endif
            @if(request('status_nyeri') !== null)
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                    Status Nyeri: 
                    @if(request('status_nyeri') == '0') Tidak Ada
                    @elseif(request('status_nyeri') == '1') Ringan
                    @elseif(request('status_nyeri') == '2') Sedang
                    @elseif(request('status_nyeri') == '3') Berat
                    @endif
                </span>
            @endif
            @if(request('tanggal'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Tanggal: {{ request('tanggal') }}</span>
            @endif
        </div>
        <a href="{{ route($currentRoutes['index']) }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-times-circle mr-1"></i> Hapus Semua Filter
        </a>
    </div>
    @endif
    @endif
    
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3 flex items-center justify-between">
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
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-4 mt-3 flex items-center justify-between">
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
    
    @if(session('info'))
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3 flex items-center justify-between">
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
    
    @if(session('warning'))
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-4 mt-3 flex items-center justify-between">
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
    
    <!-- Informasi Pemeriksaan Awal -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Informasi Pemeriksaan Awal</h4>
                <p class="text-xs text-blue-600 mt-1">
                    <strong>Pemeriksaan awal</strong> meliputi pengukuran tanda vital dasar seperti suhu tubuh, nadi, pernapasan, dan penilaian tingkat nyeri.<br>
                    Data ini menjadi dasar untuk pemeriksaan lebih lanjut dan diagnosis awal kondisi kesehatan siswa.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID Pemeriksaan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Detail Pemeriksaan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Suhu (°C)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nadi (BPM)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pernapasan (/menit)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status Nyeri
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="pemeriksaanTableBody">
                @forelse($pemeriksaanAwals as $pemeriksaanAwal)
                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                            {{ $pemeriksaanAwal->id_preawal }}
                        </span>
                        <div class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ isset($pemeriksaanAwal->created_at) ? \Carbon\Carbon::parse($pemeriksaanAwal->created_at)->format('d/m/Y') : 'N/A' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $pemeriksaanAwal->id_detprx ?? '-' }}
                        @if($pemeriksaanAwal->detailPemeriksaan && $pemeriksaanAwal->detailPemeriksaan->siswa)
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-user mr-1"></i>{{ $pemeriksaanAwal->detailPemeriksaan->siswa->nama_siswa }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->suhu)
                            <div class="flex flex-col">
                                <span class="font-medium @if($pemeriksaanAwal->suhu > 37.5) text-red-600 @else text-gray-800 @endif">
                                    {{ $pemeriksaanAwal->suhu }}°C
                                </span>
                                @if($pemeriksaanAwal->suhu > 37.5)
                                    <span class="text-xs text-red-500">
                                        <i class="fas fa-thermometer-full mr-1"></i> Demam
                                    </span>
                                @elseif($pemeriksaanAwal->suhu < 36.0)
                                    <span class="text-xs text-blue-500">
                                        <i class="fas fa-thermometer-empty mr-1"></i> Rendah
                                    </span>
                                @else
                                    <span class="text-xs text-green-500">
                                        <i class="fas fa-thermometer-half mr-1"></i> Normal
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->nadi)
                            <div class="flex flex-col">
                                <span class="font-medium @if($pemeriksaanAwal->nadi > 100 || $pemeriksaanAwal->nadi < 60) text-orange-600 @else text-gray-800 @endif">
                                    {{ $pemeriksaanAwal->nadi }}
                                </span>
                                <span class="text-xs text-gray-500">BPM</span>
                                @if($pemeriksaanAwal->nadi > 100)
                                    <span class="text-xs text-orange-500">
                                        <i class="fas fa-arrow-up mr-1"></i>Tinggi
                                    </span>
                                @elseif($pemeriksaanAwal->nadi < 60)
                                    <span class="text-xs text-blue-500">
                                        <i class="fas fa-arrow-down mr-1"></i>Rendah
                                    </span>
                                @else
                                    <span class="text-xs text-green-500">
                                        <i class="fas fa-check mr-1"></i>Normal
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->pernapasan)
                            <div class="flex flex-col">
                                <span class="font-medium @if($pemeriksaanAwal->pernapasan > 24 || $pemeriksaanAwal->pernapasan < 12) text-orange-600 @else text-gray-800 @endif">
                                    {{ $pemeriksaanAwal->pernapasan }}
                                </span>
                                <span class="text-xs text-gray-500">per menit</span>
                                @if($pemeriksaanAwal->pernapasan > 24)
                                    <span class="text-xs text-orange-500">
                                        <i class="fas fa-arrow-up mr-1"></i>Cepat
                                    </span>
                                @elseif($pemeriksaanAwal->pernapasan < 12)
                                    <span class="text-xs text-blue-500">
                                        <i class="fas fa-arrow-down mr-1"></i>Lambat
                                    </span>
                                @else
                                    <span class="text-xs text-green-500">
                                        <i class="fas fa-check mr-1"></i>Normal
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->status_nyeri === 0)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-smile mr-1"></i> Tidak Ada
                            </span>
                        @elseif($pemeriksaanAwal->status_nyeri === 1)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-meh mr-1"></i> Ringan
                            </span>
                        @elseif($pemeriksaanAwal->status_nyeri === 2)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-frown mr-1"></i> Sedang
                            </span>
                        @elseif($pemeriksaanAwal->status_nyeri === 3)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-sad-tear mr-1"></i> Berat
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-1">
                            {{-- Tombol Detail - semua role bisa akses --}}
                            <a href="{{ route($currentRoutes['show'], $pemeriksaanAwal->id_preawal) }}" 
                               class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" 
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            {{-- Tombol Edit - hanya admin dan petugas --}}
                            @if($canEdit)
                            <a href="{{ route($currentRoutes['edit'], $pemeriksaanAwal->id_preawal) }}" 
                               class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" 
                               title="Edit{{ $isPetugas ? ' (Petugas)' : '' }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                           
                            {{-- Tombol Hapus - hanya admin --}}
                            @if($canDelete)
                                <form action="{{ route($currentRoutes['destroy'], $pemeriksaanAwal->id_preawal) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" 
                                            title="Hapus" 
                                            onclick="return confirm('PERINGATAN!\n\nApakah Anda yakin ingin menghapus data pemeriksaan ini?\n\nID: {{ $pemeriksaanAwal->id_preawal }}\n\nTindakan ini akan menghapus semua data terkait dan tidak dapat dikembalikan!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-full p-5 mb-4">
                                <i class="fas fa-clipboard-check text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan awal</h3>
                            <p class="text-gray-400 mb-4">
                                @if(request()->hasAny(['keyword', 'suhu', 'status_nyeri', 'tanggal']))
                                    Tidak ada pemeriksaan awal yang sesuai dengan filter yang diterapkan
                                @else
                                    Belum ada data pemeriksaan awal yang tersedia
                                @endif
                            </p>
                            <div class="flex space-x-3">
                                @if($canCreate)
                                <a href="{{ route($currentRoutes['create']) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan
                                </a>
                                @endif
                                @if(request()->hasAny(['keyword', 'suhu', 'status_nyeri', 'tanggal']))
                                <a href="{{ route($currentRoutes['index']) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-times-circle mr-2"></i> Hapus Filter
                                </a>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                @if(isset($pemeriksaanAwals) && method_exists($pemeriksaanAwals, 'total'))
                <p class="text-sm text-gray-700">
                    Menampilkan <span class="font-medium">{{ $pemeriksaanAwals->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $pemeriksaanAwals->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $pemeriksaanAwals->total() }}</span> data
                    @if($isDokter)
                        <span class="text-green-600">(Akses Read Only)</span>
                    @elseif($isPetugas)
                        <span class="text-yellow-600">(Akses CRU)</span>
                    @elseif($isAdmin)
                        <span class="text-blue-600">(Full Access)</span>
                    @endif
                </p>
                @endif
            </div>
            @if(isset($pemeriksaanAwals) && $pemeriksaanAwals instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>
                {{ $pemeriksaanAwals->appends(request()->except(['page', 'reset']))->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
@if($canFilter)
<div id="filterModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-800">
                <i class="fas fa-filter text-blue-500 mr-2"></i>Filter Data Pemeriksaan
            </h3>
            <button id="closeFilterModal" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route($currentRoutes['index']) }}" method="GET">
            <!-- Filter Suhu -->
            <div class="mb-4">
                <label for="suhu" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-thermometer-half text-red-500 mr-1"></i>Kategori Suhu
                </label>
                <select id="suhu" name="suhu" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kategori Suhu</option>
                    <option value="normal" {{ request('suhu') == 'normal' ? 'selected' : '' }}>Normal (36.0 - 37.5°C)</option>
                    <option value="tinggi" {{ request('suhu') == 'tinggi' ? 'selected' : '' }}>Demam (> 37.5°C)</option>
                    <option value="rendah" {{ request('suhu') == 'rendah' ? 'selected' : '' }}>Rendah (< 36.0°C)</option>
                </select>
            </div>
            
            <!-- Filter Status Nyeri -->
            <div class="mb-4">
                <label for="status_nyeri" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-hand-holding-medical text-orange-500 mr-1"></i>Status Nyeri
                </label>
                <select id="status_nyeri" name="status_nyeri" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status Nyeri</option>
                    <option value="0" {{ request('status_nyeri') == '0' ? 'selected' : '' }}>Tidak Ada Nyeri</option>
                    <option value="1" {{ request('status_nyeri') == '1' ? 'selected' : '' }}>Nyeri Ringan</option>
                    <option value="2" {{ request('status_nyeri') == '2' ? 'selected' : '' }}>Nyeri Sedang</option>
                    <option value="3" {{ request('status_nyeri') == '3' ? 'selected' : '' }}>Nyeri Berat</option>
                </select>
            </div>
            
            <!-- Filter Tanggal -->
            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>Tanggal Pemeriksaan
                </label>
                <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <!-- Kata Kunci Pencarian -->
            <div class="mb-4">
                <label for="modal_keyword" class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-search text-gray-500 mr-1"></i>Kata Kunci Pencarian
                </label>
                <input type="text" id="modal_keyword" name="keyword" value="{{ request('keyword') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari ID pemeriksaan atau detail...">
                <p class="text-xs text-gray-500 mt-1">Minimal 3 karakter untuk pencarian otomatis</p>
            </div>
            
            <!-- Tombol Filter -->
            <div class="flex justify-end space-x-2">
                @if(request()->anyFilled(['status_nyeri', 'suhu', 'keyword', 'tanggal']))
                    <a href="{{ route($currentRoutes['index']) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                @endif
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="fas fa-filter mr-1"></i>Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle reset parameter
    const shouldReset = {{ $shouldReset ? 'true' : 'false' }};
    if (shouldReset && window.history.pushState) {
        // Remove 'reset' parameter from URL without reloading
        const url = new URL(window.location.href);
        url.searchParams.delete('reset');
        window.history.pushState({}, '', url);
    }
    
    // Fungsi untuk pencarian
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    
    const userLevel = '{{ $userLevel }}';
    if (userLevel !== 'orang_tua' && searchInput && searchForm && clearSearchBtn) {
        // Tampilkan tombol clear jika ada nilai pencarian
        if (searchInput.value.trim() !== '') {
            clearSearchBtn.style.display = 'block';
        }
        
        // Event untuk input pencarian
        searchInput.addEventListener('input', function() {
            const value = this.value.trim();
            clearSearchBtn.style.display = value ? 'block' : 'none';
            
            // Debounce search
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                if (value.length >= 3 || value === '') {
                    searchForm.submit();
                }
            }, 500);
        });
        
        // Event untuk tombol clear
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            searchForm.submit();
        });
    }
    
    // Toggle filter modal
    const filterBtn = document.getElementById('showFilterModal');
    const filterModal = document.getElementById('filterModal');
    const closeFilterModal = document.getElementById('closeFilterModal');
    
    if (filterBtn && filterModal && closeFilterModal) {
        filterBtn.addEventListener('click', function(e) {
            e.preventDefault();
            filterModal.classList.remove('hidden');
        });
        
        closeFilterModal.addEventListener('click', function() {
            filterModal.classList.add('hidden');
        });
        
        window.addEventListener('click', function(e) {
            if (e.target === filterModal) {
                filterModal.classList.add('hidden');
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !filterModal.classList.contains('hidden')) {
                filterModal.classList.add('hidden');
            }
        });
    }
    
    // Auto-close alerts setelah 5 detik
    const alerts = document.querySelectorAll('.close-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });
    
    // Tooltip untuk tombol aksi berdasarkan role
    if (userLevel === 'petugas') {
        // Add tooltip for petugas edit button
        const editButtons = document.querySelectorAll('a[title*="(Petugas)"]');
        editButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                button.title = 'Edit pemeriksaan awal (akses petugas)';
            });
        });
    } else if (userLevel === 'dokter') {
        // Add tooltip for dokter - no edit access
        const detailButtons = document.querySelectorAll('a[title="Detail"]');
        detailButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                button.title = 'Lihat detail pemeriksaan awal (akses dokter - hanya baca)';
            });
        });
    }
    
    // Log user level untuk debugging
    console.log('User Level:', userLevel);
    console.log('Access Level:', '{{ $isAdmin ? "Admin (Full CRUD)" : ($isPetugas ? "Petugas (CRU)" : ($isDokter ? "Dokter (Read Only)" : "Unknown")) }}');
});
</script>
@endpush
@endsection