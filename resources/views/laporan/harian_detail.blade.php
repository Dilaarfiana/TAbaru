@extends('layouts.app')

@section('page_title', 'Detail Pemeriksaan Harian')

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
                <i class="fas fa-file-medical-alt text-teal-500 mr-3 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Pemeriksaan Harian</h2>
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
                        $backRoute = 'laporan.harian';
                    } elseif ($isPetugas) {
                        $backRoute = 'petugas.laporan.harian';
                    } elseif ($isDokter) {
                        $backRoute = 'dokter.laporan.harian';
                    } else {
                        $backRoute = 'orangtua.laporan.harian';
                    }
                @endphp
                
                <a href="{{ route($backRoute) }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
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
                            Anda dapat melihat seluruh data pemeriksaan harian untuk keperluan medis, namun <span class="font-semibold text-red-600">tidak dapat mengubah data</span>.
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
                            Anda dapat melihat dan mencetak data pemeriksaan harian untuk keperluan pelayanan kesehatan siswa.
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
                            Anda memiliki akses penuh untuk melihat, mencetak, dan mengelola data pemeriksaan harian.
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
                            Anda dapat melihat hasil pemeriksaan harian anak Anda untuk memantau kondisi kesehatan.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Info Banner -->
            <div class="bg-teal-50 border-l-4 border-teal-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-medical-alt text-teal-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-teal-800 mb-1">Pemeriksaan Harian</h3>
                        <p class="text-sm text-teal-700 mb-2">
                            Hasil pemeriksaan harian yang dilakukan oleh petugas UKS untuk memantau kondisi kesehatan siswa.
                        </p>
                        
                        <!-- Metadata Info -->
                        <div class="mt-2 p-2 bg-teal-100 border border-teal-300 rounded text-xs">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <div>
                                    <span class="font-medium text-teal-800">Tanggal:</span>
                                    <span class="text-teal-700">{{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-teal-800">Waktu:</span>
                                    <span class="text-teal-700">{{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('H:i') }} WIB</span>
                                </div>
                                <div>
                                    <span class="font-medium text-teal-800">ID Pemeriksaan:</span>
                                    <span class="text-teal-700">{{ $pemeriksaanHarian->Id_Harian }}</span>
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Data Siswa -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                        <i class="fas fa-user-graduate text-blue-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Siswa</h3>
                    </div>
                    
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
                        <div class="flex items-start">
                            <i class="fas fa-birthday-cake text-blue-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Usia</p>
                                <p class="font-medium text-gray-800">
                                    {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->age . ' tahun' : 'Tidak diketahui' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Status</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $siswa->status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ $siswa->status_aktif ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                    {{ $siswa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Waktu Pemeriksaan -->
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                        <i class="fas fa-clock text-green-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Waktu Pemeriksaan</h3>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="text-3xl font-bold text-green-700 mb-1">
                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('d') }}
                        </div>
                        <div class="text-green-600 font-medium">
                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('M Y') }}
                        </div>
                        <div class="text-sm text-green-500">
                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('l') }}
                        </div>
                        <div class="text-lg font-semibold text-green-700 mt-2">
                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('H:i') }} WIB
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->diffForHumans() }}</span>
                        </div>
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
                                <p class="font-medium text-gray-800">{{ $pemeriksaanHarian->petugasUks->nama_petugas_uks ?? 'Belum ditentukan' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-id-badge text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">NIP</p>
                                <p class="font-medium text-gray-800">{{ $pemeriksaanHarian->NIP ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-purple-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Status</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Selesai
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hasil Pemeriksaan -->
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-clipboard-check text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Hasil Pemeriksaan Harian</h3>
                </div>
                
                <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm">
                    @if($pemeriksaanHarian->Hasil_Pemeriksaan)
                        <div class="bg-white p-4 rounded-md border border-purple-200">
                            <div class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $pemeriksaanHarian->Hasil_Pemeriksaan }}</div>
                        </div>
                        
                        <!-- Analysis (untuk Dokter) -->
                        @if($isDokter)
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h4 class="text-sm font-semibold text-green-800 mb-2 flex items-center">
                                <i class="fas fa-stethoscope text-green-600 mr-2"></i>
                                Analisis Medis (Mode Dokter)
                            </h4>
                            <div class="text-sm text-green-700">
                                @php
                                    $hasilLower = strtolower($pemeriksaanHarian->Hasil_Pemeriksaan);
                                    $hasSymptoms = (
                                        strpos($hasilLower, 'sakit') !== false || 
                                        strpos($hasilLower, 'demam') !== false ||
                                        strpos($hasilLower, 'pusing') !== false ||
                                        strpos($hasilLower, 'mual') !== false ||
                                        strpos($hasilLower, 'batuk') !== false ||
                                        strpos($hasilLower, 'pilek') !== false ||
                                        strpos($hasilLower, 'keluhan') !== false
                                    );
                                @endphp
                                
                                @if($hasSymptoms)
                                    <div class="flex items-start space-x-2">
                                        <i class="fas fa-exclamation-triangle text-orange-500 mt-1"></i>
                                        <div>
                                            <div class="font-medium">Perlu Perhatian Khusus</div>
                                            <div>Hasil pemeriksaan menunjukkan adanya gejala yang perlu pemantauan lebih lanjut. Disarankan untuk konsultasi lebih lanjut atau tindak lanjut medis.</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start space-x-2">
                                        <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                        <div>
                                            <div class="font-medium">Kondisi Normal</div>
                                            <div>Hasil pemeriksaan dalam batas normal. Siswa dapat melanjutkan aktivitas sekolah seperti biasa.</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="bg-white p-8 rounded-md border border-purple-200 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-file-medical text-4xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-500 mb-2">Belum Ada Hasil Pemeriksaan</h4>
                            <p class="text-gray-400">Hasil pemeriksaan harian belum diinput oleh petugas UKS</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Rekomendasi dan Tindak Lanjut -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Rekomendasi Umum -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                        <i class="fas fa-lightbulb text-blue-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Rekomendasi Umum</h3>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        @if($pemeriksaanHarian->Hasil_Pemeriksaan && (strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'sakit') !== false || strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'keluhan') !== false))
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-orange-500 mt-1 text-xs"></i>
                                <span class="text-gray-700">Pemantauan kondisi kesehatan secara berkala</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-orange-500 mt-1 text-xs"></i>
                                <span class="text-gray-700">Koordinasi dengan orang tua terkait kondisi anak</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-orange-500 mt-1 text-xs"></i>
                                <span class="text-gray-700">Konsultasi ke dokter jika gejala berlanjut</span>
                            </div>
                        @else
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-green-500 mt-1 text-xs"></i>
                                <span class="text-gray-700">Pertahankan pola hidup sehat</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-green-500 mt-1 text-xs"></i>
                                <span class="text-gray-700">Konsumsi makanan bergizi seimbang</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-green-500 mt-1 text-xs"></i>
                                <span class="text-gray-700">Olahraga ringan secara teratur</span>
                            </div>
                        @endif
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check-circle text-blue-500 mt-1 text-xs"></i>
                            <span class="text-gray-700">Jaga kebersihan tangan dan lingkungan</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check-circle text-blue-500 mt-1 text-xs"></i>
                            <span class="text-gray-700">Minum air putih minimal 8 gelas per hari</span>
                        </div>
                    </div>
                </div>
                
                <!-- Status Tindak Lanjut -->
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-orange-200 pb-2">
                        <i class="fas fa-tasks text-orange-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Status Tindak Lanjut</h3>
                    </div>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Perlu Tindak Lanjut:</span>
                            @if($pemeriksaanHarian->Hasil_Pemeriksaan && (strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'sakit') !== false || strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'keluhan') !== false))
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>YA
                                </span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>TIDAK
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pemeriksaan Ulang:</span>
                            <span class="text-gray-800 font-medium">
                                @if($pemeriksaanHarian->Hasil_Pemeriksaan && strlen($pemeriksaanHarian->Hasil_Pemeriksaan) > 50)
                                    1-2 hari
                                @else
                                    Rutin harian
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Kontak Orang Tua:</span>
                            @if($pemeriksaanHarian->Hasil_Pemeriksaan && (strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'sakit') !== false || strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'demam') !== false))
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-phone mr-1"></i>SUDAH
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                    <i class="fas fa-minus mr-1"></i>TIDAK PERLU
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prioritas:</span>
                            @if($pemeriksaanHarian->Hasil_Pemeriksaan && (strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'sakit') !== false || strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'demam') !== false))
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-exclamation mr-1"></i>TINGGI
                                </span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check mr-1"></i>NORMAL
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pemeriksaan Lainnya -->
            @if(isset($riwayatHarian) && $riwayatHarian->count() > 0)
            <div class="mb-6">
                <div class="flex items-center mb-4 border-b pb-2">
                    <i class="fas fa-history text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Riwayat Pemeriksaan Terbaru</h3>
                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">{{ $riwayatHarian->count() }} data</span>
                </div>
                
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 shadow-sm">
                    <div class="space-y-3">
                        @foreach($riwayatHarian as $index => $riwayat)
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">#{{ $index + 1 }}</span>
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($riwayat->Tanggal_Jam)->format('d M Y, H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ({{ \Carbon\Carbon::parse($riwayat->Tanggal_Jam)->diffForHumans() }})
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-user-tie mr-1"></i>
                                    {{ $riwayat->petugasUks->nama_petugas_uks ?? 'Tidak ada petugas' }}
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $riwayat->Hasil_Pemeriksaan ? (strlen($riwayat->Hasil_Pemeriksaan) > 200 ? substr($riwayat->Hasil_Pemeriksaan, 0, 200) . '...' : $riwayat->Hasil_Pemeriksaan) : 'Belum ada hasil pemeriksaan' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route($backRoute) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
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
                            $pdfRoute = 'laporan.harian.pdf';
                        } elseif ($isPetugas) {
                            $pdfRoute = 'petugas.laporan.harian.pdf';
                        } elseif ($isDokter) {
                            $pdfRoute = 'dokter.laporan.harian.pdf';
                        } else {
                            $pdfRoute = 'orangtua.laporan.harian.pdf';
                        }
                    @endphp
                    
                    <a href="{{ route($pdfRoute, ['siswaId' => $siswa->id_siswa]) }}?pemeriksaan_harian_id={{ $pemeriksaanHarian->Id_Harian }}" target="_blank" 
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
    
    .bg-teal-50, .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50, .bg-purple-50, .bg-gray-50, .bg-orange-50 {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
    }
}

@media (max-width: 768px) {
    .grid-cols-3 {
        grid-template-columns: repeat(1, 1fr) !important;
    }
    
    .text-3xl {
        font-size: 1.5rem !important;
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
            title: 'Detail Pemeriksaan Harian - {{ $siswa->nama_siswa }}',
            text: 'Detail pemeriksaan harian siswa',
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

// Print detail function
function printDetail() {
    window.print();
}
</script>
@endpush
@endsection