@extends('layouts.app')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
@endphp

{{-- BLOCK ACCESS FOR ORANG TUA --}}
@if($isOrangTua)
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-red-50 border-l-4 border-red-500 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-red-800">Akses Ditolak</h3>
                    <p class="text-sm text-red-700 mt-2">
                        Anda tidak memiliki izin untuk mengakses halaman daftar siswa. 
                        Sebagai orang tua, Anda hanya dapat mengakses data siswa Anda sendiri.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('orangtua.siswa.show') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-child mr-2"></i>
                            Lihat Data Siswa Saya
                        </a>
                        <a href="{{ route('dashboard.orangtua') }}" 
                           class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <i class="fas fa-home mr-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Redirect otomatis setelah 3 detik
    setTimeout(function() {
        window.location.href = '{{ route("orangtua.siswa.show") }}';
    }, 3000);
    
    // Show countdown
    let countdown = 3;
    const countdownElement = document.createElement('div');
    countdownElement.className = 'mt-4 text-sm text-red-600';
    countdownElement.innerHTML = '<i class="fas fa-clock mr-1"></i>Akan dialihkan dalam <span id="countdown">3</span> detik...';
    document.querySelector('.border-red-500 .ml-4').appendChild(countdownElement);
    
    const countdownTimer = setInterval(function() {
        countdown--;
        document.getElementById('countdown').textContent = countdown;
        if (countdown <= 0) {
            clearInterval(countdownTimer);
        }
    }, 1000);
    </script>
    @endpush

