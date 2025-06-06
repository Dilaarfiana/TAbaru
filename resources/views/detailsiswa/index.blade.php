@extends('layouts.app')

@section('page_title', 'Detail Siswa')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-graduate text-blue-500 mr-2"></i> Detail Data Siswa
        </h5>
        <div class="flex space-x-2">
            <a href="{{ route('alokasi.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-users-cog mr-2"></i> Kelola Alokasi
            </a>
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
        <form action="{{ route('detailsiswa.index') }}" method="GET" id="filter-form">
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
                        placeholder="Cari nama atau ID siswa..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('search') }}"
                    >
                </div>
                
                <!-- Filter Dropdown Section -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <!-- Status Dropdown -->
                    <div class="relative">
                        <select name="alokasi_status" id="alokasi_status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg appearance-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Status Alokasi</option>
                            <option value="allocated" {{ request('alokasi_status') == 'allocated' ? 'selected' : '' }}>Sudah Teralokasi</option>
                            <option value="unallocated" {{ request('alokasi_status') == 'unallocated' ? 'selected' : '' }}>Belum Teralokasi</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <!-- Jurusan Dropdown -->
                    <div class="relative">
                        <select name="jurusan" id="jurusan" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg appearance-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->kode_jurusan }}" {{ request('jurusan') == $jurusan->kode_jurusan ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <!-- Kelas Dropdown -->
                    <div class="relative">
                        <select name="kelas" id="kelas" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg appearance-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach($kelass as $kelas)
                                <option value="{{ $kelas->kode_kelas }}" {{ request('kelas') == $kelas->kode_kelas ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    
                    <!-- Filter Button -->
                    <button type="submit" class="bg-blue-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        <span>Filter</span>
                    </button>
                    
                    <!-- Reset Filter Button -->
                    <a href="{{ route('detailsiswa.index') }}" class="bg-gray-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 inline-flex items-center justify-center">
                        <i class="fas fa-undo mr-2"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Improved Stats Cards -->
    <div class="p-5 grid grid-cols-2 lg:grid-cols-4 gap-4 bg-white border-b">
        <!-- Total Siswa -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                <i class="fas fa-users text-blue-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Siswa</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalSiswa ?? '0' }}</p>
            </div>
        </div>
        
        <!-- Sudah Teralokasi -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                <i class="fas fa-user-check text-green-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Sudah Teralokasi</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalTeralokasi ?? '0' }}</p>
            </div>
        </div>
        
        <!-- Belum Teralokasi -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                <i class="fas fa-user-minus text-yellow-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Belum Teralokasi</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalBelumTeralokasi ?? '0' }}</p>
            </div>
        </div>
        
        <!-- Total Kelas -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                <i class="fas fa-school text-purple-500"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Kelas</p>
                <p class="text-lg font-bold text-gray-800">{{ $totalKelas ?? '0' }}</p>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID Detail
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jenis Kelamin
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jurusan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kelas
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="detailSiswaTableBody">
                @forelse($detailSiswas as $detail)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $detail->id_detsiswa }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $detail->id_siswa }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                            {{ $detail->siswa->nama_siswa ?? 'Angga' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->siswa)
                                @if($detail->siswa->jenis_kelamin == 'L')
                                    <i class="fas fa-male mr-1 text-blue-500"></i> Laki-laki
                                @else
                                    <i class="fas fa-female mr-1 text-pink-500"></i> Perempuan
                                @endif
                            @else
                                <i class="fas fa-male mr-1 text-blue-500"></i> Laki-laki
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->kode_jurusan && $detail->jurusan)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $detail->jurusan->Nama_Jurusan }}
                                </span>
                            @elseif($detail->kode_jurusan)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $detail->kode_jurusan }}
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-600">
                                    Belum Dialokasi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->kode_kelas && $detail->kelas)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                    {{ $detail->kelas->Nama_Kelas }}
                                </span>
                            @elseif($detail->kode_kelas)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                    {{ $detail->kode_kelas }}
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-600">
                                    Belum Dialokasi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->kode_kelas && $detail->kode_jurusan)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Teralokasi
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Belum Teralokasi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center">
                                <a href="{{ route('siswa.show', $detail->id_siswa) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-200" 
                                   title="Lihat Detail Siswa">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-100 rounded-full p-5 mb-4">
                                    <i class="fas fa-user-graduate text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data detail siswa</h3>
                                <p class="text-gray-400 mb-4">Belum ada data detail siswa yang tersedia</p>
                                <a href="{{ route('siswa.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-user-plus mr-2"></i> Tambah Siswa Baru
                                </a>
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
                    Menampilkan <span class="font-medium">{{ $detailSiswas->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $detailSiswas->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $detailSiswas->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $detailSiswas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Loading overlay untuk AJAX actions -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
            <span class="text-gray-700">Memproses...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto submit form on change
        document.querySelectorAll('#filter-form select').forEach(el => {
            el.addEventListener('change', function() {
                showLoading();
                document.getElementById('filter-form').submit();
            });
        });
        
        // Search dengan debouncing
        let searchTimeout;
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                        showLoading();
                        document.getElementById('filter-form').submit();
                    }
                }, 500);
            });
        }
        
        // Notification auto-close
        const notification = document.getElementById('notification');
        const closeNotification = document.getElementById('closeNotification');
        
        if (notification) {
            // Auto close after 5 seconds
            setTimeout(function() {
                fadeOut(notification);
            }, 5000);
            
            // Manual close button
            if (closeNotification) {
                closeNotification.addEventListener('click', function() {
                    fadeOut(notification);
                });
            }
        }
        
        // Error notification auto-close
        const errorNotification = document.getElementById('errorNotification');
        const closeErrorNotification = document.getElementById('closeErrorNotification');
        
        if (errorNotification) {
            // Auto close after 7 seconds (longer for error messages)
            setTimeout(function() {
                fadeOut(errorNotification);
            }, 7000);
            
            // Manual close button
            if (closeErrorNotification) {
                closeErrorNotification.addEventListener('click', function() {
                    fadeOut(errorNotification);
                });
            }
        }
    });
    
    // Helper function untuk fade out
    function fadeOut(element) {
        element.style.transition = 'opacity 0.5s ease-in-out';
        element.style.opacity = '0';
        setTimeout(function() {
            element.style.display = 'none';
        }, 500);
    }
    
    // Helper function untuk show loading
    function showLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('hidden');
        }
    }
    
    // Hide loading on page load
    window.addEventListener('load', function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection