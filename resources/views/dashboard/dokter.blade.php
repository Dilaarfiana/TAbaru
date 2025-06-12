{{-- File: resources/views/dokter/dashboard.blade.php --}}
@extends('layouts.app')

@section('page_title', 'Dashboard Dokter')

@section('content')
<div class="bg-gray-50 min-h-screen -mx-4 -my-6 px-4 py-6">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-stethoscope text-green-600 mr-3"></i>
                    Dashboard Dokter
                </h1>
                <p class="mt-2 text-gray-600">Selamat datang, {{ session('user_name') ?? session('nama_dokter') ?? 'Dokter' }} - Sistem Informasi Kesehatan Terpadu (SEHATI)</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center text-sm text-gray-600 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                        <i class="fas fa-calendar text-green-600 mr-2"></i>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        <span id="currentTime" class="font-medium"></span>
                    </div>
                    <div class="flex items-center text-sm text-green-700 bg-green-100 px-3 py-2 rounded-lg shadow-sm border border-green-200">
                        <i class="fas fa-user-md mr-2"></i>
                        <span class="font-medium">Akses Dokter</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Access Info Banner -->
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Informasi Akses Dokter</h3>
                <p class="text-sm text-green-700 mt-1">
                    Anda memiliki akses untuk melihat semua data kesehatan siswa dalam mode baca saja. Ini untuk memastikan integritas data medis dan memberikan Anda gambaran menyeluruh tentang kondisi siswa.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Siswa Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                        <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalSiswa) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-blue-600 text-sm font-medium">
                            <i class="fas fa-eye mr-1"></i>Dapat dilihat
                        </span>
                    </div>
                </div>
                <div class="text-blue-600">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
        </div>
        
        <!-- Rekam Medis -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <i class="fas fa-file-medical text-red-600 text-xl"></i>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">Rekam Medis</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalRekamMedis) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-search mr-1"></i>Review medis
                        </span>
                    </div>
                </div>
                <div class="text-red-600">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
        </div>
        
        <!-- Pemeriksaan -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <i class="fas fa-stethoscope text-green-600 text-xl"></i>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">Total Pemeriksaan</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalPemeriksaan) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-clipboard-check mr-1"></i>Monitoring
                        </span>
                    </div>
                </div>
                <div class="text-green-600">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
        </div>
        
        <!-- Resep Obat -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mb-4">
                        <i class="fas fa-prescription-bottle text-purple-600 text-xl"></i>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">Resep Obat</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalResep) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-purple-600 text-sm font-medium">
                            <i class="fas fa-pills mr-1"></i>Monitoring obat
                        </span>
                    </div>
                </div>
                <div class="text-purple-600">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <!-- Medical Overview Card -->
        <div class="lg:col-span-5">
            <div class="bg-green-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden h-full">
                <!-- Background decorations -->
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white opacity-10 transform translate-x-16 -translate-y-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white opacity-10 transform -translate-x-12 translate-y-12"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h2 class="text-xl font-bold">Overview Medis</h2>
                    </div>
                    <p class="mb-6 text-green-100">Ringkasan kondisi kesehatan siswa untuk review dokter</p>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Siswa Aktif</span>
                            </div>
                            <span class="text-xl font-bold">{{ number_format($siswaAktif) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-exclamation-triangle text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Perlu Monitoring</span>
                            </div>
                            <span class="text-xl font-bold">{{ number_format($siswaTidakAktif) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-heartbeat text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Pemeriksaan Hari Ini</span>
                            </div>
                            <span class="text-xl font-bold">{{ number_format($pemeriksaanHariIni) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-purple-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-prescription text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Resep Aktif</span>
                            </div>
                            <span class="text-xl font-bold">{{ number_format($resepAktif ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistik Medis -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 h-full">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-chart-line text-green-600 mr-2"></i>
                            Tren Kesehatan Siswa
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Grafik monitoring kesehatan untuk evaluasi medis</p>
                    </div>
                    <div class="mt-3 sm:mt-0">
                        <select class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option>Tahun {{ date('Y') }}</option>
                            <option>Tahun {{ date('Y')-1 }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="h-80 bg-gray-50 rounded-lg p-4">
                    <canvas id="medicalChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Rekam Medis Terbaru -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-file-medical-alt text-red-600 mr-2"></i>
                            Rekam Medis Terbaru
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Data medis terbaru untuk review dan monitoring</p>
                    </div>
                    <a href="{{ route('dokter.rekam_medis.index') }}" class="inline-flex items-center text-green-600 hover:text-green-800 font-medium text-sm transition-colors duration-200">
                        Lihat Semua
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Tanggal</th>
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Nama Siswa</th>
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Keluhan</th>
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Dokter</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekamMedisTerbaru as $rekam)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-calendar text-green-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($rekam->Tanggal_Jam)->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($rekam->Tanggal_Jam)->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold text-sm mr-3">
                                            {{ substr($rekam->siswa->nama_siswa ?? 'XX', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $rekam->siswa->nama_siswa ?? 'Tidak diketahui' }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $rekam->Id_Siswa }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-gray-900">{{ Str::limit($rekam->Keluhan_Utama, 30) }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                            <i class="fas fa-user-md text-blue-600 text-xs"></i>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $rekam->dokter->Nama_Dokter ?? 'Tidak diketahui' }}</span>
                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-file-medical text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Tidak ada data rekam medis terbaru</p>
                                        <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah ada rekam medis baru</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Menu Dokter -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-stethoscope text-green-600 mr-2"></i>
                            Menu Dokter
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Akses read-only untuk review medis</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('dokter.siswa.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-green-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-graduate text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Data Siswa</p>
                                    <p class="text-xs text-gray-500">Review profil siswa</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">R</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('dokter.rekam_medis.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-green-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-file-medical text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Rekam Medis</p>
                                    <p class="text-xs text-gray-500">Review riwayat medis</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">R</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('dokter.resep.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-green-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-prescription-bottle text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Resep Obat</p>
                                    <p class="text-xs text-gray-500">Monitor pengobatan</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">R</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('dokter.pemeriksaan_awal.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-green-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-stethoscope text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Pemeriksaan</p>
                                    <p class="text-xs text-gray-500">Review hasil pemeriksaan</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">R</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('dokter.laporan.screening') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-green-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-chart-bar text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Laporan Medis</p>
                                    <p class="text-xs text-gray-500">Analisis kesehatan</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">R</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update Current Time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }
        
        updateTime();
        setInterval(updateTime, 1000);

        // Medical Chart with Dokter theme
        const medicalCtx = document.getElementById('medicalChart');
        if (medicalCtx) {
            const chartData = @json($chartData);
            
            const labels = chartData.map(item => item.bulan);
            const rekamMedisData = chartData.map(item => item.rekamMedis);
            const resepData = chartData.map(item => item.resep);
            
            const medicalChart = new Chart(medicalCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Rekam Medis',
                            data: rekamMedisData,
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBorderWidth: 2,
                            pointBorderColor: '#ffffff'
                        },
                        {
                            label: 'Resep Obat',
                            data: resepData,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBorderWidth: 2,
                            pointBorderColor: '#ffffff'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#1F2937',
                            bodyColor: '#374151',
                            borderColor: '#10B981',
                            borderWidth: 2,
                            padding: 12,
                            displayColors: true,
                            caretPadding: 8,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                color: '#6B7280'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [3, 3],
                                color: '#F3F4F6'
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '500'
                                },
                                color: '#6B7280',
                                precision: 0,
                                stepSize: 5
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush