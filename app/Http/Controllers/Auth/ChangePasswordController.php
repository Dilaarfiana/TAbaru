<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\PetugasUKS;
use App\Models\Dokter;
use App\Models\OrangTua;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Gagal mengubah password. Periksa kembali input Anda.');
        }

        $userLevel = session('user_level');
        $userId = session('user_id');

        if (!$userId || !$userLevel) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        try {
            $user = $this->getUserByLevel($userLevel, $userId);
            
            if (!$user) {
                return redirect()->back()->with('error', 'User tidak ditemukan.');
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Password saat ini tidak benar'])
                    ->with('error', 'Password saat ini tidak benar.');
            }

            // Check if new password is same as current
            if (Hash::check($request->new_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['new_password' => 'Password baru harus berbeda dengan password saat ini'])
                    ->with('error', 'Password baru harus berbeda dengan password saat ini.');
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->back()->with('success', 'Password berhasil diubah.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getUserByLevel($userLevel, $userId)
    {
        switch ($userLevel) {
            case 'admin':
            case 'petugas':
                return PetugasUKS::find($userId);
                
            case 'dokter':
                return Dokter::find($userId);
                
            case 'orang_tua':
                return OrangTua::find($userId);
                
            default:
                return null;
        }
    }
}