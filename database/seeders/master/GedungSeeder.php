<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GedungSeeder extends Seeder
{
    public function run(): void
    {
        $gedung = [
            // Asrama Putra
            ['kode_gedung' => 'AP-01', 'nama_gedung' => 'Asrama Putra 1', 'jenis_gedung' => 'asrama_putra', 'jumlah_lantai' => 2, 'kapasitas_total' => 80, 'tahun_dibangun' => 2015, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Kamar Mandi', 'Musholah', 'Ruang Belajar'])],
            ['kode_gedung' => 'AP-02', 'nama_gedung' => 'Asrama Putra 2', 'jenis_gedung' => 'asrama_putra', 'jumlah_lantai' => 2, 'kapasitas_total' => 80, 'tahun_dibangun' => 2017, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Kamar Mandi', 'Musholah'])],
            ['kode_gedung' => 'AP-03', 'nama_gedung' => 'Asrama Putra 3', 'jenis_gedung' => 'asrama_putra', 'jumlah_lantai' => 3, 'kapasitas_total' => 120, 'tahun_dibangun' => 2020, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Kamar Mandi', 'Musholah', 'AC'])],
            
            // Asrama Putri
            ['kode_gedung' => 'APT-01', 'nama_gedung' => 'Asrama Putri 1', 'jenis_gedung' => 'asrama_putri', 'jumlah_lantai' => 2, 'kapasitas_total' => 80, 'tahun_dibangun' => 2016, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Kamar Mandi', 'Musholah', 'Ruang Belajar'])],
            ['kode_gedung' => 'APT-02', 'nama_gedung' => 'Asrama Putri 2', 'jenis_gedung' => 'asrama_putri', 'jumlah_lantai' => 3, 'kapasitas_total' => 120, 'tahun_dibangun' => 2021, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Kamar Mandi', 'Musholah', 'AC'])],
            
            // Gedung Kelas
            ['kode_gedung' => 'GK-01', 'nama_gedung' => 'Gedung Kelas A', 'jenis_gedung' => 'kelas', 'jumlah_lantai' => 2, 'kapasitas_total' => 240, 'tahun_dibangun' => 2014, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Proyektor', 'AC', 'Papan Tulis'])],
            ['kode_gedung' => 'GK-02', 'nama_gedung' => 'Gedung Kelas B', 'jenis_gedung' => 'kelas', 'jumlah_lantai' => 2, 'kapasitas_total' => 240, 'tahun_dibangun' => 2014, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Proyektor', 'Papan Tulis'])],
            
            // Fasilitas Lain
            ['kode_gedung' => 'MJ-01', 'nama_gedung' => 'Masjid Al-Hikmah', 'jenis_gedung' => 'masjid', 'jumlah_lantai' => 2, 'kapasitas_total' => 500, 'tahun_dibangun' => 2010, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Sound System', 'AC', 'Perpustakaan Mini'])],
            ['kode_gedung' => 'ADM-01', 'nama_gedung' => 'Kantor Administrasi', 'jenis_gedung' => 'kantor', 'jumlah_lantai' => 1, 'kapasitas_total' => 30, 'tahun_dibangun' => 2012, 'kondisi' => 'baik', 'fasilitas' => json_encode(['AC', 'WiFi', 'Komputer'])],
            ['kode_gedung' => 'PERP-01', 'nama_gedung' => 'Perpustakaan', 'jenis_gedung' => 'perpustakaan', 'jumlah_lantai' => 2, 'kapasitas_total' => 100, 'tahun_dibangun' => 2018, 'kondisi' => 'baik', 'fasilitas' => json_encode(['AC', 'WiFi', '5000+ Buku'])],
            ['kode_gedung' => 'AULA-01', 'nama_gedung' => 'Aula Serbaguna', 'jenis_gedung' => 'serbaguna', 'jumlah_lantai' => 1, 'kapasitas_total' => 300, 'tahun_dibangun' => 2019, 'kondisi' => 'baik', 'fasilitas' => json_encode(['Sound System', 'Proyektor', 'AC'])],
            ['kode_gedung' => 'LAB-01', 'nama_gedung' => 'Laboratorium Komputer', 'jenis_gedung' => 'lab', 'jumlah_lantai' => 1, 'kapasitas_total' => 40, 'tahun_dibangun' => 2020, 'kondisi' => 'baik', 'fasilitas' => json_encode(['40 Unit Komputer', 'AC', 'WiFi'])],
        ];

        foreach ($gedung as $g) {
            DB::table('gedung')->insert(array_merge($g, [
                'alamat_lokasi' => 'Kompleks Pondok Pesantren Al-Hikmah',
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        $this->command->info('✅ 12 Gedung seeded successfully!');
    }
}