<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIHATI</title>
    <!-- Optimized CSS loading -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .illustration-container {
            background: linear-gradient(135deg, #EBF4FF 0%, #E0E7FF 50%, #F3E8FF 100%);
        }
        
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-element:nth-child(2) { animation-delay: 1s; }
        .floating-element:nth-child(3) { animation-delay: 2s; }
        .floating-element:nth-child(4) { animation-delay: 3s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-10px) rotate(1deg); }
            50% { transform: translateY(-5px) rotate(-1deg); }
            75% { transform: translateY(-15px) rotate(0.5deg); }
        }
        
        .pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
        
        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.02); opacity: 1; }
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .input-field {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .input-field:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        
        .medical-illustration {
            position: relative;
            transform-style: preserve-3d;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-image {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 12px;
            background: white;
            padding: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .brand-text {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Left Side - 3D Health Illustration -->
        <div class="hidden lg:flex lg:w-1/2 illustration-container relative overflow-hidden">
            
            <!-- Floating decorative elements -->
            <div class="floating-element absolute top-16 left-16 opacity-30">
                <div class="w-8 h-8 bg-blue-400 rounded-full"></div>
            </div>
            <div class="floating-element absolute top-24 right-20 opacity-20">
                <div class="w-6 h-6 bg-purple-400 rounded-full"></div>
            </div>
            <div class="floating-element absolute bottom-32 left-20 opacity-25">
                <div class="w-10 h-10 bg-indigo-400 rounded-full"></div>
            </div>
            <div class="floating-element absolute bottom-20 right-16 opacity-30">
                <div class="w-4 h-4 bg-pink-400 rounded-full"></div>
            </div>
            
            <!-- Main Content Container -->
            <div class="flex flex-col justify-center items-center w-full px-8 py-8 relative z-10 min-h-screen">
                
                <!-- Header Text dengan Logo Sekolah -->
                <div class="text-center mb-8 max-w-lg">
                    <!-- Logo SLB Bantul - DIPERBESAR -->
                    <div class="flex justify-center mb-6">
                        @php
                            $logoPath = public_path('images/logosekolah.png');
                            $logoExists = file_exists($logoPath);
                        @endphp
                        
                        @if($logoExists)
                            <div class="w-56 h-56 bg-white rounded-full p-3 shadow-2xl flex items-center justify-center">
                                <img src="{{ asset('images/logosekolah.png') }}" alt="Logo SLB N 1 Bantul" class="w-48 h-48 object-contain">
                            </div>
                        @else
                            <!-- Fallback logo -->
                            <div class="w-56 h-56 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center shadow-2xl">
                                <span class="text-white font-bold text-2xl text-center leading-tight">SLB<br>N1<br>BANTUL</span>
                            </div>
                        @endif
                    </div>
                    
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">
                        Pantau kesehatan <span class="text-blue-600">Siswa</span> dan <span class="text-purple-600">Guru</span> secara digital!
                    </h2>
                    <p class="text-base lg:text-lg text-gray-600">SLB Negeri 1 Bantul - Unit Kesehatan Sekolah</p>
                </div>
                
                <!-- Simple Illustration -->
                <div class="medical-illustration relative mt-8">
                    <!-- Background elements -->
                    <div class="absolute -top-6 -left-6 w-40 h-40 bg-gradient-to-br from-blue-200 to-purple-200 rounded-full opacity-30 pulse-slow"></div>
                    
                    <!-- Health/Medical icon - DIPERBESAR -->
                    <div class="relative z-10 w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-2xl mx-auto">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 8h-2v3h-3v2h3v3h2v-3h3v-2h-3V8zM4 6h5v2h2V6h1V4H4v2zm0 5v2h8v-2H4zm0 5v2h8v-2H4z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 lg:p-8">
            <div class="form-container w-full max-w-lg p-6 lg:p-8 rounded-2xl shadow-xl">
                
                <!-- Brand Header dengan Logo Sekolah -->
                <div class="flex flex-col items-center justify-center mb-8">
                    <!-- Logo Sekolah - DIPERBESAR -->
                    <div class="logo-container mb-4">
                        @php
                            $logoPath = public_path('images/logosekolah.png');
                            $logoExists = file_exists($logoPath);
                        @endphp
                        
                        @if($logoExists)
                            <img src="{{ asset('images/logosekolah.png') }}" alt="Logo SLB N 1 Bantul" class="w-16 h-16 object-contain bg-white rounded-2xl p-2 shadow-lg border border-gray-100">
                        @else
                            <!-- Fallback logo dengan gradient -->
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Brand Text -->
                    <div class="text-center">
                        <h1 class="text-2xl font-bold brand-text mb-1">SIHATI</h1>
                        <p class="text-sm text-gray-600">SLB Negeri 1 Bantul</p>
                        <p class="text-xs text-gray-500">Sistem Informasi Kesehatan Siswa</p>
                    </div>
                </div>
                
                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Hai, selamat datang kembali!</h2>
                    <p class="text-gray-600 text-sm">Masuk ke <span class="text-blue-600 font-medium">Sistem Informasi</span> <span class="text-purple-600 font-medium">Kesehatan Siswa</span></p>
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->has('login'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="{{ old('username') }}"
                            class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('username') border-red-500 @enderror"
                            placeholder="Masukkan username"
                            required
                        >
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field with Eye Icon -->
                    <div x-data="{ show: false }">
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                        </div>
                        <div class="relative">
                            <input 
                                :type="show ? 'text' : 'password'" 
                                id="password" 
                                name="password" 
                                class="input-field w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password"
                                required
                            >
                            <button type="button" @click="show = !show" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit" 
                        class="btn-primary w-full text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Masuk
                    </button>
                </form>

                <!-- Remember me checkbox -->
                <div class="flex items-center mt-6">
                    <input id="remember" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ingat perangkat ini</label>
                </div>

                <!-- Login Guide -->
                <div class="pt-6 border-t border-gray-200">
                    <details class="cursor-pointer">
                        <summary class="text-sm font-medium text-gray-700 hover:text-gray-900">Panduan Login</summary>
                        <div class="mt-3 space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span class="font-medium">Admin UKS:</span>
                                <span>Username: NIP</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Petugas UKS:</span>
                                <span>Username: NIP</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Dokter:</span>
                                <span>Username: ID Dokter</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Orang Tua:</span>
                                <span class="text-xs">Username: ID Siswa, Password: Tanggal Lahir (ddmmyy)</span>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</body>
</html>