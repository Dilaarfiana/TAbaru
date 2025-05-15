@extends('layouts.admin')

@section('title', 'Tambah Dokter Baru')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-5">
        <h1 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-user-md text-blue-500 mr-2"></i> Tambah Dokter Baru
        </h1>
    </div>

    <form action="{{ route('dokter.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- ID Dokter -->
            <div>
                <label for="Id_Dokter" class="block text-sm font-medium text-gray-700 mb-1">
                    ID Dokter <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input type="text" name="Id_Dokter" id="Id_Dokter" value="{{ $nextId ?? 'DO001' }}" 
                           class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           readonly>
                </div>
                <p class="text-xs text-gray-500 mt-1">ID akan otomatis digenerate dengan format: DO + nomor urut</p>
            </div>
            
            <!-- Nama Dokter -->
            <div>
                <label for="Nama_Dokter" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Dokter <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="Nama_Dokter" id="Nama_Dokter" placeholder="Masukkan nama lengkap" 
                           class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('Nama_Dokter') border-red-500 @enderror"
                           value="{{ old('Nama_Dokter') }}" required>
                </div>
                @error('Nama_Dokter')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Spesialisasi -->
            <div>
                <label for="Spesialisasi" class="block text-sm font-medium text-gray-700 mb-1">
                    Spesialisasi
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-stethoscope text-gray-400"></i>
                    </div>
                    <select name="Spesialisasi" id="Spesialisasi" 
                            class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih Spesialisasi</option>
                        <option value="Umum" {{ old('Spesialisasi') == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Anak" {{ old('Spesialisasi') == 'Anak' ? 'selected' : '' }}>Anak</option>
                        <option value="Bedah" {{ old('Spesialisasi') == 'Bedah' ? 'selected' : '' }}>Bedah</option>
                        <option value="Jantung" {{ old('Spesialisasi') == 'Jantung' ? 'selected' : '' }}>Jantung</option>
                        <option value="THT" {{ old('Spesialisasi') == 'THT' ? 'selected' : '' }}>THT</option>
                        <option value="Mata" {{ old('Spesialisasi') == 'Mata' ? 'selected' : '' }}>Mata</option>
                        <option value="Kulit & Kelamin" {{ old('Spesialisasi') == 'Kulit & Kelamin' ? 'selected' : '' }}>Kulit & Kelamin</option>
                        <option value="Saraf" {{ old('Spesialisasi') == 'Saraf' ? 'selected' : '' }}>Saraf</option>
                        <option value="Gigi" {{ old('Spesialisasi') == 'Gigi' ? 'selected' : '' }}>Gigi</option>
                    </select>
                </div>
            </div>
            
            <!-- No. Telepon -->
            <div>
                <label for="No_Telp" class="block text-sm font-medium text-gray-700 mb-1">
                    No. Telepon
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
                    </div>
                    <div class="absolute inset-y-0 left-10 pr-3 flex items-center pointer-events-none text-gray-500">
                        +62
                    </div>
                    <input type="text" name="No_Telp" id="No_Telp" placeholder="8xxxxxxxxxx"
                           class="pl-20 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           value="{{ old('No_Telp') }}"
                           pattern="[8-9][0-9]*"
                           title="Masukkan nomor telepon yang dimulai dengan 8 atau 9">
                </div>
                <p class="text-xs text-gray-500 mt-1">Masukkan nomor tanpa angka 0 di depan</p>
            </div>
            
            <!-- Alamat -->
            <div class="md:col-span-2">
                <label for="Alamat" class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                    </div>
                    <textarea name="Alamat" id="Alamat" rows="2" placeholder="Masukkan alamat lengkap"
                              class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('Alamat') }}</textarea>
                </div>
            </div>
            
            <!-- Password -->
            <div class="md:col-span-2">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password" id="password" placeholder="Masukkan password"
                           class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror"
                           minlength="6" maxlength="20">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Password minimal 6 karakter, maksimal 20 karakter</p>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Button Section -->
        <div class="flex justify-end space-x-2 mt-8">
            <a href="{{ route('dokter.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
            <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                const eyeIcon = this.querySelector('i');
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        }
        
        // Format nomor telepon untuk menambahkan +62
        const phoneInput = document.getElementById('No_Telp');
        if (phoneInput) {
            // Event untuk memastikan hanya angka yang bisa dimasukkan
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Hapus angka 0 di depan jika dimasukkan
                if (this.value.startsWith('0')) {
                    this.value = this.value.substring(1);
                }
            });
            
            // Event untuk memastikan format yang benar saat form di-submit
            document.querySelector('form').addEventListener('submit', function() {
                if (phoneInput.value) {
                    // Simpan dalam format +62xxx tanpa mengubah tampilan input
                    phoneInput.value = '+62' + phoneInput.value;
                }
            });
        }
    });
</script>
@endpush

@endsection