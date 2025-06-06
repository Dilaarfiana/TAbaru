<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\Siswa;
use App\Models\Dokter;
use App\Models\DetailPemeriksaan;
use App\Models\PetugasUKS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Schema\Blueprint;

class RekamMedisController extends Controller
{
    /**
     * Get route names based on user role
     */
    private function getRouteNames()
    {
        $userLevel = session('user_level');
        
        $routes = [
            'admin' => [
                'index' => 'rekam_medis.index',
                'create' => 'rekam_medis.create',
                'show' => 'rekam_medis.show',
                'edit' => 'rekam_medis.edit',
                'destroy' => 'rekam_medis.destroy'
            ],
            'petugas' => [
                'index' => 'petugas.rekam_medis.index',
                'create' => 'petugas.rekam_medis.create',
                'show' => 'petugas.rekam_medis.show',
                'edit' => 'petugas.rekam_medis.edit',
                'destroy' => null // Petugas tidak bisa delete
            ],
            'dokter' => [
                'index' => 'dokter.rekam_medis.index',
                'create' => null, // Dokter tidak bisa create
                'show' => 'dokter.rekam_medis.show',
                'edit' => null, // Dokter tidak bisa edit
                'destroy' => null // Dokter tidak bisa delete
            ],
            // REMOVED: orang_tua routes - mereka tidak boleh akses rekam medis sama sekali
        ];
        
        return $routes[$userLevel] ?? $routes['admin'];
    }

    /**
     * Check if current user has permission for specific action
     */
    private function hasPermission($action)
    {
        $userLevel = session('user_level');
        
        $permissions = [
            'admin' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'histori'],
            'petugas' => ['index', 'create', 'store', 'show', 'edit', 'update', 'histori'], // Tidak ada destroy
            'dokter' => ['index', 'show', 'histori'], // Read only
            // REMOVED: orang_tua permissions - tidak ada akses sama sekali
        ];
        
