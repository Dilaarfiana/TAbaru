@extends('layouts.admin')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Edit Kelas</h2>
            </div>
            <a href="{{ route('kelas.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Info Box -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Kode Kelas tidak dapat diubah. Jumlah siswa akan terhitung otomatis oleh sistem berdasarkan siswa yang terdaftar di kelas ini.
                        </p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('kelas.update', $kelas->Kode_Kelas) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <!-- Kode Kelas (Readonly) -->
                    <div>
                        <label for="Kode_Kelas" class="block text-sm font-medium text-gray-700 mb-1">Kode Kelas</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <input type="text" 
                                id="Kode_Kelas" 
                                value="{{ $kelas->Kode_Kelas }}" 
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" 
                                readonly disabled>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Kode kelas tidak dapat diubah
                        </p>
                    </div>
                    
                    <!-- Nama Kelas -->
                    <div>
                        <label for="Nama_Kelas" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Kelas <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </div>
                            <input type="text" 
                                id="Nama_Kelas" 
                                name="Nama_Kelas" 
                                value="{{ old('Nama_Kelas', $kelas->Nama_Kelas) }}" 
                                required 
                                maxlength="20"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('Nama_Kelas') border-red-300 @enderror">
                        </div>
                    </div>

                    <!-- Jurusan -->
                    <div>
                        <label for="Kode_Jurusan" class="block text-sm font-medium text-gray-700 mb-1">
                            Jurusan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                </svg>
                            </div>
                            <select id="Kode_Jurusan" 
                                  name="Kode_Jurusan" 
                                  required
                                  class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none @error('Kode_Jurusan') border-red-300 @enderror">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach ($jurusan as $jur)
                                    <option value="{{ $jur->Kode_Jurusan }}" {{ (old('Kode_Jurusan', $kelas->Kode_Jurusan) == $jur->Kode_Jurusan) ? 'selected' : '' }}>
                                        {{ $jur->Kode_Jurusan }} - {{ $jur->Nama_Jurusan }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tahun Ajaran -->
                    <div>
                        <label for="Tahun_Ajaran" class="block text-sm font-medium text-gray-700 mb-1">
                            Tahun Ajaran
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="text" 
                                  id="Tahun_Ajaran" 
                                  name="Tahun_Ajaran" 
                                  value="{{ old('Tahun_Ajaran', $kelas->Tahun_Ajaran) }}" 
                                  maxlength="10" 
                                  placeholder="Contoh: 2024/2025"
                                  class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('Tahun_Ajaran') border-red-300 @enderror">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Format tahun ajaran: 2024/2025
                        </p>
                    </div>
                    
                    <!-- Hidden input untuk Jumlah Siswa - tidak ditampilkan -->
                    <input type="hidden" name="Jumlah_Siswa" value="{{ $kelas->jumlah_siswa ?? $kelas->Jumlah_Siswa ?? '0' }}">
                </div>
                
                <!-- Form Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="window.location.href='{{ route('kelas.index') }}'" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Detail Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow mt-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-medium text-gray-800">Informasi Kelas</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Kode Kelas</span>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800 mt-1">
                            {{ $kelas->Kode_Kelas }}
                        </span>
                    </div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Nama Kelas</span>
                        <span class="text-lg font-medium text-gray-900">{{ $kelas->Nama_Kelas }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Tahun Ajaran</span>
                        <span class="text-lg font-medium text-gray-900">{{ $kelas->Tahun_Ajaran ?? '-' }}</span>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Jurusan</span>
                        @if($kelas->jurusan)
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 mt-1">
                                {{ $kelas->jurusan->Kode_Jurusan }} - {{ $kelas->jurusan->Nama_Jurusan }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Jumlah Siswa</span>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800 mt-1">
                            {{ $kelas->jumlah_siswa ?? $kelas->Jumlah_Siswa ?? '0' }} siswa
                        </span>
                    </div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Opsi Tambahan</span>
                        <div class="mt-2">
                            <a href="{{ route('kelas.show', $kelas->Kode_Kelas) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat Detail Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection