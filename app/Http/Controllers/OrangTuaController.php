<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrangTuaImport;
use App\Exports\OrangTuaExport;
use App\Exports\OrangTuaTemplateExport;
use Illuminate\Support\Facades\DB;

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
                  ->orWhereHas('siswa', function($query) use ($keyword) {
                      $query->where('nama_siswa', 'like', "%{$keyword}%");
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
        return view('orangtua.create', compact('siswas'));
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
            'nama_ayah' => 'required|string|max:100',
            'nama_ibu' => 'required|string|max:100',
            'no_telp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        OrangTua::create($request->all());
        
        return redirect()->route('orangtua.index')
                        ->with('success', 'Data orang tua berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $orangTua = OrangTua::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswas,id_siswa|unique:orang_tuas,id_siswa,'.$orangTua->id_orang_tua.',id_orang_tua',
            'nama_ayah' => 'required|string|max:100',
            'nama_ibu' => 'required|string|max:100',
            'no_telp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        $orangTua->update($request->all());
        
        return redirect()->route('orangtua.index')
                        ->with('success', 'Data orang tua berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $orangTua->delete();
        
        return redirect()->route('orangtua.index')
                        ->with('success', 'Data orang tua berhasil dihapus.');
    }

    /**
     * Show form for importing data
     *
     * @return \Illuminate\Http\Response
     */
    public function importForm()
    {
        return view('orangtua.import');
    }

    /**
     * Process import from Excel/CSV file
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'File import tidak boleh kosong',
            'file.file' => 'Data harus berupa file',
            'file.mimes' => 'Format file harus xlsx, xls, atau csv',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Process import using Laravel Excel
            $import = new OrangTuaImport;
            Excel::import($import, $request->file('file'));
            
            DB::commit();
            
            // Get import statistics if available
            $message = 'Data orang tua berhasil diimport';
            if (method_exists($import, 'getRowCount')) {
                $importCount = $import->getRowCount();
                $updateCount = method_exists($import, 'getUpdateCount') ? $import->getUpdateCount() : 0;
                
                $message = "Data berhasil diimport. {$importCount} data baru ditambahkan";
                if ($updateCount > 0) {
                    $message .= " dan {$updateCount} data diperbarui";
                }
            }
            
            return redirect()->route('orangtua.index')
                            ->with('success', $message);
                            
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            
            $failures = $e->failures();
            $errorMessage = 'Terjadi kesalahan pada baris: ';
            
            foreach ($failures as $failure) {
                $errorMessage .= $failure->row() . ' ('. implode(', ', $failure->errors()) .'), ';
            }
            
            return redirect()->back()
                            ->withErrors(['import_errors' => $errorMessage])
                            ->withInput();
                            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->withErrors(['import_errors' => 'Gagal import data: ' . $e->getMessage()])
                            ->withInput();
        }
    }

    /**
     * Export data to Excel file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        return Excel::download(new OrangTuaExport, 'data-orang-tua-' . date('Y-m-d') . '.xlsx');
    }
    
    /**
     * Download template for import
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        // Menggunakan OrangTuaTemplateExport untuk membuat template
        return Excel::download(new OrangTuaTemplateExport, 'template-import-orangtua.xlsx');
    }
}