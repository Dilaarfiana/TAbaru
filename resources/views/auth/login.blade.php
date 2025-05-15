<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sehati - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .bg-gradient-light {
            background: linear-gradient(135deg, #e6f0fd 0%, #e5eeff 100%);
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .form-input {
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .btn-login {
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .social-btn {
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .highlight-text {
            color: #f97316;
            font-weight: 600;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .input-with-icon {
            padding-left: 45px;
        }
        
        .illustration {
            max-width: 100%;
            height: auto;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .illustration-container {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Illustration and information -->
        <div class="bg-gradient-light w-full lg:w-1/2 p-8 flex flex-col justify-center items-center relative illustration-container">
            <div class="max-w-md mx-auto">
                <div class="mb-8">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-gradient-to-r from-blue-600 to-blue-400 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-heartbeat text-white text-xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-800">Sehati</h1>
                    </div>
                    <p class="mt-2 text-gray-600">Sistem Informasi Kesehatan Siswa</p>
                </div>
                
                <h2 class="text-3xl font-bold mb-4 text-gray-800">Permudah interaksi antar <span class="highlight-text">Petugas</span> dan <span class="highlight-text">Siswa</span> secara online!</h2>
                
                <p class="text-gray-600 mb-8">
                    Platform Sehati membantu dokter, petugas UKS, dan orang tua dalam memantau kesehatan siswa secara real-time dan terintegrasi.
                </p>
                
                <!-- Illustration -->
                <img src="https://cdn.dribbble.com/userupload/7697555/file/original-a918fd287ccd7cad872dda31231d12d3.png?compress=1&resize=752x" alt="Health monitoring illustration" class="illustration mt-4 rounded-lg shadow-lg">
                
                <!-- Decorative elements -->
                <div class="absolute top-0 left-0 w-16 h-16 bg-blue-100 rounded-br-full opacity-70"></div>
                <div class="absolute bottom-0 right-0 w-40 h-40">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#e0e7ff" stroke-width="0.5">
                        <path d="M12 0L12 24M0 12L24 12M4 0L4 24M20 0L20 24M0 4L24 4M0 20L24 20"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="bg-white w-full lg:w-1/2 p-8 flex flex-col justify-center">
            <div class="max-w-md mx-auto w-full">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800">Hai, selamat datang kembali!</h2>
                    <p class="text-gray-500 mt-1">Silakan login untuk melanjutkan</p>
                </div>
                
                <!-- Notifications -->
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3 text-green-500"></i>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-3 mt-1 text-red-500"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <p class="mb-1">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Login Form -->
                <form class="space-y-5" method="POST" action="{{ route('login.process') }}">
                    @csrf
                    
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input id="username" name="username" type="text" required class="form-input input-with-icon appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Masukkan username" value="{{ old('username') }}">
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input id="password" name="password" type="password" required class="form-input input-with-icon appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="Masukkan password">
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                        </div>
                        
                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                                Lupa kata sandi?
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="btn-login group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Masuk
                        </button>
                    </div>
                    
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Atau masuk menggunakan</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <a href="#" class="social-btn w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fab fa-facebook-f text-blue-600"></i>
                        </a>
                        <a href="#" class="social-btn w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fab fa-google text-red-500"></i>
                        </a>
                        <a href="#" class="social-btn w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fab fa-apple text-gray-800"></i>
                        </a>
                    </div>
                </form>
                
                <!-- Footer -->
                <div class="mt-8 text-center text-sm text-gray-500">
                    <p>Dengan melanjutkan, kamu menyetujui <a href="#" class="text-blue-600 hover:underline">Syarat Penggunaan</a> dan <a href="#" class="text-blue-600 hover:underline">Kebijakan Privasi</a> kami.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>