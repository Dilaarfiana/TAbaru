@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section - DIPERBAIKI -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-users-class text-blue-500 mr-2"></i> Daftar Alokasi Siswa
        </h5>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('alokasi.unallocated') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Siswa Belum Dialokasi
            </a>
            <!-- <a href="{{ route('alokasi.allocated') }}" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-list mr-2"></i> Siswa Teralokasi
            </a> -->
            <a href="{{ route('alokasi.kenaikanForm') }}" class="bg-indigo-500 text-white hover:bg-indigo-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-level-up-alt mr-2"></i> Kenaikan Kelas
            </a>
            <a href="{{ route('alokasi.lulusForm') }}" class="bg-purple-500 text-white hover:bg-purple-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-graduation-cap mr-2"></i> Kelulusan
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

    <!-- Status Summary Cards - DITAMBAHKAN -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 border-b">
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-500 text-white">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Siswa</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $totalSiswa ?? $siswas->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                        <i class="fas fa-check-circle text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Sudah Dialokasi</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $siswaTeralokasi ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-orange-500 text-white">
                        <i class="fas fa-clock text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Belum Dialokasi</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $siswaBelumDialokasi ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-sm border">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-purple-500 text-white">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Alumni</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $siswaAlumni ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter & Search Section -->
    <div class="bg-gray-50 p-4 border-b">
        <form action="{{ route('alokasi.index') }}" method="GET">
            <div class="flex flex-col md:flex-row gap-3 items-center">
                <!-- Filter Status - DITAMBAHKAN -->
                <div class="relative w-full md:w-1/5">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <i class="fas fa-filter text-gray-500"></i>
                    </div>
                    <select name="status" id="status" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="">Semua Status</option>
                        <option value="allocated" {{ request('status') == 'allocated' ? 'selected' : '' }}>Sudah Dialokasi</option>
                        <option value="unallocated" {{ request('status') == 'unallocated' ? 'selected' : '' }}>Belum Dialokasi</option>
                        <option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Alumni</option>
                    </select>
                </div>
                
                <!-- Filter Jurusan -->
                <div class="relative w-full md:w-1/5">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <i class="fas fa-graduation-cap text-gray-500"></i>
                    </div>
                    <select name="jurusan" id="jurusan" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->Kode_Jurusan }}" {{ request('jurusan') == $jurusan->Kode_Jurusan ? 'selected' : '' }}>{{ $jurusan->Nama_Jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filter Kelas -->
                <div class="relative w-full md:w-1/5">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <i class="fas fa-chalkboard text-gray-500"></i>
                    </div>
                    <select name="kelas" id="kelas" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="">Semua Kelas</option>
                        @foreach($kelass as $kelas)
                        <option value="{{ $kelas->Kode_Kelas }}" {{ request('kelas') == $kelas->Kode_Kelas ? 'selected' : '' }}>{{ $kelas->Nama_Kelas }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Input Pencarian -->
                <div class="relative w-full md:w-1/3">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-500"></i>
                    </div>
                    <input 
                        id="liveSearch" 
                        name="nama" 
                        value="{{ request('nama') }}"
                        type="text" 
                        placeholder="Cari siswa..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                
                <!-- Tombol Filter -->
                <div class="w-full md:w-auto flex-shrink-0">
                    <button 
                        type="submit" 
                        class="bg-blue-500 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-blue-600 flex items-center w-full justify-center"
                    >
                        <i class="fas fa-filter mr-2"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        NO
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            ID SISWA <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            NAMA SISWA <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            STATUS <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            JURUSAN <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center">
                            KELAS <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        AKSI
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                @forelse($siswas as $key => $siswa)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $key + $siswas->firstItem() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $siswa->id_siswa }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                {{ $siswa->nama_siswa }}
                            </div>
                        </td>
                        <!-- Status Column - DIPERBAIKI -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if($siswa->status == 'alumni' || $siswa->status == 'lulus')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-graduation-cap mr-1"></i> Alumni
                                </span>
                            @elseif(isset($siswa->detailSiswa->kelas))
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Dialokasi
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-clock mr-1"></i> Belum Dialokasi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if(isset($siswa->detailSiswa->kelas->jurusan))
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-graduation-cap mr-1"></i> {{ $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan }}
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-minus-circle mr-1"></i> -
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @if(isset($siswa->detailSiswa->kelas))
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-chalkboard mr-1"></i> {{ $siswa->detailSiswa->kelas->Nama_Kelas }}
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-minus-circle mr-1"></i> -
                                </span>
                            @endif
                        </td>
                        <!-- Action Column - DIPERBAIKI -->
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-1">
                                @if($siswa->status == 'alumni' || $siswa->status == 'lulus')
                                    <!-- Alumni - No actions available -->
                                    <span class="px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded">
                                        <i class="fas fa-lock mr-1"></i> Alumni
                                    </span>
                                @elseif(isset($siswa->detailSiswa->kelas))
                                    <!-- Sudah dialokasi - bisa pindah atau hapus -->
                                    <button type="button" onclick="openPindahModal('{{ $siswa->id_siswa }}', '{{ $siswa->nama_siswa }}')" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Pindah Kelas">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    
                                    <button type="button" onclick="openHapusModal('{{ $siswa->id_siswa }}', '{{ $siswa->nama_siswa }}')" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus Alokasi">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @else
                                    <!-- Belum dialokasi - bisa alokasi -->
                                    <button type="button" onclick="openAlokasiModal('{{ $siswa->id_siswa }}', '{{ $siswa->nama_siswa }}')" class="text-white bg-green-500 hover:bg-green-600 rounded-md p-2 transition-colors duration-200" title="Alokasi Siswa">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-100 rounded-full p-5 mb-4">
                                    <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data siswa</h3>
                                <p class="text-gray-400 mb-4">Belum ada data siswa yang tersedia</p>
                                <a href="{{ route('siswa.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Siswa Baru
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
                    Menampilkan <span class="font-medium">{{ $siswas->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $siswas->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $siswas->total() }}</span> data
                </p>
            </div>
            <div>
                {{ $siswas->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Alokasi Siswa -->
<div id="alokasiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative top-0 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-user-plus text-blue-500 mr-2"></i>Alokasi Siswa ke Kelas
            </h3>
            <button id="closeAlokasiModal" class="text-gray-600 hover:text-gray-800" onclick="closeModals()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="alokasiForm" action="{{ route('alokasi.process') }}" method="POST">
            @csrf
            <input type="hidden" name="id_siswa" id="alokasi_id_siswa">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                <div id="alokasi_nama_siswa" class="px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700"></div>
            </div>
            
            <div class="mb-4">
                <label for="alokasi_jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-graduation-cap text-gray-400"></i>
                    </div>
                    <select name="kode_jurusan" id="alokasi_jurusan" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500" onchange="filterKelas()">
                        <option value="">Pilih Jurusan</option>
                        @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->Kode_Jurusan }}">{{ $jurusan->Nama_Jurusan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="alokasi_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-chalkboard text-gray-400"></i>
                    </div>
                    <select name="kode_kelas" id="alokasi_kelas" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kelas</option>
                        <!-- Opsi kelas akan di-load menggunakan JavaScript -->
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-200">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors duration-200">
                    <i class="fas fa-check mr-1"></i> Alokasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pindah Kelas -->
<div id="pindahModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative top-0 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-exchange-alt text-yellow-500 mr-2"></i>Pindah Kelas
            </h3>
            <button id="closePindahModal" class="text-gray-600 hover:text-gray-800" onclick="closeModals()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="pindahForm" action="{{ route('alokasi.pindah') }}" method="POST">
            @csrf
            <input type="hidden" name="id_siswa" id="pindah_id_siswa">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                <div id="pindah_nama_siswa" class="px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700"></div>
            </div>
            
            <div class="mb-4">
                <label for="pindah_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas Tujuan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-chalkboard text-gray-400"></i>
                    </div>
                    <select name="kode_kelas" id="pindah_kelas" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelass as $kelas)
                        <option value="{{ $kelas->Kode_Kelas }}">{{ $kelas->Nama_Kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-200">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors duration-200">
                    <i class="fas fa-check mr-1"></i> Pindahkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Alokasi -->
<div id="hapusModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative top-0 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-trash-alt text-red-500 mr-2"></i>Hapus Alokasi Siswa
            </h3>
            <button id="closeHapusModal" class="text-gray-600 hover:text-gray-800" onclick="closeModals()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="hapusForm" action="{{ route('alokasi.kembalikan') }}" method="POST">
            @csrf
            <input type="hidden" name="id_siswa" id="hapus_id_siswa">
            
            <div class="mb-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus Alokasi</h3>
                <p class="text-gray-500 mb-2">Apakah Anda yakin ingin menghapus alokasi untuk siswa:</p>
                <p id="hapus_nama_siswa" class="font-semibold text-gray-900"></p>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-200">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors duration-200">
                    <i class="fas fa-check mr-1"></i> Hapus Alokasi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

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
        }
        
        function performLiveSearch() {
            const filter = searchInput.value.toUpperCase();
            const tbody = document.getElementById('siswaTableBody');
            const rows = tbody.getElementsByTagName('tr');
            let visibleCount = 0;
            
            for (let i = 0; i < rows.length; i++) {
                if (rows[i].cells && rows[i].cells.length > 1) {
                    let visible = false;
                    
                    // Mencari di semua cell kecuali kolom aksi
                    for (let j = 0; j < rows[i].cells.length - 1; j++) {
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
        }
        
        // Filter dropdown kelas berdasarkan jurusan pada halaman utama
        const jurusanMainFilter = document.getElementById('jurusan');
        const kelasMainFilter = document.getElementById('kelas');
        
        if (jurusanMainFilter && kelasMainFilter) {
            jurusanMainFilter.addEventListener('change', function() {
                const selectedJurusan = this.value;
                
                // Reset dropdown kelas
                kelasMainFilter.innerHTML = '<option value="">Semua Kelas</option>';
                
                // Jika ada jurusan yang dipilih, filter kelas
                if (selectedJurusan) {
                    const filteredKelas = semuaKelas.filter(kelas => {
                        const kelasJurusan = kelas.Kode_Jurusan ? kelas.Kode_Jurusan.toString().toUpperCase().trim() : '';
                        const selectedJurusanUpper = selectedJurusan.toString().toUpperCase().trim();
                        return kelasJurusan === selectedJurusanUpper;
                    });
                    
                    filteredKelas.forEach(kelas => {
                        const option = document.createElement('option');
                        option.value = kelas.Kode_Kelas;
                        option.textContent = kelas.Nama_Kelas;
                        // Pertahankan selected value jika ada
                        if (kelas.Kode_Kelas === '{{ request("kelas") }}') {
                            option.selected = true;
                        }
                        kelasMainFilter.appendChild(option);
                    });
                } else {
                    // Jika tidak ada jurusan dipilih, tampilkan semua kelas
                    semuaKelas.forEach(kelas => {
                        const option = document.createElement('option');
                        option.value = kelas.Kode_Kelas;
                        option.textContent = kelas.Nama_Kelas;
                        // Pertahankan selected value jika ada
                        if (kelas.Kode_Kelas === '{{ request("kelas") }}') {
                            option.selected = true;
                        }
                        kelasMainFilter.appendChild(option);
                    });
                }
            });
            
            // Trigger change event on page load untuk mempertahankan filter yang dipilih
            if (jurusanMainFilter.value) {
                jurusanMainFilter.dispatchEvent(new Event('change'));
            }
        }
    });

    // Data kelas untuk filter dropdown
    const semuaKelas = @json($kelass);

    // Buka modal alokasi
    function openAlokasiModal(id, nama) {
        document.getElementById('alokasi_id_siswa').value = id;
        document.getElementById('alokasi_nama_siswa').textContent = nama;
        document.getElementById('alokasiModal').classList.remove('hidden');
        
        // Reset form
        document.getElementById('alokasi_jurusan').selectedIndex = 0;
        document.getElementById('alokasi_kelas').innerHTML = '<option value="">Pilih Kelas</option>';
    }

    // Buka modal pindah
    function openPindahModal(id, nama) {
        document.getElementById('pindah_id_siswa').value = id;
        document.getElementById('pindah_nama_siswa').textContent = nama;
        document.getElementById('pindahModal').classList.remove('hidden');
        
        // Reset form
        document.getElementById('pindah_kelas').selectedIndex = 0;
    }

    // Buka modal hapus
    function openHapusModal(id, nama) {
        document.getElementById('hapus_id_siswa').value = id;
        document.getElementById('hapus_nama_siswa').textContent = nama;
        document.getElementById('hapusModal').classList.remove('hidden');
    }

    // Tutup semua modal
    function closeModals() {
        document.getElementById('alokasiModal').classList.add('hidden');
        document.getElementById('pindahModal').classList.add('hidden');
        document.getElementById('hapusModal').classList.add('hidden');
    }

    // Filter kelas berdasarkan jurusan yang dipilih untuk modal alokasi
    function filterKelas() {
        const jurusanSelect = document.getElementById('alokasi_jurusan');
        const kelasSelect = document.getElementById('alokasi_kelas');
        const selectedJurusan = jurusanSelect.value;
        
        // Reset option kelas
        kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
        
        // Filter kelas berdasarkan jurusan
        if (selectedJurusan) {
            const kelasFiltered = semuaKelas.filter(kelas => {
                const kelasJurusan = kelas.Kode_Jurusan ? kelas.Kode_Jurusan.toString().toUpperCase().trim() : '';
                const selectedJurusanUpper = selectedJurusan.toString().toUpperCase().trim();
                return kelasJurusan === selectedJurusanUpper;
            });
            
            if (kelasFiltered.length === 0) {
                const option = document.createElement('option');
                option.value = "";
                option.textContent = "-- Tidak ada kelas untuk jurusan ini --";
                option.disabled = true;
                kelasSelect.appendChild(option);
            } else {
                kelasFiltered.forEach(kelas => {
                    const option = document.createElement('option');
                    option.value = kelas.Kode_Kelas;
                    option.textContent = kelas.Nama_Kelas;
                    kelasSelect.appendChild(option);
                });
            }
        }
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modals = ['alokasiModal', 'pindahModal', 'hapusModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModals();
            }
        });
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModals();
        }
    });
</script>
@endpush