@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-6">
        <a href="{{ route('dashboard.orangtua') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
        <span class="breadcrumb-separator">/</span>
        <a href="{{ route('orangtua.siswa.show') }}" class="text-blue-600 hover:text-blue-800">Data Siswa Saya</a>
        <span class="breadcrumb-separator">/</span>
        <span class="text-gray-500">Edit Data</span>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="page-title">Edit Data Siswa</h1>
        <p class="page-subtitle">Perbarui informasi data siswa (hanya field tertentu yang dapat diubah)</p>
    </div>

    <!-- Alert Info -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Catatan:</strong> Sebagai orang tua, Anda hanya dapat mengubah informasi pribadi dasar siswa. 
                    Untuk perubahan data akademik atau administratif lainnya, silakan hubungi pihak sekolah.
                </p>
            </div>
        </div>
    </div>

    <!-- Form Edit -->
    <form action="{{ route('orangtua.siswa.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Data Pribadi Siswa -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-600"></i>
                    Data Pribadi Siswa
                </h3>
                <p class="text-sm text-gray-600 mt-1">Field yang dapat diubah oleh orang tua</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Siswa -->
                    <div>
                        <label for="nama_siswa" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap Siswa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nama_siswa" 
                               name="nama_siswa" 
                               value="{{ old('nama_siswa', $siswa->nama_siswa) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_siswa') border-red-500 @enderror"
                               required>
                        @error('nama_siswa')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ID Siswa (Read Only) -->
                    <div>
                        <label for="id_siswa" class="block text-sm font-medium text-gray-700 mb-2">
                            ID Siswa
                        </label>
                        <input type="text" 
                               id="id_siswa" 
                               value="{{ $siswa->id_siswa }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                               readonly>
                        <p class="mt-1 text-xs text-gray-500">ID Siswa tidak dapat diubah</p>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                            Tempat Lahir
                        </label>
                        <input type="text" 
                               id="tempat_lahir" 
                               name="tempat_lahir" 
                               value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tempat_lahir') border-red-500 @enderror"
                               placeholder="Masukkan tempat lahir">
                        @error('tempat_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Lahir
                        </label>
                        <input type="date" 
                               id="tanggal_lahir" 
                               name="tanggal_lahir" 
                               value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir') border-red-500 @enderror"
                               max="{{ date('Y-m-d') }}">
                        @error('tanggal_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin (Read Only) -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kelamin
                        </label>
                        <input type="text" 
                               id="jenis_kelamin" 
                               value="{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                               readonly>
                        <p class="mt-1 text-xs text-gray-500">Jenis kelamin tidak dapat diubah</p>
                    </div>

                    <!-- Tanggal Masuk (Read Only) -->
                    <div>
                        <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Masuk Sekolah
                        </label>
                        <input type="text" 
                               id="tanggal_masuk" 
                               value="{{ $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->locale('id')->isoFormat('DD MMMM YYYY') : '-' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                               readonly>
                        <p class="mt-1 text-xs text-gray-500">Tanggal masuk tidak dapat diubah</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Akademik (Read Only) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-graduation-cap mr-2 text-green-600"></i>
                    Data Akademik
                </h3>
                <p class="text-sm text-gray-600 mt-1">Informasi akademik (tidak dapat diubah)</p>
            </div>
            <div class="p-6">
                @if($siswa->detailSiswa)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                            <input type="text" 
                                   value="{{ $siswa->detailSiswa->kelas ? $siswa->detailSiswa->kelas->Nama_Kelas : '-' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                                   readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                            <input type="text" 
                                   value="{{ $siswa->detailSiswa->kelas && $siswa->detailSiswa->kelas->jurusan ? $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan : '-' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                                   readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                            <input type="text" 
                                   value="{{ $siswa->detailSiswa->kelas && $siswa->detailSiswa->kelas->Tahun_Ajaran ? $siswa->detailSiswa->kelas->Tahun_Ajaran : '-' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                                   readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <input type="text" 
                                   value="{{ $siswa->status_aktif ? 'Aktif' : 'Tidak Aktif' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                                   readonly>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                        <p class="text-gray-500">Data akademik belum tersedia</p>
                    </div>
                @endif
                
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Data akademik hanya dapat diubah oleh administrator sekolah. Jika terdapat kesalahan, 
                                silakan hubungi bagian tata usaha atau administrator sistem.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pastikan data yang Anda masukkan sudah benar sebelum menyimpan perubahan.
                </div>
                
                <div class="flex space-x-3">
                    <a href="{{ route('orangtua.siswa.show') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out flex items-center"
                            id="submit-btn">
                        <i class="fas fa-save mr-2"></i>
                        <span>Simpan Perubahan</span>
                        <i class="fas fa-spinner fa-spin ml-2 hidden" id="loading-spinner"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Info Panel -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-question-circle mr-2 text-blue-600"></i>
            Bantuan
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Field yang Dapat Diubah:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Nama lengkap siswa</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Tempat lahir</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Tanggal lahir</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-900 mb-2">Field yang Tidak Dapat Diubah:</h4>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><i class="fas fa-times text-red-500 mr-2"></i>ID Siswa</li>
                    <li><i class="fas fa-times text-red-500 mr-2"></i>Jenis kelamin</li>
                    <li><i class="fas fa-times text-red-500 mr-2"></i>Data akademik (kelas, jurusan)</li>
                    <li><i class="fas fa-times text-red-500 mr-2"></i>Status siswa</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
            <p class="text-sm text-blue-700">
                <strong>Perlu bantuan?</strong> Hubungi bagian tata usaha sekolah di 
                <a href="tel:(0274)371243" class="font-medium underline">(0274) 371243</a> atau 
                <a href="mailto:slbn1bantul@gmail.com" class="font-medium underline">slbn1bantul@gmail.com</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    const loadingSpinner = document.getElementById('loading-spinner');
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        // Show loading state
        submitBtn.disabled = true;
        loadingSpinner.classList.remove('hidden');
        submitBtn.querySelector('span').textContent = 'Menyimpan...';
        
        // Basic client-side validation
        const namaInput = document.getElementById('nama_siswa');
        
        if (!namaInput.value.trim()) {
            e.preventDefault();
            alert('Nama siswa harus diisi');
            
            // Reset loading state
            submitBtn.disabled = false;
            loadingSpinner.classList.add('hidden');
            submitBtn.querySelector('span').textContent = 'Simpan Perubahan';
            
            namaInput.focus();
            return false;
        }
        
        // If validation passes, form will submit normally
        return true;
    });
    
    // Auto-capitalize nama siswa
    const namaInput = document.getElementById('nama_siswa');
    namaInput.addEventListener('input', function() {
        this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
    });
    
    // Auto-capitalize tempat lahir
    const tempatLahirInput = document.getElementById('tempat_lahir');
    tempatLahirInput.addEventListener('input', function() {
        this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
    });
    
    // Calculate age when birth date changes
    const tanggalLahirInput = document.getElementById('tanggal_lahir');
    tanggalLahirInput.addEventListener('change', function() {
        if (this.value) {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            // Show age info (optional)
            let ageInfo = this.parentElement.querySelector('.age-info');
            if (!ageInfo) {
                ageInfo = document.createElement('p');
                ageInfo.className = 'age-info mt-1 text-xs text-blue-600';
                this.parentElement.appendChild(ageInfo);
            }
            ageInfo.textContent = `Umur: ${age} tahun`;
        }
    });
    
    // Trigger age calculation on page load if birth date exists
    if (tanggalLahirInput.value) {
        tanggalLahirInput.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection