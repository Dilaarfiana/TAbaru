@extends('layouts.app')

@section('title', 'Detail Dokter')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Main Card -->
    <div class="max-w-6xl mx-auto">
        <!-- Top Card - White header -->
        <div class="bg-white rounded-t-lg p-6 shadow-md border-b-4 border-blue-500">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-4 rounded-full shadow-lg mr-4">
                        <i class="fas fa-user-md text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $dokter->Nama_Dokter }}</h1>
                        <div class="flex flex-wrap items-center mt-2 gap-2">
                            @if($dokter->Spesialisasi)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-stethoscope mr-1"></i>
                                    {{ $dokter->Spesialisasi }}
                                </span>
                            @endif
                            
                            <!-- Status Badge -->
                            @if($dokter->status_aktif == 1)
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
                                ID: {{ $dokter->Id_Dokter }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                    <!-- Toggle Status Button -->
                    <a href="{{ route('dokter.edit', $dokter->Id_Dokter) }}" 
                       class="px-4 py-2 bg-yellow-500 text-white rounded-md shadow hover:bg-yellow-600 transition duration-150 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    
                    <form action="{{ route('dokter.destroy', $dokter->Id_Dokter) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition duration-150 flex items-center"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data dokter ini? Data yang sudah dihapus tidak dapat dikembalikan.')">
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
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">ID Dokter</h3>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">{{ $dokter->Id_Dokter }}</p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-user text-blue-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Nama Dokter</h3>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">{{ $dokter->Nama_Dokter }}</p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-stethoscope text-blue-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Spesialisasi</h3>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $dokter->Spesialisasi ?? 'Belum diset' }}
                                </p>
                            </div>
                            
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-toggle-{{ $dokter->status_aktif ? 'on text-green-500' : 'off text-red-500' }} mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status Aktif</h3>
                                </div>
                                <div class="flex items-center">
                                    @if($dokter->status_aktif == 1)
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
                            Informasi Kontak
                        </h2>
                        <div class="space-y-4">
                            <!-- Phone Number -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-phone text-green-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">No. Telepon</h3>
                                </div>
                                @if($dokter->No_Telp)
                                    <div class="flex items-center justify-between">
                                        <p class="text-lg font-semibold text-gray-900">+62{{ $dokter->No_Telp }}</p>
                                        <a href="tel:+62{{ $dokter->No_Telp }}" 
                                           class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200 transition-colors">
                                            <i class="fas fa-phone mr-1"></i>
                                            Telepon
                                        </a>
                                    </div>
                                @else
                                    <p class="text-lg text-gray-400 italic">Belum diset</p>
                                @endif
                            </div>
                            
                            <!-- Address -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Alamat</h3>
                                </div>
                                @if($dokter->Alamat)
                                    <p class="text-gray-900 leading-relaxed">{{ $dokter->Alamat }}</p>
                                @else
                                    <p class="text-gray-400 italic">Belum diset</p>
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
                                    {{ $dokter->created_at ? $dokter->created_at->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') : '-' }}
                                </p>
                                @if($dokter->created_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $dokter->created_at->diffForHumans() }}
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
                                    {{ $dokter->updated_at ? $dokter->updated_at->setTimezone('Asia/Jakarta')->format('d F Y, H:i:s') : '-' }}
                                </p>
                                @if($dokter->updated_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $dokter->updated_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Password Status -->
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-key text-purple-500 mr-2"></i>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status Password</h3>
                                </div>
                                @if($dokter->password)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Sudah diset
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Belum diset
                                    </span>
                                @endif
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
                            <a href="{{ route('dokter.index') }}" 
                               class="flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-gray-700 transition duration-150 w-full">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Daftar Dokter
                            </a>
                            
                            <a href="{{ route('dokter.edit', $dokter->Id_Dokter) }}" 
                               class="flex items-center justify-center px-4 py-3 bg-yellow-500 text-white rounded-md shadow-sm text-sm font-medium hover:bg-yellow-600 transition duration-150 w-full">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Data Dokter
                            </a>
                            
                            <a href="{{ route('dokter.create') }}" 
                               class="flex items-center justify-center px-4 py-3 bg-blue-500 text-white rounded-md shadow-sm text-sm font-medium hover:bg-blue-600 transition duration-150 w-full">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Dokter Baru
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
    });
</script>
@endpush

@endsection