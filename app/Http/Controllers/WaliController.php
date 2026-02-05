<?php

namespace App\Http\Controllers;

use App\Models\WaliSantri;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
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

}