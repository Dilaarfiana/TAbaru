@extends('layouts.admin')

@section('page_title', 'Siswa Belum Teralokasi')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Siswa Belum Teralokasi</h2>
            <a href="{{ route('alokasi.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Alokasi
            </a>
        </div>
        
        <!-- Filter Section -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg shadow-sm">
            <form action="{{ route('alokasi.unallocated') }}" method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan Target</label>
                    <select name="jurusan" id="jurusan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->Kode_Jurusan }}" {{ request('jurusan') == $jurusan->Kode_Jurusan ? 'selected' : '' }}>
                                {{ $jurusan->Nama_Jurusan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas Target</label>
                    <select name="kelas" id="kelas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Kelas</option>
                        @foreach($kelass as $kelas)
                            <option value="{{ $kelas->Kode_Kelas }}" {{ request('kelas') == $kelas->Kode_Kelas ? 'selected' : '' }} data-jurusan="{{ $kelas->Kode_Jurusan }}">
                                {{ $kelas->Nama_Kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <input type="text" name="search" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md border-gray-300" placeholder="Cari nama atau ID" value="{{ request('search') }}">
                        <button type="submit" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Alokasi Massal -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-medium text-gray-900 mb-4"><i class="fas fa-users-cog mr-2"></i> Alokasi Massal</h3>
            <form action="{{ route('alokasi.mass') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="kode_jurusan_mass" class="block text-sm font-medium text-gray-700">Jurusan</label>
                        <select name="kode_jurusan" id="kode_jurusan_mass" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Pilih Jurusan</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->Kode_Jurusan }}">{{ $jurusan->Nama_Jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="kode_kelas_mass" class="block text-sm font-medium text-gray-700">Kelas</label>
                        <select name="kode_kelas" id="kode_kelas_mass" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelass as $kelas)
                                <option value="{{ $kelas->Kode_Kelas }}" data-jurusan="{{ $kelas->Kode_Jurusan }}">{{ $kelas->Nama_Kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-save mr-2"></i>Alokasikan Siswa Terpilih
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Siswa -->
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <form id="mass-allocation-form" action="{{ route('alokasi.mass') }}" method="POST">
                @csrf
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="check-all" class="rounded text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID Siswa
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama Siswa
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                L/P
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Detail ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($siswas as $siswa)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_siswa[]" value="{{ $siswa->id_siswa }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $siswa->id_siswa }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $siswa->nama_siswa }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $siswa->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                        {{ $siswa->jenis_kelamin }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $siswa->detailSiswa->id_detsiswa ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" 
                                            class="btn-alokasi text-indigo-600 hover:text-indigo-900"
                                            data-id="{{ $siswa->id_siswa }}" 
                                            data-nama="{{ $siswa->nama_siswa }}"
                                            data-jurusan="{{ $siswa->detailSiswa->kode_jurusan ?? '' }}"
                                            data-kelas="{{ $siswa->detailSiswa->kode_kelas ?? '' }}">
                                        <i class="fas fa-user-cog"></i> Alokasi
                                    </button>
                                    <a href="{{ route('siswa.show', $siswa->id_siswa) }}" class="text-blue-600 hover:text-blue-900 ml-3">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg">Semua siswa sudah teralokasi ke kelas</p>
                                        <a href="{{ route('siswa.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                                            <i class="fas fa-user-plus mr-2"></i> Tambah Siswa Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
            
            <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                {{ $siswas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Alokasi -->
<div id="alokasiModal" class="fixed inset-0 z-10 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="alokasi-form" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                <i class="fas fa-user-cog mr-2"></i>Alokasi Siswa
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="nama_siswa" class="block text-sm font-medium text-gray-700">Nama Siswa</label>
                                    <input type="text" id="nama_siswa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" readonly>
                                </div>
                                <div>
                                    <label for="kode_jurusan" class="block text-sm font-medium text-gray-700">Jurusan</label>
                                    <select name="kode_jurusan" id="kode_jurusan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="">Pilih Jurusan</option>
                                        @foreach($jurusans as $jurusan)
                                            <option value="{{ $jurusan->Kode_Jurusan }}">{{ $jurusan->Nama_Jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="kode_kelas" class="block text-sm font-medium text-gray-700">Kelas</label>
                                    <select name="kode_kelas" id="kode_kelas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelass as $kelas)
                                            <option value="{{ $kelas->Kode_Kelas }}" data-jurusan="{{ $kelas->Kode_Jurusan }}">{{ $kelas->Nama_Kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" id="close-modal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filter change
    document.querySelectorAll('#filter-form select').forEach(el => {
        el.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    });
    
    // Check all checkboxes
    document.getElementById('check-all').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('input[name="selected_siswa[]"]').forEach(checkbox => {
            checkbox.checked = checked;
        });
    });
    
    // Modal functionality
    const modal = document.getElementById('alokasiModal');
    
    // Open modal
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-alokasi')) {
            const id = e.target.dataset.id;
            const nama = e.target.dataset.nama;
            const jurusan = e.target.dataset.jurusan;
            const kelas = e.target.dataset.kelas;
            
            document.getElementById('alokasi-form').action = '{{ url("alokasi") }}/' + id;
            document.getElementById('nama_siswa').value = nama;
            document.getElementById('kode_jurusan').value = jurusan || '';
            
            // Filter kelas berdasarkan jurusan
            filterKelasByJurusan();
            
            // Set nilai kelas jika ada
            if (kelas) {
                document.getElementById('kode_kelas').value = kelas;
            }
            
            modal.classList.remove('hidden');
        }
    });
    
    // Close modal
    document.getElementById('close-modal').addEventListener('click', function() {
        modal.classList.add('hidden');
    });
    
    // Filter kelas berdasarkan jurusan yang dipilih
    document.getElementById('kode_jurusan').addEventListener('change', filterKelasByJurusan);
    document.getElementById('kode_jurusan_mass').addEventListener('change', function() {
        filterKelasByJurusanMass();
    });
    
    function filterKelasByJurusan() {
        const jurusanSelect = document.getElementById('kode_jurusan');
        const kelasSelect = document.getElementById('kode_kelas');
        const selectedJurusan = jurusanSelect.value;
        
        // Reset pilihan kelas
        kelasSelect.value = '';
        
        // Sembunyikan semua option kecuali yang pertama
        Array.from(kelasSelect.options).forEach(option => {
            if (option.value === '') return;
            option.hidden = (option.dataset.jurusan !== selectedJurusan);
        });
    }
    
    function filterKelasByJurusanMass() {
        const jurusanSelect = document.getElementById('kode_jurusan_mass');
        const kelasSelect = document.getElementById('kode_kelas_mass');
        const selectedJurusan = jurusanSelect.value;
        
        // Reset pilihan kelas
        kelasSelect.value = '';
        
        // Sembunyikan semua option kecuali yang pertama
        Array.from(kelasSelect.options).forEach(option => {
            if (option.value === '') return;
            option.hidden = (option.dataset.jurusan !== selectedJurusan);
        });
    }

    // Filter jurusan based on selection in main filter
    document.getElementById('jurusan').addEventListener('change', function() {
        const selectedJurusan = this.value;
        if (selectedJurusan) {
            document.getElementById('kode_jurusan_mass').value = selectedJurusan;
            filterKelasByJurusanMass();
        }
    });
</script>
@endpush