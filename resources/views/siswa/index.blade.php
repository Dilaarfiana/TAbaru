@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section - Putih dengan ikon dan tombol biru -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-graduate text-blue-500 mr-2"></i> Daftar Siswa
        </h5>
        <div class="flex space-x-2">
            <a href="{{ route('alokasi.unallocated') }}" class="bg-indigo-500 text-white hover:bg-indigo-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <i class="fas fa-user-check mr-2"></i> Alokasi Siswa
            </a>
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
                <a href="{{ route('siswa.import') }}" class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center">
                    <i class="fas fa-file-import text-green-500 mr-2"></i> Import
                </a>
                <a href="{{ route('siswa.export') }}" class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none flex items-center">
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
    
    <!-- Informasi Format ID -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-4 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Format ID Siswa</h4>
                <p class="text-xs text-blue-600 mt-1">
                    <strong>Siswa Baru:</strong> 6 + tahun (yy) + nomor urut (001)<br>
                    <strong>Setelah Alokasi:</strong> 6 + kode jurusan + tahun (yy) + nomor urut (001)
                </p>
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
            <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                @forelse($siswas as $siswa)
                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                            {{ $siswa->id_siswa }}
                        </span>
                        @php
                            $isAllocated = false;
                            $isFormatNew = false;
                            $idNeedsUpdate = false;

                            // Cek apakah format ID baru (diawali dengan "6")
                            if (substr($siswa->id_siswa, 0, 1) == '6') {
                                $isFormatNew = true;
                                
                                // Cek apakah sudah dialokasikan
                                if ($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan) {
                                    $isAllocated = true;
                                    
                                    // Cek apakah ID perlu diperbarui (tidak mengandung kode jurusan)
                                    $kodeJurusan = $siswa->detailSiswa->kode_jurusan;
                                    if (strlen($siswa->id_siswa) > 6) {
                                        $idContainsJurusan = (substr($siswa->id_siswa, 1, strlen($kodeJurusan)) === $kodeJurusan);
                                        if (!$idContainsJurusan) {
                                            $idNeedsUpdate = true;
                                        }
                                    } else {
                                        $idNeedsUpdate = true;
                                    }
                                }
                            }
                        @endphp
                        
                        @if($isFormatNew && !$isAllocated)
                            <div class="text-xs text-orange-600 mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Belum Dialokasi
                            </div>
                        @elseif($isFormatNew && $isAllocated && $idNeedsUpdate)
                            <div class="text-xs text-orange-600 mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Perlu Update ID
                            </div>
                        @elseif($isFormatNew && $isAllocated && !$idNeedsUpdate)
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
                        @php
                            $namaJurusan = null;
                            $kodeJurusan = null;
                            // Coba mendapatkan jurusan melalui kelas
                            if($siswa->detailSiswa && $siswa->detailSiswa->kelas && $siswa->detailSiswa->kelas->jurusan) {
                                $namaJurusan = $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan;
                                $kodeJurusan = $siswa->detailSiswa->kelas->jurusan->Kode_Jurusan;
                            }
                            // Jika tidak berhasil, coba mendapatkan jurusan langsung dari DetailSiswa
                            elseif($siswa->detailSiswa && $siswa->detailSiswa->jurusan) {
                                $namaJurusan = $siswa->detailSiswa->jurusan->Nama_Jurusan;
                                $kodeJurusan = $siswa->detailSiswa->jurusan->Kode_Jurusan;
                            }
                            // Jika masih tidak berhasil, coba ambil dari tabel jurusan berdasarkan kode_jurusan
                            elseif($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan) {
                                $jurusan = \App\Models\Jurusan::where('Kode_Jurusan', $siswa->detailSiswa->kode_jurusan)->first();
                                if($jurusan) {
                                    $namaJurusan = $jurusan->Nama_Jurusan;
                                    $kodeJurusan = $jurusan->Kode_Jurusan;
                                }
                            }
                            
                            // Deteksi format ID apakah sudah sesuai dengan jurusan
                            $idSesuaiJurusan = false;
                            if($kodeJurusan && strlen($siswa->id_siswa) > 7 && substr($siswa->id_siswa, 0, 1) == '6') {
                                // Periksa apakah ID siswa mengandung kode jurusan (pada posisi ke-2 setelah kode sekolah)
                                $idSesuaiJurusan = (substr($siswa->id_siswa, 1, strlen($kodeJurusan)) === $kodeJurusan);
                            }
                        @endphp
                        
                        @if($namaJurusan)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-graduation-cap mr-1"></i> {{ $namaJurusan }} ({{ $kodeJurusan }})
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-graduation-cap mr-1"></i> Belum ada jurusan
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @php
                            $namaKelas = null;
                            $tingkatKelas = null;
                            $tahunAjaran = null;
                            // Coba mendapatkan kelas
                            if($siswa->detailSiswa && $siswa->detailSiswa->kelas) {
                                $namaKelas = $siswa->detailSiswa->kelas->Nama_Kelas;
                                $tingkatKelas = $siswa->detailSiswa->kelas->tingkat;
                                $tahunAjaran = $siswa->detailSiswa->kelas->Tahun_Ajaran;
                            }
                            // Jika tidak berhasil, coba ambil dari tabel kelas berdasarkan kode_kelas
                            elseif($siswa->detailSiswa && $siswa->detailSiswa->kode_kelas) {
                                $kelas = \App\Models\Kelas::where('Kode_Kelas', $siswa->detailSiswa->kode_kelas)->first();
                                if($kelas) {
                                    $namaKelas = $kelas->Nama_Kelas;
                                    $tingkatKelas = $kelas->tingkat;
                                    $tahunAjaran = $kelas->Tahun_Ajaran;
                                }
                            }
                        @endphp
                        
                        @if($namaKelas)
                            <div class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800 inline-block">
                                <div class="flex flex-col items-center">
                                    <div>
                                        <i class="fas fa-chalkboard mr-1"></i> {{ $namaKelas }}
                                    </div>
                                    @if($tahunAjaran)
                                        <div class="text-xs text-indigo-600 mt-0.5 text-center">
                                            {{ $tahunAjaran }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-1">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-chalkboard mr-1"></i> Belum ada kelas
                                </span>
                                <button type="button" onclick="openAlokasiModal('{{ $siswa->id_siswa }}', '{{ $siswa->nama_siswa }}')" class="text-xs text-indigo-600 hover:text-indigo-800" title="Alokasi ke kelas">
                                    <i class="fas fa-user-check"></i>
                                </button>
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
                            <a href="{{ route('siswa.show', $siswa->id_siswa) }}" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('siswa.edit', $siswa->id_siswa) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 rounded-md p-2 transition-colors duration-200" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" id="alokasi-{{ $siswa->id_siswa }}" onclick="openAlokasiModal('{{ $siswa->id_siswa }}', '{{ $siswa->nama_siswa }}')" class="text-white bg-indigo-500 hover:bg-indigo-600 rounded-md p-2 transition-colors duration-200" title="Alokasi Kelas">
                                <i class="fas fa-user-check"></i>
                            </button>
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

<!-- Alokasi Siswa Modal -->
<div id="alokasiModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="alokasiModalOverlay"></div>
        
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg max-w-md w-full mx-auto shadow-xl z-20">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-lg">
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="alokasiModalTitle">
                        Alokasi Siswa ke Kelas
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeAlokasiModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Informasi Format ID -->
                <div class="mb-4 bg-yellow-50 p-3 rounded-md">
                    <h4 class="text-sm font-medium text-yellow-800">Perubahan ID Siswa</h4>
                    <p class="text-xs text-yellow-700 mt-1">
                        Setelah alokasi, ID siswa akan berubah menjadi:<br>
                        <strong>6 + kode_jurusan + tahun(yy) + nomor_urut(001)</strong>
                    </p>
                </div>
                
                <div class="py-2">
                    <div class="text-sm text-gray-500 mb-3">
                        <p>Data jurusan tersedia: <span id="jurusanCount" class="font-medium">0 jurusan</span></p>
                        <p>Data kelas tersedia: <span id="kelasCount" class="font-medium">0 kelas</span></p>
                    </div>
                    
                    <p class="text-sm text-gray-700 mb-2">
                        Anda akan mengalokasikan siswa:
                    </p>
                    <p class="font-medium text-gray-900 mb-4" id="namaSiswaAlokasi"></p>
                    <p class="text-xs text-gray-500 mb-1" id="idSiswaAlokasi"></p>
                </div>
                
                <form id="alokasiForm" action="{{ route('siswa.alokasi') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="id_siswa" id="inputIdSiswa">
                    
                    <div class="mb-4">
                        <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jurusan <span class="text-red-500">*</span>
                        </label>
                        <select id="jurusan" name="kode_jurusan" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500" onchange="loadKelas()" required>
                            <option value="">Pilih Jurusan</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">
                            Kelas <span class="text-red-500">*</span>
                        </label>
                        <select id="kelas" name="kode_kelas" class="block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Kelas akan tersedia setelah memilih jurusan</option>
                        </select>
                    </div>
                    
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Alokasikan Siswa
                        </button>
                        <button type="button" onclick="closeAlokasiModal()" class="mt-3 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
    
    // Auto close alerts after 5 seconds
    const alerts = document.querySelectorAll('.close-alert').forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });
    
    // Load jurusan data when page loads for modal
    loadJurusan();
    
    // Check URL hash for alokasi-ID pattern
    const hash = window.location.hash;
    if (hash && hash.startsWith('#alokasi-')) {
        const siswaId = hash.substring(9); // Extract ID after #alokasi-
        const siswaElement = document.getElementById('alokasi-' + siswaId);
        if (siswaElement) {
            // Find the row containing this button
            const row = siswaElement.closest('tr');
            if (row) {
                const namaSiswa = row.querySelector('td:nth-child(2)').textContent.trim();
                // Trigger click on the alokasi button
                setTimeout(() => {
                    openAlokasiModal(siswaId, namaSiswa);
                    // Scroll to the element and highlight it
                    siswaElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    row.classList.add('bg-yellow-50');
                    setTimeout(() => {
                        row.classList.remove('bg-yellow-50');
                    }, 3000);
                }, 500);
            }
        }
    }
});

