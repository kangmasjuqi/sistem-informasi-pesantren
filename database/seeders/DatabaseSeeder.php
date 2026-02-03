<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting STEP 1: Master Data Seeding...');
        $this->command->info('');

        // // STEP 1: MASTER DATA
        $this->command->info('📋 Seeding Master Data...');
        $this->call([
        //     \Database\Seeders\Master\RoleSeeder::class,
        //     \Database\Seeders\Master\UserSeeder::class,
        //     \Database\Seeders\Master\PengajarSeeder::class,
        //     \Database\Seeders\Master\SantriSeeder::class,
        //     \Database\Seeders\Master\WaliSantriSeeder::class,
        //     \Database\Seeders\Master\GedungSeeder::class,
        //     \Database\Seeders\Master\KamarSeeder::class,
        //     \Database\Seeders\Master\MataPelajaranSeeder::class,
        //     \Database\Seeders\Master\KomponenNilaiSeeder::class,
        //     \Database\Seeders\Master\JenisPembayaranSeeder::class,
        //     \Database\Seeders\Master\KategoriInventarisSeeder::class,
        //     \Database\Seeders\Master\TahunAjaranSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ STEP 1 Completed!');
        $this->command->info('');
        $this->command->info('🎉 All Master Data Seeded Successfully!');

        //////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////

        $this->command->info('🌱 Starting STEP 2: Academic Data Seeding...');
        $this->command->info('');

        // STEP 2: Academic DATA
        $this->command->info('📋 Seeding Academic Data...');
        $this->call([
            // \Database\Seeders\Academic\SemesterSeeder::class,
            // \Database\Seeders\Academic\KelasSeeder::class,
            // \Database\Seeders\Academic\KelasSantriSeeder::class,
            // \Database\Seeders\Academic\PenghuniKamarSeeder::class,
            // \Database\Seeders\Academic\PengampuSeeder::class,
            // \Database\Seeders\Academic\JadwalPelajaranSeeder::class,
        ]);
        $this->command->info('');
        $this->command->info('✅ STEP 2 Completed!');
        $this->command->info('');
        $this->command->info('🎉 All Academic Data Seeded Successfully!');

        //////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////

        $this->command->info('🌱 Starting STEP 3: Operational Data Seeding...');
        $this->command->info('');

        // STEP 3: Operational DATA
        $this->command->info('📋 Seeding Operational Data...');
        $this->call([
            // \Database\Seeders\Operational\InventarisSeeder::class,
            // \Database\Seeders\Operational\PerizinanSeeder::class,
            // \Database\Seeders\Operational\PembayaranSeeder::class,
            // \Database\Seeders\Operational\NilaiSeeder::class,
            // \Database\Seeders\Operational\KehadiranSeeder::class,
            // \Database\Seeders\Operational\RaporSeeder::class,
            // \Database\Seeders\Operational\RaporSummarySeeder::class,
        ]);
        $this->command->info('');
        $this->command->info('✅ STEP 3 Completed!');
        $this->command->info('');
        $this->command->info('🎉 All Operational Data Seeded Successfully!');

    }
}