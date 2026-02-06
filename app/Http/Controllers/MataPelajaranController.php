<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    /**
     * Display the mata pelajaran index page
     */
    public function index()
    {
        return view('mata-pelajaran.index');
    }

    /**
     * Get mata pelajaran data for DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        $query = MataPelajaran::query();

        // Search functionality
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('kode_mapel', 'like', "%{$searchValue}%")
                  ->orWhere('nama_mapel', 'like', "%{$searchValue}%")
                  ->orWhere('deskripsi', 'like', "%{$searchValue}%");
            });
        }

        // Column filters
        if ($request->has('kode_mapel') && $request->kode_mapel) {
            $query->where('kode_mapel', 'like', "%{$request->kode_mapel}%");
        }

        if ($request->has('nama_mapel') && $request->nama_mapel) {
            $query->where('nama_mapel', 'like', "%{$request->nama_mapel}%");
        }

        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }

        // Get total records before pagination
        $totalRecords = MataPelajaran::count();
        $filteredRecords = $query->count();

        // Ordering
        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'asc';
        
        $columns = ['id', 'kode_mapel', 'nama_mapel', 'kategori', 'bobot_sks', 'is_active'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'kode_mapel';
        
        $query->orderBy($orderColumn, $orderDirection);

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        
        $data = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $formattedData = $data->map(function($mapel) {
            return [
                'id' => $mapel->id,
                'kode_mapel' => $mapel->kode_mapel,
                'nama_mapel' => $mapel->nama_mapel,
                'kategori' => $mapel->kategori,
                'bobot_sks' => $mapel->bobot_sks,
                'deskripsi' => $mapel->deskripsi,
                'is_active' => $mapel->is_active,
                'created_at' => $mapel->created_at->format('d M Y H:i'),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ]);
    }

    /**
     * Store a newly created mata pelajaran
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            'kategori' => 'required|in:agama,umum,keterampilan,ekstrakurikuler',
            'bobot_sks' => 'required|integer|min:1|max:10',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran harus diisi',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan',
            'nama_mapel.required' => 'Nama mata pelajaran harus diisi',
            'kategori.required' => 'Kategori harus dipilih',
            'bobot_sks.required' => 'Bobot SKS harus diisi',
            'bobot_sks.min' => 'Bobot SKS minimal 1',
            'bobot_sks.max' => 'Bobot SKS maksimal 10',
            'is_active.required' => 'Status harus dipilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mataPelajaran = MataPelajaran::create([
                'kode_mapel' => strtoupper($request->kode_mapel),
                'nama_mapel' => $request->nama_mapel,
                'kategori' => $request->kategori,
                'bobot_sks' => $request->bobot_sks,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil ditambahkan',
                'data' => $mataPelajaran
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified mata pelajaran
     */
    public function show($id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $mataPelajaran->id,
                'kode_mapel' => $mataPelajaran->kode_mapel,
                'nama_mapel' => $mataPelajaran->nama_mapel,
                'kategori' => $mataPelajaran->kategori,
                'bobot_sks' => $mataPelajaran->bobot_sks,
                'deskripsi' => $mataPelajaran->deskripsi,
                'is_active' => $mataPelajaran->is_active,
            ]
        ]);
    }

    /**
     * Update the specified mata pelajaran
     */
    public function update(Request $request, $id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique('mata_pelajaran')->ignore($mataPelajaran->id)],
            'nama_mapel' => 'required|string|max:100',
            'kategori' => 'required|in:agama,umum,keterampilan,ekstrakurikuler',
            'bobot_sks' => 'required|integer|min:1|max:10',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran harus diisi',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan',
            'nama_mapel.required' => 'Nama mata pelajaran harus diisi',
            'kategori.required' => 'Kategori harus dipilih',
            'bobot_sks.required' => 'Bobot SKS harus diisi',
            'bobot_sks.min' => 'Bobot SKS minimal 1',
            'bobot_sks.max' => 'Bobot SKS maksimal 10',
            'is_active.required' => 'Status harus dipilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mataPelajaran->update([
                'kode_mapel' => strtoupper($request->kode_mapel),
                'nama_mapel' => $request->nama_mapel,
                'kategori' => $request->kategori,
                'bobot_sks' => $request->bobot_sks,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil diperbarui',
                'data' => $mataPelajaran
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified mata pelajaran
     */
    // public function destroy($id)
    // {
    //     try {
    //         $mataPelajaran = MataPelajaran::findOrFail($id);
            
    //         // Check if mata pelajaran is being used
    //         // Add your relationship checks here if needed
    //         // Example: if ($mataPelajaran->nilai()->exists()) { ... }

    //         $mataPelajaran->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Mata pelajaran berhasil dihapus'
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}