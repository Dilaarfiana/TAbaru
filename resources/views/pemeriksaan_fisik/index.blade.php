@extends('layouts.admin')

@section('page_title', 'Pemeriksaan Fisik')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-stethoscope text-blue-500 mr-2"></i> Data Pemeriksaan Fisik
        </h5>
        <a href="{{ route('pemeriksaan_fisik.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center shadow-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Pemeriksaan
        </a>
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
    
    <!-- Filter Section dengan Background Abu-abu -->
    <div class="bg-gray-50 p-4 border-b">
        <form action="{{ route('pemeriksaan_fisik.index') }}" method="GET" id="filter-form">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                <!-- Tanggal Input dengan Label -->
                <div class="md:w-48">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pemeriksaan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                        <input 
                            type="date" 
                            name="tanggal" 
                            id="tanggal" 
                            value="{{ request('tanggal') }}"
                            class="pl-10 pr-3 py-2 border border-gray-300 rounded-lg w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white"
                        >
                    </div>
                </div>
                
                <!-- Input Pencarian dengan Label -->
                <div class="flex-grow">
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            id="searchInput" 
                            placeholder="Cari ID pemeriksaan atau pasien..." 
                            value="{{ request('search') }}"
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm bg-white"
                        >
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex gap-2">
                    <!-- Filter -->
                    <button 
                        type="submit" 
                        class="bg-blue-600 text-white hover:bg-blue-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors duration-300 flex items-center shadow-sm h-10"
                    >
                        <i class="fas fa-filter mr-2"></i> Terapkan Filter
                    </button>
                    
                    <!-- Reset Filter -->
                    @if(request()->anyFilled(['tanggal', 'search']))
                        <a 
                            href="{{ route('pemeriksaan_fisik.index') }}" 
                            class="bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium transition-colors duration-300 flex items-center shadow-sm h-10"
                        >
                            <i class="fas fa-undo-alt mr-2"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Active Filters -->
            @if(request()->anyFilled(['tanggal', 'search']))
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs font-medium text-gray-500 py-1 pr-2">Filter Aktif:</span>
                    
                    @if(request('tanggal'))
                        <div class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 shadow-sm">
                            <i class="fas fa-calendar-day mr-1.5"></i>
                            <span>{{ request('tanggal') }}</span>
                            <a href="{{ request()->except('tanggal') }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </div>
                    @endif
                    
                    @if(request('search'))
                        <div class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200 shadow-sm">
                            <i class="fas fa-search mr-1.5"></i>
                            <span>"{{ request('search') }}"</span>
                            <a href="{{ request()->except('search') }}" class="ml-2 text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        </div>
                    @endif
                    
                    <a href="{{ route('pemeriksaan_fisik.index') }}" class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 shadow-sm ml-auto">
                        <i class="fas fa-times mr-1.5"></i>
                        <span>Hapus Semua</span>
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-hashtag text-blue-500 mr-2"></i>
                            ID Pemeriksaan
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-user-injured text-indigo-500 mr-2"></i>
                            ID Pasien
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                            Tanggal
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-ruler-vertical text-purple-500 mr-2"></i>
                            Tinggi
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-weight text-yellow-500 mr-2"></i>
                            Berat
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-heartbeat text-red-500 mr-2"></i>
                            Tekanan Darah
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-cogs text-gray-500 mr-2"></i>
                            Aksi
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pemeriksaanFisiks as $pemeriksaan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                {{ $pemeriksaan->id }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex items-center">
                                <div class="h-7 w-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-2">
                                    <i class="fas fa-user-injured text-xs"></i>
                                </div>
                                <span class="font-medium">{{ $pemeriksaan->id_pasien }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-calendar-day mr-1"></i>
                                {{ \Carbon\Carbon::parse($pemeriksaan->tanggal_pemeriksaan)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($pemeriksaan->tinggi_badan)
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-purple-100 text-purple-800">
                                    {{ $pemeriksaan->tinggi_badan }} cm
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($pemeriksaan->berat_badan)
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $pemeriksaan->berat_badan }} kg
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($pemeriksaan->tekanan_darah)
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-heartbeat mr-1"></i>
                                    {{ $pemeriksaan->tekanan_darah }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-1">
                                <a href="{{ route('pemeriksaan_fisik.show', $pemeriksaan->id) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200 shadow-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pemeriksaan_fisik.edit', $pemeriksaan->id) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200 shadow-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pemeriksaan_fisik.destroy', $pemeriksaan->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200 shadow-sm" title="Hapus" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
                                <p class="text-gray-400 mb-4">Belum ada data pemeriksaan fisik yang tersedia</p>
                                <a href="{{ route('pemeriksaan_fisik.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i> Tambah Pemeriksaan Baru
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
                    Menampilkan <span class="font-medium">{{ $pemeriksaanFisiks->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $pemeriksaanFisiks->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $pemeriksaanFisiks->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $pemeriksaanFisiks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endpush
@endsection