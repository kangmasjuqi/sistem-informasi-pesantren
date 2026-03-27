<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Pengampu;
use App\Models\KomponenNilai;
use App\Models\Semester;

class NilaiController extends Controller
{
    /**
     * Index — entry point: pick your pengampu context.
     */
    public function index()
    {
        $activeSemester = Semester::where('is_active', 1)->first();

        return view('nilai.index', compact('activeSemester'));
    }

    /**
     * GET /nilai/grid?pengampu_id=X
     *
     * Returns the spreadsheet data for a given pengampu:
     * - list of santri (rows)
     * - list of komponen_nilai (columns)
     * - existing nilai keyed by [santri_id][komponen_nilai_id]
     * - per-santri weighted average
     */
    public function grid(Request $request)
    {
        $request->validate(['pengampu_id' => 'required|exists:pengampu,id']);

        $pengampu = Pengampu::with([
            'pengajar',
            'mataPelajaran',
            'kelas',
            'semester',
        ])->findOrFail($request->pengampu_id);

        // Santri aktif in this kelas
        $santriList = DB::table('santri')
            ->join('kelas_santri', 'kelas_santri.santri_id', '=', 'santri.id')
            ->where('kelas_santri.kelas_id', $pengampu->kelas_id)
            ->where('kelas_santri.status', 'aktif')
            ->orderBy('santri.nama_lengkap')
            ->select('santri.id', 'santri.nama_lengkap', 'santri.nis')
            ->get();

        // Komponen nilai — global master, no FK to mapel/semester
        // Heaviest weight first so UAS/UTS appear before Tugas/Quiz
        $komponenList = KomponenNilai::where('is_active', 1)
            ->orderByDesc('bobot')
            ->orderBy('nama')
            ->get(['id', 'kode', 'nama', 'bobot']);

        // Existing nilai keyed by santri_id → komponen_nilai_id → row
        $existingNilai = Nilai::where('pengampu_id', $pengampu->id)
            ->get()
            ->groupBy('santri_id')
            ->map(fn($rows) => $rows->keyBy('komponen_nilai_id'));

        // Build grid rows with per-santri weighted average
        $rows = $santriList->map(function ($santri) use ($komponenList, $existingNilai) {
            $scores        = [];
            $totalBobot    = 0;
            $totalWeighted = 0;
            $hasAny        = false;

            foreach ($komponenList as $k) {
                $entry = $existingNilai[$santri->id][$k->id] ?? null;

                $scores[$k->id] = [
                    'nilai'   => $entry ? (float) $entry->nilai : null,
                    'catatan' => $entry?->catatan,
                ];

                if ($entry) {
                    $hasAny         = true;
                    $bobot          = $k->bobot > 0 ? $k->bobot : 1;
                    $totalBobot    += $bobot;
                    $totalWeighted += (float) $entry->nilai * $bobot;
                }
            }

            // Weighted average — bobot is already a percentage (0-100),
            // so we divide by sum of present bobots, not 100
            $avg = ($hasAny && $totalBobot > 0)
                ? round($totalWeighted / $totalBobot, 2)
                : null;

            return [
                'santri_id'    => $santri->id,
                'nama_lengkap' => $santri->nama_lengkap,
                'nis'          => $santri->nis,
                'scores'       => $scores,
                'rata_rata'    => $avg,
                'grade'        => $avg !== null ? Nilai::grade($avg) : null,
            ];
        });

        return response()->json([
            'success'       => true,
            'pengampu'      => [
                'id'             => $pengampu->id,
                'mata_pelajaran' => $pengampu->mataPelajaran?->nama_mapel,
                'kelas'          => $pengampu->kelas?->nama_kelas,
                'semester'       => $pengampu->semester?->nama,
                'pengajar'       => $pengampu->pengajar?->nama_lengkap,
            ],
            'komponen_list' => $komponenList,
            'rows'          => $rows,
        ]);
    }

