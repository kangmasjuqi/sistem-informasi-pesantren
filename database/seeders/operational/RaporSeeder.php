<?php

namespace Database\Seeders\Operational;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RaporSeeder extends Seeder
{
    public function run(): void
    {
        // Get komponen with weights
        $komponenList = DB::table('komponen_nilai')->where('is_active', 1)->get();

        $approverIds = $this->getApproverIds();

        // Only generate rapor for passed semesters
        $semesterAll = DB::table('semester')->where('is_active', 0)->orderBy('tanggal_mulai')->get();

        foreach ($semesterAll as $semester) {
            $this->generateRapor($semester, $komponenList, $approverIds);
        }

        $this->command->info('✅ Rapor records seeded!');
    }

    private function generateRapor($semester, $komponenList, $approverIds)
    {
        $pengampuList = DB::table('pengampu')
            ->where('semester_id', $semester->id);

        if ($semester->is_active == 1) {
            $pengampuList->where('status', 'aktif');
        }

        $pengampuList = $pengampuList->get();

        $totalBobot = $komponenList->sum('bobot');

        // Get santri per kelas
        $santriPerKelas = DB::table('kelas_santri as ks')
            ->join('kelas as k', 'ks.kelas_id', '=', 'k.id')
            ->where('k.tahun_ajaran_id', $semester->tahun_ajaran_id);

        if ($semester->is_active == 1) {
            $santriPerKelas->where('ks.status', 'aktif');
        }

        $santriPerKelas = $santriPerKelas
            ->select('ks.santri_id', 'ks.kelas_id')
            ->get()
            ->groupBy('kelas_id');

        $raporData = [];

        foreach ($pengampuList as $pengampu) {
            $santriList = $santriPerKelas->get($pengampu->kelas_id, collect());
            $nilaiAkhirPerSantri = [];

            foreach ($santriList as $kelasSantri) {
                // Get all nilai for this santri-pengampu
                $nilaiList = DB::table('nilai')
                    ->where('santri_id', $kelasSantri->santri_id)
                    ->where('pengampu_id', $pengampu->id)
                    ->get();

                // Calculate weighted average
                $nilaiAkhir = 0;
                foreach ($nilaiList as $nilai) {
                    $komponen = $komponenList->firstWhere('id', $nilai->komponen_nilai_id);
                    if ($komponen) {
                        $nilaiAkhir += ($nilai->nilai * $komponen->bobot / $totalBobot);
                    }
                }

                $nilaiAkhirPerSantri[$kelasSantri->santri_id] = round($nilaiAkhir, 2);
            }

            // Calculate ranking
            arsort($nilaiAkhirPerSantri);
            $ranking = 1;
            foreach ($nilaiAkhirPerSantri as $santriId => $nilaiAkhir) {
                [$huruf, $angka, $predikat] = $this->convertGrade($nilaiAkhir);

                $finalize = $this->getFinalizeStatus($semester, $approverIds);

                $raporData[] = [
                    'santri_id' => $santriId,
                    'pengampu_id' => $pengampu->id,
                    'nilai_akhir' => $nilaiAkhir,
                    'nilai_huruf' => $huruf,
                    'nilai_angka' => $angka,
                    'predikat' => $predikat,
                    'ranking_kelas' => $ranking++,
                    'catatan_pengajar' => $this->generateCatatan($nilaiAkhir),
                    'catatan_wali_kelas' => '',
                    'is_lulus' => $nilaiAkhir >= 60 ? 1 : 0,
                    'is_finalized' => $finalize['is_finalized'],
                    'finalized_by' => $finalize['finalized_by'],
                    'finalized_at' => $finalize['finalized_at'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

            }
        }

        // Insert in chunks
        foreach (array_chunk($raporData, 500) as $chunk) {
            DB::table('rapor')->insert($chunk);
        }
    }

    private function convertGrade($nilai): array
    {
        if ($nilai >= 90) return ['A', 4.00, 'sangat_baik'];
        if ($nilai >= 85) return ['B+', 3.50, 'baik'];
        if ($nilai >= 80) return ['B', 3.00, 'baik'];
        if ($nilai >= 75) return ['C+', 2.50, 'cukup'];
        if ($nilai >= 70) return ['C', 2.00, 'cukup'];
        if ($nilai >= 60) return ['D', 1.00, 'kurang'];
        return ['E', 0.00, 'kurang'];
    }

    private function generateCatatan($nilai): ?string
    {
        if ($nilai >= 90) return 'Prestasi sangat memuaskan, pertahankan!';
        if ($nilai >= 80) return 'Prestasi baik, tingkatkan terus!';
        if ($nilai >= 70) return 'Cukup baik, perlu peningkatan.';
        if ($nilai >= 60) return 'Perlu bimbingan lebih intensif.';
        return 'Perlu perbaikan dan perhatian khusus.';
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
            'finalized_by' => collect($approverIds)->random(),
            'finalized_at' => Carbon::parse($semester->tanggal_selesai),
        ];
    }

}