<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelasSantriSeeder extends Seeder
{
    public function run(): void
    {
        $santriList = DB::table('santri')->orderBy('id')->get();

        $tahunAjaranList = DB::table('tahun_ajaran')
            ->orderBy('tahun_mulai')
            ->get();

        // Track non aktif
        $santriNonAktif = [];

        // Track last tingkat per santri
        $lastTingkat = [];

        foreach ($santriList as $santri) {

            // Start from level 1
            $lastTingkat[$santri->id] = 1;

            foreach ($tahunAjaranList as $tahunAjaran) {

                /**
                 * Skip if masuk after tahun starts
                 */
                if (
                    $santri->tanggal_masuk &&
                    Carbon::parse($santri->tanggal_masuk)
                        ->gt(Carbon::parse($tahunAjaran->tanggal_mulai))
                ) {
                    continue;
                }

                /**
                 * Stop if already inactive
                 */
                if (in_array($santri->id, $santriNonAktif)) {
                    continue;
                }

                /**
                 * Get max tingkat
                 */
                $maxTingkat = DB::table('kelas')
                    ->max('tingkat');

                /**
                 * If finished all levels → graduate
                 */
                if ($lastTingkat[$santri->id] > $maxTingkat) {

                    $santriNonAktif[] = $santri->id;

                    DB::table('kelas_santri')->insert([
                        'kelas_id'      => null,
                        'santri_id'     => $santri->id,
                        'status'        => 'lulus',
                        'tanggal_masuk' => $tahunAjaran->tanggal_mulai,
                        'tanggal_keluar'=> $tahunAjaran->tanggal_selesai,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    continue;
                }

                /**
                 * Get kelas in current tingkat
                 */
                $kelasList = DB::table('kelas')
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->where('tingkat', $lastTingkat[$santri->id])
                    ->get();

                if ($kelasList->isEmpty()) {
                    continue;
                }

                /**
                 * Pick class in same level
                 */
                $kelas = $kelasList->random();


                /**
                 * Status probability
                 */
                $rand = rand(1, 100);

                if ($rand <= 94) {
                    $status = 'aktif';
                } elseif ($rand <= 96) {
                    $status = 'lulus';
                } elseif ($rand <= 98) {
                    $status = 'keluar';
                } else {
                    $status = 'pindah';
                }


                if ($status !== 'aktif') {
                    $santriNonAktif[] = $santri->id;
                }


                DB::table('kelas_santri')->insert([
                    'kelas_id'      => $kelas->id,
                    'santri_id'     => $santri->id,
                    'status'        => $status,

                    'tanggal_masuk' => $tahunAjaran->tanggal_mulai,

                    'tanggal_keluar'=> $status !== 'aktif'
                        ? $tahunAjaran->tanggal_selesai
                        : null,

                    'keterangan'    => null,

                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);


                /**
                 * Promote if still active
                 */
                if ($status === 'aktif') {
                    $lastTingkat[$santri->id]++;
                }
            }
        }

        $this->command->info('✅ Kelas-Santri seeded with promotion system!');
    }
}
