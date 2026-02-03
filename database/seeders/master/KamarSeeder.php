<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        $asramaPutra = DB::table('gedung')->where('jenis_gedung', 'asrama_putra')->get();
        $asramaPutri = DB::table('gedung')->where('jenis_gedung', 'asrama_putri')->get();

        $kamar = [];
        $fasilitas = json_encode(['Lemari', 'Kasur', 'Bantal', 'Meja Belajar']);
        $fasilitasAC = json_encode(['Lemari', 'Kasur', 'Bantal', 'Meja Belajar', 'AC']);

        // Kamar Asrama Putra
        foreach ($asramaPutra as $gedung) {
            $jumlahKamar = $gedung->kapasitas_total / 8; // 8 orang per kamar
            $lantaiMax = $gedung->jumlah_lantai;
            
            $nomorKamar = 1;
            for ($lantai = 1; $lantai <= $lantaiMax; $lantai++) {
                $kamarPerLantai = $jumlahKamar / $lantaiMax;
                for ($k = 0; $k < $kamarPerLantai; $k++) {
                    $kamar[] = [
                        'gedung_id' => $gedung->id,
                        'nomor_kamar' => str_pad($nomorKamar++, 3, '0', STR_PAD_LEFT),
                        'nama_kamar' => 'Kamar ' . $nomorKamar,
                        'lantai' => $lantai,
                        'kapasitas' => 8,
                        'luas' => 24.00,
                        'fasilitas' => $gedung->tahun_dibangun >= 2020 ? $fasilitasAC : $fasilitas,
                        'kondisi' => 'baik',
                        'is_active' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        // Kamar Asrama Putri
        foreach ($asramaPutri as $gedung) {
            $jumlahKamar = $gedung->kapasitas_total / 8;
            $lantaiMax = $gedung->jumlah_lantai;
            
            $nomorKamar = 1;
            for ($lantai = 1; $lantai <= $lantaiMax; $lantai++) {
                $kamarPerLantai = $jumlahKamar / $lantaiMax;
                for ($k = 0; $k < $kamarPerLantai; $k++) {
                    $kamar[] = [
                        'gedung_id' => $gedung->id,
                        'nomor_kamar' => str_pad($nomorKamar++, 3, '0', STR_PAD_LEFT),
                        'nama_kamar' => 'Kamar ' . $nomorKamar,
                        'lantai' => $lantai,
                        'kapasitas' => 8,
                        'luas' => 24.00,
                        'fasilitas' => $gedung->tahun_dibangun >= 2020 ? $fasilitasAC : $fasilitas,
                        'kondisi' => 'baik',
                        'is_active' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        DB::table('kamar')->insert($kamar);

        $this->command->info('✅ ' . count($kamar) . ' Kamar seeded successfully!');
    }
}