@extends('layouts.app')

@section('page_title', 'Detail Pemeriksaan')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-stethoscope text-blue-500 mr-2"></i> Detail Data Pemeriksaan
        </h5>
        <div class="flex space-x-2">
            @if(session('user_level') === 'admin')
                <a href="{{ route('pemeriksaan_fisik.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i> Pemeriksaan Fisik
                </a>
                <a href="{{ route('pemeriksaan_awal.index') }}" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-notes-medical mr-2"></i> Pemeriksaan Awal
                </a>
                <a href="{{ route('rekam_medis.index') }}" class="bg-purple-500 text-white hover:bg-purple-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-file-medical mr-2"></i> Rekam Medis
                </a>
            @elseif(session('user_level') === 'petugas')
                <a href="{{ route('petugas.pemeriksaan_fisik.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i> Pemeriksaan Fisik
                </a>
                <a href="{{ route('petugas.pemeriksaan_awal.index') }}" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-notes-medical mr-2"></i> Pemeriksaan Awal
                </a>
                <a href="{{ route('petugas.rekam_medis.index') }}" class="bg-purple-500 text-white hover:bg-purple-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-file-medical mr-2"></i> Rekam Medis
                </a>
            @elseif(session('user_level') === 'dokter')
                <a href="{{ route('dokter.pemeriksaan_fisik.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i> Pemeriksaan Fisik
                </a>
                <a href="{{ route('dokter.pemeriksaan_awal.index') }}" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-notes-medical mr-2"></i> Pemeriksaan Awal
                </a>
                <a href="{{ route('dokter.rekam_medis.index') }}" class="bg-purple-500 text-white hover:bg-purple-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-file-medical mr-2"></i> Rekam Medis
                </a>
            @endif
        </div>
    </div>
    
    <!-- Tampilkan pesan sukses -->
    @if(session('success'))
    <div id="notification" class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3 flex items-center justify-between">
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
        <button type="button" id="closeNotification" class="text-green-500 hover:text-green-600" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Tampilkan pesan error -->
    @if(session('error'))
    <div id="errorNotification" class="bg-red-50 border-l-4 border-red-500 p-4 mx-4 mt-3 flex items-center justify-between">
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
        <button type="button" id="closeErrorNotification" class="text-red-500 hover:text-red-600" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
    
    <!-- Improved Filter Section -->
    <div class="bg-white p-5 border-b">
        @if(session('user_level') === 'admin')
            <form action="{{ route('detail_pemeriksaan.index') }}" method="GET" id="filter-form">
        @elseif(session('user_level') === 'petugas')
            <form action="{{ route('petugas.detail_pemeriksaan.index') }}" method="GET" id="filter-form">
        @elseif(session('user_level') === 'dokter')
            <form action="{{ route('dokter.detail_pemeriksaan.index') }}" method="GET" id="filter-form">
        @else
            <form action="{{ route('detail_pemeriksaan.index') }}" method="GET" id="filter-form">
        @endif
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search Field -->
                <div class="flex-grow relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        placeholder="Cari siswa atau dokter..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('search') }}"
                    >
                </div>
                
                <!-- Filter Dropdown Section -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Tanggal Dari -->
                    <div class="relative">
                        <input 
                            type="date" 
                            name="date_from" 
                            id="date_from" 
                            class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            value="{{ request('date_from') }}"
                        >
                    </div>
                    
                    <!-- Tanggal Sampai -->
                    <div class="relative">
                        <input 
                            type="date" 
                            name="date_to" 
                            id="date_to" 
                            class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            value="{{ request('date_to') }}"
                        >
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="relative">
                        <select 
                            name="status" 
                            id="status" 
                            class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Semua Status</option>
                            <option value="lengkap" {{ request('status') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                            <option value="belum lengkap" {{ request('status') == 'belum lengkap' ? 'selected' : '' }}>Belum Lengkap</option>
                        </select>
                    </div>
                    
                    <!-- Filter Button -->
                    <button type="submit" class="bg-blue-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        <span>Filter</span>
                    </button>
                    
                    <!-- Reset Button -->
                    @if(session('user_level') === 'admin')
                        <a href="{{ route('detail_pemeriksaan.index') }}" class="bg-gray-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 inline-flex items-center justify-center">
                            <i class="fas fa-undo mr-2"></i>
                            <span>Reset</span>
                        </a>
                    @elseif(session('user_level') === 'petugas')
                        <a href="{{ route('petugas.detail_pemeriksaan.index') }}" class="bg-gray-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 inline-flex items-center justify-center">
                            <i class="fas fa-undo mr-2"></i>
                            <span>Reset</span>
                        </a>
                    @elseif(session('user_level') === 'dokter')
                        <a href="{{ route('dokter.detail_pemeriksaan.index') }}" class="bg-gray-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 inline-flex items-center justify-center">
                            <i class="fas fa-undo mr-2"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    
    <!-- Improved Stats Cards -->
    <div class="p-5 grid grid-cols-2 lg:grid-cols-4 gap-4 bg-white border-b">
        <!-- Total Pemeriksaan -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                <i class="fas fa-stethoscope text-blue-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Pemeriksaan</p>
                <p class="text-lg font-bold text-gray-800">{{ $detailPemeriksaans->total() ?? '0' }}</p>
            </div>
        </div>
        
        <!-- Pemeriksaan Hari Ini -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                <i class="fas fa-calendar-day text-green-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Pemeriksaan Hari Ini</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalHariIni ?? '0' }}</p>
            </div>
        </div>
        
        <!-- Pemeriksaan Lengkap -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                <i class="fas fa-check-double text-purple-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Pemeriksaan Lengkap</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalLengkap ?? '0' }}</p>
            </div>
        </div>
        
        <!-- Total Siswa Terlayani -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                <i class="fas fa-user-check text-orange-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Siswa Terlayani</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalSiswaTerlayani ?? '0' }}</p>
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
                        Tanggal & Waktu
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dokter
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Petugas UKS
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="detailPemeriksaanTableBody">
                @forelse($detailPemeriksaans as $pemeriksaan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ $pemeriksaan->id_detprx }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($pemeriksaan->tanggal_jam)->format('d/m/Y') }}</span>
                                <span class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($pemeriksaan->tanggal_jam)->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ $pemeriksaan->siswa->nama_siswa ?? 'Angga' }}
                                </span>
                                <span class="text-gray-500 text-xs">
                                    ID: {{ $pemeriksaan->id_siswa }}
                                </span>
                                @if($pemeriksaan->siswa && $pemeriksaan->siswa->detailSiswa && $pemeriksaan->siswa->detailSiswa->kelas)
                                    <span class="text-gray-500 text-xs">
                                        Kelas: {{ $pemeriksaan->siswa->detailSiswa->kelas->Nama_Kelas ?? '1' }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ $pemeriksaan->dokter->Nama_Dokter ?? 'Dr. Ahmad Suryadi, Sp.A' }}
                                </span>
                                <span class="text-gray-500 text-xs">
                                    {{ $pemeriksaan->dokter->Spesialisasi ?? 'Dokter Spesialis Anak' }}
                                </span>
                                @if($pemeriksaan->dokter && $pemeriksaan->dokter->No_Telp)
                                    <span class="text-gray-500 text-xs">
                                        Tel: {{ $pemeriksaan->dokter->No_Telp }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ $pemeriksaan->petugasUks->nama_petugas_uks ?? 'Sari Dewi, S.Kep' }}
                                </span>
                                <span class="text-gray-500 text-xs">
                                    NIP: {{ $pemeriksaan->nip ?? '197803052006042001' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($pemeriksaan->status_pemeriksaan == 'lengkap')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Lengkap
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Belum Lengkap
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center space-x-1">
                                @if(session('user_level') === 'admin')
                                    <!-- Tombol Pemeriksaan Awal - Admin -->
                                    <a href="{{ route('pemeriksaan_awal.index') }}" 
                                       class="inline-flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200" 
                                       title="Pemeriksaan Awal">
                                        <i class="fas fa-notes-medical text-sm"></i>
                                    </a>
                                    
                                    <!-- Tombol Pemeriksaan Fisik - Admin -->
                                    <a href="{{ route('pemeriksaan_fisik.index') }}" 
                                       class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200" 
                                       title="Pemeriksaan Fisik">
                                        <i class="fas fa-clipboard-list text-sm"></i>
                                    </a>
                                    
                                @elseif(session('user_level') === 'petugas')
                                    <!-- Tombol Pemeriksaan Awal - Petugas -->
                                    <a href="{{ route('petugas.pemeriksaan_awal.index') }}" 
                                       class="inline-flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200" 
                                       title="Pemeriksaan Awal">
                                        <i class="fas fa-notes-medical text-sm"></i>
                                    </a>
                                    
                                    <!-- Tombol Pemeriksaan Fisik - Petugas -->
                                    <a href="{{ route('petugas.pemeriksaan_fisik.index') }}" 
                                       class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200" 
                                       title="Pemeriksaan Fisik">
                                        <i class="fas fa-clipboard-list text-sm"></i>
                                    </a>
                                    
                                @elseif(session('user_level') === 'dokter')
                                    <!-- Tombol Pemeriksaan Awal - Dokter (Read Only) -->
                                    <a href="{{ route('dokter.pemeriksaan_awal.index') }}" 
                                       class="inline-flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200" 
                                       title="Lihat Pemeriksaan Awal">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    
                                    <!-- Tombol Pemeriksaan Fisik - Dokter (Read Only) -->
                                    <a href="{{ route('dokter.pemeriksaan_fisik.index') }}" 
                                       class="inline-flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200" 
                                       title="Lihat Pemeriksaan Fisik">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
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
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan</h3>
                                <p class="text-gray-400 mb-4">Belum ada data pemeriksaan yang tersedia</p>
                                <p class="text-sm text-gray-500 mb-4">Data akan terisi otomatis ketika rekam medis dibuat</p>
                                @if(session('user_level') === 'admin')
                                    <a href="{{ route('rekam_medis.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-plus mr-2"></i> Buat Rekam Medis
                                    </a>
                                @elseif(session('user_level') === 'petugas')
                                    <a href="{{ route('petugas.rekam_medis.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-plus mr-2"></i> Buat Rekam Medis
                                    </a>
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
                    Menampilkan <span class="font-medium">{{ $detailPemeriksaans->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $detailPemeriksaans->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $detailPemeriksaans->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $detailPemeriksaans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto submit form on change
        document.querySelectorAll('#filter-form select, #filter-form input[type="date"]').forEach(el => {
            el.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });
        
        // Search input with debounce
        const searchInput = document.getElementById('search');
        let searchTimeout;
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filter-form').submit();
                }, 500);
            });
        }
        
        // Notification auto-close
        const notification = document.getElementById('notification');
        const closeNotification = document.getElementById('closeNotification');
        
        if (notification) {
            // Auto close after 5 seconds
            setTimeout(function() {
                notification.style.opacity = '0';
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 500);
            }, 5000);
            
            // Manual close button
            if (closeNotification) {
                closeNotification.addEventListener('click', function() {
                    notification.style.opacity = '0';
                    setTimeout(function() {
                        notification.style.display = 'none';
                    }, 500);
                });
            }
            
            // Add transition
            notification.style.transition = 'opacity 0.5s ease-in-out';
        }
        
        // Error notification auto-close
        const errorNotification = document.getElementById('errorNotification');
        const closeErrorNotification = document.getElementById('closeErrorNotification');
        
        if (errorNotification) {
            // Auto close after 5 seconds
            setTimeout(function() {
                errorNotification.style.opacity = '0';
                setTimeout(function() {
                    errorNotification.style.display = 'none';
                }, 500);
            }, 5000);
            
            // Manual close button
            if (closeErrorNotification) {
                closeErrorNotification.addEventListener('click', function() {
                    errorNotification.style.opacity = '0';
                    setTimeout(function() {
                        errorNotification.style.display = 'none';
                    }, 500);
                });
            }
            
            // Add transition
            errorNotification.style.transition = 'opacity 0.5s ease-in-out';
        }
    });
</script>
@endpush
@endsection