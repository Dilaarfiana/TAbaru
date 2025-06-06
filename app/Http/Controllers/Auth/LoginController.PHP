<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $username = $request->username;
        $password = $request->password;

        // Cek Petugas UKS (Admin/Petugas) - Username: NIP
        $petugasUks = DB::table('petugas_uks')
            ->where('NIP', $username)
            ->where('status_aktif', 1)
            ->first();

        if ($petugasUks && Hash::check($password, $petugasUks->password)) {
            session([
                'user_id' => $petugasUks->NIP,
                'username' => $petugasUks->nama_petugas_uks, // ✅ Gunakan 'username' bukan 'user_name'
                'user_type' => 'petugas_uks',
                'user_level' => $petugasUks->level, // admin atau petugas
                'is_logged_in' => true,
                'login_time' => now(),
                'full_name' => $petugasUks->nama_petugas_uks,
                'nip' => $petugasUks->NIP,
                'alamat' => $petugasUks->alamat ?? null,
                'no_telp' => $petugasUks->no_telp ?? null,
            ]);

            if ($petugasUks->level == 'admin') {
                return redirect()->intended('/admin/dashboard')->with('success', 'Login berhasil sebagai Admin UKS');
            } else {
                return redirect()->intended('/petugas/dashboard')->with('success', 'Login berhasil sebagai Petugas UKS');
            }
        }

        // Cek Dokter - Username: Id_Dokter
        $dokter = DB::table('dokters')
            ->where('Id_Dokter', $username)
            ->where('status_aktif', 1)
            ->first();

        if ($dokter && Hash::check($password, $dokter->password)) {
            session([
                'user_id' => $dokter->Id_Dokter,
                'username' => $dokter->Nama_Dokter, // ✅ Gunakan 'username' bukan 'user_name'
                'user_type' => 'dokter',
                'user_level' => 'dokter',
                'is_logged_in' => true,
                'login_time' => now(),
                'full_name' => $dokter->Nama_Dokter,
                'id_dokter' => $dokter->Id_Dokter,
                'spesialisasi' => $dokter->Spesialisasi ?? null,
                'alamat' => $dokter->Alamat ?? null,
                'no_telp' => $dokter->No_Telp ?? null,
            ]);

            return redirect()->intended('/dokter/dashboard')->with('success', 'Login berhasil sebagai Dokter');
        }

        // Cek Orang Tua - Username: id_siswa, Password: tanggal lahir siswa (ddmmyyyy)
        $orangTua = DB::table('orang_tuas')
            ->join('siswas', 'orang_tuas.id_siswa', '=', 'siswas.id_siswa')
            ->where('orang_tuas.id_siswa', $username)
            ->where('siswas.status_aktif', 1)
            ->select('orang_tuas.*', 'siswas.tanggal_lahir', 'siswas.nama_siswa')
            ->first();

        if ($orangTua) {
            // Format tanggal lahir siswa ke ddmmyyyy
            $tanggalLahir = Carbon::parse($orangTua->tanggal_lahir);
            $passwordExpected = $tanggalLahir->format('dmY'); // Format: ddmmyyyy

            if ($password === $passwordExpected) {
                // Tentukan nama yang akan ditampilkan (prioritas: ayah, lalu ibu)
                $namaOrangTua = $orangTua->nama_ayah ?: $orangTua->nama_ibu;
                
                // Jika tidak ada nama ayah/ibu, gunakan default
                if (!$namaOrangTua) {
                    $namaOrangTua = 'Orang Tua ' . $orangTua->nama_siswa;
                }

                session([
                    'user_id' => $orangTua->id_orang_tua,
                    'username' => $namaOrangTua, // ✅ Gunakan 'username' bukan 'user_name'
                    'user_type' => 'orang_tua',
                    'user_level' => 'orang_tua',
                    'is_logged_in' => true,
                    'login_time' => now(),
                    'siswa_id' => $orangTua->id_siswa,
                    'siswa_name' => $orangTua->nama_siswa,
                    'id_orang_tua' => $orangTua->id_orang_tua,
                    'full_name' => $namaOrangTua,
                    // Data lengkap orang tua
                    'nama_ayah' => $orangTua->nama_ayah,
                    'nama_ibu' => $orangTua->nama_ibu,
                    'tanggal_lahir_ayah' => $orangTua->tanggal_lahir_ayah,
                    'tanggal_lahir_ibu' => $orangTua->tanggal_lahir_ibu,
                    'pekerjaan_ayah' => $orangTua->pekerjaan_ayah,
                    'pekerjaan_ibu' => $orangTua->pekerjaan_ibu,
                    'pendidikan_ayah' => $orangTua->pendidikan_ayah,
                    'pendidikan_ibu' => $orangTua->pendidikan_ibu,
                    'alamat' => $orangTua->alamat,
                    'no_telp' => $orangTua->no_telp,
                ]);

                return redirect()->intended('/orangtua/dashboard')->with('success', 'Login berhasil sebagai Orang Tua');
            }
        }

        return back()->withErrors([
            'login' => 'Username atau password salah, atau akun tidak aktif.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        // Log aktivitas logout (opsional)
        $username = session('username', 'Unknown');
        $userLevel = session('user_level', 'unknown');
        
        // Clear semua session
        session()->flush();
        
        // Regenerate session ID untuk keamanan
        $request->session()->regenerate();
        
        return redirect('/login')->with('success', 'Anda telah logout');
    }

    /**
     * Get user info dari session untuk keperluan lain
     */
    public static function getCurrentUser()
    {
        return [
            'user_id' => session('user_id'),
            'username' => session('username'),
            'user_type' => session('user_type'),
            'user_level' => session('user_level'),
            'full_name' => session('full_name'),
            'is_logged_in' => session('is_logged_in', false),
            'login_time' => session('login_time'),
            'siswa_id' => session('siswa_id'), // khusus orang tua
            'siswa_name' => session('siswa_name'), // khusus orang tua
        ];
    }

    /**
     * Check apakah user sudah login
     */
    public static function isAuthenticated()
    {
        return session('is_logged_in', false) && session('user_id');
    }

    /**
     * Get user level/role
     */
    public static function getUserLevel()
    {
        return session('user_level', 'guest');
    }

    /**
     * Get display name untuk user
     */
    public static function getDisplayName()
    {
        $username = session('username');
        $userLevel = session('user_level');
        
        if (!$username) {
            return 'User';
        }

        // Untuk orang tua, tampilkan nama + info siswa
        if ($userLevel === 'orang_tua') {
            $siswaName = session('siswa_name');
            return $username . ($siswaName ? ' (Orang Tua ' . $siswaName . ')' : '');
        }

        return $username;
    }

    /**
     * Validate session untuk middleware
     */
    public static function validateSession()
    {
        $isLoggedIn = session('is_logged_in', false);
        $userId = session('user_id');
        $loginTime = session('login_time');

        if (!$isLoggedIn || !$userId) {
            return false;
        }

        // Check session timeout (optional - 8 hours)
        if ($loginTime && Carbon::parse($loginTime)->addHours(8)->isPast()) {
            session()->flush();
            return false;
        }

        return true;
    }
}