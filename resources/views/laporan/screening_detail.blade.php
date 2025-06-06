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

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-file-medical mr-3"></i>
                    Detail Screening Kesehatan
                </h1>
                <p class="mt-2 opacity-90">
                    Detail pemeriksaan screening untuk {{ $siswa->nama_siswa }}
                </p>
            </div>
            <div class="text-right">
                @if($isDokter)
                    <span class="px-3 py-1 bg-green-600 rounded-lg text-sm font-medium">
                        <i class="fas fa-stethoscope mr-1"></i>Dokter
                    </span>
                @elseif($isPetugas)
                    <span class="px-3 py-1 bg-yellow-600 rounded-lg text-sm font-medium">
                        <i class="fas fa-user-tie mr-1"></i>Petugas UKS
                    </span>
                @elseif($isAdmin)
                    <span class="px-3 py-1 bg-gray-600 rounded-lg text-sm font-medium">
                        <i class="fas fa-user-shield mr-1"></i>Admin
                    </span>
                @elseif($isOrangTua)
                    <span class="px-3 py-1 bg-purple-600 rounded-lg text-sm font-medium">
                        <i class="fas fa-heart mr-1"></i>Orang Tua
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Informasi Siswa -->
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
            Informasi Siswa
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                <div class="font-semibold text-gray-900">{{ $siswa->nama_siswa }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-1">NIS</label>
                <div class="font-semibold text-gray-900">{{ $siswa->id_siswa }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-1">Kelas</label>
                <div class="font-semibold text-gray-900">
                    {{ $siswa->detailSiswa->kelas->Nama_Kelas ?? 'Belum ada kelas' }}
                    @if($siswa->detailSiswa->kelas->jurusan ?? null)
                        <span class="text-sm text-gray-500">({{ $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan }})</span>
                    @endif
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Lahir</label>
                <div class="font-semibold text-gray-900">
                    {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') : 'Tidak diketahui' }}
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-1">Jenis Kelamin</label>
                <div class="font-semibold text-gray-900">
                    @if($siswa->jenis_kelamin == 'L')
                        Laki-laki
                    @elseif($siswa->jenis_kelamin == 'P')
                        Perempuan
                    @else
                        Tidak diketahui
                    @endif
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-600 mb-1">Usia</label>
                <div class="font-semibold text-gray-900">
                    {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->age . ' tahun' : 'Tidak diketahui' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Pemeriksaan -->
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clipboard-check text-green-600 mr-2"></i>
            Detail Pemeriksaan Screening
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <label class="block text-sm font-medium text-blue-700 mb-1">Tanggal & Waktu Pemeriksaan</label>
                <div class="font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($detailPemeriksaan->tanggal_jam)->format('d F Y, H:i') }} WIB
                </div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <label class="block text-sm font-medium text-green-700 mb-1">Status Pemeriksaan</label>
                <div class="font-semibold">
                    @if($detailPemeriksaan->status_pemeriksaan == 'lengkap')
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            <i class="fas fa-check-circle mr-1"></i>Lengkap
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                            <i class="fas fa-clock mr-1"></i>Belum Lengkap
                        </span>
                    @endif
                </div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <label class="block text-sm font-medium text-purple-700 mb-1">Dokter Pemeriksa</label>
                <div class="font-semibold text-gray-900">
                    {{ $detailPemeriksaan->dokter->Nama_Dokter ?? 'Belum ditentukan' }}
                    @if($detailPemeriksaan->dokter && $detailPemeriksaan->dokter->Spesialisasi)
                        <span class="text-sm text-gray-600">({{ $detailPemeriksaan->dokter->Spesialisasi }})</span>
                    @endif
                </div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <label class="block text-sm font-medium text-yellow-700 mb-1">Petugas UKS</label>
                <div class="font-semibold text-gray-900">
                    {{ $detailPemeriksaan->petugasUks->nama_petugas_uks ?? 'Belum ditentukan' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Pemeriksaan Awal -->
    @if($detailPemeriksaan->pemeriksaanAwal)
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-thermometer-half text-red-600 mr-2"></i>
            Pemeriksaan Awal - Tanda Vital
        </h2>
        
        <!-- Vital Signs Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @if($detailPemeriksaan->pemeriksaanAwal->suhu)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-thermometer-half text-2xl text-red-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Suhu Tubuh</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanAwal->suhu }}°C</div>
                <div class="text-xs mt-1 text-gray-500">
                    @if($detailPemeriksaan->pemeriksaanAwal->suhu >= 37.5)
                        <span class="text-red-600">Demam</span>
                    @elseif($detailPemeriksaan->pemeriksaanAwal->suhu <= 35.5)
                        <span class="text-blue-600">Hipotermia</span>
                    @else
                        <span class="text-green-600">Normal</span>
                    @endif
                </div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanAwal->nadi)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-heartbeat text-2xl text-pink-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nadi</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanAwal->nadi }}</div>
                <div class="text-xs mt-1 text-gray-500">bpm</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanAwal->tegangan)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-tachometer-alt text-2xl text-blue-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Tekanan Darah</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanAwal->tegangan }}</div>
                <div class="text-xs mt-1 text-gray-500">mmHg</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanAwal->pernapasan)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-lungs text-2xl text-green-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Pernapasan</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanAwal->pernapasan }}</div>
                <div class="text-xs mt-1 text-gray-500">per menit</div>
            </div>
            @endif
        </div>
        
        <!-- Additional Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($detailPemeriksaan->pemeriksaanAwal->keluhan_dahulu)
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <label class="block text-sm font-medium text-yellow-700 mb-2">Keluhan Dahulu</label>
                <div class="text-gray-800">{{ $detailPemeriksaan->pemeriksaanAwal->keluhan_dahulu }}</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanAwal->pemeriksaan)
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <label class="block text-sm font-medium text-blue-700 mb-2">Hasil Pemeriksaan</label>
                <div class="text-gray-800">{{ $detailPemeriksaan->pemeriksaanAwal->pemeriksaan }}</div>
            </div>
            @endif
        </div>

        <!-- Pain Assessment -->
        @if($detailPemeriksaan->pemeriksaanAwal->status_nyeri)
        <div class="mt-4 bg-red-50 p-4 rounded-lg border border-red-200">
            <h4 class="font-medium text-red-700 mb-3">Penilaian Nyeri</h4>
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
        @endif
    </div>
    @endif

    <!-- Pemeriksaan Fisik -->
    @if($detailPemeriksaan->pemeriksaanFisik)
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-child text-green-600 mr-2"></i>
            Pemeriksaan Fisik & Antropometri
        </h2>
        
        <!-- Physical Measurements -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @if($detailPemeriksaan->pemeriksaanFisik->tinggi_badan)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-ruler-vertical text-2xl text-blue-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Tinggi Badan</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanFisik->tinggi_badan }}</div>
                <div class="text-xs mt-1 text-gray-500">cm</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanFisik->berat_badan)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-weight text-2xl text-green-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Berat Badan</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanFisik->berat_badan }}</div>
                <div class="text-xs mt-1 text-gray-500">kg</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanFisik->lingkar_kepala)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-circle text-2xl text-purple-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Lingkar Kepala</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanFisik->lingkar_kepala }}</div>
                <div class="text-xs mt-1 text-gray-500">cm</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanFisik->lingkar_lengan_atas)
            <div class="bg-white border border-gray-200 p-4 rounded-lg text-center shadow-sm hover:shadow-md transition-shadow">
                <i class="fas fa-ruler text-2xl text-orange-500 mb-2"></i>
                <label class="block text-sm font-medium text-gray-600 mb-1">Lingkar Lengan Atas</label>
                <div class="text-2xl font-bold text-gray-900">{{ $detailPemeriksaan->pemeriksaanFisik->lingkar_lengan_atas }}</div>
                <div class="text-xs mt-1 text-gray-500">cm</div>
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
            } elseif ($bmi < 25) {
                $bmi_status = 'Normal';
                $bmi_color = 'text-green-600';
            } elseif ($bmi < 30) {
                $bmi_status = 'Overweight';
                $bmi_color = 'text-yellow-600';
            } else {
                $bmi_status = 'Obese';
                $bmi_color = 'text-red-600';
            }
        @endphp
        <div class="mb-6 bg-indigo-50 p-4 rounded-lg border border-indigo-200">
            <h4 class="font-medium text-indigo-700 mb-2">Indeks Massa Tubuh (BMI)</h4>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-2xl font-bold {{ $bmi_color }}">{{ $bmi }}</span>
                    <span class="text-gray-600">kg/m²</span>
                </div>
                <div class="text-right">
                    <div class="{{ $bmi_color }} font-semibold">{{ $bmi_status }}</div>
                    <div class="text-xs text-gray-500">Status Gizi</div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Physical Examination Results -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $organs = [
                    'dada' => ['icon' => 'fas fa-lungs', 'color' => 'blue', 'name' => 'Dada'],
                    'jantung' => ['icon' => 'fas fa-heart', 'color' => 'red', 'name' => 'Jantung'],
                    'paru' => ['icon' => 'fas fa-lungs', 'color' => 'teal', 'name' => 'Paru-paru'],
                    'perut' => ['icon' => 'fas fa-circle', 'color' => 'yellow', 'name' => 'Perut'],
                    'hepar' => ['icon' => 'fas fa-liver', 'color' => 'orange', 'name' => 'Hepar'],
                    'anogenital' => ['icon' => 'fas fa-circle', 'color' => 'pink', 'name' => 'Anogenital'],
                    'ekstremitas' => ['icon' => 'fas fa-walking', 'color' => 'green', 'name' => 'Ekstremitas'],
                    'kepala' => ['icon' => 'fas fa-head-side-virus', 'color' => 'purple', 'name' => 'Kepala']
                ];
            @endphp
            
            @foreach($organs as $organ => $config)
                @if($detailPemeriksaan->pemeriksaanFisik->$organ)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <label class="block text-sm font-medium text-gray-700 capitalize mb-1">
                        <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-500 mr-2"></i>{{ $config['name'] }}
                    </label>
                    <div class="text-gray-800">{{ $detailPemeriksaan->pemeriksaanFisik->$organ }}</div>
                </div>
                @endif
            @endforeach
        </div>
        
        <!-- Additional Physical Examination Info -->
        <div class="mt-6 space-y-4">
            @if($detailPemeriksaan->pemeriksaanFisik->pemeriksaan_penunjang)
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <label class="block text-sm font-medium text-blue-700 mb-2">Pemeriksaan Penunjang</label>
                <div class="text-gray-800">{{ $detailPemeriksaan->pemeriksaanFisik->pemeriksaan_penunjang }}</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanFisik->masalah_aktif)
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <label class="block text-sm font-medium text-red-700 mb-2">Masalah Aktif</label>
                <div class="text-gray-800 font-medium">{{ $detailPemeriksaan->pemeriksaanFisik->masalah_aktif }}</div>
            </div>
            @endif
            
            @if($detailPemeriksaan->pemeriksaanFisik->rencana_medis_dan_terapi)
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <label class="block text-sm font-medium text-green-700 mb-2">Rencana Medis dan Terapi</label>
                <div class="text-gray-800">{{ $detailPemeriksaan->pemeriksaanFisik->rencana_medis_dan_terapi }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Rekam Medis -->
    @if(isset($rekamMedis) && $rekamMedis)
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-notes-medical text-blue-600 mr-2"></i>
            Rekam Medis Terkait
        </h2>
        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-white p-3 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-blue-600 mb-1">Tanggal Rekam Medis</label>
                    <div class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('d F Y, H:i') }} WIB</div>
                </div>
                <div class="bg-white p-3 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-blue-600 mb-1">Dokter</label>
                    <div class="font-semibold text-gray-800">{{ $rekamMedis->dokter->Nama_Dokter ?? 'Tidak ada dokter' }}</div>
                </div>
            </div>
            
            @if($rekamMedis->Keluhan_Utama)
            <div class="mb-4 bg-white p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-blue-600 mb-2">Keluhan Utama</label>
                <div class="text-gray-800">{{ $rekamMedis->Keluhan_Utama }}</div>
            </div>
            @endif
            
            @if($rekamMedis->Riwayat_Penyakit_Sekarang)
            <div class="mb-4 bg-white p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-blue-600 mb-2">Riwayat Penyakit Sekarang</label>
                <div class="text-gray-800">{{ $rekamMedis->Riwayat_Penyakit_Sekarang }}</div>
            </div>
            @endif
            
            @if($rekamMedis->Riwayat_Penyakit_Dahulu)
            <div class="mb-4 bg-white p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-blue-600 mb-2">Riwayat Penyakit Dahulu</label>
                <div class="text-gray-800">{{ $rekamMedis->Riwayat_Penyakit_Dahulu }}</div>
            </div>
            @endif
            
            @if($rekamMedis->Riwayat_Imunisasi)
            <div class="mb-4 bg-white p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-blue-600 mb-2">Riwayat Imunisasi</label>
                <div class="text-gray-800">{{ $rekamMedis->Riwayat_Imunisasi }}</div>
            </div>
            @endif

            @if($rekamMedis->Riwayat_Penyakit_Keluarga)
            <div class="mb-4 bg-white p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-blue-600 mb-2">Riwayat Penyakit Keluarga</label>
                <div class="text-gray-800">{{ $rekamMedis->Riwayat_Penyakit_Keluarga }}</div>
            </div>
            @endif

            @if($rekamMedis->Silsilah_Keluarga)
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-blue-600 mb-2">Silsilah Keluarga</label>
                <div class="text-gray-800">{{ $rekamMedis->Silsilah_Keluarga }}</div>
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
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-pills text-green-600 mr-2"></i>
            Resep Obat
        </h2>
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($resepObat as $index => $resep)
                <div class="bg-white p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-green-700 text-lg">{{ $resep->Nama_Obat }}</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">#{{ $index + 1 }}</span>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-prescription-bottle text-green-500 mr-2 w-4"></i>
                            <strong class="text-gray-700">Dosis:</strong>
                            <span class="ml-2 text-gray-800">{{ $resep->Dosis }}</span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-clock text-green-500 mr-2 w-4"></i>
                            <strong class="text-gray-700">Durasi:</strong>
                            <span class="ml-2 text-gray-800">{{ $resep->Durasi }}</span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-green-500 mr-2 w-4"></i>
                            <strong class="text-gray-700">Tanggal:</strong>
                            <span class="ml-2 text-gray-800">{{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->format('d/m/Y') }}</span>
                        </div>
                        
                        @if($resep->dokter)
                        <div class="flex items-center">
                            <i class="fas fa-user-md text-green-500 mr-2 w-4"></i>
                            <strong class="text-gray-700">Dokter:</strong>
                            <span class="ml-2 text-gray-800">{{ $resep->dokter->Nama_Dokter }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Important Notes -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-0.5"></i>
                    <div class="text-sm">
                        <strong class="text-yellow-800">Catatan Penting:</strong>
                        <ul class="mt-2 space-y-1 text-yellow-700">
                            <li>• Gunakan obat sesuai dosis yang telah ditentukan</li>
                            <li>• Jangan hentikan pengobatan tanpa konsultasi dokter</li>
                            <li>• Jika ada efek samping, segera hubungi dokter</li>
                            <li>• Simpan obat di tempat yang aman dan sejuk</li>
                            <li>• Pastikan obat tidak melewati tanggal kadaluarsa</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary & Recommendation -->
    <div class="p-6 border-b border-gray-200 bg-gray-50">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>
            Ringkasan Pemeriksaan
        </h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Status Summary -->
            <div class="bg-white p-4 rounded-lg border border-gray-200">
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
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Rekomendasi Kesehatan
                </h4>
                <div class="space-y-2 text-sm">
                    @if($detailPemeriksaan->pemeriksaanFisik && isset($bmi))
                        @if($bmi < 18.5)
                            <div class="text-blue-700">Pertahankan pola makan bergizi untuk mencapai berat badan ideal</div>
                        @elseif($bmi >= 25)
                            <div class="text-orange-700">Perhatikan pola makan dan tingkatkan aktivitas fisik</div>
                        @else
                            <div class="text-green-700">Pertahankan pola hidup sehat yang sudah baik</div>
                        @endif
                    @endif
                    
                    @if($detailPemeriksaan->pemeriksaanAwal && $detailPemeriksaan->pemeriksaanAwal->suhu >= 37.5)
                        <div class="text-red-700">Istirahat yang cukup dan konsumsi cairan yang banyak</div>
                    @endif
                    
                    <div class="text-gray-700">Konsumsi makanan bergizi seimbang</div>
                    <div class="text-gray-700">Minum air putih minimal 8 gelas per hari</div>
                    <div class="text-gray-700">Lakukan olahraga teratur minimal 30 menit per hari</div>
                    <div class="text-gray-700">Tidur yang cukup 7-9 jam per hari</div>
                    
                    @if($resepObat->count() > 0)
                        <div class="text-indigo-700">Minum obat sesuai petunjuk dokter</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="p-6 bg-gray-50">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <div class="flex flex-wrap gap-3">
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
                
                <a href="{{ $backRoute }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar
                </a>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors flex items-center">
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
                   class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Download PDF
                </a>
                
                @if($isAdmin || $isPetugas)
                <a href="{{ str_replace('.screening.pdf', '.screening.preview', $pdfRoute) }}?detail_pemeriksaan_id={{ $detailPemeriksaan->id_detprx }}" target="_blank" 
                   class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Preview PDF
                </a>
                @endif
                
                @if(!$isOrangTua)
                <button onclick="shareDetail()" 
                        class="bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 transition-colors flex items-center">
                    <i class="fas fa-share-alt mr-2"></i>
                    Bagikan
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { font-size: 12pt; }
    .bg-blue-600 { background: #f8f9fa !important; color: black !important; }
    .p-6 { padding: 1rem !important; }
    .text-white { color: black !important; }
    .shadow-md, .shadow-sm { box-shadow: none !important; }
    .border-b { border-bottom: 1px solid #e5e7eb !important; }
    
    .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-red-50, .bg-purple-50, .bg-gray-50 {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
    }
    
    .text-blue-600, .text-green-600, .text-yellow-600, .text-red-600, .text-purple-600 {
        color: #333 !important;
        font-weight: bold !important;
    }
}

/* Hover effects */
.hover\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.transition-shadow {
    transition: box-shadow 0.15s ease-in-out;
}

/* Responsive design */
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
            const actionSection = document.querySelector('.bg-gray-50');
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
            title: 'Detail Pemeriksaan Screening - {{ $siswa->nama_siswa }}',
            text: 'Detail pemeriksaan kesehatan screening',
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