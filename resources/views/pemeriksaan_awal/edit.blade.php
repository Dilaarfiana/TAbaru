@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-5 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <i class="fas fa-edit text-indigo-600 text-2xl"></i>
            <h1 class="text-xl font-bold text-gray-800">Edit Pemeriksaan Awal</h1>
        </div>
        <div class="text-sm text-gray-500 bg-indigo-50 px-3 py-1 rounded-full">
            <span class="font-semibold">ID:</span> {{ $pemeriksaanAwal->Id_PreAwal }}
        </div>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-4 mt-4">
        <div class="flex items-center mb-2">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
            <p class="font-semibold">Mohon perbaiki kesalahan berikut:</p>
        </div>
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('pemeriksaan_awal.update', $pemeriksaanAwal->Id_PreAwal) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="hidden">
            <input type="text" id="Id_PreAwal" name="Id_PreAwal" value="{{ $pemeriksaanAwal->Id_PreAwal }}">
        </div>
        
        <!-- Informasi Dasar -->
        <div class="bg-white p-4 rounded-lg mb-6 border border-gray-200 shadow-sm">
            <div class="flex items-center mb-4 border-b pb-2">
                <i class="fas fa-info-circle text-blue-500 mr-2 text-xl"></i>
                <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <div class="col-span-1">
                    <label for="Id_DetPrx" class="block text-sm font-medium text-gray-700 mb-1">Detail Pemeriksaan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-link text-blue-500"></i>
                        </div>
                        <select class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="Id_DetPrx" name="Id_DetPrx" required>
                            <option value="">-- Pilih Detail Pemeriksaan --</option>
                            @foreach($detailPemeriksaans as $detailPemeriksaan)
                                <option value="{{ $detailPemeriksaan->Id_DetPrx }}" {{ $pemeriksaanAwal->Id_DetPrx == $detailPemeriksaan->Id_DetPrx ? 'selected' : '' }}>
                                    {{ $detailPemeriksaan->Id_DetPrx }} - {{ \Carbon\Carbon::parse($detailPemeriksaan->Tanggal_Jam)->format('d/m/Y H:i') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dua Kolom Utama-->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom Kiri -->
            <div class="col-span-1 space-y-6">
                <!-- Detail Pemeriksaan -->
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <i class="fas fa-stethoscope text-green-500 mr-2 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Detail Pemeriksaan</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="Pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">Pemeriksaan</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                    <i class="fas fa-clipboard-list text-green-500"></i>
                                </div>
                                <textarea class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" id="Pemeriksaan" name="Pemeriksaan" rows="3">{{ old('Pemeriksaan', $pemeriksaanAwal->Pemeriksaan) }}</textarea>
                            </div>
                        </div>

                        <div>
                            <label for="Keluhan_Dahulu" class="block text-sm font-medium text-gray-700 mb-1">Keluhan Dahulu</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-history text-green-500"></i>
                                </div>
                                <input type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" id="Keluhan_Dahulu" name="Keluhan_Dahulu" value="{{ old('Keluhan_Dahulu', $pemeriksaanAwal->Keluhan_Dahulu) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tanda Vital -->
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <i class="fas fa-heartbeat text-red-500 mr-2 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Tanda Vital</h3>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="Suhu" class="block text-sm font-medium text-gray-700 mb-1">Suhu (°C)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-temperature-high text-red-500"></i>
                                </div>
                                <input type="number" step="0.1" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" id="Suhu" name="Suhu" value="{{ old('Suhu', $pemeriksaanAwal->Suhu) }}" placeholder="36.5">
                            </div>
                        </div>
                        
                        <div>
                            <label for="Nadi" class="block text-sm font-medium text-gray-700 mb-1">Nadi (bpm)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-heart text-red-500"></i>
                                </div>
                                <input type="number" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" id="Nadi" name="Nadi" value="{{ old('Nadi', $pemeriksaanAwal->Nadi) }}" placeholder="80">
                            </div>
                        </div>
                        
                        <div>
                            <label for="Tegangan" class="block text-sm font-medium text-gray-700 mb-1">Tegangan (mmHg)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tachometer-alt text-red-500"></i>
                                </div>
                                <input type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" id="Tegangan" name="Tegangan" value="{{ old('Tegangan', $pemeriksaanAwal->Tegangan) }}" placeholder="120/80">
                            </div>
                        </div>
                        
                        <div>
                            <label for="Pernapasan" class="block text-sm font-medium text-gray-700 mb-1">Pernapasan (rpm)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lungs text-red-500"></i>
                                </div>
                                <input type="number" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" id="Pernapasan" name="Pernapasan" value="{{ old('Pernapasan', $pemeriksaanAwal->Pernapasan) }}" placeholder="16">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-span-1 space-y-6">
                <!-- Tipe -->
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <i class="fas fa-tag text-purple-500 mr-2 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Tipe Pemeriksaan</h3>
                    </div>
                    
                    <div>
                        <label for="Tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tag text-purple-500"></i>
                            </div>
                            <input type="number" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" id="Tipe" name="Tipe" value="{{ old('Tipe', $pemeriksaanAwal->Tipe) }}">
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Nyeri -->
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                    <div class="flex items-center mb-4 border-b pb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 text-xl"></i>
                        <h3 class="text-lg font-semibold text-gray-800">Informasi Nyeri</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="Status_Nyeri" class="block text-sm font-medium text-gray-700 mb-1">Status Nyeri</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-exclamation-circle text-yellow-500"></i>
                                </div>
                                <select class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" id="Status_Nyeri" name="Status_Nyeri">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="0" {{ old('Status_Nyeri', $pemeriksaanAwal->Status_Nyeri) == '0' ? 'selected' : '' }} class="text-green-600">0 - Tidak Ada</option>
                                    <option value="1" {{ old('Status_Nyeri', $pemeriksaanAwal->Status_Nyeri) == '1' ? 'selected' : '' }} class="text-blue-600">1 - Ringan</option>
                                    <option value="2" {{ old('Status_Nyeri', $pemeriksaanAwal->Status_Nyeri) == '2' ? 'selected' : '' }} class="text-yellow-600">2 - Sedang</option>
                                    <option value="3" {{ old('Status_Nyeri', $pemeriksaanAwal->Status_Nyeri) == '3' ? 'selected' : '' }} class="text-red-600">3 - Berat</option>
                                </select>
                            </div>
                        </div>
                        
                        <div id="nyeriDetails" class="space-y-4">
                            <div>
                                <label for="Karakteristik" class="block text-sm font-medium text-gray-700 mb-1">Karakteristik</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-info-circle text-yellow-500"></i>
                                    </div>
                                    <input type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" id="Karakteristik" name="Karakteristik" value="{{ old('Karakteristik', $pemeriksaanAwal->Karakteristik) }}" placeholder="Nyeri tumpul/tajam/berdenyut">
                                </div>
                            </div>
                            
                            <div>
                                <label for="Lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-yellow-500"></i>
                                    </div>
                                    <input type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" id="Lokasi" name="Lokasi" value="{{ old('Lokasi', $pemeriksaanAwal->Lokasi) }}" placeholder="Bagian tubuh yang terasa nyeri">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="Durasi" class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-clock text-yellow-500"></i>
                                        </div>
                                        <input type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" id="Durasi" name="Durasi" value="{{ old('Durasi', $pemeriksaanAwal->Durasi) }}" placeholder="Lama nyeri">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="Frekuensi" class="block text-sm font-medium text-gray-700 mb-1">Frekuensi</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-sync-alt text-yellow-500"></i>
                                        </div>
                                        <input type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50" id="Frekuensi" name="Frekuensi" value="{{ old('Frekuensi', $pemeriksaanAwal->Frekuensi) }}" placeholder="Seberapa sering">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Perubahan -->
        <div class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex items-center mb-4 border-b pb-2">
                <i class="fas fa-history text-gray-500 mr-2 text-xl"></i>
                <h3 class="text-lg font-semibold text-gray-800">Riwayat Perubahan</h3>
            </div>
            
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <div>
                    <span class="font-medium">Dibuat:</span> {{ $pemeriksaanAwal->created_at ? $pemeriksaanAwal->created_at->format('d/m/Y H:i') : '-' }}
                </div>
                <div>
                    <span class="font-medium">Diperbarui terakhir:</span> {{ $pemeriksaanAwal->updated_at ? $pemeriksaanAwal->updated_at->format('d/m/Y H:i') : '-' }}
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="mt-8 border-t pt-5">
            <div class="flex justify-between">
                <a href="{{ route('pemeriksaan_awal.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md transition shadow-sm border border-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <div class="flex space-x-2">
                    <a href="{{ route('pemeriksaan_awal.show', $pemeriksaanAwal->Id_PreAwal) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md transition shadow-sm border border-blue-300">
                        <i class="fas fa-eye mr-2"></i> Lihat Detail
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition shadow-md">
                        <i class="fas fa-save mr-2"></i> Perbarui Data
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Enhanced form interactivity
    document.addEventListener('DOMContentLoaded', function() {
        // Status nyeri dependencies
        const statusNyeriSelect = document.getElementById('Status_Nyeri');
        const nyeriDetails = document.getElementById('nyeriDetails');
        
        statusNyeriSelect.addEventListener('change', function() {
            const nyeriValue = this.value;
            
            // Show/hide other nyeri fields based on selection
            if (nyeriValue === '' || nyeriValue === '0') {
                nyeriDetails.classList.add('opacity-50');
                nyeriDetails.querySelectorAll('input').forEach(input => {
                    input.classList.add('bg-gray-100');
                });
            } else {
                nyeriDetails.classList.remove('opacity-50');
                nyeriDetails.querySelectorAll('input').forEach(input => {
                    input.classList.remove('bg-gray-100');
                });
                
                // Highlight with different colors based on pain level
                if (nyeriValue === '1') {
                    nyeriDetails.classList.add('border-l-4', 'border-blue-500', 'pl-2');
                    nyeriDetails.classList.remove('border-yellow-500', 'border-red-500');
                } else if (nyeriValue === '2') {
                    nyeriDetails.classList.add('border-l-4', 'border-yellow-500', 'pl-2');
                    nyeriDetails.classList.remove('border-blue-500', 'border-red-500');
                } else if (nyeriValue === '3') {
                    nyeriDetails.classList.add('border-l-4', 'border-red-500', 'pl-2');
                    nyeriDetails.classList.remove('border-blue-500', 'border-yellow-500');
                }
            }
        });
        
        // Trigger on page load
        statusNyeriSelect.dispatchEvent(new Event('change'));
        
        // Highlight changes from original values
        const originalData = {
            Suhu: {{ $pemeriksaanAwal->Suhu ?? 'null' }},
            Nadi: {{ $pemeriksaanAwal->Nadi ?? 'null' }},
            Pernapasan: {{ $pemeriksaanAwal->Pernapasan ?? 'null' }},
            // Add other fields as needed
        };
        
        const inputs = document.querySelectorAll('input[type="number"], input[type="text"], textarea, select');
        inputs.forEach(input => {
            const fieldName = input.getAttribute('id');
            
            input.addEventListener('input', function() {
                // Skip if no original data available
                if (originalData[fieldName] === undefined || originalData[fieldName] === null) return;
                
                const currentValue = input.type === 'number' ? parseFloat(input.value) : input.value;
                const originalValue = originalData[fieldName];
                
                if (currentValue !== originalValue && input.value !== '') {
                    input.classList.add('border-indigo-300', 'bg-indigo-50');
                    input.parentElement.classList.add('relative');
                    
                    // Add an indicator for changed field if doesn't exist
                    if (!input.parentElement.querySelector('.change-indicator')) {
                        const indicator = document.createElement('span');
                        indicator.className = 'change-indicator absolute top-0 right-0 -mt-2 -mr-2 h-4 w-4 rounded-full bg-indigo-500';
                        input.parentElement.appendChild(indicator);
                    }
                } else {
                    input.classList.remove('border-indigo-300', 'bg-indigo-50');
                    const indicator = input.parentElement.querySelector('.change-indicator');
                    if (indicator) indicator.remove();
                }
            });
            
            // Add visual feedback on focus
            input.addEventListener('focus', function() {
                this.closest('.relative').classList.add('ring-2', 'ring-indigo-100', 'ring-opacity-50');
            });
            
            input.addEventListener('blur', function() {
                this.closest('.relative').classList.remove('ring-2', 'ring-indigo-100', 'ring-opacity-50');
            });
        });
        
        // Add visual feedback for number inputs
        const suhuInput = document.getElementById('Suhu');
        suhuInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value > 37.5) {
                this.classList.add('border-red-500', 'bg-red-50');
                this.classList.remove('border-gray-300', 'border-blue-500', 'bg-blue-50');
            } else if (value < 35.0) {
                this.classList.add('border-blue-500', 'bg-blue-50');
                this.classList.remove('border-gray-300', 'border-red-500', 'bg-red-50');
            } else {
                this.classList.remove('border-red-500', 'bg-red-50', 'border-blue-500', 'bg-blue-50');
                this.classList.add('border-gray-300');
            }
        });
    });
</script>
@endpush
@endsection