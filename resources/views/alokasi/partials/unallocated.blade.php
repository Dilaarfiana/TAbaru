@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-center p-5 bg-white border-b">
        <h5 class="text-xl font-bold text-gray-800 mb-3 sm:mb-0 flex items-center">
            <i class="fas fa-user-plus text-blue-500 mr-2"></i> Siswa Belum Dialokasi
        </h5>
        <a href="{{ route('alokasi.index') }}" class="bg-blue-500 text-white hover:bg-blue-600 font-medium px-4 py-2 rounded-md transition-all duration-300 flex items-center">
            <i class="fas fa-list mr-2"></i> Daftar Alokasi
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
    
    <!-- Alokasi Massal -->
    <div class="p-4 border-b bg-gray-50">
        <h2 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
            <i class="fas fa-users-cog text-blue-500 mr-2"></i> Alokasi Massal
        </h2>
        <form action="{{ route('alokasi.multiple') }}" method="POST" id="masalForm">
            @csrf
            <div class="flex flex-col md:flex-row gap-3 items-center">
                <!-- Jurusan -->
                <div class="relative w-full md:w-1/4">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <i class="fas fa-graduation-cap text-gray-500"></i>
                    </div>
                    <select name="kode_jurusan" id="massal_jurusan" required class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500 appearance-none" onchange="filterKelasForMassal()">
                        <option value="">Pilih Jurusan</option>
                        @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->Kode_Jurusan }}">{{ $jurusan->Nama_Jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Kelas -->
                <div class="relative w-full md:w-1/4">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <i class="fas fa-chalkboard text-gray-500"></i>
                    </div>
                    <select name="kode_kelas" id="massal_kelas" required class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500 appearance-none">
                        <option value="">Pilih Kelas</option>
                        <!-- Opsi kelas akan di-load menggunakan JavaScript -->
                    </select>
                </div>
                
                <!-- Button Alokasi -->
                <div class="w-full md:w-auto flex-shrink-0">
                    <button type="submit" id="btnAlokasiBanyak" class="bg-green-500 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-green-600 flex items-center w-full justify-center">
                        <i class="fas fa-users-cog mr-2"></i> Alokasi Siswa Terpilih
                    </button>
                </div>
            </div>

            <!-- Tabel Daftar Siswa -->
            <div class="mt-6 bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-md font-semibold text-gray-700 flex items-center">
                        <i class="fas fa-user-check text-blue-500 mr-2"></i> Pilih Siswa
                    </h3>
                    <div class="flex items-center">
                        <input type="checkbox" id="pilihSemua" class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="pilihSemua" class="text-sm text-gray-700">Pilih Semua</label>
                    </div>
                </div>
                
                <!-- Table Section -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    PILIH
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        ID SISWA <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        NAMA SISWA <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        JENIS KELAMIN <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    AKSI
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="siswaTableBody">
                            @forelse($siswas as $key => $siswa)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="selected_siswa[]" value="{{ $siswa->id_siswa }}" class="check-siswa h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                            {{ $siswa->id_siswa }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            {{ $siswa->nama_siswa }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        @if($siswa->jenis_kelamin == 'L')
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <i class="fas fa-male mr-1"></i> Laki-laki
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">
                                                <i class="fas fa-female mr-1"></i> Perempuan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button type="button" onclick="openAlokasiModal('{{ $siswa->id_siswa }}', '{{ $siswa->nama_siswa }}')" class="text-white bg-blue-500 hover:bg-blue-600 rounded-md p-2 transition-colors duration-200" title="Alokasi Siswa">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 rounded-full p-5 mb-4">
                                                <i class="fas fa-folder-open text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-500 mb-1">Tidak ada siswa yang belum dialokasi</h3>
                                            <p class="text-gray-400 mb-4">Semua siswa sudah dialokasikan ke kelas</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan <span class="font-medium">{{ $siswas->firstItem() ?? 0 }}</span> 
                                sampai <span class="font-medium">{{ $siswas->lastItem() ?? 0 }}</span> 
                                dari <span class="font-medium">{{ $siswas->total() }}</span> data
                            </p>
                        </div>
                        <div>
                            {{ $siswas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Alokasi Siswa -->
<div id="alokasiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative top-0 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="fas fa-user-plus text-blue-500 mr-2"></i>Alokasi Siswa ke Kelas
            </h3>
            <button id="closeAlokasiModal" class="text-gray-600 hover:text-gray-800" onclick="closeModals()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="alokasiForm" action="{{ route('alokasi.process') }}" method="POST">
            @csrf
            <input type="hidden" name="id_siswa" id="alokasi_id_siswa">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                <div id="alokasi_nama_siswa" class="px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700"></div>
            </div>
            
            <div class="mb-4">
                <label for="alokasi_jurusan" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-graduation-cap text-gray-400"></i>
                    </div>
                    <select name="kode_jurusan" id="alokasi_jurusan" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500" onchange="filterKelas()">
                        <option value="">Pilih Jurusan</option>
                        @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->Kode_Jurusan }}">{{ $jurusan->Nama_Jurusan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="alokasi_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-chalkboard text-gray-400"></i>
                    </div>
                    <select name="kode_kelas" id="alokasi_kelas" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kelas</option>
                        <!-- Opsi kelas akan di-load menggunakan JavaScript -->
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-200">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors duration-200">
                    <i class="fas fa-check mr-1"></i> Alokasi
                </button>
            </div>
        </form>
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
        
        // Fungsi untuk "Pilih Semua"
        const checkAll = document.getElementById('pilihSemua');
        const checkboxes = document.querySelectorAll('.check-siswa');
        const btnAlokasiBanyak = document.getElementById('btnAlokasiBanyak');
        
        // Update button status
        function updateButtonStatus() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            btnAlokasiBanyak.disabled = !anyChecked;
            btnAlokasiBanyak.classList.toggle('opacity-50', !anyChecked);
            btnAlokasiBanyak.classList.toggle('cursor-not-allowed', !anyChecked);
        }
        
        // Pilih Semua
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
                updateButtonStatus();
            });
        }
        
        // Event listener untuk masing-masing checkbox
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Check "Pilih Semua" jika semua checkbox tercentang
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                if (checkAll) {
                    checkAll.checked = allChecked;
                }
                updateButtonStatus();
            });
        });
        
        // Validasi form sebelum submit
        const masalForm = document.getElementById('masalForm');
        if (masalForm) {
            masalForm.addEventListener('submit', function(e) {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                if (!anyChecked) {
                    e.preventDefault();
                    alert('Pilih minimal satu siswa untuk dialokasikan');
                }
            });
        }
        
        // Set status awal button
        updateButtonStatus();
    });
    
    // Data kelas untuk filter dropdown
    const semuaKelas = @json($kelass);
    
    // Buka modal alokasi
    function openAlokasiModal(id, nama) {
        document.getElementById('alokasi_id_siswa').value = id;
        document.getElementById('alokasi_nama_siswa').textContent = nama;
        document.getElementById('alokasiModal').classList.remove('hidden');
    }
    
    // Tutup semua modal
    function closeModals() {
        document.getElementById('alokasiModal').classList.add('hidden');
    }
    
    // Filter kelas berdasarkan jurusan yang dipilih untuk modal alokasi
    function filterKelas() {
        const jurusanSelect = document.getElementById('alokasi_jurusan');
        const kelasSelect = document.getElementById('alokasi_kelas');
        const selectedJurusan = jurusanSelect.value;
        
        // Reset option kelas
        kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
        
        // Filter kelas berdasarkan jurusan
        if (selectedJurusan) {
            const kelasFiltered = semuaKelas.filter(kelas => 
                kelas.Kode_Jurusan.toUpperCase() === selectedJurusan.toUpperCase()
            );
            
            kelasFiltered.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas.Kode_Kelas;
                option.textContent = kelas.Nama_Kelas;
                kelasSelect.appendChild(option);
            });
        }
    }
    
    // Filter kelas berdasarkan jurusan untuk alokasi massal
    function filterKelasForMassal() {
        const jurusanSelect = document.getElementById('massal_jurusan');
        const kelasSelect = document.getElementById('massal_kelas');
        const selectedJurusan = jurusanSelect.value;
        
        // Reset option kelas
        kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
        
        // Filter kelas berdasarkan jurusan
        if (selectedJurusan) {
            const kelasFiltered = semuaKelas.filter(kelas => 
                kelas.Kode_Jurusan.toUpperCase() === selectedJurusan.toUpperCase()
            );
            
            kelasFiltered.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas.Kode_Kelas;
                option.textContent = kelas.Nama_Kelas;
                kelasSelect.appendChild(option);
            });
        }
    }
</script>
@endpush