        $userPermissions = $permissions[$userLevel] ?? [];
        return in_array($action, $userPermissions);
    }

    /**
     * Block orang tua completely - redirect to their dashboard
     */
    private function blockOrangTua()
    {
        if (session('user_level') === 'orang_tua') {
            // Redirect orang tua ke dashboard mereka dengan pesan error
            return redirect()->route('dashboard.orangtua')
                ->with('error', 'Akses ditolak. Orang tua tidak memiliki akses ke rekam medis. Silakan gunakan menu "Ringkasan Kesehatan" untuk melihat informasi kesehatan anak Anda.');
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission
        if (!$this->hasPermission('index')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat daftar rekam medis.');
        }

        Log::info("Rekam Medis Index accessed by user level: " . session('user_level'));

        $query = RekamMedis::with(['siswa', 'dokter']);

        // Filter berdasarkan siswa
        if ($request->filled('siswa')) {
            $query->where('Id_Siswa', $request->siswa);
        }

        // Filter berdasarkan dokter
        if ($request->filled('dokter')) {
            $query->where('Id_Dokter', $request->dokter);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('Tanggal_Jam', $request->tanggal);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('siswa', function($q) use ($request) {
                    $q->where('Nama_Siswa', 'like', '%' . $request->search . '%')
                      ->orWhere('nama_siswa', 'like', '%' . $request->search . '%');
                })
                ->orWhere('No_Rekam_Medis', 'like', '%' . $request->search . '%')
                ->orWhere('Keluhan_Utama', 'like', '%' . $request->search . '%');
            });
        }

        $rekamMedis = $query->orderBy('Tanggal_Jam', 'desc')->paginate(15);
        $siswas = Siswa::where('status_aktif', 1)->orderBy('Nama_Siswa')->get();
        $dokters = Dokter::where('status_aktif', 1)->orderBy('Nama_Dokter')->get();

        $totalRekamMedis = RekamMedis::count();
        $totalRekamMedisBulanIni = RekamMedis::whereMonth('Tanggal_Jam', now()->month)
            ->whereYear('Tanggal_Jam', now()->year)
            ->count();

        return view('rekam_medis.index', compact(
            'rekamMedis',
            'siswas',
            'dokters',
            'totalRekamMedis',
            'totalRekamMedisBulanIni'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission
        if (!$this->hasPermission('create')) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('error', 'Anda tidak memiliki akses untuk menambah rekam medis baru.');
        }

        $siswas = Siswa::where('status_aktif', 1)->orderBy('Nama_Siswa')->get();
        $dokters = Dokter::where('status_aktif', 1)->orderBy('Nama_Dokter')->get();
        $petugasUKS = PetugasUKS::where('status_aktif', 1)->orderBy('Nama_Petugas_UKS')->get();
        
        // Generate new ID
        $newId = $this->generateNewId();
        
        return view('rekam_medis.create', compact('siswas', 'dokters', 'petugasUKS', 'newId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission
        if (!$this->hasPermission('store')) {
            abort(403, 'Anda tidak memiliki akses untuk menyimpan rekam medis.');
        }

        $request->validate([
            'Id_Siswa' => 'required|exists:siswas,id_siswa',
            'Id_Dokter' => 'required|exists:dokters,Id_Dokter',
            'NIP' => 'required|exists:petugas_uks,NIP',
            'Tanggal_Jam' => 'required|date',
            'Keluhan_Utama' => 'required|string',
        ], [
            'Id_Siswa.required' => 'Siswa wajib dipilih',
            'Id_Siswa.exists' => 'Siswa tidak valid',
            'Id_Dokter.required' => 'Dokter wajib dipilih',
            'Id_Dokter.exists' => 'Dokter tidak valid',
            'NIP.required' => 'Petugas UKS wajib dipilih',
            'NIP.exists' => 'Petugas UKS tidak valid',
            'Tanggal_Jam.required' => 'Tanggal dan jam wajib diisi',
            'Tanggal_Jam.date' => 'Format tanggal tidak valid',
            'Keluhan_Utama.required' => 'Keluhan utama wajib diisi',
        ]);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Generate ID untuk Rekam Medis
            $newRecId = $request->No_Rekam_Medis ?? $this->generateNewId();
            
            Log::info("Creating rekam medis with ID: {$newRecId} by user level: " . session('user_level'));
            
            // Buat rekam medis
            $rekamMedis = RekamMedis::create([
                'No_Rekam_Medis' => $newRecId,
                'Id_Siswa' => $request->Id_Siswa,
                'Id_Dokter' => $request->Id_Dokter,
                'NIP' => $request->NIP,
                'Tanggal_Jam' => $request->Tanggal_Jam,
                'Keluhan_Utama' => $request->Keluhan_Utama,
                'Riwayat_Penyakit_Sekarang' => $request->Riwayat_Penyakit_Sekarang,
                'Riwayat_Penyakit_Dahulu' => $request->Riwayat_Penyakit_Dahulu,
                'Riwayat_Imunisasi' => $request->Riwayat_Imunisasi,
                'Riwayat_Penyakit_Keluarga' => $request->Riwayat_Penyakit_Keluarga,
                'Silsilah_Keluarga' => $request->Silsilah_Keluarga
            ]);
            
            // Buat detail pemeriksaan otomatis
            $detailPemeriksaan = $this->createDetailPemeriksaan(
                $request->Id_Siswa,
                $request->Id_Dokter,
                $request->NIP,
                $request->Tanggal_Jam
            );
            
            if (!$detailPemeriksaan) {
                throw new \Exception("Gagal membuat detail pemeriksaan");
            }
            
            // Commit transaksi
            DB::commit();
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('success', 'Rekam medis berhasil ditambahkan dan detail pemeriksaan telah dibuat otomatis.');
            
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();
            Log::error("Error saat menyimpan rekam medis: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['create'])
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission
        if (!$this->hasPermission('show')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail rekam medis.');
        }

        $rekamMedis = RekamMedis::with(['siswa', 'dokter', 'petugasUKS'])->findOrFail($id);
        $detailPemeriksaan = DetailPemeriksaan::with(['dokter', 'petugasUks'])
            ->where('Id_Siswa', $rekamMedis->Id_Siswa)
            ->orderBy('Tanggal_Jam', 'desc')
            ->get();
            
        return view('rekam_medis.show', compact('rekamMedis', 'detailPemeriksaan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission
        if (!$this->hasPermission('edit')) {
            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('error', 'Anda tidak memiliki akses untuk mengedit rekam medis.');
        }

        $rekamMedis = RekamMedis::findOrFail($id);
        $siswas = Siswa::where('status_aktif', 1)->orderBy('Nama_Siswa')->get();
        $dokters = Dokter::where('status_aktif', 1)->orderBy('Nama_Dokter')->get();
        $petugasUKS = PetugasUKS::where('status_aktif', 1)->orderBy('Nama_Petugas_UKS')->get();
        
        return view('rekam_medis.edit', compact('rekamMedis', 'siswas', 'dokters', 'petugasUKS'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission
        if (!$this->hasPermission('update')) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui rekam medis.');
        }

        $request->validate([
            'Id_Dokter' => 'required|exists:dokters,Id_Dokter',
            'NIP' => 'required|exists:petugas_uks,NIP',
            'Tanggal_Jam' => 'required|date',
            'Keluhan_Utama' => 'required|string',
        ], [
            'Id_Dokter.required' => 'Dokter wajib dipilih',
            'Id_Dokter.exists' => 'Dokter tidak valid',
            'NIP.required' => 'Petugas UKS wajib dipilih',
            'NIP.exists' => 'Petugas UKS tidak valid',
            'Tanggal_Jam.required' => 'Tanggal dan jam wajib diisi',
            'Tanggal_Jam.date' => 'Format tanggal tidak valid',
            'Keluhan_Utama.required' => 'Keluhan utama wajib diisi',
        ]);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            $rekamMedis = RekamMedis::findOrFail($id);
            
            Log::info("Updating rekam medis {$id} by user level: " . session('user_level'));
            
            // Update rekam medis
            $rekamMedis->update([
                'Id_Dokter' => $request->Id_Dokter,
                'NIP' => $request->NIP,
                'Tanggal_Jam' => $request->Tanggal_Jam,
                'Keluhan_Utama' => $request->Keluhan_Utama,
                'Riwayat_Penyakit_Sekarang' => $request->Riwayat_Penyakit_Sekarang,
                'Riwayat_Penyakit_Dahulu' => $request->Riwayat_Penyakit_Dahulu,
                'Riwayat_Imunisasi' => $request->Riwayat_Imunisasi,
                'Riwayat_Penyakit_Keluarga' => $request->Riwayat_Penyakit_Keluarga,
                'Silsilah_Keluarga' => $request->Silsilah_Keluarga
            ]);
            
            // Cari detail pemeriksaan terkait
            $detailPemeriksaan = DetailPemeriksaan::where('Id_Siswa', $rekamMedis->Id_Siswa)
                ->where('Id_Dokter', $rekamMedis->Id_Dokter)
                ->whereDate('Tanggal_Jam', date('Y-m-d', strtotime($rekamMedis->Tanggal_Jam)))
                ->first();
                
            if ($detailPemeriksaan) {
                // Update detail pemeriksaan jika ada
                $detailPemeriksaan->update([
                    'Id_Dokter' => $request->Id_Dokter,
                    'NIP' => $request->NIP,
                    'Tanggal_Jam' => $request->Tanggal_Jam,
                ]);
            } else {
                // Buat detail pemeriksaan baru jika tidak ada
                $detailResult = $this->createDetailPemeriksaan(
                    $rekamMedis->Id_Siswa,
                    $request->Id_Dokter,
                    $request->NIP,
                    $request->Tanggal_Jam
                );
                
                if (!$detailResult) {
                    throw new \Exception("Gagal membuat detail pemeriksaan");
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['index'])
                ->with('success', 'Rekam medis berhasil diperbarui.');
            
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();
            Log::error("Error saat memperbarui rekam medis: " . $e->getMessage());
            
            $routes = $this->getRouteNames();
            return redirect()->route($routes['edit'], $id)
                ->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Only admin can delete
     */
    public function destroy(string $id)
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission - hanya admin yang bisa delete
        if (!$this->hasPermission('destroy')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus rekam medis.');
        }

        DB::beginTransaction();
        
        try {
            $rekamMedis = RekamMedis::findOrFail($id);
            $rekamMedisNo = $rekamMedis->No_Rekam_Medis;
            
            Log::info("Deleting rekam medis {$rekamMedisNo} by user level: " . session('user_level'));
            
            // Delete related detail pemeriksaan
            DetailPemeriksaan::where('Id_Siswa', $rekamMedis->Id_Siswa)
                ->whereDate('Tanggal_Jam', date('Y-m-d', strtotime($rekamMedis->Tanggal_Jam)))
                ->delete();
            
            $rekamMedis->delete();
            
            DB::commit();
            
            return redirect()->route('rekam_medis.index')
                ->with('success', "Rekam medis {$rekamMedisNo} berhasil dihapus beserta detail pemeriksaan terkait.");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error saat menghapus rekam medis: " . $e->getMessage());
            
            return redirect()->route('rekam_medis.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Histori rekam medis siswa
     */
    public function histori($id_siswa)
    {
        // BLOCK orang tua completely
        $blockCheck = $this->blockOrangTua();
        if ($blockCheck) return $blockCheck;

        // Check permission
        if (!$this->hasPermission('histori')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat histori rekam medis.');
        }

        $siswa = Siswa::findOrFail($id_siswa);
        $rekamMedis = RekamMedis::where('Id_Siswa', $id_siswa)
            ->with(['dokter', 'petugasUKS'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->get();
        $detailPemeriksaan = DetailPemeriksaan::where('Id_Siswa', $id_siswa)
            ->with(['dokter', 'petugasUKS'])
            ->orderBy('Tanggal_Jam', 'desc')
            ->get();
            
        return view('rekam_medis.histori', compact('siswa', 'rekamMedis', 'detailPemeriksaan'));
    }
    
    /**
     * Generate new ID for rekam medis
     */
    private function generateNewId()
    {
        try {
            // Mulai transaction untuk generate ID
            DB::beginTransaction();
            
            // Ambil nilai sequence saat ini dalam lock
            $sequence = DB::table('sequence_ids')
                ->where('sequence_name', 'rekam_medis_id')
                ->lockForUpdate()
                ->first();
            
            // Jika sequence belum ada, buat baru
            if (!$sequence) {
                DB::table('sequence_ids')->insert([
                    'sequence_name' => 'rekam_medis_id',
                    'current_value' => 0
                ]);
                $currentValue = 0;
            } else {
                $currentValue = $sequence->current_value;
            }
            
            // Increment nilai sequence
            $nextValue = $currentValue + 1;
            
            // Update nilai sequence
            DB::table('sequence_ids')
                ->where('sequence_name', 'rekam_medis_id')
                ->update(['current_value' => $nextValue]);
            
            // Format ID: RM + nomor urut (3 digit)
            $formattedId = "RM" . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
            
            // Commit transaction
            DB::commit();
            
            Log::info('Generated new rekam medis ID', [
                'id' => $formattedId, 
                'sequence_value' => $nextValue
            ]);
            
            return $formattedId;
            
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            
            // Log error
            Log::error('Error generating sequence ID untuk rekam medis: ' . $e->getMessage());
            
            // Fallback ke metode lama jika terjadi error
            $lastId = RekamMedis::orderBy('No_Rekam_Medis', 'desc')->first();
            
            if ($lastId) {
                $lastNumber = intval(substr($lastId->No_Rekam_Medis, 2));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            
            return "RM" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * PERBAIKAN: Method untuk membuat detail pemeriksaan yang lebih sederhana dan reliable
     */
    private function createDetailPemeriksaan($idSiswa, $idDokter, $nip, $tanggalJam = null)
    {
        try {
            Log::info("Membuat detail pemeriksaan untuk:", [
                'Id_Siswa' => $idSiswa,
                'Id_Dokter' => $idDokter,
                'NIP' => $nip,
                'Tanggal_Jam' => $tanggalJam
            ]);

            // Generate ID untuk Detail Pemeriksaan
            $lastId = DB::table('detail_pemeriksaans')->max('Id_DetPrx');
            
            if (!$lastId) {
                $newId = 'DP001';
            } else {
                $newIdNumber = intval(substr($lastId, 2)) + 1;
                $newId = 'DP' . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
            }
            
            Log::info("Generated ID: " . $newId);
            
            // Format tanggal
            $formattedDate = $tanggalJam ?? now()->format('Y-m-d H:i:s');
            
            // Gunakan Eloquent Model untuk membuat data
            $detailPemeriksaan = DetailPemeriksaan::create([
                'Id_DetPrx' => $newId,
                'Tanggal_Jam' => $formattedDate,
                'Id_Siswa' => $idSiswa,
                'Status_Pemeriksaan' => 'belum lengkap',
                'Id_Dokter' => $idDokter,
                'NIP' => $nip,
            ]);
            
            Log::info("Detail pemeriksaan berhasil dibuat: " . $newId);
            
            return $detailPemeriksaan;
            
        } catch (\Exception $e) {
            Log::error("Error saat membuat detail pemeriksaan: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Fallback: gunakan raw query jika model gagal
            try {
                Log::info("Mencoba dengan raw query...");
                
                $newId = $newId ?? 'DP001';
                $formattedDate = $tanggalJam ?? now()->format('Y-m-d H:i:s');
                
                $inserted = DB::insert(
                    "INSERT INTO detail_pemeriksaans (Id_DetPrx, Tanggal_Jam, Id_Siswa, Status_Pemeriksaan, Id_Dokter, NIP) VALUES (?, ?, ?, ?, ?, ?)",
                    [$newId, $formattedDate, $idSiswa, 'belum lengkap', $idDokter, $nip]
                );
                
                if ($inserted) {
                    Log::info("Detail pemeriksaan berhasil dibuat dengan raw query");
                    
                    // Return pseudo model object
                    $detailPemeriksaan = new DetailPemeriksaan();
                    $detailPemeriksaan->Id_DetPrx = $newId;
                    $detailPemeriksaan->Tanggal_Jam = $formattedDate;
                    $detailPemeriksaan->Id_Siswa = $idSiswa;
                    $detailPemeriksaan->Status_Pemeriksaan = 'belum lengkap';
                    $detailPemeriksaan->Id_Dokter = $idDokter;
                    $detailPemeriksaan->NIP = $nip;
                    
                    return $detailPemeriksaan;
                }
                
                return null;
                
            } catch (\Exception $e2) {
                Log::error("Raw query juga gagal: " . $e2->getMessage());
                return null;
            }
        }
    }
}