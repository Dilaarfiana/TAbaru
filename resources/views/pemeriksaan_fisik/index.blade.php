{{-- File: resources/views/pemeriksaan_fisik/index.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('page_title', 'Pemeriksaan Fisik')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Blokir akses orang tua sepenuhnya
    if ($isOrangTua) {
        abort(403, 'Akses ditolak. Orang tua tidak memiliki izin untuk mengakses halaman pemeriksaan fisik.');
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'pemeriksaan_fisik' : ($isPetugas ? 'petugas.pemeriksaan_fisik' : 'dokter.pemeriksaan_fisik');
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';
    
    // Routes yang hanya untuk admin dan petugas
    if ($isAdmin) {
        $createRoute = 'pemeriksaan_fisik.create';
        $editRoute = 'pemeriksaan_fisik.edit';
    } elseif ($isPetugas) {
        $createRoute = 'petugas.pemeriksaan_fisik.create';
        $editRoute = 'petugas.pemeriksaan_fisik.edit';
    }
    
    // Check if we should reset filters
    $shouldReset = request()->has('reset');
@endphp

<!-- Error Modal untuk Unauthorized Access (Backup jika PHP check tidak jalan) -->
@if($isOrangTua)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="access-denied-modal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-ban text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Akses Ditolak</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman pemeriksaan fisik ini.
                </p>
                <p class="text-xs text-red-600 font-medium mb-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Level akses: Orang Tua (NO ACCESS)
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="goBack()" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function goBack() {
    window.location.href = "{{ route('orangtua.dashboard') ?? route('dashboard') }}";
}
setTimeout(function() { goBack(); }, 5000);
</script>

@stop
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-stethoscope text-blue-500 mr-2"></i> Daftar Pemeriksaan Fisik
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
        <div class="flex items-center space-x-3">
            @if($isAdmin || $isPetugas)
            <a href="{{ route($createRoute) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan
            </a>
            @endif
        </div>
    </div>
    
    <!-- Info Access Level -->
    @if($isDokter)
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    Anda mengakses data pemeriksaan fisik dengan <strong>Akses Dokter</strong>. 
                    Anda dapat melihat semua data pemeriksaan namun tidak dapat mengubah atau menghapus data.
                </p>
            </div>
        </div>
    </div>
    @elseif($isPetugas)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Anda mengakses data pemeriksaan fisik dengan <strong>Akses Petugas</strong>. 
                    Anda dapat menambah, melihat dan mengedit data pemeriksaan, namun tidak dapat menghapus data.
                </p>
            </div>
        </div>
    </div>
    @elseif($isAdmin)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Anda mengakses data pemeriksaan fisik dengan <strong>Akses Administrator</strong>. 
                    Anda memiliki akses penuh untuk melihat, mengedit, dan menghapus data pemeriksaan.
                </p>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Filter & Search -->
    <div class="bg-gray-50 p-4 border-b">
        <form action="{{ route($indexRoute) }}" method="GET">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                <!-- Tanggal Dari -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>Tanggal Dari
                    </label>
                    <input 
                        type="date" 
                        name="date_from" 
                        value="{{ request('date_from') }}"
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
                        name="date_to" 
                        value="{{ request('date_to') }}"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <!-- Status BMI -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-weight text-orange-500 mr-1"></i>Status BMI
                    </label>
                    <select name="bmi_status" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status BMI</option>
                        <option value="kurang" {{ request('bmi_status') == 'kurang' ? 'selected' : '' }}>Berat Badan Kurang</option>
                        <option value="normal" {{ request('bmi_status') == 'normal' ? 'selected' : '' }}>Berat Badan Normal</option>
                        <option value="lebih" {{ request('bmi_status') == 'lebih' ? 'selected' : '' }}>Berat Badan Lebih</option>
                        <option value="obesitas" {{ request('bmi_status') == 'obesitas' ? 'selected' : '' }}>Obesitas</option>
                    </select>
                </div>
                
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
                            name="search" 
                            placeholder="Cari ID pemeriksaan atau nama siswa..." 
                            value="{{ request('search') }}"
                            class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition-colors duration-200 flex items-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    
                    @if(request()->anyFilled(['date_from', 'date_to', 'search', 'bmi_status']))
                        <a href="{{ route($indexRoute) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md border transition-colors duration-200 flex items-center">
                            <i class="fas fa-times-circle mr-2"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filters Section -->
    @if(request()->anyFilled(['date_from', 'date_to', 'search', 'bmi_status']) && !$shouldReset)
    <div class="flex justify-between bg-blue-50 px-4 py-2 items-center">
        <div class="text-sm text-blue-700">
            <i class="fas fa-filter mr-1"></i> Filter aktif: 
            @if(request('date_from'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Dari: {{ request('date_from') }}</span>
            @endif
            @if(request('date_to'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Sampai: {{ request('date_to') }}</span>
            @endif
            @if(request('bmi_status'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">BMI: {{ ucfirst(request('bmi_status')) }}</span>
            @endif
            @if(request('search'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Pencarian: {{ request('search') }}</span>
            @endif
        </div>
        <a href="{{ route($indexRoute) }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
            <i class="fas fa-times-circle mr-1"></i> Hapus Semua Filter
        </a>
    </div>
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

    <!-- Informasi Format Pemeriksaan Fisik -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Informasi Pemeriksaan Fisik</h4>
                <p class="text-xs text-blue-600 mt-1">
                    <strong>Pemeriksaan fisik</strong> meliputi pengukuran antropometri (tinggi badan, berat badan, lingkar kepala), 
                    pemeriksaan organ, dan penentuan status gizi berdasarkan BMI.<br>
                    Data ini digunakan untuk evaluasi pertumbuhan dan status kesehatan fisik siswa secara komprehensif.
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
                        Nama Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tinggi/Berat
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        BMI
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="pemeriksaanTableBody">
                @forelse($pemeriksaanFisiks as $pemeriksaan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-purple-100 text-purple-800">
                                {{ $pemeriksaan->id_prefisik }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                {{ isset($pemeriksaan->created_at) ? \Carbon\Carbon::parse($pemeriksaan->created_at)->format('d/m/Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div>
                                <div class="font-medium text-gray-900">{{ $pemeriksaan->detailPemeriksaan->siswa->nama_siswa ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-user mr-1"></i>ID: {{ $pemeriksaan->detailPemeriksaan->siswa->id_siswa ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ $pemeriksaan->created_at ? $pemeriksaan->created_at->format('d F Y') : 'N/A' }}
                                </span>
                                @if($pemeriksaan->created_at)
                                    <span class="text-gray-500 text-xs">
                                        <i class="fas fa-clock mr-1"></i>{{ $pemeriksaan->created_at->format('H:i') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div>
                                @if($pemeriksaan->tinggi_badan)
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-arrows-alt-v text-blue-500 mr-1 text-xs"></i>
                                        <span>{{ $pemeriksaan->tinggi_badan }} cm</span>
                                    </div>
                                @endif
                                @if($pemeriksaan->berat_badan)
                                    <div class="flex items-center">
                                        <i class="fas fa-weight text-green-500 mr-1 text-xs"></i>
                                        <span>{{ $pemeriksaan->berat_badan }} kg</span>
                                    </div>
                                @endif
                                @if(!$pemeriksaan->tinggi_badan && !$pemeriksaan->berat_badan)
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($pemeriksaan->bmi)
                                <div>
                                    <span class="font-medium text-lg">{{ number_format($pemeriksaan->bmi, 1) }}</span>
                                    @if($pemeriksaan->bmi_kategori)
                                        <div class="text-xs">
                                            @php
                                                $kategori = strtolower($pemeriksaan->bmi_kategori);
                                                $colorClass = 'text-gray-500';
                                                $iconClass = 'fas fa-circle';
                                                if(strpos($kategori, 'normal') !== false) {
                                                    $colorClass = 'text-green-600';
                                                    $iconClass = 'fas fa-check-circle';
                                                } elseif(strpos($kategori, 'kurang') !== false) {
                                                    $colorClass = 'text-blue-600';
                                                    $iconClass = 'fas fa-arrow-down';
                                                } elseif(strpos($kategori, 'lebih') !== false || strpos($kategori, 'obesitas') !== false) {
                                                    $colorClass = 'text-orange-600';
                                                    $iconClass = 'fas fa-arrow-up';
                                                }
                                            @endphp
                                            <span class="{{ $colorClass }} font-medium">
                                                <i class="{{ $iconClass }} mr-1"></i>{{ $pemeriksaan->bmi_kategori }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($pemeriksaan->detailPemeriksaan)
                                @if($pemeriksaan->detailPemeriksaan->status_pemeriksaan == 'lengkap')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Lengkap
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> {{ ucfirst($pemeriksaan->detailPemeriksaan->status_pemeriksaan) }}
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-1">
                                {{-- Tombol Detail - semua role bisa akses --}}
                                <a href="{{ route($showRoute, $pemeriksaan->id_prefisik) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- Tombol Edit - hanya admin dan petugas --}}
                                @if($isAdmin || $isPetugas)
                                <a href="{{ route($editRoute, $pemeriksaan->id_prefisik) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit{{ $isPetugas ? ' (Petugas)' : '' }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                
                                {{-- Tombol Hapus - hanya admin --}}
                                @if($isAdmin)
                                    <form action="{{ route('pemeriksaan_fisik.destroy', $pemeriksaan->id_prefisik) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data pemeriksaan fisik ini?\n\nID: {{ $pemeriksaan->id_prefisik }}\n\nTindakan ini akan menghapus semua data terkait dan tidak dapat dikembalikan!')">
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
                                    <i class="fas fa-stethoscope text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan fisik</h3>
                                <p class="text-gray-400 mb-4">
                                    @if(request()->anyFilled(['date_from', 'date_to', 'search', 'bmi_status']))
                                        Tidak ada pemeriksaan fisik yang sesuai dengan filter yang diterapkan
                                    @else
                                        Belum ada data pemeriksaan fisik yang tersedia
                                    @endif
                                </p>
                                @if($isAdmin || $isPetugas)
                                <div class="flex space-x-3">
                                    <a href="{{ route($createRoute) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan
                                    </a>
                                </div>
                                @elseif(request()->anyFilled(['date_from', 'date_to', 'search', 'bmi_status']))
                                <div>
                                    <a href="{{ route($indexRoute) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Menampilkan <span class="font-medium">{{ $pemeriksaanFisiks->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $pemeriksaanFisiks->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $pemeriksaanFisiks->total() }}</span> data
                    @if($isDokter)
                        <span class="text-green-600">(Akses Dokter)</span>
                    @elseif($isPetugas)
                        <span class="text-yellow-600">(Akses Petugas)</span>
                    @endif
                </p>
            </div>
            @if(isset($pemeriksaanFisiks) && $pemeriksaanFisiks instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>
                {{ $pemeriksaanFisiks->appends(request()->except(['page', 'reset']))->links() }}
            </div>
            @endif
        </div>
    </div>
    

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced security check
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'orang_tua') {
        alert('Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.');
        window.location.href = '{{ route("dashboard") }}';
        return;
    }
    
    // Handle reset parameter
    const shouldReset = {{ $shouldReset ? 'true' : 'false' }};
    if (shouldReset && window.history.pushState) {
        const url = new URL(window.location.href);
        url.searchParams.delete('reset');
        window.history.pushState({}, '', url);
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
        const editButtons = document.querySelectorAll('a[title*="(Petugas)"]');
        editButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                button.title = 'Edit pemeriksaan fisik (akses petugas)';
            });
        });
    } else if (userLevel === 'dokter') {
        const detailButtons = document.querySelectorAll('a[title="Detail"]');
        detailButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                button.title = 'Lihat detail pemeriksaan fisik (akses dokter - hanya baca)';
            });
        });
    }
    
    // Enhanced visual feedback berdasarkan role
    if (userLevel === 'dokter') {
        document.querySelector('.bg-white.rounded-lg.shadow-md').classList.add('border-t-4', 'border-green-500');
    } else if (userLevel === 'petugas') {
        document.querySelector('.bg-white.rounded-lg.shadow-md').classList.add('border-t-4', 'border-yellow-500');
    } else if (userLevel === 'admin') {
        document.querySelector('.bg-white.rounded-lg.shadow-md').classList.add('border-t-4', 'border-blue-500');
    }
    
    // Security logging
    console.log('Akses halaman pemeriksaan fisik oleh: ' + userLevel);
});
</script>
@endpush
@endsection