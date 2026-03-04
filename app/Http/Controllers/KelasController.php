<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pengajar;

class KelasController extends Controller
{
    public function index()
    {
        return view('kelas.index', [
            'tingkatOptions' => Kelas::tingkatOptions(),
            'statusOptions'  => Kelas::statusOptions(),
        ]);
    }

    public function getData(Request $request)
    {
        $query = Kelas::with(['tahunAjaran', 'waliKelas']);

        // Global search
        if (!empty($request->search['value'])) {
            $s = $request->search['value'];
            $query->where(function ($q) use ($s) {
                $q->where('nama_kelas', 'like', "%{$s}%")
                  ->orWhere('tingkat', 'like', "%{$s}%")
                  ->orWhereHas('tahunAjaran', fn($sq) => $sq->where('nama', 'like', "%{$s}%"))
                  ->orWhereHas('waliKelas', fn($sq) => $sq->where('nama_lengkap', 'like', "%{$s}%"));
            });
        }

        // Column filters
        if (!empty($request->tahun_ajaran_id)) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }
        if (!empty($request->tingkat)) {
            $query->where('tingkat', $request->tingkat);
        }
        if (!empty($request->nama_kelas)) {
            $query->where('nama_kelas', 'like', "%{$request->nama_kelas}%");
        }
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $totalRecords    = Kelas::count();
        $filteredRecords = $query->count();

        $columns     = ['id', 'tahun_ajaran_id', 'nama_kelas', 'tingkat', 'kapasitas', 'wali_kelas_id', 'status'];
        $orderColIdx = $request->order[0]['column'] ?? 2;
        $orderDir    = $request->order[0]['dir']    ?? 'desc';
        $orderCol    = $columns[$orderColIdx] ?? 'tahun_ajaran_id';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(function ($k) {
            $jumlah = $k->santriAktif()->count();
            return [
                'id'                  => $k->id,
                'tahun_ajaran_id'     => $k->tahun_ajaran_id,
                'tahun_ajaran_nama'   => $k->tahunAjaran?->nama,
                'wali_kelas_id'       => $k->wali_kelas_id,
                'wali_kelas_nama'     => $k->waliKelas?->nama_lengkap,
                'nama_kelas'          => $k->nama_kelas,
                'tingkat'             => $k->tingkat,
                'tingkat_label'       => $k->tingkat_label,
                'kapasitas'           => $k->kapasitas,
                'jumlah_santri'       => $jumlah,
                'sisa_kapasitas'      => max(0, $k->kapasitas - $jumlah),
                'is_full'             => $jumlah >= $k->kapasitas,
                'deskripsi'           => $k->deskripsi,
                'status'              => $k->status,
                'status_label'        => $k->status_label
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
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $kelas = Kelas::create([
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'wali_kelas_id'   => $request->wali_kelas_id ?: null,
                'nama_kelas'      => $request->nama_kelas,
                'tingkat'         => $request->tingkat,
                'kapasitas'       => $request->kapasitas ?? 30,
                'deskripsi'       => $request->deskripsi,
                'status'          => $request->status ?? 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil ditambahkan',
                'data'    => $kelas->load(['tahunAjaran', 'waliKelas']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $k = Kelas::with(['tahunAjaran', 'waliKelas'])->findOrFail($id);
        $jumlah = $k->santriAktif()->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $k->id,
                'tahun_ajaran_id'   => $k->tahun_ajaran_id,
                'tahun_ajaran_nama' => $k->tahunAjaran?->nama,
                'wali_kelas_id'     => $k->wali_kelas_id,
                'wali_kelas_nama'   => $k->waliKelas?->nama_lengkap,
                'nama_kelas'        => $k->nama_kelas,
                'tingkat'           => $k->tingkat,
                'kapasitas'         => $k->kapasitas,
                'jumlah_santri'     => $jumlah,
                'deskripsi'         => $k->deskripsi,
                'status'            => $k->status,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $kelas     = Kelas::findOrFail($id);
        $validator = Validator::make($request->all(), $this->rules($id), $this->messages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $kelas->update([
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'wali_kelas_id'   => $request->wali_kelas_id ?: null,
                'nama_kelas'      => $request->nama_kelas,
                'tingkat'         => $request->tingkat,
                'kapasitas'       => $request->kapasitas ?? 30,
                'deskripsi'       => $request->deskripsi,
                'status'          => $request->status ?? 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil diperbarui',
                'data'    => $kelas->load(['tahunAjaran', 'waliKelas']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        try {
            // Guard: kelas still has active santri
            if ($kelas->santriAktif()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas tidak dapat dihapus karena masih memiliki santri aktif.',
                ], 422);
            }

            $kelas->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data kelas berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function rules(int $exceptId = null): array
    {
        return [
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'wali_kelas_id'   => 'nullable|exists:pengajar,id',
            'nama_kelas'      => 'required|string|max:100',
            'tingkat'         => 'required|string|max:20',
            'kapasitas'       => 'nullable|integer|min:1|max:200',
            'deskripsi'       => 'nullable|string',
            'status'          => 'nullable|in:active,inactive,completed',
        ];
    }

    private function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
            'tahun_ajaran_id.exists'   => 'Tahun ajaran tidak ditemukan',
            'nama_kelas.required'      => 'Nama kelas harus diisi',
            'tingkat.required'         => 'Tingkat harus diisi',
            'kapasitas.min'            => 'Kapasitas minimal 1',
        ];
    }

    // ── Select2 AJAX: search pengajar (wali kelas) ────────────────
    public function searchPengajar(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = Pengajar::where('nama_lengkap', 'like', "%{$q}%")
            ->orderBy('nama_lengkap')
            ->limit(20)
            ->get(['id', 'nama_lengkap', 'nip'])
            ->map(fn($p) => [
                'id'   => $p->id,
                'text' => ($p->nip ? "{$p->nip} – " : '') . $p->nama_lengkap,
            ]);

        return response()->json(['results' => $results]);
    }

    // ── Select2 AJAX: search tahun ajaran ─────────────────────────
    public function searchTahunAjaran(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = TahunAjaran::where('nama', 'like', "%{$q}%")
            ->orderByDesc('nama')
            ->limit(20)
            ->get(['id', 'nama'])
            ->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->nama,
            ]);

        return response()->json(['results' => $results]);
    }
}