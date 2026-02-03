<?php

namespace Database\Seeders\Operational;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RaporSummarySeeder extends Seeder
{
    public function run(): void
    {
        $approverIds = $this->getApproverIds();

        // Only generate rapor for passed semesters
        $semesterAll = DB::table('semester')->where('is_active', 0)->orderBy('tanggal_mulai')->get();

        foreach ($semesterAll as $semester) {
            $finalizeFields = $this->getFinalizeStatus($semester, $approverIds);
            $this->generateRaporSummary($semester, $finalizeFields);
        }

        $this->command->info('✅ Rapor Summary records seeded!');
    }

    private function generateRaporSummary($semester, $finalizeFields)
    {
        // Get all kelas
        $kelasList = DB::table('kelas')
            ->where('tahun_ajaran_id', $semester->tahun_ajaran_id)
            ->get();

        $summaryData = [];

        foreach ($kelasList as $kelas) {
            // Get santri in this kelas
            $santriList = DB::table('kelas_santri')
                ->where('kelas_id', $kelas->id)
                ->pluck('santri_id');

            $avgPerSantri = [];

            foreach ($santriList as $santriId) {
                // Get all rapor for this santri in this semester
                $raporList = DB::table('rapor as r')
                    ->join('pengampu as p', 'r.pengampu_id', '=', 'p.id')
                    ->where('r.santri_id', $santriId)
                    ->where('p.semester_id', $semester->id)
                    ->where('p.kelas_id', $kelas->id)
                    ->select('r.nilai_akhir', 'r.is_lulus')
                    ->get();

                if ($raporList->isEmpty()) continue;

                $rataRata = round($raporList->avg('nilai_akhir'), 2);
                $totalMapel = $raporList->count();
                $totalLulus = $raporList->where('is_lulus', 1)->count();

                // Get kehadiran stats
                $kehadiranStats = DB::table('kehadiran as k')
                    ->join('pengampu as p', 'k.pengampu_id', '=', 'p.id')
                    ->where('k.santri_id', $santriId)
                    ->where('p.semester_id', $semester->id)
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN k.status_kehadiran = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN k.status_kehadiran = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN k.status_kehadiran = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN k.status_kehadiran = "alpa" THEN 1 ELSE 0 END) as alpa
                    ')
                    ->first();

                $avgPerSantri[$santriId] = [
                    'rata_rata' => $rataRata,
                    'total_mapel' => $totalMapel,
                    'total_lulus' => $totalLulus,
                    'kehadiran' => $kehadiranStats,
                ];
            }

            // Calculate ranking
            uasort($avgPerSantri, fn($a, $b) => $b['rata_rata'] <=> $a['rata_rata']);
            $ranking = 1;
            $totalSiswa = count($avgPerSantri);

            foreach ($avgPerSantri as $santriId => $data) {
                $summary = array_merge($finalizeFields, [
                    'santri_id' => $santriId,
                    'kelas_id' => $kelas->id,
                    'semester_id' => $semester->id,
                    'rata_rata' => $data['rata_rata'],
                    'total_mapel' => $data['total_mapel'],
                    'total_mapel_lulus' => $data['total_lulus'],
                    'ranking_kelas' => $ranking++,
                    'total_siswa_kelas' => $totalSiswa,
                    'total_kehadiran' => $data['kehadiran']->hadir ?? 0,
                    'total_sakit' => $data['kehadiran']->sakit ?? 0,
                    'total_izin' => $data['kehadiran']->izin ?? 0,
                    'total_alpa' => $data['kehadiran']->alpa ?? 0,
                    'catatan_wali_kelas' => $this->generateCatatanWaliKelas($data['rata_rata'], $ranking - 1),
                    'catatan_kepala_sekolah' => null,
                    'saran' => $this->generateSaran($data['rata_rata']),
                    'prestasi' => $this->generatePrestasi($data['rata_rata']),
                    'pelanggaran' => null,
                    'keputusan' => $this->generateKeputusan($data['total_lulus'], $data['total_mapel']),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $summaryData[] = $summary;
            }
        }

        DB::table('rapor_summary')->insert($summaryData);
    }

    private function generateCatatanWaliKelas($rataRata, $ranking): string
    {
        if ($rataRata >= 85) return "Prestasi sangat baik. Ranking $ranking di kelas. Pertahankan dan tingkatkan!";
        if ($rataRata >= 75) return "Prestasi baik. Terus tingkatkan belajar dan disiplin.";
        if ($rataRata >= 65) return "Prestasi cukup. Perlu lebih fokus dan rajin belajar.";
        return "Perlu bimbingan dan perhatian lebih dari orang tua.";
    }

    private function generateSaran($rataRata): string
    {
        if ($rataRata >= 85) return "Tingkatkan prestasi dan jadi teladan bagi teman.";
        if ($rataRata >= 75) return "Tingkatkan kemampuan di mata pelajaran yang masih kurang.";
        return "Perbanyak belajar dan bertanya kepada ustadz/ustadzah.";
    }

    private function generatePrestasi($rataRata): ?string
    {
        if ($rataRata >= 90) return json_encode(['Juara Kelas', 'Santri Teladan']);
        if ($rataRata >= 85) return json_encode(['Peringkat 3 Besar']);
        return null;
    }

    private function generateKeputusan($totalLulus, $totalMapel): string
    {
        $persenLulus = ($totalLulus / $totalMapel) * 100;
        if ($persenLulus >= 80) return 'naik_kelas';
        if ($persenLulus >= 60) return 'naik_kelas'; // Conditional
        return 'tinggal_kelas';
    }

    private function getApproverIds(): array
    {
        return DB::table('users as u')
            ->join('role_user as ru', 'u.id', '=', 'ru.user_id')
            ->join('roles as r', 'ru.role_id', '=', 'r.id')
            ->whereIn('r.kode', ['ADMIN', 'KEPSEK', 'WALI_KELAS'])
            ->pluck('u.id')
            ->toArray();
    }

    private function getFinalizeStatus($semester, array $approverIds): array
    {
        // Semester aktif → belum final
        if ($semester->is_active == 1) {
            return [
                'is_finalized' => 0,
                'finalized_by' => null,
                'finalized_at' => null,
            ];
        }

        // Semester non-aktif → sudah final
        return [
            'is_finalized' => 1,
            'finalized_by' => !empty($approverIds)
                ? collect($approverIds)->random()
                : null,
            'finalized_at' => Carbon::parse($semester->tanggal_selesai),
        ];
    }

}