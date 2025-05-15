@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-clipboard-check text-blue-500 mr-2"></i> Daftar Pemeriksaan Awal
        </h5>
        <a href="{{ route('pemeriksaan_awal.create') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan
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
                    placeholder="Cari pemeriksaan..." 
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
                
                <!-- Reset -->
                <a 
                    href="{{ route('pemeriksaan_awal.index') }}" 
                    class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center"
                >
                    <i class="fas fa-sync-alt mr-2 text-gray-500"></i> Reset
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
                        <div class="flex items-center">
                            <i class="fas fa-hashtag mr-2 text-blue-500"></i>
                            ID
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-link mr-2 text-blue-500"></i>
                            Detail Pemeriksaan
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-temperature-high mr-2 text-red-500"></i>
                            Suhu
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-heartbeat mr-2 text-pink-500"></i>
                            Nadi
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-lungs mr-2 text-green-500"></i>
                            Pernapasan
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2 text-amber-500"></i>
                            Status Nyeri
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-cogs mr-2 text-purple-500"></i>
                            Aksi
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="pemeriksaanTableBody">
                @forelse($pemeriksaanAwals as $pemeriksaanAwal)
                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                            {{ $pemeriksaanAwal->Id_PreAwal }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $pemeriksaanAwal->Id_DetPrx }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->Suhu)
                            <span class="{{ $pemeriksaanAwal->Suhu > 37.5 ? 'px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800' : 'px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800' }}">
                                {{ $pemeriksaanAwal->Suhu }} °C
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->Nadi)
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                {{ $pemeriksaanAwal->Nadi }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->Pernapasan)
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800">
                                {{ $pemeriksaanAwal->Pernapasan }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($pemeriksaanAwal->Status_Nyeri === 0)
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 flex items-center w-fit">
                                <i class="fas fa-check-circle mr-1"></i> Tidak Ada
                            </span>
                        @elseif($pemeriksaanAwal->Status_Nyeri === 1)
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 flex items-center w-fit">
                                <i class="fas fa-info-circle mr-1"></i> Ringan
                            </span>
                        @elseif($pemeriksaanAwal->Status_Nyeri === 2)
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 flex items-center w-fit">
                                <i class="fas fa-exclamation-circle mr-1"></i> Sedang
                            </span>
                        @elseif($pemeriksaanAwal->Status_Nyeri === 3)
                            <span class="px-2.5 py-1 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 flex items-center w-fit">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Berat
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-1">
                            <a href="{{ route('pemeriksaan_awal.show', $pemeriksaanAwal->Id_PreAwal) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('pemeriksaan_awal.edit', $pemeriksaanAwal->Id_PreAwal) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('pemeriksaan_awal.destroy', $pemeriksaanAwal->Id_PreAwal) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pemeriksaan ini?')">
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
                                    <i class="fas fa-clipboard-check text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data pemeriksaan awal</h3>
                                <p class="text-gray-400 mb-4">Belum ada data pemeriksaan awal yang tersedia</p>
                                <a href="{{ route('pemeriksaan_awal.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Pemeriksaan Sekarang
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
                @if(isset($pemeriksaanAwals) && method_exists($pemeriksaanAwals, 'total'))
                <p class="text-sm text-gray-700">
                    Menampilkan <span class="font-medium">{{ $pemeriksaanAwals->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $pemeriksaanAwals->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $pemeriksaanAwals->total() }}</span> data
                </p>
                @endif
            </div>
            <div>
                @if(isset($pemeriksaanAwals) && method_exists($pemeriksaanAwals, 'links'))
                    {{ $pemeriksaanAwals->appends(request()->query())->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Filter Data Pemeriksaan</h3>
            <button id="closeFilterModal" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('pemeriksaan_awal.index') }}" method="GET">
            <!-- Filter Suhu -->
            <div class="mb-4">
                <label for="suhu" class="block text-sm font-medium text-gray-700 mb-1">Suhu</label>
                <select id="suhu" name="suhu" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Suhu</option>
                    <option value="normal" {{ request('suhu') == 'normal' ? 'selected' : '' }}>Normal (≤ 37.5°C)</option>
                    <option value="tinggi" {{ request('suhu') == 'tinggi' ? 'selected' : '' }}>Tinggi (> 37.5°C)</option>
                </select>
            </div>
            
            <!-- Filter Status Nyeri -->
            <div class="mb-4">
                <label for="status_nyeri" class="block text-sm font-medium text-gray-700 mb-1">Status Nyeri</label>
                <select id="status_nyeri" name="status_nyeri" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="0" {{ request('status_nyeri') == '0' ? 'selected' : '' }}>Tidak Ada</option>
                    <option value="1" {{ request('status_nyeri') == '1' ? 'selected' : '' }}>Ringan</option>
                    <option value="2" {{ request('status_nyeri') == '2' ? 'selected' : '' }}>Sedang</option>
                    <option value="3" {{ request('status_nyeri') == '3' ? 'selected' : '' }}>Berat</option>
                </select>
            </div>
            
            <!-- Filter Tanggal -->
            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pemeriksaan</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <!-- Kata Kunci Pencarian -->
            <div class="mb-4">
                <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari ID atau detail...">
            </div>
            
            <!-- Tombol Filter -->
            <div class="flex justify-end space-x-2">
                @if(request()->anyFilled(['status_nyeri', 'suhu', 'keyword', 'tanggal']))
                    <a href="{{ route('pemeriksaan_awal.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md">
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
        
        // Live search functionality
        const searchInput = document.getElementById('searchInput');
        
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toUpperCase();
                const tbody = document.getElementById('pemeriksaanTableBody');
                const rows = tbody.getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].getElementsByTagName('td').length > 1) {
                        const idCell = rows[i].getElementsByTagName('td')[0];
                        const detailCell = rows[i].getElementsByTagName('td')[1];
                        
                        if (idCell && detailCell) {
                            const idValue = idCell.textContent || idCell.innerText;
                            const detailValue = detailCell.textContent || detailCell.innerText;
                            
                            if (idValue.toUpperCase().indexOf(filter) > -1 || 
                                detailValue.toUpperCase().indexOf(filter) > -1) {
                                rows[i].style.display = '';
                            } else {
                                rows[i].style.display = 'none';
                            }
                        }
                    }
                }
                
                // Tampilkan pesan jika tidak ada hasil
                const noDataRow = document.getElementById('noDataFound');
                let visibleCount = Array.from(rows).filter(row => row.style.display !== 'none').length;
                
                if (visibleCount === 0 && filter !== '') {
                    if (!noDataRow) {
                        const newRow = document.createElement('tr');
                        newRow.id = 'noDataFound';
                        newRow.innerHTML = `
                            <td colspan="7" class="px-6 py-8 text-center">
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