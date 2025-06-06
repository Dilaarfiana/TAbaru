@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto"> <!-- Lebih lebar dari sebelumnya -->
        <!-- Back and Action Buttons -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <a href="{{ route('petugasuks.index') }}" class="p-2 bg-white rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left text-gray-500"></i>
                </a>
                <h1 class="text-2xl font-semibold text-gray-800">Detail Petugas UKS</h1>
            </div>
            <div class="flex space-x-2">
                <form action="{{ route('petugasuks.destroy', $petugasUKS->NIP) }}" method="POST" onsubmit="return confirm('Apakah anda yakin ingin menghapus petugas ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Hapus
                    </button>
                </form>
                <a href="{{ route('petugasuks.edit', $petugasUKS->NIP) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <!-- Header with profile info -->
            <div class="relative">
                <!-- Background Pattern dengan gradien yang lebih menarik -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600"></div>
                <div class="absolute inset-0 bg-pattern opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 40L40 0H20L0 20M40 40V20L20 40\'/%3E%3C/g%3E%3C/svg%3E');"></div>
                
                <!-- Profile Content -->
                <div class="relative pt-12 pb-24 px-8 text-center">
                    <div class="inline-flex items-center justify-center h-28 w-28 rounded-full bg-white bg-opacity-90 shadow-xl mb-5 border-4 border-white">
                        <span class="text-blue-600 text-5xl">
                            <i class="fas fa-user-nurse"></i>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-2">
                        {{ $petugasUKS->nama_petugas_uks }}
                    </h2>
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-blue-800 bg-opacity-60 text-white mb-4 shadow-sm">
                        <i class="fas fa-id-badge mr-2"></i>
                        {{ $petugasUKS->NIP }}
                    </div>
                    
                    <!-- Quick Info Pills -->
                    <div class="flex justify-center mt-5 space-x-4">
                        @if($petugasUKS->no_telp)
                        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-white bg-opacity-25 text-white shadow-sm backdrop-blur-sm">
                            <i class="fas fa-phone-alt mr-2"></i>
                            {{ $petugasUKS->no_telp }}
                        </div>
                        @endif
                        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium {{ $petugasUKS->status_aktif ? 'bg-green-500' : 'bg-red-500' }} text-white shadow-sm">
                            <i class="fas {{ $petugasUKS->status_aktif ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                            {{ $petugasUKS->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                        </div>
                        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium {{ $petugasUKS->level == 'admin' ? 'bg-purple-500' : 'bg-blue-500' }} text-white shadow-sm">
                            <i class="fas {{ $petugasUKS->level == 'admin' ? 'fa-user-shield' : 'fa-user-nurse' }} mr-2"></i>
                            {{ $petugasUKS->level == 'admin' ? 'Admin' : 'Petugas' }}
                        </div>
                    </div>
                </div>
                
                <!-- Card Tabs -->
                <div class="absolute bottom-0 left-0 right-0">
                    <div class="flex justify-center">
                        <div class="inline-flex rounded-t-lg overflow-hidden shadow-md">
                            <a href="#" class="px-5 py-3 bg-white text-blue-600 font-medium text-sm flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Informasi Dasar
                            </a>
                            <a href="#" class="px-5 py-3 bg-white bg-opacity-15 text-white hover:bg-opacity-25 transition-colors font-medium text-sm flex items-center">
                                <i class="fas fa-history mr-2"></i>
                                Riwayat Aktivitas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Detail -->
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Personal Information -->
                    <div class="bg-gray-50 rounded-lg p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-5 flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-user-circle text-blue-600"></i>
                            </div>
                            Informasi Pribadi
                        </h3>
                        
                        <div class="space-y-5">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Nama Lengkap</h4>
                                <p class="text-gray-900 font-medium text-lg">{{ $petugasUKS->nama_petugas_uks }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">NIP</h4>
                                <p class="text-gray-900 font-medium">{{ $petugasUKS->NIP }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Alamat</h4>
                                <p class="text-gray-900">{{ $petugasUKS->alamat ?? 'Tidak ada data' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact & System Information -->
                    <div class="bg-gray-50 rounded-lg p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <h3 class="text-lg font-medium text-gray-900 mb-5 flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-address-card text-blue-600"></i>
                            </div>
                            Kontak & Sistem
                        </h3>
                        
                        <div class="space-y-5">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">No. Telepon</h4>
                                @if($petugasUKS->no_telp)
                                    <p class="text-gray-900 flex items-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-100 mr-2">
                                            <i class="fas fa-phone-alt text-green-600"></i>
                                        </span>
                                        {{ $petugasUKS->no_telp }}
                                    </p>
                                @else
                                    <p class="text-gray-500 italic flex items-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 mr-2">
                                            <i class="fas fa-phone-slash text-gray-400"></i>
                                        </span>
                                        Tidak ada data
                                    </p>
                                @endif
                            </div>

                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Level Akses</h4>
                                <p class="text-gray-900 flex items-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full {{ $petugasUKS->level == 'admin' ? 'bg-purple-100' : 'bg-blue-100' }} mr-2">
                                        <i class="fas {{ $petugasUKS->level == 'admin' ? 'fa-user-shield text-purple-600' : 'fa-user-nurse text-blue-600' }}"></i>
                                    </span>
                                    {{ $petugasUKS->level == 'admin' ? 'Admin' : 'Petugas' }}
                                </p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Tanggal Dibuat</h4>
                                <p class="text-gray-900 flex items-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 mr-2">
                                        <i class="fas fa-calendar-plus text-blue-600"></i>
                                    </span>
                                    {{ $petugasUKS->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Terakhir Diperbarui</h4>
                                <p class="text-gray-900 flex items-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 mr-2">
                                        <i class="fas fa-sync text-amber-600"></i>
                                    </span>
                                    {{ $petugasUKS->updated_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="mt-8 bg-white rounded-lg p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <h3 class="text-lg font-medium text-gray-900 mb-5 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-clipboard-list text-blue-600"></i>
                        </div>
                        Status & Aktivitas
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-2 text-xl font-semibold {{ $petugasUKS->status_aktif ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $petugasUKS->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                    </p>
                                </div>
                                <div class="{{ $petugasUKS->status_aktif ? 'bg-green-100' : 'bg-red-100' }} p-3 rounded-full">
                                    <i class="fas {{ $petugasUKS->status_aktif ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }} text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Level</p>
                                    <p class="mt-2 text-xl font-semibold {{ $petugasUKS->level == 'admin' ? 'text-purple-600' : 'text-blue-600' }}">
                                        {{ $petugasUKS->level == 'admin' ? 'Admin' : 'Petugas' }}
                                    </p>
                                </div>
                                <div class="{{ $petugasUKS->level == 'admin' ? 'bg-purple-100' : 'bg-blue-100' }} p-3 rounded-full">
                                    <i class="fas {{ $petugasUKS->level == 'admin' ? 'fa-user-shield text-purple-500' : 'fa-user-nurse text-blue-500' }} text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Login Terakhir</p>
                                    <p class="mt-2 text-sm font-semibold text-gray-600">-</p>
                                </div>
                                <div class="bg-gray-100 p-3 rounded-full">
                                    <i class="fas fa-user-clock text-gray-500 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-center mt-8 mb-6 space-x-4">
            <a href="{{ route('petugasuks.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-list mr-2"></i>
                Kembali ke Daftar
            </a>
            <a href="{{ route('petugasuks.edit', $petugasUKS->NIP) }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit Data
            </a>
        </div>
    </div>
@endsection