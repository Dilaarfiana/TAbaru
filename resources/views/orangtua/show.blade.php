@extends('layouts.app')

@section('page_title', 'Detail Data Orang Tua')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-7xl mx-auto bg-white rounded-md shadow-md">
        <!-- Header -->
        <div class="bg-white rounded-t-md px-6 py-4 flex justify-between items-center border-b">
            <div class="flex items-center">
                <i class="fas fa-users text-indigo-500 mr-3 text-xl"></i>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Data Orang Tua</h2>
                    <div class="flex items-center mt-1">
                        <span class="text-sm text-gray-600 mr-2">ID:</span>
                        <span class="bg-indigo-100 text-indigo-800 text-sm font-bold py-1 px-3 rounded-full">
                            {{ $orangTua->id_orang_tua }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('orangtua.edit', $orangTua->id_orang_tua) }}" 
                   class="bg-orange-500 text-white hover:bg-orange-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit Data
                </a>
                
                <a href="{{ route('orangtua.index') }}" 
                   class="bg-gray-500 text-white hover:bg-gray-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
            <!-- Alert Messages -->
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 flex items-center justify-between">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="close-alert text-green-500 hover:text-green-600" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
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

            <!-- Info Banner -->
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-indigo-500"></i>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-md font-medium text-indigo-800 mb-1">Informasi Orang Tua</h3>
                        <p class="text-sm text-indigo-700 mb-2">
                            Menampilkan detail lengkap data orang tua siswa termasuk informasi kontak dan data pribadi.
                        </p>
                        
                        <!-- Metadata Info -->
                        <div class="mt-2 p-2 bg-indigo-100 border border-indigo-300 rounded text-xs">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <div>
                                    <span class="font-medium text-indigo-800">Data Ayah:</span>
                                    <span class="text-indigo-700">
                                        {{ $orangTua->nama_ayah ? 'Lengkap' : 'Belum Lengkap' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-indigo-800">Data Ibu:</span>
                                    <span class="text-indigo-700">
                                        {{ $orangTua->nama_ibu ? 'Lengkap' : 'Belum Lengkap' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-indigo-800">Kontak:</span>
                                    <span class="text-indigo-700">
                                        {{ ($orangTua->alamat && $orangTua->no_telp) ? 'Lengkap' : 'Belum Lengkap' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-indigo-800">Siswa:</span>
                                    <span class="text-indigo-700">
                                        {{ $orangTua->siswa ? 'Terhubung' : 'Tidak Terhubung' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Informasi Utama -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <!-- Data Siswa -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 shadow-sm lg:col-span-3">
                    <div class="flex items-center mb-4 border-b border-blue-200 pb-2">
                        <i class="fas fa-user-graduate text-blue-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Informasi Siswa</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-id-card text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">ID Siswa</p>
                                    <p class="font-mono font-bold text-gray-800 cursor-pointer" onclick="copyToClipboard('{{ $orangTua->siswa->id_siswa ?? '-' }}')" title="Klik untuk menyalin">
                                        {{ $orangTua->siswa->id_siswa ?? '-' }}
                                        <i class="fas fa-copy text-gray-400 text-xs ml-1"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-user text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Nama Siswa</p>
                                    <p class="font-medium text-gray-800">{{ $orangTua->siswa->nama_siswa ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-calendar text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Lahir</p>
                                    <p class="font-medium text-gray-800">
                                        @if($orangTua->siswa && $orangTua->siswa->tanggal_lahir)
                                            {{ \Carbon\Carbon::parse($orangTua->siswa->tanggal_lahir)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-venus-mars text-blue-600 mr-2 w-4 mt-1"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Jenis Kelamin</p>
                                    <p class="font-medium text-gray-800">
                                        @if($orangTua->siswa && $orangTua->siswa->jenis_kelamin == 'L')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                                <i class="fas fa-male mr-1"></i>Laki-laki
                                            </span>
                                        @elseif($orangTua->siswa && $orangTua->siswa->jenis_kelamin == 'P')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-pink-100 text-pink-800">
                                                <i class="fas fa-female mr-1"></i>Perempuan
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="bg-green-50 border border-green-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-green-200 pb-2">
                        <i class="fas fa-chart-pie text-green-500 mr-2 text-lg"></i>
                        <h3 class="text-lg font-medium text-gray-800">Ringkasan</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <i class="fas fa-{{ $orangTua->nama_ayah ? 'check-circle' : 'times-circle' }} text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Data Ayah</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->nama_ayah ? 'Lengkap' : 'Belum Lengkap' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-{{ $orangTua->nama_ibu ? 'check-circle' : 'times-circle' }} text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Data Ibu</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->nama_ibu ? 'Lengkap' : 'Belum Lengkap' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-{{ ($orangTua->alamat && $orangTua->no_telp) ? 'check-circle' : 'times-circle' }} text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Kontak</p>
                                <p class="font-medium text-gray-800">{{ ($orangTua->alamat && $orangTua->no_telp) ? 'Lengkap' : 'Belum Lengkap' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-link text-green-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Status Siswa</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->siswa ? 'Terhubung' : 'Tidak Terhubung' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Data Ayah -->
                <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-male text-blue-600 mr-2 text-lg"></i>
                        <h3 class="text-xl font-semibold text-gray-800">Data Ayah</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-user text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Nama Lengkap</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->nama_ayah ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-birthday-cake text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal Lahir</p>
                                <p class="font-medium text-gray-800">
                                    @if($orangTua->tanggal_lahir_ayah)
                                        {{ \Carbon\Carbon::parse($orangTua->tanggal_lahir_ayah)->format('d M Y') }}
                                        <span class="text-xs text-gray-500 block">
                                            ({{ \Carbon\Carbon::parse($orangTua->tanggal_lahir_ayah)->age }} tahun)
                                        </span>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-briefcase text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Pekerjaan</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->pekerjaan_ayah ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-graduation-cap text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Pendidikan</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->pendidikan_ayah ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Ibu -->
                <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                        <i class="fas fa-female text-pink-600 mr-2 text-lg"></i>
                        <h3 class="text-xl font-semibold text-gray-800">Data Ibu</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-user text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Nama Lengkap</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->nama_ibu ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-birthday-cake text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal Lahir</p>
                                <p class="font-medium text-gray-800">
                                    @if($orangTua->tanggal_lahir_ibu)
                                        {{ \Carbon\Carbon::parse($orangTua->tanggal_lahir_ibu)->format('d M Y') }}
                                        <span class="text-xs text-gray-500 block">
                                            ({{ \Carbon\Carbon::parse($orangTua->tanggal_lahir_ibu)->age }} tahun)
                                        </span>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-briefcase text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Pekerjaan</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->pekerjaan_ibu ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-graduation-cap text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Pendidikan</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->pendidikan_ibu ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Kontak -->
            <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm mb-6">
                <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-address-book text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Informasi Kontak</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 shadow-sm md:col-span-2">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">Alamat</p>
                                <p class="font-medium text-gray-800">{{ $orangTua->alamat ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start">
                            <i class="fas fa-phone text-gray-600 mr-2 w-4 mt-1"></i>
                            <div>
                                <p class="text-xs text-gray-500">No. Telepon</p>
                                <p class="font-mono font-medium text-gray-800">{{ $orangTua->no_telp ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-gray-50 border border-gray-100 rounded-lg p-5 shadow-sm mb-6">
                <div class="flex items-center mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-cog text-gray-600 mr-2 text-lg"></i>
                    <h3 class="text-xl font-semibold text-gray-800">Informasi Sistem</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-start">
                        <i class="fas fa-calendar-plus text-gray-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Dibuat</p>
                            <p class="font-medium text-gray-800">
                                {{ $orangTua->created_at ? $orangTua->created_at->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-calendar-check text-gray-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
                            <p class="font-medium text-gray-800">
                                {{ $orangTua->updated_at ? $orangTua->updated_at->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-history text-gray-600 mr-2 w-4 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Relatif</p>
                            <p class="font-medium text-gray-800">
                                {{ $orangTua->updated_at ? $orangTua->updated_at->diffForHumans() : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('orangtua.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-gray-500"></i>
                    Kembali ke Daftar
                </a>
                
                <div class="flex space-x-2">
                    <a href="{{ route('orangtua.edit', $orangTua->id_orang_tua) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Data
                    </a>
                    
                    <form action="{{ route('orangtua.destroy', $orangTua->id_orang_tua) }}" method="POST" class="inline-block" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('ID Siswa berhasil disalin!', 'success');
        }).catch(() => {
            showToast('Gagal menyalin ID Siswa', 'error');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToast('ID Siswa berhasil disalin!', 'success');
        } catch (err) {
            showToast('Gagal menyalin ID Siswa', 'error');
        } finally {
            textArea.remove();
        }
    }
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    toast.className = `${bgColor} text-white px-4 py-2 rounded-lg shadow-lg`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : 'times'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Simple toast positioning
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.close-alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentElement) {
                alert.parentElement.style.display = 'none';
            }
        }, 5000);
    });
    
    // Log page load for debugging
    console.log('Parent Detail Page Loaded');
    console.log('Parent ID:', '{{ $orangTua->id_orang_tua }}');
    console.log('Student ID:', '{{ $orangTua->siswa->id_siswa ?? 'N/A' }}');
});

function confirmDelete() {
    const parentId = '{{ $orangTua->id_orang_tua }}';
    const studentName = '{{ $orangTua->siswa->nama_siswa ?? 'N/A' }}';
    
    if (confirm(`PERINGATAN!\n\nApakah Anda yakin ingin menghapus data orang tua ini?\n\nID: ${parentId}\nSiswa: ${studentName}\n\nTindakan ini akan menghapus:\n- Data orang tua lengkap\n- Semua data terkait\n\nData yang dihapus TIDAK DAPAT dikembalikan!\n\nKlik OK untuk melanjutkan atau Cancel untuk membatalkan.`)) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
@endsection