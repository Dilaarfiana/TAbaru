<div class="sidebar-menu py-2">

    <!-- DASHBOARD - Semua Role -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DASHBOARD</h2>
    <ul class="mb-4">
        <li>
            <a href="{{ route('dashboard') }}" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md">
                <i class="fas fa-chart-line w-5 text-center"></i>
                <span class="ml-2">Dashboard</span>
            </a>
        </li>
    </ul>

    @if(session('user_id'))
        @if(session('user_level') === 'admin')
            <!-- DATA MASTER - Hanya Admin -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DATA MASTER</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="master-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-database w-5 text-center"></i>
                            <span class="ml-2">Data Master</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="master-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('jurusan.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-graduation-cap w-5 text-center"></i><span class="ml-2">Data Jurusan</span></a></li>
                        <li><a href="{{ route('kelas.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-chalkboard w-5 text-center"></i><span class="ml-2">Data Kelas</span></a></li>
                        <li><a href="{{ route('siswa.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-user-graduate w-5 text-center"></i><span class="ml-2">Data Siswa</span></a></li>
                        <li><a href="{{ route('orangtua.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-users w-5 text-center"></i><span class="ml-2">Data Orang Tua</span></a></li>
                        <li><a href="{{ route('petugasuks.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-first-aid w-5 text-center"></i><span class="ml-2">Data Petugas UKS</span></a></li>
                        <li><a href="{{ route('dokter.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-user-md w-5 text-center"></i><span class="ml-2">Data Dokter</span></a></li>
                    </ul>
                </li>
            </ul>

            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">ALOKASI</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="alokasi-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-sitemap w-5 text-center"></i>
                            <span class="ml-2">Alokasi</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="alokasi-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('alokasi.unallocated') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-share-alt w-5 text-center"></i><span class="ml-2">Alokasi Siswa</span></a></li>
                        <li><a href="{{ route('alokasi.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-tasks w-5 text-center"></i><span class="ml-2">Manajemen Alokasi</span></a></li>
                    </ul>
                </li>
            </ul>

            <!-- PEMERIKSAAN - Admin -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">PEMERIKSAAN</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="pemeriksaan-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-notes-medical w-5 text-center"></i>
                            <span class="ml-2">Pemeriksaan</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="pemeriksaan-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('rekam_medis.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-notes-medical w-5 text-center"></i><span class="ml-2">Rekam Medis</span></a></li>
                        <li><a href="{{ route('pemeriksaan_awal.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-clipboard-check w-5 text-center"></i><span class="ml-2">Pemeriksaan Awal</span></a></li>
                        <li><a href="{{ route('pemeriksaan_fisik.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-diagnoses w-5 text-center"></i><span class="ml-2">Pemeriksaan Fisik</span></a></li>
                        <li><a href="{{ route('pemeriksaan_harian.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-calendar-check w-5 text-center"></i><span class="ml-2">Pemeriksaan Harian</span></a></li>
                        <li><a href="{{ route('resep.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-prescription w-5 text-center"></i><span class="ml-2">Resep Obat</span></a></li>
                    </ul>
                </li>
            </ul>

            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DETAIL</h2>             
            <ul class="mb-4">                 
                <li>                     
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="detail-submenu">                         
                        <span class="flex items-center">                             
                            <i class="fas fa-info-circle w-5 text-center"></i>                             
                            <span class="ml-2">Detail</span>                         
                        </span>                         
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>                     
                    </button>                     
                    <ul id="detail-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2"> 
                        <li>
                            <a href="{{ route('detailsiswa.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md">
                                <i class="fas fa-users w-5 text-center"></i>
                                <span class="ml-2">Detail Siswa</span>
                            </a>
                        </li>                          
                        <li>
                            <a href="{{ route('detail_pemeriksaan.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md">
                                <i class="fas fa-stethoscope w-5 text-center"></i>
                                <span class="ml-2">Detail Pemeriksaan</span>
                            </a>
                        </li>                         
                    </ul>                 
                </li>             
            </ul>

            <!-- LAPORAN - Admin -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">LAPORAN</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="laporan-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-chart-bar w-5 text-center"></i>
                            <span class="ml-2">Laporan</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="laporan-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('laporan.screening') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-file-medical w-5 text-center"></i><span class="ml-2">Laporan Hasil Screening</span></a></li>
                        <li><a href="{{ route('laporan.pemeriksaan_harian') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-calendar-day w-5 text-center"></i><span class="ml-2">Laporan Pemeriksaan Harian</span></a></li>
                    </ul>
                </li>
            </ul>

        @elseif(session('user_level') === 'petugas')
            <!-- DATA SISWA - Petugas -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DATA SISWA</h2>
            <ul class="mb-4">
                <li>
                    <a href="{{ route('petugas.siswa.index') }}" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md">
                        <i class="fas fa-user-graduate w-5 text-center"></i>
                        <span class="ml-2">Data Siswa</span>
                    </a>
                </li>
            </ul>

            <!-- Data Detail Petugas -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DETAIL</h2>             
            <ul class="mb-4">                 
                <li>                     
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="detail-petugas-submenu">                         
                        <span class="flex items-center">                             
                            <i class="fas fa-info-circle w-5 text-center"></i>                             
                            <span class="ml-2">Detail</span>                         
                        </span>                         
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>                     
                    </button>                     
                    <ul id="detail-petugas-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">                          
                        <li>
                            <a href="{{ route('petugas.detail_pemeriksaan.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md">
                                <i class="fas fa-stethoscope w-5 text-center"></i>
                                <span class="ml-2">Detail Pemeriksaan</span>
                            </a>
                        </li>                         
                    </ul>                 
                </li>             
            </ul>

            <!-- PEMERIKSAAN - Petugas -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">PEMERIKSAAN</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="pemeriksaan-petugas-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-notes-medical w-5 text-center"></i>
                            <span class="ml-2">Pemeriksaan</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="pemeriksaan-petugas-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('petugas.rekam_medis.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-notes-medical w-5 text-center"></i><span class="ml-2">Rekam Medis</span></a></li>
                        <li><a href="{{ route('petugas.pemeriksaan_awal.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-clipboard-check w-5 text-center"></i><span class="ml-2">Pemeriksaan Awal</span></a></li>
                        <li><a href="{{ route('petugas.pemeriksaan_fisik.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-diagnoses w-5 text-center"></i><span class="ml-2">Pemeriksaan Fisik</span></a></li>
                        <li><a href="{{ route('petugas.pemeriksaan_harian.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-calendar-check w-5 text-center"></i><span class="ml-2">Pemeriksaan Harian</span></a></li>
                        <li><a href="{{ route('petugas.resep.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-prescription w-5 text-center"></i><span class="ml-2">Resep Obat</span></a></li>
                    </ul>
                </li>
            </ul>

            <!-- LAPORAN - Petugas -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">LAPORAN</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="laporan-petugas-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-chart-bar w-5 text-center"></i>
                            <span class="ml-2">Laporan</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="laporan-petugas-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('petugas.laporan.screening') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-file-medical w-5 text-center"></i><span class="ml-2">Laporan Hasil Screening</span></a></li>
                        <li><a href="{{ route('petugas.laporan.pemeriksaan_harian') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-calendar-day w-5 text-center"></i><span class="ml-2">Laporan Pemeriksaan Harian</span></a></li>
                    </ul>
                </li>
            </ul>

        @elseif(session('user_level') === 'dokter')
            <!-- DATA SISWA - Dokter -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DATA SISWA</h2>
            <ul class="mb-4">
                <li>
                    <a href="{{ route('dokter.siswa.index') }}" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md">
                        <i class="fas fa-user-graduate w-5 text-center"></i>
                        <span class="ml-2">Data Siswa</span>
                    </a>
                </li>
            </ul>

            <!-- PEMERIKSAAN - Dokter (Read Only) -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">PEMERIKSAAN</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="pemeriksaan-dokter-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-notes-medical w-5 text-center"></i>
                            <span class="ml-2">Pemeriksaan</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="pemeriksaan-dokter-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('dokter.rekam_medis.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-eye w-5 text-center"></i><span class="ml-2">Lihat Rekam Medis</span></a></li>
                        <li><a href="{{ route('dokter.pemeriksaan_awal.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-eye w-5 text-center"></i><span class="ml-2">Lihat Pemeriksaan Awal</span></a></li>
                        <li><a href="{{ route('dokter.pemeriksaan_fisik.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-eye w-5 text-center"></i><span class="ml-2">Lihat Pemeriksaan Fisik</span></a></li>
                        <li><a href="{{ route('dokter.resep.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-eye w-5 text-center"></i><span class="ml-2">Lihat Resep Obat</span></a></li>
                        <li><a href="{{ route('dokter.detail_pemeriksaan.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-eye w-5 text-center"></i><span class="ml-2">Lihat Detail Pemeriksaan</span></a></li>
                    </ul>
                </li>
            </ul>

            <!-- LAPORAN - Dokter -->            
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">LAPORAN</h2>             
            <ul class="mb-4">                 
                <li>                     
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="laporan-dokter-submenu">                         
                        <span class="flex items-center">                             
                            <i class="fas fa-chart-line w-5 text-center"></i>                             
                            <span class="ml-2">Laporan</span>                         
                        </span>                         
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>                     
                    </button>                     
                    <ul id="laporan-dokter-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">                         
                        <li><a href="{{ route('dokter.laporan.screening') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-file-medical w-5 text-center"></i><span class="ml-2">Laporan Hasil Screening</span></a></li>                         
                        <li><a href="{{ route('dokter.laporan.harian') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-calendar-day w-5 text-center"></i><span class="ml-2">Laporan Pemeriksaan Harian</span></a></li>                     
                    </ul>                 
                </li>             
            </ul>

        @elseif(session('user_level') === 'orang_tua')
            <!-- DATA SISWA SAYA - Orang Tua -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DATA SISWA</h2>
            <ul class="mb-4">
                <li>
                    <a href="{{ route('orangtua.siswa.show') }}" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md">
                        <i class="fas fa-child w-5 text-center"></i>
                        <span class="ml-2">Data Siswa Saya</span>
                    </a>
                </li>
            </ul>

            <!-- LAPORAN - Orang Tua -->
            <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">LAPORAN</h2>
            <ul class="mb-4">
                <li>
                    <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md transition-colors duration-200" data-target="laporan-orangtua-submenu">
                        <span class="flex items-center">
                            <i class="fas fa-chart-bar w-5 text-center"></i>
                            <span class="ml-2">Laporan</span>
                        </span>
                        <i class="fas fa-chevron-down toggle-icon text-xs transition-transform duration-200"></i>
                    </button>
                    <ul id="laporan-orangtua-submenu" class="submenu pl-6 mt-1 space-y-1 py-1 mb-2">
                        <li><a href="{{ route('orangtua.laporan.screening') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-file-medical w-5 text-center"></i><span class="ml-2">Laporan Hasil Screening</span></a></li>
                        <li><a href="{{ route('orangtua.laporan.harian') }}" class="flex items-center text-gray-600 hover:text-indigo-600 py-1.5 px-2 rounded-md"><i class="fas fa-calendar-day w-5 text-center"></i><span class="ml-2">Laporan Pemeriksaan Harian</span></a></li>
                    </ul>
                </li>
            </ul>

        @endif
    @else
        <!-- Fallback menu jika user tidak login -->
        <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">AKSES</h2>
        <ul class="mb-4">
            <li>
                <a href="{{ route('login') }}" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4 rounded-md">
                    <i class="fas fa-sign-in-alt w-5 text-center"></i>
                    <span class="ml-2">Login</span>
                </a>
            </li>
        </ul>
    @endif

</div>