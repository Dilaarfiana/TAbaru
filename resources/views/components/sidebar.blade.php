<div class="sidebar-menu py-2">
    <!-- HOMES -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">HOMES</h2>
    <ul>
        <li>
            <a href="{{ route('dashboard') }}" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4">
                <i class="fas fa-chart-line"></i>
                <span class="ml-2">Analytics Dashboard</span>
            </a>
        </li>
    </ul>

    <!-- DATA MASTER -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DATA MASTER</h2>
    <ul>
        <li>
            <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4" data-target="master-submenu">
                <span class="flex items-center">
                    <i class="fas fa-database"></i>
                    <span class="ml-2">Data Master</span>
                </span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </button>
            <ul id="master-submenu" class="submenu pl-6 mt-1 space-y-1" style="display: none;">
                <li><a href="{{ route('siswa.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-user-graduate w-5"></i><span class="ml-2">Siswa</span></a></li>
                <li><a href="{{ route('orangtua.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-users w-5"></i><span class="ml-2">Orang Tua</span></a></li>
                <li><a href="{{ route('dokter.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-user-md w-5"></i><span class="ml-2">Dokter</span></a></li>
                <li><a href="{{ route('petugasuks.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-first-aid w-5"></i><span class="ml-2">Petugas UKS</span></a></li>
                <li><a href="{{ route('jurusan.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-graduation-cap w-5"></i><span class="ml-2">Jurusan</span></a></li>
                <li><a href="{{ route('kelas.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-chalkboard w-5"></i><span class="ml-2">Kelas</span></a></li>
            </ul>
        </li>
    </ul>

    <!-- ALOKASI SISWA (Menu Baru) -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">ALOKASI SISWA</h2>
    <ul>
        <li>
            <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4" data-target="alokasi-submenu">
                <span class="flex items-center">
                    <i class="fas fa-user-tag"></i>
                    <span class="ml-2">Alokasi Siswa</span>
                </span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </button>
            <ul id="alokasi-submenu" class="submenu pl-6 mt-1 space-y-1" style="display: none;">
                <li><a href="{{ route('alokasi.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-users-cog w-5"></i><span class="ml-2">Pengaturan Alokasi</span></a></li>
                <li><a href="{{ route('alokasi.unallocated') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-user-plus w-5"></i><span class="ml-2">Siswa Belum Teralokasi</span></a></li>
            </ul>
        </li>
    </ul>

    <!-- PEMERIKSAAN -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">PEMERIKSAAN</h2>
    <ul>
        <li>
            <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4" data-target="pemeriksaan-submenu">
                <span class="flex items-center">
                    <i class="fas fa-notes-medical"></i>
                    <span class="ml-2">Pemeriksaan</span>
                </span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </button>
            <ul id="pemeriksaan-submenu" class="submenu pl-6 mt-1 space-y-1" style="display: none;">
                <li><a href="#" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-stethoscope w-5"></i><span class="ml-2">Detail Pemeriksaan</span></a></li>
                <li><a href="{{ route('pemeriksaan_awal.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-clipboard-check w-5"></i><span class="ml-2">Pemeriksaan Awal</span></a></li>
                <li><a href="{{ route('pemeriksaan_fisik.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-diagnoses w-5"></i><span class="ml-2">Pemeriksaan Fisik</span></a></li>
                <li><a href="#" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-calendar-check w-5"></i><span class="ml-2">Pemeriksaan Harian</span></a></li>
            </ul>
        </li>
    </ul>

    <!-- DATA MEDIS -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DATA MEDIS</h2>
    <ul>
        <li>
            <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4" data-target="medis-submenu">
                <span class="flex items-center">
                    <i class="fas fa-file-medical-alt"></i>
                    <span class="ml-2">Data Medis</span>
                </span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </button>
            <ul id="medis-submenu" class="submenu pl-6 mt-1 space-y-1" style="display: none;">
                <li><a href="{{ route('rekam_medis.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-notes-medical w-5"></i><span class="ml-2">Rekam Medis</span></a></li>
                <li><a href="#" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-prescription w-5"></i><span class="ml-2">Resep</span></a></li>
            </ul>
        </li>
    </ul>

    <!-- DATA DETAIL -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">DATA DETAIL</h2>
    <ul>
        <li>
            <button type="button" class="toggle-btn w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 py-2 px-4" data-target="detail-submenu">
                <span class="flex items-center">
                    <i class="fas fa-user-cog"></i>
                    <span class="ml-2">Data Detail</span>
                </span>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </button>
            <ul id="detail-submenu" class="submenu pl-6 mt-1 space-y-1" style="display: none;">
                <li><a href="{{ route('detailsiswa.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600"><i class="fas fa-user-cog w-5"></i><span class="ml-2">Detail Siswa</span></a></li>
            </ul>
        </li>
    </ul>

    <!-- PENGATURAN -->
    <h2 class="px-4 mt-4 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">PENGATURAN</h2>
    <ul>
        <li>
            <a href="#" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4">
                <i class="fas fa-user-circle"></i>
                <span class="ml-2">Profil</span>
            </a>
        </li>
        <li>
            <a href="#" class="flex items-center text-gray-700 hover:text-indigo-600 py-2 px-4">
                <i class="fas fa-sign-out-alt"></i>
                <span class="ml-2">Logout</span>
            </a>
        </li>
    </ul>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Menyiapkan menu dropdown
        const setupDropdowns = () => {
            // Simpan status menu di localStorage
            const saveMenuState = () => {
                const openMenus = {};
                document.querySelectorAll('.submenu').forEach(menu => {
                    const id = menu.id;
                    const isOpen = menu.style.display !== 'none';
                    openMenus[id] = isOpen;
                });
                localStorage.setItem('openSidebarMenus', JSON.stringify(openMenus));
            };

            // Ambil status menu dari localStorage
            const restoreMenuState = () => {
                try {
                    const openMenus = JSON.parse(localStorage.getItem('openSidebarMenus'));
                    if (openMenus) {
                        Object.entries(openMenus).forEach(([id, isOpen]) => {
                            const menu = document.getElementById(id);
                            if (menu && isOpen) {
                                menu.style.display = 'block';
                                const toggleBtn = document.querySelector(`[data-target="${id}"]`);
                                if (toggleBtn) {
                                    toggleBtn.querySelector('.toggle-icon').classList.remove('fa-chevron-down');
                                    toggleBtn.querySelector('.toggle-icon').classList.add('fa-chevron-up');
                                }
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error restoring menu state:', e);
                }
            };

            // Setup toggle buttons
            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetMenu = document.getElementById(targetId);
                    const icon = this.querySelector('.toggle-icon');
                    
                    if (targetMenu.style.display === 'none') {
                        targetMenu.style.display = 'block';
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    } else {
                        targetMenu.style.display = 'none';
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                    
                    // Simpan status menu
                    saveMenuState();
                });
            });

            // Tambahkan event listener pada link di submenu
            document.querySelectorAll('.submenu a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Cegah event propagasi
                    e.stopPropagation();
                    // Simpan status menu
                    saveMenuState();
                });
            });

            // Kembalikan status menu
            restoreMenuState();
        };

        // Jalankan setup
        setupDropdowns();
    });
    </script>
</div>