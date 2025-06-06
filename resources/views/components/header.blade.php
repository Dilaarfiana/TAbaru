<!-- resources/views/components/header.blade.php -->
<header class="bg-white border-b border-gray-200 shadow-sm fixed top-0 right-0 z-40 md:left-250 left-0 transition-all duration-300">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <button id="mobile-menu-button" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-600 md:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                
                @php
                    $userLevel = session('user_level', 'admin');
                    $roleName = 'Administrator';
                    $roleColor = 'text-gray-600';
                    $roleIcon = 'fa-user-shield';
                    
                    if ($userLevel == 'petugas') {
                        $roleName = 'Petugas UKS';
                        $roleColor = 'text-green-600';
                        $roleIcon = 'fa-clinic-medical';
                    } elseif ($userLevel == 'dokter') {
                        $roleName = 'Dokter';
                        $roleColor = 'text-blue-600';
                        $roleIcon = 'fa-user-md';
                    } elseif ($userLevel == 'orang_tua') {
                        $roleName = 'Orang Tua';
                        $roleColor = 'text-purple-600';
                        $roleIcon = 'fa-users';
                    }
                @endphp

                <!-- Role Badge (hidden on mobile) -->
                <div class="hidden md:flex items-center px-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColor }} bg-gray-100">
                        <i class="fas {{ $roleIcon }} mr-1"></i>
                        {{ $roleName }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <!-- Notifications Bell (Only for Orang Tua) -->
                @if($userLevel === 'orang_tua')
                <div class="relative" x-data="{ 
                    open: false, 
                    notifications: [], 
                    unreadCount: 0,
                    loading: false,
                    error: null,
                    
                    async fetchNotifications() {
                        this.loading = true;
                        this.error = null;
                        
                        try {
                            const response = await fetch('/api/notifications');
                            const data = await response.json();
                            
                            if (data.success) {
                                this.notifications = data.data;
                                this.unreadCount = data.unread_count;
                            } else {
                                console.error('API Error:', data.message);
                                this.notifications = [];
                                this.unreadCount = 0;
                                this.error = data.message || 'Gagal memuat notifikasi';
                            }
                        } catch (error) {
                            console.error('Fetch error:', error);
                            this.notifications = [];
                            this.unreadCount = 0;
                            this.error = 'Koneksi bermasalah';
                        } finally {
                            this.loading = false;
                        }
                    },
                    
                    async markAsRead(notificationId) {
                        try {
                            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                }
                            });
                            
                            if (response.ok) {
                                this.fetchNotifications();
                            }
                        } catch (error) {
                            console.error('Failed to mark notification as read:', error);
                        }
                    },
                    
                    async markAllAsRead() {
                        try {
                            const response = await fetch('/api/notifications/mark-all-read', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                }
                            });
                            
                            if (response.ok) {
                                this.fetchNotifications();
                            }
                        } catch (error) {
                            console.error('Failed to mark all as read:', error);
                        }
                    }
                }" 
                x-init="
                    fetchNotifications();
                    setInterval(() => fetchNotifications(), 30000);
                ">
                    <!-- Bell Button -->
                    <button @click="open = !open; if(open) fetchNotifications()" 
                            class="relative p-2 text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md transition-colors duration-200" 
                            title="Notifikasi">
                        <i class="fas fa-bell text-lg" :class="error ? 'text-red-500' : ''"></i>
                        
                        <!-- Notification Badge (No Animation) -->
                        <span x-show="unreadCount > 0" 
                              x-text="unreadCount > 99 ? '99+' : unreadCount"
                              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">
                        </span>
                        
                        <!-- Error Indicator -->
                        <span x-show="error && !unreadCount" 
                              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-3 w-3">
                        </span>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <div x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="origin-top-right absolute right-0 mt-2 w-96 rounded-lg bg-white shadow-xl z-50 border border-gray-200 max-h-96 overflow-hidden">
                        
                        <!-- Header -->
                        <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-bell text-blue-600"></i>
                                <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span x-show="unreadCount > 0" 
                                      x-text="`${unreadCount} belum dibaca`"
                                      class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                </span>
                                <button @click="markAllAsRead()" 
                                        x-show="unreadCount > 0"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:bg-blue-50 px-2 py-1 rounded">
                                    <i class="fas fa-check-double mr-1"></i>
                                    Tandai Semua
                                </button>
                            </div>
                        </div>
                        
                        <!-- Notifications List -->
                        <div class="max-h-80 overflow-y-auto">
                            <!-- Loading State -->
                            <div x-show="loading" class="p-6 text-center">
                                <i class="fas fa-spinner fa-spin text-blue-500 mr-2"></i>
                                <span class="text-sm text-gray-500">Memuat notifikasi...</span>
                            </div>
                            
                            <!-- Error State -->
                            <div x-show="!loading && error" class="p-6 text-center">
                                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Terjadi Masalah</h4>
                                <p class="text-xs text-gray-500 mb-3" x-text="error"></p>
                                <button @click="fetchNotifications()" 
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium bg-blue-50 px-3 py-1 rounded">
                                    <i class="fas fa-refresh mr-1"></i>
                                    Coba Lagi
                                </button>
                            </div>
                            
                            <!-- No Notifications -->
                            <div x-show="!loading && !error && notifications.length === 0" class="p-8 text-center">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
                                </div>
                                <h4 class="text-sm font-medium text-gray-900 mb-1">Tidak ada notifikasi</h4>
                                <p class="text-xs text-gray-500">Notifikasi tentang kesehatan anak Anda akan muncul di sini</p>
                            </div>
                            
                            <!-- Notifications -->
                            <template x-for="notification in notifications" :key="notification.id">
                                <div class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 cursor-pointer"
                                     :class="notification.is_read ? 'opacity-75' : 'bg-blue-50 border-l-4 border-l-blue-500'"
                                     @click="if(!notification.is_read) markAsRead(notification.id)">
                                    <div class="p-4 space-y-2">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center space-x-2 flex-1">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                         :class="{
                                                             'bg-blue-100': notification.color === 'text-blue-500',
                                                             'bg-green-100': notification.color === 'text-green-500',
                                                             'bg-red-100': notification.color === 'text-red-500',
                                                             'bg-purple-100': notification.color === 'text-purple-500',
                                                             'bg-orange-100': notification.color === 'text-orange-500'
                                                         }">
                                                        <i :class="notification.icon + ' ' + notification.color + ' text-sm'"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-2">
                                                        <h4 class="text-sm font-medium text-gray-900 truncate" x-text="notification.title"></h4>
                                                        <span x-show="!notification.is_read" 
                                                              class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <button @click.stop="markAsRead(notification.id)" 
                                                    x-show="!notification.is_read"
                                                    class="text-xs text-blue-600 hover:text-blue-800 ml-2 flex-shrink-0 hover:bg-blue-50 px-2 py-1 rounded">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600" x-text="notification.message"></p>
                                        
                                        <div class="flex items-center justify-between text-xs">
                                            <div class="flex items-center space-x-3 text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-user mr-1"></i>
                                                    <span x-text="notification.siswa_nama"></span>
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    <span x-text="notification.time_ago"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Footer -->
                        <div x-show="notifications.length > 0" class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <a href="{{ route('orangtua.notifications.index') }}" 
                               class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium hover:bg-blue-50 py-1 rounded transition-colors duration-200">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Quick Actions Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-600" title="Menu Cepat">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    
                    <div x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg bg-white shadow-lg z-50 border border-gray-200">
                        
                        <div class="py-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-home text-blue-500 mr-3 w-4 text-center"></i> 
                                Dashboard
                            </a>
                            
                            @if($userLevel === 'orang_tua')
                                <a href="{{ route('orangtua.siswa.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-child text-purple-500 mr-3 w-4 text-center"></i> 
                                    Data Siswa Saya
                                </a>
                            @endif
                            
                            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user text-blue-500 mr-3 w-4 text-center"></i> 
                                Profil Saya
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <div>
                        <button @click="open = !open" class="flex items-center focus:outline-none">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center">
                                @php
                                    $userName = session('username', 'User');
                                    $initials = '';
                                    $nameParts = explode(' ', $userName);
                                    foreach($nameParts as $part) {
                                        if(!empty($part)) {
                                            $initials .= substr($part, 0, 1);
                                            if(strlen($initials) >= 2) break;
                                        }
                                    }
                                    echo strtoupper($initials ?: 'U');
                                @endphp
                            </div>
                            <div class="ml-2 hidden sm:block">
                                <span class="text-sm font-medium text-gray-700">{{ session('username', 'User') }}</span>
                                <div class="text-xs text-gray-500 {{ $roleColor }}">{{ $roleName }}</div>
                            </div>
                            <i class="fas fa-chevron-down text-xs ml-2"></i>
                        </button>
                    </div>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="origin-top-right absolute right-0 mt-2 w-64 rounded-lg bg-white shadow-lg z-50 divide-y divide-gray-100 border border-gray-200">
                        
                        <!-- User Info Header -->
                        <div class="px-4 py-3 bg-gray-50">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center font-semibold">
                                    {{ strtoupper($initials ?: 'U') }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ session('username', 'User') }}</p>
                                    <p class="text-xs {{ $roleColor }} flex items-center">
                                        <i class="fas {{ $roleIcon }} mr-1"></i>
                                        {{ $roleName }}
                                    </p>
                                    @if($userLevel === 'orang_tua' && session('siswa_id'))
                                        <p class="text-xs text-gray-500">ID Siswa: {{ session('siswa_id') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="py-1">
                            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user-circle text-blue-500 mr-3 w-5 text-center"></i> 
                                Profil Saya
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-edit text-green-500 mr-3 w-5 text-center"></i> 
                                Edit Profil
                            </a>
                            <a href="{{ route('change.password') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-key text-orange-500 mr-3 w-5 text-center"></i> 
                                Ganti Password
                            </a>
                        </div>
                        
                        <div class="py-1">
                            <button type="button" 
                                    onclick="simpleLogout()"
                                    class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left border-none bg-transparent cursor-pointer"
                                    id="logout-btn">
                                <i class="fas fa-sign-out-alt text-red-500 mr-3 w-5 text-center" id="logout-icon"></i> 
                                <span id="logout-text">Keluar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Hidden logout form -->
<form method="POST" action="{{ route('logout') }}" style="display: none;" id="header-logout-form">
    @csrf
</form>

<script>
    // Simple logout function
    function simpleLogout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            const btn = document.getElementById('logout-btn');
            const icon = document.getElementById('logout-icon');
            const text = document.getElementById('logout-text');
            
            if (btn) btn.classList.add('opacity-50');
            if (icon) icon.className = 'fas fa-spinner fa-spin text-red-500 mr-3 w-5 text-center';
            if (text) text.textContent = 'Keluar...';
            
            setTimeout(() => {
                const logoutForm = document.getElementById('header-logout-form');
                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    window.location.href = '/logout';
                }
            }, 500);
        }
    }
</script>
