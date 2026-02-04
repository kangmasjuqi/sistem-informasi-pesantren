<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'nama_lengkap' => 'Administrator Sistem',
                'username' => 'admin',
                'email' => 'admin@pesantren.id',
                'password' => Hash::make('password'),
                'telepon' => '081234567890',
                'alamat' => 'Kantor Pesantren',
                'status' => 'aktif',
                'roles' => ['SUPERADMIN', 'ADMIN']
            ],
            [
                'name' => 'KH Ahmad Dahlan',
                'nama_lengkap' => 'KH Ahmad Dahlan Al-Hafidz',
                'username' => 'ahmad.dahlan',
                'email' => 'kepsek@pesantren.id',
                'password' => Hash::make('password'),
                'telepon' => '081234567891',
                'alamat' => 'Pesantren Al-Hikmah',
                'status' => 'aktif',
                'roles' => ['KEPSEK']
            ],
            [
                'name' => 'Siti Nur',
                'nama_lengkap' => 'Siti Nur Khadijah, S.E',
                'username' => 'siti.nur',
                'email' => 'bendahara@pesantren.id',
                'password' => Hash::make('password'),
                'telepon' => '081234567896',
                'alamat' => 'Jl. Melati No. 12',
                'status' => 'aktif',
                'roles' => ['BENDAHARA']
            ],
            [
                'name' => 'Mujahidin',
                'nama_lengkap' => 'Mujahidin Ahmad',
                'username' => 'mujahidin',
                'email' => 'tu@pesantren.id',
                'password' => Hash::make('password'),
                'telepon' => '081234567897',
                'alamat' => 'Jl. Dakwah No. 20',
                'status' => 'aktif',
                'roles' => ['STAFF_TU']
            ],
            [
                'name' => 'Hamzah',
                'nama_lengkap' => 'Hamzah Fakhruddin',
                'username' => 'hamzah',
                'email' => 'hamzah@pesantren.id',
                'password' => Hash::make('password'),
                'telepon' => '081234567898',
                'alamat' => 'Jl. Iman No. 5',
                'status' => 'aktif',
                'roles' => ['STAFF_TU']
            ],
            [
                'name' => 'Marjuqi',
                'nama_lengkap' => 'Marjuqi Wali',
                'username' => 'marjuqiwali',
                'email' => 'marjuqiwali@pesantren.id',
                'password' => Hash::make('password'),
                'telepon' => '081234567898',
                'alamat' => 'Jl. Pemijen No. 5',
                'status' => 'aktif',
                'roles' => ['WALI']
            ],
            [
                'name' => 'Rahmat',
                'nama_lengkap' => 'Rahmat Santri',
                'username' => 'rahmatsantri',
                'email' => 'rahmatsantri@pesantren.id',
                'password' => Hash::make('password'),
                'telepon' => '081234567898',
                'alamat' => 'Jl. Pemijen No. 4',
                'status' => 'aktif',
                'roles' => ['SANTRI']
            ]
        ];

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);

            $userId = DB::table('users')->insertGetId(array_merge($userData, [
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));

            foreach ($roles as $roleKode) {
                $role = DB::table('roles')->where('kode', $roleKode)->first();
                if ($role) {
                    DB::table('role_user')->insert([
                        'user_id' => $userId,
                        'role_id' => $role->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ 10 Users seeded successfully!');
    }
}