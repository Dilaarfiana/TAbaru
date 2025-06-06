@extends('layouts.admin')

@section('page_title', 'Detail Siswa - ' . ($detailSiswa->siswa->nama_siswa ?? 'N/A'))

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-center p-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="bg-white bg-opacity-20 rounded-full p-3 mr-4">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">{{ $detailSiswa->siswa->nama_siswa ?? 'N/A' }}</h1>
                    <p class="text-blue-100">ID Siswa: {{ $detailSiswa->id_siswa }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('detailsiswa.edit', $detailSiswa->id_detsiswa) }}" class="bg-white text-blue-600 hover:bg-blue-50 font-medium px-4 py-2 rounded-md transition-colors duration-200 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('detailsiswa.index') }}" class="bg-blue-700 text-white hover:bg-blue-800 font-medium px-4 py-2 rounded-md transition-colors duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-id-card text-blue-500 mr-2"></i>
                        Informasi Detail Siswa
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ID Detail Siswa -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-500">ID Detail Siswa</label>
                            <div class="flex items-center">
                                <span class="px-3 py-2 bg-blue-100 text-blue-800 text-sm font-medium rounded-lg">
                                    {{ $detailSiswa->id_detsiswa }}
                                </span>
                            </div>
                        </div>

                        <!-- ID Siswa -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-500">ID Siswa</label>
                            <div class="flex items-center">
                                <span class="px-3 py-2 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-lg">
                                    {{ $detailSiswa->id_siswa }}
                                </span>
                            </div>
                        </div>

                        <!-- Kode Jurusan -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-500">Kode Jurusan</label>
                            <div class="flex items-center">
                                @if($detailSiswa->kode_jurusan)
                                    <span class="px-3 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-lg">
                                        {{ $detailSiswa->kode_jurusan }}
                                    </span>
                                @else
                                    <span class="px-3 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg">
                                        Belum Ditentukan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Kode Kelas -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-500">Kode Kelas</label>
                            <div class="flex items-center">
                                @if($detailSiswa->kode_kelas)
                                    <span class="px-3 py-2 bg-purple-100 text-purple-800 text-sm font-medium rounded-lg">
                                        {{ $detailSiswa->kode_kelas }}
                                    </span>
                                @else
                                    <span class="px-3 py-2 bg-gray-100 text-gray-500 text-sm font-medium rounded-lg">
                                        Belum Ditentukan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Nama Jurusan -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-500">Nama Jurusan</label>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-gray-800 font-medium">
                                    {{ $detailSiswa->jurusan->Nama_Jurusan ?? 'Belum Dialokasikan' }}
                                </p>
                            </div>
                        </div>

                        <!-- Nama Kelas -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-500">Nama Kelas</label>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-gray-800 font-medium">
                                    {{ $detailSiswa->kelas->Nama_Kelas ?? 'Belum Dialokasikan' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Alokasi -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-medium text-gray-800">Status Alokasi</h4>
                                <p class="text-sm text-gray-500 mt-1">Status alokasi siswa ke kelas dan jurusan</p>
                            </div>
                            <div class="text-right">
                                @if($detailSiswa->kode_kelas && $detailSiswa->kode_jurusan)
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Sudah Teralokasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        Belum Teralokasi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="space-y-6">
            <!-- Data Siswa Card -->
            @if($detailSiswa->siswa)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-user text-green-500 mr-2"></i>
                        Data Siswa
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                        <p class="text-gray-800 font-medium mt-1">{{ $detailSiswa->siswa->nama_siswa }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat, Tanggal Lahir</label>
                        <p class="text-gray-800 mt-1">
                            {{ $detailSiswa->siswa->tempat_lahir ?? '-' }}, 
                            {{ $detailSiswa->siswa->tanggal_lahir ? \Carbon\Carbon::parse($detailSiswa->siswa->tanggal_lahir)->format('d F Y') : '-' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</label>
                        <div class="mt-1">
                            @if($detailSiswa->siswa->jenis_kelamin == 'L')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-male mr-1"></i> Laki-laki
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    <i class="fas fa-female mr-1"></i> Perempuan
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Masuk</label>
                        <p class="text-gray-800 mt-1">
                            {{ $detailSiswa->siswa->tanggal_masuk ? \Carbon\Carbon::parse($detailSiswa->siswa->tanggal_masuk)->format('d F Y') : '-' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status Aktif</label>
                        <div class="mt-1">
                            @if($detailSiswa->siswa->status_aktif)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t">
                        <a href="{{ route('siswa.show', $detailSiswa->id_siswa) }}" class="w-full bg-blue-500 text-white text-center py-2 px-4 rounded-md hover:bg-blue-600 transition-colors duration-200 inline-flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail Lengkap
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Data Jurusan Card -->
            @if($detailSiswa->jurusan)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-book text-purple-500 mr-2"></i>
                        Data Jurusan
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Jurusan</label>
                        <p class="text-gray-800 font-medium mt-1">{{ $detailSiswa->jurusan->Kode_Jurusan }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jurusan</label>
                        <p class="text-gray-800 font-medium mt-1">{{ $detailSiswa->jurusan->Nama_Jurusan }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Data Kelas Card -->
            @if($detailSiswa->kelas)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chalkboard text-orange-500 mr-2"></i>
                        Data Kelas
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Kelas</label>
                        <p class="text-gray-800 font-medium mt-1">{{ $detailSiswa->kelas->Kode_Kelas }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</label>
                        <p class="text-gray-800 font-medium mt-1">{{ $detailSiswa->kelas->Nama_Kelas }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</label>
                        <p class="text-gray-800 mt-1">{{ $detailSiswa->kelas->tahun_ajaran ?? '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Siswa</label>
                        <p class="text-gray-800 mt-1">{{ $detailSiswa->kelas->jumlah_siswa ?? '0' }} siswa</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-cogs text-gray-500 mr-2"></i>
                        Aksi Cepat
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('detailsiswa.edit', $detailSiswa->id_detsiswa) }}" class="w-full bg-blue-500 text-white text-center py-2 px-4 rounded-md hover:bg-blue-600 transition-colors duration-200 inline-flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Detail Siswa
                    </a>
                    
                    @if(Route::has('alokasi.index'))
                    <a href="{{ route('alokasi.index') }}?search={{ $detailSiswa->id_siswa }}" class="w-full bg-purple-500 text-white text-center py-2 px-4 rounded-md hover:bg-purple-600 transition-colors duration-200 inline-flex items-center justify-center">
                        <i class="fas fa-user-cog mr-2"></i>
                        Kelola Alokasi
                    </a>
                    @endif
                    
                    <form action="{{ route('detailsiswa.destroy', $detailSiswa->id_detsiswa) }}" method="POST" class="w-full" onsubmit="return confirmDelete(this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 text-white text-center py-2 px-4 rounded-md hover:bg-red-600 transition-colors duration-200 inline-flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Detail Siswa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Timestamps Card -->
    <div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-clock text-gray-500 mr-2"></i>
                Informasi Timestamp
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Dibuat Pada</label>
                    <p class="text-gray-800 mt-1">
                        @if($detailSiswa->created_at)
                            {{ \Carbon\Carbon::parse($detailSiswa->created_at)->format('d F Y, H:i:s') }}
                            <span class="text-gray-500 text-sm">({{ \Carbon\Carbon::parse($detailSiswa->created_at)->diffForHumans() }})</span>
                        @else
                            -
                        @endif
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Diperbarui Pada</label>
                    <p class="text-gray-800 mt-1">
                        @if($detailSiswa->updated_at)
                            {{ \Carbon\Carbon::parse($detailSiswa->updated_at)->format('d F Y, H:i:s') }}
                            <span class="text-gray-500 text-sm">({{ \Carbon\Carbon::parse($detailSiswa->updated_at)->diffForHumans() }})</span>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
    
    // Function untuk konfirmasi delete
    function confirmDelete(form) {
        const confirmation = confirm('Apakah Anda yakin ingin menghapus data detail siswa ini?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.');
        
        if (confirmation) {
            // Tampilkan loading state
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menghapus...';
            button.disabled = true;
            
            // Jika user membatalkan, reset button
            setTimeout(() => {
                if (!form.submitted) {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            }, 100);
            
            form.submitted = true;
            return true;
        }
        
        return false;
    }
</script>
@endpush
@endsection