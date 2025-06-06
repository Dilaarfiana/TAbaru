{{-- File: resources/views/petugas/dashboard.blade.php --}}
@extends('layouts.app')

@section('page_title', 'Dashboard Petugas UKS')

@section('content')
<div class="bg-gray-50 min-h-screen -mx-4 -my-6 px-4 py-6">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-user-tie text-yellow-600 mr-3"></i>
                    Dashboard Petugas UKS
                </h1>
                <p class="mt-2 text-gray-600">Selamat datang, {{ session('user_name') ?? session('nama_petugas_uks') ?? 'Petugas' }} - Sistem Informasi Kesehatan Terpadu (SEHATI)</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center text-sm text-gray-600 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                        <i class="fas fa-calendar text-yellow-600 mr-2"></i>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        <span id="currentTime" class="font-medium"></span>
                    </div>
                    <div class="flex items-center text-sm text-yellow-700 bg-yellow-100 px-3 py-2 rounded-lg shadow-sm border border-yellow-200">
                        <i class="fas fa-user-tie mr-2"></i>
                        <span class="font-medium">Akses Petugas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Access Info Banner -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-8 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Informasi Akses Petugas UKS</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    Anda memiliki akses untuk menambah, melihat dan mengedit data kesehatan siswa. Anda tidak dapat menghapus data untuk menjaga integritas rekam medis.
                </p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Siswa Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                        <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalSiswa) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            {{ $totalSiswa > 0 ? round($siswaAktif / $totalSiswa * 100) : 0 }}% aktif
                        </span>
                    </div>
                </div>
                <div class="text-blue-600">
                    <i class="fas fa-arrow-up text-sm"></i>
                </div>
            </div>
        </div>
        
        <!-- Pemeriksaan Hari Ini -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-4">
                        <i class="fas fa-stethoscope text-green-600 text-xl"></i>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">Pemeriksaan Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($pemeriksaanHariIni) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i>Dapat menambah
                        </span>
                    </div>
                </div>
                <div class="text-green-600">
                    <i class="fas fa-arrow-up text-sm"></i>
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
                        <span class="text-yellow-600 text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i>Dapat mengedit
                        </span>
                    </div>
                </div>
                <div class="text-purple-600">
                    <i class="fas fa-arrow-up text-sm"></i>
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
                        <span class="text-red-600 text-sm font-medium">
                            <i class="fas fa-shield-alt mr-1"></i>Data terlindungi
                        </span>
                    </div>
                </div>
                <div class="text-red-600">
                    <i class="fas fa-arrow-up text-sm"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <!-- Quick Actions Card -->
        <div class="lg:col-span-5">
            <div class="bg-yellow-500 rounded-xl shadow-lg p-6 text-white relative overflow-hidden h-full">
                <!-- Background decorations -->
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white opacity-10 transform translate-x-16 -translate-y-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white opacity-10 transform -translate-x-12 translate-y-12"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h2 class="text-xl font-bold">Tindakan Cepat</h2>
                    </div>
                    <p class="mb-6 text-yellow-100">Akses cepat untuk kegiatan UKS sehari-hari</p>
                    
                    <div class="space-y-3">
                        <a href="{{ route('petugas.pemeriksaan_harian.create') }}" class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm hover:bg-opacity-20 transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-plus text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Pemeriksaan Harian</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <a href="{{ route('petugas.resep.create') }}" class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm hover:bg-opacity-20 transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-purple-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-prescription text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Buat Resep Obat</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <a href="{{ route('petugas.rekam_medis.create') }}" class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm hover:bg-opacity-20 transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-file-medical text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Rekam Medis Baru</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        
                        <a href="{{ route('petugas.siswa.index') }}" class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm hover:bg-opacity-20 transition-all duration-200">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Data Siswa</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistik Pemeriksaan -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 h-full">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-chart-bar text-yellow-600 mr-2"></i>
                            Statistik Pemeriksaan
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Grafik perkembangan pemeriksaan bulanan (Akses Petugas)</p>
                    </div>
                    <div class="mt-3 sm:mt-0">
                        <select class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <option>Tahun {{ date('Y') }}</option>
                            <option>Tahun {{ date('Y')-1 }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="h-80 bg-gray-50 rounded-lg p-4">
                    <canvas id="pemeriksaanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Pemeriksaan Terbaru -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
                            Pemeriksaan Terbaru
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Daftar pemeriksaan yang baru dilakukan (dapat mengedit)</p>
                    </div>
                    <a href="{{ route('petugas.pemeriksaan_harian.index') }}" class="inline-flex items-center text-yellow-600 hover:text-yellow-800 font-medium text-sm transition-colors duration-200">
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
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Hasil</th>
                                <th class="text-right px-4 py-3 text-gray-600 text-sm font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemeriksaanTerbaru as $pemeriksaan)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-calendar text-yellow-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pemeriksaan['tanggal'])->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pemeriksaan['tanggal'])->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center text-white font-semibold text-sm mr-3">
                                            {{ substr($pemeriksaan['siswa']->nama_siswa ?? 'XX', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $pemeriksaan['siswa']->nama_siswa ?? 'Tidak diketahui' }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $pemeriksaan['siswa']->id_siswa ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ Str::limit($pemeriksaan['hasil'] ?? 'Tidak ada hasil', 20) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('petugas.pemeriksaan_harian.show', $pemeriksaan['id']) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i>
                                            Detail
                                        </a>
                                        <a href="{{ route('petugas.pemeriksaan_harian.edit', $pemeriksaan['id']) }}" class="inline-flex items-center text-yellow-600 hover:text-yellow-800 font-medium text-sm transition-colors duration-200">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">Tidak ada data pemeriksaan terbaru</p>
                                        <p class="text-gray-400 text-sm mt-1">Mulai tambahkan pemeriksaan baru</p>
                                        <a href="{{ route('petugas.pemeriksaan_harian.create') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                                            <i class="fas fa-plus mr-2"></i>
                                            Tambah Pemeriksaan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Menu Petugas -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-tools text-yellow-600 mr-2"></i>
                            Menu Petugas
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Akses menu berdasarkan hak petugas UKS</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <a href="{{ route('petugas.siswa.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-yellow-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user-graduate text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Data Siswa</p>
                                    <p class="text-xs text-gray-500">Lihat & Edit data siswa</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">RU</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('petugas.pemeriksaan_harian.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-yellow-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-stethoscope text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Pemeriksaan Harian</p>
                                    <p class="text-xs text-gray-500">Kelola pemeriksaan harian</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">CRU</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('petugas.resep.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-yellow-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-prescription-bottle text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Resep Obat</p>
                                    <p class="text-xs text-gray-500">Kelola resep obat siswa</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">CRU</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('petugas.rekam_medis.index') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-yellow-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-file-medical text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Rekam Medis</p>
                                    <p class="text-xs text-gray-500">Kelola rekam medis siswa</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">CRU</span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('petugas.laporan.screening') }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-yellow-300 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-chart-bar text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Laporan Kesehatan</p>
                                    <p class="text-xs text-gray-500">Lihat laporan & statistik</p>
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

        // Pemeriksaan Chart with Petugas theme
        const pemeriksaanCtx = document.getElementById('pemeriksaanChart');
        if (pemeriksaanCtx) {
            const chartData = @json($chartData);
            
            const labels = chartData.map(item => item.bulan);
            const pemeriksaanHarianData = chartData.map(item => item.pemeriksaanHarian);
            const resepData = chartData.map(item => item.resep);
            
            const pemeriksaanChart = new Chart(pemeriksaanCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pemeriksaan Harian',
                            data: pemeriksaanHarianData,
                            borderColor: '#F59E0B',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
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
                            borderColor: '#F59E0B',
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