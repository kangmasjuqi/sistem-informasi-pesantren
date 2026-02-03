<?php

namespace Database\Seeders\Academic;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenghuniKamarSeeder extends Seeder
{
    public function run(): void
    {
        // Get santri by gender
        $santriPutra = DB::table('santri')
            ->where('jenis_kelamin', 'laki-laki')
            ->orderBy('id')
            ->get();

        $santriPutri = DB::table('santri')
            ->where('jenis_kelamin', 'perempuan')
            ->orderBy('id')
            ->get();

        // Get kamar by gedung type
        $kamarPutra = DB::table('kamar')
            ->join('gedung', 'kamar.gedung_id', '=', 'gedung.id')
            ->where('gedung.jenis_gedung', 'asrama_putra')
            ->where('kamar.is_active', 1)
            ->select('kamar.*')
            ->orderBy('kamar.gedung_id')
            ->orderBy('kamar.nomor_kamar')
            ->get();

        $kamarPutri = DB::table('kamar')
            ->join('gedung', 'kamar.gedung_id', '=', 'gedung.id')
            ->where('gedung.jenis_gedung', 'asrama_putri')
            ->where('kamar.is_active', 1)
            ->select('kamar.*')
            ->orderBy('kamar.gedung_id')
            ->orderBy('kamar.nomor_kamar')
            ->get();

        $penghuniData = [];

        // Assign putra to kamar putra
        $kamarIndex = 0;
        $santriInKamar = 0;
        foreach ($santriPutra as $santri) {
            if ($kamarIndex >= $kamarPutra->count()) break;

            $kamar = $kamarPutra[$kamarIndex];

            $tanggalMasuk = $santri->tanggal_masuk ?? null;
            $tanggalKeluar = $santri->tanggal_keluar ?? null;

            $penghuniData[] = [
                'santri_id' => $santri->id,
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_keluar' => $tanggalKeluar,
                'status' => $tanggalKeluar ? 'keluar' : 'aktif',
                'keterangan' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $santriInKamar++;
            // Move to next kamar when capacity reached
            if ($santriInKamar >= $kamar->kapasitas) {
                $kamarIndex++;
                $santriInKamar = 0;
            }
        }

        // Assign putri to kamar putri
        $kamarIndex = 0;
        $santriInKamar = 0;
        foreach ($santriPutri as $santri) {
            if ($kamarIndex >= $kamarPutri->count()) break;

            $kamar = $kamarPutri[$kamarIndex];

            $tanggalMasuk = $santri->tanggal_masuk ?? null;
            $tanggalKeluar = $santri->tanggal_keluar ?? null;

            $penghuniData[] = [
                'santri_id' => $santri->id,
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_keluar' => $tanggalKeluar,
                'status' => $tanggalKeluar ? 'keluar' : 'aktif',
                'keterangan' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $santriInKamar++;
            if ($santriInKamar >= $kamar->kapasitas) {
                $kamarIndex++;
                $santriInKamar = 0;
            }
        }

        DB::table('penghuni_kamar')->insert($penghuniData);

        $this->command->info('✅ ' . count($penghuniData) . ' Penghuni Kamar seeded successfully!');
        $this->command->info('   (Santri assigned to kamar with gender segregation)');
    }
}