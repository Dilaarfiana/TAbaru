@extends('layouts.admin')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Siswa Card -->
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded">
                    {{ $totalSiswa > 0 ? round($siswaAktif / $totalSiswa * 100) : 0 }}% <i class="fas fa-chart-pie"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $totalSiswa }}</h3>
            <p class="text-gray-600 text-sm">Total Siswa</p>
        </div>
        
        <!-- Dokter Card -->
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                    <i class="fas fa-user-md"></i>
                </div>
                <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-1 rounded">
                    {{ $totalDokter }} <i class="fas fa-user-md"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $totalDokter }}</h3>
            <p class="text-gray-600 text-sm">Dokter</p>
        </div>
        
        <!-- Petugas UKS Card -->
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-500">
                    <i class="fas fa-first-aid"></i>
                </div>
                <span class="text-xs font-semibold bg-gray-100 text-gray-700 px-2 py-1 rounded">
                    {{ $totalOrangTua }} <i class="fas fa-users"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $totalOrangTua }}</h3>
            <p class="text-gray-600 text-sm">Orang Tua</p>
        </div>
        
        <!-- Pemeriksaan Card -->
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-500">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-1 rounded">
                    {{ $totalPemeriksaan }} <i class="fas fa-clipboard-check"></i>
                </span>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $totalPemeriksaan }}</h3>
            <p class="text-gray-600 text-sm">Total Pemeriksaan</p>
        </div>
    </div>
    
    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mt-5">
        <!-- Status Kesehatan Card -->
        <div class="lg:col-span-5 bg-blue-500 rounded-lg p-5 text-white relative">
            <div class="relative z-10">
                <h2 class="text-xl font-bold mb-3">Status Kesehatan</h2>
                <p class="mb-4">Ringkasan status kesehatan siswa saat ini</p>
                
                <ul class="space-y-3">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ $siswaAktif }} Siswa dalam kondisi sehat</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $siswaTidakAktif }} Siswa memerlukan perhatian</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span>{{ $totalRekamMedis }} Total rekam medis tercatat</span>
                    </li>
                </ul>
            </div>
            
            <!-- Ilustrasi Kesehatan -->
            <div class="absolute bottom-0 right-0 opacity-20">
                <i class="fas fa-heartbeat text-8xl"></i>
            </div>
        </div>
        
        <!-- Statistik Pemeriksaan -->
        <div class="lg:col-span-7 bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Statistik Pemeriksaan</h2>
                <select class="border border-gray-300 rounded px-2 py-1 text-sm">
                    <option>Tahun {{ date('Y') }}</option>
                    <option>Tahun {{ date('Y')-1 }}</option>
                    <option>Tahun {{ date('Y')-2 }}</option>
                </select>
            </div>
            
            <div class="h-64">
                <canvas id="pemeriksaanChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Bottom Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mt-5">
        <!-- Pemeriksaan Terbaru -->
        <div class="lg:col-span-7 bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Pemeriksaan Terbaru</h2>
                <a href="#" class="text-blue-500 text-sm hover:underline">Lihat Semua</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="text-left px-4 py-2 text-gray-500 text-xs uppercase">Tanggal</th>
                            <th class="text-left px-4 py-2 text-gray-500 text-xs uppercase">Nama Siswa</th>
                            <th class="text-left px-4 py-2 text-gray-500 text-xs uppercase">Dokter</th>
                            <th class="text-left px-4 py-2 text-gray-500 text-xs uppercase">Hasil</th>
                            <th class="text-right px-4 py-2 text-gray-500 text-xs uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemeriksaanTerbaru as $pemeriksaan)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($pemeriksaan['tanggal'])->format('d M Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 mr-2">
                                        {{ substr($pemeriksaan['siswa']->Nama_Siswa ?? 'XX', 0, 2) }}
                                    </div>
                                    <span>{{ $pemeriksaan['siswa']->Nama_Siswa ?? 'Tidak diketahui' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $pemeriksaan['dokter']->Nama_Dokter ?? 'Tidak diketahui' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ Str::limit($pemeriksaan['hasil'] ?? 'Tidak ada hasil', 20) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right">
                                <a href="#" class="text-blue-500 hover:text-blue-700">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                <div class="py-4">Tidak ada data pemeriksaan terbaru</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Resep Terbaru -->
        <div class="lg:col-span-5 bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Resep Terbaru</h2>
                <a href="#" class="text-blue-500 text-sm hover:underline">Lihat Semua</a>
            </div>
            
            <div class="space-y-4">
                @forelse($resepTerbaru as $resep)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between">
                        <p class="font-medium">{{ $resep->siswa->Nama_Siswa ?? 'Tidak diketahui' }}</p>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($resep->Tanggal_Resep)->format('d M Y') }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $resep->Nama_Obat ?? '' }}, {{ $resep->Dosis ?? '' }}, {{ $resep->Durasi ?? '' }}</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-gray-500">{{ $resep->dokter->Nama_Dokter ?? 'Tidak diketahui' }}</span>
                        <a href="#" class="text-blue-500 text-xs hover:underline">Lihat Resep</a>
                    </div>
                </div>
                @empty
                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-center text-gray-500">Tidak ada data resep terbaru</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                            borderWidth: 2,
                            pointRadius: 3
                        },
                        {
                            label: 'Pemeriksaan Fisik',
                            data: pemeriksaanFisikData,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3
                        },
                        {
                            label: 'Pemeriksaan Harian',
                            data: pemeriksaanHarianData,
                            borderColor: '#F59E0B',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3
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
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#333',
                            bodyColor: '#666',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: true,
                            caretPadding: 5,
                            cornerRadius: 4
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 2]
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
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
                            radius: 3,
                            hoverRadius: 5
                        }
                    }
                }
            });
            
            // Resize handler
            window.addEventListener('resize', function() {
                pemeriksaanChart.resize();
            });
        }
    });
</script>
@endpush