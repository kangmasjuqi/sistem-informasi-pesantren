<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\KategoriInventaris;
use App\Models\Gedung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InventarisController extends Controller
{
    public function index()
    {
        $kategoris = KategoriInventaris::active()->orderBy('nama')->get(['id', 'nama']);
        $gedungs   = Gedung::active()->orderBy('nama_gedung')->get(['id', 'nama_gedung']);

        return view('inventaris.index', compact('kategoris', 'gedungs'));
    }

    public function getData(Request $request)
    {
        $query = Inventaris::with(['kategori', 'gedung']);

        // Global search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('nomor_seri', 'like', "%{$search}%");
            });
        }

        // Column filters
        if (!empty($request->kode_inventaris)) {
            $query->where('kode_inventaris', 'like', "%{$request->kode_inventaris}%");
        }
        if (!empty($request->nama_barang)) {
            $query->where('nama_barang', 'like', "%{$request->nama_barang}%");
        }
        if (!empty($request->kategori_inventaris_id)) {
            $query->where('kategori_inventaris_id', $request->kategori_inventaris_id);
        }
        if (!empty($request->gedung_id)) {
            $query->where('gedung_id', $request->gedung_id);
        }
        if (!empty($request->kondisi)) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $totalRecords    = Inventaris::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns     = ['id', 'kode_inventaris', 'nama_barang', 'kategori_inventaris_id', 'jumlah', 'kondisi', 'tanggal_perolehan', 'is_active'];
        $orderColIdx = $request->order[0]['column'] ?? 2;
        $orderDir    = $request->order[0]['dir'] ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nama_barang';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($inv) => [
            'id'                           => $inv->id,
            'kode_inventaris'              => $inv->kode_inventaris,
            'nama_barang'                  => $inv->nama_barang,
            'merk'                         => $inv->merk,
            'tipe_model'                   => $inv->tipe_model,
            'kategori_id'                  => $inv->kategori_inventaris_id,
            'kategori_nama'                => $inv->kategori?->nama ?? '—',
            'gedung_id'                    => $inv->gedung_id,
            'gedung_nama'                  => $inv->gedung?->nama_gedung ?? '—',
            'jumlah'                       => $inv->jumlah,
            'satuan'                       => $inv->satuan,
            'kondisi'                      => $inv->kondisi,
            'kondisi_label'                => $inv->kondisi_label,
            'tanggal_perolehan'            => $inv->tanggal_perolehan?->format('Y-m-d'),
            'tanggal_perolehan_fmt'        => $inv->tanggal_perolehan?->format('d M Y'),
            'harga_perolehan'              => $inv->harga_perolehan,
            'harga_formatted'              => $inv->harga_formatted,
            'nilai_penyusutan'             => $inv->nilai_penyusutan,
            'sumber_dana'                  => $inv->sumber_dana,
            'lokasi'                       => $inv->lokasi,
            'spesifikasi'                  => $inv->spesifikasi,
            'nomor_seri'                   => $inv->nomor_seri,
            'tanggal_maintenance_terakhir' => $inv->tanggal_maintenance_terakhir?->format('Y-m-d'),
            'penanggung_jawab'             => $inv->penanggung_jawab,
            'foto'                         => $inv->foto ? Storage::url($inv->foto) : null,
            'is_active'                    => $inv->is_active,
            'keterangan'                   => $inv->keterangan,
            'created_at'                   => $inv->created_at?->format('d M Y H:i'),
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
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('inventaris/foto', 'public');
            }

            $kode = $request->kode_inventaris ?: Inventaris::generateKode();

            $inv = Inventaris::create([
                'kategori_inventaris_id'       => $request->kategori_inventaris_id,
                'gedung_id'                    => $request->gedung_id,
                'kode_inventaris'              => $kode,
                'nama_barang'                  => $request->nama_barang,
                'merk'                         => $request->merk,
                'tipe_model'                   => $request->tipe_model,
                'jumlah'                       => $request->jumlah ?? 1,
                'satuan'                       => $request->satuan ?? 'unit',
                'kondisi'                      => $request->kondisi,
                'tanggal_perolehan'            => $request->tanggal_perolehan,
                'harga_perolehan'              => $request->harga_perolehan ? str_replace(['.', ','], ['', '.'], $request->harga_perolehan) : null,
                'nilai_penyusutan'             => $request->nilai_penyusutan ? str_replace(['.', ','], ['', '.'], $request->nilai_penyusutan) : null,
                'sumber_dana'                  => $request->sumber_dana,
                'lokasi'                       => $request->lokasi,
                'spesifikasi'                  => $request->spesifikasi,
                'nomor_seri'                   => $request->nomor_seri,
                'tanggal_maintenance_terakhir' => $request->tanggal_maintenance_terakhir,
                'penanggung_jawab'             => $request->penanggung_jawab,
                'foto'                         => $fotoPath,
                'is_active'                    => $request->is_active,
                'keterangan'                   => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Inventaris berhasil ditambahkan', 'data' => $inv]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $inv = Inventaris::with(['kategori', 'gedung'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                           => $inv->id,
                'kode_inventaris'              => $inv->kode_inventaris,
                'nama_barang'                  => $inv->nama_barang,
                'merk'                         => $inv->merk,
                'tipe_model'                   => $inv->tipe_model,
                'kategori_inventaris_id'       => $inv->kategori_inventaris_id,
                'gedung_id'                    => $inv->gedung_id,
                'jumlah'                       => $inv->jumlah,
                'satuan'                       => $inv->satuan,
                'kondisi'                      => $inv->kondisi,
                'tanggal_perolehan'            => $inv->tanggal_perolehan?->format('Y-m-d'),
                'harga_perolehan'              => $inv->harga_perolehan,
                'nilai_penyusutan'             => $inv->nilai_penyusutan,
                'sumber_dana'                  => $inv->sumber_dana,
                'lokasi'                       => $inv->lokasi,
                'spesifikasi'                  => $inv->spesifikasi,
                'nomor_seri'                   => $inv->nomor_seri,
                'tanggal_maintenance_terakhir' => $inv->tanggal_maintenance_terakhir?->format('Y-m-d'),
                'penanggung_jawab'             => $inv->penanggung_jawab,
                'foto_url'                     => $inv->foto ? Storage::url($inv->foto) : null,
                'is_active'                    => $inv->is_active,
                'keterangan'                   => $inv->keterangan,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $inv = Inventaris::findOrFail($id);

        $rules                     = $this->rules();
        $rules['kode_inventaris']  = ['required', 'string', 'max:30', Rule::unique('inventaris')->ignore($inv->id)];

        $validator = Validator::make($request->all(), $rules, $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $fotoPath = $inv->foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath) Storage::disk('public')->delete($fotoPath);
                $fotoPath = $request->file('foto')->store('inventaris/foto', 'public');
            }

            $inv->update([
                'kategori_inventaris_id'       => $request->kategori_inventaris_id,
                'gedung_id'                    => $request->gedung_id,
                'kode_inventaris'              => $request->kode_inventaris,
                'nama_barang'                  => $request->nama_barang,
                'merk'                         => $request->merk,
                'tipe_model'                   => $request->tipe_model,
                'jumlah'                       => $request->jumlah ?? 1,
                'satuan'                       => $request->satuan ?? 'unit',
                'kondisi'                      => $request->kondisi,
                'tanggal_perolehan'            => $request->tanggal_perolehan,
                'harga_perolehan'              => $request->harga_perolehan ? str_replace(['.', ','], ['', '.'], $request->harga_perolehan) : null,
                'nilai_penyusutan'             => $request->nilai_penyusutan ? str_replace(['.', ','], ['', '.'], $request->nilai_penyusutan) : null,
                'sumber_dana'                  => $request->sumber_dana,
                'lokasi'                       => $request->lokasi,
                'spesifikasi'                  => $request->spesifikasi,
                'nomor_seri'                   => $request->nomor_seri,
                'tanggal_maintenance_terakhir' => $request->tanggal_maintenance_terakhir,
                'penanggung_jawab'             => $request->penanggung_jawab,
                'foto'                         => $fotoPath,
                'is_active'                    => $request->is_active,
                'keterangan'                   => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Inventaris berhasil diperbarui', 'data' => $inv]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $inv = Inventaris::findOrFail($id);

        try {
            if ($inv->foto) Storage::disk('public')->delete($inv->foto);
            $inv->delete();
            return response()->json(['success' => true, 'message' => 'Inventaris berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules(): array
    {
        return [
            'kategori_inventaris_id'       => 'required|exists:kategori_inventaris,id',
            'gedung_id'                    => 'nullable|exists:gedung,id',
            'kode_inventaris'              => 'nullable|string|max:30|unique:inventaris,kode_inventaris',
            'nama_barang'                  => 'required|string|max:255',
            'merk'                         => 'nullable|string|max:100',
            'tipe_model'                   => 'nullable|string|max:100',
            'jumlah'                       => 'required|integer|min:1',
            'satuan'                       => 'required|string|max:20',
            'kondisi'                      => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'tanggal_perolehan'            => 'required|date',
            'harga_perolehan'              => 'nullable|string',
            'nilai_penyusutan'             => 'nullable|string',
            'sumber_dana'                  => 'nullable|string|max:100',
            'lokasi'                       => 'nullable|string|max:200',
            'spesifikasi'                  => 'nullable|string',
            'nomor_seri'                   => 'nullable|string|max:100',
            'tanggal_maintenance_terakhir' => 'nullable|date',
            'penanggung_jawab'             => 'nullable|string|max:100',
            'foto'                         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'                    => 'required|boolean',
            'keterangan'                   => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'kategori_inventaris_id.required' => 'Kategori harus dipilih',
            'kategori_inventaris_id.exists'   => 'Kategori tidak ditemukan',
            'nama_barang.required'            => 'Nama barang harus diisi',
            'jumlah.required'                 => 'Jumlah harus diisi',
            'jumlah.min'                      => 'Jumlah minimal 1',
            'satuan.required'                 => 'Satuan harus diisi',
            'kondisi.required'                => 'Kondisi harus dipilih',
            'tanggal_perolehan.required'      => 'Tanggal perolehan harus diisi',
            'foto.image'                      => 'File harus berupa gambar',
            'foto.max'                        => 'Ukuran foto maksimal 2MB',
            'is_active.required'              => 'Status harus dipilih',
        ];
    }
}