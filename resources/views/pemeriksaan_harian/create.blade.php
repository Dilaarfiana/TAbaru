{{-- File: resources/views/pemeriksaan_harian/create.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: CRU ACCESS, DOKTER: NO ACCESS, ORANG TUA: REDIRECT --}}
@extends('layouts.app')

@section('page_title', 'Tambah Pemeriksaan Harian')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Redirect orang tua ke halaman khusus mereka
    if ($isOrangTua) {
        header('Location: ' . route('orangtua.riwayat.pemeriksaan_harian'));
        exit;
    }
    
    // Redirect dokter ke dashboard karena tidak ada akses
    if ($isDokter) {
        header('Location: ' . route('dashboard.dokter'));
        exit;
    }
    
    // Check if user has permission to create
    if (!in_array($userLevel, ['admin', 'petugas'])) {
        header('Location: ' . route('dashboard'));
        exit;
    }
    
    // Define routes based on user role
    $baseRoute = $isAdmin ? 'pemeriksaan_harian' : 'petugas.pemeriksaan_harian';
    $indexRoute = $baseRoute . '.index';
    $storeRoute = $baseRoute . '.store';
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Form Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-calendar-plus text-blue-500 mr-3 text-xl"></i>
                <h2 class="text-xl font-bold text-gray-800">Tambah Pemeriksaan Harian</h2>
                @if($isPetugas)
                    <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                        <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                    </span>
                @elseif($isAdmin)
                    <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        <i class="fas fa-user-shield mr-1"></i>Akses Admin
                    </span>
                @endif
            </div>
            <div class="flex items-center space-x-2">
                @if(isset($id))
                <span class="bg-blue-100 text-blue-800 text-sm font-bold py-1 px-3 rounded-full flex items-center">
                    <i class="fas fa-tag mr-1"></i>
                    ID: {{ $id }}
                </span>
                @endif
                <a href="{{ route($indexRoute) }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            <!-- Alert Messages -->
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Mohon perbaiki kesalahan berikut:</p>
                            <ul class="text-sm text-red-700 list-disc list-inside mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-red-500 hover:text-red-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            @if(session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">{!! session('warning') !!}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-yellow-500 hover:text-yellow-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif

            <!-- Info Access Level -->
            @if($isPetugas)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Anda mengakses form tambah pemeriksaan harian dengan <strong>Akses Petugas</strong>. 
                            Anda dapat menambah dan mengedit data pemeriksaan harian.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-blue-800 mb-1">Informasi Pemeriksaan Harian</h3>
                        <p class="text-sm text-blue-700 mb-2">
                            Pemeriksaan harian adalah pencatatan kondisi kesehatan siswa yang dilakukan secara rutin oleh petugas UKS. 
                            Data ini penting untuk monitoring kesehatan siswa di sekolah dan dokumentasi tindakan medis yang diberikan.
                        </p>
                        
                        <!-- Guidelines -->
                        <div class="mt-3 p-3 bg-blue-100 border border-blue-300 rounded">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">
                                <i class="fas fa-clipboard-list mr-1"></i>
                                Panduan Pengisian:
                            </h4>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>• Pilih siswa yang akan diperiksa dari daftar siswa aktif</li>
                                <li>• Pilih petugas UKS yang melakukan pemeriksaan</li>
                                <li>• Isikan hasil pemeriksaan dengan detail dan akurat</li>
                                <li>• Gunakan template yang tersedia untuk mempermudah pengisian</li>
                                <li>• Pastikan tanggal dan waktu pemeriksaan sesuai</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route($storeRoute) }}" method="POST" id="pemeriksaanHarianForm">
                @csrf
                
                @if(isset($id))
                <input type="hidden" id="Id_Harian" name="Id_Harian" value="{{ $id }}">
                @endif
                
                <!-- Informasi Dasar -->
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <i class="fas fa-clock text-blue-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Waktu Pemeriksaan</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal dan Jam -->
                        <div>
                            <label for="Tanggal_Jam" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>
                                Tanggal & Jam Pemeriksaan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="datetime-local" 
                                    id="Tanggal_Jam" 
                                    name="Tanggal_Jam" 
                                    value="{{ old('Tanggal_Jam', now()->format('Y-m-d\TH:i')) }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Masukkan tanggal dan waktu pemeriksaan dilakukan</p>
                            @error('Tanggal_Jam')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle text-gray-500 mr-1"></i>
                                Preview Waktu
                            </h4>
                            <div id="timePreview" class="text-sm text-gray-600">
                                Pilih tanggal dan waktu untuk melihat preview
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid untuk Siswa dan Petugas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Kolom Kiri: Siswa -->
                    <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                            <i class="fas fa-user-graduate text-green-500 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-800">Data Siswa</h3>
                        </div>
                        
                        <div>
                            <label for="Id_Siswa" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Siswa <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <select id="Id_Siswa" name="Id_Siswa" required
                                    class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 appearance-none">
                                    <option value="">-- Pilih Siswa --</option>
                                    @if(isset($siswaList))
                                        @foreach($siswaList as $siswa)
                                            <option value="{{ $siswa->id_siswa }}" {{ old('Id_Siswa') == $siswa->id_siswa ? 'selected' : '' }}>
                                                {{ $siswa->id_siswa }} - {{ $siswa->nama_siswa }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih siswa yang akan diperiksa</p>
                            @error('Id_Siswa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Siswa Info Preview -->
                        <div id="siswaInfo" class="mt-4 p-3 bg-white rounded-md border border-green-200 hidden">
                            <h4 class="text-sm font-medium text-green-700 mb-2">
                                <i class="fas fa-user text-green-600 mr-1"></i>
                                Informasi Siswa
                            </h4>
                            <div id="siswaDetails" class="text-sm text-gray-600">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Petugas UKS -->
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-5 shadow-sm">
                        <div class="flex items-center mb-4 border-b border-purple-200 pb-2">
                            <i class="fas fa-user-nurse text-purple-500 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-800">Petugas UKS</h3>
                        </div>
                        
                        <div>
                            <label for="NIP" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Petugas <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-id-badge text-gray-400"></i>
                                </div>
                                <select id="NIP" name="NIP" required
                                    class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500 appearance-none">
                                    <option value="">-- Pilih Petugas UKS --</option>
                                    @if(isset($petugasList))
                                        @foreach($petugasList as $petugas)
                                            <option value="{{ $petugas->NIP }}" {{ old('NIP') == $petugas->NIP ? 'selected' : '' }}>
                                                {{ $petugas->NIP }} - {{ $petugas->nama_petugas_uks }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih petugas UKS yang melakukan pemeriksaan</p>
                            @error('NIP')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Petugas Info Preview -->
                        <div id="petugasInfo" class="mt-4 p-3 bg-white rounded-md border border-purple-200 hidden">
                            <h4 class="text-sm font-medium text-purple-700 mb-2">
                                <i class="fas fa-user-md text-purple-600 mr-1"></i>
                                Informasi Petugas
                            </h4>
                            <div id="petugasDetails" class="text-sm text-gray-600">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hasil Pemeriksaan -->
                <div class="bg-orange-50 border border-orange-100 rounded-lg p-5 shadow-sm mb-6">
                    <div class="flex items-center mb-4 border-b border-orange-200 pb-2">
                        <i class="fas fa-clipboard-check text-orange-500 mr-2"></i>
                        <h3 class="text-lg font-medium text-gray-800">Hasil Pemeriksaan</h3>
                    </div>
                    
                    <div>
                        <label for="Hasil_Pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-notes-medical text-orange-500 mr-1"></i>
                            Detail Hasil Pemeriksaan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <textarea id="Hasil_Pemeriksaan" name="Hasil_Pemeriksaan" rows="6" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                                placeholder="Masukkan hasil pemeriksaan secara lengkap...">{{ old('Hasil_Pemeriksaan') }}</textarea>
                            <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                <span id="charCount">0</span> karakter
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Deskripsikan hasil pemeriksaan dengan lengkap termasuk kondisi siswa, gejala yang dialami, dan rekomendasi tindakan</p>
                        @error('Hasil_Pemeriksaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Template Hasil Pemeriksaan -->
                    <div class="mt-4 p-3 bg-white rounded-md border border-orange-200">
                        <h4 class="text-sm font-medium text-orange-700 mb-2">
                            <i class="fas fa-lightbulb text-orange-600 mr-1"></i>
                            Template Hasil Pemeriksaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <button type="button" class="template-btn text-left p-2 text-xs bg-gray-50 hover:bg-orange-100 rounded border transition-colors" 
                                data-template="Kondisi: Sehat, tidak ada keluhan&#10;Suhu: Normal (36.5°C)&#10;Tekanan Darah: Normal&#10;Tindakan: -&#10;Rekomendasi: Pertahankan pola hidup sehat">
                                <i class="fas fa-heart text-green-500 mr-1"></i> Kondisi Sehat
                            </button>
                            <button type="button" class="template-btn text-left p-2 text-xs bg-gray-50 hover:bg-orange-100 rounded border transition-colors"
                                data-template="Kondisi: Demam ringan&#10;Suhu: 37.5°C&#10;Keluhan: Pusing, lemas, nyeri kepala&#10;Tindakan: Kompres dingin, istirahat di UKS&#10;Rekomendasi: Monitor suhu, banyak minum air putih. Jika demam berlanjut >2 hari, konsultasi ke dokter">
                                <i class="fas fa-thermometer-half text-red-500 mr-1"></i> Demam Ringan
                            </button>
                            <button type="button" class="template-btn text-left p-2 text-xs bg-gray-50 hover:bg-orange-100 rounded border transition-colors"
                                data-template="Kondisi: Sakit perut&#10;Keluhan: Mual, nyeri perut, tidak nafsu makan&#10;Tindakan: Minum air hangat, posisi tidur miring, istirahat&#10;Rekomendasi: Hindari makanan pedas dan berminyak, makan porsi kecil tapi sering">
                                <i class="fas fa-hand-holding-medical text-blue-500 mr-1"></i> Sakit Perut
                            </button>
                            <button type="button" class="template-btn text-left p-2 text-xs bg-gray-50 hover:bg-orange-100 rounded border transition-colors"
                                data-template="Kondisi: Luka ringan&#10;Lokasi: [sebutkan lokasi luka]&#10;Jenis: Luka lecet/sayat superfisial&#10;Tindakan: Cuci luka dengan air bersih, beri antiseptik (betadine), tutup dengan plester steril&#10;Rekomendasi: Jaga kebersihan luka, ganti plester jika basah/kotor, hindari menggaruk">
                                <i class="fas fa-band-aid text-yellow-500 mr-1"></i> Luka Ringan
                            </button>
                            <button type="button" class="template-btn text-left p-2 text-xs bg-gray-50 hover:bg-orange-100 rounded border transition-colors"
                                data-template="Kondisi: Sakit kepala&#10;Keluhan: Nyeri kepala, pusing, mata berkunang-kunang&#10;Tindakan: Istirahat di ruang tenang, kompres dingin di dahi&#10;Rekomendasi: Cukup tidur, hindari begadang, makan teratur">
                                <i class="fas fa-head-side-cough text-purple-500 mr-1"></i> Sakit Kepala
                            </button>
                            <button type="button" class="template-btn text-left p-2 text-xs bg-gray-50 hover:bg-orange-100 rounded border transition-colors"
                                data-template="Kondisi: Batuk pilek&#10;Keluhan: Batuk kering, hidung tersumbat, bersin-bersin&#10;Tindakan: Minum air hangat, istirahat&#10;Rekomendasi: Gunakan masker, hindari AC langsung, banyak minum air putih">
                                <i class="fas fa-lungs text-teal-500 mr-1"></i> Batuk Pilek
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Form Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.location.href='{{ route($indexRoute) }}'" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-times mr-2 text-gray-500"></i>
                        Batal
                    </button>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Simpan Pemeriksaan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // PERBAIKAN: Hapus auto-close alerts - notifikasi akan tetap muncul sampai user menutup manual
    // Sekarang notifikasi success/error/warning akan tetap muncul dan tidak hilang otomatis

    // PERBAIKAN: Hapus validasi max date yang menyebabkan error timezone
    // Sekarang user bisa input tanggal tanpa batasan maksimum dari JavaScript
    const dateTimeInput = document.getElementById('Tanggal_Jam');
    if (dateTimeInput) {
        // Hanya update time preview, tanpa batasan max date
        dateTimeInput.addEventListener('change', updateTimePreview);
        updateTimePreview(); // Initial update
    }
    
    function updateTimePreview() {
        const timePreview = document.getElementById('timePreview');
        if (dateTimeInput && dateTimeInput.value && timePreview) {
            const date = new Date(dateTimeInput.value);
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                timeZone: 'Asia/Jakarta'
            };
            timePreview.innerHTML = `
                <div class="text-blue-600 font-medium">${date.toLocaleDateString('id-ID', options)} WIB</div>
                <div class="text-xs text-gray-500 mt-1">Hari: ${date.toLocaleDateString('id-ID', {weekday: 'long'})}</div>
            `;
        } else if (timePreview) {
            timePreview.innerHTML = 'Pilih tanggal dan waktu untuk melihat preview';
        }
    }

    // Character counter for textarea
    const hasilTextarea = document.getElementById('Hasil_Pemeriksaan');
    const charCount = document.getElementById('charCount');
    
    if (hasilTextarea && charCount) {
        // Initial count
        charCount.textContent = hasilTextarea.value.length;
        
        hasilTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            
            // Add visual feedback for length
            if (this.value.length > 500) {
                charCount.classList.add('text-orange-500');
                charCount.classList.remove('text-gray-400');
            } else {
                charCount.classList.remove('text-orange-500');
                charCount.classList.add('text-gray-400');
            }
        });
    }

    // Template buttons
    const templateBtns = document.querySelectorAll('.template-btn');
    templateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const template = this.dataset.template;
            if (hasilTextarea && template) {
                hasilTextarea.value = template.replace(/&#10;/g, '\n');
                hasilTextarea.focus();
                if (charCount) {
                    charCount.textContent = hasilTextarea.value.length;
                }
                
                // Trigger input event for other listeners
                hasilTextarea.dispatchEvent(new Event('input'));
            }
        });
    });

    // Siswa selection handler
    const siswaSelect = document.getElementById('Id_Siswa');
    const siswaInfo = document.getElementById('siswaInfo');
    const siswaDetails = document.getElementById('siswaDetails');
    
    if (siswaSelect && siswaInfo && siswaDetails) {
        siswaSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const text = selectedOption.textContent;
                const [id, nama] = text.split(' - ');
                
                siswaDetails.innerHTML = `
                    <div class="grid grid-cols-1 gap-2">
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-green-500 mr-2 w-4"></i>
                            <span><strong>ID Siswa:</strong> ${id}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user text-green-500 mr-2 w-4"></i>
                            <span><strong>Nama:</strong> ${nama}</span>
                        </div>
                    </div>
                `;
                siswaInfo.classList.remove('hidden');
            } else {
                siswaInfo.classList.add('hidden');
            }
        });
    }

    // Petugas selection handler
    const petugasSelect = document.getElementById('NIP');
    const petugasInfo = document.getElementById('petugasInfo');
    const petugasDetails = document.getElementById('petugasDetails');
    
    if (petugasSelect && petugasInfo && petugasDetails) {
        petugasSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const text = selectedOption.textContent;
                const [nip, nama] = text.split(' - ');
                
                petugasDetails.innerHTML = `
                    <div class="grid grid-cols-1 gap-2">
                        <div class="flex items-center">
                            <i class="fas fa-id-badge text-purple-500 mr-2 w-4"></i>
                            <span><strong>NIP:</strong> ${nip}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user-nurse text-purple-500 mr-2 w-4"></i>
                            <span><strong>Nama:</strong> ${nama}</span>
                        </div>
                    </div>
                `;
                petugasInfo.classList.remove('hidden');
            } else {
                petugasInfo.classList.add('hidden');
            }
        });
    }
    
    // Form validation
    const form = document.getElementById('pemeriksaanHarianForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    
    if (form && submitBtn && submitText) {
        form.addEventListener('submit', function(event) {
            const siswaSelect = document.getElementById('Id_Siswa');
            const petugasSelect = document.getElementById('NIP');
            const hasilTextarea = document.getElementById('Hasil_Pemeriksaan');
            const dateTimeInput = document.getElementById('Tanggal_Jam');
            
            let isValid = true;
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            
            // Check required fields
            if (siswaSelect && !siswaSelect.value) {
                markInvalid(siswaSelect, 'Siswa harus dipilih');
                isValid = false;
            } else if (siswaSelect) {
                markValid(siswaSelect);
            }
            
            if (petugasSelect && !petugasSelect.value) {
                markInvalid(petugasSelect, 'Petugas UKS harus dipilih');
                isValid = false;
            } else if (petugasSelect) {
                markValid(petugasSelect);
            }
            
            if (hasilTextarea && !hasilTextarea.value.trim()) {
                markInvalid(hasilTextarea, 'Hasil Pemeriksaan harus diisi');
                isValid = false;
            } else if (hasilTextarea) {
                markValid(hasilTextarea);
            }
            
            if (dateTimeInput && !dateTimeInput.value) {
                markInvalid(dateTimeInput, 'Tanggal dan jam pemeriksaan harus diisi');
                isValid = false;
            } else if (dateTimeInput) {
                markValid(dateTimeInput);
            }
            
            if (!isValid) {
                event.preventDefault();
                submitBtn.disabled = false;
                submitText.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Pemeriksaan';
                
                // Scroll to first error
                const firstError = document.querySelector('.border-red-500');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
    
    function markInvalid(element, message) {
        element.classList.add('border-red-500');
        element.classList.remove('border-gray-300');
        
        // Add error message if not exists
        const parent = element.closest('div');
        let errorElement = parent.querySelector('.text-red-600');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'mt-1 text-sm text-red-600';
            parent.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }
    
    function markValid(element) {
        element.classList.remove('border-red-500');
        element.classList.add('border-gray-300');
        
        // Remove error message if exists
        const parent = element.closest('div');
        const errorElement = parent.querySelector('.text-red-600');
        if (errorElement && !errorElement.classList.contains('mt-1')) {
            // Don't remove Laravel validation errors
            errorElement.remove();
        }
    }

    // Initialize if there are old values
    if (siswaSelect && siswaSelect.value) {
        siswaSelect.dispatchEvent(new Event('change'));
    }
    if (petugasSelect && petugasSelect.value) {
        petugasSelect.dispatchEvent(new Event('change'));
    }
    
    // Initialize time preview
    updateTimePreview();
});
</script>
@endpush
@endsection