@extends('layouts.app')

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
                    placeholder="Cari nama atau spesialisasi..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
                    value="{{ request('search') }}"
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
                        Nama Dokter
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Spesialisasi
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        No. Telepon
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="dokterTableBody">
                @forelse ($dokters as $dokter)
                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                            {{ $dokter->Id_Dokter }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user-md text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $dokter->Nama_Dokter }}</div>
                                @if($dokter->Alamat)
                                    <div class="text-sm text-gray-500">{{ Str::limit($dokter->Alamat, 30) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($dokter->Spesialisasi)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-stethoscope mr-1"></i> {{ $dokter->Spesialisasi }}
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">
                                <i class="fas fa-minus mr-1"></i> Belum diset
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if($dokter->No_Telp)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-phone mr-1"></i> 
                                @if(str_starts_with($dokter->No_Telp, '+62'))
                                    {{ $dokter->No_Telp }}
                                @elseif(str_starts_with($dokter->No_Telp, '62'))
                                    +{{ $dokter->No_Telp }}
                                @elseif(str_starts_with($dokter->No_Telp, '0'))
                                    +62{{ substr($dokter->No_Telp, 1) }}
                                @else
                                    +62{{ $dokter->No_Telp }}
                                @endif
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">
                                <i class="fas fa-phone-slash mr-1"></i> Belum diset
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($dokter->status_aktif == 1)
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
                            <a href="{{ route('dokter.show', $dokter->Id_Dokter) }}" 
                               class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" 
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('dokter.edit', $dokter->Id_Dokter) }}" 
                               class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" 
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('dokter.destroy', $dokter->Id_Dokter) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" 
                                        title="Hapus" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data dokter ini? Data yang sudah dihapus tidak dapat dikembalikan.')">
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
                            <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data dokter</h3>
                            <p class="text-gray-400 mb-4">
                                @if(request()->anyFilled(['search', 'spesialis', 'status']))
                                    Tidak ada dokter yang sesuai dengan kriteria pencarian Anda
                                @else
                                    Belum ada data dokter yang tersedia
                                @endif
                            </p>
                            @if(request()->anyFilled(['search', 'spesialis', 'status']))
                                <div class="flex space-x-2">
                                    <a href="{{ route('dokter.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        <i class="fas fa-undo mr-2"></i> Reset Filter
                                    </a>
                                    <a href="{{ route('dokter.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        <i class="fas fa-plus-circle mr-2"></i> Tambah Dokter
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('dokter.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Dokter Sekarang
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
                    Menampilkan <span class="font-medium">{{ $dokters->firstItem() ?? 0 }}</span> - 
                    <span class="font-medium">{{ $dokters->lastItem() ?? 0 }}</span> dari 
                    <span class="font-medium">{{ $dokters->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $dokters->appends(request()->query())->links() }}
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
                    @if(isset($spesialisasi))
                        @foreach($spesialisasi as $s)
                            <option value="{{ $s }}" {{ request('spesialis') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    @else
                        <option value="Umum" {{ request('spesialis') == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Anak" {{ request('spesialis') == 'Anak' ? 'selected' : '' }}>Anak</option>
                        <option value="Bedah" {{ request('spesialis') == 'Bedah' ? 'selected' : '' }}>Bedah</option>
                        <option value="Jantung" {{ request('spesialis') == 'Jantung' ? 'selected' : '' }}>Jantung</option>
                        <option value="THT" {{ request('spesialis') == 'THT' ? 'selected' : '' }}>THT</option>
                        <option value="Mata" {{ request('spesialis') == 'Mata' ? 'selected' : '' }}>Mata</option>
                        <option value="Kulit & Kelamin" {{ request('spesialis') == 'Kulit & Kelamin' ? 'selected' : '' }}>Kulit & Kelamin</option>
                        <option value="Saraf" {{ request('spesialis') == 'Saraf' ? 'selected' : '' }}>Saraf</option>
                        <option value="Gigi" {{ request('spesialis') == 'Gigi' ? 'selected' : '' }}>Gigi</option>
                        <option value="Psikiatri" {{ request('spesialis') == 'Psikiatri' ? 'selected' : '' }}>Psikiatri</option>
                        <option value="Penyakit Dalam" {{ request('spesialis') == 'Penyakit Dalam' ? 'selected' : '' }}>Penyakit Dalam</option>
                    @endif
                </select>
            </div>
            
            <!-- Filter Status Aktif -->
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Aktif</label>
                <select id="status" name="status" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            
            <!-- Kata Kunci Pencarian -->
            <div class="mb-4">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Cari nama dokter...">
            </div>
            
            <!-- Tombol Filter -->
            <div class="flex justify-end space-x-2">
                @if(request()->anyFilled(['spesialis', 'search', 'status']))
                    <a href="{{ route('dokter.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                @endif
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="fas fa-search mr-1"></i> Terapkan Filter
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
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toUpperCase();
                const tbody = document.getElementById('dokterTableBody');
                const rows = tbody.getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].getElementsByTagName('td').length > 1) {
                        const idCell = rows[i].getElementsByTagName('td')[0];
                        const namaCell = rows[i].getElementsByTagName('td')[1];
                        const spesialisasiCell = rows[i].getElementsByTagName('td')[2];
                        
                        if (idCell && namaCell && spesialisasiCell) {
                            const idValue = idCell.textContent || idCell.innerText;
                            const namaValue = namaCell.textContent || namaCell.innerText;
                            const spesialisasiValue = spesialisasiCell.textContent || spesialisasiCell.innerText;
                            
                            if (idValue.toUpperCase().indexOf(filter) > -1 || 
                                namaValue.toUpperCase().indexOf(filter) > -1 ||
                                spesialisasiValue.toUpperCase().indexOf(filter) > -1) {
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