    /**
     * POST /nilai/batch
     *
     * Receives the full grid and upserts all non-empty cells.
     */
    public function batchSave(Request $request)
    {
        $request->validate([
            'pengampu_id'                    => 'required|exists:pengampu,id',
            'tanggal_input'                  => 'required|date',
            'rows'                           => 'required|array',
            'rows.*.santri_id'               => 'required|exists:santri,id',
            'rows.*.komponen_nilai_id'        => 'required|exists:komponen_nilai,id',
            'rows.*.nilai'                   => 'nullable|numeric|min:0|max:100',
            'rows.*.catatan'                 => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $count = Nilai::batchUpsert(
                $request->pengampu_id,
                $request->rows,
                $request->tanggal_input
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$count} nilai berhasil disimpan.",
                'count'   => $count,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /nilai/{id} — remove a single score cell.
     */
    public function destroy($id)
    {
        $nilai = Nilai::findOrFail($id);

        try {
            $nilai->delete();
            return response()->json(['success' => true, 'message' => 'Nilai berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /nilai/rekap?semester_id=X&kelas_id=Y
     *
     * Summary table: santri × mata pelajaran weighted averages.
     * Used for the rekap / rapor view.
     */
    public function rekap(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'kelas_id'    => 'required|exists:kelas,id',
        ]);

        // All pengampu for this kelas × semester
        $pengampuList = Pengampu::with('mataPelajaran')
            ->where('kelas_id', $request->kelas_id)
            ->where('semester_id', $request->semester_id)
            ->orderBy('mata_pelajaran_id')
            ->get();

        // All aktif santri in this kelas
        $santriList = DB::table('santri')
            ->join('kelas_santri', 'kelas_santri.santri_id', '=', 'santri.id')
            ->where('kelas_santri.kelas_id', $request->kelas_id)
            ->where('kelas_santri.status', 'aktif')
            ->orderBy('santri.nama_lengkap')
            ->select('santri.id', 'santri.nama_lengkap', 'santri.nis')
            ->get();

        // Global komponen keyed by id — no mata_pelajaran_id FK exists
        $komponenMap = KomponenNilai::where('is_active', 1)
            ->get(['id', 'bobot'])
            ->keyBy('id');

        // All nilai for these pengampu, grouped: pengampu_id → santri_id → komponen_id → row
        $pengampuIds = $pengampuList->pluck('id');

        $semuaNilai = Nilai::whereIn('pengampu_id', $pengampuIds)
            ->get()
            ->groupBy('pengampu_id')
            ->map(fn($rows) => $rows
                ->groupBy('santri_id')
                ->map(fn($r) => $r->keyBy('komponen_nilai_id'))
            );

        // Build rekap: santri × mapel weighted averages
        $rekap = $santriList->map(function ($santri) use ($pengampuList, $semuaNilai, $komponenMap) {
            $mapelScores = [];
            $grandTotal  = 0;
            $mapelCount  = 0;

            foreach ($pengampuList as $pengampu) {
                $scoremap   = $semuaNilai[$pengampu->id][$santri->id] ?? collect();
                $totalBobot = 0;
                $totalW     = 0;

                foreach ($scoremap as $komponenId => $entry) {
                    $bobot       = $komponenMap[$komponenId]?->bobot ?? 1;
                    $totalBobot += $bobot;
                    $totalW     += (float) $entry->nilai * $bobot;
                }

                $avg = $totalBobot > 0 ? round($totalW / $totalBobot, 2) : null;

                $mapelScores[$pengampu->id] = [
                    'avg'   => $avg,
                    'grade' => $avg !== null ? Nilai::grade($avg) : null,
                ];

                if ($avg !== null) {
                    $grandTotal += $avg;
                    $mapelCount++;
                }
            }

            return [
                'santri_id'    => $santri->id,
                'nama_lengkap' => $santri->nama_lengkap,
                'nis'          => $santri->nis,
                'mapel_scores' => $mapelScores,
                'grand_avg'    => $mapelCount > 0 ? round($grandTotal / $mapelCount, 2) : null,
            ];
        });

        return response()->json([
            'success'       => true,
            'pengampu_list' => $pengampuList->map(fn($p) => [
                'id'             => $p->id,
                'mata_pelajaran' => $p->mataPelajaran?->nama_mapel,
            ]),
            'santri_list'   => $rekap,
        ]);
    }

    // ── Select2 AJAX ─────────────────────────────────────────────

    /**
     * Search pengampu for Select2 — returns context-rich label.
     */
    public function searchPengampu(Request $request)
    {
        $q          = trim($request->get('q', ''));
        $semesterId = $request->get('semester_id');
        $kelasId    = $request->get('kelas_id');

        $query = Pengampu::with(['pengajar', 'mataPelajaran', 'kelas', 'semester'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($kelasId,    fn($q) => $q->where('kelas_id', $kelasId))
            ->when($q, fn($query) => $query->whereHas(
                'mataPelajaran', fn($sq) => $sq->where('nama_mapel', 'like', "%{$q}%")
            )->orWhereHas(
                'pengajar', fn($sq) => $sq->where('nama_lengkap', 'like', "%{$q}%")
            ))
            ->limit(30);

        $results = $query->get()->map(fn($p) => [
            'id'   => $p->id,
            'text' => "[{$p->kelas?->nama_kelas}] {$p->mataPelajaran?->nama_mapel} — {$p->pengajar?->nama_lengkap}",
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

    public function searchKelas(Request $request)
    {
        $q          = trim($request->get('q', ''));
        $semesterId = $request->get('semester_id');

        // Only return kelas that have at least one pengampu in this semester
        $query = \App\Models\Kelas::where('nama_kelas', 'like', "%{$q}%");

        if ($semesterId) {
            $query->whereHas('kelasSantri') // has students
                  ->whereExists(fn($sq) => $sq
                      ->from('pengampu')
                      ->whereColumn('pengampu.kelas_id', 'kelas.id')
                      ->where('pengampu.semester_id', $semesterId)
                  );
        }

        $results = $query->orderBy('nama_kelas')->limit(20)->get(['id', 'nama_kelas', 'tingkat'])
            ->map(fn($k) => [
                'id'   => $k->id,
                'text' => "Kelas {$k->nama_kelas} (Tingkat {$k->tingkat})",
            ]);

        return response()->json(['results' => $results]);
    }
}