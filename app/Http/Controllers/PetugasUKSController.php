<?php

namespace App\Http\Controllers;

use App\Models\PetugasUKS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Exports\PetugasUksExport;
use Maatwebsite\Excel\Facades\Excel;

class PetugasUKSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Base query
        $query = PetugasUKS::query();
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('NIP', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nama_petugas_uks', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('no_telp', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status_aktif', $request->status);
        }
        
        // Apply keyword filter if provided (additional search field from filter modal)
        if ($request->has('keyword') && !empty($request->keyword)) {
            $keywordTerm = $request->keyword;
            $query->where(function($q) use ($keywordTerm) {
                $q->where('NIP', 'LIKE', "%{$keywordTerm}%")
                  ->orWhere('nama_petugas_uks', 'LIKE', "%{$keywordTerm}%");
            });
        }
        
        // Get paginated results
        $petugasUKS = $query->paginate(10);
        
        // Preserve query parameters in pagination links
        $petugasUKS->appends($request->except('page'));
        
        // Hitung statistik untuk dashboard
        $totalPetugas = PetugasUKS::count();
        $petugasAktif = PetugasUKS::where('status_aktif', 1)->count();
        $petugasTidakAktif = PetugasUKS::where('status_aktif', 0)->count();
        $petugasMingguan = PetugasUKS::where('created_at', '>=', now()->subWeek())->count();
        
        return view('petugasuks.index', compact(
            'petugasUKS', 
            'totalPetugas', 
            'petugasAktif', 
            'petugasTidakAktif', 
            'petugasMingguan'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('petugasuks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'NIP' => 'required|string|max:18|unique:petugas_uks,NIP',
            'nama_petugas_uks' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:15',
            'status_aktif' => 'boolean',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Format nomor telepon dengan +62 jika ada
        $no_telp = $request->no_telp;
        if (!empty($no_telp)) {
            // Nomor telepon akan diformat oleh mutator di model
        }

        PetugasUKS::create([
            'NIP' => $request->NIP,
            'nama_petugas_uks' => $request->nama_petugas_uks,
            'alamat' => $request->alamat,
            'no_telp' => $no_telp,
            'status_aktif' => $request->status_aktif ?? true,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('petugasuks.index')
            ->with('success', 'Petugas UKS berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $nip
     * @return \Illuminate\Http\Response
     */
    public function show($nip)
    {
        $petugasUKS = PetugasUKS::findOrFail($nip);
        return view('petugasuks.show', compact('petugasUKS'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $nip
     * @return \Illuminate\Http\Response
     */
    public function edit($nip)
    {
        $petugasUKS = PetugasUKS::findOrFail($nip);
        return view('petugasuks.edit', compact('petugasUKS'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $nip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nip)
    {
        $petugasUKS = PetugasUKS::findOrFail($nip);
        
        $request->validate([
            'nama_petugas_uks' => 'required|string|max:50',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:15',
            'status_aktif' => 'boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Format nomor telepon dengan +62 jika ada
        $no_telp = $request->no_telp;
        if (!empty($no_telp)) {
            // Nomor telepon akan diformat oleh mutator di model
        }

        $data = [
            'nama_petugas_uks' => $request->nama_petugas_uks,
            'alamat' => $request->alamat,
            'no_telp' => $no_telp,
            'status_aktif' => $request->status_aktif ?? $petugasUKS->status_aktif,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugasUKS->update($data);

        return redirect()->route('petugasuks.index')
            ->with('success', 'Petugas UKS berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $nip
     * @return \Illuminate\Http\Response
     */
    public function destroy($nip)
    {
        $petugasUKS = PetugasUKS::findOrFail($nip);
        $petugasUKS->delete();

        return redirect()->route('petugasuks.index')
            ->with('success', 'Petugas UKS berhasil dihapus.');
    }
    
    /**
     * Export data petugas UKS ke Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $fileName = 'petugas_uks_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PetugasUksExport($request->all()), $fileName);
    }
}