@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-nurse text-blue-500 mr-2"></i> Daftar Petugas UKS
        </h5>
        <a href="{{ route('petugasuks.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Petugas UKS
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
    
    <!-- Filter & Search -->
    <div class="bg-gray-50 p-4 border-b">
        <div class="flex flex-col md:flex-row gap-3 justify-between">
            
            <!-- Input Pencarian -->
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input 
                    id="liveSearch" 
                    type="text" 
                    placeholder="Cari petugas..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
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
                
                <!-- Export -->
                <a 
                    href="{{ route('petugasuks.export', request()->query()) }}" 
                    class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center"
                >
                    <i class="fas fa-download mr-2 text-green-500"></i> Export
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
                        <div class="flex items-center space-x-1">
                            <span>NIP</span>
                            <i class="fas fa-sort text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <span>Nama Petugas</span>
                            <i class="fas fa-sort text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <span>No. Telepon</span>
                            <i class="fas fa-sort text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <span>Status</span>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="petugasTableBody">
                @forelse($petugasUKS as $petugas)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $petugas->NIP }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                {{ $petugas->nama_petugas_uks }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($petugas->no_telp)
                                <div class="flex items-center">
                                    <i class="fas fa-phone-alt mr-2 text-green-500"></i>
                                    {{ $petugas->no_telp }}
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($petugas->status_aktif)
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
                                <a href="{{ route('petugasuks.show', $petugas->NIP) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('petugasuks.edit', $petugas->NIP) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('petugasuks.destroy', $petugas->NIP) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus petugas ini?')">
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
                                    <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data petugas UKS</h3>
                                <p class="text-gray-400 mb-4">Belum ada data petugas UKS yang tersedia</p>
                                <a href="{{ route('petugasuks.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Petugas UKS Sekarang
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
                    Menampilkan <span class="font-medium">{{ $petugasUKS->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $petugasUKS->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $petugasUKS->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $petugasUKS->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Filter Data Petugas UKS</h3>
            <button id="closeFilterModal" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('petugasuks.index') }}" method="GET">
            <!-- Filter Status -->
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            
            <!-- Kata Kunci Pencarian -->
            <div class="mb-4">
                <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari nama atau NIP...">
            </div>
            
            <!-- Tombol Filter -->
            <div class="flex justify-end space-x-2">
                @if(request()->anyFilled(['status', 'keyword']))
                    <a href="{{ route('petugasuks.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md">
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
        
        // Live search functionality
        const searchInput = document.getElementById('liveSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                performLiveSearch();
            });
            
            // Juga cari saat halaman dimuat pertama kali
            performLiveSearch();
        }
        
        function performLiveSearch() {
            const filter = searchInput.value.toUpperCase();
            const tbody = document.getElementById('petugasTableBody');
            const rows = tbody.getElementsByTagName('tr');
            let visibleCount = 0;
            
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].cells && rows[i].cells.length > 1) {
                    let visible = false;
                    
                    // Mencari di semua cell
                    for (let j = 0; j < rows[i].cells.length - 1; j++) { // Abaikan kolom terakhir (aksi)
                        const cell = rows[i].cells[j];
                        const text = cell.textContent || cell.innerText;
                        
                        if (text.toUpperCase().indexOf(filter) > -1) {
                            visible = true;
                            break;
                        }
                    }
                    
                    if (visible) {
                        rows[i].style.display = '';
                        visibleCount++;
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
            
            // Tampilkan pesan jika tidak ada hasil
            const noDataRow = document.getElementById('noDataFound');
            if (visibleCount === 0 && filter !== '') {
                if (!noDataRow) {
                    const newRow = document.createElement('tr');
                    newRow.id = 'noDataFound';
                    newRow.innerHTML = `
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-100 rounded-full p-4 mb-3">
                                    <i class="fas fa-search text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Tidak ditemukan data yang sesuai</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Coba gunakan kata kunci lain atau hapus filter pencarian
                                </p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(newRow);
                }
            } else if (noDataRow) {
                noDataRow.remove();
            }
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