// Function to open alokasi modal
function openAlokasiModal(id_siswa, nama_siswa) {
    document.getElementById('inputIdSiswa').value = id_siswa;
    document.getElementById('namaSiswaAlokasi').textContent = nama_siswa;
    document.getElementById('idSiswaAlokasi').textContent = 'ID saat ini: ' + id_siswa;
    document.getElementById('alokasiModal').classList.remove('hidden');
    
    // Load jurusan data
    loadJurusan();
    
    // Reset form selections
    document.getElementById('jurusan').selectedIndex = 0;
    document.getElementById('kelas').innerHTML = '<option value="">Kelas akan tersedia setelah memilih jurusan</option>';
}

// Function to close alokasi modal
function closeAlokasiModal() {
    document.getElementById('alokasiModal').classList.add('hidden');
}

// Function to load jurusan data
function loadJurusan() {
    // Set loading indicator
    document.getElementById('jurusanCount').textContent = 'Memuat...';
    
    fetch('{{ route("api.jurusan") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const jurusanSelect = document.getElementById('jurusan');
            jurusanSelect.innerHTML = '<option value="">Pilih Jurusan</option>';
            
            // Check if data is valid array
            if (!data || !Array.isArray(data)) {
                console.error("Respon jurusan tidak valid:", data);
                document.getElementById('jurusanCount').textContent = '0 jurusan';
                return;
            }
            
            data.forEach(jurusan => {
                const option = document.createElement('option');
                option.value = jurusan.kode_jurusan || jurusan.Kode_Jurusan;
                option.textContent = (jurusan.nama_jurusan || jurusan.Nama_Jurusan) + ' (' + (jurusan.kode_jurusan || jurusan.Kode_Jurusan) + ')';
                jurusanSelect.appendChild(option);
            });
            
            // Update jurusan count
            document.getElementById('jurusanCount').textContent = data.length + ' jurusan';
        })
        .catch(error => {
            console.error('Error loading jurusan:', error);
            document.getElementById('jurusanCount').textContent = '0 jurusan';
            
            // Add diagnostic info to console
            console.log('API endpoint called:', '{{ route("api.jurusan") }}');
        });
}

