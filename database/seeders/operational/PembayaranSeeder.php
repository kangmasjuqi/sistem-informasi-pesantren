<?php

namespace Database\Seeders\Operational;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $jenisPembayaranList = DB::table('jenis_pembayaran')->where('is_active', 1)->get();
        $petugasId = DB::table('users')->where('username', 'siti.nur')->value('id'); // Bendahara

        $santriListAll = DB::table('santri')->select('id', 'status', 'tanggal_masuk')->get();
        $santriListAktif = $santriListAll->filter(function ($santri) {
            return $santri->status === 'aktif';
        })->values();
        
        // pembayaran tahun-tahun terlewat
        $pastTahunAjaran = DB::table('tahun_ajaran')
            ->where('is_active', 0)
            ->orderBy('tahun_mulai', 'asc')
            ->get();
        foreach ($pastTahunAjaran as $tahun) {
            $this->seedPembayaran("tahun_terlewat", $santriListAll, $jenisPembayaranList, $tahun, $petugasId);
        }

        // pembayaran tahun berjalan
        $tahunAjaran = DB::table('tahun_ajaran')->where('is_active', 1)->first();
        $this->seedPembayaran("tahun_berjalan" , $santriListAktif, $jenisPembayaranList, $tahunAjaran, $petugasId);

        $this->command->info('✅ Pembayaran records seeded!');
    }

    private function seedPembayaran($mode, $santriList, $jenisPembayaranList, $tahunAjaran, $petugasId)
    {

        $pembayaranData = [];
        $kodeCounter = 1000;

        foreach ($santriList as $santri) {
            foreach ($jenisPembayaranList as $jenis) {
                // Skip if not applicable
                if ($jenis->kategori === 'pendaftaran') {
                    // Only for new students (entered this year)
                    if (Carbon::parse($santri->tanggal_masuk)->year != $tahunAjaran->tahun_mulai) {
                        continue;
                    }
                }

                if ($jenis->kategori === 'bulanan') {

                    if($mode == 'tahun_terlewat'){
                        // For past years, randomly decide if student fully paid or only partial
                        $paidMonths = rand(8, 12);
                    } else {
                        $paidMonths = 8; // this year we are in February
                    }

                    // Generate for months
                    for ($bulan = $paidMonths; $bulan >= 1; $bulan--) {

                        if($mode == 'tahun_terlewat'){
                            $tanggal = Carbon::parse($tahunAjaran->tanggal_mulai)->addMonths($bulan - 1)->day(rand(5,15));
                        } else {
                            $tanggal = Carbon::parse($tahunAjaran->tanggal_mulai)->addMonths($bulan - 1)->day(10);
                        }

                        $status = 'lunas';
                        $denda = 0;
                        $potongan = rand(0, 1) ? 0 : rand(50, 100) * 1000; // 50% get discount

                        $pembayaranData[] = [
                            'kode_pembayaran' => 'PAY' . $tahunAjaran->tahun_mulai . str_pad($kodeCounter++, 6, '0', STR_PAD_LEFT),
                            'santri_id' => $santri->id,
                            'jenis_pembayaran_id' => $jenis->id,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                            'tanggal_pembayaran' => $tanggal->format('Y-m-d'),
                            'bulan' => $tanggal->month,
                            'tahun' => $tanggal->year,
                            'nominal' => $jenis->nominal,
                            'potongan' => $potongan,
                            'denda' => $denda,
                            'total_bayar' => $jenis->nominal - $potongan + $denda,
                            'metode_pembayaran' => $this->randomMetode(),
                            'nomor_referensi' => $this->generateReferensi(),
                            'status' => $status,
                            'petugas_id' => $petugasId,
                            'keterangan' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    }
                } elseif ($jenis->kategori === 'tahunan') {
                    // One payment per year
                    $tanggal = Carbon::parse($tahunAjaran->tanggal_mulai)->addMonths(1);
                    $status = rand(1, 10) <= 8 ? 'lunas' : 'cicilan';

                    $pembayaranData[] = [
                        'kode_pembayaran' => 'PAY' . $tahunAjaran->tahun_mulai . str_pad($kodeCounter++, 6, '0', STR_PAD_LEFT),
                        'santri_id' => $santri->id,
                        'jenis_pembayaran_id' => $jenis->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'tanggal_pembayaran' => $tanggal->format('Y-m-d'),
                        'bulan' => null,
                        'tahun' => $tahunAjaran->tahun_mulai,
                        'nominal' => $jenis->nominal,
                        'potongan' => 0,
                        'denda' => 0,
                        'total_bayar' => $jenis->nominal,
                        'metode_pembayaran' => $this->randomMetode(),
                        'nomor_referensi' => $this->generateReferensi(),
                        'status' => $status,
                        'petugas_id' => $petugasId,
                        'keterangan' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                } elseif ($jenis->kategori === 'pendaftaran') {
                    // One-time payment
                    $tanggal = Carbon::parse($santri->tanggal_masuk);

                    $pembayaranData[] = [
                        'kode_pembayaran' => 'PAY' . $tahunAjaran->tahun_mulai . str_pad($kodeCounter++, 6, '0', STR_PAD_LEFT),
                        'santri_id' => $santri->id,
                        'jenis_pembayaran_id' => $jenis->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'tanggal_pembayaran' => $tanggal->format('Y-m-d'),
                        'bulan' => null,
                        'tahun' => $tanggal->year,
                        'nominal' => $jenis->nominal,
                        'potongan' => 0,
                        'denda' => 0,
                        'total_bayar' => $jenis->nominal,
                        'metode_pembayaran' => $this->randomMetode(),
                        'nomor_referensi' => $this->generateReferensi(),
                        'status' => 'lunas',
                        'petugas_id' => $petugasId,
                        'keterangan' => 'Pembayaran pendaftaran santri baru',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            // Limit to prevent too much data
            if (count($pembayaranData) >= 5000) break;
        }

        // Insert in chunks
        foreach (array_chunk($pembayaranData, 500) as $chunk) {
            DB::table('pembayaran')->insert($chunk);
        }
    }

    private function randomMetode(): string
    {
        $metode = ['tunai', 'tunai', 'transfer', 'transfer', 'qris'];
        return $metode[array_rand($metode)];
    }

    private function generateReferensi(): ?string
    {
        $metode = $this->randomMetode();
        if ($metode === 'tunai') return null;
        if ($metode === 'qris') return 'QRIS' . rand(100000, 999999);
        return 'TRF' . time() . rand(100, 999);
    }
}