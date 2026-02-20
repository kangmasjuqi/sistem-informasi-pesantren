<?php

namespace App\Http\Controllers;

use App\Models\KomponenNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KomponenNilaiController extends Controller
{
    /**
     * Display the index page
     */
    public function index()
    {
        return view('komponen-nilai.index');
    }

    /**
     * Get data for DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        $query = KomponenNilai::query();

        // Global search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Column filters
        if (!empty($request->kode)) {
            $query->where('kode', 'like', "%{$request->kode}%");
        }

        if (!empty($request->nama)) {
            $query->where('nama', 'like', "%{$request->nama}%");
        }

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $totalRecords    = KomponenNilai::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns     = ['id', 'kode', 'nama', 'bobot', 'is_active'];
        $orderColIdx = $request->order[0]['column'] ?? 1;
        $orderDir    = $request->order[0]['dir'] ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nama';

        $query->orderBy($orderCol, $orderDir);

        // Pagination
        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($kn) => [
            'id'              => $kn->id,
            'kode'            => $kn->kode,
            'nama'            => $kn->nama,
            'bobot'           => $kn->bobot,
            'bobot_formatted' => $kn->bobot_formatted,
            'deskripsi'       => $kn->deskripsi,
            'is_active'       => $kn->is_active,
            'created_at'      => $kn->created_at?->format('d M Y H:i'),
        ]);

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $formatted,
        ]);
    }

    /**
     * Store a new komponen nilai
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode'      => 'required|string|max:20|unique:komponen_nilai,kode',
            'nama'      => 'required|string|max:100',
            'bobot'     => 'required|integer|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], $this->messages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $kn = KomponenNilai::create([
                'kode'      => strtoupper($request->kode),
                'nama'      => $request->nama,
                'bobot'     => $request->bobot,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Komponen nilai berhasil ditambahkan',
                'data'    => $kn,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show single record
     */
    public function show($id)
    {
        $kn = KomponenNilai::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'        => $kn->id,
                'kode'      => $kn->kode,
                'nama'      => $kn->nama,
                'bobot'     => $kn->bobot,
                'deskripsi' => $kn->deskripsi,
                'is_active' => $kn->is_active,
            ],
        ]);
    }

    /**
     * Update komponen nilai
     */
    public function update(Request $request, $id)
    {
        $kn = KomponenNilai::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode'      => ['required', 'string', 'max:20', Rule::unique('komponen_nilai')->ignore($kn->id)],
            'nama'      => 'required|string|max:100',
            'bobot'     => 'required|integer|min:0|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], $this->messages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $kn->update([
                'kode'      => strtoupper($request->kode),
                'nama'      => $request->nama,
                'bobot'     => $request->bobot,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Komponen nilai berhasil diperbarui',
                'data'    => $kn,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft delete
     */
    // public function destroy($id)
    // {
    //     $kn = KomponenNilai::findOrFail($id);

    //     try {
    //         $kn->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Komponen nilai berhasil dihapus',
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    /**
     * Shared validation messages
     */
    private function messages(): array
    {
        return [
            'kode.required'      => 'Kode harus diisi',
            'kode.unique'        => 'Kode sudah digunakan',
            'nama.required'      => 'Nama komponen harus diisi',
            'bobot.required'     => 'Bobot harus diisi',
            'bobot.integer'      => 'Bobot harus berupa bilangan bulat',
            'bobot.min'          => 'Bobot minimal 0%',
            'bobot.max'          => 'Bobot maksimal 100%',
            'is_active.required' => 'Status harus dipilih',
        ];
    }
}