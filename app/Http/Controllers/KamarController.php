<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Gedung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KamarController extends Controller
{
    public function index()
    {
        $gedungs = Gedung::active()->orderBy('nama_gedung')->get(['id', 'nama_gedung', 'jenis_gedung']);
        return view('kamar.index', compact('gedungs'));
    }

    public function getData(Request $request)
    {
        $query = Kamar::with(['gedung', 'penghuniAktif']);

        // Global search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%")
                  ->orWhere('nama_kamar', 'like', "%{$search}%")
                  ->orWhereHas('gedung', fn($g) => $g->where('nama_gedung', 'like', "%{$search}%"));
            });
        }

        // Column filters
        if (!empty($request->gedung_id)) {
            $query->where('gedung_id', $request->gedung_id);
        }
        if (!empty($request->nomor_kamar)) {
            $query->where('nomor_kamar', 'like', "%{$request->nomor_kamar}%");
        }
        if (!empty($request->lantai)) {
            $query->where('lantai', $request->lantai);
        }
        if (!empty($request->kondisi)) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $totalRecords    = Kamar::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns     = ['id', 'gedung_id', 'nomor_kamar', 'lantai', 'kapasitas', 'kondisi', 'is_active'];
        $orderColIdx = $request->order[0]['column'] ?? 2;
        $orderDir    = $request->order[0]['dir'] ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nomor_kamar';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(function ($k) {
            $penghuniCount = $k->penghuniAktif->count();
            $sisa          = max(0, $k->kapasitas - $penghuniCount);

            return [
                'id'             => $k->id,
                'gedung_id'      => $k->gedung_id,
                'gedung_nama'    => $k->gedung?->nama_gedung ?? '—',
                'nomor_kamar'    => $k->nomor_kamar,
                'nama_kamar'     => $k->nama_kamar,
                'lantai'         => $k->lantai,
                'kapasitas'      => $k->kapasitas,
                'penghuni'       => $penghuniCount,
                'sisa_kapasitas' => $sisa,
                'luas'           => $k->luas,
                'fasilitas'      => $k->fasilitas ?? [],
                'kondisi'        => $k->kondisi,
                'kondisi_label'  => $k->kondisi_label,
                'is_active'      => $k->is_active,
                'keterangan'     => $k->keterangan,
                'created_at'     => $k->created_at?->format('d M Y H:i'),
            ];
        });

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $formatted,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $kamar = Kamar::create([
                'gedung_id'    => $request->gedung_id,
                'nomor_kamar'  => $request->nomor_kamar,
                'nama_kamar'   => $request->nama_kamar,
                'lantai'       => $request->lantai,
                'kapasitas'    => $request->kapasitas,
                'luas'         => $request->luas,
                'fasilitas'    => $request->fasilitas ? array_filter(array_map('trim', explode(',', $request->fasilitas))) : null,
                'kondisi'      => $request->kondisi,
                'is_active'    => $request->is_active,
                'keterangan'   => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Kamar berhasil ditambahkan', 'data' => $kamar->load('gedung')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $k = Kamar::with('gedung')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $k->id,
                'gedung_id'    => $k->gedung_id,
                'nomor_kamar'  => $k->nomor_kamar,
                'nama_kamar'   => $k->nama_kamar,
                'lantai'       => $k->lantai,
                'kapasitas'    => $k->kapasitas,
                'luas'         => $k->luas,
                'fasilitas'    => $k->fasilitas ? implode(', ', $k->fasilitas) : '',
                'kondisi'      => $k->kondisi,
                'is_active'    => $k->is_active,
                'keterangan'   => $k->keterangan,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $rules = $this->rules();
        // Unique constraint on (gedung_id, nomor_kamar) — ignore current record
        $rules['nomor_kamar'] = [
            'required', 'string', 'max:20',
            Rule::unique('kamar')->where(fn($q) => $q->where('gedung_id', $request->gedung_id))->ignore($kamar->id),
        ];

        $validator = Validator::make($request->all(), $rules, $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $kamar->update([
                'gedung_id'   => $request->gedung_id,
                'nomor_kamar' => $request->nomor_kamar,
                'nama_kamar'  => $request->nama_kamar,
                'lantai'      => $request->lantai,
                'kapasitas'   => $request->kapasitas,
                'luas'        => $request->luas,
                'fasilitas'   => $request->fasilitas ? array_filter(array_map('trim', explode(',', $request->fasilitas))) : null,
                'kondisi'     => $request->kondisi,
                'is_active'   => $request->is_active,
                'keterangan'  => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Kamar berhasil diperbarui', 'data' => $kamar]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);

        // Warn if there are active occupants
        if ($kamar->penghuniAktif()->exists()) {
            return response()->json(['success' => false, 'message' => 'Kamar memiliki penghuni aktif. Pindahkan penghuni terlebih dahulu.'], 422);
        }

        try {
            $kamar->delete();
            return response()->json(['success' => true, 'message' => 'Kamar berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules(): array
    {
        return [
            'gedung_id'   => 'required|exists:gedung,id',
            'nomor_kamar' => 'required|string|max:20',
            'nama_kamar'  => 'nullable|string|max:100',
            'lantai'      => 'required|integer|min:1',
            'kapasitas'   => 'required|integer|min:1',
            'luas'        => 'nullable|numeric|min:0',
            'fasilitas'   => 'nullable|string',
            'kondisi'     => 'required|in:baik,rusak_ringan,rusak_berat',
            'is_active'   => 'required|boolean',
            'keterangan'  => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'gedung_id.required'  => 'Gedung harus dipilih',
            'gedung_id.exists'    => 'Gedung tidak ditemukan',
            'nomor_kamar.required'=> 'Nomor kamar harus diisi',
            'lantai.required'     => 'Lantai harus diisi',
            'kapasitas.required'  => 'Kapasitas harus diisi',
            'kapasitas.min'       => 'Kapasitas minimal 1',
            'kondisi.required'    => 'Kondisi harus dipilih',
            'is_active.required'  => 'Status harus dipilih',
        ];
    }
}