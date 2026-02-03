<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $pengampuList = DB::table('pengampu')->get();
        
        $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $jamSlots = [
            ['07:00:00', '08:30:00'],
            ['08:30:00', '10:00:00'],
            ['10:15:00', '11:45:00'],
            ['13:00:00', '14:30:00'],
            ['14:30:00', '16:00:00'],
        ];

        $ruanganList = ['R101', 'R102', 'R103', 'R201', 'R202', 'R203', 'Lab Komp', 'Aula'];

        $jadwalData = [];
        $slotIndex = 0;
        $hariIndex = 0;

        // Group pengampu by kelas to avoid scheduling conflicts
        $pengampuByKelas = [];
        foreach ($pengampuList as $p) {
            $pengampuByKelas[$p->kelas_id][] = $p;
        }

        foreach ($pengampuByKelas as $kelasId => $pengampuKelas) {
            $slotIndex = 0;
            $hariIndex = 0;

            foreach ($pengampuKelas as $pengampu) {
                // Get bobot_sks for scheduling frequency
                $mapel = DB::table('mata_pelajaran')->find($pengampu->mata_pelajaran_id);
                $jumlahPertemuan = ceil($mapel->bobot_sks / 2); // 2 jam = 1 pertemuan

                for ($i = 0; $i < $jumlahPertemuan; $i++) {
                    $hari = $hariList[$hariIndex % count($hariList)];
                    $slot = $jamSlots[$slotIndex % count($jamSlots)];
                    $ruangan = $ruanganList[array_rand($ruanganList)];

                    $jadwalData = [
                        'pengampu_id' => $pengampu->id,
                        'hari' => $hari,
                        'jam_mulai' => $slot[0],
                        'jam_selesai' => $slot[1],
                        'ruangan' => $ruangan,
                        'is_active' => $pengampu->status == 'aktif' ? true : false,
                        'keterangan' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    DB::table('jadwal_pelajaran')->insert($jadwalData);

                    // Move to next slot
                    $slotIndex++;
                    if ($slotIndex % count($jamSlots) == 0) {
                        $hariIndex++;
                        $slotIndex = 0;
                    }
                }
            }
        }

        $this->command->info('✅  Jadwal Pelajaran seeded successfully!');
    }
}