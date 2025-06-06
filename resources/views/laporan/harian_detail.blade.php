@extends('layouts.app')

@section('page_title', 'Detail Pemeriksaan Harian - ' . ($siswa->nama_siswa ?? 'Siswa'))

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="p-5 bg-gradient-to-br from-blue-50 to-indigo-100 border-b">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <h5 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-file-medical-alt text-blue-500 mr-2"></i> 
                    Detail Pemeriksaan Harian
                </h5>
                
                <!-- Status Badge -->
                @if($isDokter)
                    <span class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                        <i class="fas fa-stethoscope mr-1"></i>Mode Dokter
                    </span>
                @elseif($isPetugas)
                    <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                        <i class="fas fa-user-tie mr-1"></i>Mode Petugas UKS
                    </span>
                @elseif($isAdmin)
                    <span class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        <i class="fas fa-user-shield mr-1"></i>Mode Admin
                    </span>
                @elseif($isOrangTua)
                    <span class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                        <i class="fas fa-heart mr-1"></i>Mode Orang Tua
                    </span>
                @endif
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                
                <!-- Print Button -->
                <button onclick="printDetail()" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center text-sm no-print">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                
                <!-- Back Button -->
                <a href="{{ route($backRoute) }}" class="bg-indigo-500 text-white hover:bg-indigo-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center text-sm no-print">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="p-6">
        <!-- Row 1: Informasi Siswa dan Waktu Pemeriksaan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Informasi Siswa -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                    Informasi Siswa
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Nama Lengkap:</span>
                        <span class="text-gray-800 font-semibold">{{ $siswa->nama_siswa }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">NIS:</span>
                        <span class="text-gray-800">{{ $siswa->id_siswa }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Kelas:</span>
                        <span class="text-gray-800">{{ $siswa->detailSiswa->kelas->Nama_Kelas ?? 'Belum ada kelas' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Program:</span>
                        <span class="text-gray-800">{{ $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan ?? 'Belum ada program' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Usia:</span>
                        <span class="text-gray-800">{{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->age : 'N/A' }} Tahun</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Status:</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $siswa->status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $siswa->status_aktif ? 'AKTIF' : 'TIDAK AKTIF' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Waktu Pemeriksaan -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                    <i class="fas fa-clock text-green-600 mr-2"></i>
                    Waktu Pemeriksaan
                </h3>
                <div class="space-y-3">
                    <div class="text-center mb-4">
                        <div class="text-2xl font-bold text-green-700">
                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('d') }}
                        </div>
                        <div class="text-green-600 font-medium">
                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('F Y') }}
                        </div>
                        <div class="text-sm text-green-500">
                            {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('l') }}
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Waktu:</span>
                        <span class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">ID Pemeriksaan:</span>
                        <span class="text-gray-800">{{ $pemeriksaanHarian->Id_Harian }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Durasi:</span>
                        <span class="text-gray-800">{{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Row 2: Informasi Petugas -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6 border border-yellow-200 mb-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                <i class="fas fa-user-tie text-yellow-600 mr-2"></i>
                Petugas UKS yang Bertugas
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-sm text-gray-600 mb-1">Nama Petugas</div>
                    <div class="text-lg font-semibold text-gray-800">{{ $pemeriksaanHarian->petugasUks->nama_petugas_uks ?? 'Tidak ada petugas' }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-sm text-gray-600 mb-1">NIP</div>
                    <div class="text-lg font-semibold text-gray-800">{{ $pemeriksaanHarian->NIP ?? 'N/A' }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="text-sm text-gray-600 mb-1">Status Pemeriksaan</div>
                    <div class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 inline-block">
                        <i class="fas fa-check-circle mr-1"></i>SELESAI
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Row 3: Hasil Pemeriksaan (Main Content) -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200 mb-6">
            <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                <i class="fas fa-clipboard-check text-purple-600 mr-2"></i>
                Hasil Pemeriksaan Harian
            </h3>
            
            <div class="bg-white rounded-lg p-6 shadow-sm border">
                @if($pemeriksaanHarian->Hasil_Pemeriksaan)
                    <div class="max-w-none">
                        <div class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $pemeriksaanHarian->Hasil_Pemeriksaan }}</div>
                    </div>
                    
                    <!-- Analysis (untuk Dokter) -->
                    @if($isDokter)
                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
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
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-file-medical text-4xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-500 mb-2">Belum Ada Hasil Pemeriksaan</h4>
                        <p class="text-gray-400">Hasil pemeriksaan harian belum diinput oleh petugas UKS</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Row 4: Rekomendasi dan Tindak Lanjut -->
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 border border-indigo-200 mb-6">
            <h3 class="text-lg font-semibold text-indigo-800 mb-4 flex items-center">
                <i class="fas fa-lightbulb text-indigo-600 mr-2"></i>
                Rekomendasi & Tindak Lanjut
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Rekomendasi Umum -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-clipboard-list text-blue-500 mr-2"></i>
                        Rekomendasi Umum
                    </h4>
                    <ul class="text-sm text-gray-700 space-y-2">
                        @if($pemeriksaanHarian->Hasil_Pemeriksaan && (strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'sakit') !== false || strpos(strtolower($pemeriksaanHarian->Hasil_Pemeriksaan), 'keluhan') !== false))
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-orange-500 mt-1 text-xs"></i>
                                <span>Pemantauan kondisi kesehatan secara berkala</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-orange-500 mt-1 text-xs"></i>
                                <span>Koordinasi dengan orang tua terkait kondisi anak</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-orange-500 mt-1 text-xs"></i>
                                <span>Konsultasi ke dokter jika gejala berlanjut</span>
                            </li>
                        @else
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-green-500 mt-1 text-xs"></i>
                                <span>Pertahankan pola hidup sehat</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-green-500 mt-1 text-xs"></i>
                                <span>Konsumsi makanan bergizi seimbang</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <i class="fas fa-check-circle text-green-500 mt-1 text-xs"></i>
                                <span>Olahraga ringan secara teratur</span>
                            </li>
                        @endif
                        <li class="flex items-start space-x-2">
                            <i class="fas fa-check-circle text-blue-500 mt-1 text-xs"></i>
                            <span>Jaga kebersihan tangan dan lingkungan</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Status Tindak Lanjut -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-tasks text-green-500 mr-2"></i>
                        Status Tindak Lanjut
                    </h4>
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
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Row 5: Riwayat Pemeriksaan Lainnya (jika ada) -->
        @if($riwayatHarian->count() > 0)
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-gray-600 mr-2"></i>
                Riwayat Pemeriksaan Terbaru ({{ $riwayatHarian->count() }} data terakhir)
            </h3>
            
            <div class="space-y-3">
                @foreach($riwayatHarian as $riwayat)
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                    <div class="flex flex-col">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="text-sm font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($riwayat->Tanggal_Jam)->format('d M Y, H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    ({{ \Carbon\Carbon::parse($riwayat->Tanggal_Jam)->diffForHumans() }})
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 line-clamp-2">
                                {{ $riwayat->Hasil_Pemeriksaan ? (strlen($riwayat->Hasil_Pemeriksaan) > 100 ? substr($riwayat->Hasil_Pemeriksaan, 0, 100) . '...' : $riwayat->Hasil_Pemeriksaan) : 'Belum ada hasil pemeriksaan' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-user-tie mr-1"></i>
                                {{ $riwayat->petugasUks->nama_petugas_uks ?? 'Tidak ada petugas' }}
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <!-- Bottom Actions -->
    <div class="bg-gray-50 px-6 py-4 border-t no-print">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Detail pemeriksaan harian - {{ \Carbon\Carbon::parse($pemeriksaanHarian->Tanggal_Jam)->format('d F Y') }}
                @if($isOrangTua)
                    <span class="ml-2 text-purple-600">(Mode Orang Tua)</span>
                @elseif($isDokter)
                    <span class="ml-2 text-green-600">(Mode Dokter)</span>
                @elseif($isPetugas)
                    <span class="ml-2 text-yellow-600">(Mode Petugas UKS)</span>
                @elseif($isAdmin)
                    <span class="ml-2 text-blue-600">(Mode Admin)</span>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route($backRoute) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Close alert function
function closeAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.display = 'none';
    }
}

// Download PDF function
function downloadPDF() {
    const siswaId = '{{ $siswa->id_siswa }}';
    const harianId = '{{ $pemeriksaanHarian->Id_Harian }}';
    const url = `{{ route($pdfRoute, ['siswaId' => '__SISWA_ID__']) }}?pemeriksaan_harian_id=${harianId}`.replace('__SISWA_ID__', siswaId);
    window.open(url, '_blank');
}

// Print function
function printDetail() {
    window.print();
}
</script>
@endpush
@endsection