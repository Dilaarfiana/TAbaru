@extends('layouts.app')

@section('title', 'Edit Dokter')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-5">
        <h1 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-user-edit text-blue-500 mr-2"></i> Edit Data Dokter
        </h1>
        <p class="text-sm text-gray-600 mt-1">Ubah informasi dokter {{ $dokter->Nama_Dokter }}</p>
    </div>

    <form action="{{ route('dokter.update', $dokter->Id_Dokter) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                    <input type="text" name="Id_Dokter" id="Id_Dokter" value="{{ $dokter->Id_Dokter }}" 
                           class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-100"
                           readonly>
                </div>
                <p class="text-xs text-gray-500 mt-1">ID dokter tidak dapat diubah</p>
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
                           value="{{ old('Nama_Dokter', $dokter->Nama_Dokter) }}" maxlength="50" required>
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
                            class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('Spesialisasi') border-red-500 @enderror">
                        <option value="">Pilih Spesialisasi</option>
                        <option value="Umum" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Umum' ? 'selected' : '' }}>Umum</option>
                        <option value="Anak" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Anak' ? 'selected' : '' }}>Anak</option>
                        <option value="Bedah" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Bedah' ? 'selected' : '' }}>Bedah</option>
                        <option value="Jantung" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Jantung' ? 'selected' : '' }}>Jantung</option>
                        <option value="THT" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'THT' ? 'selected' : '' }}>THT</option>
                        <option value="Mata" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Mata' ? 'selected' : '' }}>Mata</option>
                        <option value="Kulit & Kelamin" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Kulit & Kelamin' ? 'selected' : '' }}>Kulit & Kelamin</option>
                        <option value="Saraf" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Saraf' ? 'selected' : '' }}>Saraf</option>
                        <option value="Gigi" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Gigi' ? 'selected' : '' }}>Gigi</option>
                        <option value="Psikiatri" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Psikiatri' ? 'selected' : '' }}>Psikiatri</option>
                        <option value="Penyakit Dalam" {{ old('Spesialisasi', $dokter->Spesialisasi) == 'Penyakit Dalam' ? 'selected' : '' }}>Penyakit Dalam</option>
                    </select>
                </div>
                @error('Spesialisasi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
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
                           class="pl-20 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('No_Telp') border-red-500 @enderror"
                           value="{{ old('No_Telp', $dokter->No_Telp) }}"
                           maxlength="15"
                           pattern="[8-9][0-9]*"
                           title="Masukkan nomor telepon yang dimulai dengan 8 atau 9">
                </div>
                <p class="text-xs text-gray-500 mt-1">Masukkan nomor tanpa angka 0 di depan (contoh: 81234567890)</p>
                @error('No_Telp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
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
                    <textarea name="Alamat" id="Alamat" rows="3" placeholder="Masukkan alamat lengkap"
                              class="pl-10 py-2 pr-3 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('Alamat') border-red-500 @enderror">{{ old('Alamat', $dokter->Alamat) }}</textarea>
                </div>
                @error('Alamat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password Baru
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah password"
                           class="pl-10 py-2 pr-10 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror"
                           minlength="6" maxlength="20">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" id="togglePassword" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Password minimal 6 karakter, maksimal 20 karakter (kosongkan jika tidak ingin mengubah)</p>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Status Aktif -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Status Aktif <span class="text-red-500">*</span>
                </label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="radio" 
                               id="status_aktif_1" 
                               name="status_aktif" 
                               value="1" 
                               class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500"
                               {{ old('status_aktif', $dokter->status_aktif) == '1' ? 'checked' : '' }}>
                        <label for="status_aktif_1" class="ml-3 flex items-center">
                            <span class="text-sm font-medium text-green-600">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="radio" 
                               id="status_aktif_0" 
                               name="status_aktif" 
                               value="0" 
                               class="h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500"
                               {{ old('status_aktif', $dokter->status_aktif) == '0' ? 'checked' : '' }}>
                        <label for="status_aktif_0" class="ml-3 flex items-center">
                            <span class="text-sm font-medium text-red-600">
                                <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                            </span>
                        </label>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Status aktif menentukan dokter dapat melakukan praktik atau tidak</p>
                @error('status_aktif')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Info Terakhir Diupdate -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                <span>
                    Terakhir diupdate: 
                    <span class="font-medium">{{ $dokter->updated_at ? $dokter->updated_at->format('d/m/Y H:i') : 'Belum pernah diupdate' }}</span>
                </span>
            </div>
        </div>
        
        <!-- Button Section -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('dokter.index') }}" 
               class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
            <button type="submit" 
                    class="inline-flex justify-center items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-save mr-2"></i> Update Data
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
        
        // Format nomor telepon
        const phoneInput = document.getElementById('No_Telp');
        if (phoneInput) {
            // Event untuk memastikan hanya angka yang bisa dimasukkan
            phoneInput.addEventListener('input', function() {
                // Simpan posisi cursor
                const cursorPos = this.selectionStart;
                
                // Hapus semua karakter non-digit
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Hapus angka 0 di depan jika dimasukkan
                if (this.value.startsWith('0')) {
                    this.value = this.value.substring(1);
                }
                
                // Batasi panjang input (13 digit untuk +62xxx)
                if (this.value.length > 13) {
                    this.value = this.value.substring(0, 13);
                }
                
                // Restore cursor position
                this.setSelectionRange(cursorPos, cursorPos);
            });
            
            // Validasi saat blur (kehilangan fokus)
            phoneInput.addEventListener('blur', function() {
                if (this.value && !this.value.startsWith('8') && !this.value.startsWith('9')) {
                    alert('Nomor telepon harus dimulai dengan angka 8 atau 9');
                    this.focus();
                }
            });
        }

        // Status aktif radio button styling
        const radioButtons = document.querySelectorAll('input[name="status_aktif"]');
        radioButtons.forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Reset all labels
                radioButtons.forEach(function(r) {
                    const label = document.querySelector(`label[for="${r.id}"] span`);
                    if (label) {
                        label.classList.remove('font-bold');
                    }
                });
                
                // Highlight selected option
                const selectedLabel = document.querySelector(`label[for="${this.id}"] span`);
                if (selectedLabel) {
                    selectedLabel.classList.add('font-bold');
                }
            });
        });
        
        // Initial styling for default selected radio
        const defaultSelected = document.querySelector('input[name="status_aktif"]:checked');
        if (defaultSelected) {
            const selectedLabel = document.querySelector(`label[for="${defaultSelected.id}"] span`);
            if (selectedLabel) {
                selectedLabel.classList.add('font-bold');
            }
        }

        // Form validation before submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const namaInput = document.getElementById('Nama_Dokter');
            const statusInput = document.querySelector('input[name="status_aktif"]:checked');
            
            // Validasi nama dokter
            if (!namaInput.value.trim()) {
                e.preventDefault();
                alert('Nama dokter harus diisi');
                namaInput.focus();
                return;
            }
            
            // Validasi status aktif
            if (!statusInput) {
                e.preventDefault();
                alert('Status aktif harus dipilih');
                document.getElementById('status_aktif_1').focus();
                return;
            }
            
            // Format nomor telepon jika ada
            if (phoneInput && phoneInput.value) {
                // Pastikan format sudah benar
                let phoneValue = phoneInput.value.replace(/[^0-9]/g, '');
                if (phoneValue.startsWith('0')) {
                    phoneValue = phoneValue.substring(1);
                }
                // Set nilai final tanpa mengubah tampilan (akan diproses di controller)
                phoneInput.value = phoneValue;
            }
            
            // Konfirmasi update
            if (!confirm('Apakah Anda yakin ingin mengupdate data dokter ini?')) {
                e.preventDefault();
                return;
            }
        });
    });
</script>
@endpush

@endsection