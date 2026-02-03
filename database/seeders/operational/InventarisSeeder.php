<?php

namespace Database\Seeders\Operational;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarisSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriList = DB::table('kategori_inventaris')->get();
        $gedungList = DB::table('gedung')->get();

        $inventarisData = [];
        $kodeCounter = 1000;

        foreach ($kategoriList as $kategori) {
            $items = $this->getItemsByKategori($kategori->kode);

            foreach ($items as $item) {
                $gedung = $gedungList->random();
                $kondisi = ['baik', 'baik', 'baik', 'baik', 'rusak_ringan'];
                $tanggalPerolehan = Carbon::now()->subYears(rand(1, 10));

                $inventarisData[] = [
                    'kategori_inventaris_id' => $kategori->id,
                    'gedung_id' => $gedung->id,
                    'kode_inventaris' => $kategori->kode . '-' . str_pad($kodeCounter++, 4, '0', STR_PAD_LEFT),
                    'nama_barang' => $item['nama'],
                    'merk' => $item['merk'] ?? null,
                    'tipe_model' => $item['tipe'] ?? null,
                    'jumlah' => $item['jumlah'] ?? 1,
                    'satuan' => $item['satuan'] ?? 'unit',
                    'kondisi' => $kondisi[array_rand($kondisi)],
                    'tanggal_perolehan' => $tanggalPerolehan->format('Y-m-d'),
                    'harga_perolehan' => $item['harga'] ?? null,
                    'nilai_penyusutan' => isset($item['harga']) ? $item['harga'] * 0.1 * rand(1, 5) : null,
                    'sumber_dana' => $this->randomSumberDana(),
                    'lokasi' => $gedung->nama_gedung,
                    'spesifikasi' => $item['spesifikasi'] ?? null,
                    'nomor_seri' => isset($item['merk']) ? strtoupper(substr(md5(rand()), 0, 12)) : null,
                    'tanggal_maintenance_terakhir' => rand(0, 1) ? Carbon::now()->subMonths(rand(1, 12))->format('Y-m-d') : null,
                    'penanggung_jawab' => 'Staff TU',
                    'foto' => null,
                    'is_active' => 1,
                    'keterangan' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        DB::table('inventaris')->insert($inventarisData);

        $this->command->info('✅ ' . count($inventarisData) . ' Inventaris records seeded!');
    }

    private function getItemsByKategori($kode): array
    {
        $items = [
            'ELK' => [
                ['nama' => 'Komputer Desktop', 'merk' => 'Lenovo', 'tipe' => 'ThinkCentre', 'jumlah' => 40, 'harga' => 6000000],
                ['nama' => 'Laptop', 'merk' => 'ASUS', 'tipe' => 'VivoBook', 'jumlah' => 10, 'harga' => 7000000],
                ['nama' => 'Proyektor', 'merk' => 'Epson', 'tipe' => 'EB-X05', 'jumlah' => 12, 'harga' => 5000000],
                ['nama' => 'AC Split', 'merk' => 'Daikin', 'tipe' => '1 PK', 'jumlah' => 25, 'harga' => 4500000],
                ['nama' => 'Kipas Angin', 'merk' => 'Miyako', 'jumlah' => 50, 'satuan' => 'unit', 'harga' => 300000],
                ['nama' => 'Printer', 'merk' => 'Canon', 'tipe' => 'G2010', 'jumlah' => 5, 'harga' => 2500000],
            ],
            'MBL' => [
                ['nama' => 'Meja Belajar', 'jumlah' => 300, 'satuan' => 'unit', 'harga' => 500000],
                ['nama' => 'Kursi Belajar', 'jumlah' => 300, 'satuan' => 'unit', 'harga' => 250000],
                ['nama' => 'Lemari Kamar', 'jumlah' => 120, 'satuan' => 'unit', 'harga' => 1500000],
                ['nama' => 'Rak Buku', 'jumlah' => 50, 'satuan' => 'unit', 'harga' => 800000],
                ['nama' => 'Papan Tulis', 'jumlah' => 20, 'satuan' => 'unit', 'harga' => 1000000],
            ],
            'OLR' => [
                ['nama' => 'Bola Sepak', 'merk' => 'Mikasa', 'jumlah' => 20, 'satuan' => 'buah', 'harga' => 250000],
                ['nama' => 'Bola Voli', 'merk' => 'Molten', 'jumlah' => 15, 'satuan' => 'buah', 'harga' => 300000],
                ['nama' => 'Net Voli', 'jumlah' => 5, 'satuan' => 'set', 'harga' => 500000],
                ['nama' => 'Matras', 'jumlah' => 30, 'satuan' => 'buah', 'harga' => 200000],
            ],
            'MSJ' => [
                ['nama' => 'Sajadah', 'jumlah' => 500, 'satuan' => 'buah', 'harga' => 50000],
                ['nama' => 'Mukena', 'jumlah' => 200, 'satuan' => 'buah', 'harga' => 100000],
                ['nama' => 'Al-Quran', 'jumlah' => 300, 'satuan' => 'buah', 'harga' => 150000],
                ['nama' => 'Sound System Masjid', 'merk' => 'TOA', 'jumlah' => 1, 'satuan' => 'set', 'harga' => 15000000],
            ],
            'BKU' => [
                ['nama' => 'Buku Paket Bahasa Arab', 'jumlah' => 300, 'satuan' => 'buah', 'harga' => 75000],
                ['nama' => 'Buku Paket Fiqih', 'jumlah' => 300, 'satuan' => 'buah', 'harga' => 75000],
                ['nama' => 'Kitab Kuning', 'jumlah' => 100, 'satuan' => 'buah', 'harga' => 50000],
            ],
            'DPR' => [
                ['nama' => 'Kompor Gas', 'merk' => 'Rinnai', 'jumlah' => 5, 'satuan' => 'unit', 'harga' => 3000000],
                ['nama' => 'Panci Besar', 'jumlah' => 10, 'satuan' => 'buah', 'harga' => 500000],
                ['nama' => 'Dispenser', 'merk' => 'Sanken', 'jumlah' => 15, 'satuan' => 'unit', 'harga' => 1000000],
            ],
            'KND' => [
                ['nama' => 'Bus Santri', 'merk' => 'Isuzu', 'tipe' => 'Elf', 'jumlah' => 2, 'satuan' => 'unit', 'harga' => 450000000],
                ['nama' => 'Motor Operasional', 'merk' => 'Honda', 'tipe' => 'Supra X', 'jumlah' => 5, 'satuan' => 'unit', 'harga' => 18000000],
            ],
            'ALT' => [
                ['nama' => 'Spidol Whiteboard', 'jumlah' => 500, 'satuan' => 'buah', 'harga' => 5000],
                ['nama' => 'Kertas HVS A4', 'jumlah' => 100, 'satuan' => 'rim', 'harga' => 40000],
            ],
            'MDS' => [
                ['nama' => 'Kotak P3K', 'jumlah' => 20, 'satuan' => 'set', 'harga' => 200000],
                ['nama' => 'Termometer', 'jumlah' => 10, 'satuan' => 'buah', 'harga' => 150000],
            ],
        ];

        return $items[$kode] ?? [['nama' => 'Barang ' . $kode, 'jumlah' => 1]];
    }

    private function randomSumberDana(): string
    {
        $sumber = ['APBN', 'Donasi', 'Yayasan', 'Hibah', 'Swadaya'];
        return $sumber[array_rand($sumber)];
    }
}