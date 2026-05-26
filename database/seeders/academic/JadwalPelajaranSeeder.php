<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalPelajaranSeeder extends Seeder
{
    // ── Tuneable constants ────────────────────────────────────
    const DURASI_JAM       = 45;
    const DURASI_ISTIRAHAT = 15;
    const JUMLAH_JAM       = 8;
    const JAM_MULAI_HARI   = '07:00';

    // Breaks happen AFTER these period numbers
    const ISTIRAHAT_SETELAH_JAM = [4, 6];

    public function run(): void
    {
        // ── Step 1: Build bell schedule ───────────────────────
        $slots = $this->buildSlots();

        $this->command->info('📅 Generated time slots:');
        foreach ($slots as $slot) {
            $break = in_array($slot['jam_ke'], self::ISTIRAHAT_SETELAH_JAM) ? '  ← istirahat' : '';
            $this->command->line("   Jam {$slot['jam_ke']}: {$slot['mulai']} – {$slot['selesai']}{$break}");
        }

        // ── Step 2: Load all pengampu grouped by kelas+semester ──
        $hariList    = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $ruanganList = ['R101', 'R102', 'R103', 'R201', 'R202', 'R203', 'Lab Komp', 'Aula'];

        $pengampuList = DB::table('pengampu')->get();

        if ($pengampuList->isEmpty()) {
            $this->command->error('❌ No pengampu found. Seed pengampu first.');
            return;
        }

        // Group by "kelas_id-semester_id" — slots are independent per combo
        $pengampuByGroup = $pengampuList->groupBy(
            fn($p) => "{$p->kelas_id}-{$p->semester_id}"
        );

        $this->command->info(
            "📋 Found {$pengampuList->count()} pengampu across " .
            $pengampuByGroup->count() . " kelas-semester groups."
        );

        $totalInserted = 0;
        $totalWarnings = 0;

        foreach ($pengampuByGroup as $groupKey => $pengampuGroup) {

            [$kelasId, $semesterId] = explode('-', $groupKey);

            // Slot usage tracker per kelas-semester: "hari-jam_ke" → true
            $usedSlots = [];

            foreach ($pengampuGroup as $pengampu) {

                $mapel = DB::table('mata_pelajaran')->find($pengampu->mata_pelajaran_id);
                $sks   = max(1, (int) ($mapel->bobot_sks ?? 2));

                /**
                 * SKS → sessions per week:
                 *   1 SKS → [1]        one single period, one day
                 *   2 SKS → [2]        two consecutive periods, same day
                 *   3 SKS → [2, 1]     double + single, different days
                 *   4 SKS → [2, 2]     two doubles, different days
                 */
                $sessions       = $this->sksToSessions($sks);
                $usedHariByThis = []; // days already used by THIS subject this week

                foreach ($sessions as $blockSize) {
                    $placed = false;

                    foreach ($hariList as $hari) {

                        // Each session of the same subject must be on a different day
                        if (in_array($hari, $usedHariByThis)) continue;

                        $startJam = $this->findFreeBlock(
                            $usedSlots, $hari, count($slots), $blockSize
                        );

                        if ($startJam === null) continue;

                        $ruangan = $ruanganList[array_rand($ruanganList)];

                        for ($p = 0; $p < $blockSize; $p++) {
                            $jamKe = $startJam + $p;
                            $slot  = $slots[$jamKe - 1];

                            $roll_status = rand(1, 100);

                            DB::table('jadwal_pelajaran')->insert([
                                'pengampu_id' => $pengampu->id,
                                'kelas_id'    => $pengampu->kelas_id,
                                'pengajar_id' => $pengampu->pengajar_id,
                                'hari'        => $hari,
                                'jam_ke'      => $jamKe,
                                'jam_mulai'   => $slot['mulai'] . ':00',
                                'jam_selesai' => $slot['selesai'] . ':00',
                                'ruangan'     => $ruangan,
                                'status' => match(true) {
                                    $roll_status <= 90 => 'aktif',
                                    $roll_status <= 97 => 'diganti',
                                    default     => 'libur',
                                },
                                'keterangan'  => null,
                                'created_at'  => Carbon::now(),
                                'updated_at'  => Carbon::now(),
                            ]);

                            $usedSlots["{$hari}-{$jamKe}"] = true;
                            $totalInserted++;
                        }

                        $usedHariByThis[] = $hari;
                        $placed = true;
                        break;
                    }

                    if (!$placed) {
                        $this->command->warn(
                            "⚠️  No room: pengampu_id={$pengampu->id} " .
                            "({$mapel->nama_mapel}) sks={$sks} block={$blockSize} " .
                            "kelas={$kelasId} sem={$semesterId}"
                        );
                        $totalWarnings++;
                    }
                }
            }
        }

        $this->command->info("✅  Done: {$totalInserted} rows inserted, {$totalWarnings} warnings.");
    }

    /**
     * Convert SKS into block sizes per session.
     *
     *   1 → [1]
     *   2 → [2]
     *   3 → [2, 1]
     *   4 → [2, 2]
     *   5 → [2, 2, 1]
     */
    private function sksToSessions(int $sks): array
    {
        $sessions  = [];
        $remaining = $sks;
        while ($remaining > 0) {
            $block      = min(2, $remaining);
            $sessions[] = $block;
            $remaining -= $block;
        }
        return $sessions;
    }

    /**
     * Build the bell schedule array.
     * Breaks are gaps (not rows) inserted after the listed period numbers.
     */
    private function buildSlots(): array
    {
        $slots   = [];
        $current = Carbon::createFromFormat('H:i', self::JAM_MULAI_HARI);

        for ($jamKe = 1; $jamKe <= self::JUMLAH_JAM; $jamKe++) {
            $mulai = $current->format('H:i');
            $current->addMinutes(self::DURASI_JAM);
            $selesai = $current->format('H:i');

            $slots[] = [
                'jam_ke'  => $jamKe,
                'mulai'   => $mulai,
                'selesai' => $selesai,
            ];

            if (in_array($jamKe, self::ISTIRAHAT_SETELAH_JAM)) {
                $current->addMinutes(self::DURASI_ISTIRAHAT);
            }
        }

        return $slots;
    }

    /**
     * Find the first free starting period (1-based) where $blockSize
     * consecutive periods are all free and don't straddle a break.
     */
    private function findFreeBlock(
        array  $usedSlots,
        string $hari,
        int    $totalSlots,
        int    $blockSize
    ): ?int {
        for ($start = 1; $start <= ($totalSlots - $blockSize + 1); $start++) {

            // Block must not straddle a break boundary
            if ($blockSize > 1) {
                $straddlesBreak = false;
                for ($p = $start; $p < $start + $blockSize - 1; $p++) {
                    if (in_array($p, self::ISTIRAHAT_SETELAH_JAM)) {
                        $straddlesBreak = true;
                        break;
                    }
                }
                if ($straddlesBreak) continue;
            }

            // All periods in block must be free
            $allFree = true;
            for ($p = $start; $p < $start + $blockSize; $p++) {
                if (!empty($usedSlots["{$hari}-{$p}"])) {
                    $allFree = false;
                    break;
                }
            }

            if ($allFree) return $start;
        }

        return null;
    }
}