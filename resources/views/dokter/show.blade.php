@extends('layouts.admin')

@section('title', 'Detail Dokter')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Main Card -->
    <div class="max-w-5xl mx-auto">
        <!-- Top Card - White header -->
        <div class="bg-white rounded-t-lg p-6 shadow-md border-b-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-3 rounded-full shadow-md mr-4">
                        <i class="fas fa-user-md text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $dokter->Nama_Dokter }}</h1>
                        <div class="flex items-center mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-stethoscope mr-1 text-xs"></i>
                                {{ $dokter->Spesialisasi ?? 'Dokter' }}
                            </span>
                            <span class="ml-2 text-gray-500 text-sm">ID: {{ $dokter->Id_Dokter }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dokter.edit', $dokter->Id_Dokter) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md shadow hover:bg-yellow-600 transition duration-150 flex items-center">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <form action="{{ route('dokter.destroy', $dokter->Id_Dokter) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition duration-150 flex items-center"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data dokter ini?')">
                            <i class="fas fa-trash mr-2"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Detail Content -->
        <div class="bg-white rounded-b-lg shadow-md p-6">
            <!-- Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="col-span-2">
                    <!-- Basic Details Card -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-blue-500"></i>
                            Informasi Dasar
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-3 bg-white rounded-md shadow-sm">
                                <h3 class="text-xs uppercase tracking-wide font-medium text-gray-500">ID Dokter</h3>
                                <p class="mt-1 font-medium text-gray-900">{{ $dokter->Id_Dokter }}</p>
                            </div>
                            <div class="p-3 bg-white rounded-md shadow-sm">
                                <h3 class="text-xs uppercase tracking-wide font-medium text-gray-500">Nama Dokter</h3>
                                <p class="mt-1 font-medium text-gray-900">{{ $dokter->Nama_Dokter }}</p>
                            </div>
                            <div class="p-3 bg-white rounded-md shadow-sm">
                                <h3 class="text-xs uppercase tracking-wide font-medium text-gray-500">Spesialisasi</h3>
                                <p class="mt-1 font-medium text-gray-900">{{ $dokter->Spesialisasi ?? '-' }}</p>
                            </div>
                            <div class="p-3 bg-white rounded-md shadow-sm">
                                <h3 class="text-xs uppercase tracking-wide font-medium text-gray-500">No. Telepon</h3>
                                <p class="mt-1 font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-phone text-green-500 mr-1"></i>
                                    {{ $dokter->No_Telp ? str_replace('+62', '0', $dokter->No_Telp) : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Card -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 mt-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                            Alamat
                        </h2>
                        <div class="p-4 bg-white rounded-md shadow-sm">
                            <p class="text-gray-900">{{ $dokter->Alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Updates & Actions -->
                <div>
                    <!-- Update History -->
                    <div class="bg-blue-50 rounded-lg p-5 border border-blue-100">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                            Pembaruan Terakhir
                        </h2>
                        <div class="flex items-center p-3 bg-white rounded-md shadow-sm">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <i class="fas fa-calendar-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xs uppercase tracking-wide font-medium text-gray-500">Terakhir Diperbarui</h3>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $dokter->updated_at ? $dokter->updated_at->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('dokter.index') }}" class="flex items-center justify-center px-4 py-3 bg-gray-300 text-gray-800 rounded-md shadow-sm text-sm font-medium hover:bg-gray-400 transition duration-150 w-full">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Daftar Dokter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection