@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center text-lg font-semibold">
                        @php
                            $userName = session('username', 'User');
                            $initials = '';
                            $nameParts = explode(' ', $userName);
                            foreach($nameParts as $part) {
                                if(!empty($part)) {
                                    $initials .= substr($part, 0, 1);
                                    if(strlen($initials) >= 2) break;
                                }
                            }
                            echo strtoupper($initials ?: 'U');
                        @endphp
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
                        <p class="text-sm text-gray-600">
                            @if($userLevel === 'admin')
                                <i class="fas fa-user-shield text-gray-500 mr-1"></i> Administrator
                            @elseif($userLevel === 'petugas')
                                <i class="fas fa-clinic-medical text-green-500 mr-1"></i> Petugas UKS
                            @elseif($userLevel === 'dokter')
                                <i class="fas fa-user-md text-blue-500 mr-1"></i> Dokter
                            @elseif($userLevel === 'orang_tua')
                                <i class="fas fa-users text-purple-500 mr-1"></i> Orang Tua
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Profil Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Profile Info -->
        <div class="lg:col-span-2">
            @if($userLevel === 'admin' || $userLevel === 'petugas')
                <!-- Petugas UKS Profile -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-clinic-medical text-green-500 mr-2"></i>
                            Informasi {{ $userLevel === 'admin' ? 'Administrator' : 'Petugas UKS' }}
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">NIP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->NIP ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->nama_petugas_uks ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Telepon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->no_telp ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Level</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $profileData->level === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($profileData->level ?? 'petugas') }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->alamat ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($profileData->status_aktif)
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Tidak Aktif</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bergabung</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $profileData->created_at ? $profileData->created_at->format('d F Y') : '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

            @elseif($userLevel === 'dokter')
                <!-- Dokter Profile -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-user-md text-blue-500 mr-2"></i>
                            Informasi Dokter
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Dokter</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->Id_Dokter ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Dokter</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->Nama_Dokter ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Spesialisasi</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ $profileData->Spesialisasi ?? 'Umum' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Telepon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->No_Telp ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->Alamat ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($profileData->status_aktif)
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Tidak Aktif</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bergabung</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $profileData->created_at ? $profileData->created_at->format('d F Y') : '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

            @elseif($userLevel === 'orang_tua')
                <!-- Orang Tua Profile -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-users text-purple-500 mr-2"></i>
                            Informasi Orang Tua
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <h4 class="text-md font-medium text-gray-700 mb-3">
                                    <i class="fas fa-male text-blue-500 mr-2"></i>
                                    Data Ayah
                                </h4>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Ayah</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->nama_ayah ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Lahir</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $profileData->tanggal_lahir_ayah ? \Carbon\Carbon::parse($profileData->tanggal_lahir_ayah)->format('d F Y') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pekerjaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->pekerjaan_ayah ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pendidikan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->pendidikan_ayah ?? '-' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2 pt-4 border-t border-gray-200">
                                <h4 class="text-md font-medium text-gray-700 mb-3">
                                    <i class="fas fa-female text-pink-500 mr-2"></i>
                                    Data Ibu
                                </h4>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Ibu</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->nama_ibu ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Lahir</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $profileData->tanggal_lahir_ibu ? \Carbon\Carbon::parse($profileData->tanggal_lahir_ibu)->format('d F Y') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pekerjaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->pekerjaan_ibu ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pendidikan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->pendidikan_ibu ?? '-' }}</dd>
                            </div>
                            
                            <div class="sm:col-span-2 pt-4 border-t border-gray-200">
                                <h4 class="text-md font-medium text-gray-700 mb-3">
                                    <i class="fas fa-home text-gray-500 mr-2"></i>
                                    Kontak & Alamat
                                </h4>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Telepon</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->no_telp ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bergabung</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $profileData->created_at ? $profileData->created_at->format('d F Y') : '-' }}
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $profileData->alamat ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Account Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Status Akun
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Status</span>
                            @if($profileData->status_aktif ?? true)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                        
                        @if($userLevel === 'orang_tua' && $profileData->siswa)
                            <div class="pt-3 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-child text-blue-500 mr-1"></i>
                                    Data Siswa
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-500">Nama</span>
                                        <span class="text-xs text-gray-900">{{ $profileData->siswa->nama_siswa ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-500">ID Siswa</span>
                                        <span class="text-xs text-gray-900">{{ $profileData->siswa->id_siswa ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Terakhir Update</span>
                                <span class="text-sm text-gray-900">
                                    {{ $profileData->updated_at ? $profileData->updated_at->diffForHumans() : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Aksi Cepat
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        <a href="{{ route('profile.edit') }}" class="w-full flex items-center px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-md transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Profil
                        </a>
                        <a href="{{ route('change.password') }}" class="w-full flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md transition-colors duration-200">
                            <i class="fas fa-key mr-2"></i>
                            Ganti Password
                        </a>
                        <a href="{{ route('dashboard') }}" class="w-full flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md transition-colors duration-200">
                            <i class="fas fa-home mr-2"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection