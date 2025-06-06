<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\PetugasUKS;
use App\Models\Dokter;
use App\Models\OrangTua;
use App\Models\Siswa;

class ProfileController extends Controller
{
    public function show()
    {
        $userLevel = session('user_level');
        $userId = session('user_id');
        
        if (!$userId || !$userLevel) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $profileData = $this->getProfileData($userLevel, $userId);
        
        if (!$profileData) {
            return redirect()->route('dashboard')->with('error', 'Profile data not found.');
        }

        return view('profile.show', compact('profileData', 'userLevel'));
    }

    public function edit()
    {
        $userLevel = session('user_level');
        $userId = session('user_id');
        
        if (!$userId || !$userLevel) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $profileData = $this->getProfileData($userLevel, $userId);
        
        if (!$profileData) {
            return redirect()->route('dashboard')->with('error', 'Profile data not found.');
        }

        // Get additional data for orang tua
        $siswaData = null;
        if ($userLevel === 'orang_tua') {
            $siswaId = session('siswa_id');
            if ($siswaId) {
                $siswaData = Siswa::with(['detailSiswa.kelas.jurusan'])->find($siswaId);
            }
        }

        return view('profile.edit', compact('profileData', 'userLevel', 'siswaData'));
    }

    public function update(Request $request)
    {
        $userLevel = session('user_level');
        $userId = session('user_id');
        
        if (!$userId || !$userLevel) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Validate based on user level
        $rules = $this->getValidationRules($userLevel);
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updated = $this->updateProfileData($userLevel, $userId, $request);
            
            if ($updated) {
                // Update session username if name changed
                $this->updateSessionData($userLevel, $userId);
                
                return redirect()->route('profile.show')
                    ->with('success', 'Profile berhasil diperbarui.');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui profile.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function getProfileData($userLevel, $userId)
    {
        switch ($userLevel) {
            case 'admin':
            case 'petugas':
                return PetugasUKS::find($userId);
                
            case 'dokter':
                return Dokter::find($userId);
                
            case 'orang_tua':
                return OrangTua::with(['siswa'])->find($userId);
                
            default:
                return null;
        }
    }

    private function getValidationRules($userLevel)
    {
        $baseRules = [
            'alamat' => 'nullable|string|max:500',
            'no_telp' => 'nullable|string|max:15',
        ];

        switch ($userLevel) {
            case 'admin':
            case 'petugas':
                return array_merge($baseRules, [
                    'nama_petugas_uks' => 'required|string|max:50',
                ]);
                
            case 'dokter':
                return array_merge($baseRules, [
                    'Nama_Dokter' => 'required|string|max:50',
                    'Spesialisasi' => 'nullable|string|max:25',
                    'No_Telp' => 'nullable|string|max:15',
                    'Alamat' => 'nullable|string|max:500',
                ]);
                
            case 'orang_tua':
                return [
                    'nama_ayah' => 'nullable|string|max:100',
                    'tanggal_lahir_ayah' => 'nullable|date',
                    'pekerjaan_ayah' => 'nullable|string|max:50',
                    'pendidikan_ayah' => 'nullable|string|max:50',
                    'nama_ibu' => 'nullable|string|max:100',
                    'tanggal_lahir_ibu' => 'nullable|date',
                    'pekerjaan_ibu' => 'nullable|string|max:50',
                    'pendidikan_ibu' => 'nullable|string|max:50',
                    'alamat' => 'nullable|string|max:500',
                    'no_telp' => 'nullable|string|max:20',
                ];
                
            default:
                return [];
        }
    }

    private function updateProfileData($userLevel, $userId, $request)
    {
        switch ($userLevel) {
            case 'admin':
            case 'petugas':
                $petugas = PetugasUKS::find($userId);
                if ($petugas) {
                    $petugas->update([
                        'nama_petugas_uks' => $request->nama_petugas_uks,
                        'alamat' => $request->alamat,
                        'no_telp' => $request->no_telp,
                    ]);
                    return true;
                }
                break;
                
            case 'dokter':
                $dokter = Dokter::find($userId);
                if ($dokter) {
                    $dokter->update([
                        'Nama_Dokter' => $request->Nama_Dokter,
                        'Spesialisasi' => $request->Spesialisasi,
                        'No_Telp' => $request->No_Telp,
                        'Alamat' => $request->Alamat,
                    ]);
                    return true;
                }
                break;
                
            case 'orang_tua':
                $orangTua = OrangTua::find($userId);
                if ($orangTua) {
                    $orangTua->update([
                        'nama_ayah' => $request->nama_ayah,
                        'tanggal_lahir_ayah' => $request->tanggal_lahir_ayah,
                        'pekerjaan_ayah' => $request->pekerjaan_ayah,
                        'pendidikan_ayah' => $request->pendidikan_ayah,
                        'nama_ibu' => $request->nama_ibu,
                        'tanggal_lahir_ibu' => $request->tanggal_lahir_ibu,
                        'pekerjaan_ibu' => $request->pekerjaan_ibu,
                        'pendidikan_ibu' => $request->pendidikan_ibu,
                        'alamat' => $request->alamat,
                        'no_telp' => $request->no_telp,
                    ]);
                    return true;
                }
                break;
        }
        
        return false;
    }

    private function updateSessionData($userLevel, $userId)
    {
        $profileData = $this->getProfileData($userLevel, $userId);
        
        if ($profileData) {
            switch ($userLevel) {
                case 'admin':
                case 'petugas':
                    session(['username' => $profileData->nama_petugas_uks]);
                    break;
                    
                case 'dokter':
                    session(['username' => $profileData->Nama_Dokter]);
                    break;
                    
                case 'orang_tua':
                    $name = $profileData->nama_ayah ?: $profileData->nama_ibu ?: 'Orang Tua';
                    session(['username' => $name]);
                    break;
            }
        }
    }

    public function destroy(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur hapus akun belum tersedia. Hubungi administrator untuk menonaktifkan akun.');
    }
}