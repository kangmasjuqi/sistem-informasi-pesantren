<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GedungController extends Controller
{
    public function index()
    {
        return view('gedung.index');
    }

    public function getData(Request $request)
    {
        $query = Gedung::query();

        // Global search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('kode_gedung', 'like', "%{$search}%")
                  ->orWhere('nama_gedung', 'like', "%{$search}%")
                  ->orWhere('alamat_lokasi', 'like', "%{$search}%");
            });
        }

        // Column filters
        if (!empty($request->kode_gedung)) {
            $query->where('kode_gedung', 'like', "%{$request->kode_gedung}%");
        }
        if (!empty($request->nama_gedung)) {
            $query->where('nama_gedung', 'like', "%{$request->nama_gedung}%");
        }
        if (!empty($request->jenis_gedung)) {
            $query->where('jenis_gedung', $request->jenis_gedung);
        }
        if (!empty($request->kondisi)) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->is_active !== null && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $totalRecords    = Gedung::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns     = ['id', 'kode_gedung', 'nama_gedung', 'jenis_gedung', 'jumlah_lantai', 'kapasitas_total', 'kondisi', 'is_active'];
        $orderColIdx = $request->order[0]['column'] ?? 2;
        $orderDir    = $request->order[0]['dir'] ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nama_gedung';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($g) => [
            'id'              => $g->id,
            'kode_gedung'     => $g->kode_gedung,
            'nama_gedung'     => $g->nama_gedung,
            'jenis_gedung'    => $g->jenis_gedung,
            'jenis_label'     => $g->jenis_label,
            'jumlah_lantai'   => $g->jumlah_lantai,
            'kapasitas_total' => $g->kapasitas_total,
            'alamat_lokasi'   => $g->alamat_lokasi,
            'tahun_dibangun'  => $g->tahun_dibangun,
            'kondisi'         => $g->kondisi,
            'kondisi_label'   => $g->kondisi_label,
            'fasilitas'       => $g->fasilitas ?? [],
            'keterangan'      => $g->keterangan,
            'is_active'       => $g->is_active,
            'created_at'      => $g->created_at?->format('d M Y H:i'),
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
            $gedung = Gedung::create([
                'kode_gedung'     => strtoupper($request->kode_gedung),
                'nama_gedung'     => $request->nama_gedung,
                'jenis_gedung'    => $request->jenis_gedung,
                'jumlah_lantai'   => $request->jumlah_lantai,
                'kapasitas_total' => $request->kapasitas_total,
                'alamat_lokasi'   => $request->alamat_lokasi,
                'tahun_dibangun'  => $request->tahun_dibangun,
                'kondisi'         => $request->kondisi,
                'fasilitas'       => $request->fasilitas ? array_filter(array_map('trim', explode(',', $request->fasilitas))) : null,
                'keterangan'      => $request->keterangan,
                'is_active'       => $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Gedung berhasil ditambahkan', 'data' => $gedung]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $g = Gedung::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $g->id,
                'kode_gedung'     => $g->kode_gedung,
                'nama_gedung'     => $g->nama_gedung,
                'jenis_gedung'    => $g->jenis_gedung,
                'jumlah_lantai'   => $g->jumlah_lantai,
                'kapasitas_total' => $g->kapasitas_total,
                'alamat_lokasi'   => $g->alamat_lokasi,
                'tahun_dibangun'  => $g->tahun_dibangun,
                'kondisi'         => $g->kondisi,
                'fasilitas'       => $g->fasilitas ? implode(', ', $g->fasilitas) : '',
                'keterangan'      => $g->keterangan,
                'is_active'       => $g->is_active,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $gedung = Gedung::findOrFail($id);

        $rules = $this->rules();
        $rules['kode_gedung'] = ['required', 'string', 'max:20', Rule::unique('gedung')->ignore($gedung->id)];

        $validator = Validator::make($request->all(), $rules, $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $gedung->update([
                'kode_gedung'     => strtoupper($request->kode_gedung),
                'nama_gedung'     => $request->nama_gedung,
                'jenis_gedung'    => $request->jenis_gedung,
                'jumlah_lantai'   => $request->jumlah_lantai,
                'kapasitas_total' => $request->kapasitas_total,
                'alamat_lokasi'   => $request->alamat_lokasi,
                'tahun_dibangun'  => $request->tahun_dibangun,
                'kondisi'         => $request->kondisi,
                'fasilitas'       => $request->fasilitas ? array_filter(array_map('trim', explode(',', $request->fasilitas))) : null,
                'keterangan'      => $request->keterangan,
                'is_active'       => $request->is_active,
            ]);

            return response()->json(['success' => true, 'message' => 'Gedung berhasil diperbarui', 'data' => $gedung]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $gedung = Gedung::findOrFail($id);

        try {
            $gedung->delete();
            return response()->json(['success' => true, 'message' => 'Gedung berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules(): array
    {
        return [
            'kode_gedung'     => 'required|string|max:20|unique:gedung,kode_gedung',
            'nama_gedung'     => 'required|string|max:255',
            'jenis_gedung'    => 'required|in:asrama_putra,asrama_putri,kelas,serbaguna,masjid,kantor,perpustakaan,lab,dapur,lainnya',
            'jumlah_lantai'   => 'required|integer|min:1|max:99',
            'kapasitas_total' => 'nullable|integer|min:1',
            'alamat_lokasi'   => 'nullable|string',
            'tahun_dibangun'  => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'kondisi'         => 'required|in:baik,rusak_ringan,rusak_berat',
            'fasilitas'       => 'nullable|string',
            'keterangan'      => 'nullable|string',
            'is_active'       => 'required|boolean',
        ];
    }

    private function messages(): array
    {
        return [
            'kode_gedung.required'  => 'Kode gedung harus diisi',
            'kode_gedung.unique'    => 'Kode gedung sudah digunakan',
            'nama_gedung.required'  => 'Nama gedung harus diisi',
            'jenis_gedung.required' => 'Jenis gedung harus dipilih',
            'jenis_gedung.in'       => 'Jenis gedung tidak valid',
            'jumlah_lantai.required'=> 'Jumlah lantai harus diisi',
            'jumlah_lantai.min'     => 'Jumlah lantai minimal 1',
            'kondisi.required'      => 'Kondisi gedung harus dipilih',
            'kondisi.in'            => 'Kondisi tidak valid',
            'tahun_dibangun.digits' => 'Tahun harus 4 digit',
            'is_active.required'    => 'Status harus dipilih',
        ];
    }
}