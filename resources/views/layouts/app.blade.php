<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sehati - @yield('page_title', 'Sistem Kesehatan Terpadu')</title>
    
    <!-- Optimized CSS loading -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <style>
        /* Base styles dengan ukuran font yang lebih besar */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Typography improvements */
        h1 { font-size: 2.25rem; font-weight: 700; line-height: 1.2; }
        h2 { font-size: 1.875rem; font-weight: 600; line-height: 1.3; }
        h3 { font-size: 1.5rem; font-weight: 600; line-height: 1.3; }
        h4 { font-size: 1.25rem; font-weight: 600; line-height: 1.4; }
        h5 { font-size: 1.125rem; font-weight: 600; line-height: 1.4; }
        h6 { font-size: 1rem; font-weight: 600; line-height: 1.4; }

        p, div, span { font-size: 1rem; line-height: 1.6; }
        
        /* Improved text sizes for common elements */
        .text-xs { font-size: 0.875rem; }
        .text-sm { font-size: 1rem; }
        .text-base { font-size: 1.125rem; }
        .text-lg { font-size: 1.25rem; }
        .text-xl { font-size: 1.375rem; }
        
        /* Table typography improvements */
        table {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        table th {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 1rem 0.75rem;
        }
        
        table td {
            font-size: 1rem;
            padding: 0.875rem 0.75rem;
            line-height: 1.5;
        }

        /* Form elements typography */
        input, textarea, select, button {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        label {
            font-size: 1rem;
            font-weight: 500;
            line-height: 1.4;
        }
        
        /* Button improvements */
        .btn {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            line-height: 1.4;
            font-weight: 500;
        }
        
        .btn-sm {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        
        .btn-lg {
            font-size: 1.125rem;
            padding: 1rem 2rem;
        }
        
        /* Sidebar dengan fixed header dan scrollable content */
        #sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            will-change: transform;
            transition: transform 0.2s ease;
        }
        
        /* Fixed header di sidebar */
        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            background-color: white;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sidebar-header span {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        /* Scrollable content pada sidebar */
        .sidebar-content {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            height: calc(100vh - 64px);
        }
        
        /* Styling scrollbar */
        .sidebar-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .sidebar-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .sidebar-content::-webkit-scrollbar-thumb {
            background: #c5c5c5;
            border-radius: 4px;
        }
        
        /* Main content positioning with sticky header and standard footer */
        #main-content {
            margin-left: 250px;
            transition: margin-left 0.2s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 64px;
        }
        
        /* Standard Footer - Not sticky */
        .main-footer {
            background-color: white;
            border-top: 1px solid #e5e7eb;
            margin-top: auto;
            font-size: 0.95rem;
        }
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }
            
            #sidebar.active {
                transform: translateX(0);
            }
            
            #main-content {
                margin-left: 0;
                width: 100%;
            }
            
            /* Mobile font adjustments */
            body { font-size: 15px; }
            h1 { font-size: 1.875rem; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.25rem; }
            
            table th { font-size: 1rem; padding: 0.75rem 0.5rem; }
            table td { font-size: 0.95rem; padding: 0.75rem 0.5rem; }
        }
        
        /* Improved sidebar menu with better typography */
        .sidebar-menu a, .sidebar-menu button {
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            transition: background-color 0.15s ease;
            color: #4B5563;
            font-size: 1rem;
            line-height: 1.4;
            font-weight: 500;
        }
        
        .sidebar-menu a:hover, .sidebar-menu button:hover {
            background-color: #F3F4F6;
            color: #3B82F6;
        }
        
        .sidebar-menu i {
            width: 1.5rem;
            text-align: center;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        /* Mobile footer */
        .mobile-footer {
            background: white;
            display: none;
            border-top: 1px solid #E5E7EB;
        }
        
        @media (max-width: 768px) {
            .mobile-footer {
                display: flex;
                margin-top: auto;
            }
            
            .desktop-footer {
                display: none;
            }
        }
        
        /* Fixed Submenu Styling */
        .submenu {
            display: none;
            transition: all 0.3s ease;
        }
        
        .submenu.open {
            display: block;
        }
        
        .submenu a {
            font-size: 0.95rem;
            padding-left: 1rem;
        }
        
        /* Active state for menu items */
        .submenu a.active {
            background-color: #EEF2FF;
            color: #4F46E5;
            font-weight: 600;
        }
        
        .toggle-btn.active {
            background-color: #EEF2FF;
            color: #4F46E5;
            font-weight: 600;
        }

        /* Card and content improvements */
        .card {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .card-text {
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Alert improvements */
        .alert {
            font-size: 1rem;
            line-height: 1.5;
            padding: 1rem;
        }
        
        .alert h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Badge and status improvements */
        .badge {
            font-size: 0.9rem;
            padding: 0.375rem 0.75rem;
            font-weight: 500;
        }

        /* Simple loading spinner */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Simple logout loading state */
        .logout-loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* Dashboard specific improvements */
        .dashboard-card {
            padding: 1.5rem;
        }
        
        .dashboard-card h3 {
            font-size: 1.375rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .dashboard-stat {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .dashboard-label {
            font-size: 1rem;
            color: #6B7280;
            font-weight: 500;
        }

        /* Navigation breadcrumb improvements */
        .breadcrumb {
            font-size: 1rem;
            color: #6B7280;
        }
        
        .breadcrumb a {
            color: #3B82F6;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Page title improvements */
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 1rem;
        }
        
        .page-subtitle {
            font-size: 1.125rem;
            color: #6B7280;
            margin-bottom: 1.5rem;
        }

        /* Modal improvements */
        .modal-overlay {
            backdrop-filter: blur(4px);
        }
        
        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .modal-footer {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
        }

        /* Info cards styling */
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .info-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .info-card-icon {
            width: 3rem;
            height: 3rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .info-card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
        }
        
        .info-label {
            font-size: 0.875rem;
            opacity: 0.8;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Vital signs cards */
        .vital-signs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .vital-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .vital-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .vital-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .vital-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .vital-label {
            font-size: 0.875rem;
            color: #6B7280;
            font-weight: 500;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-badge.success {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-badge.warning {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-badge.danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .status-badge.info {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        /* Print styles */
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .modal-content { 
                box-shadow: none;
                border: 1px solid #000;
            }
            .info-card {
                background: #f8f9fa !important;
                color: #000 !important;
                border: 1px solid #000;
            }
            .vital-card {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar dengan fixed header dan scrollable content -->
    <div id="sidebar">
        <!-- Fixed Header -->
        <div class="sidebar-header">
            <div class="flex items-center">
                <img src="{{ asset('images/logosekolah.png') }}" alt="Logo Sekolah" class="w-9 h-10 mr-2">
                <span class="font-bold text-lg">Sihati</span>
            </div>
            <button id="sidebar-close" class="text-gray-500 p-2 rounded-full md:hidden">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Scrollable Content -->
        <div class="sidebar-content">
            <div class="sidebar-menu py-2">
                @include('components.sidebar')
            </div>
        </div>
    </div>
    
    <!-- Lightweight Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-50 hidden" style="transition: opacity 0.2s ease;"></div>
    
    <!-- Main Content -->
    <div id="main-content">
        <!-- Include Header Component -->
        @include('components.header')
        
        <!-- Page Content -->
        <main class="py-6 px-4 flex-grow">
            <!-- Success Alert -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded alert" id="success-alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-medium">{{ session('success') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="document.getElementById('success-alert').remove()" class="text-green-700 hover:text-green-900">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Error Alert -->
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded alert" id="error-alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-medium">{{ session('error') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="document.getElementById('error-alert').remove()" class="text-red-700 hover:text-red-900">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded alert" id="validation-errors">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold text-base">Terdapat kesalahan:</h4>
                            <ul class="mt-2 list-disc list-inside text-base">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="document.getElementById('validation-errors').remove()" class="text-red-700 hover:text-red-900">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
            @yield('content')
        </main>
        
        <!-- Standard Footer - Desktop (not sticky) -->
        <footer class="main-footer desktop-footer py-4 px-6 hidden md:block">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('images/logosekolah.png') }}" alt="Logo Sekolah" class="w-6 h-6 mr-2">
                    <span class="font-semibold text-base">Sehati</span>
                    <span class="text-sm text-gray-500 ml-4">&copy; {{ date('Y') }} Sistem Kesehatan Terpadu</span>
                </div>
                
                <div class="text-sm text-gray-600 text-right">
                    <div class="font-medium">SLBN 1 Bantul</div>
                    <div>Jl. Wates KM 3 No. 147, Ngestiharjo, Kasihan, Bantul, Yogyakarta</div>
                    <div>Telp: (0274) 371243 | Email: slbn1bantul@gmail.com</div>
                </div>
            </div>
        </footer>
        
        <!-- Mobile Footer (not sticky) -->
        <div class="mobile-footer md:hidden py-3">
            <a href="{{ route('dashboard') }}" class="py-3 flex-1 text-center">
                <i class="fas fa-home text-blue-500 text-xl"></i>
            </a>
            <a href="#" class="py-3 flex-1 text-center">
                <i class="fas fa-school text-blue-500 text-xl"></i>
            </a>
            <a href="tel:(0274)371243" class="py-3 flex-1 text-center">
                <i class="fas fa-phone text-blue-500 text-xl"></i>
            </a>
            <a href="mailto:slbn1bantul@gmail.com" class="py-3 flex-1 text-center">
                <i class="fas fa-envelope text-blue-500 text-xl"></i>
            </a>
        </div>
    </div>

    <!-- Hidden logout form -->
    <form method="POST" action="{{ route('logout') }}" style="display: none;" id="logout-form">
        @csrf
    </form>
    
    <!-- Optimized Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cache DOM elements
            const sidebarToggle = document.getElementById('mobile-menu-button');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const mainContent = document.getElementById('main-content');
            
            // Toggle sidebar - improved performance
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    requestAnimationFrame(() => {
                        sidebar.classList.toggle('active');
                        sidebarOverlay.classList.toggle('hidden');
                        document.body.classList.toggle('overflow-hidden');
                    });
                });
            }
            
            // Close sidebar
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }
            
            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }
            
            // ================ SIDEBAR MENU FUNCTIONALITY ================
            
            // Function to save menu state
            function saveMenuState(menuIds) {
                localStorage.setItem('openMenus', JSON.stringify(menuIds));
            }
            
            // Function to get saved menu state
            function getSavedMenuState() {
                const saved = localStorage.getItem('openMenus');
                return saved ? JSON.parse(saved) : [];
            }
            
            // Open previously saved menus or detect from URL path
            function initializeMenuState() {
                // Try to detect active menu from current URL path
                const path = window.location.pathname;
                let activeMenuDetected = false;
                
                // Define path patterns for different menus
                const pathPatterns = {
                    'master-submenu': ['/siswa', '/orangtua', '/dokter', '/petugasuks', '/jurusan', '/kelas'],
                    'alokasi-submenu': ['/alokasi'],
                    'pemeriksaan-submenu': ['/pemeriksaan_awal', '/pemeriksaan_fisik'],
                    'medis-submenu': ['/rekam_medis'],
                    'detail-submenu': ['/detailsiswa']
                };
                
                // Check if current path matches any pattern
                const openMenus = [];
                for (const [menuId, patterns] of Object.entries(pathPatterns)) {
                    if (patterns.some(pattern => path.includes(pattern))) {
                        openMenus.push(menuId);
                        activeMenuDetected = true;
                    }
                }
                
                // If no match detected, use saved state
                if (!activeMenuDetected) {
                    const savedMenus = getSavedMenuState();
                    if (savedMenus && savedMenus.length > 0) {
                        savedMenus.forEach(menuId => {
                            if (!openMenus.includes(menuId)) {
                                openMenus.push(menuId);
                            }
                        });
                    }
                }
                
                // Open all detected/saved menus
                openMenus.forEach(menuId => {
                    const menu = document.getElementById(menuId);
                    const toggleBtn = document.querySelector(`[data-target="${menuId}"]`);
                    
                    if (menu && toggleBtn) {
                        menu.style.display = 'block';
                        menu.classList.add('open');
                        
                        const icon = toggleBtn.querySelector('.toggle-icon');
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-up');
                        }
                    }
                });
                
                // Save current state
                saveMenuState(openMenus);
            }
            
            // Initialize the menu state
            initializeMenuState();
            
            // Handle toggle button clicks with fixed behavior
            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const targetId = this.getAttribute('data-target');
                    const target = document.getElementById(targetId);
                    const icon = this.querySelector('.toggle-icon');
                    
                    // Toggle submenu
                    if (target.classList.contains('open')) {
                        // Close menu
                        target.classList.remove('open');
                        target.style.display = 'none';
                        
                        if (icon) {
                            icon.classList.remove('fa-chevron-up');
                            icon.classList.add('fa-chevron-down');
                        }
                        
                        // Update saved state (remove this menu)
                        const currentMenus = getSavedMenuState();
                        const updatedMenus = currentMenus.filter(id => id !== targetId);
                        saveMenuState(updatedMenus);
                    } else {
                        // Open menu
                        target.classList.add('open');
                        target.style.display = 'block';
                        
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-up');
                        }
                        
                        // Update saved state (add this menu)
                        const currentMenus = getSavedMenuState();
                        if (!currentMenus.includes(targetId)) {
                            currentMenus.push(targetId);
                            saveMenuState(currentMenus);
                        }
                    }
                });
            });
            
            // Make sure submenu links don't close parent menu
            document.querySelectorAll('.submenu a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Stop event propagation to prevent parent toggle
                    e.stopPropagation();
                    
                    // Find parent submenu and save its state
                    const parentSubmenu = this.closest('.submenu');
                    if (parentSubmenu) {
                        const parentId = parentSubmenu.id;
                        const currentMenus = getSavedMenuState();
                        
                        if (!currentMenus.includes(parentId)) {
                            currentMenus.push(parentId);
                            saveMenuState(currentMenus);
                        }
                    }
                });
            });
        });

        // ================ SESSION PROTECTION ================
        
        // Detect back button usage after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Check if page was restored from cache
                // This happens when user presses back button
                window.location.reload();
            }
        });

        // Prevent back button after logout by modifying history
        function preventBackButton() {
            history.pushState(null, null, location.href);
            window.onpopstate = function() {
                history.go(1);
            };
        }

        // Call on page load
        document.addEventListener('DOMContentLoaded', preventBackButton);

        // ================ ALERT AUTO HIDE ================
        
        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = ['success-alert', 'error-alert', 'validation-errors'];
            
            alerts.forEach(alertId => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    setTimeout(() => {
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    }, 5000);
                }
            });
        });

        // ================ UTILITY FUNCTIONS ================
        
        // Format numbers with Indonesian locale
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
        
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
        
        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(date);
        }
        
        // Format datetime
        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        }
        
        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show toast notification
                showToast('Berhasil disalin ke clipboard', 'success');
            }).catch(err => {
                console.error('Failed to copy: ', err);
                showToast('Gagal menyalin ke clipboard', 'error');
            });
        }
        
        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg text-white transition-all duration-300 transform translate-x-full`;
            
            // Set background color based on type
            switch(type) {
                case 'success':
                    toast.classList.add('bg-green-500');
                    break;
                case 'error':
                    toast.classList.add('bg-red-500');
                    break;
                case 'warning':
                    toast.classList.add('bg-yellow-500');
                    break;
                default:
                    toast.classList.add('bg-blue-500');
            }
            
            toast.innerHTML = `
                <div class="flex items-center text-base">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, 3000);
        }
        
        // Confirm dialog with custom styling
        function confirmDialog(message, callback) {
            const result = confirm(message);
            if (result && typeof callback === 'function') {
                callback();
            }
            return result;
        }
        
        // Simple loading overlay
        function showLoading(message = 'Loading...') {
            const overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            overlay.innerHTML = `
                <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                    <i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>
                    <span class="text-gray-700 text-base">${message}</span>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        
        function hideLoading() {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                overlay.remove();
            }
        }

        // Calculate BMI
        function calculateBMI(weight, height) {
            if (!weight || !height || weight <= 0 || height <= 0) return null;
            const bmi = weight / Math.pow(height / 100, 2);
            return Math.round(bmi * 10) / 10;
        }

        // Get BMI status
        function getBMIStatus(bmi) {
            if (!bmi) return 'Data tidak lengkap';
            if (bmi < 18.5) return 'Underweight';
            if (bmi < 25) return 'Normal';
            if (bmi < 30) return 'Overweight';
            return 'Obese';
        }

        // Format Indonesian date
        function formatIndonesianDate(date) {
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            const d = new Date(date);
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }

        // Format Indonesian datetime
        function formatIndonesianDateTime(date) {
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            const d = new Date(date);
            const hours = String(d.getHours()).padStart(2, '0');
            const minutes = String(d.getMinutes()).padStart(2, '0');
            
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${hours}:${minutes} WIB`;
        }
    </script>
    
    <!-- Stack untuk scripts dari halaman lain -->
    @stack('scripts')
</body>
</html>