{{-- File: resources/views/pemeriksaan_harian/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: NO ACCESS, ORANG TUA: REDIRECT --}}
@extends('layouts.app')

@section('page_title', 'Detail Pemeriksaan Harian')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Redirect orang tua ke halaman khusus mereka
    if ($isOrangTua) {
        header('Location: ' . route('orangtua.riwayat.pemeriksaan_harian'));
        exit;
    }
    
    // Redirect dokter ke dashboard karena tidak ada akses
    if ($isDokter) {
        header('Location: ' . route('dashboard.dokter'));
        exit;
    }
    
    // Check if user has permission to view
    if (!in_array($userLevel, ['admin', 'petugas'])) {
        header('Location: ' . route('dashboard'));
        exit;
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'pemeriksaan_harian' : 'petugas.pemeriksaan_harian';
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';
    $editRoute = $baseRoute . '.edit';
    $createRoute = $baseRoute . '.create';
@endphp

<div class="py-3">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
            <!-- Header -->
            <div class="border-b border-gray-200 bg-white px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <i class="fas fa-eye text-blue-500 mr-3 text-xl"></i>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Detail Pemeriksaan Harian</h2>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-bold rounded-full">
                                    <i class="fas fa-tag mr-1"></i>
                                    ID: {{ $pemeriksaanHarian->Id_Harian }}
                                </span>
                                @if($isPetugas)
                                    <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                        <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                                    </span>
                                @elseif($isAdmin)
                                    <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                        <i class="fas fa-user-shield mr-1"></i>Akses Admin
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-2 mt-4 md:mt-0">
                    @if($isAdmin || $isPetugas)
                    <a href="{{ route($editRoute, $pemeriksaanHarian->Id_Harian) }}" class="px-3 py-2 text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-edit mr-1"></i> Edit Data
                    </a>
                    @endif
                    
                    @if($isAdmin)
                    <form action="{{ route('pemeriksaan_harian.destroy', $pemeriksaanHarian->Id_Harian) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pemeriksaan harian ini?\n\nID: {{ $pemeriksaanHarian->Id_Harian }}\nSiswa: {{ $pemeriksaanHarian->siswa->nama_siswa ?? 'N/A' }}\n\nTindakan ini akan menghapus semua data terkait dan tidak dapat dikembalikan!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                    @endif
                    
                    <a href="{{ route($indexRoute) }}" class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <div class="px-6">
                @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-4 flex items-center justify-between">
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
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-4 flex items-center justify-between">
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

                @if(session('warning'))
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mt-4 flex items-center justify-between">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">{!! session('warning') !!}</p>
                        </div>
                    </div>
                    <button type="button" class="close-alert text-yellow-500 hover:text-yellow-600" onclick="this.parentElement.style.display='none'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif

                @if(session('info'))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4 flex items-center justify-between">
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
            </div>

            <!-- Info Access Level -->
            @if($isPetugas)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-6 mt-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Anda mengakses detail pemeriksaan harian dengan <strong>Akses Petugas</strong>. 
                            Anda dapat melihat dan mengedit data pemeriksaan, namun tidak dapat menghapus data.
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Tanggal dan Info -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-gray-700">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                <span class="text-sm font-medium">
                                    Tanggal Pemeriksaan: 
                                    <strong class="text-blue-700">
                                        @if($pemeriksaanHarian->Tanggal_Jam)
                                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('d F Y, H:i') }} WIB
                                        @else
                                            Tidak tersedia
                                        @endif
                                    </strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center mt-2 md:mt-0">
                        <div class="text-gray-600">
                            <div class="flex items-center text-xs">
                                <i class="fas fa-clock text-gray-500 mr-1"></i>
                                <span>
                                    @php
                                        $createdAt = $pemeriksaanHarian->created_at ?? $pemeriksaanHarian->dibuat_pada ?? null;
                                        $updatedAt = $pemeriksaanHarian->updated_at ?? $pemeriksaanHarian->diperbarui_pada ?? null;
                                    @endphp
                                    @if($createdAt)
                                        Dibuat: {{ \Carbon\Carbon::parse($createdAt)->format('d/m/Y H:i') }}
                                        @if($updatedAt && $createdAt != $updatedAt)
                                        | Diperbarui: {{ \Carbon\Carbon::parse($updatedAt)->format('d/m/Y H:i') }}
                                        @endif
                                    @else
                                        Tanggal tidak tersedia
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Access Information -->
                <div class="mt-3 p-3 bg-blue-100 border border-blue-300 rounded-md">
                    <div class="flex items-center text-sm text-blue-800">
                        <i class="fas fa-user-tag text-blue-600 mr-2"></i>
                        <div>
                            <strong>Akses Anda:</strong> 
                            @if($isAdmin)
                                Administrator - Dapat melihat, mengedit, dan menghapus data pemeriksaan harian
                            @elseif($isPetugas)
                                Petugas UKS - Dapat melihat dan mengedit data pemeriksaan harian
                            @else
                                Guest - Hanya dapat melihat data pemeriksaan harian
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <!-- Kolom Kiri - Info Siswa -->
                <div class="lg:col-span-1">
                    <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                            <i class="fas fa-user-graduate text-green-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-900">Informasi Siswa</h3>
                        </div>
                        
                        <div class="text-center mb-4">
                            <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3 border-2 border-green-200">
                                <i class="fas fa-user text-2xl text-green-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-1">
                                {{ $pemeriksaanHarian->siswa->nama_siswa ?? 'Data tidak tersedia' }}
                            </h4>
                            <p class="text-gray-500 text-sm flex items-center justify-center">
                                <i class="fas fa-id-card mr-1"></i>
                                ID: {{ $pemeriksaanHarian->Id_Siswa }}
                            </p>
                        </div>
                        
                        @if($pemeriksaanHarian->siswa)
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-green-200">
                                <span class="text-gray-600 text-sm flex items-center">
                                    <i class="fas fa-venus-mars text-gray-500 mr-1 text-xs"></i>
                                    Jenis Kelamin
                                </span>
                                <span class="font-medium text-sm">
                                    @if($pemeriksaanHarian->siswa->jenis_kelamin == 'L')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                            <i class="fas fa-male mr-1"></i> Laki-laki
                                        </span>
                                    @elseif($pemeriksaanHarian->siswa->jenis_kelamin == 'P')
                                        <span class="px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-xs">
                                            <i class="fas fa-female mr-1"></i> Perempuan
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </span>
                            </div>
                            
                            @if($pemeriksaanHarian->siswa->tanggal_lahir)
                            <div class="flex justify-between py-2 border-b border-green-200">
                                <span class="text-gray-600 text-sm flex items-center">
                                    <i class="fas fa-birthday-cake text-gray-500 mr-1 text-xs"></i>
                                    Tanggal Lahir
                                </span>
                                <span class="font-medium text-sm">
                                    {{ \Carbon\Carbon::parse($pemeriksaanHarian->siswa->tanggal_lahir)->format('d F Y') }}
                                </span>
                            </div>
                            @endif
                            
                            @if($pemeriksaanHarian->siswa->tempat_lahir)
                            <div class="flex justify-between py-2 border-b border-green-200">
                                <span class="text-gray-600 text-sm flex items-center">
                                    <i class="fas fa-map-marker-alt text-gray-500 mr-1 text-xs"></i>
                                    Tempat Lahir
                                </span>
                                <span class="font-medium text-sm">{{ $pemeriksaanHarian->siswa->tempat_lahir }}</span>
                            </div>
                            @endif
                            
                            <div class="flex justify-between py-2 border-b border-green-200">
                                <span class="text-gray-600 text-sm flex items-center">
                                    <i class="fas fa-user-check text-gray-500 mr-1 text-xs"></i>
                                    Status
                                </span>
                                <span class="font-medium text-sm">
                                    @if($pemeriksaanHarian->siswa->status_aktif == 1)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                                        </span>
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 text-sm flex items-center">
                                    <i class="fas fa-calendar-plus text-gray-500 mr-1 text-xs"></i>
                                    Tanggal Masuk
                                </span>
                                <span class="font-medium text-sm">
                                    @if($pemeriksaanHarian->siswa->tanggal_masuk)
                                        {{ \Carbon\Carbon::parse($pemeriksaanHarian->siswa->tanggal_masuk)->format('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-times text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">Data siswa tidak tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Kolom Tengah - Hasil Pemeriksaan -->
                <div class="lg:col-span-2">
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-5 shadow-sm mb-6">
                        <div class="flex items-center mb-4 border-b border-orange-200 pb-2">
                            <i class="fas fa-clipboard-check text-orange-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-900">Hasil Pemeriksaan</h3>
                        </div>
                        
                        <div class="bg-white p-4 rounded-lg border border-orange-200 mb-5 shadow-sm">
                            <div class="flex items-start">
                                <i class="fas fa-notes-medical text-orange-500 mr-3 mt-1"></i>
                                <div class="flex-1">
                                    @if($pemeriksaanHarian->Hasil_Pemeriksaan)
                                        <p class="whitespace-pre-line text-gray-800 leading-relaxed">{{ $pemeriksaanHarian->Hasil_Pemeriksaan }}</p>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-clipboard text-gray-400 text-2xl mb-2"></i>
                                            <p class="text-gray-500 text-sm">Hasil pemeriksaan belum diisi</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Petugas UKS -->
                        <div class="bg-white border border-orange-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-center mb-3 border-b border-gray-200 pb-2">
                                <i class="fas fa-user-nurse text-purple-600 mr-2"></i>
                                <h4 class="text-base font-medium text-gray-900">Petugas UKS yang Memeriksa</h4>
                            </div>
                            
                            @if($pemeriksaanHarian->petugasUKS)
                            <div class="flex items-center">
                                <div class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-md text-purple-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h5 class="text-sm font-medium text-gray-900">{{ $pemeriksaanHarian->petugasUKS->nama_petugas_uks }}</h5>
                                    <p class="text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-id-badge mr-1"></i>
                                        NIP: {{ $pemeriksaanHarian->petugasUKS->NIP }}
                                    </p>
                                    @if($pemeriksaanHarian->petugasUKS->no_telp)
                                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                                        <i class="fas fa-phone mr-1"></i>
                                        Telp: {{ $pemeriksaanHarian->petugasUKS->no_telp }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-user-times text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">Data petugas UKS tidak tersedia</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Riwayat Pemeriksaan Terbaru Siswa -->
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-purple-200 pb-2">
                            <i class="fas fa-history text-purple-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-900">Riwayat Pemeriksaan Terbaru</h3>
                        </div>
                        
                        @php
                            $riwayatPemeriksaan = \App\Models\PemeriksaanHarian::with(['petugasUKS'])
                                ->where('Id_Siswa', $pemeriksaanHarian->Id_Siswa)
                                ->where('Id_Harian', '!=', $pemeriksaanHarian->Id_Harian)
                                ->orderBy('Tanggal_Jam', 'desc')
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        @if($riwayatPemeriksaan->count() > 0)
                        <div class="space-y-4">
                            @foreach($riwayatPemeriksaan as $riwayat)
                            <div class="bg-white p-4 rounded-lg border border-purple-200 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <a href="{{ route($showRoute, $riwayat->Id_Harian) }}" class="font-medium text-purple-700 hover:text-purple-800 transition-colors flex items-center">
                                            <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                                            Pemeriksaan {{ $riwayat->Id_Harian }}
                                        </a>
                                        <div class="flex items-center text-xs text-gray-500 mt-1">
                                            <i class="fas fa-calendar text-purple-500 mr-1"></i>
                                            <span>{{ $riwayat->Tanggal_Jam ? \Carbon\Carbon::parse($riwayat->Tanggal_Jam)->format('d M Y, H:i') : 'Tanggal tidak tersedia' }}</span>
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-user-nurse text-purple-500 mr-1"></i>
                                            <span>{{ $riwayat->petugasUKS->nama_petugas_uks ?? 'Petugas UKS' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-700 bg-gray-50 p-3 rounded border">
                                    <p>{{ $riwayat->Hasil_Pemeriksaan ? \Illuminate\Support\Str::limit($riwayat->Hasil_Pemeriksaan, 150) : 'Hasil pemeriksaan tidak tersedia' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 bg-white rounded-lg border border-purple-200">
                            <i class="fas fa-clipboard-list text-gray-400 text-3xl mb-3"></i>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada riwayat pemeriksaan lain</h3>
                            <p class="text-xs text-gray-500">Siswa ini belum memiliki pemeriksaan harian lainnya.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons Section -->
            <div class="bg-gray-50 px-6 py-4 border-t">
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route($indexRoute) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-4 rounded-md text-center flex items-center justify-center transition-colors">
                        <i class="fas fa-list mr-2"></i>
                        Lihat Semua Pemeriksaan
                    </a>
                    
                    @if($isAdmin || $isPetugas)
                    <a href="{{ route($editRoute, $pemeriksaanHarian->Id_Harian) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2.5 px-4 rounded-md text-center flex items-center justify-center transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Pemeriksaan
                    </a>
                    @endif
                    
                    @if($isAdmin || $isPetugas)
                    <a href="{{ route($createRoute) }}" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2.5 px-4 rounded-md text-center flex items-center justify-center transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Pemeriksaan Baru
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.close-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });
});
</script>
@endpush
@endsection