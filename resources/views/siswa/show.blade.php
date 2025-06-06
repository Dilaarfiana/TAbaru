{{-- File: resources/views/siswa/show.blade.php --}}
{{-- ADMIN: FULL ACCESS, PETUGAS: LIMITED ACCESS, DOKTER: READ ONLY, ORANGTUA: OWN CHILD ONLY (NO PARENT DATA) --}}
@extends('layouts.app')

@section('content')
@php
    $userLevel = session('user_level');
    $isAdmin = $userLevel === 'admin';
    $isPetugas = $userLevel === 'petugas';
    $isDokter = $userLevel === 'dokter';
    $isOrangTua = $userLevel === 'orang_tua';
    
    // Check if orang tua is accessing their own child's data
    if ($isOrangTua) {
        $siswaId = session('siswa_id');
        if ($siswaId !== $siswa->id_siswa) {
            abort(403, 'Anda hanya dapat melihat data anak Anda sendiri.');
        }
    }
    
    // Define routes based on user role
    if ($isAdmin) {
        $baseRoute = 'siswa';
        $indexRoute = 'siswa.index';
        $editRoute = 'siswa.edit';
    } elseif ($isPetugas) {
        $baseRoute = 'petugas.siswa';
        $indexRoute = 'petugas.siswa.index';
        $editRoute = 'petugas.siswa.edit';
    } elseif ($isDokter) {
        $baseRoute = 'dokter.siswa';
        $indexRoute = 'dokter.siswa.index';
        $editRoute = null; // Dokter tidak bisa edit
    } else { // orang_tua
        $baseRoute = 'orangtua.siswa';
        $indexRoute = 'orangtua.siswa.show';
        $editRoute = 'orangtua.siswa.edit';
    }
@endphp

<div class="p-4 bg-gray-50 min-h-screen">
    <!-- Detail Card -->
    <div class="max-w-5xl mx-auto">
        <!-- Header dengan warna solid -->
        <div class="bg-blue-600 rounded-t-lg px-6 py-5 flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <div>
                    <h2 class="text-xl font-bold text-white">
                        @if($isOrangTua)
                            Data Siswa Saya
                        @else
                            Detail Data Siswa
                        @endif
                        @if($isDokter)
                            <span class="ml-3 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                <i class="fas fa-stethoscope mr-1"></i>Akses Dokter
                            </span>
                        @elseif($isPetugas)
                            <span class="ml-3 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                <i class="fas fa-user-tie mr-1"></i>Akses Petugas
                            </span>
                        @elseif($isAdmin)
                            <span class="ml-3 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                <i class="fas fa-user-shield mr-1"></i>Akses Admin
                            </span>
                        @elseif($isOrangTua)
                            <span class="ml-3 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                <i class="fas fa-heart mr-1"></i>Data Anak
                            </span>
                        @endif
                    </h2>
                    <p class="text-blue-100 text-sm">ID: {{ $siswa->id_siswa }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                @if($isAdmin || $isPetugas || $isOrangTua)
                <a href="{{ route($editRoute, $siswa->id_siswa) }}" class="flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-md transition-all duration-300 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    @if($isOrangTua)
                        Edit Data
                    @elseif($isPetugas)
                        Edit Data (Terbatas)
                    @else
                        Edit Data
                    @endif
                </a>
                @endif
                
                @if(!$isOrangTua)
                <a href="{{ route($indexRoute) }}" class="flex items-center px-4 py-2 bg-white text-gray-700 rounded-md hover:bg-gray-100 transition-all duration-300 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                @else
                <a href="{{ route('dashboard.orangtua') }}" class="flex items-center px-4 py-2 bg-white text-gray-700 rounded-md hover:bg-gray-100 transition-all duration-300 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-b-lg shadow-lg overflow-hidden p-6">
            <!-- Access Level Info -->
            @if($isDokter)
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            Anda mengakses data siswa dengan <strong>Akses Dokter</strong>. 
                            Anda dapat melihat semua data siswa namun tidak dapat mengubah atau menghapus data.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isPetugas)
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Anda mengakses data siswa dengan <strong>Akses Petugas</strong>. 
                            Anda dapat melihat dan mengedit data dasar siswa, namun tidak dapat menghapus atau mengakses alokasi siswa.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($isOrangTua)
            <div class="mb-6 bg-purple-50 border-l-4 border-purple-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-heart text-purple-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-purple-700">
                            Anda melihat data <strong>anak Anda</strong>. 
                            Anda dapat melihat informasi lengkap siswa dan mengedit data yang diizinkan untuk orang tua.
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Format ID Siswa Information -->
            @php
                $idStatus = '';
                $idStatusClass = '';
                $idFormatInfo = '';
                
                if (strlen($siswa->id_siswa) >= 6) {
                    if (substr($siswa->id_siswa, 0, 1) == '6') {
                        if ($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan) {
                            $idStatus = 'Sudah Dialokasi ke Jurusan';
                            $idStatusClass = 'bg-green-100 text-green-800';
                            $idFormatInfo = '6 + kode jurusan + tahun (yy) + nomor urut (001)';
                        } else {
                            $idStatus = 'Belum Dialokasi ke Jurusan';
                            $idStatusClass = 'bg-yellow-100 text-yellow-800';
                            $idFormatInfo = '6 + tahun (yy) + nomor urut (001)';
                        }
                    } else {
                        $idStatus = 'Format ID Lama';
                        $idStatusClass = 'bg-orange-100 text-orange-800';
                        $idFormatInfo = 'Format ID lama (perlu diperbarui)';
                    }
                }
            @endphp
            
            @if($idStatus && !$isOrangTua)
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Format ID Siswa</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>ID Siswa: <span class="font-mono font-medium">{{ $siswa->id_siswa }}</span></p>
                            <p class="mt-1">Status: <span class="px-2 py-0.5 rounded-full text-xs {{ $idStatusClass }}">{{ $idStatus }}</span></p>
                            <p class="mt-1">Format: {{ $idFormatInfo }}</p>
                            
                            @if($idStatus == 'Belum Dialokasi ke Jurusan' && $isAdmin)
                            <div class="mt-3">
                                <a href="{{ route('alokasi.unallocated') }}" 
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <i class="fas fa-user-check mr-1"></i> Alokasikan Siswa
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Information Card for Siswa -->
            <div class="mb-8 border border-blue-100 rounded-lg overflow-hidden">
                <div class="bg-blue-500 px-4 py-3">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="text-md font-semibold text-white">
                            @if($isOrangTua)
                                Informasi Anak Anda
                            @else
                                Informasi Siswa
                            @endif
                        </h3>
                    </div>
                </div>
                <div class="p-4 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">ID Siswa</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $siswa->id_siswa }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Nama Siswa</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $siswa->nama_siswa }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Tempat Lahir</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $siswa->tempat_lahir ?? '-' }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Tanggal Lahir</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $siswa->tanggal_lahir ? date('d-m-Y', strtotime($siswa->tanggal_lahir)) : '-' }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Jenis Kelamin</div>
                            <div class="flex-1 font-semibold text-gray-800">
                                @if($siswa->jenis_kelamin == 'L')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Laki-laki</span>
                                @elseif($siswa->jenis_kelamin == 'P')
                                    <span class="px-2 py-1 bg-pink-100 text-pink-800 text-xs rounded-full">Perempuan</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Tanggal Masuk</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $siswa->tanggal_masuk ? date('d-m-Y', strtotime($siswa->tanggal_masuk)) : '-' }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Status</div>
                            <div class="flex-1 font-semibold text-gray-800">
                                @if($siswa->status_aktif)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Tidak Aktif</span>
                                @endif
                            </div>
                        </div>
                        @if(!$isOrangTua || ($isOrangTua && $siswa->tanggal_lulus))
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Tanggal Lulus</div>
                            <div class="flex-1 font-semibold text-gray-800">{{ $siswa->tanggal_lulus ? date('d-m-Y', strtotime($siswa->tanggal_lulus)) : '-' }}</div>
                        </div>
                        @endif
                        @if(isset($umur) && $umur)
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Usia</div>
                            <div class="flex-1 font-semibold text-gray-800">
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">{{ $umur }} tahun</span>
                            </div>
                        </div>
                        @endif
                        @if(isset($lamaSekolah) && $lamaSekolah)
                        <div class="flex">
                            <div class="w-32 text-sm font-medium text-gray-500">Lama Sekolah</div>
                            <div class="flex-1 font-semibold text-gray-800">
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">
                                    {{ $lamaSekolah->y }} tahun, {{ $lamaSekolah->m }} bulan, {{ $lamaSekolah->d }} hari
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 {{ $isOrangTua ? 'md:grid-cols-1' : 'md:grid-cols-2' }} gap-8 mb-8">
                <!-- Information Card for Akademik -->
                <div class="border border-indigo-100 rounded-lg overflow-hidden">
                    <div class="bg-indigo-500 px-4 py-3">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                            </svg>
                            <h3 class="text-md font-semibold text-white">Data Akademik</h3>
                        </div>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Kelas</div>
                                <div class="flex-1 font-semibold text-gray-800">
                                    @if($siswa->detailSiswa && $siswa->detailSiswa->kelas)
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">
                                            {{ $siswa->detailSiswa->kelas->Nama_Kelas }}
                                        </span>
                                        @if($siswa->detailSiswa->kelas->Tahun_Ajaran)
                                            <span class="ml-1 text-xs text-gray-500">
                                                ({{ $siswa->detailSiswa->kelas->Tahun_Ajaran }})
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                            Belum dialokasikan
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Jurusan</div>
                                <div class="flex-1 font-semibold text-gray-800">
                                    @php
                                        $namaJurusan = null;
                                        $kodeJurusan = null;
                                        
                                        // Coba mendapatkan jurusan melalui kelas
                                        if($siswa->detailSiswa && $siswa->detailSiswa->kelas && isset($siswa->detailSiswa->kelas->jurusan)) {
                                            $namaJurusan = $siswa->detailSiswa->kelas->jurusan->Nama_Jurusan;
                                            $kodeJurusan = $siswa->detailSiswa->kelas->jurusan->Kode_Jurusan;
                                        }
                                        // Jika tidak berhasil, coba mendapatkan jurusan langsung dari DetailSiswa
                                        elseif($siswa->detailSiswa && isset($siswa->detailSiswa->jurusan)) {
                                            $namaJurusan = $siswa->detailSiswa->jurusan->Nama_Jurusan;
                                            $kodeJurusan = $siswa->detailSiswa->jurusan->Kode_Jurusan;
                                        }
                                        // Jika masih tidak berhasil, coba ambil dari kode_jurusan
                                        elseif($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan) {
                                            $jurusan = \App\Models\Jurusan::where('Kode_Jurusan', $siswa->detailSiswa->kode_jurusan)->first();
                                            if($jurusan) {
                                                $namaJurusan = $jurusan->Nama_Jurusan;
                                                $kodeJurusan = $jurusan->Kode_Jurusan;
                                            }
                                        }
                                    @endphp
                                    
                                    @if($namaJurusan && $kodeJurusan)
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                                            {{ $namaJurusan }}
                                            <span class="text-xs ml-1 text-purple-600">({{ $kodeJurusan }})</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                                            Belum dialokasikan
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if(!$siswa->detailSiswa && $isAdmin)
                            <div class="mt-4 p-3 bg-yellow-50 rounded-md">
                                <div class="flex items-center text-yellow-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm">Siswa belum dialokasikan ke kelas dan jurusan</span>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('alokasi.unallocated') }}" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-user-check mr-1"></i> Alokasikan Sekarang
                                    </a>
                                </div>
                            </div>
                            @elseif(!$siswa->detailSiswa && !$isAdmin)
                            <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                <div class="flex items-center text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm">
                                        @if($isOrangTua)
                                            Siswa belum dialokasikan ke kelas dan jurusan
                                        @else
                                            Siswa belum dialokasikan ke kelas dan jurusan
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @elseif(substr($siswa->id_siswa, 0, 1) == '6' && $siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan && $isAdmin)
                                @php
                                    $kodeJurusan = $siswa->detailSiswa->kode_jurusan;
                                    $idContainsJurusan = (strlen($siswa->id_siswa) > 6 && substr($siswa->id_siswa, 1, strlen($kodeJurusan)) === $kodeJurusan);
                                @endphp
                                
                                @if(!$idContainsJurusan)
                                <div class="mt-4 p-3 bg-orange-50 rounded-md">
                                    <div class="flex items-center text-orange-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span class="text-sm">ID siswa perlu diperbarui agar sesuai dengan format baru</span>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('alokasi.unallocated') }}" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            <i class="fas fa-sync mr-1"></i> Perbarui ID Siswa
                                        </a>
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Information Card for Sistem - Only for non-parents -->
                @if(!$isOrangTua)
                <div class="border border-gray-100 rounded-lg overflow-hidden">
                    <div class="bg-gray-500 px-4 py-3">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-md font-semibold text-white">Informasi Sistem</h3>
                        </div>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Dibuat Pada</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $siswa->created_at ? $siswa->created_at->format('d-m-Y H:i:s') : '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Diperbarui Pada</div>
                                <div class="flex-1 font-semibold text-gray-800">{{ $siswa->updated_at ? $siswa->updated_at->format('d-m-Y H:i:s') : '-' }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-32 text-sm font-medium text-gray-500">Format ID</div>
                                <div class="flex-1 font-semibold text-gray-800">
                                    @if(substr($siswa->id_siswa, 0, 1) == '6')
                                        @if($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan)
                                            @php
                                                $kodeJurusan = $siswa->detailSiswa->kode_jurusan;
                                                $idContainsJurusan = (strlen($siswa->id_siswa) > 6 && substr($siswa->id_siswa, 1, strlen($kodeJurusan)) === $kodeJurusan);
                                            @endphp
                                            
                                            @if($idContainsJurusan)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                    Format Baru (Dengan Jurusan)
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                                    Format Baru (Perlu Update)
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                Format Baru (Belum Alokasi)
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                            Format Lama
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Information Card for Orang Tua - Hidden for orang_tua role -->
            @if(!$isOrangTua)
            <div class="border border-green-100 rounded-lg overflow-hidden">
                <div class="bg-green-500 px-4 py-3">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-md font-semibold text-white">Informasi Orang Tua</h3>
                    </div>
                </div>
                <div class="p-4 bg-white">
                    @if($siswa->orangTua)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border border-blue-100 rounded-lg p-3 bg-blue-50">
                                <h4 class="font-medium text-blue-800 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Data Ayah
                                </h4>
                                <div class="space-y-1">
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">Nama</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->nama_ayah ?? '-' }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">Pekerjaan</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->pekerjaan_ayah ?? '-' }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">Pendidikan</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->pendidikan_ayah ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border border-pink-100 rounded-lg p-3 bg-pink-50">
                                <h4 class="font-medium text-pink-800 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Data Ibu
                                </h4>
                                <div class="space-y-1">
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">Nama</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->nama_ibu ?? '-' }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">Pekerjaan</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->pekerjaan_ibu ?? '-' }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">Pendidikan</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->pendidikan_ibu ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="md:col-span-2 border border-gray-100 rounded-lg p-3 bg-gray-50">
                                <h4 class="font-medium text-gray-800 mb-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Kontak
                                </h4>
                                <div class="space-y-1">
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">Alamat</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->alamat ?? '-' }}</div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-24 text-xs font-medium text-gray-500">No. Telepon</div>
                                        <div class="flex-1 text-sm text-gray-800">{{ $siswa->orangTua->no_telp ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($isAdmin)
                            <div class="md:col-span-2 flex justify-center mt-2">
                                <a href="{{ route('orangtua.edit', ['orangtua' => $siswa->orangTua->id_orang_tua]) }}" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition duration-300 flex items-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Data Orang Tua
                                </a>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col items-center py-4">
                            <div class="p-3 bg-yellow-50 text-yellow-700 rounded-md mb-4 w-full">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm">Data orang tua belum terinput untuk siswa ini</span>
                                </div>
                            </div>
                            @if($isAdmin)
                            <a href="{{ route('orangtua.create', ['siswa_id' => $siswa->id_siswa]) }}" class="bg-green-500 text-white hover:bg-green-600 font-medium px-4 py-2 rounded-md transition duration-300 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Data Orang Tua
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-8 pt-4 border-t flex justify-end space-x-3">
                @if($isAdmin)
                <form action="{{ route('siswa.destroy', $siswa->id_siswa) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Data
                    </button>
                </form>
                @endif
                
                @if($isAdmin || $isPetugas || $isOrangTua)
                <a href="{{ route($editRoute, $siswa->id_siswa) }}" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    @if($isOrangTua)
                        Edit Data
                    @elseif($isPetugas)
                        Edit Data (Terbatas)
                    @else
                        Edit Data
                    @endif
                </a>
                @endif
                
                @if(!$isOrangTua)
                <a href="{{ route($indexRoute) }}" class="flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors duration-200 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                @else
                <a href="{{ route('dashboard.orangtua') }}" class="flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors duration-200 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Log info for debugging
    console.log('Detail Siswa Script loaded');
    console.log('ID Siswa:', '{{ $siswa->id_siswa }}');
    console.log('User Level:', '{{ $userLevel }}');
    
    @if($siswa->detailSiswa && $siswa->detailSiswa->kode_jurusan)
        console.log('Kode Jurusan:', '{{ $siswa->detailSiswa->kode_jurusan }}');
    @else
        console.log('Kode Jurusan: Tidak ada');
    @endif
    
    // Fungsi untuk menyalin ID siswa ke clipboard
    const idSiswa = document.querySelector('.font-mono.font-medium');
    if (idSiswa) {
        idSiswa.addEventListener('click', function() {
            const tempInput = document.createElement('input');
            tempInput.value = this.textContent;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Tampilkan notifikasi kecil
            const notification = document.createElement('div');
            notification.textContent = 'ID siswa disalin!';
            notification.className = 'px-3 py-2 bg-blue-500 text-white text-xs rounded-md fixed top-4 right-4 shadow-md z-50';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2000);
        });
        
        // Tambahkan indikator
        idSiswa.title = 'Klik untuk menyalin ID';
        idSiswa.style.cursor = 'pointer';
        
        // Tambahkan ikon salin
        const copyIcon = document.createElement('i');
        copyIcon.className = 'fas fa-copy ml-1 text-blue-400';
        copyIcon.style.fontSize = '0.75rem';
        idSiswa.appendChild(copyIcon);
    }
    
    // Double check access for parents
    const userLevel = '{{ $userLevel }}';
    const allowedSiswaId = '{{ session("siswa_id") }}';
    const currentSiswaId = '{{ $siswa->id_siswa }}';
    
    if (userLevel === 'orang_tua' && allowedSiswaId !== currentSiswaId) {
        console.warn('Access violation detected: Parent trying to access another student data');
        window.location.href = '{{ route("orangtua.siswa.show") }}';
        return;
    }
});
</script>
@endpush
@endsection