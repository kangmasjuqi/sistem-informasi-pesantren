<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengampuSeeder extends Seeder
{
    public function run(): void
    {
        $semesterList = DB::table('semester')->orderBy('tanggal_mulai')->get();

        foreach ($semesterList as $semester) {
        
            $kelasList = DB::table('kelas')
                ->where('tahun_ajaran_id', $semester->tahun_ajaran_id)
                ->get();

            $mapelList = DB::table('mata_pelajaran')->where('is_active', 1)->get();
            $pengajarList = DB::table('pengajar')->where('status', 'aktif')->get();

            $pengampuData = [];
            $pengajarIndex = 0;

            foreach ($kelasList as $kelas) {
                foreach ($mapelList as $mapel) {
                    // Assign pengajar (cycling through available pengajar)
                    $pengajar = $pengajarList[$pengajarIndex % $pengajarList->count()];
                    $pengajarIndex++;

                    $pengampuData[] = [
                        'pengajar_id' => $pengajar->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'semester_id' => $semester->id,
                        'tanggal_mulai' => Carbon::parse($semester->tanggal_mulai)->format('Y-m-d'),
                        'tanggal_selesai' => $semester->is_active == 1 ? null : Carbon::parse($semester->tanggal_selesai)->format('Y-m-d'),
                        'status' => $semester->is_active == 1 ? 'aktif' : 'selesai',
                        'keterangan' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            DB::table('pengampu')->insert($pengampuData);
        }

        $this->command->info('✅ ' . count($pengampuData) . ' Pengampu seeded successfully!');
        $this->command->info('   (' . $kelasList->count() . ' kelas × ' . $mapelList->count() . ' mapel)');
    }
}