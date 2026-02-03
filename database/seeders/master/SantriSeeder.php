<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class SantriSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $namaPria = ['Ahmad', 'Muhammad', 'Abdullah', 'Yusuf', 'Ali', 'Hassan', 'Zaki', 'Fahmi', 'Rizki', 'Hafiz', 'Amir', 'Rafi', 'Farhan', 'Ilham', 'Dzaki'];
        $namaWanita = ['Fatimah', 'Aisyah', 'Khadijah', 'Maryam', 'Zainab', 'Nur', 'Azzahra', 'Nabila', 'Salma', 'Nadhira', 'Rahma', 'Safira', 'Zahra'];
        $namaBelakang = ['Abdullah', 'Ahmad', 'Rizki', 'Putra', 'Putri', 'Hidayat', 'Rahman', 'Syafiq', 'Santoso', 'Wibowo', 'Kurniawan', 'Anggraini'];
        
        $provinsi = ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'DKI Jakarta', 'Banten', 'DI Yogyakarta'];
        $kota = [
            'Bandung', 'Bogor', 'Depok', 'Bekasi', 'Cirebon', 'Tasikmalaya',
            'Semarang', 'Solo', 'Yogyakarta', 'Surabaya', 'Malang', 'Jakarta', 'Tangerang'
        ];

        $santri = [];
        $nisCounter = 20220001;

        for ($i = 0; $i < 300; $i++) {
            $jk = $i < 150 ? 'laki-laki' : 'perempuan';
            $nama = $jk === 'laki-laki' ? $namaPria[array_rand($namaPria)] : $namaWanita[array_rand($namaWanita)];
            $namaLengkap = $nama . ' ' . $namaBelakang[array_rand($namaBelakang)];

            $kotaPilih = $kota[array_rand($kota)];
            $tanggalLahir = $faker->dateTimeBetween('-18 years', '-12 years')->format('Y-m-d');
            $tanggalMasuk = $faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d');
            
            $status = $faker->randomElement(['aktif', 'aktif', 'aktif', 'aktif', 'lulus']);

            $anak_ke = rand(1, 5);
            $jumlah_saudara = max(rand(1, 7), $anak_ke);

            $santri[] = [
                'user_id' => null,
                'nis' => (string)$nisCounter++,
                'nisn' => $faker->numerify('##########'),
                'nama_lengkap' => $namaLengkap,
                'nama_panggilan' => $nama,
                'jenis_kelamin' => $jk,
                'tempat_lahir' => $kotaPilih,
                'tanggal_lahir' => $tanggalLahir,
                'nik' => $faker->numerify('################'),
                'alamat_lengkap' => $faker->address(),
                'provinsi' => $provinsi[array_rand($provinsi)],
                'kabupaten' => $kotaPilih,
                'kecamatan' => $faker->city(),
                'kelurahan' => $faker->streetName(),
                'kode_pos' => $faker->postcode(),
                'telepon' => rand(0, 1) ? $faker->phoneNumber() : null,
                'anak_ke' => $anak_ke,
                'jumlah_saudara' => $jumlah_saudara,
                'golongan_darah' => $faker->randomElement(['A', 'B', 'AB', 'O', null]),
                'riwayat_penyakit' => rand(0, 10) > 8
                    ? 'Asma ringan, Migrain'
                    : null,
                'foto' => null,
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_keluar' => $status === 'lulus' ? $faker->dateTimeBetween($tanggalMasuk, 'now')->format('Y-m-d') : null,
                'status' => $status,
                'keterangan' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        foreach (array_chunk($santri, 50) as $chunk) {
            DB::table('santri')->insert($chunk);
        }

        $this->command->info('✅ 300 Santri seeded successfully! (150 laki-laki, 150 perempuan)');
    }
}