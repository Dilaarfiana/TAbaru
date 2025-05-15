@extends('layouts.admin')

@section('page_title', 'Detail Siswa')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-graduate text-blue-500 mr-2"></i> Detail Data Siswa
        </h5>
        <div class="flex space-x-2">
            <a href="{{ route('detailsiswa.cleanup') }}" class="bg-red-500 text-white hover:bg-red-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center" onclick="return confirm('Apakah Anda yakin ingin membersihkan data duplikat?')">
                <i class="fas fa-broom mr-2"></i> Bersihkan Duplikat
            </a>
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
    
    <!-- Filter Section -->
    <div class="bg-gray-50 p-4 border-b">
        <form action="{{ route('detailsiswa.index') }}" method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div>
                <label for="alokasi_status" class="block text-sm font-medium text-gray-700 mb-1">Status Alokasi</label>
                <select name="alokasi_status" id="alokasi_status" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="allocated" {{ request('alokasi_status') == 'allocated' ? 'selected' : '' }}>Sudah Teralokasi</option>
                    <option value="unallocated" {{ request('alokasi_status') == 'unallocated' ? 'selected' : '' }}>Belum Teralokasi</option>
                </select>
            </div>
            
            <div>
                <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan" id="jurusan" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->Kode_Jurusan }}" {{ request('jurusan') == $jurusan->Kode_Jurusan ? 'selected' : '' }}>
                            {{ $jurusan->Nama_Jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="kelas" id="kelas" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($kelass as $kelas)
                        <option value="{{ $kelas->Kode_Kelas }}" {{ request('kelas') == $kelas->Kode_Kelas ? 'selected' : '' }}>
                            {{ $kelas->Nama_Kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="lg:col-span-2 md:flex gap-2">
                <div class="flex-grow mb-2 md:mb-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            id="search" 
                            placeholder="Cari nama atau ID..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center h-10 w-full justify-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Info Cards -->
    <div class="p-4 grid grid-cols-2 lg:grid-cols-4 gap-3 bg-white border-b">
        <div class="bg-white p-3 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-users text-blue-500"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Total Siswa</p>
                    <p class="text-lg font-bold text-gray-800">{{ $totalSiswa ?? '0' }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border border-green-200 shadow-sm">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user-check text-green-500"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Sudah Teralokasi</p>
                    <p class="text-lg font-bold text-gray-800">{{ $totalTeralokasi ?? '0' }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border border-yellow-200 shadow-sm">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user-minus text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Belum Teralokasi</p>
                    <p class="text-lg font-bold text-gray-800">{{ $totalBelumTeralokasi ?? '0' }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-3 rounded-lg border border-purple-200 shadow-sm">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-school text-purple-500"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Total Kelas</p>
                    <p class="text-lg font-bold text-gray-800">{{ $totalKelas ?? '0' }}</p>
                </div>
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
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ $detail->id_detsiswa }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-indigo-100 text-indigo-800">
                                {{ $detail->id_siswa }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                            {{ $detail->siswa->nama_siswa ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->siswa)
                                @if($detail->siswa->jenis_kelamin == 'L')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">
                                        Laki-laki
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-pink-100 text-pink-800">
                                        Perempuan
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->jurusan)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                    {{ $detail->jurusan->Nama_Jurusan }}
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-800">
                                    Belum Dialokasikan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->kelas)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $detail->kelas->Nama_Kelas }}
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-gray-100 text-gray-800">
                                    Belum Dialokasikan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($detail->kode_kelas)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                    Teralokasi
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-yellow-100 text-yellow-800">
                                    Belum Teralokasi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('siswa.show', $detail->id_siswa) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-2 rounded-md mr-1" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('alokasi.index') }}?search={{ $detail->id_siswa }}" class="text-purple-600 hover:text-purple-900 bg-purple-100 hover:bg-purple-200 p-2 rounded-md" title="Kelola Alokasi">
                                <i class="fas fa-user-cog"></i>
                            </a>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto submit form on change
        document.querySelectorAll('#filter-form select').forEach(el => {
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
        
        // Error notification auto-close
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
    });
</script>
@endpush
@endsection