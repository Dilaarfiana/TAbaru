<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanAwal;
use App\Models\DetailPemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemeriksaanAwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Membuat query dasar
        $query = PemeriksaanAwal::with('detailPemeriksaan');
        
        // Pencarian berdasarkan keyword
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Id_PreAwal', 'like', "%$search%")
                  ->orWhere('Id_DetPrx', 'like', "%$search%")
                  ->orWhere('Keluhan_Dahulu', 'like', "%$search%");
            });
        }
        
        // Filter berdasarkan status nyeri
        if ($request->has('status') && $request->status != '') {
            $query->where('Status_Nyeri', $request->status);
        }
        
        // Menambahkan pengurutan
        $query->orderBy('Id_PreAwal', 'desc');
        
        // Mengambil data dengan pagination
        $pemeriksaanAwals = $query->paginate(10);
        $pemeriksaanAwals->appends($request->all()); // Mempertahankan parameter URL saat paginasi
        
        return view('pemeriksaan_awal.index', compact('pemeriksaanAwals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $detailPemeriksaans = DetailPemeriksaan::all();
        
        // Generate ID dengan format PA001, PA002, dll.
        $lastRecord = PemeriksaanAwal::orderBy('Id_PreAwal', 'desc')->first();
        
        if ($lastRecord) {
            $lastNumber = intval(substr($lastRecord->Id_PreAwal, 2));
            $newNumber = $lastNumber + 1;
            $id = 'PA' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $id = 'PA001';
        }
        
        return view('pemeriksaan_awal.create', compact('detailPemeriksaans', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Id_PreAwal' => 'required|string|max:5|unique:pemeriksaan_awals',
            'Id_DetPrx' => 'required|string|exists:detail_pemeriksaans,Id_DetPrx',
            'Pemeriksaan' => 'nullable|string',
            'Keluhan_Dahulu' => 'nullable|string|max:255',
            'Suhu' => 'nullable|numeric|between:30,45',
            'Nadi' => 'nullable|numeric|between:0,300',
            'Tegangan' => 'nullable|string|max:7',
            'Pernapasan' => 'nullable|integer|between:0,100',
            'Tipe' => 'nullable|integer',
            'Status_Nyeri' => 'nullable|integer|in:0,1,2,3',
            'Karakteristik' => 'nullable|string|max:50',
            'Lokasi' => 'nullable|string|max:50',
            'Durasi' => 'nullable|string|max:30',
            'Frekuensi' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        PemeriksaanAwal::create($request->all());

        return redirect()->route('pemeriksaan_awal.index')
            ->with('success', 'Pemeriksaan Awal berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pemeriksaanAwal = PemeriksaanAwal::with('detailPemeriksaan.siswa', 'detailPemeriksaan.dokter')
            ->findOrFail($id);
        return view('pemeriksaan_awal.show', compact('pemeriksaanAwal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pemeriksaanAwal = PemeriksaanAwal::findOrFail($id);
        $detailPemeriksaans = DetailPemeriksaan::all();
        return view('pemeriksaan_awal.edit', compact('pemeriksaanAwal', 'detailPemeriksaans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'Id_DetPrx' => 'required|string|exists:detail_pemeriksaans,Id_DetPrx',
            'Pemeriksaan' => 'nullable|string',
            'Keluhan_Dahulu' => 'nullable|string|max:255',
            'Suhu' => 'nullable|numeric|between:30,45',
            'Nadi' => 'nullable|numeric|between:0,300',
            'Tegangan' => 'nullable|string|max:7',
            'Pernapasan' => 'nullable|integer|between:0,100',
            'Tipe' => 'nullable|integer',
            'Status_Nyeri' => 'nullable|integer|in:0,1,2,3',
            'Karakteristik' => 'nullable|string|max:50',
            'Lokasi' => 'nullable|string|max:50',
            'Durasi' => 'nullable|string|max:30',
            'Frekuensi' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pemeriksaanAwal = PemeriksaanAwal::findOrFail($id);
        $pemeriksaanAwal->update($request->all());

        return redirect()->route('pemeriksaan_awal.index')
            ->with('success', 'Pemeriksaan Awal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pemeriksaanAwal = PemeriksaanAwal::findOrFail($id);
        $pemeriksaanAwal->delete();

        return redirect()->route('pemeriksaan_awal.index')
            ->with('success', 'Pemeriksaan Awal berhasil dihapus.');
    }
    
    /**
     * Export data to PDF
     */
    public function exportPdf(string $id)
    {
        $pemeriksaanAwal = PemeriksaanAwal::with('detailPemeriksaan.siswa', 'detailPemeriksaan.dokter')
            ->findOrFail($id);
            
        // Logika untuk export PDF akan ditambahkan di sini
        // Contoh menggunakan package dompdf
        
        return redirect()->back()->with('info', 'Fitur export PDF sedang dalam pengembangan.');
    }
    
    /**
     * Get recent pemeriksaan awal for dashboard
     */
    public function getRecent()
    {
        $recentPemeriksaan = PemeriksaanAwal::with('detailPemeriksaan.siswa')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return response()->json($recentPemeriksaan);
    }
}