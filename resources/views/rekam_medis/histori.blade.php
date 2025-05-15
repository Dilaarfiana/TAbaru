@extends('layouts.admin')

@section('page_title', 'Histori Rekam Medis Siswa')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Histori Rekam Medis Siswa</h2>
                <p class="text-gray-600 mt-1">{{ $siswa->id_siswa }} - {{ $siswa->Nama_Siswa }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('rekam_medis.create', ['siswa' => $siswa->id_siswa]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Tambah Rekam Medis
                </a>
            </div>
        </div>

        <!-- Data Siswa -->
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-blue-500 font-semibold uppercase">ID Siswa</p>
                    <p class="font-medium">{{ $siswa->id_siswa }}</p>
                </div>
                <div>
                    <p class="text-xs text-blue-500 font-semibold uppercase">Nama Lengkap</p>
                    <p class="font-medium">{{ $siswa->Nama_Siswa }}</p>
                </div>
                <div>
                    <p class="text-xs text-blue-500 font-semibold uppercase">Jenis Kelamin</p>
                    <p class="font-medium">
                        @if(isset($siswa->Jenis_Kelamin))
                            {{ $siswa->Jenis_Kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-blue-500 font-semibold uppercase">Tanggal Lahir</p>
                    <p class="font-medium">
                        @if(isset($siswa->Tanggal_Lahir))
                            {{ \Carbon\Carbon::parse($siswa->Tanggal_Lahir)->format('d M Y') }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px" id="historyTabs" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block py-2 px-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active" 
                            id="rekam-medis-tab" 
                            data-tabs-target="#rekam-medis" 
                            type="button" 
                            role="tab" 
                            aria-controls="rekam-medis" 
                            aria-selected="true">
                        Rekam Medis
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block py-2 px-4 text-gray-500 hover:text-gray-600 hover:border-gray-300 rounded-t-lg border-b-2 border-transparent" 
                            id="pemeriksaan-tab" 
                            data-tabs-target="#pemeriksaan" 
                            type="button" 
                            role="tab" 
                            aria-controls="pemeriksaan" 
                            aria-selected="false">
                        Pemeriksaan
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Contents -->
        <div id="historyTabContent">
            <!-- Rekam Medis Tab -->
            <div class="block" id="rekam-medis" role="tabpanel" aria-labelledby="rekam-medis-tab">
                @if($rekamMedis->count() > 0)
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No Rekam Medis
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal & Waktu
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dokter
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keluhan Utama
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rekamMedis as $rm)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $rm->No_Rekam_Medis }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($rm->Tanggal_Jam)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $rm->dokter->Nama_Dokter ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $rm->Keluhan_Utama }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('rekam_medis.show', $rm->No_Rekam_Medis) }}" class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('rekam_medis.edit', $rm->No_Rekam_Medis) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('rekam_medis.cetak', $rm->No_Rekam_Medis) }}" target="_blank" class="text-green-600 hover:text-green-900" title="Cetak">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="bg-gray-50 p-4 rounded text-center">
                        <p class="text-gray-600">Belum ada rekam medis untuk siswa ini</p>
                        <a href="{{ route('rekam_medis.create', ['siswa' => $siswa->id_siswa]) }}" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:shadow-outline-blue transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-2"></i> Tambah Rekam Medis Baru
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Pemeriksaan Tab -->
            <div class="hidden" id="pemeriksaan" role="tabpanel" aria-labelledby="pemeriksaan-tab">
                @if($detailPemeriksaan->count() > 0)
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID Pemeriksaan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal & Waktu
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Petugas
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $dp->Id_DetPrx }}
                                    </td>
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
        </div>

        <div class="mt-6">
            <a href="{{ route('rekam_medis.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const tabContents = document.querySelectorAll('[role="tabpanel"]');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = document.querySelector(tab.dataset.tabsTarget);
                
                tabContents.forEach(tc => {
                    tc.classList.add('hidden');
                });
                
                tabs.forEach(t => {
                    t.classList.remove('text-blue-600', 'border-blue-600');
                    t.classList.add('text-gray-500', 'border-transparent');
                    t.setAttribute('aria-selected', false);
                });
                
                tab.classList.remove('text-gray-500', 'border-transparent');
                tab.classList.add('text-blue-600', 'border-blue-600');
                tab.setAttribute('aria-selected', true);
                
                target.classList.remove('hidden');
                target.classList.add('block');
            });
        });
    });
</script>
@endpush