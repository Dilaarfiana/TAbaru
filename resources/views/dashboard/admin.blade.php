@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen -mx-4 -my-6 px-4 py-6">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                    Dashboard
                </h1>
                <p class="mt-2 text-gray-600">Selamat datang di Sistem Informasi Kesehatan Terpadu (SEHATI)</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center text-sm text-gray-600 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                        <i class="fas fa-calendar text-blue-600 mr-2"></i>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        <span id="currentTime" class="font-medium"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Siswa Card -->
        <x-stat-card 
            icon="fa-user-graduate" 
            :value="number_format($totalSiswa)"
            label="Total Siswa"
            color="blue"
            :percentage="$totalSiswa > 0 ? round($siswaAktif / $totalSiswa * 100) : 0"
            trend="up"
        />
        
        <!-- Dokter Card -->
        <x-stat-card 
            icon="fa-user-md" 
            :value="number_format($totalDokter)"
            label="Dokter"
            color="red"
            percentage="100"
            trend="up"
        />
        
        <!-- Orang Tua Card -->
        <x-stat-card 
            icon="fa-users" 
            :value="number_format($totalOrangTua)"
            label="Orang Tua"
            color="yellow"
            percentage="85"
            trend="up"
        />
        
        <!-- Pemeriksaan Card -->
        <x-stat-card 
            icon="fa-stethoscope" 
            :value="number_format($totalPemeriksaan)"
            label="Total Pemeriksaan"
            color="green"
            percentage="92"
            trend="up"
        />
    </div>
    
    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <!-- Status Kesehatan Card -->
        <div class="lg:col-span-5">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl shadow-lg p-6 text-white relative overflow-hidden h-full">
                <!-- Background decorations -->
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white opacity-10 transform translate-x-16 -translate-y-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white opacity-10 transform -translate-x-12 translate-y-12"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h2 class="text-xl font-bold">Status Kesehatan</h2>
                    </div>
                    <p class="mb-6 text-indigo-100">Ringkasan status kesehatan siswa saat ini</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Siswa Sehat</span>
                            </div>
                            <span class="text-xl font-bold">{{ number_format($siswaAktif) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-exclamation text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Perlu Perhatian</span>
                            </div>
                            <span class="text-xl font-bold">{{ number_format($siswaTidakAktif) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg backdrop-blur-sm">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-400 flex items-center justify-center mr-3">
                                    <i class="fas fa-file-medical text-sm text-white"></i>
                                </div>
                                <span class="font-medium">Rekam Medis</span>
                            </div>
                            <span class="text-xl font-bold">{{ number_format($totalRekamMedis) }}</span>
                        </div>
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
                            <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                            Statistik Pemeriksaan
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Grafik perkembangan pemeriksaan bulanan</p>
                    </div>
                    <div class="mt-3 sm:mt-0">
                        <select class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option>Tahun {{ date('Y') }}</option>
                            <option>Tahun {{ date('Y')-1 }}</option>
                            <option>Tahun {{ date('Y')-2 }}</option>
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
                        <p class="text-gray-600 text-sm mt-1">Daftar pemeriksaan yang baru dilakukan</p>
                    </div>
                    @if(session('user_level') === 'admin')
                        <a href="{{ route('detail_pemeriksaan.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @elseif(session('user_level') === 'petugas')
                        <a href="{{ route('petugas.detail_pemeriksaan.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @elseif(session('user_level') === 'dokter')
                        <a href="{{ route('dokter.detail_pemeriksaan.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @else
                        <a href="{{ route('orangtua.riwayat.pemeriksaan_harian') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Tanggal</th>
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Nama Siswa</th>
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Dokter</th>
                                <th class="text-left px-4 py-3 text-gray-600 text-sm font-semibold">Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemeriksaanTerbaru as $pemeriksaan)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-calendar text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pemeriksaan['tanggal'])->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($pemeriksaan['tanggal'])->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-semibold text-sm mr-3">
                                            {{ substr($pemeriksaan['siswa']->nama_siswa ?? 'XX', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $pemeriksaan['siswa']->nama_siswa ?? 'Tidak diketahui' }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $pemeriksaan['siswa']->id_siswa ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-2">
                                            <i class="fas fa-user-md text-green-600 text-xs"></i>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $pemeriksaan['dokter']->Nama_Dokter ?? 'Tidak diketahui' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full 
                                        @if($pemeriksaan['jenis'] === 'Harian') bg-blue-100 text-blue-800
                                        @elseif($pemeriksaan['jenis'] === 'Detail') bg-green-100 text-green-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ \Illuminate\Support\Str::limit($pemeriksaan['hasil'] ?? 'Tidak ada hasil', 20) }}
                                    </span>
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
                                        <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah ada pemeriksaan baru</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Resep Terbaru -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-prescription-bottle-alt text-orange-600 mr-2"></i>
                            Resep Terbaru
                        </h2>
                        <p class="text-gray-600 text-sm mt-1">Resep obat yang baru diterbitkan</p>
                    </div>
                    @if(session('user_level') === 'admin')
                        <a href="{{ route('resep.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @elseif(session('user_level') === 'petugas')
                        <a href="{{ route('petugas.resep.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @elseif(session('user_level') === 'dokter')
                        <a href="{{ route('dokter.resep.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @else
                        <a href="{{ route('orangtua.riwayat.resep') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>
                
                <div class="space-y-4">
                    @forelse($resepTerbaru as $resep)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-gray-300 transition-all duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-400 to-red-400 flex items-center justify-center text-white font-semibold text-sm mr-3">
                                    {{ substr($resep->siswa->nama_siswa ?? 'XX', 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $resep->siswa->nama_siswa ?? 'Tidak diketahui' }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-pills text-orange-600 mr-2"></i>
                                <span class="font-medium text-gray-900">{{ $resep->Nama_Obat ?? 'Tidak ada obat' }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-1 text-gray-400"></i>
                                    <span>{{ $resep->Dosis ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-1 text-gray-400"></i>
                                    <span>{{ $resep->Durasi ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-user-md mr-1"></i>
                                <span>{{ $resep->dokter->Nama_Dokter ?? 'Tidak diketahui' }}</span>
                            </div>
                            @if(session('user_level') === 'admin')
                                <a href="{{ route('resep.show', $resep->Id_Resep) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                            @elseif(session('user_level') === 'petugas')
                                <a href="{{ route('petugas.resep.show', $resep->Id_Resep) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                            @elseif(session('user_level') === 'dokter')
                                <a href="{{ route('dokter.resep.show', $resep->Id_Resep) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                            @else
                                <a href="{{ route('orangtua.riwayat.resep') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    Detail
                                </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4 mx-auto">
                                <i class="fas fa-prescription-bottle-alt text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Tidak ada data resep terbaru</p>
                            <p class="text-gray-400 text-sm mt-1">Data akan muncul setelah ada resep baru</p>
                        </div>
                    </div>
                    @endforelse
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
        
        // Update time immediately and then every second
        updateTime();
        setInterval(updateTime, 1000);

        // Pemeriksaan Chart
        const pemeriksaanCtx = document.getElementById('pemeriksaanChart');
        if (pemeriksaanCtx) {
            // Data dari controller
            const chartData = @json($chartData);
            
            const labels = chartData.map(item => item.bulan);
            const pemeriksaanAwalData = chartData.map(item => item.pemeriksaanAwal);
            const pemeriksaanFisikData = chartData.map(item => item.pemeriksaanFisik);
            const pemeriksaanHarianData = chartData.map(item => item.pemeriksaanHarian);
            
            const pemeriksaanChart = new Chart(pemeriksaanCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pemeriksaan Awal',
                            data: pemeriksaanAwalData,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBorderWidth: 2,
                            pointBorderColor: '#ffffff'
                        },
                        {
                            label: 'Pemeriksaan Fisik',
                            data: pemeriksaanFisikData,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBorderWidth: 2,
                            pointBorderColor: '#ffffff'
                        },
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
                            borderColor: '#E5E7EB',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            caretPadding: 8,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            }
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
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    elements: {
                        line: {
                            tension: 0.4
                        },
                        point: {
                            radius: 4,
                            hoverRadius: 6
                        }
                    }
                }
            });
            
            // Resize handler
            window.addEventListener('resize', function() {
                pemeriksaanChart.resize();
            });
        }

        // Add smooth scroll animations using Intersection Observer
        const cards = document.querySelectorAll('.bg-white, [class*="bg-gradient"]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                }
            });
        }, {
            threshold: 0.1
        });

        cards.forEach((card) => {
            observer.observe(card);
        });
    });
</script>
@endpush