@extends('layouts.admin')

@section('title', 'Daftar Dokter')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-md text-blue-500 mr-2"></i> Daftar Dokter
        </h5>
        <a href="{{ route('dokter.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Dokter
        </a>
    </div>
    
    <!-- Filter & Search -->
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex flex-col md:flex-row gap-3 justify-between">
            
            <!-- Input Pencarian -->
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <form action="{{ route('dokter.index') }}" method="GET">
                    <input 
                        id="searchInput" 
                        name="search"
                        type="text" 
                        placeholder="Cari nama atau spesialisasi..." 
                        value="{{ request('search') }}"
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
                    >
                </form>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-wrap gap-2">
                
                <!-- Filter -->
                <a 
                    href="#" 
                    id="showFilterModal" 
                    class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center"
                >
                    <i class="fas fa-filter mr-2 text-blue-500"></i> Filter
                </a>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Dokter
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Spesialisasi
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No. Telepon
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="dokterTableBody">
                @forelse ($dokters as $dokter)
                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $dokter->Id_Dokter }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $dokter->Nama_Dokter }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $dokter->Spesialisasi ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $dokter->No_Telp ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-1">
                            <a href="{{ route('dokter.show', $dokter->Id_Dokter) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('dokter.edit', $dokter->Id_Dokter) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('dokter.destroy', $dokter->Id_Dokter) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data dokter ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 rounded-full p-5 mb-4">
                                <i class="fas fa-database text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data dokter</h3>
                            <p class="text-gray-400 mb-4">Belum ada data dokter yang tersedia</p>
                            <a href="{{ route('dokter.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-plus-circle mr-2"></i> Tambah Dokter Sekarang
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
                    Menampilkan <span class="font-medium">{{ $dokters->count() }}</span> dari <span class="font-medium">{{ $dokters->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $dokters->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Filter Data Dokter</h3>
            <button id="closeFilterModal" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('dokter.index') }}" method="GET">
            <!-- Filter Spesialisasi -->
            <div class="mb-4">
                <label for="spesialis" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
                <select id="spesialis" name="spesialis" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Spesialisasi</option>
                    @foreach($spesialisasi as $s)
                        <option value="{{ $s }}" {{ request('spesialis') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Kata Kunci Pencarian -->
            <div class="mb-4">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari nama atau spesialisasi...">
            </div>
            
            <!-- Tombol Filter -->
            <div class="flex justify-end space-x-2">
                @if(request()->anyFilled(['spesialis', 'search']))
                    <a href="{{ route('dokter.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md">
                        Reset
                    </a>
                @endif
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pencarian dokter
        const searchInput = document.getElementById('searchInput');
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    this.closest('form').submit();
                }
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
        }
    });
</script>
@endpush
@endsection