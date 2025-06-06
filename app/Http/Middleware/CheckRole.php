<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Log untuk debugging
        Log::info('CheckRole Middleware', [
            'required_role' => $role,
            'user_level' => session('user_level'),
            'user_id' => session('user_id'),
            'has_session' => session()->has('user_id'),
            'url' => $request->url()
        ]);
        
        // Cek apakah user sudah login
        if (!session()->has('user_id')) {
            Log::warning('User not logged in, redirecting to login');
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $userLevel = session('user_level');
        
        // Cek role
        if ($userLevel !== $role) {
            Log::warning('Access denied for user', [
                'user_level' => $userLevel,
                'required_role' => $role,
                'url' => $request->url()
            ]);
            
            // Redirect ke dashboard sesuai role user
            switch($userLevel) {
                case 'admin':
                    return redirect()->route('dashboard.admin')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut');
                case 'petugas':
                    return redirect()->route('dashboard.petugas')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut');
                case 'dokter':
                    return redirect()->route('dashboard.dokter')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut');
                case 'orang_tua':
                    return redirect()->route('dashboard.orangtua')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut');
                default:
                    return redirect('/login')->with('error', 'Akses ditolak. Role tidak dikenali.');
            }
        }

        Log::info('Access granted', [
            'user_level' => $userLevel,
            'required_role' => $role
        ]);

        return $next($request);
    }
}