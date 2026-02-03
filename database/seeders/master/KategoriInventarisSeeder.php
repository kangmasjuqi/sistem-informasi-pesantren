<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KategoriInventarisSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['kode' => 'ELK', 'nama' => 'Elektronik', 'deskripsi' => 'Barang elektronik seperti komputer, proyektor, AC, dll'],
            ['kode' => 'MBL', 'nama' => 'Mebel', 'deskripsi' => 'Furniture seperti meja, kursi, lemari, dll'],
            ['kode' => 'OLR', 'nama' => 'Alat Olahraga', 'deskripsi' => 'Peralatan olahraga dan penjas'],
            ['kode' => 'MSJ', 'nama' => 'Perlengkapan Masjid', 'deskripsi' => 'Sajadah, mukena, Al-Quran, sound system, dll'],
            ['kode' => 'BKU', 'nama' => 'Buku dan Referensi', 'deskripsi' => 'Buku pelajaran, kitab kuning, referensi perpustakaan'],
            ['kode' => 'DPR', 'nama' => 'Peralatan Dapur', 'deskripsi' => 'Kompor, panci, piring, gelas, dll'],
            ['kode' => 'KND', 'nama' => 'Kendaraan', 'deskripsi' => 'Bus, mobil, motor operasional pesantren'],
            ['kode' => 'ALT', 'nama' => 'Alat Tulis', 'deskripsi' => 'Spidol, kertas, papan tulis, dll'],
            ['kode' => 'MDS', 'nama' => 'Alat Kesehatan', 'deskripsi' => 'Kotak P3K, obat-obatan, termometer, dll'],
            ['kode' => 'LNN', 'nama' => 'Lain-lain', 'deskripsi' => 'Barang inventaris lainnya'],
        ];

        foreach ($kategori as $k) {
            DB::table('kategori_inventaris')->insert(array_merge($k, [
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        $this->command->info('✅ 10 Kategori Inventaris seeded successfully!');
    }
}