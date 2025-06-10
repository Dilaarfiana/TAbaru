@extends('layouts.app')

@section('title', 'Detail Petugas UKS')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Main Card -->
    <div class="max-w-6xl mx-auto">
        <!-- Top Card - White header -->
        <div class="bg-white rounded-t-lg p-6 shadow-md border-b-4 border-blue-500">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-4 rounded-full shadow-lg mr-4">
                        <i class="fas fa-user-nurse text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $petugasUKS->nama_petugas_uks }}</h1>
                        <div class="flex flex-wrap items-center mt-2 gap-2">
                            @if($petugasUKS->level == 'admin')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    Administrator
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    Petugas
                                </span>
                            @endif
                            
                            <!-- Status Badge -->
                            @if($petugasUKS->status_aktif == 1)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                            @endif
                            
                            <span class="text-gray-500 text-sm bg-gray-100 px-2 py-1 rounded">
                                <i class="fas fa-id-card mr-1"></i>
                                NIP: {{ $petugasUKS->NIP }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('petugasuks.edit', $petugasUKS->NIP) }}" 
                       class="px-4 py-2 bg-yellow-500 text-white rounded-md shadow hover:bg-yellow-600 transition duration-150 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    
                    <form action="{{ route('petugasuks.destroy', $petugasUKS->NIP) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition duration-150 flex items-center"
                                onclick="return confirmDelete()">
                            <i class="fas fa-trash mr-2"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="bg-white rounded-b-lg shadow-md p-6">
            <!-- Information Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Main Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Details Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center">
                            <i class="fas fa-user mr-3 text-blue-500"></i>
                            Informasi Dasar
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-id-card text-blue-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">NIP</h3>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">{{ $petugasUKS->NIP }}</p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-user text-blue-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Nama Petugas</h3>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">{{ $petugasUKS->nama_petugas_uks }}</p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Level Akses</h3>
                                </div>
                                <div class="flex items-center">
                                    @if($petugasUKS->level == 'admin')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-user-shield mr-1"></i>
                                            Administrator
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-user-tie mr-1"></i>
                                            Petugas
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-toggle-{{ $petugasUKS->status_aktif ? 'on text-green-500' : 'off text-red-500' }} mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status Aktif</h3>
                                </div>
                                <div class="flex items-center">
                                    @if($petugasUKS->status_aktif == 1)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border border-green-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center">
                            <i class="fas fa-address-book mr-3 text-green-500"></i>
                            Informasi Kontak & Alamat
                        </h2>
                        <div class="space-y-4">
                            <!-- Phone Number -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-phone text-green-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">No. Telepon</h3>
                                </div>
                                @if($petugasUKS->no_telp)
                                    <div class="flex items-center justify-between">
                                        <p class="text-lg font-semibold text-gray-900">{{ $petugasUKS->no_telp }}</p>
                                        <a href="tel:{{ $petugasUKS->no_telp }}" 
                                           class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition-colors">
                                            <i class="fas fa-phone mr-1"></i>
                                            Telepon
                                        </a>
                                    </div>
                                @else
                                    <p class="text-lg text-gray-400 italic">Belum diisi</p>
                                @endif
                            </div>
                            
                            <!-- Address -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Alamat</h3>
                                </div>
                                @if($petugasUKS->alamat)
                                    <p class="text-gray-900 leading-relaxed">{{ $petugasUKS->alamat }}</p>
                                @else
                                    <p class="text-gray-400 italic">Alamat belum diisi</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Metadata & Actions -->
                <div class="space-y-6">
                    <!-- System Information -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-6 border border-purple-100">
                        <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center">
                            <i class="fas fa-cog mr-3 text-purple-500"></i>
                            Informasi Sistem
                        </h2>
                        <div class="space-y-4">
                            <!-- Created At -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-calendar-plus text-purple-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Dibuat Pada</h3>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $petugasUKS->created_at ? $petugasUKS->created_at->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') : '-' }}
                                </p>
                                @if($petugasUKS->created_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $petugasUKS->created_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Updated At -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-calendar-check text-purple-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Terakhir Diperbarui</h3>
                                </div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $petugasUKS->updated_at ? $petugasUKS->updated_at->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') : '-' }}
                                </p>
                                @if($petugasUKS->updated_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $petugasUKS->updated_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Record ID -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-fingerprint text-purple-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">ID Record</h3>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-database mr-1"></i>
                                    {{ $petugasUKS->NIP }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Actions -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-compass mr-2 text-gray-500"></i>
                            Navigasi
                        </h2>
                        <div class="space-y-3">
                            <a href="{{ route('petugasuks.index') }}" 
                               class="flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-gray-700 transition duration-150 w-full">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Daftar Petugas
                            </a>
                            
                            <a href="{{ route('petugasuks.edit', $petugasUKS->NIP) }}" 
                               class="flex items-center justify-center px-4 py-3 bg-yellow-500 text-white rounded-md shadow-sm text-sm font-medium hover:bg-yellow-600 transition duration-150 w-full">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Data Petugas
                            </a>
                            
                            <a href="{{ route('petugasuks.create') }}" 
                               class="flex items-center justify-center px-4 py-3 bg-blue-500 text-white rounded-md shadow-sm text-sm font-medium hover:bg-blue-600 transition duration-150 w-full">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Petugas Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    const petugasNIP = '{{ $petugasUKS->NIP }}';
    const petugasName = '{{ $petugasUKS->nama_petugas_uks }}';
    
    return confirm(`Apakah Anda yakin ingin menghapus data petugas UKS ini?\n\nNIP: ${petugasNIP}\nNama: ${petugasName}\n\nData yang sudah dihapus tidak dapat dikembalikan.`);
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success/error messages
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Add click animation to action buttons
    const buttons = document.querySelectorAll('button, a[href]');
    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
        });
    });

    // Log record info for debugging
    console.log('Petugas UKS Detail Page Loaded', {
        petugasNIP: '{{ $petugasUKS->NIP }}',
        petugasName: '{{ $petugasUKS->nama_petugas_uks }}',
        level: '{{ $petugasUKS->level }}',
        status: {{ $petugasUKS->status_aktif ? 'true' : 'false' }},
        timestamp: new Date().toISOString()
    });
});
</script>
@endpush
@endsection