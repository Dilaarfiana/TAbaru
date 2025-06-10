@extends('layouts.app')

@section('page_title', 'Detail Screening Kesehatan')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-7xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-file-medical text-teal-500 mr-3 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Screening Kesehatan</h2>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-600 mr-2">Pemeriksaan untuk:</span>
                        <span class="bg-teal-100 text-teal-800 text-sm font-bold py-1 px-3 rounded-full">
                            {{ $siswa->nama_siswa }}
                        </span>
                        @if($isDokter)
                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                <i class="fas fa-stethoscope mr-1"></i>Akses Dokter (Read Only)
                            </span>
                        @elseif($isPetugas)
                            <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                            </span>
                        @elseif($isAdmin)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                <i class="fas fa-user-shield mr-1"></i>Akses Admin
                            </span>
                        @elseif($isOrangTua)
                            <span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                <i class="fas fa-heart mr-1"></i>Orang Tua
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @php
                    if ($isAdmin) {
                        $backRoute = route('laporan.screening');
                    } elseif ($isPetugas) {
                        $backRoute = route('petugas.laporan.screening');
                    } elseif ($isDokter) {
                        $backRoute = route('dokter.laporan.screening');
                    } else {
                        $backRoute = route('orangtua.laporan.screening');
                    }
                @endphp
                
                <a href="{{ $backRoute }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
            <!-- Access Level Info -->
            @if($isDokter)
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-green-800 mb-1">Akses Dokter</h3>
                        <p class="text-sm text-green-700">
                            Anda dapat melihat seluruh data screening untuk keperluan medis, namun <span class="font-semibold text-red-600">tidak dapat mengubah data</span>.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isPetugas)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-yellow-800 mb-1">Akses Petugas UKS</h3>
                        <p class="text-sm text-yellow-700">
                            Anda dapat melihat dan mencetak data screening kesehatan untuk keperluan pelayanan kesehatan siswa.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isAdmin)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-blue-800 mb-1">Akses Administrator</h3>
                        <p class="text-sm text-blue-700">
                            Anda memiliki akses penuh untuk melihat, mencetak, dan mengelola data screening kesehatan.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isOrangTua)
            <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-heart text-purple-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-md font-medium text-purple-800 mb-1">Data Anak Anda</h3>
                        <p class="text-sm text-purple-700">
                            Anda dapat melihat hasil screening kesehatan anak Anda untuk memantau kondisi kesehatan.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Info Banner -->
            <div class="bg-teal-50 border-l-4 border-teal-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-medical text-teal-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-teal-800 mb-1">Screening Kesehatan</h3>
                        <p class="text-sm text-teal-700 mb-2">
                            Hasil lengkap pemeriksaan screening kesehatan termasuk tanda vital, antropometri, dan pemeriksaan fisik.
                        </p>
                        
                        <!-- Metadata Info -->
                        <div class="mt-2 p-2 bg-teal-100 border border-teal-300 rounded text-xs">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <div>
                                    <span class="font-medium text-teal-800">Tanggal:</span>
                                    <span class="text-teal-700">{{ \Carbon\Carbon::parse($detailPemeriksaan->tanggal_jam)->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-teal-800">Waktu:</span>
                                    <span class="text-teal-700">{{ \Carbon\Carbon::parse($detailPemeriksaan->tanggal_jam)->format('H:i') }} WIB</span>
                                </div>
                                <div>
                                    <span class="font-medium text-teal-800">Status:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $detailPemeriksaan->status_pemeriksaan == 'lengkap' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas {{ $detailPemeriksaan->status_pemeriksaan == 'lengkap' ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                        {{ $detailPemeriksaan->status_pemeriksaan == 'lengkap' ? 'Lengkap' : 'Belum Lengkap' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-teal-800">Akses Anda:</span>
                                    <span class="text-teal-700">
                                        @if($isAdmin)
                                            Administrator
                                        @elseif($isPetugas)
                                            Petugas UKS
                                        @elseif($isDokter)
                                            Dokter (Read Only)
                                        @elseif($isOrangTua)
                                            Orang Tua
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Informasi Utama -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <!-- Data Siswa -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm lg:col-span-2">
                    <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                        <i class="fas fa-user-graduate text-blue-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Siswa</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-user text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Nama Lengkap</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->nama_siswa }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-id-card text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">NIS</p>
                                    <p class="font-medium text-gray-800">{{ $siswa->id_siswa }}</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-graduation-cap text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Kelas</p>
                                    <p class="font-medium text-gray-800">
                                        {{ $siswa->detailSiswa->kelas->Nama_Kelas ?? 'Belum ada kelas' }}
                                        @if($siswa->detailSiswa->kelas->jurusan ?? null)
                                            <span class="text-xs text-gray-500 block">({{ $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan }})</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-birthday-cake text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Lahir</p>
                                    <p class="font-medium text-gray-800">
                                        {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') : 'Tidak diketahui' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-venus-mars text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Jenis Kelamin</p>
                                    <p class="font-medium text-gray-800">
                                        @if($siswa->jenis_kelamin == 'L')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                <i class="fas fa-male mr-1"></i>Laki-laki
                                            </span>
                                        @elseif($siswa->jenis_kelamin == 'P')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-pink-100 text-pink-800">
                                                <i class="fas fa-female mr-1"></i>Perempuan
                                            </span>
                                        @else
                                            Tidak diketahui
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-clock text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Usia</p>
                                    <p class="font-medium text-gray-800">
                                        {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->age . ' tahun' : 'Tidak diketahui' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Dokter -->
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                        <i class="fas fa-user-md text-green-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Dokter Pemeriksa</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-stethoscope text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Nama Dokter</p>
                                <p class="font-medium text-gray-800">{{ $detailPemeriksaan->dokter->Nama_Dokter ?? 'Belum ditentukan' }}</p>
                            </div>
                        </div>
                        @if($detailPemeriksaan->dokter && $detailPemeriksaan->dokter->Spesialisasi)
                        <div class="flex items-start">
                            <i class="fas fa-medal text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Spesialisasi</p>
                                <p class="font-medium text-gray-800">{{ $detailPemeriksaan->dokter->Spesialisasi }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Petugas UKS -->
                <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-purple-200 pb-2">
                        <i class="fas fa-user-nurse text-purple-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Petugas UKS</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-user text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Nama Petugas</p>
                                <p class="font-medium text-gray-800">{{ $detailPemeriksaan->petugasUks->nama_petugas_uks ?? 'Belum ditentukan' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pemeriksaan Awal - Tanda Vital -->
            @if($detailPemeriksaan->pemeriksaanAwal)
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-thermometer-half text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Tanda Vital</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    @if($detailPemeriksaan->pemeriksaanAwal->suhu)
                    <div class="bg-red-50 border border-red-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-thermometer-half text-red-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Suhu Tubuh</h4>
                        <p class="text-2xl font-bold text-red-600">{{ $detailPemeriksaan->pemeriksaanAwal->suhu }}°C</p>
                        <p class="text-xs mt-1 {{ $detailPemeriksaan->pemeriksaanAwal->suhu >= 37.5 ? 'text-red-600' : ($detailPemeriksaan->pemeriksaanAwal->suhu <= 35.5 ? 'text-blue-600' : 'text-green-600') }}">
                            @if($detailPemeriksaan->pemeriksaanAwal->suhu >= 37.5)
                                Demam
                            @elseif($detailPemeriksaan->pemeriksaanAwal->suhu <= 35.5)
                                Hipotermia
                            @else
                                Normal
                            @endif
                        </p>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanAwal->nadi)
                    <div class="bg-pink-50 border border-pink-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-heartbeat text-pink-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Nadi</h4>
                        <p class="text-2xl font-bold text-pink-600">{{ $detailPemeriksaan->pemeriksaanAwal->nadi }}</p>
                        <p class="text-xs mt-1 text-gray-500">bpm</p>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanAwal->tegangan)
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-tachometer-alt text-blue-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Tekanan Darah</h4>
                        <p class="text-2xl font-bold text-blue-600">{{ $detailPemeriksaan->pemeriksaanAwal->tegangan }}</p>
                        <p class="text-xs mt-1 text-gray-500">mmHg</p>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanAwal->pernapasan)
                    <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-lungs text-green-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Pernapasan</h4>
                        <p class="text-2xl font-bold text-green-600">{{ $detailPemeriksaan->pemeriksaanAwal->pernapasan }}</p>
                        <p class="text-xs mt-1 text-gray-500">per menit</p>
                    </div>
                    @endif
                </div>

                <!-- Additional Information -->
                @if($detailPemeriksaan->pemeriksaanAwal->keluhan_dahulu || $detailPemeriksaan->pemeriksaanAwal->pemeriksaan)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    @if($detailPemeriksaan->pemeriksaanAwal->keluhan_dahulu)
                    <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-history text-yellow-500 mr-2"></i>
                            <h4 class="font-semibold text-yellow-800">Keluhan Dahulu</h4>
                        </div>
                        <div class="bg-white p-3 rounded-md border border-yellow-200">
                            <p class="text-gray-700">{{ $detailPemeriksaan->pemeriksaanAwal->keluhan_dahulu }}</p>
                        </div>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanAwal->pemeriksaan)
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-stethoscope text-blue-500 mr-2"></i>
                            <h4 class="font-semibold text-blue-800">Hasil Pemeriksaan</h4>
                        </div>
                        <div class="bg-white p-3 rounded-md border border-blue-200">
                            <p class="text-gray-700">{{ $detailPemeriksaan->pemeriksaanAwal->pemeriksaan }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Pain Assessment -->
                @if($detailPemeriksaan->pemeriksaanAwal->status_nyeri)
                <div class="bg-red-50 border border-red-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <h4 class="font-semibold text-red-800">Penilaian Nyeri</h4>
                    </div>
                    <div class="bg-white p-4 rounded-md border border-red-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <strong class="text-red-600">Tingkat Nyeri:</strong>
                                <div class="flex items-center mt-1">
                                    @for($i = 1; $i <= 10; $i++)
                                        @if($i <= $detailPemeriksaan->pemeriksaanAwal->status_nyeri)
                                            <span class="text-red-500 text-lg">●</span>
                                        @else
                                            <span class="text-gray-300 text-lg">○</span>
                                        @endif
                                    @endfor
                                    <span class="ml-2 font-bold">{{ $detailPemeriksaan->pemeriksaanAwal->status_nyeri }}/10</span>
                                </div>
                            </div>
                            @if($detailPemeriksaan->pemeriksaanAwal->karakteristik)
                            <div>
                                <strong class="text-red-600">Karakteristik:</strong>
                                <div class="mt-1">{{ $detailPemeriksaan->pemeriksaanAwal->karakteristik }}</div>
                            </div>
                            @endif
                            @if($detailPemeriksaan->pemeriksaanAwal->lokasi)
                            <div>
                                <strong class="text-red-600">Lokasi:</strong>
                                <div class="mt-1">{{ $detailPemeriksaan->pemeriksaanAwal->lokasi }}</div>
                            </div>
                            @endif
                            @if($detailPemeriksaan->pemeriksaanAwal->durasi)
                            <div>
                                <strong class="text-red-600">Durasi:</strong>
                                <div class="mt-1">{{ $detailPemeriksaan->pemeriksaanAwal->durasi }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Pemeriksaan Fisik & Antropometri -->
            @if($detailPemeriksaan->pemeriksaanFisik)
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-child text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Pemeriksaan Fisik & Antropometri</h3>
                </div>
                
                <!-- Physical Measurements -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    @if($detailPemeriksaan->pemeriksaanFisik->tinggi_badan)
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-ruler-vertical text-blue-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Tinggi Badan</h4>
                        <p class="text-2xl font-bold text-blue-600">{{ $detailPemeriksaan->pemeriksaanFisik->tinggi_badan }}</p>
                        <p class="text-xs mt-1 text-gray-500">cm</p>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanFisik->berat_badan)
                    <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-weight text-green-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Berat Badan</h4>
                        <p class="text-2xl font-bold text-green-600">{{ $detailPemeriksaan->pemeriksaanFisik->berat_badan }}</p>
                        <p class="text-xs mt-1 text-gray-500">kg</p>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanFisik->lingkar_kepala)
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-circle text-purple-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Lingkar Kepala</h4>
                        <p class="text-2xl font-bold text-purple-600">{{ $detailPemeriksaan->pemeriksaanFisik->lingkar_kepala }}</p>
                        <p class="text-xs mt-1 text-gray-500">cm</p>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanFisik->lingkar_lengan_atas)
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-5 shadow-sm text-center">
                        <div class="flex items-center justify-center mb-3">
                            <i class="fas fa-ruler text-orange-500 text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Lingkar Lengan Atas</h4>
                        <p class="text-2xl font-bold text-orange-600">{{ $detailPemeriksaan->pemeriksaanFisik->lingkar_lengan_atas }}</p>
                        <p class="text-xs mt-1 text-gray-500">cm</p>
                    </div>
                    @endif
                </div>

                <!-- BMI Calculation -->
                @if($detailPemeriksaan->pemeriksaanFisik->tinggi_badan && $detailPemeriksaan->pemeriksaanFisik->berat_badan)
                @php
                    $tinggi_m = $detailPemeriksaan->pemeriksaanFisik->tinggi_badan / 100;
                    $bmi = round($detailPemeriksaan->pemeriksaanFisik->berat_badan / ($tinggi_m * $tinggi_m), 1);
                    
                    if ($bmi < 18.5) {
                        $bmi_status = 'Underweight';
                        $bmi_color = 'text-blue-600';
                        $bg_color = 'bg-blue-100';
                        $border_color = 'border-blue-200';
                    } elseif ($bmi < 25) {
                        $bmi_status = 'Normal';
                        $bmi_color = 'text-green-600';
                        $bg_color = 'bg-green-100';
                        $border_color = 'border-green-200';
                    } elseif ($bmi < 30) {
                        $bmi_status = 'Overweight';
                        $bmi_color = 'text-yellow-600';
                        $bg_color = 'bg-yellow-100';
                        $border_color = 'border-yellow-200';
                    } else {
                        $bmi_status = 'Obese';
                        $bmi_color = 'text-red-600';
                        $bg_color = 'bg-red-100';
                        $border_color = 'border-red-200';
                    }
                @endphp
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 shadow-sm mb-4">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-calculator text-gray-500 mr-2"></i>
                        <h4 class="font-semibold text-gray-800">Indeks Massa Tubuh (BMI)</h4>
                    </div>
                    <div class="bg-white p-4 rounded-md border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="text-3xl font-bold {{ $bmi_color }} mr-4">{{ $bmi }}</span>
                                <div class="px-3 py-1 rounded-full text-sm font-medium {{ $bg_color }} {{ $bmi_color }} border {{ $border_color }}">
                                    {{ $bmi_status }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 text-right">
                                <div>Tinggi: {{ $detailPemeriksaan->pemeriksaanFisik->tinggi_badan }} cm</div>
                                <div>Berat: {{ $detailPemeriksaan->pemeriksaanFisik->berat_badan }} kg</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Physical Examination Results -->
                @php
                    $organs = [
                        'dada' => ['icon' => 'fas fa-lungs', 'color' => 'blue', 'name' => 'Dada'],
                        'jantung' => ['icon' => 'fas fa-heart', 'color' => 'red', 'name' => 'Jantung'],
                        'paru' => ['icon' => 'fas fa-lungs', 'color' => 'teal', 'name' => 'Paru-paru'],
                        'perut' => ['icon' => 'fas fa-circle', 'color' => 'yellow', 'name' => 'Perut'],
                        'hepar' => ['icon' => 'fas fa-prescription-bottle', 'color' => 'orange', 'name' => 'Hepar'],
                        'anogenital' => ['icon' => 'fas fa-circle', 'color' => 'pink', 'name' => 'Anogenital'],
                        'ekstremitas' => ['icon' => 'fas fa-walking', 'color' => 'green', 'name' => 'Ekstremitas'],
                        'kepala' => ['icon' => 'fas fa-head-side-virus', 'color' => 'purple', 'name' => 'Kepala']
                    ];
                @endphp

                @php
                    $hasOrganData = false;
                    foreach($organs as $organ => $config) {
                        if($detailPemeriksaan->pemeriksaanFisik->$organ) {
                            $hasOrganData = true;
                            break;
                        }
                    }
                @endphp

                @if($hasOrganData)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    @foreach($organs as $organ => $config)
                        @if($detailPemeriksaan->pemeriksaanFisik->$organ)
                        <div class="bg-{{ $config['color'] }}-50 border border-{{ $config['color'] }}-100 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start">
                                <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">{{ $config['name'] }}</p>
                                    <p class="font-medium text-gray-800">{{ $detailPemeriksaan->pemeriksaanFisik->$organ }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                <!-- Additional Physical Examination Info -->
                @if($detailPemeriksaan->pemeriksaanFisik->pemeriksaan_penunjang || $detailPemeriksaan->pemeriksaanFisik->masalah_aktif || $detailPemeriksaan->pemeriksaanFisik->rencana_medis_dan_terapi)
                <div class="space-y-4">
                    @if($detailPemeriksaan->pemeriksaanFisik->pemeriksaan_penunjang)
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-microscope text-blue-500 mr-2"></i>
                            <h4 class="font-semibold text-blue-800">Pemeriksaan Penunjang</h4>
                        </div>
                        <div class="bg-white p-3 rounded-md border border-blue-200">
                            <p class="text-gray-700">{{ $detailPemeriksaan->pemeriksaanFisik->pemeriksaan_penunjang }}</p>
                        </div>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanFisik->masalah_aktif)
                    <div class="bg-red-50 border border-red-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <h4 class="font-semibold text-red-800">Masalah Aktif</h4>
                        </div>
                        <div class="bg-white p-3 rounded-md border border-red-200">
                            <p class="text-gray-700 font-medium">{{ $detailPemeriksaan->pemeriksaanFisik->masalah_aktif }}</p>
                        </div>
                    </div>
                    @endif

                    @if($detailPemeriksaan->pemeriksaanFisik->rencana_medis_dan_terapi)
                    <div class="bg-green-50 border border-green-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-pills text-green-500 mr-2"></i>
                            <h4 class="font-semibold text-green-800">Rencana Medis dan Terapi</h4>
                        </div>
                        <div class="bg-white p-3 rounded-md border border-green-200">
                            <p class="text-gray-700">{{ $detailPemeriksaan->pemeriksaanFisik->rencana_medis_dan_terapi }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Rekam Medis -->
            @if(isset($rekamMedis) && $rekamMedis)
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-notes-medical text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Rekam Medis Terkait</h3>
                </div>
                
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-calendar-alt text-blue-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal Rekam Medis</p>
                                <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('d F Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-user-md text-blue-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Dokter</p>
                                <p class="font-medium text-gray-800">{{ $rekamMedis->dokter->Nama_Dokter ?? 'Tidak ada dokter' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($rekamMedis->Keluhan_Utama)
                    <div class="bg-white p-4 rounded-md border border-blue-200 mb-3">
                        <h4 class="font-semibold text-blue-800 mb-2">Keluhan Utama</h4>
                        <p class="text-gray-700">{{ $rekamMedis->Keluhan_Utama }}</p>
                    </div>
                    @endif
                    
                    @if($rekamMedis->Riwayat_Penyakit_Sekarang)
                    <div class="bg-white p-4 rounded-md border border-blue-200 mb-3">
                        <h4 class="font-semibold text-blue-800 mb-2">Riwayat Penyakit Sekarang</h4>
                        <p class="text-gray-700">{{ $rekamMedis->Riwayat_Penyakit_Sekarang }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Resep Obat -->
            @php
                $resepObat = \App\Models\Resep::with('dokter')
                    ->where('Id_Siswa', $siswa->id_siswa)
                    ->whereDate('Tanggal_Resep', \Carbon\Carbon::parse($detailPemeriksaan->tanggal_jam)->format('Y-m-d'))
                    ->orderBy('Tanggal_Resep', 'desc')
                    ->get();
            @endphp

            @if($resepObat->count() > 0)
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-pills text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Resep Obat</h3>
                </div>
                
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        @foreach($resepObat as $index => $resep)
                        <div class="bg-white border border-green-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-green-700 text-lg">{{ $resep->Nama_Obat }}</h4>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">#{{ $index + 1 }}</span>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start">
                                    <i class="fas fa-prescription-bottle text-green-600 mr-2 w-4 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Dosis</p>
                                        <p class="font-medium text-gray-800">{{ $resep->Dosis }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-clock text-green-600 mr-2 w-4 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Durasi</p>
                                        <p class="font-medium text-gray-800">{{ $resep->Durasi }}</p>
                                    </div>
                                </div>
                                @if($resep->dokter)
                                <div class="flex items-start">
                                    <i class="fas fa-user-md text-green-600 mr-2 w-4 mt-1"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Dokter</p>
                                        <p class="font-medium text-gray-800">{{ $resep->dokter->Nama_Dokter }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Important Notes -->
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-0.5"></i>
                            <div class="text-sm">
                                <strong class="text-yellow-800">Catatan Penting:</strong>
                                <ul class="mt-2 space-y-1 text-yellow-700 text-xs">
                                    <li>• Gunakan obat sesuai dosis yang telah ditentukan</li>
                                    <li>• Jangan hentikan pengobatan tanpa konsultasi dokter</li>
                                    <li>• Jika ada efek samping, segera hubungi dokter</li>
                                    <li>• Simpan obat di tempat yang aman dan sejuk</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Summary & Recommendation -->
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-clipboard-check text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Ringkasan & Rekomendasi</h3>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Status Summary -->
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 shadow-sm">
                        <h4 class="font-medium text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-chart-pie text-blue-500 mr-2"></i>Status Pemeriksaan
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pemeriksaan Awal:</span>
                                <span class="{{ $detailPemeriksaan->pemeriksaanAwal ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $detailPemeriksaan->pemeriksaanAwal ? 'Lengkap' : 'Belum Lengkap' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pemeriksaan Fisik:</span>
                                <span class="{{ $detailPemeriksaan->pemeriksaanFisik ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $detailPemeriksaan->pemeriksaanFisik ? 'Lengkap' : 'Belum Lengkap' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Rekam Medis:</span>
                                <span class="{{ isset($rekamMedis) && $rekamMedis ? 'text-green-600' : 'text-red-600' }}">
                                    {{ isset($rekamMedis) && $rekamMedis ? 'Ada' : 'Tidak Ada' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Resep Obat:</span>
                                <span class="{{ $resepObat->count() > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $resepObat->count() > 0 ? $resepObat->count() . ' jenis obat' : 'Tidak Ada' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Health Recommendations -->
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 shadow-sm">
                        <h4 class="font-medium text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Rekomendasi Kesehatan
                        </h4>
                        <div class="space-y-2 text-sm">
                            @if($detailPemeriksaan->pemeriksaanFisik && isset($bmi))
                                @if($bmi < 18.5)
                                    <div class="text-blue-700">• Pertahankan pola makan bergizi untuk mencapai berat badan ideal</div>
                                @elseif($bmi >= 25)
                                    <div class="text-orange-700">• Perhatikan pola makan dan tingkatkan aktivitas fisik</div>
                                @else
                                    <div class="text-green-700">• Pertahankan pola hidup sehat yang sudah baik</div>
                                @endif
                            @endif
                            
                            @if($detailPemeriksaan->pemeriksaanAwal && $detailPemeriksaan->pemeriksaanAwal->suhu >= 37.5)
                                <div class="text-red-700">• Istirahat yang cukup dan konsumsi cairan yang banyak</div>
                            @endif
                            
                            <div class="text-gray-700">• Konsumsi makanan bergizi seimbang</div>
                            <div class="text-gray-700">• Minum air putih minimal 8 gelas per hari</div>
                            <div class="text-gray-700">• Lakukan olahraga teratur minimal 30 menit per hari</div>
                            <div class="text-gray-700">• Tidur yang cukup 7-9 jam per hari</div>
                            
                            @if($resepObat->count() > 0)
                                <div class="text-indigo-700">• Minum obat sesuai petunjuk dokter</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ $backRoute }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-gray-500"></i>
                    Kembali ke Daftar
                </a>
                
                <div class="flex space-x-2">
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Cetak
                    </button>
                    
                    @php
                        if ($isAdmin) {
                            $pdfRoute = route('laporan.screening.pdf', ['siswaId' => $siswa->id_siswa]);
                        } elseif ($isPetugas) {
                            $pdfRoute = route('petugas.laporan.screening.pdf', ['siswaId' => $siswa->id_siswa]);
                        } elseif ($isDokter) {
                            $pdfRoute = route('dokter.laporan.screening.pdf', ['siswaId' => $siswa->id_siswa]);
                        } else {
                            $pdfRoute = route('orangtua.laporan.screening.pdf');
                        }
                    @endphp
                    
                    <a href="{{ $pdfRoute }}?detail_pemeriksaan_id={{ $detailPemeriksaan->id_detprx }}" target="_blank" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Download PDF
                    </a>
                    
                    @if(!$isOrangTua)
                    <button onclick="shareDetail()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-purple-500 hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>
                        Bagikan
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { font-size: 12pt; }
    .p-6 { padding: 1rem !important; }
    .shadow-md, .shadow-sm { box-shadow: none !important; }
    .border-b { border-bottom: 1px solid #e5e7eb !important; }
    
    .bg-teal-50, .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50, .bg-purple-50, .bg-gray-50 {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
    }
}

@media (max-width: 768px) {
    .grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .text-2xl {
        font-size: 1.25rem !important;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Print functionality
    const printButton = document.querySelector('[onclick="window.print()"]');
    if (printButton) {
        printButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Hide action buttons when printing
            const actionSection = document.querySelector('.border-t.border-gray-200').parentElement;
            if (actionSection) {
                actionSection.style.display = 'none';
            }
            
            // Print
            window.print();
            
            // Show action buttons after printing
            setTimeout(() => {
                if (actionSection) {
                    actionSection.style.display = 'block';
                }
            }, 1000);
        });
    }
});

// Share functionality
function shareDetail() {
    if (navigator.share) {
        navigator.share({
            title: 'Detail Screening Kesehatan - {{ $siswa->nama_siswa }}',
            text: 'Detail pemeriksaan screening kesehatan',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('Link berhasil disalin ke clipboard!');
        }).catch(() => {
            // Manual fallback
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Link berhasil disalin ke clipboard!');
        });
    }
}
</script>
@endpush
@endsection