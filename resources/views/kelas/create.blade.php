@extends('layouts.app')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Tambah Kelas Baru</h2>
            </div>
            <a href="{{ route('kelas.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

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
                        Kode Kelas akan dibuat otomatis menggunakan format: <span class="font-mono font-medium">KL001, KL002</span> dst.
                        Nama kelas bisa berupa format sederhana seperti <span class="font-medium">"1", "2A", "XA1", "XI IPA 1"</span> sesuai kebutuhan sekolah Anda.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('kelas.store') }}" method="POST" id="formKelas">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <!-- Kode Kelas (Auto-generated, hidden) -->
                <input type="hidden" id="Kode_Kelas_hidden" name="Kode_Kelas" value="{{ old('Kode_Kelas', $nextId) }}">
                
                <!-- Preview Kode Kelas (read-only) -->
                <div>
                    <label for="Kode_Kelas_preview" class="block text-sm font-medium text-gray-700 mb-1">
                        Kode Kelas
                    </label>
                    <div class="relative rounded-md shadow-sm bg-gray-50 border border-gray-300">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="Kode_Kelas_preview" 
                               value="{{ old('Kode_Kelas', $nextId) }}" 
                               class="pl-10 block w-full bg-gray-50 border-0 rounded-md h-10 focus:ring-0 focus:outline-none text-gray-600"
                               readonly disabled>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        ID akan dibuat otomatis dengan format KL001
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
                               value="{{ old('Nama_Kelas') }}" 
                               required 
                               maxlength="30" 
                               placeholder="Contoh: 1, 2A, XA1, XI IPA 1"
                               class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('Nama_Kelas') border-red-300 @enderror"
                               oninput="updatePreviewNamaKelas()">
                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-gray-500">
                            <span class="font-medium">Preview nama lengkap:</span> 
                            <span id="preview_nama_lengkap" class="text-blue-600 font-medium">-</span>
                        </p>
                    </div>
                    @error('Nama_Kelas')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
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
                                onchange="updatePreviewNamaKelas()"
                                class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none @error('Kode_Jurusan') border-red-300 @enderror">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach ($jurusan as $jur)
                                <option value="{{ $jur->Kode_Jurusan }}" 
                                        data-nama="{{ $jur->Nama_Jurusan }}"
                                        {{ (old('Kode_Jurusan', $selectedJurusan) == $jur->Kode_Jurusan) ? 'selected' : '' }}>
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
                    @error('Kode_Jurusan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
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
                               value="{{ old('Tahun_Ajaran', $currentTahunAjaran) }}" 
                               maxlength="10" 
                               placeholder="Contoh: 2024/2025"
                               class="pl-10 block w-full border border-gray-300 rounded-md h-10 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('Tahun_Ajaran') border-red-300 @enderror">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Format tahun ajaran: 2024/2025
                    </p>
                    @error('Tahun_Ajaran')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Form Buttons -->
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" onclick="window.location.href='{{ route('kelas.index') }}'" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Panduan Pengisian -->
<div class="mt-8 max-w-5xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
    <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
        <h2 class="text-lg font-bold text-gray-800">
            <i class="fas fa-book mr-2 text-blue-500"></i>Panduan Pengisian Data Kelas
        </h2>
    </div>
    <div class="p-6">
        <div class="bg-blue-50 rounded-lg p-5 text-blue-800 border-l-4 border-blue-400 shadow-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-lightbulb text-2xl text-blue-500 mr-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base mb-2">Tips Pengisian Form Kelas</h3>
                    <ul class="list-disc list-inside text-sm space-y-2 ml-2">
                        <li><strong>Kode Kelas:</strong> Terisi otomatis dengan format KL001, KL002, dst.</li>
                        <li><strong>Nama Kelas:</strong> Bisa berupa format sederhana seperti:
                            <ul class="list-disc list-inside ml-4 mt-1 space-y-1">
                                <li><span class="font-mono bg-gray-100 px-1 rounded">"1"</span> untuk kelas 1</li>
                                <li><span class="font-mono bg-gray-100 px-1 rounded">"2A"</span> untuk kelas 2A</li>
                                <li><span class="font-mono bg-gray-100 px-1 rounded">"XA1"</span> untuk kelas X jurusan A1</li>
                                <li><span class="font-mono bg-gray-100 px-1 rounded">"XI IPA 1"</span> untuk format lengkap</li>
                            </ul>
                        </li>
                        <li><strong>Jurusan:</strong> Pilih jurusan yang sesuai untuk kelas ini.</li>
                        <li><strong>Tahun Ajaran:</strong> Terisi otomatis dengan format "{{ $currentTahunAjaran }}".</li>
                        <li><strong>Jumlah Siswa:</strong> Akan dihitung otomatis saat siswa didaftarkan ke kelas.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePreviewNamaKelas() {
    const namaKelas = document.getElementById('Nama_Kelas').value;
    const jurusanSelect = document.getElementById('Kode_Jurusan');
    const selectedOption = jurusanSelect.options[jurusanSelect.selectedIndex];
    const namaJurusan = selectedOption.getAttribute('data-nama') || '';
    const previewElement = document.getElementById('preview_nama_lengkap');
    
    if (namaKelas && namaJurusan) {
        previewElement.textContent = `Kelas ${namaKelas} - ${namaJurusan}`;
    } else if (namaKelas) {
        previewElement.textContent = `Kelas ${namaKelas}`;
    } else {
        previewElement.textContent = '-';
    }
}

// Initialize preview on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePreviewNamaKelas();
});
</script>
@endsection