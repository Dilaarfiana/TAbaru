@extends('layouts.app')

@section('page_title', 'Rekam Medis')

@section('content')
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Check if we should reset filters
    $shouldReset = request()->has('reset');
    
    // Define routes and permissions based on user role
    $routes = [
        'admin' => [
            'index' => 'rekam_medis.index',
            'create' => 'rekam_medis.create',
            'show' => 'rekam_medis.show',
            'edit' => 'rekam_medis.edit',
            'destroy' => 'rekam_medis.destroy'
        ],
        'petugas' => [
            'index' => 'petugas.rekam_medis.index',
            'create' => 'petugas.rekam_medis.create',
            'show' => 'petugas.rekam_medis.show',
            'edit' => 'petugas.rekam_medis.edit',
            'destroy' => null // Petugas tidak bisa hapus
        ],
        'dokter' => [
            'index' => 'dokter.rekam_medis.index',
            'create' => null, // Dokter tidak bisa create
            'show' => 'dokter.rekam_medis.show',
            'edit' => null, // Dokter tidak bisa edit
            'destroy' => null // Dokter tidak bisa hapus
        ]
    ];
    
    // Orang tua tidak boleh mengakses rekam medis sama sekali
    if ($isOrangTua) {
        abort(403, 'Akses ditolak. Orang tua tidak memiliki akses ke rekam medis.');
    }
    
    $currentRoutes = $routes[$userLevel];
    
    // Permission checks
    $canCreate = !is_null($currentRoutes['create']) && ($isAdmin || $isPetugas);
    $canEdit = !is_null($currentRoutes['edit']) && ($isAdmin || $isPetugas);
    $canDelete = !is_null($currentRoutes['destroy']) && $isAdmin; // Hanya admin yang bisa hapus
    $canFilter = true; // Semua role yang bisa akses bisa filter
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-notes-medical text-blue-500 mr-2"></i> 
            Daftar Rekam Medis
            
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
                <i class="fas fa-plus-circle mr-2"></i> Tambah Rekam Medis
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
                        <strong>Administrator:</strong> Akses penuh - Dapat menambah, melihat, mengedit, dan menghapus semua rekam medis
                    @elseif($isPetugas)
                        <strong>Petugas UKS:</strong> Dapat menambah, melihat, dan mengedit rekam medis. 
                        <span class="text-red-600 font-semibold">Tidak dapat menghapus data.</span>
                    @elseif($isDokter)
                        <strong>Dokter:</strong> Hanya dapat melihat rekam medis. 
                        <span class="text-yellow-600 font-semibold">Tidak dapat menambah, mengedit, atau menghapus data.</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
    
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3 flex items-center justify-between">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    {{ session('success') }}
                </p>
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
                <p class="text-sm text-red-700">
                    {{ session('error') }}
                </p>
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
                <p class="text-sm text-blue-700">
                    {{ session('info') }}
                </p>
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
                <p class="text-sm text-yellow-700">
                    {!! session('warning') !!}
                </p>
            </div>
        </div>
        <button type="button" class="close-alert text-yellow-500 hover:text-yellow-600" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
    
    <!-- Filter Section -->
    @if($canFilter)
    <div class="bg-gray-50 p-4 border-b">
        <form action="{{ route($currentRoutes['index']) }}" method="GET" id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label for="siswa" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-graduate text-blue-500 mr-1"></i>
                        Siswa
                    </label>
                    <select name="siswa" id="siswa" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Siswa</option>
                        @if(isset($siswas))
                            @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id_siswa }}" {{ request('siswa') == $siswa->id_siswa ? 'selected' : '' }}>
                                    {{ $siswa->Nama_Siswa ?? $siswa->nama_siswa }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div>
                    <label for="dokter" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-md text-green-500 mr-1"></i>
                        Dokter
                    </label>
                    <select name="dokter" id="dokter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Dokter</option>
                        @if(isset($dokters))
                            @foreach($dokters as $dokter)
                                <option value="{{ $dokter->Id_Dokter }}" {{ request('dokter') == $dokter->Id_Dokter ? 'selected' : '' }}>
                                    {{ $dokter->Nama_Dokter }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar text-purple-500 mr-1"></i>
                        Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" value="{{ request('tanggal') }}">
                </div>
                
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-search text-orange-500 mr-1"></i>
                        Pencarian
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        placeholder="Cari nama/keluhan..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('search') }}"
                    >
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition-colors duration-200 w-full h-10 flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>

                <div class="flex items-end">
                    @if(request()->anyFilled(['siswa', 'dokter', 'tanggal', 'search']))
                        <a href="{{ route($currentRoutes['index']) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md border transition-colors duration-200 w-full h-10 flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filters Section -->
    @if(request()->anyFilled(['siswa', 'dokter', 'tanggal', 'search']) && !$shouldReset)
    <div class="flex justify-between bg-blue-50 px-4 py-2 items-center">
        <div class="text-sm text-blue-700">
            <i class="fas fa-filter mr-1"></i> Filter aktif: 
            @if(request('siswa'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Siswa: {{ collect($siswas ?? [])->where('id_siswa', request('siswa'))->first()->Nama_Siswa ?? request('siswa') }}</span>
            @endif
            @if(request('dokter'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Dokter: {{ collect($dokters ?? [])->where('Id_Dokter', request('dokter'))->first()->Nama_Dokter ?? request('dokter') }}</span>
            @endif
            @if(request('tanggal'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Tanggal: {{ request('tanggal') }}</span>
            @endif
            @if(request('search'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Pencarian: {{ request('search') }}</span>
            @endif
        </div>
        <a href="{{ route($currentRoutes['index']) }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-times-circle mr-1"></i> Hapus Semua Filter
        </a>
    </div>
    @endif
    @endif
    
    <!-- Info Cards -->
@if(isset($totalRekamMedis) && isset($totalRekamMedisBulanIni))
<div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="bg-blue-500 p-3 rounded-full mr-4 shadow-md">
                <i class="fas fa-book-medical text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-700">Total Rekam Medis</p>
                <p class="text-2xl font-bold text-blue-900">{{ number_format($totalRekamMedis) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="bg-green-500 p-3 rounded-full mr-4 shadow-md">
                <i class="fas fa-calendar-check text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-green-700">Rekam Medis Bulan Ini</p>
                <p class="text-2xl font-bold text-green-900">{{ number_format($totalRekamMedisBulanIni) }}</p>
            </div>
        </div>
    </div>
</div>
@endif

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No Rekam Medis
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dokter
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal & Waktu
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Keluhan Utama
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="rekamMedisTableBody">
                @forelse($rekamMedis as $rm)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-purple-100 text-purple-800">
                                {{ $rm->No_Rekam_Medis }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div>
                                <div class="font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-user-graduate text-blue-500 mr-2"></i>
                                    {{ $rm->siswa->Nama_Siswa ?? $rm->siswa->nama_siswa ?? 'Data tidak tersedia' }}
                                </div>
                                <div class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-id-card mr-1"></i>
                                    ID: {{ $rm->siswa->id_siswa ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex items-center">
                                <i class="fas fa-user-md text-green-500 mr-2"></i>
                                <span>{{ $rm->dokter->Nama_Dokter ?? 'Data tidak tersedia' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium flex items-center">
                                    <i class="fas fa-calendar text-purple-500 mr-1 text-xs"></i>
                                    {{ \Carbon\Carbon::parse($rm->Tanggal_Jam)->format('d F Y') }}
                                </span>
                                <span class="text-gray-500 text-xs flex items-center">
                                    <i class="fas fa-clock text-blue-500 mr-1"></i>
                                    {{ \Carbon\Carbon::parse($rm->Tanggal_Jam)->format('H:i') }} WIB
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                            <div title="{{ $rm->Keluhan_Utama }}" class="truncate flex items-center">
                                <i class="fas fa-stethoscope text-red-500 mr-2 flex-shrink-0"></i>
                                <span>{{ $rm->Keluhan_Utama }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-1">
                                {{-- Tombol Detail - Semua role bisa lihat --}}
                                <a href="{{ route($currentRoutes['show'], $rm->No_Rekam_Medis) }}" 
                                   class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- Tombol Edit - Hanya Admin dan Petugas --}}
                                @if($canEdit)
                                <a href="{{ route($currentRoutes['edit'], $rm->No_Rekam_Medis) }}" 
                                   class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                
                                {{-- Tombol Hapus - Hanya Admin --}}
                                @if($canDelete)
                                    <form action="{{ route($currentRoutes['destroy'], $rm->No_Rekam_Medis) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" 
                                                title="Hapus" 
                                                onclick="return confirm('PERINGATAN!\n\nApakah Anda yakin ingin menghapus rekam medis ini?\n\nTindakan ini akan menghapus:\n- Data rekam medis lengkap\n- Semua data pemeriksaan terkait\n- Riwayat medis siswa\n\nData yang dihapus tidak dapat dikembalikan!\n\nKlik OK untuk melanjutkan atau Cancel untuk membatalkan.')">
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
                                <div class="bg-gray-100 rounded-full p-5 mb-4">
                                    <i class="fas fa-notes-medical text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">
                                    Tidak ada data rekam medis
                                </h3>
                                <p class="text-gray-400 mb-4">
                                    @if(request()->anyFilled(['siswa', 'dokter', 'tanggal', 'search']))
                                        Tidak ada rekam medis yang sesuai dengan filter yang diterapkan
                                    @else
                                        Belum ada data rekam medis yang tersedia
                                    @endif
                                </p>
                                <div class="flex space-x-3">
                                    @if($canCreate)
                                    <a href="{{ route($currentRoutes['create']) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        <i class="fas fa-plus-circle mr-2"></i> Tambah Rekam Medis
                                    </a>
                                    @endif
                                    @if(request()->anyFilled(['siswa', 'dokter', 'tanggal', 'search']))
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
                @if(isset($rekamMedis) && method_exists($rekamMedis, 'total'))
                <p class="text-sm text-gray-700">
                    Menampilkan <span class="font-medium">{{ $rekamMedis->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $rekamMedis->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $rekamMedis->total() }}</span> data
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
            <div>
                @if(isset($rekamMedis) && method_exists($rekamMedis, 'links'))
                    {{ $rekamMedis->appends(request()->except(['page', 'reset']))->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

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
    
    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.close-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });
    
    // Auto submit form on filter change
    const userLevel = '{{ $userLevel }}';
    if (userLevel !== 'orang_tua') {
        document.querySelectorAll('#filter-form select, #filter-form input[type="date"]').forEach(el => {
            el.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });
        
        // Debounced search
        const searchInput = document.getElementById('search');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 3 || this.value === '') {
                        document.getElementById('filter-form').submit();
                    }
                }, 800);
            });
        }
    }
    
    // Add tooltips based on role permissions
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'dokter') {
        // Dokter hanya bisa lihat
        document.querySelectorAll('a[title="Detail"]').forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                this.title = 'Lihat detail rekam medis (akses read-only)';
            });
        });
    } else if (userLevel === 'petugas') {
        // Petugas bisa CRU tapi tidak bisa delete
        document.querySelectorAll('button[title="Hapus"]').forEach(function(button) {
            if (button) {
                button.style.display = 'none'; // Hide delete button for petugas
            }
        });
    }
    
    // Konfirmasi khusus untuk hapus data
    document.querySelectorAll('form[method="POST"] button[title="Hapus"]').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('PERINGATAN KERAS!\n\nAnda akan menghapus data rekam medis secara PERMANEN!\n\nData yang akan terhapus:\n✗ Rekam medis lengkap\n✗ Riwayat pemeriksaan\n✗ Data medis terkait\n\nTindakan ini TIDAK DAPAT DIBATALKAN!\n\nApakah Anda BENAR-BENAR yakin ingin melanjutkan?\n\nKlik OK hanya jika Anda yakin 100%')) {
                this.closest('form').submit();
            }
        });
    });
});
</script>
@endpush
@endsection