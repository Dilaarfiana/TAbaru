{{-- File: resources/views/pemeriksaan_awal/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: READ ONLY, ORANG TUA: NO ACCESS --}}
@extends('layouts.app')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Blokir akses orang tua sepenuhnya
    if ($isOrangTua) {
        abort(403, 'Akses ditolak. Orang tua tidak memiliki izin untuk mengakses halaman ini.');
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'pemeriksaan_awal' : ($isPetugas ? 'petugas.pemeriksaan_awal' : 'dokter.pemeriksaan_awal');
    $indexRoute = $baseRoute . '.index';
    
    // Routes yang hanya untuk admin dan petugas
    if ($isAdmin) {
        $editRoute = 'pemeriksaan_awal.edit';
        $destroyRoute = 'pemeriksaan_awal.destroy';
    } elseif ($isPetugas) {
        $editRoute = 'petugas.pemeriksaan_awal.edit';
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
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman detail pemeriksaan awal ini.
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
    // Redirect ke halaman yang sesuai untuk orang tua
    window.location.href = "{{ route('orangtua.dashboard') ?? route('dashboard') }}";
}

// Auto redirect setelah 5 detik
setTimeout(function() {
    goBack();
}, 5000);
</script>

@stop
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section dengan Security Warning -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-5">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
                <h1 class="text-xl font-bold text-white">Detail Pemeriksaan Awal</h1>
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
            </div>
            <div class="flex items-center space-x-3">
                <!-- Security Badge -->
                <div class="bg-white text-red-600 rounded-full px-3 py-1 text-xs font-bold shadow-sm border border-red-200">
                    <i class="fas fa-shield-alt mr-1"></i> SECURED
                </div>
                <div class="bg-white text-blue-600 rounded-full px-4 py-2 text-sm font-bold shadow-sm">
                    <i class="fas fa-hashtag mr-1"></i> ID: {{ $pemeriksaanAwal->id_preawal }}
                </div>
            </div>
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
                    Orang tua tidak memiliki izin untuk mengakses detail pemeriksaan medis ini.
                </p>
            </div>
        </div>
    </div>

    <!-- Info Access Level -->
    @if($isDokter)
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    Anda mengakses detail pemeriksaan awal dengan <strong>Akses Dokter</strong>. 
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
                    Anda mengakses detail pemeriksaan awal dengan <strong>Akses Petugas</strong>. 
                    Anda dapat melihat dan mengedit data pemeriksaan, namun tidak dapat menghapus data.
                </p>
            </div>
        </div>
    </div>
    @elseif($isAdmin)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    Anda mengakses detail pemeriksaan awal dengan <strong>Akses Administrator</strong>. 
                    Anda memiliki akses penuh untuk melihat, mengedit, dan menghapus data pemeriksaan.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-3 flex items-center justify-between">
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
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mx-6 mt-3 flex items-center justify-between">
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
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mx-6 mt-3 flex items-center justify-between">
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

    @if(session('warning'))
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mx-6 mt-3 flex items-center justify-between">
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

    <!-- Content tetap sama seperti sebelumnya... -->
    <div class="p-6">
        <!-- Kartu Informasi Pasien & Pemeriksaan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Informasi Dasar -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-blue-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <h3 class="font-semibold text-gray-800">Informasi Dasar</h3>
                        </div>
                        @if($isDokter)
                            <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded">Baca Saja</span>
                        @elseif($isPetugas)
                            <span class="text-xs bg-yellow-100 text-yellow-600 px-2 py-1 rounded">Dapat Edit</span>
                        @elseif($isAdmin)
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">Akses Penuh</span>
                        @endif
                    </div>
                </div>
                <div class="p-4">
                    <ul class="space-y-3">
                        <li class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">ID Pemeriksaan:</span>
                            <span class="text-gray-800 font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-bold">{{ $pemeriksaanAwal->id_preawal }}</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">ID Detail:</span>
                            <span class="text-gray-800 font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ $pemeriksaanAwal->id_detprx ?? '-' }}</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Tanggal & Jam:</span>
                            <span class="text-gray-800 text-sm">
                                @if($pemeriksaanAwal->detailPemeriksaan && $pemeriksaanAwal->detailPemeriksaan->tanggal_jam)
                                    {{ \Carbon\Carbon::parse($pemeriksaanAwal->detailPemeriksaan->tanggal_jam)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </span>
                        </li>
                        @if($pemeriksaanAwal->detailPemeriksaan && $pemeriksaanAwal->detailPemeriksaan->siswa)
                        <li class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Pasien:</span>
                            <span class="text-gray-800 font-semibold text-sm">{{ $pemeriksaanAwal->detailPemeriksaan->siswa->nama_siswa }}</span>
                        </li>
                        @endif
                        @if($pemeriksaanAwal->detailPemeriksaan && $pemeriksaanAwal->detailPemeriksaan->dokter)
                        <li class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Dokter:</span>
                            <span class="text-gray-800 font-semibold text-sm">{{ $pemeriksaanAwal->detailPemeriksaan->dokter->nama_dokter }}</span>
                        </li>
                        @endif
                        @if($pemeriksaanAwal->detailPemeriksaan && $pemeriksaanAwal->detailPemeriksaan->status_pemeriksaan)
                        <li class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Status:</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                {{ ucfirst($pemeriksaanAwal->detailPemeriksaan->status_pemeriksaan) }}
                            </span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Tanda Vital dan konten lainnya tetap sama... -->
            
        </div>
    </div>
    
    <!-- Enhanced Role Information Footer -->
    <div class="mt-4 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg border-2 border-gray-200">
        <div class="flex items-center text-sm text-gray-700">
            <div class="flex items-center mr-4">
                <i class="fas fa-shield-alt text-red-500 mr-2"></i>
                <span class="font-bold text-red-600">KEAMANAN AKSES:</span>
            </div>
            <div class="flex-1">
                @if($isDokter)
                    <span><strong>Akses Dokter:</strong> Anda dapat melihat semua detail pemeriksaan awal untuk keperluan konsultasi dan diagnosis.</span>
                @elseif($isPetugas)
                    <span><strong>Akses Petugas:</strong> Anda dapat melihat dan mengedit data pemeriksaan awal, namun tidak dapat menghapus data yang sudah tersimpan.</span>
                @elseif($isAdmin)
                    <span><strong>Akses Administrator:</strong> Anda memiliki kontrol penuh untuk mengelola data pemeriksaan awal termasuk mengedit dan menghapus data.</span>
                @endif
            </div>
        </div>
        <div class="mt-2 text-xs text-red-600 font-medium">
            <i class="fas fa-ban mr-1"></i>
            ORANG TUA: TIDAK MEMILIKI AKSES KE HALAMAN INI
        </div>
    </div>
</div>

@push('scripts')
<script>
    @if($isAdmin)
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus data pemeriksaan awal ini?\n\nTindakan ini akan menghapus:\n- Data pemeriksaan awal\n- Semua data terkait lainnya\n\nData yang dihapus tidak dapat dikembalikan!')) {
            document.getElementById('delete-form').submit();
        }
    }
    @endif
    
    // Enhanced security check
    document.addEventListener('DOMContentLoaded', function() {
        // Double check untuk memastikan orang tua tidak bisa mengakses
        const userLevel = '{{ $userLevel }}';
        if (userLevel === 'orang_tua') {
            // Force redirect jika somehow sampai ke sini
            alert('Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.');
            window.location.href = '{{ route("dashboard") }}';
            return;
        }
        
        // Auto-close alerts setelah 5 detik
        const alerts = document.querySelectorAll('.close-alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert.parentElement) {
                    alert.parentElement.style.display = 'none';
                }
            }, 5000);
        });
        
        // Add security logging (optional)
        console.log('Akses halaman detail pemeriksaan oleh: ' + userLevel);
        
        // Enhanced visual feedback berdasarkan role
        const userLevel = '{{ $userLevel }}';
        if (userLevel === 'dokter') {
            document.querySelector('.bg-gradient-to-r').classList.add('border-b-4', 'border-green-500');
        } else if (userLevel === 'petugas') {
            document.querySelector('.bg-gradient-to-r').classList.add('border-b-4', 'border-yellow-500');
        } else if (userLevel === 'admin') {
            document.querySelector('.bg-gradient-to-r').classList.add('border-b-4', 'border-red-500');
        }
    });
</script>
@endpush
@endsection