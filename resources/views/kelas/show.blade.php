@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-chalkboard mr-3 text-blue-500"></i>Detail Kelas
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('kelas.edit', $kelas->Kode_Kelas) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg shadow-sm transition duration-300 flex items-center">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('kelas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg shadow-sm transition duration-300 flex items-center">
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
                <h3 class="font-medium text-blue-800">Informasi Kelas</h3>
                <p class="text-blue-600 text-sm mt-1">
                    Berikut adalah detail lengkap dari kelas {{ $kelas->Nama_Kelas }} beserta daftar siswa yang terdaftar.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 p-4 bg-gray-50">
                        <h3 class="font-medium text-gray-700">Detail Kelas</h3>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-500">Kode Kelas</span>
                            <span class="px-3 py-1 text-sm font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $kelas->Kode_Kelas }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-500">Nama Kelas</span>
                            <div class="text-lg font-medium text-gray-900 mt-1">{{ $kelas->Nama_Kelas }}</div>
                        </div>
                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-500">Tahun Ajaran</span>
                            <div class="text-lg font-medium text-gray-900 mt-1">{{ $kelas->Tahun_Ajaran ?? '-' }}</div>
                        </div>
                        <div class="mb-4">
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
                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-500">Jumlah Siswa</span>
                            <div class="text-xl font-bold text-gray-900 mt-1">
                                {{ $kelas->jumlah_siswa ?? $kelas->Jumlah_Siswa ?? '0' }} 
                                <span class="text-sm font-normal text-gray-500">siswa</span>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <form action="{{ route('kelas.destroy', $kelas->Kode_Kelas) }}" method="POST" class="inline-block w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex justify-center items-center text-red-600 hover:bg-red-50 border border-red-200 rounded-md px-4 py-2 transition duration-200" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Kelas
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 p-4 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-medium text-gray-700">Daftar Siswa</h3>
                        <a href="{{ route('kelas.tambah-siswa', $kelas->Kode_Kelas) }}" class="text-sm bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded transition duration-300">
                            <i class="fas fa-plus mr-1"></i> Tambah Siswa
                        </a>
                    </div>
                    
                    @if(isset($siswa) && $siswa->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID Siswa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Siswa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis Kelamin
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($siswa as $detail)
                                        @if($detail->siswa)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $detail->siswa->id_siswa }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                    {{ $detail->siswa->nama_siswa }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($detail->siswa->jenis_kelamin == 'L')
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            Laki-laki
                                                        </span>
                                                    @elseif($detail->siswa->jenis_kelamin == 'P')
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                                            Perempuan
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('siswa.show', $detail->siswa->id_siswa) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    <form action="{{ route('kelas.remove-siswa', [$kelas->Kode_Kelas, $detail->id_detsiswa]) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini dari kelas?')">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                            {{ $siswa->links() }}
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-500 mb-4">
                                <i class="fas fa-user-graduate text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada siswa</h3>
                            <p class="text-gray-500 mb-6">Belum ada siswa yang terdaftar di kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Ringkasan Kelas</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between bg-green-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">Status Kelas</span>
                        <p class="text-2xl font-bold text-gray-800">Aktif</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center justify-between bg-blue-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">Total Siswa</span>
                        <p class="text-2xl font-bold text-gray-800">{{ $kelas->jumlah_siswa ?? $kelas->Jumlah_Siswa ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-user-graduate text-blue-500 text-xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center justify-between bg-purple-50 p-4 rounded-lg">
                    <div>
                        <span class="text-sm text-gray-500">Jurusan</span>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $kelas->jurusan ? $kelas->jurusan->Nama_Jurusan : '-' }}
                        </p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-graduation-cap text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Distribusi Jenis Kelamin</h2>
        </div>
        <div class="p-6">
            @if(isset($jumlahLakiLaki) || isset($jumlahPerempuan))
                <canvas id="genderChart" height="250"></canvas>
            @else
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                        <i class="fas fa-chart-pie text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada data</h3>
                    <p class="text-gray-400">Belum ada data siswa untuk ditampilkan.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(isset($jumlahLakiLaki) || isset($jumlahPerempuan))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('genderChart').getContext('2d');
        
        // Use the precalculated counts from controller
        const maleCount = {{ $jumlahLakiLaki ?? 0 }};
        const femaleCount = {{ $jumlahPerempuan ?? 0 }};
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [maleCount, femaleCount],
                    backgroundColor: [
                        '#3B82F6', // blue-500
                        '#EC4899', // pink-500
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = maleCount + femaleCount;
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