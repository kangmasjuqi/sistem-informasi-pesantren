<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaranList = DB::table('tahun_ajaran')->orderBy('tahun_mulai')->get();
        $pengajarList = DB::table('pengajar')->where('status', 'aktif')->pluck('id')->toArray();
        
        $kelasData = [];
        
        // Structure kelas: 3 tingkat (1, 2, 3) x 3 kelas (A, B, C) = 9 kelas
        $tingkatList = [1, 2, 3];
        $kelasList = ['A', 'B', 'C'];

        foreach ($tahunAjaranList as $ta) {
            $waliKelasIndex = 0;
            
            foreach ($tingkatList as $tingkat) {
                foreach ($kelasList as $kelas) {
                    // Assign wali kelas (cycling through pengajar)
                    $waliKelasId = $pengajarList[$waliKelasIndex % count($pengajarList)];
                    $waliKelasIndex++;

                    $kelasData[] = [
                        'tahun_ajaran_id' => $ta->id,
                        'wali_kelas_id' => $waliKelasId,
                        'nama_kelas' => $tingkat . $kelas,
                        'tingkat' => (string)$tingkat,
                        'kapasitas' => 35,
                        'deskripsi' => 'Kelas ' . $tingkat . $kelas . ' Tahun Ajaran ' . $ta->nama,
                        'is_active' => $ta->is_active,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        DB::table('kelas')->insert($kelasData);

        $this->command->info('✅ ' . count($kelasData) . ' Kelas seeded successfully!');
        $this->command->info('   (9 kelas per tahun ajaran: 1A-3C)');
    }
}