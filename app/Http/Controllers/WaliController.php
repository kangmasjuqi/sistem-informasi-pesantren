<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\WaliSantri;
use App\Models\Pembayaran;
use App\Models\Santri;
use Carbon\Carbon;

class WaliController extends Controller
{
    /**
     * Display wali dashboard with their children's data
     */
    public function dashboard()
    {
        $user = auth()->user();

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear  = $now->year;

        $nextMonth = $now->copy()->addMonth()->month;
        $nextYear  = $now->copy()->addMonth()->year;

        // Get bulanan payment type
        $jenisBulanan = DB::table('jenis_pembayaran')
            ->where('kategori', 'bulanan')
            ->where('is_active', 1)
            ->value('id');

        // Get children
        $children = WaliSantri::where('wali_santri.id', $user->id)
            ->join('santri', 'santri.id', '=', 'wali_santri.santri_id')
            ->leftJoin('kelas_santri', function ($q) {
                $q->on('kelas_santri.santri_id', '=', 'santri.id')
                ->where('kelas_santri.status', 'aktif');
            })
            ->leftJoin('kelas', 'kelas.id', '=', 'kelas_santri.kelas_id')
            ->select(
                'santri.*',
                'kelas.id as kelas_id',
                'kelas.nama_kelas'
            )
            ->get();

        $childrenData = $children->map(function ($santri) use (
            $jenisBulanan,
            $currentMonth,
            $currentYear,
            $nextMonth,
            $nextYear,
            $now
        ) {

            /* ================= PAYMENT ================= */

            $currentPayment = Pembayaran::where('santri_id', $santri->id)
                ->where('jenis_pembayaran_id', $jenisBulanan)
                ->where('bulan', $currentMonth)
                ->where('tahun', $currentYear)
                ->latest()
                ->first();

            $nextPayment = Pembayaran::where('santri_id', $santri->id)
                ->where('jenis_pembayaran_id', $jenisBulanan)
                ->where('bulan', $nextMonth)
                ->where('tahun', $nextYear)
                ->latest()
                ->first();

            /* ================= ATTENDANCE ================= */

            $totalHari = DB::table('kehadiran')
                ->where('santri_id', $santri->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->count();

            $hadir = DB::table('kehadiran')
                ->where('santri_id', $santri->id)
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status_kehadiran', 'hadir')
                ->count();

            $kehadiran = $totalHari > 0
                ? round(($hadir / $totalHari) * 100, 1)
                : 0;

            /* ================= AVERAGE SCORE ================= */

            $rataRata = DB::table('nilai')
                ->where('santri_id', $santri->id)
                ->avg('nilai');

            $rataRata = $rataRata ? round($rataRata, 1) : 0;

            /* ================= RANKING ================= */

            $ranking = null;
            $totalSiswa = null;

            if ($santri->kelas_id) {

                $rankingData = DB::table('santri')
                    ->join('kelas_santri', 'kelas_santri.santri_id', '=', 'santri.id')
                    ->leftJoin('nilai', 'nilai.santri_id', '=', 'santri.id')
                    ->where('kelas_santri.kelas_id', $santri->kelas_id)
                    ->where('kelas_santri.status', 'aktif')
                    ->select(
                        'santri.id',
                        DB::raw('AVG(nilai.nilai) as avg_score')
                    )
                    ->groupBy('santri.id')
                    ->orderByDesc('avg_score')
                    ->get();

                $totalSiswa = $rankingData->count();

                $ranking = $rankingData
                    ->pluck('id')
                    ->search($santri->id);

                $ranking = $ranking !== false ? $ranking + 1 : null;
            }

            /* ================= RESULT ================= */

            return [
                'id' => $santri->id,
                'nama' => $santri->nama_lengkap,
                'nis' => $santri->nis,
                'kelas' => $santri->nama_kelas ?? 'Belum ada kelas',
                'status' => $santri->status,

                'avatar' => strtoupper(substr($santri->nama_lengkap, 0, 2)),

                'kehadiran' => $kehadiran,
                'rata_rata' => $rataRata,

                'ranking' => $ranking,
                'total_siswa' => $totalSiswa,

                'current_month_payment' => [
                    'month' => $now->isoFormat('MMMM YYYY'),
                    'status' => $currentPayment?->status ?? 'belum_lunas',
                ],

                'next_month_payment' => [
                    'month' => $now->copy()->addMonth()->isoFormat('MMMM YYYY'),
                    'status' => $nextPayment?->status ?? 'belum_lunas',
                ],
            ];
        });

        return view('wali.dashboard', compact('childrenData'));
    }

    public function index()
    {
        return view('wali.index');
    }

    public function getData(Request $request)
    {
        $query = WaliSantri::with('santri');

        // Global search
        if (!empty($request->search['value'])) {
            $s = $request->search['value'];
            $query->where(function ($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%{$s}%")
                  ->orWhere('telepon', 'like', "%{$s}%")
                  ->orWhere('pekerjaan', 'like', "%{$s}%")
                  ->orWhereHas('santri', fn($sq) => $sq->where('nama_lengkap', 'like', "%{$s}%")
                                                        ->orWhere('nis', 'like', "%{$s}%"));
            });
        }

        // Column filters
        if (!empty($request->santri_id)) {
            $query->where('santri_id', $request->santri_id);
        }
        if (!empty($request->jenis_wali)) {
            $query->where('jenis_wali', $request->jenis_wali);
        }
        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', "%{$request->nama_lengkap}%");
        }
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        $totalRecords    = WaliSantri::count();
        $filteredRecords = $query->count();

        $columns     = ['id', 'santri_id', 'jenis_wali', 'nama_lengkap', 'pekerjaan', 'telepon', 'status'];
        $orderColIdx = $request->order[0]['column'] ?? 3;
        $orderDir    = $request->order[0]['dir']    ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nama_lengkap';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($w) => [
            'id'                  => $w->id,
            'santri_id'           => $w->santri_id,
            'santri_nama'         => $w->santri?->nama_lengkap,
            'santri_nis'          => $w->santri?->nis,
            'jenis_wali'          => $w->jenis_wali,
            'jenis_label'         => $w->jenis_label,
            'nama_lengkap'        => $w->nama_lengkap,
            'nik'                 => $w->nik,
            'tempat_lahir'        => $w->tempat_lahir,
            'tanggal_lahir'       => $w->tanggal_lahir?->format('Y-m-d'),
            'tanggal_lahir_fmt'   => $w->tanggal_lahir?->format('d M Y'),
            'pendidikan_terakhir' => $w->pendidikan_terakhir,
            'pekerjaan'           => $w->pekerjaan,
            'penghasilan'         => $w->penghasilan,
            'penghasilan_fmt'     => $w->penghasilan_formatted,
            'telepon'             => $w->telepon,
            'email'               => $w->email,
            'alamat'              => $w->alamat,
            'status'              => $w->status,
            'status_label'        => $w->status_label,
            'keterangan'          => $w->keterangan,
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
            $wali = WaliSantri::create([
                'santri_id'           => $request->santri_id,
                'jenis_wali'          => $request->jenis_wali,
                'nama_lengkap'        => $request->nama_lengkap,
                'nik'                 => $request->nik,
                'tempat_lahir'        => $request->tempat_lahir,
                'tanggal_lahir'       => $request->tanggal_lahir,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'pekerjaan'           => $request->pekerjaan,
                'penghasilan'         => $request->penghasilan
                    ? (float) str_replace(['.', ','], ['', '.'], $request->penghasilan)
                    : null,
                'telepon'             => $request->telepon,
                'email'               => $request->email,
                'alamat'              => $request->alamat,
                'status'              => $request->status ?? 'hidup',
                'keterangan'          => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Data wali santri berhasil ditambahkan', 'data' => $wali->load('santri')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $w = WaliSantri::with('santri')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $w->id,
                'santri_id'           => $w->santri_id,
                'santri_nama'         => $w->santri?->nama_lengkap,
                'santri_nis'          => $w->santri?->nis,
                'jenis_wali'          => $w->jenis_wali,
                'nama_lengkap'        => $w->nama_lengkap,
                'nik'                 => $w->nik,
                'tempat_lahir'        => $w->tempat_lahir,
                'tanggal_lahir'       => $w->tanggal_lahir?->format('Y-m-d'),
                'pendidikan_terakhir' => $w->pendidikan_terakhir,
                'pekerjaan'           => $w->pekerjaan,
                'penghasilan'         => $w->penghasilan
                    ? number_format($w->penghasilan, 0, ',', '.')
                    : '',
                'telepon'             => $w->telepon,
                'email'               => $w->email,
                'alamat'              => $w->alamat,
                'status'              => $w->status,
                'keterangan'          => $w->keterangan,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $wali      = WaliSantri::findOrFail($id);
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $wali->update([
                'santri_id'           => $request->santri_id,
                'jenis_wali'          => $request->jenis_wali,
                'nama_lengkap'        => $request->nama_lengkap,
                'nik'                 => $request->nik,
                'tempat_lahir'        => $request->tempat_lahir,
                'tanggal_lahir'       => $request->tanggal_lahir,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'pekerjaan'           => $request->pekerjaan,
                'penghasilan'         => $request->penghasilan
                    ? (float) str_replace(['.', ','], ['', '.'], $request->penghasilan)
                    : null,
                'telepon'             => $request->telepon,
                'email'               => $request->email,
                'alamat'              => $request->alamat,
                'status'              => $request->status,
                'keterangan'          => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Data wali santri berhasil diperbarui', 'data' => $wali]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $wali = WaliSantri::findOrFail($id);

        try {
            $wali->delete();
            return response()->json(['success' => true, 'message' => 'Data wali santri berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules(): array
    {
        return [
            'santri_id'           => 'required|exists:santri,id',
            'jenis_wali'          => 'required|in:ayah,ibu,wali',
            'nama_lengkap'        => 'required|string|max:255',
            'nik'                 => 'nullable|string|max:20',
            'tempat_lahir'        => 'nullable|string|max:100',
            'tanggal_lahir'       => 'nullable|date',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'pekerjaan'           => 'nullable|string|max:100',
            'penghasilan'         => 'nullable|string',
            'telepon'             => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:100',
            'alamat'              => 'nullable|string',
            'status'              => 'required|in:hidup,meninggal',
            'keterangan'          => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'santri_id.required'   => 'Santri harus dipilih',
            'santri_id.exists'     => 'Santri tidak ditemukan',
            'jenis_wali.required'  => 'Jenis wali harus dipilih',
            'nama_lengkap.required'=> 'Nama lengkap harus diisi',
            'email.email'          => 'Format email tidak valid',
            'status.required'      => 'Status harus dipilih',
        ];
    }

    /**
     * Select2 AJAX: search santri by name or NIS
     */
    public function searchSantri(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = Santri::where(function ($query) use ($q) {
                $query->where('nama_lengkap', 'like', "%{$q}%")
                      ->orWhere('nis', 'like', "%{$q}%");
            })
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