@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-green-500 to-green-600 text-white flex items-center justify-center text-lg font-semibold">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">Ubah Password</h1>
                        <p class="text-sm text-gray-600">
                            @if(session('user_level') === 'admin')
                                <i class="fas fa-user-shield text-gray-500 mr-1"></i> Administrator
                            @elseif(session('user_level') === 'petugas')
                                <i class="fas fa-clinic-medical text-green-500 mr-1"></i> Petugas UKS
                            @elseif(session('user_level') === 'dokter')
                                <i class="fas fa-user-md text-blue-500 mr-1"></i> Dokter
                            @elseif(session('user_level') === 'orang_tua')
                                <i class="fas fa-users text-purple-500 mr-1"></i> Orang Tua
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('profile.show') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Change Password Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-key text-green-500 mr-2"></i>
                Ubah Password
            </h3>
            <p class="text-sm text-gray-600 mt-1">Pastikan password baru Anda aman dan mudah diingat</p>
        </div>

        <form method="POST" action="{{ route('change.password.update') }}" class="px-6 py-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               required
                               placeholder="Masukkan password saat ini"
                               class="pl-10 pr-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500 @error('current_password') border-red-300 @enderror">
                        <button type="button" 
                                onclick="togglePassword('current_password')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="current_password_icon"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <input type="password" 
                               name="new_password" 
                               id="new_password" 
                               required
                               minlength="6"
                               placeholder="Masukkan password baru (minimal 6 karakter)"
                               class="pl-10 pr-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500 @error('new_password') border-red-300 @enderror">
                        <button type="button" 
                                onclick="togglePassword('new_password')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="new_password_icon"></i>
                        </button>
                    </div>
                    @error('new_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-2">
                        <div class="text-xs text-gray-500">
                            Password harus:
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li id="length-check" class="text-gray-400">Minimal 6 karakter</li>
                                <li id="letter-check" class="text-gray-400">Mengandung huruf</li>
                                <li id="number-check" class="text-gray-400">Mengandung angka (disarankan)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-check-circle text-gray-400"></i>
                        </div>
                        <input type="password" 
                               name="new_password_confirmation" 
                               id="new_password_confirmation" 
                               required
                               placeholder="Ulangi password baru"
                               class="pl-10 pr-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <button type="button" 
                                onclick="togglePassword('new_password_confirmation')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="new_password_confirmation_icon"></i>
                        </button>
                    </div>
                    <div id="password-match" class="mt-2 text-sm" style="display: none;"></div>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Tips Keamanan Password</h4>
                        <div class="mt-1 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                                <li>Hindari menggunakan informasi personal seperti nama atau tanggal lahir</li>
                                <li>Jangan gunakan password yang sama untuk akun lain</li>
                                <li>Ganti password secara berkala untuk keamanan maksimal</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('profile.show') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </a>
                    <button type="submit" 
                            id="submit-btn"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-1"></i>
                        Ubah Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    const submitBtn = document.getElementById('submit-btn');
    
    // Password strength validation
    newPassword.addEventListener('input', function() {
        const password = this.value;
        
        // Length check
        const lengthCheck = document.getElementById('length-check');
        if (password.length >= 6) {
            lengthCheck.className = 'text-green-600';
            lengthCheck.innerHTML = '✓ Minimal 6 karakter';
        } else {
            lengthCheck.className = 'text-gray-400';
            lengthCheck.innerHTML = 'Minimal 6 karakter';
        }
        
        // Letter check
        const letterCheck = document.getElementById('letter-check');
        if (/[a-zA-Z]/.test(password)) {
            letterCheck.className = 'text-green-600';
            letterCheck.innerHTML = '✓ Mengandung huruf';
        } else {
            letterCheck.className = 'text-gray-400';
            letterCheck.innerHTML = 'Mengandung huruf';
        }
        
        // Number check
        const numberCheck = document.getElementById('number-check');
        if (/[0-9]/.test(password)) {
            numberCheck.className = 'text-green-600';
            numberCheck.innerHTML = '✓ Mengandung angka';
        } else {
            numberCheck.className = 'text-gray-400';
            numberCheck.innerHTML = 'Mengandung angka (disarankan)';
        }
        
        checkPasswordMatch();
    });
    
    // Password match validation
    confirmPassword.addEventListener('input', checkPasswordMatch);
    
    function checkPasswordMatch() {
        const matchDiv = document.getElementById('password-match');
        if (confirmPassword.value === '') {
            matchDiv.style.display = 'none';
            return;
        }
        
        matchDiv.style.display = 'block';
        if (newPassword.value === confirmPassword.value) {
            matchDiv.className = 'mt-2 text-sm text-green-600';
            matchDiv.innerHTML = '✓ Password cocok';
            submitBtn.disabled = false;
        } else {
            matchDiv.className = 'mt-2 text-sm text-red-600';
            matchDiv.innerHTML = '✗ Password tidak cocok';
            submitBtn.disabled = true;
        }
    }
    
    // Form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengubah Password...';
        
        setTimeout(() => {
            if (!form.checkValidity()) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Ubah Password';
            }
        }, 3000);
    });
});

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye text-gray-400 hover:text-gray-600';
    }
}
</script>
@endpush
@endsection