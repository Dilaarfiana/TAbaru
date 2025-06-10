@extends('layouts.app')

@section('content')
<!-- Main Content Container dengan padding yang tepat -->
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200 gap-4">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-chalkboard mr-3 text-blue-500"></i>
                    Detail Kelas
                </h1>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                    <a href="{{ route('kelas.edit', $kelas->Kode_Kelas) }}" 
                       class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg shadow-sm transition duration-300 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <a href="{{ route('kelas.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg shadow-sm transition duration-300 flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Info Banner -->
            <div class="p-6">
                <div class="bg-blue-50 rounded-lg p-4 flex items-start">
                    <div class="bg-blue-100 rounded-full p-3 mr-4 flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-blue-800">Informasi Kelas</h3>
                        <p class="text-blue-600 text-sm mt-1">
                            Berikut adalah detail lengkap dari kelas {{ $kelas->Nama_Kelas }} beserta daftar siswa yang terdaftar.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Left Column - Detail Kelas -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden h-fit">
                    <div class="border-b border-gray-200 p-4 bg-gray-50">
                        <h3 class="font-medium text-gray-700 flex items-center">
                            <i class="fas fa-school mr-2 text-gray-500"></i>
                            Detail Kelas
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        
                        <!-- Kode Kelas -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Kode Kelas</span>
                            <span class="px-3 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $kelas->Kode_Kelas }}
                            </span>
                        </div>
                        
                        <!-- Nama Kelas -->
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nama Kelas</span>
                            <div class="text-lg font-medium text-gray-900 mt-1">{{ $kelas->Nama_Kelas }}</div>
                        </div>
                        
                        <!-- Tahun Ajaran -->
                        <div>
                            <span class="text-sm font-medium text-gray-500">Tahun Ajaran</span>
                            <div class="text-lg font-medium text-gray-900 mt-1">{{ $kelas->Tahun_Ajaran ?? '-' }}</div>
                        </div>
                        
                        <!-- Jurusan -->
                        <div>
                            <span class="text-sm font-medium text-gray-500">Jurusan</span>
                            <div class="mt-1">
                                @if($kelas->jurusan)
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $kelas->jurusan->Kode_Jurusan }} - {{ $kelas->jurusan->Nama_Jurusan }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Jumlah Siswa -->
                        <div>
                            <span class="text-sm font-medium text-gray-500">Jumlah Siswa</span>
                            <div class="text-xl font-bold text-gray-900 mt-1 flex items-center">
                                <i class="fas fa-users mr-2 text-blue-500"></i>
                                {{ $kelas->jumlah_siswa ?? $kelas->Jumlah_Siswa ?? '0' }} 
                                <span class="text-sm font-normal text-gray-500 ml-1">siswa</span>
                            </div>
                        </div>
                        
                        <!-- Delete Button -->
                        <div class="pt-4 border-t border-gray-200">
                            <form action="{{ route('kelas.destroy', $kelas->Kode_Kelas) }}" method="POST" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full flex justify-center items-center text-red-600 hover:bg-red-50 border border-red-200 rounded-md px-4 py-2 transition duration-200" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Kelas
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Daftar Siswa -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="font-medium text-gray-700 flex items-center">
                            <i class="fas fa-user-graduate mr-2 text-gray-500"></i>
                            Daftar Siswa
                        </h3>
                    </div>
                    
                    @if(isset($siswa) && $siswa->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-id-card mr-1"></i>
                                            ID Siswa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-user mr-1"></i>
                                            Nama Siswa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-venus-mars mr-1"></i>
                                            Jenis Kelamin
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fas fa-cogs mr-1"></i>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($siswa as $detail)
                                        @if($detail->siswa)
                                            <tr class="hover:bg-gray-50 transition duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $detail->siswa->id_siswa }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                            <i class="fas fa-user text-gray-500 text-xs"></i>
                                                        </div>
                                                        {{ $detail->siswa->nama_siswa }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($detail->siswa->jenis_kelamin == 'L')
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            <i class="fas fa-mars mr-1"></i>
                                                            Laki-laki
                                                        </span>
                                                    @elseif($detail->siswa->jenis_kelamin == 'P')
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                                            <i class="fas fa-venus mr-1"></i>
                                                            Perempuan
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex justify-end space-x-2">
                                                        <a href="{{ route('siswa.show', $detail->siswa->id_siswa) }}" 
                                                           class="text-blue-600 hover:text-blue-900 p-1 rounded">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form action="{{ route('kelas.remove-siswa', [$kelas->Kode_Kelas, $detail->id_detsiswa]) }}" 
                                                              method="POST" class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="text-red-600 hover:text-red-900 p-1 rounded" 
                                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini dari kelas?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($siswa->hasPages())
                            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                                {{ $siswa->links() }}
                            </div>
                        @endif
                    @else
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-500 mb-4">
                                <i class="fas fa-user-graduate text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada siswa</h3>
                            <p class="text-gray-500 mb-6">Belum ada siswa yang terdaftar di kelas ini.</p>
                            <a href="#" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Siswa
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Statistics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Ringkasan Kelas -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                        Ringkasan Kelas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        
                        <!-- Status Kelas -->
                        <div class="flex items-center justify-between bg-green-50 p-4 rounded-lg">
                            <div>
                                <span class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Status Kelas
                                </span>
                                <p class="text-2xl font-bold text-gray-800">Aktif</p>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            </div>
                        </div>
                        
                        <!-- Total Siswa -->
                        <div class="flex items-center justify-between bg-blue-50 p-4 rounded-lg">
                            <div>
                                <span class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-users mr-1"></i>
                                    Total Siswa
                                </span>
                                <p class="text-2xl font-bold text-gray-800">{{ $kelas->jumlah_siswa ?? $kelas->Jumlah_Siswa ?? 0 }}</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-3">
                                <i class="fas fa-user-graduate text-blue-500 text-xl"></i>
                            </div>
                        </div>
                        
                        <!-- Jurusan -->
                        <div class="flex items-center justify-between bg-purple-50 p-4 rounded-lg">
                            <div class="flex-1">
                                <span class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Jurusan
                                </span>
                                <p class="text-2xl font-bold text-gray-800 truncate">
                                    {{ $kelas->jurusan ? $kelas->jurusan->Nama_Jurusan : '-' }}
                                </p>
                            </div>
                            <div class="bg-purple-100 rounded-full p-3 ml-4">
                                <i class="fas fa-graduation-cap text-purple-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Distribusi Jenis Kelamin -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                        Distribusi Jenis Kelamin
                    </h2>
                </div>
                <div class="p-6">
                    @if((isset($jumlahLakiLaki) && $jumlahLakiLaki > 0) || (isset($jumlahPerempuan) && $jumlahPerempuan > 0))
                        <div class="relative mb-6">
                            <canvas id="genderChart" width="400" height="300"></canvas>
                        </div>
                        
                        <!-- Statistics Summary -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $jumlahLakiLaki ?? 0 }}</div>
                                <div class="text-sm text-blue-800 font-medium flex items-center justify-center">
                                    <i class="fas fa-mars mr-1"></i>
                                    Laki-laki
                                </div>
                                <div class="text-xs text-blue-600">
                                    {{ ($jumlahLakiLaki ?? 0) + ($jumlahPerempuan ?? 0) > 0 ? round((($jumlahLakiLaki ?? 0) / (($jumlahLakiLaki ?? 0) + ($jumlahPerempuan ?? 0))) * 100, 1) : 0 }}%
                                </div>
                            </div>
                            <div class="text-center p-4 bg-pink-50 rounded-lg">
                                <div class="text-2xl font-bold text-pink-600">{{ $jumlahPerempuan ?? 0 }}</div>
                                <div class="text-sm text-pink-800 font-medium flex items-center justify-center">
                                    <i class="fas fa-venus mr-1"></i>
                                    Perempuan
                                </div>
                                <div class="text-xs text-pink-600">
                                    {{ ($jumlahLakiLaki ?? 0) + ($jumlahPerempuan ?? 0) > 0 ? round((($jumlahPerempuan ?? 0) / (($jumlahLakiLaki ?? 0) + ($jumlahPerempuan ?? 0))) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                <i class="fas fa-chart-pie text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data</h3>
                            <p class="text-gray-400 mb-4">Belum ada data siswa untuk ditampilkan dalam chart.</p>
                            <div class="text-sm text-gray-500 bg-gray-50 p-3 rounded-lg">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tambahkan siswa ke kelas untuk melihat distribusi jenis kelamin
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Pastikan konten tidak tertutup navbar */
    body {
        padding-top: 0;
    }
    
    /* Custom scrollbar */
    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Animation untuk hover effect */
    .transition-all {
        transition: all 0.3s ease;
    }
    
    /* Canvas container untuk chart */
    canvas {
        max-height: 300px !important;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

@if((isset($jumlahLakiLaki) && $jumlahLakiLaki > 0) || (isset($jumlahPerempuan) && $jumlahPerempuan > 0))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('genderChart');
    
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }
    
    // Data dari controller
    const maleCount = {{ $jumlahLakiLaki ?? 0 }};
    const femaleCount = {{ $jumlahPerempuan ?? 0 }};
    const total = maleCount + femaleCount;
    
    // Pastikan ada data untuk ditampilkan
    if (total === 0) {
        console.warn('No data to display in chart');
        return;
    }
    
    // Destroy existing chart if exists
    if (window.genderChartInstance) {
        window.genderChartInstance.destroy();
    }
    
    // Create new chart
    window.genderChartInstance = new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                data: [maleCount, femaleCount],
                backgroundColor: [
                    '#3B82F6', // blue-500
                    '#EC4899', // pink-500
                ],
                borderColor: [
                    '#FFFFFF', // white border
                    '#FFFFFF', // white border
                ],
                borderWidth: 3,
                hoverBackgroundColor: [
                    '#2563EB', // blue-600
                    '#DB2777', // pink-600
                ],
                hoverBorderColor: [
                    '#FFFFFF', // white border
                    '#FFFFFF', // white border
                ],
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 13,
                            weight: '600'
                        },
                        color: '#374151'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#374151',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} siswa (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1500,
                easing: 'easeOutQuart'
            },
            cutout: '65%',
            radius: '90%',
            elements: {
                arc: {
                    borderWidth: 3
                }
            },
            interaction: {
                intersect: false,
                mode: 'point'
            }
        }
    });
    
    console.log('Gender chart created successfully', {
        maleCount: maleCount,
        femaleCount: femaleCount,
        total: total
    });
});
</script>
@endif
@endpush