<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KomponenNilaiSeeder extends Seeder
{
    public function run(): void
    {
        $komponen = [
            [
                'kode' => 'UTS',
                'nama' => 'Ujian Tengah Semester',
                'bobot' => 30,
                'deskripsi' => 'Ujian yang dilaksanakan di tengah semester',
            ],
            [
                'kode' => 'UAS',
                'nama' => 'Ujian Akhir Semester',
                'bobot' => 40,
                'deskripsi' => 'Ujian yang dilaksanakan di akhir semester',
            ],
            [
                'kode' => 'TUGAS',
                'nama' => 'Tugas dan Kuis',
                'bobot' => 20,
                'deskripsi' => 'Nilai dari tugas harian dan kuis',
            ],
            [
                'kode' => 'PRAKTEK',
                'nama' => 'Praktik',
                'bobot' => 10,
                'deskripsi' => 'Nilai praktik dan keaktifan di kelas',
            ],
        ];

        foreach ($komponen as $k) {
            DB::table('komponen_nilai')->insert(array_merge($k, [
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        $this->command->info('✅ 4 Komponen Nilai seeded successfully! (Total bobot: 100%)');
    }
}