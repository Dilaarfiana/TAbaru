@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-users text-blue-500 mr-2"></i> Daftar Orang Tua
        </h5>
        <a href="{{ route('orangtua.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Orang Tua
        </a>
    </div>
    
    <!-- Tampilkan pesan sukses -->
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
    
    <!-- Tampilkan pesan error -->
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
    
    <!-- Tampilkan pesan info -->
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
    
    <!-- Filter & Search -->
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex flex-col md:flex-row gap-3 justify-between">
            
            <!-- Input Pencarian -->
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    id="searchInput" 
                    type="text" 
                    placeholder="Cari nama siswa atau orang tua..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
                    value="{{ request('keyword') }}"
                >
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
                        Nama Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Ayah
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Ibu
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No. Telepon
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="orangTuaTableBody">
                @forelse ($orangTuas as $orangTua)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $orangTua->id_orang_tua }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user-graduate text-gray-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    {{ $orangTua->siswa->nama_siswa ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-male mr-1"></i> {{ $orangTua->nama_ayah ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                <i class="fas fa-female mr-1"></i> {{ $orangTua->nama_ibu ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                <i class="fas fa-phone mr-1"></i> {{ $orangTua->no_telp ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-1">
                                <a href="{{ route('orangtua.show', $orangTua->id_orang_tua) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orangtua.edit', $orangTua->id_orang_tua) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('orangtua.destroy', $orangTua->id_orang_tua) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data orang tua ini?')">
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
                                    <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data orang tua</h3>
                                <p class="text-gray-400 mb-4">Belum ada data orang tua yang tersedia</p>
                                <a href="{{ route('orangtua.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Orang Tua Sekarang
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
                    Menampilkan <span class="font-medium">{{ count($orangTuas) > 0 ? 1 : 0 }}</span> 
                    sampai <span class="font-medium">{{ count($orangTuas) }}</span> 
                    dari <span class="font-medium">{{ isset($orangTuas) && method_exists($orangTuas, 'total') ? $orangTuas->total() : count($orangTuas) }}</span> data
                </p>
            </div>
            <div>
                @if(isset($orangTuas) && method_exists($orangTuas, 'links'))
                    {{ $orangTuas->appends(request()->query())->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Filter Data Orang Tua</h3>
            <button id="closeFilterModal" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('orangtua.index') }}" method="GET">
            <!-- Filter Nama Siswa -->
            <div class="mb-4">
                <label for="nama_siswa" class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                <input type="text" id="nama_siswa" name="nama_siswa" value="{{ request('nama_siswa') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama siswa...">
            </div>
            
            <!-- Filter Nama Ayah -->
            <div class="mb-4">
                <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                <input type="text" id="nama_ayah" name="nama_ayah" value="{{ request('nama_ayah') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama ayah...">
            </div>
            
            <!-- Filter Nama Ibu -->
            <div class="mb-4">
                <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                <input type="text" id="nama_ibu" name="nama_ibu" value="{{ request('nama_ibu') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Masukkan nama ibu...">
            </div>
            
            <!-- Kata Kunci Pencarian -->
            <div class="mb-4">
                <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari semua data...">
            </div>
            
            <!-- Tombol Filter -->
            <div class="flex justify-end space-x-2">
                @if(request()->anyFilled(['nama_siswa', 'nama_ayah', 'nama_ibu', 'keyword']))
                    <a href="{{ route('orangtua.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md">
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
        // Pencarian orang tua
        const searchInput = document.getElementById('searchInput');
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toUpperCase();
                const tbody = document.getElementById('orangTuaTableBody');
                const rows = tbody.getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].getElementsByTagName('td').length > 1) {
                        const idCell = rows[i].getElementsByTagName('td')[0];
                        const siswaCell = rows[i].getElementsByTagName('td')[1];
                        const ayahCell = rows[i].getElementsByTagName('td')[2];
                        const ibuCell = rows[i].getElementsByTagName('td')[3];
                        const telpCell = rows[i].getElementsByTagName('td')[4];
                        
                        if (idCell && siswaCell && ayahCell && ibuCell && telpCell) {
                            const idValue = idCell.textContent || idCell.innerText;
                            const siswaValue = siswaCell.textContent || siswaCell.innerText;
                            const ayahValue = ayahCell.textContent || ayahCell.innerText;
                            const ibuValue = ibuCell.textContent || ibuCell.innerText;
                            const telpValue = telpCell.textContent || telpCell.innerText;
                            
                            if (idValue.toUpperCase().indexOf(filter) > -1 || 
                                siswaValue.toUpperCase().indexOf(filter) > -1 ||
                                ayahValue.toUpperCase().indexOf(filter) > -1 ||
                                ibuValue.toUpperCase().indexOf(filter) > -1 ||
                                telpValue.toUpperCase().indexOf(filter) > -1) {
                                rows[i].style.display = '';
                            } else {
                                rows[i].style.display = 'none';
                            }
                        }
                    }
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