@else
{{-- NORMAL CONTENT FOR OTHER ROLES --}}
@php
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'siswa' : ($isPetugas ? 'petugas.siswa' : 'dokter.siswa');
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';
    
    // Routes yang hanya untuk admin dan petugas
    if ($isAdmin) {
        $createRoute = 'siswa.create';
        $editRoute = 'siswa.edit';
    } elseif ($isPetugas) {
        $editRoute = 'petugas.siswa.edit';
    }
    
    // Check if we should reset filters (after import)
    $shouldReset = request()->has('reset');
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-graduate text-blue-500 mr-2"></i> Daftar Siswa
            @if($isDokter)
                <span class="ml-3 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                    <i class="fas fa-stethoscope mr-1"></i>Akses Dokter
                </span>
            @elseif($isPetugas)
                <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                    <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                </span>
            @elseif($isAdmin)
                <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                    <i class="fas fa-user-shield mr-1"></i>Akses Admin
                </span>
            @endif
        </h5>
        <div class="flex space-x-2">
            @if($isAdmin)
            <a href="{{ route('alokasi.unallocated') }}" class="bg-indigo-500 text-white hover:bg-indigo-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-user-check mr-2"></i> Alokasi Siswa
            </a>
            <a href="{{ route($createRoute) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Siswa
            </a>
            @endif
        </div>
    </div>
    
    <!-- Info Access Level -->
    @if($isDokter)
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    Anda mengakses data siswa dengan <strong>Akses Dokter</strong>. 
                    Anda dapat melihat semua data siswa namun tidak dapat mengubah atau menghapus data.
                </p>
            </div>
        </div>
    </div>
    @elseif($isPetugas)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Anda mengakses data siswa dengan <strong>Akses Petugas</strong>. 
                    Anda dapat melihat dan mengedit data dasar siswa, namun tidak dapat menambah, menghapus, atau mengubah status siswa.
                </p>
            </div>
        </div>
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
                <form id="searchForm" action="{{ route($indexRoute) }}" method="GET" class="inline">
                    <input id="searchInput" name="keyword" type="text" placeholder="Cari ID atau nama siswa..." class="pl-10 pr-10 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500" value="{{ request('keyword') }}">
                    <button type="button" id="clearSearchBtn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600" style="display: {{ request('keyword') ? 'block' : 'none' }};">
                        &times;
                    </button>
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
                    @if(request()->anyFilled(['status', 'jenis_kelamin', 'tahun_masuk', 'kelas', 'jurusan', 'status_alokasi']))
                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                            {{ collect(request()->only(['status', 'jenis_kelamin', 'tahun_masuk', 'kelas', 'jurusan', 'status_alokasi']))->filter()->count() }}
                        </span>
                    @endif
                </a>

                <!-- Reset Filter (jika ada filter aktif) -->
                @if(request()->anyFilled(['keyword', 'status', 'jenis_kelamin', 'tahun_masuk', 'kelas', 'jurusan', 'status_alokasi']))
                    <a 
                        href="{{ route($indexRoute) }}" 
                        class="bg-gray-500 text-white rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-600 flex items-center"
                    >
                        <i class="fas fa-times mr-2"></i> Reset
                    </a>
                @endif
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(request()->anyFilled(['keyword', 'status', 'jenis_kelamin', 'tahun_masuk', 'kelas', 'jurusan', 'status_alokasi']))
        <div class="mt-3 flex flex-wrap gap-2">
            <span class="text-sm text-gray-600 font-medium">Filter aktif:</span>
            
            @if(request('keyword'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Kata kunci: "{{ request('keyword') }}"
                    <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if(request('status'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Status: {{ request('status') == '1' ? 'Aktif' : 'Tidak Aktif' }}
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if(request('jenis_kelamin'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Jenis Kelamin: {{ request('jenis_kelamin') == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    <a href="{{ request()->fullUrlWithQuery(['jenis_kelamin' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if(request('tahun_masuk'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Tahun Masuk: {{ request('tahun_masuk') }}
                    <a href="{{ request()->fullUrlWithQuery(['tahun_masuk' => null]) }}" class="ml-1 text-yellow-600 hover:text-yellow-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if(request('kelas'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Kelas: {{ request('kelas') }}
                    <a href="{{ request()->fullUrlWithQuery(['kelas' => null]) }}" class="ml-1 text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if(request('jurusan'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    Jurusan: {{ request('jurusan') }}
                    <a href="{{ request()->fullUrlWithQuery(['jurusan' => null]) }}" class="ml-1 text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif

            @if(request('status_alokasi'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    Status Alokasi: {{ request('status_alokasi') == 'sudah' ? 'Sudah Dialokasi' : 'Belum Dialokasi' }}
                    <a href="{{ request()->fullUrlWithQuery(['status_alokasi' => null]) }}" class="ml-1 text-orange-600 hover:text-orange-800">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif
        </div>
        @endif
    </div>
    
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3 flex items-center justify-between">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
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
                <p class="text-sm text-red-700">{{ session('error') }}</p>
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
                <p class="text-sm text-blue-700">{{ session('info') }}</p>
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
                        ID Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Siswa
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tempat, Tgl Lahir
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Jenis Kelamin
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tgl Masuk
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
                        @php
                            $hasDetailSiswa = $siswa->detailSiswa ?? false;
                            $isAllocated = false;
                            $isFormatNew = substr($siswa->id_siswa, 0, 1) == '6';

                            if ($hasDetailSiswa) {
                                $kodeJurusan = $hasDetailSiswa->kode_jurusan ?? null;
                                $kodeKelas = $hasDetailSiswa->kode_kelas ?? null;
                                
                                if ($kodeJurusan && $kodeKelas) {
                                    $isAllocated = true;
                                }
                            }
                        @endphp
                        
                        @if($isFormatNew && !$isAllocated)
                            <div class="text-xs text-orange-600 mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Belum Dialokasi
                            </div>
                        @elseif($isFormatNew && $isAllocated)
                            <div class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle"></i> Teralokasi
                            </div>
                        @elseif(!$isFormatNew)
                            <div class="text-xs text-red-600 mt-1">
                                <i class="fas fa-exclamation-circle"></i> Format Lama
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $siswa->nama_siswa }}
                        {{-- Tampilkan kelas jika ada --}}
                        @if($siswa->detailSiswa && $siswa->detailSiswa->kelas)
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-graduation-cap mr-1"></i>{{ $siswa->detailSiswa->kelas->Nama_Kelas }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <div class="flex flex-col">
                            <span class="font-medium">{{ $siswa->tempat_lahir ?? '-' }}</span>
                            <span class="text-gray-500 text-xs">
                                {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') : '-' }}
                            </span>
                        </div>
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
                        <span class="text-gray-800">
                            {{ $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->format('d F Y') : '-' }}
                        </span>
                        @if($siswa->tanggal_masuk)
                            <div class="text-xs text-gray-500 mt-1">
                                ({{ \Carbon\Carbon::parse($siswa->tanggal_masuk)->diffForHumans() }})
                            </div>
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
                            {{-- Tombol Detail - semua role bisa akses (kecuali orang tua) --}}
                            <a href="{{ route($showRoute, $siswa->id_siswa) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            {{-- Tombol Edit - hanya admin dan petugas --}}
                            @if($isAdmin || $isPetugas)
                            <a href="{{ route($editRoute, $siswa->id_siswa) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit{{ $isPetugas ? ' (Terbatas)' : '' }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                           
                            {{-- Tombol Hapus - hanya admin --}}
                            @if($isAdmin)
                                <form action="{{ route('siswa.destroy', $siswa->id_siswa) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-500 hover:bg-red-600 rounded-md p-2 transition-colors duration-200" title="Hapus" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?\n\nTindakan ini akan menghapus:\n- Data siswa\n- Data detail siswa (alokasi)\n- Semua rekam medis terkait\n- Semua data pemeriksaan\n\nData yang dihapus tidak dapat dikembalikan!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
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
                            <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data siswa</h3>
                            <p class="text-gray-400 mb-4">
                                @if(request()->hasAny(['keyword', 'status', 'jenis_kelamin', 'tahun_masuk']))
                                    Tidak ada siswa yang sesuai dengan filter yang diterapkan
                                @else
                                    Belum ada data siswa yang tersedia
                                @endif
                            </p>
                            @if($isAdmin)
                            <div class="flex space-x-3">
                                <a href="{{ route($createRoute) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus-circle mr-2"></i> Tambah Siswa
                                </a>
                            </div>
                            @elseif(request()->hasAny(['keyword', 'status', 'jenis_kelamin', 'tahun_masuk']))
                            <div>
                                <a href="{{ route($indexRoute) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-times-circle mr-2"></i> Hapus Filter
                                </a>
                            </div>
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
                    Menampilkan <span class="font-medium">{{ $siswas->firstItem() ?? 0 }}</span> 
                    sampai <span class="font-medium">{{ $siswas->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $siswas->total() }}</span> data
                    @if($isDokter)
                        <span class="text-green-600">(Akses Dokter)</span>
                    @elseif($isPetugas)
                        <span class="text-yellow-600">(Akses Petugas)</span>
                    @endif
                </p>
            </div>
            @if(isset($siswas) && $siswas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div>
                {{ $siswas->appends(request()->except(['page', 'reset']))->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Filter Data Siswa</h3>
            <button id="closeFilterModal" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route($indexRoute) }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Filter Status -->
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Siswa</label>
                    <select id="status" name="status" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                
                <!-- Filter Jenis Kelamin -->
                <div class="mb-4">
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                
                <!-- Filter Tahun Masuk -->
                <div class="mb-4">
                    <label for="tahun_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk</label>
                    <select id="tahun_masuk" name="tahun_masuk" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tahun</option>
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ request('tahun_masuk') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                
                <!-- Filter Status Alokasi -->
                <div class="mb-4">
                    <label for="status_alokasi" class="block text-sm font-medium text-gray-700 mb-1">Status Alokasi</label>
                    <select id="status_alokasi" name="status_alokasi" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status Alokasi</option>
                        <option value="sudah" {{ request('status_alokasi') == 'sudah' ? 'selected' : '' }}>Sudah Dialokasi</option>
                        <option value="belum" {{ request('status_alokasi') == 'belum' ? 'selected' : '' }}>Belum Dialokasi</option>
                    </select>
                </div>
                
                <!-- Filter Kelas -->
                <div class="mb-4">
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <input type="text" id="kelas" name="kelas" value="{{ request('kelas') }}" 
                           class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: XII TKJ 1">
                </div>
                
                <!-- Filter Jurusan -->
                <div class="mb-4">
                    <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                    <input type="text" id="jurusan" name="jurusan" value="{{ request('jurusan') }}" 
                           class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: Teknik Komputer">
                </div>
                
                <!-- Kata Kunci Pencarian -->
                <div class="mb-4 md:col-span-2">
                    <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                    <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}" 
                           class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Cari berdasarkan ID atau nama siswa...">
                </div>
            </div>
            
            <!-- Tombol Filter -->
            <div class="flex justify-end space-x-2 mt-6">
                @if(request()->anyFilled(['keyword', 'status', 'jenis_kelamin', 'tahun_masuk', 'kelas', 'jurusan', 'status_alokasi']))
                    <a href="{{ route($indexRoute) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Reset
                    </a>
                @endif
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Double check access level - prevent any bypass attempts
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'orang_tua') {
        console.warn('Access violation detected: Parent trying to access student list');
        window.location.href = '{{ route("orangtua.siswa.show") }}';
        return;
    }

    // Clear search functionality
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const searchInput = document.getElementById('searchInput');
    
    if (clearSearchBtn && searchInput) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            document.getElementById('searchForm').submit();
        });
        
        searchInput.addEventListener('input', function() {
            clearSearchBtn.style.display = this.value ? 'block' : 'none';
        });
    }

    // Filter modal functionality
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

    // Real-time search functionality (optional)
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('searchForm').submit();
                return;
            }
            
            // Optional: implement debounced real-time search
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                // Uncomment below for real-time search
                // document.getElementById('searchForm').submit();
            }, 1000);
        });
    }
});
</script>
@endpush

@endif {{-- End of access control for orang_tua --}}
@endsection