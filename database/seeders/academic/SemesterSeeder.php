<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaranList = DB::table('tahun_ajaran')->orderBy('tahun_mulai')->get();
        $semesterData = [];

        foreach ($tahunAjaranList as $ta) {
            // Semester Ganjil (Juli - Desember)
            $semesterData[] = [
                'tahun_ajaran_id' => $ta->id,
                'jenis_semester' => 'ganjil',
                'nama' => 'Semester Ganjil ' . $ta->nama,
                'tanggal_mulai' => Carbon::parse($ta->tanggal_mulai)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::parse($ta->tanggal_mulai)->addMonths(6)->subDay()->format('Y-m-d'),
                'is_active' => 0, // Ganjil tidak active dulu
                'keterangan' => 'Semester Ganjil tahun ajaran ' . $ta->nama,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Semester Genap (Januari - Juni)
            $semesterData[] = [
                'tahun_ajaran_id' => $ta->id,
                'jenis_semester' => 'genap',
                'nama' => 'Semester Genap ' . $ta->nama,
                'tanggal_mulai' => Carbon::parse($ta->tanggal_mulai)->addMonths(6)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::parse($ta->tanggal_selesai)->format('Y-m-d'),
                'is_active' => $ta->is_active, // Active jika tahun ajaran active
                'keterangan' => 'Semester Genap tahun ajaran ' . $ta->nama,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('semester')->insert($semesterData);

        $this->command->info('✅ ' . count($semesterData) . ' Semester seeded successfully!');
        $this->command->info('   (2 semesters per tahun ajaran: Ganjil & Genap)');
    }
}