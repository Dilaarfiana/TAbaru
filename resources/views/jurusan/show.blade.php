@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-graduation-cap mr-3 text-blue-500"></i>Detail Jurusan
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('jurusan.edit', $jurusan->Kode_Jurusan) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg shadow-sm transition duration-300 flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('jurusan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg shadow-sm transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="p-6">
        <div class="bg-blue-50 rounded-lg p-4 mb-6 flex items-start">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div>
                <h3 class="font-medium text-blue-800">Informasi Jurusan</h3>
                <p class="text-blue-600 text-sm mt-1">
                    Berikut adalah detail lengkap dari jurusan {{ $jurusan->Nama_Jurusan }} beserta kelas-kelas yang terdaftar.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 p-4 bg-gray-50">
                        <h3 class="font-medium text-gray-700">Detail Jurusan</h3>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-500">Kode Jurusan</span>
                            <span class="px-3 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $jurusan->Kode_Jurusan }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-500">Nama Jurusan</span>
                            <div class="text-lg font-medium text-gray-900 mt-1">{{ $jurusan->Nama_Jurusan }}</div>
                        </div>
                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-500">Jumlah Kelas</span>
                            <div class="text-xl font-bold text-gray-900 mt-1">
                                {{ $jurusan->kelas->count() }} 
                                <span class="text-sm font-normal text-gray-500">kelas</span>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <form action="{{ route('jurusan.destroy', $jurusan->Kode_Jurusan) }}" method="POST" class="inline-block w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex justify-center items-center text-red-600 hover:bg-red-50 border border-red-200 rounded-md px-4 py-2 transition duration-200" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus jurusan ini? Semua kelas yang terkait juga akan dihapus.')">
                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Jurusan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 p-4 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-medium text-gray-700">Daftar Kelas</h3>
                        @if(Route::has('kelas.create'))
                            <a href="{{ route('kelas.create') }}?jurusan={{ $jurusan->Kode_Jurusan }}" class="text-sm bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded transition duration-300">
                                <i class="fas fa-plus mr-1"></i> Tambah Kelas
                            </a>
                        @endif
                    </div>
                    
                    @if($jurusan->kelas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kode Kelas
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Kelas
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tahun Ajaran
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah Siswa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($jurusan->kelas as $kelas)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $kelas->Kode_Kelas }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $kelas->Nama_Kelas }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $kelas->Tahun_Ajaran ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $kelas->Jumlah_Siswa ?? '0' }} siswa
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if(Route::has('kelas.show'))
                                                    <a href="{{ route('kelas.show', $kelas->Kode_Kelas) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-500 mb-4">
                                <i class="fas fa-school text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada kelas</h3>
                            <p class="text-gray-500 mb-6">Belum ada kelas yang terdaftar di jurusan ini.</p>
                            
                            @if(Route::has('kelas.create'))
                                <a href="{{ route('kelas.create') }}?jurusan={{ $jurusan->Kode_Jurusan }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i> Tambah Kelas Baru
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($jurusan->kelas->count() > 0)
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Distribusi Siswa per Kelas</h2>
        </div>
        <div class="p-6">
            <canvas id="kelasChart" height="300"></canvas>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Ringkasan Kelas</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between bg-green-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">Total Kelas</span>
                        <p class="text-2xl font-bold text-gray-800">{{ $jurusan->kelas->count() }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-school text-green-500 text-xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center justify-between bg-blue-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">Total Siswa</span>
                        <p class="text-2xl font-bold text-gray-800">{{ $jurusan->kelas->sum('Jumlah_Siswa') ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-user-graduate text-blue-500 text-xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center justify-between bg-purple-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">Rata-rata Siswa per Kelas</span>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $jurusan->kelas->count() > 0 ? round($jurusan->kelas->sum('Jumlah_Siswa') / $jurusan->kelas->count()) : 0 }}
                        </p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-chart-pie text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
@if($jurusan->kelas->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('kelasChart').getContext('2d');
        
        // Prepare data for chart
        const labels = @json($jurusan->kelas->pluck('Nama_Kelas'));
        const data = @json($jurusan->kelas->pluck('Jumlah_Siswa'));
        
        // Generate colors
        const colors = [
            '#4299E1', // blue-500
            '#38B2AC', // teal-500
            '#48BB78', // green-500
            '#F6AD55', // orange-400
            '#9F7AEA', // purple-400
            '#ED64A6', // pink-500
            '#F56565', // red-500
            '#667EEA', // indigo-500
            '#ED8936', // orange-500
            '#ECC94B', // yellow-500
        ];
        
        const bgColors = [];
        for (let i = 0; i < labels.length; i++) {
            bgColors.push(colors[i % colors.length]);
        }
        
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bgColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} siswa (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush