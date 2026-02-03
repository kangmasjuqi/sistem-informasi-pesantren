<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class WaliSantriSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $pekerjaan = ['PNS', 'TNI/Polri', 'Guru', 'Pegawai Swasta', 'Wiraswasta', 'Pedagang', 'Petani', 'Buruh', 'Sopir', 'Pengusaha', 'Dokter'];
        $pendidikan = ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2'];

        $santriList = DB::table('santri')->get();
        $waliData = [];

        foreach ($santriList as $santri) {
            $namaParts = explode(' ', $santri->nama_lengkap);
            $namaKeluarga = end($namaParts);

            // AYAH
            $statusAyah = rand(1, 10) <= 8 ? 'hidup' : 'meninggal';
            $waliData[] = [
                'santri_id' => $santri->id,
                'jenis_wali' => 'ayah',
                'nama_lengkap' => $faker->firstNameMale() . ' ' . $namaKeluarga,
                'nik' => $faker->numerify('################'),
                'tempat_lahir' => $santri->tempat_lahir,
                'tanggal_lahir' => $faker->dateTimeBetween('-55 years', '-30 years')->format('Y-m-d'),
                'pendidikan_terakhir' => $pendidikan[array_rand($pendidikan)],
                'pekerjaan' => $statusAyah === 'hidup' ? $pekerjaan[array_rand($pekerjaan)] : null,
                'penghasilan' => $statusAyah === 'hidup' ? rand(3000000, 15000000) : 0,
                'telepon' => $faker->phoneNumber(),
                'email' => rand(0, 1) ? $faker->email() : null,
                'alamat' => $santri->alamat_lengkap,
                'status' => $statusAyah,
                'keterangan' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // IBU
            $statusIbu = rand(1, 10) <= 9 ? 'hidup' : 'meninggal';
            $pekerjaanIbu = $statusIbu === 'hidup' ? $faker->randomElement(['Ibu Rumah Tangga', 'Ibu Rumah Tangga', 'Guru', 'Pedagang', 'Wiraswasta']) : null;

            $waliData[] = [
                'santri_id' => $santri->id,
                'jenis_wali' => 'ibu',
                'nama_lengkap' => $faker->firstNameFemale() . ' ' . $namaKeluarga,
                'nik' => $faker->numerify('################'),
                'tempat_lahir' => $faker->city(),
                'tanggal_lahir' => $faker->dateTimeBetween('-50 years', '-28 years')->format('Y-m-d'),
                'pendidikan_terakhir' => $pendidikan[array_rand($pendidikan)],
                'pekerjaan' => $pekerjaanIbu,
                'penghasilan' => ($statusIbu === 'hidup' && $pekerjaanIbu !== 'Ibu Rumah Tangga') ? rand(2000000, 8000000) : 0,
                'telepon' => $faker->phoneNumber(),
                'email' => rand(0, 1) ? $faker->email() : null,
                'alamat' => $santri->alamat_lengkap,
                'status' => $statusIbu,
                'keterangan' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // WALI (10% santri punya wali tambahan)
            if ($statusAyah === 'meninggal' || $statusIbu === 'meninggal' || rand(1, 10) === 1) {
                $waliData[] = [
                    'santri_id' => $santri->id,
                    'jenis_wali' => 'wali',
                    'nama_lengkap' => $faker->name() . ' (Paman/Bibi)',
                    'nik' => $faker->numerify('################'),
                    'tempat_lahir' => $faker->city(),
                    'tanggal_lahir' => $faker->dateTimeBetween('-55 years', '-30 years')->format('Y-m-d'),
                    'pendidikan_terakhir' => $pendidikan[array_rand($pendidikan)],
                    'pekerjaan' => $pekerjaan[array_rand($pekerjaan)],
                    'penghasilan' => rand(3000000, 10000000),
                    'telepon' => $faker->phoneNumber(),
                    'email' => null,
                    'alamat' => $faker->address(),
                    'status' => 'hidup',
                    'keterangan' => 'Wali pengganti orang tua',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        foreach (array_chunk($waliData, 100) as $chunk) {
            DB::table('wali_santri')->insert($chunk);
        }

        $this->command->info('✅ ' . count($waliData) . ' Wali Santri seeded successfully!');
    }
}