@extends('layouts.admin')

@section('page_title', 'Detail Rekam Medis')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Detail Rekam Medis</h2>
                <p class="text-gray-600 mt-1">Nomor: {{ $rekamMedis->No_Rekam_Medis }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('rekam_medis.edit', $rekamMedis->No_Rekam_Medis) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('rekam_medis.cetak', $rekamMedis->No_Rekam_Medis) }}" target="_blank" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-print mr-2"></i> Cetak
                </a>
            </div>
        </div>

        <!-- Data Siswa dan Waktu -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h3 class="font-semibold text-blue-800 mb-2">Data Siswa</h3>
                <p class="mb-1"><span class="font-medium">ID Siswa:</span> {{ $rekamMedis->Id_Siswa }}</p>
                <p class="mb-1"><span class="font-medium">Nama:</span> {{ $rekamMedis->siswa->Nama_Siswa ?? 'N/A' }}</p>
                <p class="mb-1"><span class="font-medium">Jenis Kelamin:</span> 
                    @if(isset($rekamMedis->siswa->Jenis_Kelamin))
                        {{ $rekamMedis->siswa->Jenis_Kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    @else
                        N/A
                    @endif
                </p>
                <p><span class="font-medium">Tanggal Lahir:</span> 
                    @if(isset($rekamMedis->siswa->Tanggal_Lahir))
                        {{ \Carbon\Carbon::parse($rekamMedis->siswa->Tanggal_Lahir)->format('d M Y') }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
            
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                <h3 class="font-semibold text-indigo-800 mb-2">Dokter</h3>
                <p class="mb-1"><span class="font-medium">ID Dokter:</span> {{ $rekamMedis->Id_Dokter }}</p>
                <p class="mb-1"><span class="font-medium">Nama:</span> {{ $rekamMedis->dokter->Nama_Dokter ?? 'N/A' }}</p>
                <p><span class="font-medium">Spesialisasi:</span> {{ $rekamMedis->dokter->Spesialisasi ?? 'N/A' }}</p>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <h3 class="font-semibold text-green-800 mb-2">Waktu Rekam Medis</h3>
                <p class="mb-1"><span class="font-medium">Tanggal:</span> {{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('d M Y') }}</p>
                <p><span class="font-medium">Waktu:</span> {{ \Carbon\Carbon::parse($rekamMedis->Tanggal_Jam)->format('H:i') }}</p>
            </div>
        </div>

        <!-- Informasi Rekam Medis -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg text-gray-800 mb-3 border-b pb-2">Data Rekam Medis</h3>
            
            <div class="bg-white rounded-lg border p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">Keluhan Utama</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $rekamMedis->Keluhan_Utama ?? 'Tidak ada data' }}</p>
            </div>
            
            <div class="bg-white rounded-lg border p-4 mb-4">
                <h4 class="font-semibold text-gray-800 mb-2">Riwayat Penyakit Sekarang</h4>
                <p class="text-gray-700 whitespace-pre-line">{{ $rekamMedis->Riwayat_Penyakit_Sekarang ?? 'Tidak ada data' }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Riwayat Penyakit Dahulu</h4>
                    <p class="text-gray-700 whitespace-pre-line">{{ $rekamMedis->Riwayat_Penyakit_Dahulu ?? 'Tidak ada data' }}</p>
                </div>
                
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Riwayat Imunisasi</h4>
                    <p class="text-gray-700 whitespace-pre-line">{{ $rekamMedis->Riwayat_Imunisasi ?? 'Tidak ada data' }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Riwayat Penyakit Keluarga</h4>
                    <p class="text-gray-700 whitespace-pre-line">{{ $rekamMedis->Riwayat_Penyakit_Keluarga ?? 'Tidak ada data' }}</p>
                </div>
                
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Silsilah Keluarga</h4>
                    <p class="text-gray-700 whitespace-pre-line">{{ $rekamMedis->Silsilah_Keluarga ?? 'Tidak ada data' }}</p>
                </div>
            </div>
        </div>

        <!-- Riwayat Pemeriksaan -->
        <div class="mb-6">
            <h3 class="font-semibold text-lg text-gray-800 mb-3 border-b pb-2">Riwayat Pemeriksaan Siswa</h3>
            
            @if($detailPemeriksaan->count() > 0)
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal & Waktu
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dokter / Petugas UKS
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hasil Pemeriksaan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($detailPemeriksaan as $dp)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($dp->Tanggal_Jam)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($dp->Id_Dokter)
                                            {{ $dp->dokter->Nama_Dokter ?? 'N/A' }} (Dokter)
                                        @elseif($dp->NIP)
                                            {{ $dp->petugasUks->Nama_Petugas_UKS ?? 'N/A' }} (Petugas UKS)
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $dp->Hasil_Pemeriksaan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('pemeriksaan.show', $dp->Id_DetPrx) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-gray-600">Belum ada data pemeriksaan untuk siswa ini</p>
                </div>
            @endif
        </div>

        <div class="flex justify-between">
            <a href="{{ route('rekam_medis.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            
            <form action="{{ route('rekam_medis.destroy', $rekamMedis->No_Rekam_Medis) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekam medis ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection