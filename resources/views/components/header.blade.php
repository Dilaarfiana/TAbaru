<!-- resources/views/components/header.blade.php -->
<header class="bg-white border-b border-gray-200 shadow-sm">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <button id="mobile-menu-button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-600 md:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Role badge dihilangkan -->
                @php
                    $userType = session('user_type', 'admin');
                    $roleName = 'Administrator';
                    $roleColor = 'text-gray-600';
                    $roleIcon = 'fa-user-shield';
                    
                    if ($userType == 'petugas_uks') {
                        $roleName = 'Petugas UKS';
                        $roleColor = 'text-green-600';
                        $roleIcon = 'fa-clinic-medical';
                    } elseif ($userType == 'dokter') {
                        $roleName = 'Dokter';
                        $roleColor = 'text-blue-600';
                        $roleIcon = 'fa-user-md';
                    } elseif ($userType == 'orang_tua') {
                        $roleName = 'Orang Tua';
                        $roleColor = 'text-purple-600';
                        $roleIcon = 'fa-users';
                    }
                @endphp
                
                <!-- Bagian role badge ini dihilangkan
                <div class="hidden md:flex items-center ml-3">
                    <span class="text-sm font-medium {{ $roleColor }}">
                        <i class="fas {{ $roleIcon }} mr-1"></i> {{ $roleName }}
                    </span>
                </div>
                -->
            </div>
            
            <div class="flex items-center">
                <button class="p-2 text-gray-500 hover:text-gray-600 mr-3 relative">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                </button>
                
                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <div>
                        <button @click="open = !open" class="flex items-center focus:outline-none">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center">
                                @php
                                    // Ambil inisial dari nama pengguna yang login
                                    $userName = session('user_name', 'User');
                                    $initials = '';
                                    $nameParts = explode(' ', $userName);
                                    foreach($nameParts as $part) {
                                        if(!empty($part)) {
                                            $initials .= substr($part, 0, 1);
                                            if(strlen($initials) >= 2) break;
                                        }
                                    }
                                    echo strtoupper($initials);
                                @endphp
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ session('user_name', 'User') }}</span>
                            <i class="fas fa-chevron-down text-xs ml-1"></i>
                        </button>
                    </div>
                    
                    <!-- Simpel dropdown yang menarik -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="origin-top-right absolute right-0 mt-2 w-64 rounded-lg bg-white shadow-lg z-50 divide-y divide-gray-100">
                        
                        <!-- Header dengan foto/inisial user -->
                        <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-lg">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center shadow">
                                    @if(isset($userPhoto) && !empty($userPhoto))
                                        <img src="{{ $userPhoto }}" alt="{{ session('user_name', 'User') }}" class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <span class="text-base font-medium">
                                            {{ strtoupper($initials) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ session('user_name', 'User') }}</p>
                                    <p class="text-xs text-gray-600">
                                        <i class="fas {{ $roleIcon }} mr-1"></i> {{ $roleName }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Salam dinamis berdasarkan waktu -->
                            @php
                                date_default_timezone_set('Asia/Jakarta'); // Atur zona waktu Indonesia
                                $hour = date('H');
                                $greeting = '';
                                $greetingIcon = '';
                                
                                if($hour >= 5 && $hour < 12) {
                                    $greeting = 'Selamat pagi!';
                                    $greetingIcon = 'fa-sun';
                                } elseif($hour >= 12 && $hour < 17) {
                                    $greeting = 'Selamat siang!';
                                    $greetingIcon = 'fa-sun';
                                } elseif($hour >= 17 && $hour < 20) {
                                    $greeting = 'Selamat sore!';
                                    $greetingIcon = 'fa-cloud-sun';
                                } else {
                                    $greeting = 'Selamat malam!';
                                    $greetingIcon = 'fa-moon';
                                }
                            @endphp
                            
                            <div class="mt-2 text-sm flex items-center bg-white/80 py-1.5 px-2 rounded">
                                <i class="fas {{ $greetingIcon }} text-yellow-500 mr-2"></i>
                                {{ $greeting }}
                            </div>
                        </div>
                        
                        <!-- Menu items -->
                        <div class="py-1">
                            <a href="{{ url('profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user-circle text-blue-500 mr-3 w-5 text-center"></i> Profil Saya
                            </a>
                            <a href="{{ url('change-password') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-key text-blue-500 mr-3 w-5 text-center"></i> Ganti Password
                            </a>
                            <a href="{{ url('logout') }}" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt text-red-500 mr-3 w-5 text-center"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>