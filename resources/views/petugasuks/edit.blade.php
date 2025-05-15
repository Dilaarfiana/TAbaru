@extends('layouts.admin')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <div class="w-full max-w-5xl mx-auto">
        <!-- Header dengan desain yang lebih elegan -->
        <div class="bg-white rounded-t-lg p-5 flex justify-between items-center shadow-sm border-b border-gray-100">
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-lg bg-blue-50 flex items-center justify-center mr-4">
                    <i class="fas fa-user-edit text-blue-500 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Edit Petugas UKS</h2>
                    <p class="text-sm text-gray-500 mt-0.5 flex items-center">
                        <i class="fas fa-id-badge text-gray-400 mr-1"></i>
                        <span>{{ $petugasUKS->NIP }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('petugasuks.show', $petugasUKS->NIP) }}" class="bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 font-medium px-4 py-2 rounded-lg shadow-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Data
                </a>
                <a href="{{ route('petugasuks.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-lg shadow-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Panel Form dengan bayangan yang lebih halus -->
        <div class="bg-white rounded-b-lg shadow-md p-6 relative overflow-hidden">
            <!-- Elemen dekoratif -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 via-blue-500 to-indigo-600"></div>
            
            <!-- Header Informasi Petugas dengan desain yang diperbarui -->
            <div class="flex items-center mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Informasi Petugas</h3>
            </div>

            <!-- Form -->
            <form action="{{ route('petugasuks.update', $petugasUKS->NIP) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Form Errors dengan desain yang lebih baik -->
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 mb-2">Ada beberapa kesalahan pada form:</h3>
                                <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <!-- NIP Field dengan desain yang lebih profesional -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <label for="NIP" class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-badge text-gray-400"></i>
                            </div>
                            <input type="text" id="NIP" value="{{ $petugasUKS->NIP }}" 
                                class="pl-10 block w-full border border-gray-300 rounded-lg h-11 bg-white text-gray-500 focus:ring-blue-500 focus:border-blue-500" 
                                readonly disabled>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                            NIP tidak dapat diubah
                        </p>
                    </div>
                    
                    <!-- Nama Petugas Field dengan desain yang ditingkatkan -->
                    <div>
                        <label for="nama_petugas_uks" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Petugas <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-blue-400"></i>
                            </div>
                            <input type="text" id="nama_petugas_uks" name="nama_petugas_uks" 
                                value="{{ old('nama_petugas_uks', $petugasUKS->nama_petugas_uks) }}" 
                                class="pl-10 block w-full border border-gray-300 rounded-lg h-11 focus:ring-blue-500 focus:border-blue-500 @error('nama_petugas_uks') border-red-300 ring-2 ring-red-200 @enderror" 
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        @error('nama_petugas_uks')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <!-- No. Telepon Field dengan desain yang ditingkatkan -->
                    <div>
                        <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-2">
                            No. Telepon
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone-alt text-blue-400"></i>
                            </div>
                            <div class="absolute inset-y-0 left-10 flex items-center pointer-events-none">
                                <span class="text-gray-500 pl-0.5 pr-1 font-medium">+62</span>
                            </div>
                            <input type="text" id="no_telp" name="no_telp" 
                                value="{{ old('no_telp', $petugasUKS->no_telp ? (strpos($petugasUKS->no_telp, '+62') === 0 ? substr($petugasUKS->no_telp, 3) : $petugasUKS->no_telp) : '') }}" 
                                class="pl-20 block w-full border border-gray-300 rounded-lg h-11 focus:ring-blue-500 focus:border-blue-500 @error('no_telp') border-red-300 ring-2 ring-red-200 @enderror" 
                                placeholder="81234567890 (tanpa awalan 0)">
                        </div>
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                            Masukkan nomor tanpa awalan 0, akan otomatis diformat menjadi +62
                        </p>
                        @error('no_telp')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <!-- Status Field dengan desain yang ditingkatkan -->
                    <div>
                        <label for="status_aktif" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-toggle-on text-blue-400"></i>
                            </div>
                            <select id="status_aktif" name="status_aktif" class="pl-10 block w-full border border-gray-300 rounded-lg h-11 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                <option value="1" {{ old('status_aktif', $petugasUKS->status_aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status_aktif', $petugasUKS->status_aktif) == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alamat Field dengan desain yang ditingkatkan -->
                    <div class="col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-blue-400"></i>
                            </div>
                            <textarea id="alamat" name="alamat" rows="3" 
                                class="pl-10 block w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('alamat') border-red-300 ring-2 ring-red-200 @enderror" 
                                placeholder="Masukkan alamat lengkap">{{ old('alamat', $petugasUKS->alamat) }}</textarea>
                        </div>
                        @error('alamat')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                
                <!-- Password Section dengan desain yang lebih menarik -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-lock text-blue-500 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Keamanan Akun</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 bg-gray-50 p-5 rounded-lg border border-gray-100">
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-blue-400"></i>
                                </div>
                                <input type="password" id="password" name="password" 
                                    class="pl-10 block w-full border border-gray-300 rounded-lg h-11 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 ring-2 ring-red-200 @enderror" 
                                    placeholder="Kosongkan jika tidak diubah">
                            </div>
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                                Minimal 6 karakter
                            </p>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <!-- Konfirmasi Password Field -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-check-double text-blue-400"></i>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                    class="pl-10 block w-full border border-gray-300 rounded-lg h-11 focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="Konfirmasi password baru">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Buttons dengan desain yang lebih menarik -->
                <div class="flex justify-end mt-8 space-x-3">
                    <a href="{{ route('petugasuks.index') }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 bg-white text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 shadow-sm transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection