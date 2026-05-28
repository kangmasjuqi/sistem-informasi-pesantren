<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Kehadiran;
use App\Models\Pengampu;
use App\Models\Semester;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;

class KehadiranController extends Controller
{
    /**
     * Index — daily attendance entry point.
     */
    public function index()
    {
        $activeSemester = Semester::where('is_active', 1)->first();

        return view('kehadiran.index', compact('activeSemester'));
    }

    /**
     * GET /kehadiran/session
     *
     * Load santri list + existing attendance for a given context.
     * Supports both jenis: 'pelajaran' (needs pengampu_id) and 'sholat'/'kegiatan' (needs kelas_id).
     *
     * Returns:
     *   - santri[]     : [{id, nama, nis}]
     *   - existing{}   : {santri_id → {status, waktu_absen, keterangan}}
     *   - sudah_diabsen: bool
     *   - context{}    : display info
     */
    public function session(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'jenis'          => 'required|in:pelajaran,sholat,kegiatan',
            'pengampu_id'    => 'required_if:jenis,pelajaran|nullable|exists:pengampu,id',
            'kelas_id'       => 'required_unless:jenis,pelajaran|nullable|exists:kelas,id',
            'keterangan_kegiatan' => 'nullable|string|max:100',
        ]);

        $jenis  = $request->jenis;
        $tanggal = $request->tanggal;

        // Resolve kelas_id and context label
        if ($jenis === 'pelajaran') {
            $pengampu = Pengampu::with(['mataPelajaran', 'kelas', 'pengajar', 'semester'])
                ->findOrFail($request->pengampu_id);

            $kelasId     = $pengampu->kelas_id;
            $pengampuId  = $pengampu->id;
            $contextLabel = $pengampu->mataPelajaran?->nama ?? '—';
            $contextSub   = implode(' · ', array_filter([
                $pengampu->kelas?->nama_kelas,
                $pengampu->semester?->nama,
                $pengampu->pengajar?->nama_lengkap,
            ]));
        } else {
            $kelasId     = $request->kelas_id;
            $pengampuId  = null;
            $contextLabel = $jenis === 'sholat' ? 'Sholat' : ($request->keterangan_kegiatan ?? 'Kegiatan');
            $contextSub   = 'Kelas ' . optional(\App\Models\Kelas::find($kelasId))->nama_kelas;
        }

        // Santri aktif in this kelas
        $santriList = DB::table('santri')
            ->join('kelas_santri', 'kelas_santri.santri_id', '=', 'santri.id')
            ->where('kelas_santri.kelas_id', $kelasId)
            ->where('kelas_santri.status', 'aktif')
            ->orderBy('santri.nama_lengkap')
            ->select('santri.id', 'santri.nama_lengkap', 'santri.nis')
            ->get();

        // Existing attendance for this session
        $existingQuery = Kehadiran::where('tanggal', $tanggal)
            ->where('jenis_kehadiran', $jenis);

        if ($pengampuId) {
            $existingQuery->where('pengampu_id', $pengampuId);
        } else {
            // For non-pelajaran: match by santri in this kelas + jenis + kegiatan label
            $existingQuery->whereIn('santri_id', $santriList->pluck('id'))
                ->where(function ($q) use ($request) {
                    $q->whereNull('keterangan_kegiatan')
                      ->orWhere('keterangan_kegiatan', $request->keterangan_kegiatan);
                });
        }

        $existing = $existingQuery->get()
            ->keyBy('santri_id')
            ->map(fn($k) => [
                'status'      => $k->status_kehadiran,
                'waktu_absen' => $k->waktu_absen,
                'keterangan'  => $k->keterangan,
            ]);

        $sudahDiabsen = $existing->isNotEmpty();

        // Summary counts
        $summary = [
            'hadir' => $existing->where('status', 'hadir')->count(),
            'sakit' => $existing->where('status', 'sakit')->count(),
            'izin'  => $existing->where('status', 'izin')->count(),
            'alpa'  => $existing->where('status', 'alpa')->count(),
            'total' => $santriList->count(),
            'belum' => $santriList->count() - $existing->count(),
        ];

        return response()->json([
            'success'      => true,
            'santri'       => $santriList,
            'existing'     => $existing,
            'sudah_diabsen'=> $sudahDiabsen,
            'summary'      => $summary,
            'context'      => [
                'label'    => $contextLabel,
                'sub'      => $contextSub,
                'jenis'    => $jenis,
                'tanggal'  => Carbon::parse($tanggal)->isoFormat('dddd, D MMMM YYYY'),
                'pengampu_id' => $pengampuId,
                'kelas_id'    => $kelasId,
                'keterangan_kegiatan' => $request->keterangan_kegiatan,
            ],
        ]);
    }

    /**
     * POST /kehadiran/batch
     *
     * Bulk upsert attendance for one session.
     *
     * rows = [{santri_id, status_kehadiran, waktu_absen?, keterangan?}]
     */
    public function batchSave(Request $request)
    {
        $request->validate([
            'tanggal'              => 'required|date',
            'jenis'                => 'required|in:pelajaran,sholat,kegiatan',
            'pengampu_id'          => 'nullable|exists:pengampu,id',
            'kelas_id'             => 'nullable|exists:kelas,id',
            'jadwal_pelajaran_id'  => 'nullable|exists:jadwal_pelajaran,id',
            'keterangan_kegiatan'  => 'nullable|string|max:100',
            'rows'                 => 'required|array|min:1',
            'rows.*.santri_id'     => 'required|exists:santri,id',
            'rows.*.status'        => 'required|in:hadir,sakit,izin,alpa',
            'rows.*.waktu_absen'   => 'nullable|date_format:H:i',
            'rows.*.keterangan'    => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $tanggal             = $request->tanggal;
            $jenis               = $request->jenis;
            $pengampuId          = $request->pengampu_id;
            $jadwalId            = $request->jadwal_pelajaran_id;
            $keteranganKegiatan  = $request->keterangan_kegiatan;
            $count               = 0;
            $waktu_absen         = now()->format('H:i:s');

            foreach ($request->rows as $row) {
                Kehadiran::updateOrCreate(
                    [
                        'tanggal'         => $tanggal,
                        'santri_id'       => $row['santri_id'],
                        'pengampu_id'     => $pengampuId,
                        'jenis_kehadiran' => $jenis,
                        // For kegiatan/sholat, also scope by kegiatan label
                        ...($pengampuId === null
                            ? ['keterangan_kegiatan' => $keteranganKegiatan]
                            : [])
                    ],
                    [
                        'jadwal_pelajaran_id' => $jadwalId,
                        'status_kehadiran'    => $row['status'],
                        'waktu_absen'         => $waktu_absen,
                        'keterangan_kegiatan' => $keteranganKegiatan,
                        'keterangan'          => $row['keterangan'] ?? null,
                    ]
                );
                $count++;
            }

            DB::commit();

            // Build summary for response
            $hadirCount = collect($request->rows)->where('status', 'hadir')->count();
            $tidakHadir = $count - $hadirCount;

            return response()->json([
                'success' => true,
                'message' => "Kehadiran {$count} santri berhasil disimpan. "
                    . ($tidakHadir > 0 ? "{$tidakHadir} tidak hadir." : "Semua hadir! 🎉"),
                'count'   => $count,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /kehadiran/rekap?kelas_id=X&bulan=Y&tahun=Z
     *
     * Monthly attendance recap per santri for a class.
     */
    public function rekap(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'bulan'    => 'required|integer|between:1,12',
            'tahun'    => 'required|integer|min:2020',
            'jenis'    => 'nullable|in:pelajaran,sholat,kegiatan',
        ]);

        $jenis = $request->jenis ?? 'pelajaran';

        $santriList = DB::table('santri')
            ->join('kelas_santri', 'kelas_santri.santri_id', '=', 'santri.id')
            ->where('kelas_santri.kelas_id', $request->kelas_id)
            ->where('kelas_santri.status', 'aktif')
            ->orderBy('santri.nama_lengkap')
            ->select('santri.id', 'santri.nama_lengkap', 'santri.nis')
            ->get();

        $hadir_counts = Kehadiran::whereIn('santri_id', $santriList->pluck('id'))
            ->where('jenis_kehadiran', $jenis)
            ->whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->selectRaw('santri_id, status_kehadiran, COUNT(*) as jumlah')
            ->groupBy('santri_id', 'status_kehadiran')
            ->get()
            ->groupBy('santri_id')
            ->map(fn($rows) => $rows->keyBy('status_kehadiran'));

        $result = $santriList->map(function ($santri) use ($hadir_counts) {
            $counts = $hadir_counts[$santri->id] ?? collect();
            $hadir  = (int) ($counts['hadir']?->jumlah ?? 0);
            $sakit  = (int) ($counts['sakit']?->jumlah ?? 0);
            $izin   = (int) ($counts['izin']?->jumlah  ?? 0);
            $alpa   = (int) ($counts['alpa']?->jumlah  ?? 0);
            $total  = $hadir + $sakit + $izin + $alpa;

            return [
                'santri_id'    => $santri->id,
                'nama_lengkap' => $santri->nama_lengkap,
                'nis'          => $santri->nis,
                'hadir'        => $hadir,
                'sakit'        => $sakit,
                'izin'         => $izin,
                'alpa'         => $alpa,
                'total'        => $total,
                'persen_hadir' => $total > 0 ? round($hadir / $total * 100, 1) : 0,
            ];
        });

        return response()->json(['success' => true, 'data' => $result]);
    }

    // ── Select2 AJAX ─────────────────────────────────────────────

    /**
     * Today's scheduled pengampu — shown in the quick-pick dropdown.
     * Filtered by hari (day of week) and optionally semester.
     */
    public function todaySchedule(Request $request)
    {
        $tanggal    = $request->get('tanggal', today()->format('Y-m-d'));
        $semesterId = $request->get('semester_id');
        $hari       = strtolower(Carbon::parse($tanggal)->locale('id')->dayName);

        // Map Carbon day name to enum values
        $hariMap = [
            'senin' => 'senin', 'selasa' => 'selasa', 'rabu' => 'rabu',
            'kamis' => 'kamis', 'jumat' => 'jumat', 'sabtu' => 'sabtu', 'minggu' => 'minggu',
        ];
        $hariEnum = $hariMap[$hari] ?? null;

        $query = JadwalPelajaran::with(['pengampu.mataPelajaran', 'pengampu.semester', 'kelas'])
            ->where('status', 'aktif')
            ->when($hariEnum, fn($q) => $q->where('hari', $hariEnum))
            ->when($semesterId, fn($q) => $q->whereHas(
                'pengampu', fn($sq) => $sq->where('semester_id', $semesterId)
            ));

        $results = $query->orderBy('jam_mulai')->get()->map(fn($j) => [
            'id'          => $j->pengampu_id,
            'text'        => "[{$j->kelas?->nama_kelas}] {$j->kelas?->deskripsi}", // {$j->pengampu?->mataPelajaran?->nama_mapel} ({$j->waktu_label})",
            'kelas_id'    => $j->kelas_id,
            'jam_mulai'   => substr($j->jam_mulai, 0, 5),
            'jam_selesai' => substr($j->jam_selesai, 0, 5),
        ]);

        return response()->json(['results' => $results]);
    }

    public function searchSemester(Request $request)
    {
        $results = Semester::where('nama', 'like', '%' . $request->get('q') . '%')
            ->orderByDesc('nama')->limit(10)->get(['id', 'nama', 'is_active'])
            ->map(fn($s) => ['id' => $s->id, 'text' => $s->nama . ($s->is_active ? ' ★' : '')]);

        return response()->json(['results' => $results]);
    }

    public function searchKelas(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $results = \App\Models\Kelas::where('nama_kelas', 'like', '%' . $request->get('q') . '%')
            ->aktif()
            ->when($semesterId, fn($q) => $q->whereExists(
                fn($sq) => $sq->from('pengampu')
                    ->whereColumn('pengampu.kelas_id', 'kelas.id')
                    ->where('pengampu.semester_id', $semesterId)
            ))
            ->orderBy('nama_kelas')->limit(20)->get(['id', 'nama_kelas', 'tingkat'])
            ->map(fn($k) => ['id' => $k->id, 'text' => "Kelas {$k->nama_kelas} (Tkt {$k->tingkat})"]);

        return response()->json(['results' => $results]);
    }
}
