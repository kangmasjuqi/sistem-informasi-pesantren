<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use App\Models\Pengampu;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Pengajar;

class JadwalPelajaranController extends Controller
{
    public function index()
    {
        $activeSemester = Semester::where('is_active', 1)->first();
        $hariOptions    = JadwalPelajaran::hariOptions();
        $statusOptions  = JadwalPelajaran::statusOptions();

        return view('jadwal-pelajaran.index', compact(
            'activeSemester',
            'hariOptions',
            'statusOptions'
        ));
    }

    /**
     * GET /jadwal-pelajaran/timetable?semester_id=X&kelas_id=Y
     *
     * Returns full weekly timetable for a kelas:
     * structured as hari → [slots sorted by jam_mulai]
     * Also flags any conflicts for display.
     */
    public function timetable(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id',
            'kelas_id'    => 'required|exists:kelas,id',
        ]);

        $kelas    = Kelas::with(['tahunAjaran', 'waliKelas'])->findOrFail($request->kelas_id);
        $semester = Semester::findOrFail($request->semester_id);

        // All jadwal for this kelas in this semester (via pengampu)
        $jadwalList = JadwalPelajaran::with([
            'pengampu.mataPelajaran',
            'pengajar',
        ])
        ->where('jadwal_pelajaran.kelas_id', $request->kelas_id)
        ->whereHas('pengampu', fn($q) => $q->where('semester_id', $request->semester_id))
        ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
        ->orderBy('jam_mulai')
        ->get();

        // Structure by hari
        $byHari = collect(JadwalPelajaran::hariOrder())
            ->mapWithKeys(fn($hari) => [
                $hari => $jadwalList
                    ->where('hari', $hari)
                    ->values()
                    ->map(fn($j) => $this->formatSlot($j)),
            ]);

        // All pengampu available for this kelas × semester (for the add slot dropdown)
        $pengampuOptions = Pengampu::with(['mataPelajaran', 'pengajar'])
            ->where('kelas_id', $request->kelas_id)
            ->where('semester_id', $request->semester_id)
            ->aktif()
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'text'        => $p->mataPelajaran?->nama_mapel . ' — ' . $p->pengajar?->nama_lengkap,
                'pengajar_id' => $p->pengajar_id,
                'mapel'       => $p->mataPelajaran?->nama_mapel,
                'pengajar'    => $p->pengajar?->nama_lengkap,
            ]);

        return response()->json([
            'success'          => true,
            'kelas'            => [
                'id'         => $kelas->id,
                'nama_kelas' => $kelas->nama_kelas,
                'tingkat'    => $kelas->tingkat,
                'wali_kelas' => $kelas->waliKelas?->nama_lengkap,
            ],
            'semester'         => [
                'id'   => $semester->id,
                'nama' => $semester->nama,
            ],
            'timetable'        => $byHari,
            'pengampu_options' => $pengampuOptions,
            'total_slots'      => $jadwalList->count(),
        ]);
    }

    /**
     * POST /jadwal-pelajaran
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // Resolve denormalised fields from pengampu
        $pengampu = Pengampu::findOrFail($request->pengampu_id);

        // Conflict check
        $conflicts = JadwalPelajaran::checkAllConflicts(
            $pengampu->kelas_id,
            $pengampu->pengajar_id,
            $request->ruangan ?? '',
            $request->hari,
            $request->jam_mulai,
            $request->jam_selesai
        );

        if ($conflicts) {
            return response()->json([
                'success'   => false,
                'message'   => 'Terdapat konflik jadwal.',
                'conflicts' => $conflicts,
            ], 422);
        }

        try {
            $jadwal = JadwalPelajaran::create([
                'pengampu_id'  => $request->pengampu_id,
                'kelas_id'     => $pengampu->kelas_id,
                'pengajar_id'  => $pengampu->pengajar_id,
                'hari'         => $request->hari,
                'jam_ke'       => $request->jam_ke,
                'jam_mulai'    => $request->jam_mulai,
                'jam_selesai'  => $request->jam_selesai,
                'ruangan'      => $request->ruangan,
                'status'       => $request->status ?? 'aktif',
                'keterangan'   => $request->keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil ditambahkan.',
                'data'    => $this->formatSlot($jadwal->load(['pengampu.mataPelajaran', 'pengajar'])),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /jadwal-pelajaran/{id}
     */
    public function show($id)
    {
        $j = JadwalPelajaran::with(['pengampu.mataPelajaran', 'pengampu.semester', 'pengajar', 'kelas'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $j->id,
                'pengampu_id'  => $j->pengampu_id,
                'pengampu_text'=> $j->pengampu?->mataPelajaran?->nama_mapel . ' — ' . $j->pengajar?->nama_lengkap,
                'kelas_id'     => $j->kelas_id,
                'pengajar_id'  => $j->pengajar_id,
                'hari'         => $j->hari,
                'jam_ke'       => $j->jam_ke,
                'jam_mulai'    => substr($j->jam_mulai, 0, 5),
                'jam_selesai'  => substr($j->jam_selesai, 0, 5),
                'ruangan'      => $j->ruangan,
                'status'       => $j->status,
                'keterangan'   => $j->keterangan,
                'semester_id'  => $j->pengampu?->semester_id,
            ],
        ]);
    }

    /**
     * PUT /jadwal-pelajaran/{id}
     */
    public function update(Request $request, $id)
    {
        $jadwal    = JadwalPelajaran::findOrFail($id);
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $pengampu  = Pengampu::findOrFail($request->pengampu_id);

        $conflicts = JadwalPelajaran::checkAllConflicts(
            $pengampu->kelas_id,
            $pengampu->pengajar_id,
            $request->ruangan ?? '',
            $request->hari,
            $request->jam_mulai,
            $request->jam_selesai,
            $id
        );

        if ($conflicts) {
            return response()->json([
                'success'   => false,
                'message'   => 'Terdapat konflik jadwal.',
                'conflicts' => $conflicts,
            ], 422);
        }

        try {
            $jadwal->update([
                'pengampu_id' => $request->pengampu_id,
                'kelas_id'    => $pengampu->kelas_id,
                'pengajar_id' => $pengampu->pengajar_id,
                'hari'        => $request->hari,
                'jam_ke'      => $request->jam_ke,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'ruangan'     => $request->ruangan,
                'status'      => $request->status,
                'keterangan'  => $request->keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui.',
                'data'    => $this->formatSlot($jadwal->load(['pengampu.mataPelajaran', 'pengajar'])),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /jadwal-pelajaran/{id}
     */
    public function destroy($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        try {
            $jadwal->delete();
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /jadwal-pelajaran/pengajar?pengajar_id=X&semester_id=Y
     *
     * Returns the personal weekly timetable for a teacher.
     */
    public function pengajarTimetable(Request $request)
    {
        $request->validate([
            'pengajar_id' => 'required|exists:pengajar,id',
            'semester_id' => 'required|exists:semester,id',
        ]);

        $jadwalList = JadwalPelajaran::with(['pengampu.mataPelajaran', 'kelas'])
            ->where('jadwal_pelajaran.pengajar_id', $request->pengajar_id)
            ->whereHas('pengampu', fn($q) => $q->where('semester_id', $request->semester_id))
            ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
            ->orderBy('jam_mulai')
            ->get();

        $byHari = collect(JadwalPelajaran::hariOrder())
            ->mapWithKeys(fn($hari) => [
                $hari => $jadwalList->where('hari', $hari)->values()->map(fn($j) => $this->formatSlot($j)),
            ]);

        return response()->json(['success' => true, 'timetable' => $byHari]);
    }

    // ── Select2 AJAX ─────────────────────────────────────────────

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

        $query = Kelas::where('nama_kelas', 'like', '%' . $request->get('q') . '%')
            ->aktif();

        if ($semesterId) {
            $query->whereHas('kelasSantri')->whereExists(
                fn($sq) => $sq->from('pengampu')
                    ->whereColumn('pengampu.kelas_id', 'kelas.id')
                    ->where('pengampu.semester_id', $semesterId)
            );
        }

        $results = $query->orderBy('nama_kelas')->limit(20)->get(['id', 'nama_kelas', 'tingkat'])
            ->map(fn($k) => ['id' => $k->id, 'text' => "Kelas {$k->nama_kelas} (Tingkat {$k->tingkat})"]);

        return response()->json(['results' => $results]);
    }

    public function searchPengampu(Request $request)
    {
        $kelasId    = $request->get('kelas_id');
        $semesterId = $request->get('semester_id');
        $q          = $request->get('q', '');

        $results = Pengampu::with(['mataPelajaran', 'pengajar'])
            ->when($kelasId,    fn($query) => $query->where('kelas_id', $kelasId))
            ->when($semesterId, fn($query) => $query->where('semester_id', $semesterId))
            ->when($q, fn($query) => $query->whereHas('mataPelajaran', fn($sq) => $sq->where('nama', 'like', "%{$q}%")))
            ->aktif()
            ->limit(30)
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'text'        => $p->mataPelajaran?->nama_mapel . ' — ' . $p->pengajar?->nama_lengkap,
                'pengajar_id' => $p->pengajar_id,
            ]);

        return response()->json(['results' => $results]);
    }

    // ── Private helpers ───────────────────────────────────────────

    private function formatSlot(JadwalPelajaran $j): array
    {
        return [
            'id'           => $j->id,
            'pengampu_id'  => $j->pengampu_id,
            'pengajar_id'  => $j->pengajar_id,
            'hari'         => $j->hari,
            'hari_label'   => $j->hari_label,
            'jam_ke'       => $j->jam_ke,
            'jam_mulai'    => substr($j->jam_mulai,   0, 5),
            'jam_selesai'  => substr($j->jam_selesai, 0, 5),
            'waktu_label'  => $j->waktu_label,
            'ruangan'      => $j->ruangan,
            'status'       => $j->status,
            'status_label' => $j->status_label,
            'status_css'   => $j->status_css,
            'keterangan'   => $j->keterangan,
            'nama_mapel'   => $j->pengampu?->mataPelajaran?->nama_mapel,
            'pengajar_nama'=> $j->pengajar?->nama_lengkap,
        ];
    }

    private function rules(): array
    {
        return [
            'pengampu_id' => 'required|exists:pengampu,id',
            'hari'        => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_ke'      => 'nullable|integer|min:1|max:20',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'     => 'nullable|string|max:50',
            'status'      => 'nullable|in:aktif,libur,diganti',
            'keterangan'  => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'pengampu_id.required'  => 'Mata pelajaran / pengampu harus dipilih',
            'hari.required'         => 'Hari harus dipilih',
            'jam_mulai.required'    => 'Jam mulai harus diisi',
            'jam_selesai.required'  => 'Jam selesai harus diisi',
            'jam_selesai.after'     => 'Jam selesai harus setelah jam mulai',
        ];
    }
}