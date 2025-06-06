{{-- File: resources/views/pemeriksaan_fisik/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('page_title', 'Detail Pemeriksaan Fisik')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Blokir akses orang tua sepenuhnya
    if ($isOrangTua) {
        abort(403, 'Akses ditolak. Orang tua tidak memiliki izin untuk mengakses halaman pemeriksaan fisik.');
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'pemeriksaan_fisik' : ($isPetugas ? 'petugas.pemeriksaan_fisik' : 'dokter.pemeriksaan_fisik');
    $indexRoute = $baseRoute . '.index';
    $showRoute = $baseRoute . '.show';
    
    // Routes yang hanya untuk admin dan petugas
    if ($isAdmin) {
        $createRoute = 'pemeriksaan_fisik.create';
        $editRoute = 'pemeriksaan_fisik.edit';
        $destroyRoute = 'pemeriksaan_fisik.destroy';
    } elseif ($isPetugas) {
        $createRoute = 'petugas.pemeriksaan_fisik.create';
        $editRoute = 'petugas.pemeriksaan_fisik.edit';
    }
@endphp

<!-- Error Modal untuk Unauthorized Access (Backup jika PHP check tidak jalan) -->
@if($isOrangTua)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="access-denied-modal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-ban text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Akses Ditolak</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Maaf, Anda tidak memiliki izin untuk mengakses detail pemeriksaan fisik ini.
                </p>
                <p class="text-xs text-red-600 font-medium mb-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Level akses: Orang Tua (NO ACCESS)
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="goBack()" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function goBack() {
    window.location.href = "{{ route('orangtua.dashboard') ?? route('dashboard') }}";
}
setTimeout(function() { goBack(); }, 5000);
</script>

@stop
@endif

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-md px-6 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-eye text-white mr-3 text-xl"></i>
                <h2 class="text-xl font-bold text-white">Detail Pemeriksaan Fisik</h2>
                @if($isDokter)
                    <span class="ml-3 px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                        <i class="fas fa-stethoscope mr-1"></i>Akses Dokter
                    </span>
                @elseif($isPetugas)
                    <span class="ml-3 px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                        <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                    </span>
                @elseif($isAdmin)
                    <span class="ml-3 px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                        <i class="fas fa-user-shield mr-1"></i>Akses Admin
                    </span>
                @endif
                <!-- Security Badge -->
                <div class="ml-3 bg-white text-red-600 rounded-full px-3 py-1 text-xs font-bold border border-red-200">
                    <i class="fas fa-shield-alt mr-1"></i> SECURED
                </div>
            </div>
            <div class="flex gap-2">
                @if($isAdmin || $isPetugas)
                <a href="{{ route($editRoute, $pemeriksaanFisik->id_prefisik) }}" class="bg-white text-blue-600 hover:bg-blue-50 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Data
                </a>
                @endif
                <a href="{{ route($indexRoute) }}" class="bg-blue-700 text-white hover:bg-blue-800 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        <!-- Access Control Warning -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-3">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <strong>Akses Terbatas:</strong> Halaman ini hanya dapat diakses oleh tenaga medis (Admin, Petugas, Dokter). 
                        Orang tua tidak memiliki izin untuk mengakses detail pemeriksaan fisik ini.
                    </p>
                </div>
            </div>
        </div>

        <!-- Access Level Info -->
        @if($isDokter)
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-3">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        Anda melihat detail pemeriksaan fisik dengan <strong>Akses Dokter</strong>. 
                        Anda dapat melihat semua data pemeriksaan namun tidak dapat mengubah atau menghapus data.
                    </p>
                </div>
            </div>
        </div>
        @elseif($isPetugas)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-6 mt-3">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Anda melihat detail pemeriksaan fisik dengan <strong>Akses Petugas</strong>. 
                        Anda dapat melihat dan mengedit data pemeriksaan, namun tidak dapat menghapus data.
                    </p>
                </div>
            </div>
        </div>
        @elseif($isAdmin)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-6 mt-3">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Anda melihat detail pemeriksaan fisik dengan <strong>Akses Administrator</strong>. 
                        Anda memiliki akses penuh untuk melihat, mengedit, dan menghapus data pemeriksaan.
                    </p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Badge ID Pemeriksaan -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 border-b border-blue-100">
            <div class="flex justify-center">
                <div class="bg-white py-4 px-8 rounded-full shadow-sm border border-blue-200 flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">ID Pemeriksaan Fisik</div>
                        <div class="font-mono font-bold text-2xl text-blue-600">{{ $pemeriksaanFisik->id_prefisik }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            @if($isDokter)
                                <span class="text-green-600">View Only Access</span>
                            @elseif($isPetugas)
                                <span class="text-yellow-600">Edit Access</span>
                            @elseif($isAdmin)
                                <span class="text-red-600">Full Access</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <div class="px-6 pt-6">
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 flex items-center justify-between">
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
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 flex items-center justify-between">
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
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 flex items-center justify-between">
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
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 flex items-center justify-between">
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
        
        <!-- Detail Content -->
        <div class="p-6">
            <!-- Informasi Detail Pemeriksaan -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-100 p-6 shadow-sm mb-6">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-blue-800">Informasi Detail Pemeriksaan</h3>
                        <p class="text-sm text-blue-600">Data pemeriksaan dan identitas pasien</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-md p-4 border border-blue-200 shadow-sm">
                        <div class="text-sm text-gray-500 mb-2">ID Detail Pemeriksaan</div>
                        <div class="flex items-center">
                            <i class="fas fa-file-medical text-blue-500 mr-2"></i>
                            <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->id_detprx }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-md p-4 border border-blue-200 shadow-sm">
                        <div class="text-sm text-gray-500 mb-2">Nama Siswa</div>
                        <div class="flex items-center">
                            <i class="fas fa-user-graduate text-blue-500 mr-2"></i>
                            <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan->siswa->nama_siswa ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-md p-4 border border-blue-200 shadow-sm">
                        <div class="text-sm text-gray-500 mb-2">Tanggal Pemeriksaan</div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                            <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan ? \Carbon\Carbon::parse($pemeriksaanFisik->detailPemeriksaan->tanggal_jam)->format('d F Y - H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-md p-4 border border-blue-200 shadow-sm">
                        <div class="text-sm text-gray-500 mb-2">Dokter Pemeriksa</div>
                        <div class="flex items-center">
                            <i class="fas fa-user-md text-blue-500 mr-2"></i>
                            <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->detailPemeriksaan->dokter->nama_dokter ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-md p-4 border border-blue-200 shadow-sm">
                        <div class="text-sm text-gray-500 mb-2">Status Pemeriksaan</div>
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-check text-blue-500 mr-2"></i>
                            @if($pemeriksaanFisik->detailPemeriksaan)
                                @if($pemeriksaanFisik->detailPemeriksaan->status_pemeriksaan == 'lengkap')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i> Lengkap
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                                        <i class="fas fa-clock mr-1"></i> {{ ucfirst($pemeriksaanFisik->detailPemeriksaan->status_pemeriksaan) }}
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Enhanced Role Access Information -->
                <div class="mt-4 p-4 rounded-lg border {{ $isAdmin ? 'bg-blue-100 border-blue-300' : ($isPetugas ? 'bg-yellow-100 border-yellow-300' : 'bg-green-100 border-green-300') }}">
                    <div class="flex items-center">
                        <i class="fas fa-user-tag {{ $isAdmin ? 'text-blue-600' : ($isPetugas ? 'text-yellow-600' : 'text-green-600') }} mr-2"></i>
                        <div class="text-sm {{ $isAdmin ? 'text-blue-800' : ($isPetugas ? 'text-yellow-800' : 'text-green-800') }}">
                            <strong>Level Akses Anda:</strong> 
                            @if($isAdmin)
                                Administrator - Dapat melihat, mengedit, dan menghapus semua data pemeriksaan fisik
                            @elseif($isPetugas)
                                Petugas UKS - Dapat melihat dan mengedit data pemeriksaan fisik
                            @elseif($isDokter)
                                Dokter - Dapat melihat data pemeriksaan fisik untuk keperluan konsultasi
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Security Information -->
                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-red-500 mr-2"></i>
                        <div>
                            <p class="text-sm text-red-700 font-medium">Informasi Keamanan</p>
                            <p class="text-xs text-red-600 mt-1">
                                • Orang Tua: Tidak memiliki akses sama sekali ke halaman ini
                                • Semua aktivitas viewing tercatat dalam sistem audit
                                • Data medis dilindungi sesuai standar privasi
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Antropometri - Data Fisik Dasar -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-ruler-combined text-green-500 mr-2"></i>
                    Antropometri
                    <span class="ml-2 text-xs text-gray-500">(Pengukuran Fisik)</span>
                </h3>
                
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Tinggi Badan -->
                        <div class="bg-white rounded-md p-4 border border-green-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-arrows-alt-v text-green-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Tinggi Badan</span>
                                </div>
                                @if($pemeriksaanFisik->tinggi_badan)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $pemeriksaanFisik->tinggi_badan ? $pemeriksaanFisik->tinggi_badan . ' cm' : '-' }}
                            </div>
                        </div>
                        
                        <!-- Berat Badan -->
                        <div class="bg-white rounded-md p-4 border border-green-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-weight text-green-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Berat Badan</span>
                                </div>
                                @if($pemeriksaanFisik->berat_badan)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $pemeriksaanFisik->berat_badan ? $pemeriksaanFisik->berat_badan . ' kg' : '-' }}
                            </div>
                        </div>
                        
                        <!-- Lingkar Kepala -->
                        <div class="bg-white rounded-md p-4 border border-green-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-circle text-green-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Lingkar Kepala</span>
                                </div>
                                @if($pemeriksaanFisik->lingkar_kepala)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $pemeriksaanFisik->lingkar_kepala ? $pemeriksaanFisik->lingkar_kepala . ' cm' : '-' }}
                            </div>
                        </div>
                        
                        <!-- Lingkar Lengan Atas -->
                        <div class="bg-white rounded-md p-4 border border-green-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-ring text-green-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Lingkar Lengan Atas</span>
                                </div>
                                @if($pemeriksaanFisik->lingkar_lengan_atas)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ $pemeriksaanFisik->lingkar_lengan_atas ? $pemeriksaanFisik->lingkar_lengan_atas . ' cm' : '-' }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Enhanced BMI Display -->
                    @if($pemeriksaanFisik->tinggi_badan && $pemeriksaanFisik->berat_badan)
                    @php
                        $heightInMeters = $pemeriksaanFisik->tinggi_badan / 100;
                        $bmi = $pemeriksaanFisik->berat_badan / ($heightInMeters * $heightInMeters);
                        $bmi = round($bmi, 1);
                        
                        if ($bmi < 18.5) {
                            $category = 'Berat Badan Kurang';
                            $colorClass = 'text-blue-600';
                            $bgClass = 'bg-blue-100';
                            $borderClass = 'border-blue-200';
                            $icon = 'fas fa-arrow-down';
                        } elseif ($bmi < 25) {
                            $category = 'Berat Badan Normal';
                            $colorClass = 'text-green-600';
                            $bgClass = 'bg-green-100';
                            $borderClass = 'border-green-200';
                            $icon = 'fas fa-check-circle';
                        } elseif ($bmi < 30) {
                            $category = 'Berat Badan Berlebih';
                            $colorClass = 'text-yellow-600';
                            $bgClass = 'bg-yellow-100';
                            $borderClass = 'border-yellow-200';
                            $icon = 'fas fa-arrow-up';
                        } else {
                            $category = 'Obesitas';
                            $colorClass = 'text-red-600';
                            $bgClass = 'bg-red-100';
                            $borderClass = 'border-red-200';
                            $icon = 'fas fa-exclamation-triangle';
                        }
                    @endphp
                    <div class="bg-white rounded-lg border {{ $borderClass }} p-6 mt-6 shadow-sm">
                        <div class="flex items-center">
                            <div class="mr-6">
                                <i class="{{ $icon }} text-3xl {{ $colorClass }}"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-lg font-medium text-gray-700">Indeks Massa Tubuh (BMI)</h4>
                                    <span class="text-xs {{ $colorClass }} font-medium">Perhitungan Otomatis</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-4xl font-bold {{ $colorClass }} mr-4">{{ $bmi }}</span>
                                        <div class="px-4 py-2 rounded-full text-sm font-medium {{ $bgClass }} {{ $colorClass }} border {{ $borderClass }}">
                                            {{ $category }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 text-right">
                                        <div>Tinggi: {{ $pemeriksaanFisik->tinggi_badan }} cm</div>
                                        <div>Berat: {{ $pemeriksaanFisik->berat_badan }} kg</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-white rounded-lg border border-gray-200 p-6 mt-6 shadow-sm">
                        <div class="flex items-center justify-center text-gray-500">
                            <i class="fas fa-calculator text-2xl mr-3"></i>
                            <div class="text-center">
                                <div class="font-medium">BMI tidak dapat dihitung</div>
                                <div class="text-sm">Data tinggi atau berat badan belum lengkap</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Pemeriksaan Sistem Organ -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-heartbeat text-red-500 mr-2"></i>
                    Pemeriksaan Sistem Organ
                    <span class="ml-2 text-xs text-gray-500">(Hasil Pemeriksaan)</span>
                </h3>
                
                <div class="bg-gradient-to-br from-red-50 to-pink-50 border border-red-100 rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Kepala -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-head-side-virus text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Kepala</span>
                                </div>
                                @if($pemeriksaanFisik->kepala)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->kepala ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->kepala ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Dada -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-lungs text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Dada</span>
                                </div>
                                @if($pemeriksaanFisik->dada)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->dada ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->dada ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Jantung -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-heart text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Jantung</span>
                                </div>
                                @if($pemeriksaanFisik->jantung)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->jantung ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->jantung ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Paru -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-lungs-virus text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Paru</span>
                                </div>
                                @if($pemeriksaanFisik->paru)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->paru ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->paru ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Perut -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-hand-paper text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Perut</span>
                                </div>
                                @if($pemeriksaanFisik->perut)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->perut ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->perut ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Hepar -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-prescription-bottle text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Hepar</span>
                                </div>
                                @if($pemeriksaanFisik->hepar)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->hepar ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->hepar ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Anogenital -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-user-check text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Anogenital</span>
                                </div>
                                @if($pemeriksaanFisik->anogenital)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->anogenital ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->anogenital ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Ekstremitas -->
                        <div class="bg-white rounded-md p-4 border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <i class="fas fa-walking text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Ekstremitas</span>
                                </div>
                                @if($pemeriksaanFisik->ekstremitas)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="text-gray-800 min-h-[2rem] flex items-center">
                                <span class="{{ $pemeriksaanFisik->ekstremitas ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->ekstremitas ?: 'Tidak diperiksa' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pemeriksaan Penunjang & Rencana -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clipboard-check text-purple-500 mr-2"></i>
                    Pemeriksaan Penunjang & Rencana
                    <span class="ml-2 text-xs text-gray-500">(Diagnosis & Terapi)</span>
                </h3>
                
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-100 rounded-lg p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Pemeriksaan Penunjang -->
                        <div class="bg-white rounded-lg p-5 border border-purple-200 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-microscope text-purple-500 mr-2"></i>
                                    <h4 class="text-md font-medium text-purple-700">Pemeriksaan Penunjang</h4>
                                </div>
                                @if($pemeriksaanFisik->pemeriksaan_penunjang)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-400"></i>
                                @endif
                            </div>
                            <div class="p-4 bg-purple-50 rounded-md min-h-[100px] border border-purple-100">
                                <p class="text-gray-800 whitespace-pre-wrap {{ $pemeriksaanFisik->pemeriksaan_penunjang ? 'font-medium' : 'italic text-gray-500' }}">
                                    {{ $pemeriksaanFisik->pemeriksaan_penunjang ?: 'Tidak ada pemeriksaan penunjang yang diperlukan' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Masalah Aktif -->
                            <div class="bg-white rounded-lg p-5 border border-purple-200 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-purple-500 mr-2"></i>
                                        <h4 class="text-md font-medium text-purple-700">Masalah Aktif</h4>
                                    </div>
                                    @if($pemeriksaanFisik->masalah_aktif)
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    @else
                                        <i class="fas fa-minus-circle text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="p-3 bg-purple-50 rounded-md min-h-[80px] border border-purple-100">
                                    <p class="text-gray-800 {{ $pemeriksaanFisik->masalah_aktif ? 'font-medium' : 'italic text-gray-500' }}">
                                        {{ $pemeriksaanFisik->masalah_aktif ?: 'Tidak ada masalah aktif' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Rencana Medis dan Terapi -->
                            <div class="bg-white rounded-lg p-5 border border-purple-200 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-pills text-purple-500 mr-2"></i>
                                        <h4 class="text-md font-medium text-purple-700">Rencana Medis dan Terapi</h4>
                                    </div>
                                    @if($pemeriksaanFisik->rencana_medis_dan_terapi)
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    @else
                                        <i class="fas fa-minus-circle text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="p-3 bg-purple-50 rounded-md min-h-[80px] border border-purple-100">
                                    <p class="text-gray-800 {{ $pemeriksaanFisik->rencana_medis_dan_terapi ? 'font-medium' : 'italic text-gray-500' }}">
                                        {{ $pemeriksaanFisik->rencana_medis_dan_terapi ?: 'Belum ada rencana terapi' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Metadata Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                    Informasi Metadata
                    <span class="ml-2 text-xs text-gray-500">(Data Sistem)</span>
                </h3>
                
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-md p-4 border border-gray-200 shadow-sm">
                            <div class="text-sm text-gray-500 mb-2">Dibuat Tanggal</div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>
                                <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->created_at->format('d F Y H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-4 border border-gray-200 shadow-sm">
                            <div class="text-sm text-gray-500 mb-2">Terakhir Diupdate</div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                                <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->updated_at->format('d F Y H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-md p-4 border border-gray-200 shadow-sm">
                            <div class="text-sm text-gray-500 mb-2">Selisih Waktu</div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-purple-500 mr-2"></i>
                                <span class="font-medium text-gray-800">{{ $pemeriksaanFisik->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route($indexRoute) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-md text-center flex items-center justify-center transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Lihat Semua Pemeriksaan
                </a>
                
                @if($isAdmin || $isPetugas)
                <a href="{{ route($editRoute, $pemeriksaanFisik->id_prefisik) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-6 rounded-md text-center flex items-center justify-center transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Pemeriksaan
                </a>
                @endif
                
                @if($isAdmin)
                <form action="{{ route($destroyRoute, $pemeriksaanFisik->id_prefisik) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pemeriksaan fisik ini?\n\nID: {{ $pemeriksaanFisik->id_prefisik }}\n\nTindakan ini akan menghapus semua data terkait dan tidak dapat dikembalikan!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-6 rounded-md flex items-center justify-center transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Pemeriksaan
                    </button>
                </form>
                @endif
            </div>

            <!-- Enhanced Role Information Footer -->
            <div class="mt-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border-2 border-gray-200">
                <div class="flex items-center text-sm text-gray-700">
                    <div class="flex items-center mr-4">
                        <i class="fas fa-shield-alt text-red-500 mr-2"></i>
                        <span class="font-bold text-red-600">KEAMANAN AKSES:</span>
                    </div>
                    <div class="flex-1">
                        @if($isDokter)
                            <span><strong>Akses Dokter:</strong> Anda dapat melihat semua detail pemeriksaan fisik untuk keperluan konsultasi dan diagnosis.</span>
                        @elseif($isPetugas)
                            <span><strong>Akses Petugas:</strong> Anda dapat melihat dan mengedit data pemeriksaan fisik, namun tidak dapat menghapus data yang sudah tersimpan.</span>
                        @elseif($isAdmin)
                            <span><strong>Akses Administrator:</strong> Anda memiliki kontrol penuh untuk mengelola data pemeriksaan fisik termasuk mengedit dan menghapus data.</span>
                        @endif
                    </div>
                </div>
                <div class="mt-2 text-xs text-red-600 font-medium">
                    <i class="fas fa-ban mr-1"></i>
                    ORANG TUA: TIDAK MEMILIKI AKSES KE HALAMAN INI
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced security check
    const userLevel = '{{ $userLevel }}';
    if (userLevel === 'orang_tua') {
        alert('Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.');
        window.location.href = '{{ route("dashboard") }}';
        return;
    }
    
    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.close-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });

    // Add hover effects to cards
    const cards = document.querySelectorAll('.hover\\:shadow-md');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('transform', 'scale-105');
            this.style.transition = 'all 0.2s ease-in-out';
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('transform', 'scale-105');
        });
    });

    // Add visual feedback for buttons
    const buttons = document.querySelectorAll('a, button');
    buttons.forEach(button => {
        button.addEventListener('mousedown', function() {
            this.classList.add('transform', 'scale-95');
        });
        
        button.addEventListener('mouseup', function() {
            this.classList.remove('transform', 'scale-95');
        });
        
        button.addEventListener('mouseleave', function() {
            this.classList.remove('transform', 'scale-95');
        });
    });

    // Add role-based visual accents
    if (userLevel === 'dokter') {
        document.querySelector('.bg-gradient-to-r').classList.add('border-b-4', 'border-green-500');
    } else if (userLevel === 'petugas') {
        document.querySelector('.bg-gradient-to-r').classList.add('border-b-4', 'border-yellow-500');
    } else if (userLevel === 'admin') {
        document.querySelector('.bg-gradient-to-r').classList.add('border-b-4', 'border-blue-500');
    }
    
    // Security logging
    console.log('Akses halaman detail pemeriksaan fisik oleh: ' + userLevel);
});
</script>
@endpush
@endsection