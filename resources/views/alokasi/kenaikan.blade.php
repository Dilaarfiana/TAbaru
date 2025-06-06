@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-level-up-alt text-green-500 mr-2"></i> Kenaikan Kelas
        </h5>
        <a href="{{ route('alokasi.allocated') }}" class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
    
    <!-- Tampilkan pesan sukses -->
    @if(session('success'))
    <div id="notification" class="bg-green-50 border-l-4 border-green-500 p-4 mx-4 mt-3 flex items-center justify-between">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    {{ session('success') }}
                </p>
            </div>
        </div>
        <button type="button" id="closeNotification" class="text-green-500 hover:text-green-600" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
    
    <!-- Tampilkan pesan error -->
    @if(session('error'))
    <div id="errorNotification" class="bg-red-50 border-l-4 border-red-500 p-4 mx-4 mt-3 flex items-center justify-between">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    {{ session('error') }}
                </p>
            </div>
        </div>
        <button type="button" id="closeErrorNotification" class="text-red-500 hover:text-red-600" onclick="this.parentElement.style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
    
    <!-- Form Kenaikan Kelas -->
    <div class="p-6">
        <div class="mx-auto max-w-3xl bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h6 class="font-medium text-gray-700 mb-2">Instruksi</h6>
                <p class="text-gray-600 text-sm mb-2">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    Proses kenaikan kelas akan memindahkan seluruh siswa dari kelas asal ke kelas tujuan.
                </p>
                <p class="text-gray-600 text-sm">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                    Jika jurusan kelas tujuan berbeda dengan kelas asal, ID siswa akan diubah sesuai kode jurusan yang baru.
                </p>
            </div>
            
            <form action="{{ route('alokasi.proses-kenaikan') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pilih Kelas Asal -->
                    <div>
                        <label for="kelas_asal" class="block text-sm font-medium text-gray-700 mb-1">Kelas Asal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-chalkboard text-gray-400"></i>
                            </div>
                            <select name="kelas_asal" id="kelas_asal" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Kelas Asal</option>
                                @foreach($kelass as $kelas)
                                <option value="{{ $kelas->Kode_Kelas }}">{{ $kelas->Nama_Kelas }} - {{ $kelas->jurusan_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('kelas_asal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Pilih Kelas Tujuan -->
                    <div>
                        <label for="kelas_tujuan" class="block text-sm font-medium text-gray-700 mb-1">Kelas Tujuan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-chalkboard-teacher text-gray-400"></i>
                            </div>
                            <select name="kelas_tujuan" id="kelas_tujuan" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Kelas Tujuan</option>
                                @foreach($kelass as $kelas)
                                <option value="{{ $kelas->Kode_Kelas }}">{{ $kelas->Nama_Kelas }} - {{ $kelas->jurusan_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('kelas_tujuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Tahun Ajaran -->
                <div class="mt-4">
                    <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar text-gray-400"></i>
                        </div>
                        <select name="tahun_ajaran" id="tahun_ajaran" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjaran as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('tahun_ajaran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Update Tahun Ajaran Checkbox -->
                <div class="mt-4">
                    <div class="flex items-center">
                        <input id="update_tahun_ajaran" name="update_tahun_ajaran" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="1">
                        <label for="update_tahun_ajaran" class="ml-2 block text-sm text-gray-700">
                            Update tahun ajaran kelas tujuan
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Jika dipilih, tahun ajaran kelas tujuan akan diperbarui sesuai dengan tahun ajaran yang dipilih di atas.</p>
                </div>
                
                <!-- Konfirmasi dan Tombol Submit -->
                <div class="mt-8">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Pastikan Anda sudah memilih kelas yang tepat. Proses ini akan memindahkan semua siswa.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('alokasi.allocated') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-200">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition-colors duration-200">
                            <i class="fas fa-check mr-1"></i> Proses Kenaikan Kelas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Notification auto-close
        const notification = document.getElementById('notification');
        const closeNotification = document.getElementById('closeNotification');
        
        if (notification) {
            // Auto close after 5 seconds
            setTimeout(function() {
                notification.style.opacity = '0';
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 500);
            }, 5000);
            
            // Manual close button
            if (closeNotification) {
                closeNotification.addEventListener('click', function() {
                    notification.style.opacity = '0';
                    setTimeout(function() {
                        notification.style.display = 'none';
                    }, 500);
                });
            }
            
            // Add transition
            notification.style.transition = 'opacity 0.5s ease-in-out';
        }
        
        const errorNotification = document.getElementById('errorNotification');
        const closeErrorNotification = document.getElementById('closeErrorNotification');
        
        if (errorNotification) {
            // Auto close after 5 seconds
            setTimeout(function() {
                errorNotification.style.opacity = '0';
                setTimeout(function() {
                    errorNotification.style.display = 'none';
                }, 500);
            }, 5000);
            
            // Manual close button
            if (closeErrorNotification) {
                closeErrorNotification.addEventListener('click', function() {
                    errorNotification.style.opacity = '0';
                    setTimeout(function() {
                        errorNotification.style.display = 'none';
                    }, 500);
                });
            }
            
            // Add transition
            errorNotification.style.transition = 'opacity 0.5s ease-in-out';
        }
    });
</script>
@endpush