@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section - Putih dengan ikon dan tombol biru -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg> 
            Daftar Orang Tua
        </h5>
        <div class="flex space-x-2">
            <a href="{{ route('orangtua.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Orang Tua
            </a>
        </div>
    </div>
    
    <!-- Filter & Search -->
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex flex-col md:flex-row gap-3 justify-between">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <form id="searchForm" action="{{ route('orangtua.index') }}" method="GET" class="inline">
                    <input id="searchInput" name="keyword" type="text" placeholder="Cari nama siswa atau orang tua..." class="pl-10 pr-10 py-2 border border-gray-300 rounded-md w-full md:w-64 focus:ring-blue-500 focus:border-blue-500" value="{{ request('keyword') }}">
                    <button type="button" id="clearSearchBtn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" style="display: {{ request('keyword') ? 'block' : 'none' }};">
                        &times;
                    </button>
                </form>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('orangtua.import') }}" class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    Import
                </a>
                <a href="{{ route('orangtua.export') }}" class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </a>
            </div>
        </div>
    </div>
    
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p class="font-medium">{{ session('error') }}</p>
    </div>
    @endif
    
    @if(session('info'))
    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
        <p class="font-medium">{{ session('info') }}</p>
    </div>
    @endif
    
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
                @forelse($orangTuas as $orangTua)
                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $orangTua->id_orang_tua }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $orangTua->siswa->nama_siswa ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $orangTua->nama_ayah ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $orangTua->nama_ibu ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $orangTua->no_telp ?? '-' }}
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
                                <i class="fas fa-database text-4xl text-gray-400"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data orang tua</h3>
                        <p class="text-gray-400 mb-4">Belum ada data orang tua yang tersedia</p>
                        <a href="{{ route('orangtua.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Orang Tua Sekarang
                        </a>
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
                    Menampilkan <span class="font-medium">{{ count($orangTuas) }}</span> dari <span class="font-medium">{{ $orangTuas->total() }}</span> data
                </p>
            </div>
            @if(isset($orangTuas) && $orangTuas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>
                {{ $orangTuas->appends(request()->except('page'))->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="modalOverlay"></div>
        
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg max-w-md w-full mx-auto shadow-xl z-20">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-lg">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Import Data Orang Tua
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Silakan unggah file Excel (.xlsx, .xls, .csv) yang berisi data orang tua untuk diimport.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('orangtua.import') }}" method="POST" enctype="multipart/form-data" class="mt-5">
                    @csrf
                    <div class="mb-3">
                        <label for="importFile" class="block text-sm font-medium text-gray-700 mb-1">File Excel</label>
                        <input type="file" name="file" id="importFile" required 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md
                               file:border-0 file:text-sm file:font-medium
                               file:bg-blue-50 file:text-blue-700
                               hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">Format file: .xlsx, .xls, atau .csv (maks. 5MB)</p>
                    </div>
                    
                    <div class="border rounded-md p-3 mt-3 bg-yellow-50">
                        <h4 class="text-sm font-medium text-yellow-800">Format Template</h4>
                        <p class="text-xs text-yellow-600 mt-1">Pastikan file memiliki kolom berikut: ID Siswa, Nama Ayah, Nama Ibu, No. Telepon, dan Alamat</p>
                        <a href="{{ route('orangtua.template') }}" class="inline-flex items-center mt-2 text-xs text-blue-600 hover:text-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Template
                        </a>
                    </div>
                    
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Import Data
                        </button>
                        <button type="button" id="cancelImport" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    if (searchInput && searchForm && clearSearchBtn) {
        // Show clear button if search has value
        if (searchInput.value.trim() !== '') {
            clearSearchBtn.style.display = 'block';
        }

        // Real-time search (with debounce)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            const searchValue = this.value.trim();
            
            // Show/hide clear button
            clearSearchBtn.style.display = searchValue ? 'block' : 'none';
            
            // Debounce search submit
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (searchValue.length > 2 || searchValue === '') {
                    searchForm.submit();
                }
            }, 500);
        });

        // Submit on Enter key
        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchForm.submit();
            }
        });

        // Clear search button
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            window.location.href = "{{ route('orangtua.index') }}";
        });
    }
});
</script>
@endpush
@endsection