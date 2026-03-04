<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pengajar;
use App\Models\KelasSantri;

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
            ->where('status', 'aktif')
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
    public function getAktifTahunAjaran(Request $request)
    {
        $results = TahunAjaran::where('is_active', '=', 1)
            ->orderByDesc('nama')
            ->limit(20)
            ->get(['id', 'nama'])
            ->map(fn($t) => [
                'id'   => $t->id,
                'text' => $t->nama,
            ]);

        return response()->json(['results' => $results]);
    }

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

    // ── GET /kelas/{id}/santri ────────────────────────────────────
    public function santri(Kelas $kelas)
    {
        $kelas->load(['tahunAjaran', 'waliKelas']);

        $statusOptions = [
            'aktif'  => ['label' => 'Aktif',  'cls' => 'status-aktif'],
            'lulus'  => ['label' => 'Lulus',  'cls' => 'kategori-bulanan'],
            'pindah' => ['label' => 'Pindah', 'cls' => 'kategori-kegiatan'],
            'keluar' => ['label' => 'Keluar', 'cls' => 'status-tidak_aktif'],
        ];

        return view('kelas.santri', compact('kelas', 'statusOptions'));
    }

    // ── GET /kelas/{id}/santri/data  (DataTables AJAX) ───────────
    public function santriData(Request $request, Kelas $kelas)
    {
        $query = KelasSantri::with('santri')
            ->join('santri', 'santri.id', '=', 'kelas_santri.santri_id')
            ->where('kelas_santri.kelas_id', $kelas->id)
            ->select('kelas_santri.*');

        // Status filter
        if ($request->filled('status')) {
            $query->where('kelas_santri.status', $request->status);
        }

        // Global search on santri name / NIS
        if (!empty($request->search['value'])) {
            $s = $request->search['value'];
            $query->where(fn($q) => $q
                ->where('santri.nama_lengkap', 'like', "%{$s}%")
                ->orWhere('santri.nis', 'like', "%{$s}%")
            );
        }

        $total    = KelasSantri::where('kelas_id', $kelas->id)->count();
        $filtered = $query->count();

        // Sortable columns — index matches DataTables column order
        $columns = [
            0 => 'santri.nama_lengkap',
            1 => 'kelas_santri.tanggal_masuk',
            2 => 'kelas_santri.tanggal_keluar',
            3 => 'kelas_santri.tanggal_keluar', // durasi — proxy sort by tanggal_keluar
            4 => 'kelas_santri.keterangan',
            5 => 'kelas_santri.status',
        ];

        $orderColIdx = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'santri.nama_lengkap';

        $query->orderBy($orderCol, $orderDir);

        $data = $query
            ->skip($request->start  ?? 0)
            ->take($request->length ?? 25)
            ->get()
            ->map(fn($ks) => [
                'id'                => $ks->id,
                'santri_id'         => $ks->santri_id,
                'santri_nama'       => $ks->santri?->nama_lengkap,
                'santri_nis'        => $ks->santri?->nis,
                'tanggal_masuk'     => $ks->tanggal_masuk?->format('Y-m-d'),
                'tanggal_masuk_fmt' => $ks->tanggal_masuk?->isoFormat('D MMM YYYY'),
                'tanggal_keluar'    => $ks->tanggal_keluar?->format('Y-m-d'),
                'tanggal_keluar_fmt'=> $ks->tanggal_keluar?->isoFormat('D MMM YYYY'),
                'status'            => $ks->status,
                'status_label'      => $ks->status_label,
                'durasi_label'      => $ks->durasi_label,
                'keterangan'        => $ks->keterangan,
            ]);

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    // ── POST /kelas/{id}/santri  (enroll) ────────────────────────
    public function santriStore(Request $request, Kelas $kelas)
    {
        $validator = Validator::make($request->all(), [
            'santri_id'     => 'required|exists:santri,id',
            'tanggal_masuk' => 'required|date',
            'keterangan'    => 'nullable|string',
        ], [
            'santri_id.required'     => 'Santri harus dipilih',
            'santri_id.exists'       => 'Santri tidak ditemukan',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Guard: already active in THIS kelas
        $alreadyInThisKelas = KelasSantri::where('kelas_id', $kelas->id)
            ->where('santri_id', $request->santri_id)
            ->where('status', 'aktif')
            ->exists();

        if ($alreadyInThisKelas) {
            return response()->json(['success' => false, 'message' => 'Santri sudah terdaftar aktif di kelas ini.'], 422);
        }

        // Guard: already active in another kelas in the same tahun ajaran
        $alreadyElsewhere = KelasSantri::where('santri_id', $request->santri_id)
            ->where('status', 'aktif')
            ->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $kelas->tahun_ajaran_id))
            ->with('kelas')
            ->first();

        if ($alreadyElsewhere) {
            return response()->json([
                'success' => false,
                'message' => "Santri sudah aktif di kelas {$alreadyElsewhere->kelas->nama_kelas} pada tahun ajaran yang sama.",
            ], 422);
        }

        // Guard: kelas is full
        if ($kelas->is_full) {
            return response()->json(['success' => false, 'message' => 'Kelas sudah mencapai kapasitas maksimal.'], 422);
        }

        try {
            $ks = KelasSantri::create([
                'kelas_id'      => $kelas->id,
                'santri_id'     => $request->santri_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status'        => 'aktif',
                'keterangan'    => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Santri berhasil didaftarkan ke kelas.', 'data' => $ks]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ── PATCH /kelas/{id}/santri/{ksId}/exit  (exit santri) ──────
    public function santriExit(Request $request, Kelas $kelas, int $ksId)
    {
        $ks = KelasSantri::where('kelas_id', $kelas->id)->findOrFail($ksId);

        $validator = Validator::make($request->all(), [
            'status'         => 'required|in:lulus,pindah,keluar',
            'tanggal_keluar' => 'required|date|after_or_equal:' . $ks->tanggal_masuk->format('Y-m-d'),
            'keterangan'     => 'nullable|string',
        ], [
            'status.required'         => 'Status keluar harus dipilih',
            'tanggal_keluar.required' => 'Tanggal keluar harus diisi',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar tidak boleh sebelum tanggal masuk',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        if ($ks->status !== 'aktif') {
            return response()->json(['success' => false, 'message' => 'Santri ini sudah tidak aktif di kelas.'], 422);
        }

        try {
            $ks->keluarkan($request->status, $request->keterangan, \Carbon\Carbon::parse($request->tanggal_keluar));
            return response()->json(['success' => true, 'message' => 'Status santri berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ── GET /kelas/{id}/santri/available  (Select2 AJAX) ─────────
    public function santriAvailable(Request $request, Kelas $kelas)
    {
        $q = trim($request->get('q', ''));

        // Exclude santri already active in any kelas this tahun ajaran
        $enrolled = KelasSantri::where('status', 'aktif')
            ->whereHas('kelas', fn($query) => $query->where('tahun_ajaran_id', $kelas->tahun_ajaran_id))
            ->pluck('santri_id');

        $results = \App\Models\Santri::whereNotIn('id', $enrolled)
            ->where(fn($query) => $query
                ->where('nama_lengkap', 'like', "%{$q}%")
                ->orWhere('nis', 'like', "%{$q}%")
            )
            ->orderBy('nama_lengkap')
            ->limit(20)
            ->get(['id', 'nama_lengkap', 'nis'])
            ->map(fn($s) => [
                'id'   => $s->id,
                'text' => "{$s->nis} – {$s->nama_lengkap}",
            ]);

        return response()->json(['results' => $results]);
    }
}