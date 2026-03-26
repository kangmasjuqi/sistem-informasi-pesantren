<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pengampu;
use App\Models\Pengajar;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Semester;

class PengampuController extends Controller
{
    public function index()
    {
        $statusOptions  = Pengampu::statusOptions();
        $activeSemester = Semester::where('is_active', 1)->first();

        return view('pengampu.index', compact('statusOptions', 'activeSemester'));
    }

    // ── DataTables AJAX ───────────────────────────────────────────
    public function getData(Request $request)
    {
        $query = Pengampu::with(['pengajar', 'mataPelajaran', 'kelas', 'semester']);

        // ── Filters ───────────────────────────────────────────────
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        if ($request->filled('pengajar_id')) {
            $query->where('pengajar_id', $request->pengajar_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }
        if ($request->filled('status')) {
            $query->where('pengampu.status', $request->status);
        }

        // ── Global search ──────────────────────────────────────────
        if (!empty($request->search['value'])) {
            $s = $request->search['value'];
            $query->where(function ($q) use ($s) {
                $q->whereHas('pengajar',      fn($sq) => $sq->where('nama_lengkap', 'like', "%{$s}%"))
                  ->orWhereHas('mataPelajaran',fn($sq) => $sq->where('nama', 'like', "%{$s}%"))
                  ->orWhereHas('kelas',        fn($sq) => $sq->where('nama_kelas', 'like', "%{$s}%"));
            });
        }

        $totalRecords    = Pengampu::count();
        $filteredRecords = $query->count();

        // ── Sorting ───────────────────────────────────────────────
        $query->join('pengajar',       'pengajar.id',        '=', 'pengampu.pengajar_id')
              ->join('mata_pelajaran', 'mata_pelajaran.id',  '=', 'pengampu.mata_pelajaran_id')
              ->join('kelas',          'kelas.id',           '=', 'pengampu.kelas_id')
              ->select('pengampu.*');

        $columns = [
            0 => 'pengajar.nama_lengkap',
            1 => 'mata_pelajaran.nama',
            2 => 'kelas.nama_kelas',
            3 => 'pengampu.tanggal_mulai',
            4 => 'pengampu.status',
        ];
        $orderColIdx = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'pengajar.nama_lengkap';
        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 25;

        $data = $query->skip($start)->take($length)->get()->map(fn($p) => [
            'id'                  => $p->id,
            'pengajar_id'         => $p->pengajar_id,
            'pengajar_nama'       => $p->pengajar?->nama_lengkap,
            'mata_pelajaran_id'   => $p->mata_pelajaran_id,
            'mata_pelajaran_nama' => $p->mataPelajaran?->nama_mapel,
            'kelas_id'            => $p->kelas_id,
            'kelas_nama'          => $p->kelas?->nama_kelas,
            'semester_id'         => $p->semester_id,
            'semester_nama'       => $p->semester?->nama,
            'tanggal_mulai'       => $p->tanggal_mulai?->format('Y-m-d'),
            'tanggal_mulai_fmt'   => $p->tanggal_mulai?->isoFormat('D MMM YYYY'),
            'tanggal_selesai'     => $p->tanggal_selesai?->format('Y-m-d'),
            'tanggal_selesai_fmt' => $p->tanggal_selesai?->isoFormat('D MMM YYYY'),
            'status'              => $p->status,
            'status_label'        => $p->status_label,
            'status_css'          => $p->status_css,
            'keterangan'          => $p->keterangan,
        ]);

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }

    // ── Batch store (core UX feature) ─────────────────────────────
    public function batchStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pengajar_id'              => 'required|exists:pengajar,id',
            'semester_id'              => 'required|exists:semester,id',
            'keterangan'               => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.kelas_id'         => 'required|exists:kelas,id',
            'items.*.mata_pelajaran_id'=> 'required|exists:mata_pelajaran,id',
            'items.*.tanggal_mulai'    => 'required|date',
            'items.*.tanggal_selesai'  => 'nullable|date|after_or_equal:items.*.tanggal_mulai',
        ], [
            'pengajar_id.required'               => 'Pengajar harus dipilih',
            'semester_id.required'               => 'Semester harus dipilih',
            'items.required'                     => 'Minimal 1 penugasan harus ditambahkan',
            'items.*.kelas_id.required'          => 'Kelas harus dipilih',
            'items.*.mata_pelajaran_id.required' => 'Mata pelajaran harus dipilih',
            'items.*.tanggal_mulai.required'     => 'Tanggal mulai harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $result = Pengampu::batchAssign(
                $request->pengajar_id,
                $request->semester_id,
                $request->items,
                $request->keterangan
            );

            DB::commit();

            $createdCount = count($result['created']);
            $skippedCount = count($result['skipped']);

            $message = "{$createdCount} penugasan berhasil disimpan.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} diabaikan karena sudah ada.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'created' => $createdCount,
                'skipped' => $skippedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ── Single record show (for edit modal) ───────────────────────
    public function show($id)
    {
        $p = Pengampu::with(['pengajar', 'mataPelajaran', 'kelas', 'semester'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $p->id,
                'pengajar_id'         => $p->pengajar_id,
                'pengajar_nama'       => $p->pengajar?->nama_lengkap,
                'mata_pelajaran_id'   => $p->mata_pelajaran_id,
                'mata_pelajaran_nama' => $p->mataPelajaran?->nama_mapel,
                'kelas_id'            => $p->kelas_id,
                'kelas_nama'          => $p->kelas?->nama_kelas,
                'semester_id'         => $p->semester_id,
                'semester_nama'       => $p->semester?->nama,
                'tanggal_mulai'       => $p->tanggal_mulai?->format('Y-m-d'),
                'tanggal_selesai'     => $p->tanggal_selesai?->format('Y-m-d'),
                'status'              => $p->status,
                'keterangan'          => $p->keterangan,
            ],
        ]);
    }

    // ── Single record update ──────────────────────────────────────
    public function update(Request $request, $id)
    {
        $pengampu  = Pengampu::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'pengajar_id'       => 'required|exists:pengajar,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id'          => 'required|exists:kelas,id',
            'semester_id'       => 'required|exists:semester,id',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'nullable|date|after_or_equal:tanggal_mulai',
            'status'            => 'required|in:aktif,selesai,diganti',
            'keterangan'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Check duplicate (excluding self)
        if (Pengampu::isDuplicate(
            $request->pengajar_id,
            $request->mata_pelajaran_id,
            $request->kelas_id,
            $request->semester_id,
            $id
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi pengajar, mata pelajaran, kelas, dan semester sudah ada.',
            ], 422);
        }

        try {
            $pengampu->update([
                'pengajar_id'       => $request->pengajar_id,
                'mata_pelajaran_id' => $request->mata_pelajaran_id,
                'kelas_id'          => $request->kelas_id,
                'semester_id'       => $request->semester_id,
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
                'status'            => $request->status,
                'keterangan'        => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Data pengampu berhasil diperbarui', 'data' => $pengampu]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ── Delete ────────────────────────────────────────────────────
    public function destroy($id)
    {
        $pengampu = Pengampu::findOrFail($id);

        try {
            $pengampu->delete();
            return response()->json(['success' => true, 'message' => 'Data pengampu berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    // ── Select2 AJAX endpoints ────────────────────────────────────

    public function searchPengajar(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = Pengajar::where('nama_lengkap', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->orderBy('nama_lengkap')
            ->limit(20)
            ->get(['id', 'nama_lengkap', 'nip'])
            ->map(fn($p) => [
                'id'   => $p->id,
                'text' => ($p->nip ? "{$p->nip} – " : '') . $p->nama_lengkap,
            ]);

        return response()->json(['results' => $results]);
    }

    public function searchMataPelajaran(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = MataPelajaran::where('nama_mapel', 'like', "%{$q}%")
            ->orWhere('kode_mapel', 'like', "%{$q}%")
            ->orderBy('nama_mapel')
            ->limit(20)
            ->get(['id', 'nama_mapel', 'kode_mapel'])
            ->map(fn($m) => [
                'id'   => $m->id,
                'text' => ($m->kode_mapel ? "[{$m->kode_mapel}] " : '') . $m->nama_mapel,
            ]);

        return response()->json(['results' => $results]);
    }

    public function searchKelas(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = Kelas::where('nama_kelas', 'like', "%{$q}%")
            ->aktif()
            ->orderBy('nama_kelas')
            ->limit(20)
            ->get(['id', 'nama_kelas', 'tingkat'])
            ->map(fn($k) => [
                'id'   => $k->id,
                'text' => "Kelas {$k->nama_kelas} (Tingkat {$k->tingkat})",
            ]);

        return response()->json(['results' => $results]);
    }

    public function searchSemester(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = Semester::where('nama', 'like', "%{$q}%")
            ->orderByDesc('nama')
            ->limit(10)
            ->get(['id', 'nama', 'is_active'])
            ->map(fn($s) => [
                'id'   => $s->id,
                'text' => $s->nama . ($s->is_active ? ' ★' : ''),
            ]);

        return response()->json(['results' => $results]);
    }
}