// Function to load kelas data based on selected jurusan
function loadKelas() {
    // Set loading indicator
    document.getElementById('kelasCount').textContent = 'Memuat...';
    
    const jurusanSelect = document.getElementById('jurusan');
    const kelasSelect = document.getElementById('kelas');
    const kodeJurusan = jurusanSelect.value;
    
    if (!kodeJurusan) {
        kelasSelect.innerHTML = '<option value="">Kelas akan tersedia setelah memilih jurusan</option>';
        document.getElementById('kelasCount').textContent = '0 kelas';
        return;
    }
    
    // Show API URL for debugging
    console.log('Calling API:', `{{ route('api.kelas') }}?kode_jurusan=${kodeJurusan}`);
    
    fetch(`{{ route('api.kelas') }}?kode_jurusan=${kodeJurusan}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Log the response for debugging
            console.log('API response:', data);
            
            kelasSelect.innerHTML = '';
            
            // Handle if data is not array or empty
            if (!data || typeof data !== 'object') {
                kelasSelect.innerHTML = '<option value="">Tidak ada kelas tersedia</option>';
                document.getElementById('kelasCount').textContent = '0 kelas';
                return;
            }
            
            // Handle error response
            if (data.error) {
                console.error('API error:', data.error);
                kelasSelect.innerHTML = `<option value="">Error: ${data.error}</option>`;
                document.getElementById('kelasCount').textContent = '0 kelas';
                return;
            }
            
            // Convert data to array if it's not already
            const kelasArray = Array.isArray(data) ? data : Object.values(data);
            
            if (kelasArray.length === 0) {
                kelasSelect.innerHTML = '<option value="">Tidak ada kelas tersedia untuk jurusan ini</option>';
                document.getElementById('kelasCount').textContent = '0 kelas';
                return;
            }
            
            kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
            
            kelasArray.forEach(kelas => {
                if (kelas && typeof kelas === 'object') {
                    const option = document.createElement('option');
                    option.value = kelas.Kode_Kelas || kelas.kode_kelas;
                    const namaKelas = kelas.Nama_Kelas || kelas.nama_kelas;
                    const tahunAjaran = kelas.Tahun_Ajaran || kelas.tahun_ajaran;
                    
                    let displayText = namaKelas || 'Kelas tanpa nama';
                    if (tahunAjaran) {
                        displayText += ` (${tahunAjaran})`;
                    }
                    
                    option.textContent = displayText;
                    kelasSelect.appendChild(option);
                }
            });
            
            // Update kelas count
            document.getElementById('kelasCount').textContent = kelasArray.length + ' kelas';
            
            // Generate preview of new ID
            const idSiswa = document.getElementById('inputIdSiswa').value;
            const tahunSekarang = new Date().getFullYear().toString().substr(-2);
            
            if (idSiswa) {
                let newIdPreview = '6' + kodeJurusan + tahunSekarang + '001*';
                document.getElementById('idSiswaAlokasi').innerHTML = 'ID saat ini: ' + idSiswa + '<br>ID baru: <span class="text-blue-600 font-medium">' + newIdPreview + '</span><br><small class="text-gray-500">*Nomor urut akan dihitung otomatis</small>';
            }
        })
        .catch(error => {
            console.error('Error loading kelas:', error);
            kelasSelect.innerHTML = '<option value="">Error: Gagal memuat data kelas</option>';
            document.getElementById('kelasCount').textContent = '0 kelas';
        });
}

// Add event listener for alokasi modal overlay
document.addEventListener('DOMContentLoaded', function() {
    const alokasiModalOverlay = document.getElementById('alokasiModalOverlay');
    
    if (alokasiModalOverlay) {
        alokasiModalOverlay.addEventListener('click', function(e) {
            if (e.target === alokasiModalOverlay) {
                closeAlokasiModal();
            }
        });
    }
    
    // Add console log for API routes (debugging)
    console.log('Jurusan API URL:', '{{ route("api.jurusan") }}');
    console.log('Kelas API URL template:', '{{ route("api.kelas") }}?kode_jurusan=XXX');
    
    // Validate form before submission
    const alokasiForm = document.getElementById('alokasiForm');
    if (alokasiForm) {
        alokasiForm.addEventListener('submit', function(e) {
            const jurusanSelect = document.getElementById('jurusan');
            const kelasSelect = document.getElementById('kelas');
            
            if (!jurusanSelect.value) {
                e.preventDefault();
                alert('Pilih jurusan terlebih dahulu');
                return false;
            }
            
            if (!kelasSelect.value) {
                e.preventDefault();
                alert('Pilih kelas terlebih dahulu');
                return false;
            }
            
            // Konfirmasi perubahan ID
            const confirmMessage = 'Perhatian: Alokasi ini akan mengubah ID siswa sesuai dengan jurusan yang dipilih. Pastikan semua sistem terkait sudah diperbarui untuk menggunakan ID baru. Lanjutkan?';
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    }
});
</script>
@endpush
@endsection