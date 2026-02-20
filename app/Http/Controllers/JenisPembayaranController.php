<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JenisPembayaranController extends Controller
{
    /**
     * Display the index page
     */
    public function index()
    {
        return view('jenis-pembayaran.index');
    }

    /**
     * Get data for DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        $query = JenisPembayaran::query();

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

        if (!empty($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $totalRecords    = JenisPembayaran::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns      = ['id', 'kode', 'nama', 'kategori', 'nominal', 'is_active'];
        $orderColIdx  = $request->order[0]['column'] ?? 1;
        $orderDir     = $request->order[0]['dir'] ?? 'asc';
        $orderCol     = $columns[$orderColIdx] ?? 'nama';

        $query->orderBy($orderCol, $orderDir);

        // Pagination
        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($jp) => [
            'id'                => $jp->id,
            'kode'              => $jp->kode,
            'nama'              => $jp->nama,
            'kategori'          => $jp->kategori,
            'nominal'           => $jp->nominal,
            'nominal_formatted' => $jp->nominal_formatted,
            'deskripsi'         => $jp->deskripsi,
            'is_active'         => $jp->is_active,
            'created_at'        => $jp->created_at?->format('d M Y H:i'),
        ]);

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $formatted,
        ]);
    }

    /**
     * Store a new jenis pembayaran
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode'      => 'required|string|max:20|unique:jenis_pembayaran,kode',
            'nama'      => 'required|string|max:100',
            'kategori'  => 'required|in:bulanan,tahunan,pendaftaran,kegiatan,lainnya',
            'nominal'   => 'required|numeric|min:0',
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
            $jp = JenisPembayaran::create([
                'kode'      => strtoupper($request->kode),
                'nama'      => $request->nama,
                'kategori'  => $request->kategori,
                'nominal'   => $request->nominal,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jenis pembayaran berhasil ditambahkan',
                'data'    => $jp,
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
        $jp = JenisPembayaran::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'        => $jp->id,
                'kode'      => $jp->kode,
                'nama'      => $jp->nama,
                'kategori'  => $jp->kategori,
                'nominal'   => $jp->nominal,
                'deskripsi' => $jp->deskripsi,
                'is_active' => $jp->is_active,
            ],
        ]);
    }

    /**
     * Update jenis pembayaran
     */
    public function update(Request $request, $id)
    {
        $jp = JenisPembayaran::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode'      => ['required', 'string', 'max:20', Rule::unique('jenis_pembayaran')->ignore($jp->id)],
            'nama'      => 'required|string|max:100',
            'kategori'  => 'required|in:bulanan,tahunan,pendaftaran,kegiatan,lainnya',
            'nominal'   => 'required|numeric|min:0',
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
            $jp->update([
                'kode'      => strtoupper($request->kode),
                'nama'      => $request->nama,
                'kategori'  => $request->kategori,
                'nominal'   => $request->nominal,
                'deskripsi' => $request->deskripsi,
                'is_active' => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jenis pembayaran berhasil diperbarui',
                'data'    => $jp,
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
    //     $jp = JenisPembayaran::findOrFail($id);

    //     try {
    //         $jp->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Jenis pembayaran berhasil dihapus',
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
            'kode.required'     => 'Kode harus diisi',
            'kode.unique'       => 'Kode sudah digunakan',
            'nama.required'     => 'Nama harus diisi',
            'kategori.required' => 'Kategori harus dipilih',
            'kategori.in'       => 'Kategori tidak valid',
            'nominal.required'  => 'Nominal harus diisi',
            'nominal.numeric'   => 'Nominal harus berupa angka',
            'nominal.min'       => 'Nominal tidak boleh negatif',
            'is_active.required'=> 'Status harus dipilih',
        ];
    }
}