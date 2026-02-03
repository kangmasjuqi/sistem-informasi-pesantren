<?php

namespace Database\Seeders\Operational;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KehadiranSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data to avoid duplicates (optional)
        // DB::table('kehadiran')->truncate();
        
        $semesterAll = DB::table('semester')->orderBy('tanggal_mulai')->get();

        foreach ($semesterAll as $semester) {
            $this->generateKehadiran($semester);
            
            // Force garbage collection after each semester
            gc_collect_cycles();
        }

        $this->command->info('✅ All Kehadiran records seeded!');
    }

    private function generateKehadiran($semester)
    {
        // Get all jadwal for this semester
        $jadwalList = DB::table('jadwal_pelajaran as jp')
            ->join('pengampu as p', 'jp.pengampu_id', '=', 'p.id')
            ->where('p.semester_id', $semester->id)
            ->select('jp.*', 'p.kelas_id', 'p.semester_id')
            ->get();

        if ($jadwalList->isEmpty()) {
            $this->command->warn("⚠️ Semester {$semester->id}: No jadwal found");
            return;
        }

        // $arrSantriKelasStatusNonAktif = ['lulus','lulus','lulus','lulus','lulus','pindah','keluar'];
        // $santriKelasStatusNonAktif = $arrSantriKelasStatusNonAktif[rand(0,6)];

        // Get all santri in classes
        $santriPerKelas = DB::table('kelas_santri as ks')
            ->join('kelas as k', 'ks.kelas_id', '=', 'k.id')
            ->where('k.tahun_ajaran_id', $semester->tahun_ajaran_id)
            // ->where('ks.status', in_array($semester->id, [5,6]) ? 'aktif' : $santriKelasStatusNonAktif)
            ->select('ks.santri_id', 'ks.kelas_id')
            ->get()
            ->groupBy('kelas_id');

        if ($santriPerKelas->isEmpty()) {
            $this->command->warn("⚠️ Semester {$semester->id}: No santri found");
            return;
        }
        
        // Define semester date boundaries
        $semesterStart = Carbon::parse($semester->tanggal_mulai);
        $semesterEnd = Carbon::parse($semester->tanggal_selesai);
        $now = Carbon::now();
        
        // Determine start and end dates based on semester status
        if ($semester->is_active == 1) {
            // CURRENT ACTIVE SEMESTER - last 2 months only
            $twoMonthsAgo = $now->copy()->subMonths(2);
            $startDate = $semesterStart->max($twoMonthsAgo);
            $endDate = $now->min($semesterEnd);
        } else {
            // PAST SEMESTER - limit to last months for performance
            $startDate = $semesterStart;
            $endDate = $semesterEnd;
        }
        
        // Validation: ensure startDate is not after endDate
        if ($startDate->gt($endDate)) {
            $this->command->warn("⚠️ Skipping semester {$semester->id}: Invalid date range (start > end)");
            return;
        }
        
        // Validation: ensure dates are within semester boundaries
        if ($startDate->lt($semesterStart)) {
            $startDate = $semesterStart;
        }
        if ($endDate->gt($semesterEnd)) {
            $endDate = $semesterEnd;
        }

        $totalRecords = 0;

        // Process each jadwal separately to avoid memory buildup
        foreach ($jadwalList as $jadwal) {
            $kelasId = $jadwal->kelas_id;
            $santriList = $santriPerKelas->get($kelasId, collect());

            if ($santriList->isEmpty()) {
                continue;
            }

            $kehadiranData = [];
            
            // Generate for each occurrence of this jadwal
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                // Check if this day matches jadwal's hari
                $dayName = strtolower($currentDate->locale('id')->dayName);
                
                if ($dayName === $jadwal->hari) {
                    foreach ($santriList as $kelasSantri) {

                        // Generate random number 1-100
                        $rand = rand(1, 100);

                        if ($rand <= 90) {
                            // 90% chance: hadir
                            $status = 'hadir';
                        } elseif ($rand <= 93) {
                            // 3% chance: sakit
                            $status = 'sakit';
                        } elseif ($rand <= 97) {
                            // 4% chance: izin
                            $status = 'izin';
                        } else {
                            // 3% chance: alpa
                            $status = 'alpa';
                        }

                        $waktuAbsen = $status === 'hadir' 
                            ? Carbon::parse($jadwal->jam_mulai)->addMinutes(rand(-5, 10))->format('H:i:s')
                            : null;

                        $kehadiranData[] = [
                            'tanggal' => $currentDate->format('Y-m-d'),
                            'santri_id' => $kelasSantri->santri_id,
                            'pengampu_id' => $jadwal->pengampu_id,
                            'jadwal_pelajaran_id' => $jadwal->id,
                            'jenis_kehadiran' => 'pelajaran',
                            'status_kehadiran' => $status,
                            'waktu_absen' => $waktuAbsen,
                            'keterangan_kegiatan' => null,
                            'keterangan' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                        
                        // Insert in smaller chunks (every 250 records)
                        if (count($kehadiranData) >= 250) {
                            DB::table('kehadiran')->insert($kehadiranData);
                            $totalRecords += count($kehadiranData);
                            $kehadiranData = []; // Clear array
                        }
                    }
                }
                
                $currentDate->addDay();
            }
            
            // Insert remaining data for this jadwal
            if (!empty($kehadiranData)) {
                DB::table('kehadiran')->insert($kehadiranData);
                $totalRecords += count($kehadiranData);
                unset($kehadiranData); // Free memory
            }
        }

        if ($totalRecords > 0) {
            $semesterType = $semester->is_active == 1 ? 'ACTIVE' : 'PAST';
            $this->command->info("✅ [{$semesterType}] Semester {$semester->id}: {$totalRecords} kehadiran records ({$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')})");
        } else {
            $this->command->warn("⚠️ Semester {$semester->id}: No kehadiran data generated");
        }
    }
}