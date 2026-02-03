<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PengajarSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now   = Carbon::now();

        // Nama khas Indonesia
        $namaPria = [
            'Ahmad','Muhammad','Abdullah','Yusuf','Ibrahim','Ali',
            'Hassan','Umar','Khalid','Salman','Rizki','Fauzi','Hakim','Amin'
        ];

        $namaWanita = [
            'Fatimah','Aisyah','Khadijah','Maryam','Zainab','Aminah',
            'Siti','Nur','Dewi','Sri','Putri','Indah'
        ];

        $namaBelakang = [
            'Rahman','Hidayat','Hakim','Syafiq','Karim','Hadi',
            'Wahid','Amin','Wibowo','Santoso','Kurniawan','Setiawan'
        ];

        $gelar = ['S.Pd.I','S.Ag','Lc','M.Pd.I','M.A','S.Pd','S.Si',''];

        $kota = [
            'Jakarta','Bandung','Surabaya','Yogyakarta','Solo','Semarang',
            'Malang','Bogor','Depok','Bekasi','Tangerang','Cirebon'
        ];

        $univ = [
            'UIN Sunan Kalijaga',
            'UIN Syarif Hidayatullah',
            'IAIN Surakarta',
            'Universitas Al-Azhar Cairo',
            'LIPIA Jakarta',
            'Ma\'had Aly'
        ];

        $jurusan = [
            'Pendidikan Agama Islam',
            'Pendidikan Bahasa Arab',
            'Syariah','Tarbiyah','Ushuluddin',
            'Tahfidz Quran','Tafsir','Fiqh'
        ];

        $keahlian = [
            'Tahfidz Quran','Nahwu Shorof','Fiqh','Hadits','Tafsir',
            'Bahasa Arab','Matematika','Bahasa Inggris','Sejarah Islam'
        ];

        // Ambil role PENGAJAR
        $rolePengajar = DB::table('roles')
            ->where('kode', 'PENGAJAR')
            ->first();

        if (!$rolePengajar) {
            $this->command->error('❌ Role PENGAJAR tidak ditemukan!');
            return;
        }

        $nipCounter = 2020001;

        for ($i = 1; $i <= 30; $i++) {

            /* ===============================
             | Generate Identitas
             =============================== */

            $jk = $i % 2 === 0 ? 'laki-laki' : 'perempuan';

            $namaDepan = $jk === 'laki-laki'
                ? $namaPria[array_rand($namaPria)]
                : $namaWanita[array_rand($namaWanita)];

            $namaLengkap =
                $namaDepan . ' ' .
                $namaBelakang[array_rand($namaBelakang)] .
                ', ' . $gelar[array_rand($gelar)];

            $namaLengkap = trim(str_replace(', ,', ',', $namaLengkap));

            $username = strtolower($namaDepan) . $i;

            $email = $username . '@pesantren.id';


            /* ===============================
             | 1. INSERT USER
             =============================== */

            $userId = DB::table('users')->insertGetId([
                'name'            => $namaDepan,
                'nama_lengkap'    => $namaLengkap,
                'username'        => $username,
                'email'           => $email,
                'password'        => Hash::make('password'),
                'telepon'         => $faker->phoneNumber(),
                'alamat'          => $faker->address(),
                'status'          => 'aktif',
                'email_verified_at' => $now,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);


            /* ===============================
             | 2. ASSIGN ROLE
             =============================== */

            DB::table('role_user')->insert([
                'user_id'    => $userId,
                'role_id'    => $rolePengajar->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);


            /* ===============================
             | 3. INSERT PENGAJAR
             =============================== */

            DB::table('pengajar')->insert([

                'user_id' => $userId,

                'nip' => 'NIP' . str_pad($nipCounter++, 7, '0', STR_PAD_LEFT),

                'nama_lengkap' => $namaLengkap,

                'jenis_kelamin' => $jk,

                'tempat_lahir' => $kota[array_rand($kota)],

                'tanggal_lahir' => $faker
                    ->dateTimeBetween('-50 years', '-25 years')
                    ->format('Y-m-d'),

                'nik' => $faker->numerify('################'),

                'alamat_lengkap' => $faker->address(),

                'telepon' => $faker->phoneNumber(),

                'email' => $email,

                'pendidikan_terakhir' => $faker->randomElement(['S1','S2','S3']),

                'jurusan' => $jurusan[array_rand($jurusan)],

                'universitas' => $univ[array_rand($univ)],

                'tahun_lulus' => rand(2000, 2020),

                'keahlian' => json_encode(
                    array_rand(
                        array_flip($keahlian),
                        rand(1, 3)
                    )
                ),

                'foto' => null,

                'tanggal_bergabung' => $faker
                    ->dateTimeBetween('-8 years', '-1 year')
                    ->format('Y-m-d'),

                'tanggal_keluar' => null,

                'status_kepegawaian' =>
                    $faker->randomElement(['tetap','tetap','tidak_tetap']),

                'status' => 'aktif',

                'keterangan' => null,

                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->info('✅ 30 Pengajar + User + Role berhasil di-generate!');
    }
}
