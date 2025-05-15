@extends('layouts.admin')

@section('page_title', 'Rekam Medis')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-notes-medical text-blue-500 mr-2"></i> Daftar Rekam Medis
        </h5>
        <a href="{{ route('rekam_medis.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Rekam Medis
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
    
    <!-- Filter Section -->
    <div class="bg-gray-50 p-4 border-b">
        <form action="{{ route('rekam_medis.index') }}" method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="siswa" class="block text-sm font-medium text-gray-700 mb-1">Siswa</label>
                <select name="siswa" id="siswa" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Siswa</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id_siswa }}" {{ request('siswa') == $siswa->id_siswa ? 'selected' : '' }}>
                            {{ $siswa->Nama_Siswa }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="dokter" class="block text-sm font-medium text-gray-700 mb-1">Dokter</label>
                <select name="dokter" id="dokter" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Dokter</option>
                    @foreach($dokters as $dokter)
                        <option value="{{ $dokter->Id_Dokter }}" {{ request('dokter') == $dokter->Id_Dokter ? 'selected' : '' }}>
                            {{ $dokter->Nama_Dokter }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500" value="{{ request('tanggal') }}">
            </div>
            
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        placeholder="Cari nama/keluhan..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('search') }}"
                    >
                </div>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center h-10 w-full justify-center">
                    <i class="fas fa-filter mr-2"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Info Cards -->
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100 flex items-center">
            <div class="bg-blue-500 p-3 rounded-full mr-4 flex-shrink-0">
                <i class="fas fa-book-medical text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Rekam Medis</p>
                <p class="text-xl font-semibold text-gray-800">{{ $totalRekamMedis }}</p>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-lg p-4 border border-green-100 flex items-center">
            <div class="bg-green-500 p-3 rounded-full mr-4 flex-shrink-0">
                <i class="fas fa-calendar-check text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Rekam Medis Bulan Ini</p>
                <p class="text-xl font-semibold text-gray-800">{{ $totalRekamMedisBulanIni }}</p>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                            No Rekam Medis
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-user-graduate text-indigo-500 mr-2"></i>
                            Siswa
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-user-md text-purple-500 mr-2"></i>
                            Dokter
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-teal-500 mr-2"></i>
                            Tanggal & Waktu
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-stethoscope text-red-500 mr-2"></i>
                            Keluhan Utama
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
            <tbody class="bg-white divide-y divide-gray-200" id="rekamMedisTableBody">
                @forelse($rekamMedis as $rm)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $rm->No_Rekam_Medis }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                {{ $rm->siswa->Nama_Siswa ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                {{ $rm->dokter->Nama_Dokter ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800">
                                <i class="fas fa-calendar-day mr-1"></i> {{ \Carbon\Carbon::parse($rm->Tanggal_Jam)->format('d M Y') }}
                            </span>
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 ml-1">
                                <i class="fas fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($rm->Tanggal_Jam)->format('H:i') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                            {{ $rm->Keluhan_Utama }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-1">
                                <a href="{{ route('rekam_medis.show', $rm->No_Rekam_Medis) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('rekam_medis.edit', $rm->No_Rekam_Medis) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('rekam_medis.cetak', $rm->No_Rekam_Medis) }}" target="_blank" class="text-white bg-green-500 hover:bg-green-600 rounded-md p-2 transition-colors duration-200" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                                <form action="{{ route('rekam_medis.destroy', $rm->No_Rekam_Medis) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data rekam medis</h3>
                                <p class="text-gray-400 mb-4">Belum ada data rekam medis yang tersedia</p>
                                <a href="{{ route('rekam_medis.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Rekam Medis Sekarang
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
                    Menampilkan <span class="font-medium">{{ $rekamMedis->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $rekamMedis->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $rekamMedis->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $rekamMedis->appends(request()->query())->links() }}
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