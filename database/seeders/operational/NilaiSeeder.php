<?php

namespace Database\Seeders\Operational;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        // Get all komponen nilai
        $komponenList = DB::table('komponen_nilai')->where('is_active', 1)->get();

        $semesterAll = DB::table('semester')->orderBy('tanggal_mulai')->get();
        foreach ($semesterAll as $semester) {
            $this->generateNilai($semester, $komponenList);
        }

        $this->command->info('✅ Nilai records seeded!');
        $this->command->info('   (All components × all students × all pengampu)');
    }

    private function generateNilai($semester, $komponenList)
    {
        $pengampuList = DB::table('pengampu')
            ->where('semester_id', $semester->id);

        if ($semester->is_active == 1) {
            $pengampuList->where('status', 'aktif');
        }

        $pengampuList = $pengampuList->get();

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

        $batch = [];
        $batchSize = 500;

        $now = now();

        foreach ($pengampuList as $pengampu) {

            $santriList = $santriPerKelas->get($pengampu->kelas_id, collect());

            foreach ($santriList as $kelasSantri) {

                foreach ($komponenList as $komponen) {

                    $nilai = $this->generateRealisticGrade();

                    $tanggalInput = Carbon::parse($semester->tanggal_selesai)->subDays(rand(0, 7))->toDateString();

                    $batch[] = [
                        'santri_id' => $kelasSantri->santri_id,
                        'pengampu_id' => $pengampu->id,
                        'komponen_nilai_id' => $komponen->id,
                        'nilai' => $nilai,
                        'catatan' => $nilai < 60
                            ? 'Perlu perbaikan'
                            : ($nilai >= 90 ? 'Sangat baik' : null),
                        'tanggal_input' => $tanggalInput,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // Insert tiap 500
                    if (count($batch) >= $batchSize) {
                        DB::table('nilai')->insert($batch);
                        $batch = []; // reset memory
                    }
                }
            }
        }

        // Sisa terakhir
        if (!empty($batch)) {
            DB::table('nilai')->insert($batch);
        }
    }

    private function generateRealisticGrade(): float
    {
        // Normal distribution: mean=78, std=10
        // 68% between 68-88, 95% between 58-98
        $mean = 78;
        $stdDev = 10;
        
        // Box-Muller transform for normal distribution
        $u1 = mt_rand() / mt_getrandmax();
        $u2 = mt_rand() / mt_getrandmax();
        $z = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);
        
        $grade = $mean + $z * $stdDev;
        
        // Clamp between 50-100
        $grade = max(50, min(100, $grade));
        
        // Round to 2 decimals
        return round($grade, 2);
    }
}