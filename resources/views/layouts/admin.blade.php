<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sehati - Admin Dashboard</title>
    
    <!-- Gunakan CDN untuk memastikan CSS dimuat -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    
    <style>
        /* Pastikan style ini selalu dimuat */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        #sidebar {
            width: 250px;
            transition: all 0.3s;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }
        
        #main-content {
            margin-left: 250px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                left: -250px;
            }
            
            #sidebar.active {
                left: 0;
            }
            
            #main-content {
                margin-left: 0;
                width: 100%;
            }
        }
        
        /* Styling untuk menu sidebar */
        .sidebar-menu a {
            padding: 10px 15px;
            display: block;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .sidebar-menu i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        /* Override Tailwind untuk tabel */
        .table-auto {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-auto th {
            text-align: left;
            padding: 12px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table-auto td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        /* Dropdown animation */
        .dropdown-animation {
            transition: all 0.2s ease-out;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div id="sidebar" class="bg-white shadow-md">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-heartbeat text-xl mr-2 text-blue-600"></i>
                <h1 class="text-xl font-bold">Sehati</h1>
            </div>
            <button id="sidebar-toggle" class="text-gray-500 hover:bg-gray-100 p-2 rounded-full md:hidden">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="overflow-y-auto h-full pb-20">
            @include('components.sidebar')
        </div>
    </div>
    
    <!-- Main Content -->
    <div id="main-content" class="min-h-screen pb-10">
    
        <!-- Topbar -->
        @include('components.header')
        
        <!-- Page Content -->
        <main class="py-6 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    <!-- Scripts -->
    <script>
        // Mobile menu button functionality
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            
            if (window.innerWidth <= 768 && sidebar.classList.contains('active') && 
                !sidebar.contains(event.target) && event.target !== mobileMenuButton) {
                sidebar.classList.remove('active');
            }
        });
        
        // Resize handler
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                mainContent.style.marginLeft = '250px';
            } else {
                mainContent.style.marginLeft = '0';
            }
        });

        // Display current time
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const timeString = `${hours}:${minutes}:${seconds}`;
            
            // Update the time element if it exists
            const timeElements = document.querySelectorAll('.time-display');
            timeElements.forEach(element => {
                element.textContent = timeString;
            });
        }
        
        // Update time every second
        setInterval(updateTime, 1000);
        updateTime(); // Initial update
    </script>
    
    @stack('scripts')
</body>
</html>