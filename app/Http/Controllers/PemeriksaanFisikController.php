<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanFisik;
use App\Models\Pasien; // Tambahkan jika akan menggunakan relasi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemeriksaanFisikController extends Controller
{
    /**
     * Display a listing of the pemeriksaan fisik.
     */
    public function index(Request $request)
    {
        $query = PemeriksaanFisik::query();
        
        // Filter berdasarkan tanggal jika ada
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_pemeriksaan', $request->tanggal);
        }
        
        // Filter berdasarkan pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('id_pasien', 'like', "%{$search}%");
            });
        }
        
        $pemeriksaanFisiks = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10);
        return view('pemeriksaan_fisik.index', compact('pemeriksaanFisiks'));
    }

    /**
     * Show the form for creating a new pemeriksaan fisik.
     */
    public function create()
    {
        // Jika perlu load data pasien untuk dropdown
        // $pasiens = Pasien::where('status_aktif', 1)->get();
        return view('pemeriksaan_fisik.create');
    }

    /**
     * Store a newly created pemeriksaan fisik in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pasien' => 'required',
            'tanggal_pemeriksaan' => 'required|date',
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'berat_badan' => 'nullable|numeric|min:0|max:500',
            'suhu_badan' => 'nullable|numeric|min:20|max:45',
            'tekanan_darah' => 'nullable|string|max:10',
            'keluhan' => 'nullable|string',
            'hasil_pemeriksaan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('pemeriksaan_fisik.create') // Perbaiki nama route dari pemeriksaan-fisik menjadi pemeriksaan_fisik
                ->withErrors($validator)
                ->withInput();
        }

        // Generate new ID
        $newId = PemeriksaanFisik::generateNewId();
        
        // Create new record
        $pemeriksaanFisik = new PemeriksaanFisik();
        $pemeriksaanFisik->id = $newId;
        $pemeriksaanFisik->id_pasien = $request->id_pasien;
        $pemeriksaanFisik->tanggal_pemeriksaan = $request->tanggal_pemeriksaan;
        $pemeriksaanFisik->tinggi_badan = $request->tinggi_badan;
        $pemeriksaanFisik->berat_badan = $request->berat_badan;
        $pemeriksaanFisik->suhu_badan = $request->suhu_badan;
        $pemeriksaanFisik->tekanan_darah = $request->tekanan_darah;
        $pemeriksaanFisik->keluhan = $request->keluhan;
        $pemeriksaanFisik->hasil_pemeriksaan = $request->hasil_pemeriksaan;
        $pemeriksaanFisik->save();

        return redirect()
            ->route('pemeriksaan_fisik.index') // Perbaiki nama route
            ->with('success', 'Data pemeriksaan fisik berhasil disimpan dengan ID: ' . $newId);
    }

    /**
     * Display the specified pemeriksaan fisik.
     */
    public function show(string $id)
    {
        $pemeriksaanFisik = PemeriksaanFisik::findOrFail($id);
        return view('pemeriksaan_fisik.show', compact('pemeriksaanFisik'));
    }

    /**
     * Show the form for editing the specified pemeriksaan fisik.
     */
    public function edit(string $id)
    {
        $pemeriksaanFisik = PemeriksaanFisik::findOrFail($id);
        return view('pemeriksaan_fisik.edit', compact('pemeriksaanFisik'));
    }

    /**
     * Update the specified pemeriksaan fisik in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'id_pasien' => 'required',
            'tanggal_pemeriksaan' => 'required|date',
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'berat_badan' => 'nullable|numeric|min:0|max:500',
            'suhu_badan' => 'nullable|numeric|min:20|max:45',
            'tekanan_darah' => 'nullable|string|max:10',
            'keluhan' => 'nullable|string',
            'hasil_pemeriksaan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('pemeriksaan_fisik.edit', $id) // Perbaiki nama route
                ->withErrors($validator)
                ->withInput();
        }

        $pemeriksaanFisik = PemeriksaanFisik::findOrFail($id);
        $pemeriksaanFisik->id_pasien = $request->id_pasien;
        $pemeriksaanFisik->tanggal_pemeriksaan = $request->tanggal_pemeriksaan;
        $pemeriksaanFisik->tinggi_badan = $request->tinggi_badan;
        $pemeriksaanFisik->berat_badan = $request->berat_badan;
        $pemeriksaanFisik->suhu_badan = $request->suhu_badan;
        $pemeriksaanFisik->tekanan_darah = $request->tekanan_darah;
        $pemeriksaanFisik->keluhan = $request->keluhan;
        $pemeriksaanFisik->hasil_pemeriksaan = $request->hasil_pemeriksaan;
        $pemeriksaanFisik->save();

        return redirect()
            ->route('pemeriksaan_fisik.index') // Perbaiki nama route
            ->with('success', 'Data pemeriksaan fisik berhasil diperbarui.');
    }

    /**
     * Remove the specified pemeriksaan fisik from storage.
     */
    public function destroy(string $id)
    {
        $pemeriksaanFisik = PemeriksaanFisik::findOrFail($id);
        $pemeriksaanFisik->delete();

        return redirect()
            ->route('pemeriksaan_fisik.index') // Perbaiki nama route
            ->with('success', 'Data pemeriksaan fisik berhasil dihapus.');
    }
}