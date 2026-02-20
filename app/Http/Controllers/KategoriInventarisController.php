<?php

namespace App\Http\Controllers;

use App\Models\KategoriInventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KategoriInventarisController extends Controller
{
    public function index()
    {
        return view('kategori-inventaris.index');
    }

    public function getData(Request $request)
    {
        $query = KategoriInventaris::query();

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

        $totalRecords    = KategoriInventaris::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns     = ['id', 'kode', 'nama', 'is_active'];
        $orderColIdx = $request->order[0]['column'] ?? 1;
        $orderDir    = $request->order[0]['dir'] ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nama';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($ki) => [
            'id'        => $ki->id,
            'kode'      => $ki->kode,
            'nama'      => $ki->nama,
            'deskripsi' => $ki->deskripsi,
            'is_active' => $ki->is_active,
            'created_at'=> $ki->created_at?->format('d M Y H:i'),
        ]);

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $formatted,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode'      => 'required|string|max:20|unique:kategori_inventaris,kode',
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $ki = KategoriInventaris::create([
                'kode'      => strtoupper($request->kode),
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Kategori inventaris berhasil ditambahkan', 'data' => $ki]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $ki = KategoriInventaris::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'        => $ki->id,
                'kode'      => $ki->kode,
                'nama'      => $ki->nama,
                'deskripsi' => $ki->deskripsi,
                'is_active' => $ki->is_active,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $ki = KategoriInventaris::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode'      => ['required', 'string', 'max:20', Rule::unique('kategori_inventaris')->ignore($ki->id)],
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $ki->update([
                'kode'      => strtoupper($request->kode),
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Kategori inventaris berhasil diperbarui', 'data' => $ki]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $ki = KategoriInventaris::findOrFail($id);

        try {
            $ki->delete();
            return response()->json(['success' => true, 'message' => 'Kategori inventaris berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function messages(): array
    {
        return [
            'kode.required'      => 'Kode harus diisi',
            'kode.unique'        => 'Kode sudah digunakan',
            'nama.required'      => 'Nama kategori harus diisi',
            'is_active.required' => 'Status harus dipilih',
        ];
    }
}