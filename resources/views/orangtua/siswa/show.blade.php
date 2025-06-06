@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-6">
        <a href="{{ route('dashboard.orangtua') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="breadcrumb-separator">/</span>
        <span class="text-gray-500">Data Siswa Saya</span>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="page-title">Data Siswa Saya</h1>
        <p class="page-subtitle">Informasi lengkap data siswa dan keluarga</p>
    </div>

    <!-- Data Siswa Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Foto dan Info Utama -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Foto Siswa -->
                <div class="text-center mb-6">
                    <div class="w-32 h-32 mx-auto bg-gray-200 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user text-4xl text-gray-400"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $siswa->nama_siswa }}</h2>
                    <p class="text-gray-600">{{ $siswa->id_siswa }}</p>
                    
                    <!-- Status Badge -->
                    <div class="mt-3">
                        @if($siswa->status_aktif)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="space-y-3">
                    <a href="{{ route('orangtua.siswa.edit') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Data Siswa
                    </a>
                    
                    <a href="{{ route('orangtua.laporan.screening') }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center">
                        <i class="fas fa-file-medical mr-2"></i>
                        Lihat Laporan Kesehatan
                    </a>
                    
                    <a href="{{ route('orangtua.laporan.harian') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center">
                        <i class="fas fa-calendar-check mr-2"></i>
                        Pemeriksaan Harian
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Informasi -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Data Pribadi Siswa
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium">{{ $siswa->nama_siswa ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID Siswa</label>
                            <p class="text-gray-900 font-medium">{{ $siswa->id_siswa ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <p class="text-gray-900">{{ $siswa->tempat_lahir ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <p class="text-gray-900">
                                @if($siswa->tanggal_lahir)
                                    {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                    <span class="text-sm text-gray-500">
                                        ({{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->age }} tahun)
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <p class="text-gray-900">
                                @if($siswa->jenis_kelamin === 'L')
                                    <i class="fas fa-mars text-blue-500 mr-1"></i> Laki-laki
                                @elseif($siswa->jenis_kelamin === 'P')
                                    <i class="fas fa-venus text-pink-500 mr-1"></i> Perempuan
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                            <p class="text-gray-900">
                                @if($siswa->tanggal_masuk)
                                    {{ \Carbon\Carbon::parse($siswa->tanggal_masuk)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Akademik -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap mr-2 text-green-600"></i>
                        Data Akademik
                    </h3>
                </div>
                <div class="p-6">
                    @if($siswa->detailSiswa)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                <p class="text-gray-900 font-medium">
                                    @if($siswa->detailSiswa->kelas)
                                        {{ $siswa->detailSiswa->kelas->Nama_Kelas }}
                                        @if($siswa->detailSiswa->kelas->Tahun_Ajaran)
                                            <span class="text-sm text-gray-500">({{ $siswa->detailSiswa->kelas->Tahun_Ajaran }})</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                <p class="text-gray-900">
                                    @if($siswa->detailSiswa->kelas && $siswa->detailSiswa->kelas->jurusan)
                                        {{ $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kelas</label>
                                <p class="text-gray-900">{{ $siswa->detailSiswa->kode_kelas ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Jurusan</label>
                                <p class="text-gray-900">{{ $siswa->detailSiswa->kode_jurusan ?? '-' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500">Data akademik belum tersedia</p>
                            <p class="text-sm text-gray-400">Hubungi administrator untuk melengkapi data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Orang Tua -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-users mr-2 text-purple-600"></i>
                Data Orang Tua / Wali
            </h3>
        </div>
        <div class="p-6">
            @if($siswa->orangTua)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Data Ayah -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-male mr-2 text-blue-600"></i>
                            Data Ayah
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                                <p class="text-gray-900">{{ $siswa->orangTua->nama_ayah ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <p class="text-gray-900">
                                    @if($siswa->orangTua->tanggal_lahir_ayah)
                                        {{ \Carbon\Carbon::parse($siswa->orangTua->tanggal_lahir_ayah)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                        <span class="text-sm text-gray-500">
                                            ({{ \Carbon\Carbon::parse($siswa->orangTua->tanggal_lahir_ayah)->age }} tahun)
                                        </span>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                <p class="text-gray-900">{{ $siswa->orangTua->pekerjaan_ayah ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                                <p class="text-gray-900">{{ $siswa->orangTua->pendidikan_ayah ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Data Ibu -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-female mr-2 text-pink-600"></i>
                            Data Ibu
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                                <p class="text-gray-900">{{ $siswa->orangTua->nama_ibu ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <p class="text-gray-900">
                                    @if($siswa->orangTua->tanggal_lahir_ibu)
                                        {{ \Carbon\Carbon::parse($siswa->orangTua->tanggal_lahir_ibu)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                        <span class="text-sm text-gray-500">
                                            ({{ \Carbon\Carbon::parse($siswa->orangTua->tanggal_lahir_ibu)->age }} tahun)
                                        </span>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                <p class="text-gray-900">{{ $siswa->orangTua->pekerjaan_ibu ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                                <p class="text-gray-900">{{ $siswa->orangTua->pendidikan_ibu ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kontak dan Alamat -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-address-card mr-2 text-indigo-600"></i>
                        Informasi Kontak
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <p class="text-gray-900">{{ $siswa->orangTua->alamat ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <p class="text-gray-900">
                                @if($siswa->orangTua->no_telp)
                                    <a href="tel:{{ $siswa->orangTua->no_telp }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                                        <i class="fas fa-phone mr-1"></i>
                                        {{ $siswa->orangTua->no_telp }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 mb-2">Data orang tua belum tersedia</p>
                    <p class="text-sm text-gray-400">Hubungi administrator sekolah untuk melengkapi data orang tua</p>
                </div>
            @endif
        </div>
    </div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll behavior for quick links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // Add loading state for action buttons
    document.querySelectorAll('a[href*="laporan"]').forEach(link => {
        link.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spin');
            }
        });
    });
});
</script>
@endpush
@endsection