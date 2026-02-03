<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = Carbon::now()->year;
        
        $tahunAjaran = [
            // 2 tahun yang lalu (2023/2024)
            [
                'nama' => ($currentYear - 3) . '/' . ($currentYear - 2),
                'tahun_mulai' => $currentYear - 3,
                'tahun_selesai' => $currentYear - 2,
                'tanggal_mulai' => Carbon::create($currentYear - 3, 7, 15)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::create($currentYear - 2, 6, 30)->format('Y-m-d'),
                'is_active' => 0,
                'keterangan' => 'Tahun ajaran yang sudah selesai',
            ],
            // Tahun lalu (2024/2025)
            [
                'nama' => ($currentYear - 2) . '/' . ($currentYear - 1),
                'tahun_mulai' => $currentYear - 2,
                'tahun_selesai' => $currentYear - 1,
                'tanggal_mulai' => Carbon::create($currentYear - 2, 7, 15)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::create($currentYear - 1, 6, 30)->format('Y-m-d'),
                'is_active' => 0,
                'keterangan' => 'Tahun ajaran yang sudah selesai',
            ],
            // Tahun sekarang (2025/2026)
            [
                'nama' => ($currentYear - 1) . '/' . $currentYear,
                'tahun_mulai' => $currentYear - 1,
                'tahun_selesai' => $currentYear,
                'tanggal_mulai' => Carbon::create($currentYear - 1, 7, 15)->format('Y-m-d'),
                'tanggal_selesai' => Carbon::create($currentYear, 6, 30)->format('Y-m-d'),
                'is_active' => 1,
                'keterangan' => 'Tahun ajaran yang sedang berjalan',
            ],
        ];

        foreach ($tahunAjaran as $ta) {
            DB::table('tahun_ajaran')->insert(array_merge($ta, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        $this->command->info('✅ 3 Tahun Ajaran seeded successfully!');
        $this->command->info('   - ' . ($currentYear - 3) . '/' . ($currentYear - 2) . ' (Selesai)');
        $this->command->info('   - ' . ($currentYear - 2) . '/' . ($currentYear - 1) . ' (Selesai)');
        $this->command->info('   - ' . ($currentYear - 1) . '/' . $currentYear . ' (Aktif)');
    }
}