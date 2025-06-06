<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class OrangTuaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        
        $query = OrangTua::with('siswa');
        
        // Apply search if keyword exists
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('nama_ayah', 'like', "%{$keyword}%")
                  ->orWhere('nama_ibu', 'like', "%{$keyword}%")
                  ->orWhere('no_telp', 'like', "%{$keyword}%")
                  ->orWhere('id_orang_tua', 'like', "%{$keyword}%")
                  ->orWhereHas('siswa', function($query) use ($keyword) {
                      $query->where('nama_siswa', 'like', "%{$keyword}%")
                            ->orWhere('id_siswa', 'like', "%{$keyword}%");
                  });
            });
        }
        
        $orangTuas = $query->orderBy('id_orang_tua', 'desc')->paginate(10);
        
        return view('orangtua.index', compact('orangTuas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $siswas = Siswa::doesntHave('orangTua')->get();
        
        // Generate next ID
        $lastId = OrangTua::orderBy('id_orang_tua', 'desc')->first()->id_orang_tua ?? 'OT000';
        $nextNumber = 1;
        
        if (preg_match('/^OT(\d+)$/', $lastId, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }
        
        $nextId = 'OT' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        
        return view('orangtua.create', compact('siswas', 'nextId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswas,id_siswa|unique:orang_tuas,id_siswa',
            'nama_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:100',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'pendidikan_ayah' => 'nullable|string|max:20',
            'pendidikan_ibu' => 'nullable|string|max:20',
            'tanggal_lahir_ayah' => 'nullable|date',
            'tanggal_lahir_ibu' => 'nullable|date',
            // Password menjadi opsional karena akan diisi otomatis jika kosong
            'password' => 'nullable|string',
        ], [
            'id_siswa.required' => 'ID Siswa harus dipilih',
            'id_siswa.exists' => 'ID Siswa tidak terdaftar dalam sistem',
            'id_siswa.unique' => 'Siswa ini sudah memiliki data orang tua',
            'nama_ayah.required' => 'Nama Ayah harus diisi',
            'nama_ibu.required' => 'Nama Ibu harus diisi',
            'no_telp.required' => 'Nomor Telepon harus diisi',
            'alamat.required' => 'Alamat harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Format phone number if needed
            $noTelp = $request->no_telp;
            if (!empty($noTelp) && !str_starts_with($noTelp, '+')) {
                if (str_starts_with($noTelp, '0')) {
                    $noTelp = '+62' . substr($noTelp, 1);
                } elseif (str_starts_with($noTelp, '8')) {
                    $noTelp = '+62' . $noTelp;
                } elseif (str_starts_with($noTelp, '62')) {
                    $noTelp = '+' . $noTelp;
                }
            }
            
            // Create parent data
            $data = $request->all();
            $data['no_telp'] = $noTelp;
            
            // If no ID is provided, generate one
            if (empty($data['id_orang_tua'])) {
                $lastId = OrangTua::orderBy('id_orang_tua', 'desc')->first()->id_orang_tua ?? 'OT000';
                $nextNumber = 1;
                
                if (preg_match('/^OT(\d+)$/', $lastId, $matches)) {
                    $nextNumber = (int)$matches[1] + 1;
                }
                
                $data['id_orang_tua'] = 'OT' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
            
            // Ambil tanggal lahir siswa untuk password jika password kosong
            if (empty($data['password'])) {
                $siswa = Siswa::find($data['id_siswa']);
                if ($siswa && $siswa->tanggal_lahir) {
                    // Format password: DDMMYYYY
                    $data['password'] = date('dmY', strtotime($siswa->tanggal_lahir));
                    Log::info('Password otomatis dibuat dari tanggal lahir siswa', [
                        'id_siswa' => $siswa->id_siswa,
                        'tanggal_lahir' => $siswa->tanggal_lahir
                    ]);
                } else {
                    // Default password jika tidak ada tanggal lahir
                    $data['password'] = 'password123';
                    Log::info('Password default digunakan karena tidak ada tanggal lahir', [
                        'id_siswa' => $data['id_siswa']
                    ]);
                }
            }
            
            // Hash password before saving
            if (!empty($data['password'])) {
                $plainPassword = $data['password']; // Simpan password asli untuk notifikasi
                $data['password'] = Hash::make($data['password']);
            }
            
            $orangTua = OrangTua::create($data);
            
            DB::commit();
            
            // Siapkan message sukses dengan info password
            $successMessage = 'Data orang tua berhasil ditambahkan.';
            if (isset($plainPassword) && isset($siswa) && $siswa->tanggal_lahir) {
                $successMessage .= ' Password login dibuat otomatis dari tanggal lahir siswa: ' . date('d/m/Y', strtotime($siswa->tanggal_lahir)) . '.';
            }
            
            return redirect()->route('orangtua.index')
                            ->with('success', $successMessage);
                            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating parent data: ' . $e->getMessage());
            
            return redirect()->back()
                    ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                    ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orangTua = OrangTua::with('siswa')->findOrFail($id);
        return view('orangtua.show', compact('orangTua'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $siswas = Siswa::where('id_siswa', $orangTua->id_siswa)
                ->orDoesntHave('orangTua')
                ->get();
        
        return view('orangtua.edit', compact('orangTua', 'siswas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $orangTua = OrangTua::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswas,id_siswa|unique:orang_tuas,id_siswa,'.$orangTua->id_orang_tua.',id_orang_tua',
            'nama_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:100',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'pendidikan_ayah' => 'nullable|string|max:20',
            'pendidikan_ibu' => 'nullable|string|max:20',
            'tanggal_lahir_ayah' => 'nullable|date',
            'tanggal_lahir_ibu' => 'nullable|date',
            'password' => 'nullable|string',
            'reset_password' => 'nullable|boolean',
        ], [
            'id_siswa.required' => 'ID Siswa harus dipilih',
            'id_siswa.exists' => 'ID Siswa tidak terdaftar dalam sistem',
            'id_siswa.unique' => 'Siswa ini sudah memiliki data orang tua lain',
            'nama_ayah.required' => 'Nama Ayah harus diisi',
            'nama_ibu.required' => 'Nama Ibu harus diisi',
            'no_telp.required' => 'Nomor Telepon harus diisi',
            'alamat.required' => 'Alamat harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Format phone number if needed
            $noTelp = $request->no_telp;
            if (!empty($noTelp) && !str_starts_with($noTelp, '+')) {
                if (str_starts_with($noTelp, '0')) {
                    $noTelp = '+62' . substr($noTelp, 1);
                } elseif (str_starts_with($noTelp, '8')) {
                    $noTelp = '+62' . $noTelp;
                } elseif (str_starts_with($noTelp, '62')) {
                    $noTelp = '+' . $noTelp;
                }
            }
            
            // Persiapkan data untuk update
            $data = $request->except(['password', 'id_orang_tua', 'reset_password']);
            $data['no_telp'] = $noTelp;
            
            // Jika ID siswa berubah, periksa apakah perlu update password
            $passwordChanged = false;
            $resetPassword = $request->has('reset_password') && $request->reset_password;
            
            // Jika password dimasukkan secara manual
            if (!empty($request->password)) {
                $plainPassword = $request->password;
                $passwordToSave = Hash::make($request->password);
                $passwordChanged = true;
            } 
            // Jika reset password diminta atau perlu reset karena ID siswa berubah
            elseif ($resetPassword) {
                $siswa = Siswa::find($request->id_siswa);
                if ($siswa && $siswa->tanggal_lahir) {
                    // Format password dari tanggal lahir: DDMMYYYY
                    $plainPassword = date('dmY', strtotime($siswa->tanggal_lahir));
                    $passwordToSave = Hash::make($plainPassword);
                    $passwordChanged = true;
                    
                    Log::info('Password direset menggunakan tanggal lahir siswa', [
                        'id_siswa' => $siswa->id_siswa,
                        'tanggal_lahir' => $siswa->tanggal_lahir
                    ]);
                }
            }
            
            // Update data orang tua
            $orangTua->update($data);
            
            // Update password jika berubah
            if ($passwordChanged && isset($passwordToSave)) {
                $orangTua->password = $passwordToSave;
                $orangTua->save();
            }
            
            DB::commit();
            
            // Siapkan pesan sukses
            $successMessage = 'Data orang tua berhasil diperbarui.';
            if ($passwordChanged && isset($plainPassword) && isset($siswa) && $siswa->tanggal_lahir) {
                $successMessage .= ' Password telah direset menggunakan tanggal lahir siswa: ' . date('d/m/Y', strtotime($siswa->tanggal_lahir)) . '.'; 
            } elseif ($passwordChanged && isset($plainPassword)) {
                $successMessage .= ' Password berhasil diubah.';
            }
            
            return redirect()->route('orangtua.index')
                            ->with('success', $successMessage);
                            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating parent data: ' . $e->getMessage());
            
            return redirect()->back()
                    ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                    ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $orangTua = OrangTua::findOrFail($id);
            $orangTua->delete();
            
            DB::commit();
            
            return redirect()->route('orangtua.index')
                            ->with('success', 'Data orang tua berhasil dihapus.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting parent data: ' . $e->getMessage());
            
            return redirect()->route('orangtua.index')
                            ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
