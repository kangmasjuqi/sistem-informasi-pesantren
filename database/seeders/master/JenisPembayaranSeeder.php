<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JenisPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $jenisBayar = [
            // Pendaftaran
            [
                'kode' => 'DFT',
                'nama' => 'Uang Pendaftaran',
                'kategori' => 'pendaftaran',
                'nominal' => 500000.00,
                'deskripsi' => 'Biaya pendaftaran santri baru',
            ],
            [
                'kode' => 'GD',
                'nama' => 'Uang Gedung',
                'kategori' => 'pendaftaran',
                'nominal' => 3000000.00,
                'deskripsi' => 'Uang gedung untuk santri baru (dibayar sekali)',
            ],
            
            // Bulanan
            [
                'kode' => 'SPP',
                'nama' => 'SPP Bulanan',
                'kategori' => 'bulanan',
                'nominal' => 750000.00,
                'deskripsi' => 'Sumbangan Pembinaan Pendidikan bulanan',
            ],
            [
                'kode' => 'MKN',
                'nama' => 'Uang Makan',
                'kategori' => 'bulanan',
                'nominal' => 500000.00,
                'deskripsi' => 'Biaya makan 3x sehari selama 1 bulan',
            ],
            [
                'kode' => 'ASR',
                'nama' => 'Uang Asrama',
                'kategori' => 'bulanan',
                'nominal' => 250000.00,
                'deskripsi' => 'Biaya pengelolaan dan perawatan asrama',
            ],
            
            // Tahunan
            [
                'kode' => 'BKU',
                'nama' => 'Buku dan LKS',
                'kategori' => 'tahunan',
                'nominal' => 1200000.00,
                'deskripsi' => 'Biaya buku pelajaran dan LKS untuk 1 tahun',
            ],
            [
                'kode' => 'SRG',
                'nama' => 'Seragam',
                'kategori' => 'tahunan',
                'nominal' => 800000.00,
                'deskripsi' => 'Biaya seragam lengkap (putih abu-abu, pramuka, olahraga)',
            ],
            [
                'kode' => 'UJN',
                'nama' => 'Ujian dan Rapor',
                'kategori' => 'tahunan',
                'nominal' => 400000.00,
                'deskripsi' => 'Biaya ujian semester dan pembuatan rapor',
            ],
            
            // Kegiatan
            [
                'kode' => 'STD',
                'nama' => 'Study Tour',
                'kategori' => 'kegiatan',
                'nominal' => 1500000.00,
                'deskripsi' => 'Biaya kegiatan study tour/wisata edukatif',
            ],
            [
                'kode' => 'PES',
                'nama' => 'Pesantren Kilat',
                'kategori' => 'kegiatan',
                'nominal' => 300000.00,
                'deskripsi' => 'Biaya kegiatan pesantren kilat ramadhan',
            ],
            [
                'kode' => 'MHD',
                'nama' => 'Peringatan Maulid',
                'kategori' => 'kegiatan',
                'nominal' => 150000.00,
                'deskripsi' => 'Kontribusi peringatan Maulid Nabi Muhammad SAW',
            ],
            [
                'kode' => 'QBN',
                'nama' => 'Qurban',
                'kategori' => 'kegiatan',
                'nominal' => 200000.00,
                'deskripsi' => 'Kontribusi penyembelihan hewan qurban (opsional)',
            ],
        ];

        foreach ($jenisBayar as $jb) {
            DB::table('jenis_pembayaran')->insert(array_merge($jb, [
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        $this->command->info('✅ 12 Jenis Pembayaran seeded successfully!');
    }
}