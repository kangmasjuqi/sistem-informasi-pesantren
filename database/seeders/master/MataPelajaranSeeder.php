<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $mapel = [
            // Agama (14 mapel)
            ['kode_mapel' => 'AQD', 'nama_mapel' => 'Aqidah', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'QUR', 'nama_mapel' => 'Al-Quran', 'kategori' => 'agama', 'bobot_sks' => 4],
            ['kode_mapel' => 'TAF', 'nama_mapel' => 'Tafsir', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'HAD', 'nama_mapel' => 'Hadits', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'FQH', 'nama_mapel' => 'Fiqh', 'kategori' => 'agama', 'bobot_sks' => 3],
            ['kode_mapel' => 'USH', 'nama_mapel' => 'Ushul Fiqh', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'ARB', 'nama_mapel' => 'Bahasa Arab', 'kategori' => 'agama', 'bobot_sks' => 4],
            ['kode_mapel' => 'NHW', 'nama_mapel' => 'Nahwu', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'SHR', 'nama_mapel' => 'Sharaf', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'AKH', 'nama_mapel' => 'Akhlak Tasawuf', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'SKI', 'nama_mapel' => 'Sejarah Kebudayaan Islam', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'TJW', 'nama_mapel' => 'Tajwid', 'kategori' => 'agama', 'bobot_sks' => 2],
            ['kode_mapel' => 'THF', 'nama_mapel' => 'Tahfidz Quran', 'kategori' => 'agama', 'bobot_sks' => 4],
            ['kode_mapel' => 'MHD', 'nama_mapel' => 'Muthola\'ah Hadits', 'kategori' => 'agama', 'bobot_sks' => 2],
            
            // Umum (8 mapel)
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika', 'kategori' => 'umum', 'bobot_sks' => 3],
            ['kode_mapel' => 'IPA', 'nama_mapel' => 'IPA Terpadu', 'kategori' => 'umum', 'bobot_sks' => 3],
            ['kode_mapel' => 'IPS', 'nama_mapel' => 'IPS Terpadu', 'kategori' => 'umum', 'bobot_sks' => 2],
            ['kode_mapel' => 'IND', 'nama_mapel' => 'Bahasa Indonesia', 'kategori' => 'umum', 'bobot_sks' => 3],
            ['kode_mapel' => 'ING', 'nama_mapel' => 'Bahasa Inggris', 'kategori' => 'umum', 'bobot_sks' => 2],
            ['kode_mapel' => 'PKN', 'nama_mapel' => 'PKn', 'kategori' => 'umum', 'bobot_sks' => 2],
            ['kode_mapel' => 'SBD', 'nama_mapel' => 'Seni Budaya', 'kategori' => 'umum', 'bobot_sks' => 1],
            ['kode_mapel' => 'PJK', 'nama_mapel' => 'Penjas Orkes', 'kategori' => 'umum', 'bobot_sks' => 2],
            
            // Keterampilan (4 mapel)
            ['kode_mapel' => 'KOM', 'nama_mapel' => 'Komputer/TIK', 'kategori' => 'keterampilan', 'bobot_sks' => 2],
            ['kode_mapel' => 'KAL', 'nama_mapel' => 'Kaligrafi', 'kategori' => 'keterampilan', 'bobot_sks' => 1],
            ['kode_mapel' => 'KHA', 'nama_mapel' => 'Khitobah (Pidato)', 'kategori' => 'keterampilan', 'bobot_sks' => 1],
            ['kode_mapel' => 'KWU', 'nama_mapel' => 'Kewirausahaan', 'kategori' => 'keterampilan', 'bobot_sks' => 2],
        ];

        foreach ($mapel as $m) {
            DB::table('mata_pelajaran')->insert(array_merge($m, [
                'deskripsi' => 'Mata pelajaran ' . $m['nama_mapel'],
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        $this->command->info('✅ 26 Mata Pelajaran seeded successfully!');
    }
}