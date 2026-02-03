<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nama' => 'Super Admin', 'kode' => 'SUPERADMIN', 'deskripsi' => 'Administrator sistem dengan akses penuh', 'is_active' => 1],
            ['nama' => 'Admin', 'kode' => 'ADMIN', 'deskripsi' => 'Administrator yang mengelola data pesantren', 'is_active' => 1],
            ['nama' => 'Kepala Sekolah', 'kode' => 'KEPSEK', 'deskripsi' => 'Kepala Sekolah/Mudir pesantren', 'is_active' => 1],
            ['nama' => 'Ustadz/Ustadzah', 'kode' => 'PENGAJAR', 'deskripsi' => 'Pengajar mata pelajaran', 'is_active' => 1],
            ['nama' => 'Wali Kelas', 'kode' => 'WALIKELAS', 'deskripsi' => 'Wali kelas pembimbing', 'is_active' => 1],
            ['nama' => 'Staff TU', 'kode' => 'STAFF_TU', 'deskripsi' => 'Staff Tata Usaha', 'is_active' => 1],
            ['nama' => 'Bendahara', 'kode' => 'BENDAHARA', 'deskripsi' => 'Bendahara keuangan', 'is_active' => 1],
            ['nama' => 'Santri', 'kode' => 'SANTRI', 'deskripsi' => 'Santri pesantren', 'is_active' => 1],
            ['nama' => 'Wali Santri', 'kode' => 'WALI', 'deskripsi' => 'Orang tua/wali santri', 'is_active' => 1],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        $this->command->info('✅ 9 Roles seeded successfully!');
    }
}