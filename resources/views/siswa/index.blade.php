@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section - Putih dengan ikon dan tombol biru -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-graduate text-blue-500 mr-2"></i> Daftar Siswa
        </h5>
        <div class="flex space-x-2">
            <a href="{{ route('siswa.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Siswa
            </a>
        </div>
    </div>
    
    <!-- Filter & Search -->
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex flex-col md:flex-row gap-3 justify-between">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <form id="searchForm" action="{{ route('siswa.index') }}" method="GET" class="inline">
                    <input id="searchInput" name="keyword" type="text" placeholder="Cari ID atau nama siswa..." class="pl-10 pr-10 py-2 border border-gray-300 rounded-md w-full md:w-64 focus:ring-blue-500 focus:border-blue-500" value="{{ request('keyword') }}">
                    <button type="button" id="clearSearchBtn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" style="display: {{ request('keyword') ? 'block' : 'none' }};">
                        &times;
                    </button>
                </form>
            </div>
            
            <div class="flex space-x-2">
                <a href="#" onclick="document.getElementById('importModal').classList.remove('hidden'); return false;" class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center">
                    <i class="fas fa-file-import text-green-500 mr-2"></i> Import
                </a>
                <a href="#" onclick="alert('Fitur export belum diimplementasikan'); return false;" class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center">
                    <i class="fas fa-file-export text-blue-500 mr-2"></i> Export
                </a>
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
                        Jenis Kelamin
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal Lahir
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
            <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                @forelse($siswas as $siswa)
                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                            {{ $siswa->id_siswa }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $siswa->nama_siswa }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($siswa->jenis_kelamin == 'L')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-male mr-1"></i> Laki-laki
                            </span>
                        @elseif($siswa->jenis_kelamin == 'P')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                <i class="fas fa-female mr-1"></i> Perempuan
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            <i class="fas fa-calendar-alt mr-1"></i> {{ $siswa->tanggal_lahir ? date('d/m/Y', strtotime($siswa->tanggal_lahir)) : '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($siswa->detailSiswa && $siswa->detailSiswa->kelas)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                <i class="fas fa-chalkboard mr-1"></i> {{ $siswa->detailSiswa->kelas->nama_kelas }}
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-chalkboard mr-1"></i> Belum ada kelas
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($siswa->status_aktif)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-1">
                            <a href="{{ route('siswa.show', $siswa->id_siswa) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('siswa.edit', $siswa->id_siswa) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('siswa.destroy', $siswa->id_siswa) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
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
                                <i class="fas fa-user-graduate text-4xl text-gray-400"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data siswa</h3>
                        <p class="text-gray-400 mb-4">Belum ada data siswa yang tersedia</p>
                        <a href="{{ route('siswa.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Siswa Sekarang
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
                    Menampilkan <span class="font-medium">{{ count($siswas) > 0 ? 1 : 0 }}</span> 
                    sampai <span class="font-medium">{{ count($siswas) }}</span> 
                    dari <span class="font-medium">{{ $siswas->total() }}</span> data
                </p>
            </div>
            @if(isset($siswas) && $siswas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>
                {{ $siswas->appends(request()->except('page'))->links() }}
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
                        <i class="fas fa-file-import text-green-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Import Data Siswa
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Silakan unggah file Excel (.xlsx, .xls, .csv) yang berisi data siswa untuk diimport.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="#" method="POST" enctype="multipart/form-data" class="mt-5">
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
                        <p class="text-xs text-yellow-600 mt-1">Pastikan file memiliki kolom berikut: ID Siswa, Nama Siswa, Jenis Kelamin, Tanggal Lahir, dan Tanggal Masuk</p>
                        <a href="#" class="inline-flex items-center mt-2 text-xs text-blue-600 hover:text-blue-800">
                            <i class="fas fa-download mr-1"></i> Download Template
                        </a>
                    </div>
                    
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Import Data
                        </button>
                        <button type="button" id="cancelImport" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
            window.location.href = "{{ route('siswa.index') }}";
        });
    }
    
    // Import Modal
    const importModal = document.getElementById('importModal');
    const cancelImport = document.getElementById('cancelImport');
    const modalOverlay = document.getElementById('modalOverlay');
    
    if (cancelImport) {
        cancelImport.addEventListener('click', function() {
            importModal.classList.add('hidden');
        });
    }
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                importModal.classList.add('hidden');
            }
        });
    }
    
    // Auto close alerts after 5 seconds
    const alerts = document.querySelectorAll('.close-alert').forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });
});
</script>
@endpush
@endsection