{{-- File: resources/views/pemeriksaan_harian/index.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: NO ACCESS, ORANG TUA: REDIRECT --}}
@extends('layouts.app')

@section('page_title', 'Pemeriksaan Harian')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Redirect orang tua ke halaman khusus mereka
    if ($isOrangTua) {
        header('Location: ' . route('orangtua.riwayat.pemeriksaan_harian'));
        exit;
    }
    
    // Redirect dokter ke dashboard karena tidak ada akses
    if ($isDokter) {
        header('Location: ' . route('dashboard.dokter'));
        exit;
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'pemeriksaan_harian' : 'petugas.pemeriksaan_harian';
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';
    
    // Routes yang hanya untuk admin dan petugas
    if ($isAdmin) {
        $createRoute = 'pemeriksaan_harian.create';
        $editRoute = 'pemeriksaan_harian.edit';
    } elseif ($isPetugas) {
        $createRoute = 'petugas.pemeriksaan_harian.create';
        $editRoute = 'petugas.pemeriksaan_harian.edit';
    }
    
    // Check if we should reset filters
    $shouldReset = request()->has('reset');
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-calendar-check text-blue-500 mr-2"></i> Daftar Pemeriksaan Harian
            @if($isPetugas)
                <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                    <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                </span>
            @elseif($isAdmin)
                <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                    <i class="fas fa-user-shield mr-1"></i>Akses Admin
                </span>
            @endif
        </h5>
        <div class="flex space-x-2">
            @if($isAdmin || $isPetugas)
            <a href="{{ route($createRoute) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan
            </a>
            @endif
        </div>
    </div>
    
    <!-- Info Access Level -->
    @if($isPetugas)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Anda mengakses data pemeriksaan harian dengan <strong>Akses Petugas</strong>. 
                    Anda dapat menambah, melihat dan mengedit data pemeriksaan, namun tidak dapat menghapus data.
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

                <!-- Filter Petugas -->
                @if(isset($petugasList) && $petugasList->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-user-nurse text-green-500 mr-1"></i>Petugas UKS
                    </label>
                    <select name="petugas_filter" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Petugas</option>
                        @foreach($petugasList as $petugas)
                            <option value="{{ $petugas->NIP }}" {{ request('petugas_filter') == $petugas->NIP ? 'selected' : '' }}>
                                {{ $petugas->nama_petugas_uks }}
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
                            placeholder="Cari ID pemeriksaan, nama siswa, atau hasil pemeriksaan..." 
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
                    
                    @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'petugas_filter']))
                        <a href="{{ route($indexRoute) }}" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md border transition-colors duration-200 flex items-center">
                            <i class="fas fa-times-circle mr-2"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filters Section -->
    @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'petugas_filter']) && !$shouldReset)
    <div class="flex justify-between bg-blue-50 px-4 py-2 items-center">
        <div class="text-sm text-blue-700">
            <i class="fas fa-filter mr-1"></i> Filter aktif: 
            @if(request('tanggal_dari'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Dari: {{ request('tanggal_dari') }}</span>
            @endif
            @if(request('tanggal_sampai'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Sampai: {{ request('tanggal_sampai') }}</span>
            @endif
            @if(request('petugas_filter'))
                @php
                    $selectedPetugas = $petugasList->firstWhere('NIP', request('petugas_filter'));
                @endphp
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                    Petugas: {{ $selectedPetugas->nama_petugas_uks ?? request('petugas_filter') }}
                </span>
            @endif
            @if(request('keyword'))
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">Pencarian: {{ request('keyword') }}</span>
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

    <!-- Informasi Format Pemeriksaan Harian -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Informasi Pemeriksaan Harian</h4>
                <p class="text-xs text-blue-600 mt-1">
                    <strong>Pemeriksaan harian</strong> adalah pemeriksaan rutin yang dilakukan oleh petugas UKS untuk siswa yang datang dengan keluhan kesehatan ringan atau memerlukan perawatan lanjutan.<br>
                    Data ini mencatat hasil pemeriksaan, tindakan yang diberikan, dan kondisi siswa setelah pemeriksaan.
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
                        Petugas UKS
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal & Waktu
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Hasil Pemeriksaan
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="pemeriksaanTableBody">
                @forelse($pemeriksaanHarian as $pemeriksaan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $pemeriksaan->Id_Harian }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                {{ isset($pemeriksaan->Tanggal_Jam) ? \Carbon\Carbon::parse($pemeriksaan->Tanggal_Jam)->format('d/m/Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div>
                                <div class="font-medium text-gray-900">{{ $pemeriksaan->siswa->nama_siswa ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-user mr-1"></i>ID: {{ $pemeriksaan->Id_Siswa ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div>
                                <div class="text-gray-900 flex items-center">
                                    <i class="fas fa-user-nurse text-blue-500 mr-1 text-xs"></i>
                                    {{ $pemeriksaan->petugasUKS->nama_petugas_uks ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-id-badge mr-1"></i>
                                    NIP: {{ $pemeriksaan->NIP }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex flex-col">
                                @if($pemeriksaan->Tanggal_Jam)
                                    <span class="font-medium flex items-center">
                                        <i class="fas fa-calendar text-green-500 mr-1 text-xs"></i>
                                        {{ \Carbon\Carbon::parse($pemeriksaan->Tanggal_Jam)->format('d F Y') }}
                                    </span>
                                    <span class="text-gray-500 text-xs flex items-center">
                                        <i class="fas fa-clock text-blue-500 mr-1"></i>
                                        {{ \Carbon\Carbon::parse($pemeriksaan->Tanggal_Jam)->format('H:i') }} WIB
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                            <div title="{{ $pemeriksaan->Hasil_Pemeriksaan }}" class="truncate">
                                @if($pemeriksaan->Hasil_Pemeriksaan)
                                    <i class="fas fa-notes-medical text-purple-500 mr-1"></i>
                                    {{ \Illuminate\Support\Str::limit($pemeriksaan->Hasil_Pemeriksaan, 50) }}
                                @else
                                    <span class="text-gray-400 italic">Belum ada hasil</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-1">
                                {{-- Tombol Detail - semua role bisa akses (admin dan petugas) --}}
                                <a href="{{ route($showRoute, $pemeriksaan->Id_Harian) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- Tombol Edit - hanya admin dan petugas --}}
                                @if($isAdmin || $isPetugas)
                                <a href="{{ route($editRoute, $pemeriksaan->Id_Harian) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit{{ $isPetugas ? ' (Petugas)' : '' }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                
                                {{-- Tombol Hapus - hanya admin --}}
                                @if($isAdmin)
                                    <form action="{{ route('pemeriksaan_harian.destroy', $pemeriksaan->Id_Harian) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data pemeriksaan harian ini?\n\nID: {{ $pemeriksaan->Id_Harian }}\nSiswa: {{ $pemeriksaan->siswa->nama_siswa ?? 'N/A' }}\n\nTindakan ini akan menghapus semua data terkait dan tidak dapat dikembalikan!')">
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
                                    <i class="fas fa-calendar-check text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan harian</h3>
                                <p class="text-gray-400 mb-4">
                                    @if(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'petugas_filter']))
                                        Tidak ada pemeriksaan harian yang sesuai dengan filter yang diterapkan
                                    @else
                                        Belum ada data pemeriksaan harian yang tersedia
                                    @endif
                                </p>
                                @if($isAdmin || $isPetugas)
                                <div class="flex space-x-3">
                                    <a href="{{ route($createRoute) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan
                                    </a>
                                </div>
                                @elseif(request()->anyFilled(['tanggal_dari', 'tanggal_sampai', 'keyword', 'petugas_filter']))
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
                    Menampilkan <span class="font-medium">{{ $pemeriksaanHarian->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $pemeriksaanHarian->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $pemeriksaanHarian->total() }}</span> data
                    @if($isPetugas)
                        <span class="text-yellow-600">(Akses Petugas)</span>
                    @endif
                </p>
            </div>
            @if(isset($pemeriksaanHarian) && $pemeriksaanHarian instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>
                {{ $pemeriksaanHarian->appends(request()->except(['page', 'reset']))->links() }}
            </div>
            @endif
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
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'petugas') {
        // Add tooltip for petugas edit button
        const editButtons = document.querySelectorAll('a[title*="(Petugas)"]');
        editButtons.forEach(function(button) {
            button.addEventListener('mouseenter', function() {
                button.title = 'Edit pemeriksaan harian (akses petugas)';
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
                const tbody = document.getElementById('pemeriksaanTableBody');
                const rows = tbody.getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].getElementsByTagName('td').length > 1) {
                        const idCell = rows[i].getElementsByTagName('td')[0];
                        const siswaCell = rows[i].getElementsByTagName('td')[1];
                        const petugasCell = rows[i].getElementsByTagName('td')[2];
                        const hasilCell = rows[i].getElementsByTagName('td')[4];
                        
                        if (idCell && siswaCell && petugasCell && hasilCell) {
                            const idValue = idCell.textContent || idCell.innerText;
                            const siswaValue = siswaCell.textContent || siswaCell.innerText;
                            const petugasValue = petugasCell.textContent || petugasCell.innerText;
                            const hasilValue = hasilCell.textContent || hasilCell.innerText;
                            
                            if (idValue.toUpperCase().indexOf(filter) > -1 || 
                                siswaValue.toUpperCase().indexOf(filter) > -1 ||
                                petugasValue.toUpperCase().indexOf(filter) > -1 ||
                                hasilValue.toUpperCase().indexOf(filter) > -1) {
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