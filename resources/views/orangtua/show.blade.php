@extends('layouts.admin')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-5xl mx-auto">
        <!-- Header dengan gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-t-lg px-6 py-5 flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div>
                    <h2 class="text-xl font-bold text-white">Detail Data Orang Tua</h2>
                    <p class="text-blue-100 text-sm">ID: {{ $orangTua->id_orang_tua }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('orangtua.edit', $orangTua->id_orang_tua) }}" class="flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-md transition-all duration-300 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data
                </a>
                <a href="{{ route('orangtua.index') }}" class="flex items-center px-4 py-2 bg-white text-gray-700 rounded-md hover:bg-gray-100 transition-all duration-300 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-b-lg shadow-lg overflow-hidden p-6">
            <!-- Information Card for Siswa -->
            <div class="mb-8 border border-blue-100 rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="text-md font-semibold text-white">Informasi Siswa</h3>
                    </div>
                </div>
                <div class="p-4 bg-gradient-to-b from-blue-50 to-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex">
                            <div class="w-24 text-sm font-medium text-gray-500">ID Siswa</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->siswa->id_siswa ?? '-' }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-24 text-sm font-medium text-gray-500">Nama Siswa</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->siswa->nama_siswa ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Information Card for Ayah -->
                <div class="border border-blue-100 rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h3 class="text-md font-semibold text-white">Data Ayah</h3>
                        </div>
                    </div>
                    <div class="p-4 bg-gradient-to-b from-blue-50 to-white">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Nama</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->nama_ayah ?? '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Tanggal Lahir</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->tanggal_lahir_ayah ? date('d-m-Y', strtotime($orangTua->tanggal_lahir_ayah)) : '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Pendidikan</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->pendidikan_ayah ?? '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Pekerjaan</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->pekerjaan_ayah ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Card for Ibu -->
                <div class="border border-pink-100 rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-4 py-3">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h3 class="text-md font-semibold text-white">Data Ibu</h3>
                        </div>
                    </div>
                    <div class="p-4 bg-gradient-to-b from-pink-50 to-white">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Nama</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->nama_ibu ?? '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Tanggal Lahir</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->tanggal_lahir_ibu ? date('d-m-Y', strtotime($orangTua->tanggal_lahir_ibu)) : '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Pendidikan</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->pendidikan_ibu ?? '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Pekerjaan</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->pekerjaan_ibu ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Card for Kontak -->
            <div class="border border-green-100 rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-4 py-3">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-md font-semibold text-white">Informasi Kontak</h3>
                    </div>
                </div>
                <div class="p-4 bg-gradient-to-b from-green-50 to-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start md:col-span-2">
                            <div class="w-32 text-sm font-medium text-gray-500">Alamat</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->alamat ?? '-' }}</div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-32 text-sm font-medium text-gray-500">No. Telepon</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $orangTua->no_telp ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-4 border-t flex justify-end space-x-3">
                <form action="{{ route('orangtua.destroy', $orangTua->id_orang_tua) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Data
                    </button>
                </form>
                <a href="{{ route('orangtua.edit', $orangTua->id_orang_tua) }}" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data
                </a>
                <a href="{{ route('orangtua.index') }}" class="flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors duration-200 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection