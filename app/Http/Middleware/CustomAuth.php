<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log untuk debugging
        Log::info('CustomAuth Middleware check', [
            'has_user_id' => session()->has('user_id'),
            'user_id' => session('user_id'),
            'user_level' => session('user_level'),
            'url' => $request->url(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Cek apakah user sudah login
        if (!session()->has('user_id')) {
            Log::warning('Unauthorized access attempt', [
                'url' => $request->url(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId()
            ]);
            
            // Hapus session data yang mungkin corrupt
            session()->flush();
            
            // Redirect ke login dengan pesan error
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Validasi tambahan untuk memastikan data session valid
        $userId = session('user_id');
        $userLevel = session('user_level');
        
        if (empty($userId) || empty($userLevel)) {
            Log::warning('Invalid session data detected', [
                'user_id' => $userId,
                'user_level' => $userLevel,
                'url' => $request->url(),
                'session_id' => session()->getId()
            ]);
            
            // Hapus session yang invalid
            session()->flush();
            
            return redirect('/login')->with('error', 'Session tidak valid. Silakan login kembali');
        }

        // Validasi user level yang diizinkan
        $allowedLevels = ['admin', 'petugas', 'dokter', 'orang_tua'];
        if (!in_array($userLevel, $allowedLevels)) {
            Log::error('Invalid user level detected', [
                'user_id' => $userId,
                'user_level' => $userLevel,
                'url' => $request->url(),
                'session_id' => session()->getId()
            ]);
            
            session()->flush();
            
            return redirect('/login')->with('error', 'Level user tidak valid. Silakan hubungi administrator');
        }

        // Log successful authentication
        Log::info('User authenticated successfully', [
            'user_id' => $userId,
            'user_level' => $userLevel,
            'url' => $request->url()
        ]);

        // Set session timeout untuk keamanan (optional)
        $this->checkSessionTimeout($request);

        return $next($request);
    }

    /**
     * Check session timeout untuk keamanan
     */
    private function checkSessionTimeout(Request $request)
    {
        $timeout = config('session.lifetime', 120) * 60; // Convert to seconds
        $lastActivity = session('last_activity', time());
        
        if (time() - $lastActivity > $timeout) {
            Log::info('Session timeout detected', [
                'user_id' => session('user_id'),
                'last_activity' => $lastActivity,
                'timeout' => $timeout
            ]);
            
            session()->flush();
            
            // Note: Kita tidak bisa redirect di sini karena middleware sudah dijalankan
            // Session akan dihapus dan user akan diredirect di request berikutnya
            session(['session_expired' => true]);
        } else {
            // Update last activity
            session(['last_activity' => time()]);
        }
    }
}