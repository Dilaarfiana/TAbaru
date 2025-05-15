@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-5">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
                <h1 class="text-xl font-bold text-white">Detail Pemeriksaan Awal</h1>
            </div>
            <div class="bg-white text-indigo-600 rounded-full px-4 py-1 text-sm font-bold shadow-sm">
                ID: {{ $pemeriksaanAwal->Id_PreAwal }}
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Kartu Informasi Pasien & Pemeriksaan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Informasi Dasar -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-indigo-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                        <h3 class="font-semibold text-gray-800">Informasi Dasar</h3>
                    </div>
                </div>
                <div class="p-4">
                    <ul class="space-y-3">
                        <li class="flex justify-between">
                            <span class="text-gray-600 font-medium">ID Pemeriksaan:</span>
                            <span class="text-gray-800">{{ $pemeriksaanAwal->Id_PreAwal }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600 font-medium">ID Detail:</span>
                            <span class="text-gray-800">{{ $pemeriksaanAwal->Id_DetPrx }}</span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-gray-600 font-medium">Tanggal & Jam:</span>
                            <span class="text-gray-800">{{ $pemeriksaanAwal->detailPemeriksaan ? \Carbon\Carbon::parse($pemeriksaanAwal->detailPemeriksaan->Tanggal_Jam)->format('d/m/Y H:i') : '-' }}</span>
                        </li>
                        @if($pemeriksaanAwal->detailPemeriksaan && $pemeriksaanAwal->detailPemeriksaan->siswa)
                        <li class="flex justify-between">
                            <span class="text-gray-600 font-medium">Pasien:</span>
                            <span class="text-gray-800">{{ $pemeriksaanAwal->detailPemeriksaan->siswa->Nama_Siswa }}</span>
                        </li>
                        @endif
                        @if($pemeriksaanAwal->detailPemeriksaan && $pemeriksaanAwal->detailPemeriksaan->dokter)
                        <li class="flex justify-between">
                            <span class="text-gray-600 font-medium">Dokter:</span>
                            <span class="text-gray-800">{{ $pemeriksaanAwal->detailPemeriksaan->dokter->Nama_Dokter }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Tanda Vital -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-red-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-heartbeat text-red-500 mr-2"></i>
                        <h3 class="font-semibold text-gray-800">Tanda Vital</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-temperature-high text-red-500 text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 mb-1">Suhu</div>
                                <div class="font-bold text-lg {{ $pemeriksaanAwal->Suhu > 37.5 ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ $pemeriksaanAwal->Suhu ?? '-' }} °C
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-heart text-red-500 text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 mb-1">Nadi</div>
                                <div class="font-bold text-lg text-gray-800">
                                    {{ $pemeriksaanAwal->Nadi ?? '-' }} <span class="text-xs text-gray-500">bpm</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-tachometer-alt text-red-500 text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 mb-1">Tegangan</div>
                                <div class="font-bold text-lg text-gray-800">
                                    {{ $pemeriksaanAwal->Tegangan ?? '-' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-lungs text-red-500 text-lg"></i>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-600 mb-1">Pernapasan</div>
                                <div class="font-bold text-lg text-gray-800">
                                    {{ $pemeriksaanAwal->Pernapasan ?? '-' }} <span class="text-xs text-gray-500">rpm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Nyeri -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-yellow-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                        <h3 class="font-semibold text-gray-800">Status Nyeri</h3>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-center my-3">
                        @if($pemeriksaanAwal->Status_Nyeri === 0)
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full inline-flex items-center">
                                <i class="fas fa-check-circle mr-2"></i> Tidak Ada
                            </span>
                        @elseif($pemeriksaanAwal->Status_Nyeri === 1)
                            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full inline-flex items-center">
                                <i class="fas fa-info-circle mr-2"></i> Ringan
                            </span>
                        @elseif($pemeriksaanAwal->Status_Nyeri === 2)
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full inline-flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i> Sedang
                            </span>
                        @elseif($pemeriksaanAwal->Status_Nyeri === 3)
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full inline-flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Berat
                            </span>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full inline-flex items-center">
                                <i class="fas fa-question-circle mr-2"></i> Tidak Diketahui
                            </span>
                        @endif
                    </div>
                    
                    @if($pemeriksaanAwal->Status_Nyeri !== 0 && $pemeriksaanAwal->Status_Nyeri !== null)
                    <div class="mt-4 space-y-2">
                        @if($pemeriksaanAwal->Karakteristik)
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-500 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm text-gray-600">Karakteristik</p>
                                <p class="text-gray-800">{{ $pemeriksaanAwal->Karakteristik }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($pemeriksaanAwal->Lokasi)
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-yellow-500 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm text-gray-600">Lokasi</p>
                                <p class="text-gray-800">{{ $pemeriksaanAwal->Lokasi }}</p>
                            </div>
                        </div>
                        @endif
                        
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            @if($pemeriksaanAwal->Durasi)
                            <div class="flex items-start">
                                <i class="fas fa-clock text-yellow-500 mt-1 mr-2"></i>
                                <div>
                                    <p class="text-sm text-gray-600">Durasi</p>
                                    <p class="text-gray-800">{{ $pemeriksaanAwal->Durasi }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($pemeriksaanAwal->Frekuensi)
                            <div class="flex items-start">
                                <i class="fas fa-sync-alt text-yellow-500 mt-1 mr-2"></i>
                                <div>
                                    <p class="text-sm text-gray-600">Frekuensi</p>
                                    <p class="text-gray-800">{{ $pemeriksaanAwal->Frekuensi }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Detail Pemeriksaan -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="bg-green-50 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center">
                    <i class="fas fa-stethoscope text-green-500 mr-2"></i>
                    <h3 class="font-semibold text-gray-800">Detail Pemeriksaan</h3>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-clipboard-list text-green-500 mr-2"></i> Pemeriksaan
                        </h4>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            {{ $pemeriksaanAwal->Pemeriksaan ?? 'Tidak ada catatan pemeriksaan' }}
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-history text-green-500 mr-2"></i> Keluhan Dahulu
                        </h4>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            {{ $pemeriksaanAwal->Keluhan_Dahulu ?? 'Tidak ada keluhan dahulu' }}
                        </div>
                    </div>
                    
                    @if($pemeriksaanAwal->Tipe !== null)
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-tag text-green-500 mr-2"></i> Tipe Pemeriksaan
                        </h4>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            {{ $pemeriksaanAwal->Tipe }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    
        <!-- Tombol Aksi -->
        <div class="flex justify-between items-center mt-6 border-t pt-6">
            <a href="{{ route('pemeriksaan_awal.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md transition shadow-sm border border-gray-300">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            
            <div class="flex space-x-2">
                <a href="{{ route('pemeriksaan_awal.edit', $pemeriksaanAwal->Id_PreAwal) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition shadow">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition shadow">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                </button>
                
                <form id="delete-form" action="{{ route('pemeriksaan_awal.destroy', $pemeriksaanAwal->Id_PreAwal) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Apakah Anda yakin ingin menghapus data pemeriksaan awal ini?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection