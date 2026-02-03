<?php

namespace Database\Seeders\Operational;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;

class PerizinanSeeder extends Seeder
{
    public function run(): void
    {
        $santriList = DB::table('santri')->get();

        $roleIds = Role::whereIn('kode', ['KEPSEK', 'ADMIN'])
            ->pluck('id');

        $approverIds = User::whereHas('roleUser', function ($q) use ($roleIds) {
                $q->whereIn('role_id', $roleIds);
            })
            ->pluck('id')
            ->toArray();

        if (empty($approverIds)) {
            $approverIds = DB::table('users')->limit(3)->pluck('id')->toArray();
        }

        $perizinanData = [];
        $nomorCounter = 1000;

        $jenisIzin = ['pulang', 'pulang', 'kunjungan', 'sakit', 'keluar_sementara'];
        $statusOptions = ['diajukan', 'diajukan', 'disetujui', 'disetujui', 'disetujui', 'selesai', 'ditolak'];

        foreach ($santriList as $santri) {

            // Generate 1-3 izin per santri
            $jumlahIzin = rand(1, 3);

            for ($i = 0; $i < $jumlahIzin; $i++) {
                $jenis = $jenisIzin[array_rand($jenisIzin)];
                $durasi = $jenis === 'pulang' ? rand(2, 7) : rand(1, 3);

                $status = $statusOptions[array_rand($statusOptions)];
                if($status == 'diajukan') {
                    // Random start date in the next 3 months
                    $tanggalMulai = Carbon::now()->addDays(rand(3, 90));
                } else {
                    // Random start date within last 1000 days
                    $tanggalMulai = Carbon::now()->subDays(rand(3, 1000));
                }

                $tanggalSelesai = $tanggalMulai->copy()->addDays($durasi);

                // Get wali for penjemput
                $wali = DB::table('wali_santri')
                    ->where('santri_id', $santri->id)
                    ->where('jenis_wali', 'ayah')
                    ->first();

                if (!$wali) {
                    $wali = DB::table('wali_santri')
                        ->where('santri_id', $santri->id)
                        ->first();
                }

                $perizinanData = [
                    'nomor_izin' => 'IZN' . Carbon::now()->format('Y') . str_pad($nomorCounter++, 5, '0', STR_PAD_LEFT),
                    'santri_id' => $santri->id,
                    'jenis_izin' => $jenis,
                    'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                    'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
                    'waktu_keluar' => $tanggalMulai->format('H:i:s'),
                    'waktu_kembali' => $tanggalSelesai->format('H:i:s'),
                    'keperluan' => $this->generateKeperluan($jenis),
                    'tujuan' => $jenis === 'sakit' ? 'Rumah Sakit' : ($wali ? $wali->alamat : 'Rumah'),
                    'penjemput_nama' => $wali ? $wali->nama_lengkap : 'Keluarga',
                    'penjemput_hubungan' => $wali ? ucfirst($wali->jenis_wali) : 'Keluarga',
                    'penjemput_telepon' => $wali ? $wali->telepon : '081234567890',
                    'penjemput_identitas' => $wali ? $wali->nik : null,
                    'status' => $status,
                    'disetujui_oleh' => $status !== 'diajukan' ? $approverIds[array_rand($approverIds)] : null,
                    'waktu_persetujuan' => $status !== 'diajukan' ? $tanggalMulai->copy()->subHours(2) : null,
                    'catatan_persetujuan' => $status === 'ditolak' ? 'Jadwal ujian, tidak dapat izin' : ($status === 'disetujui' ? 'Disetujui' : null),
                    'waktu_kembali_aktual' => $status === 'selesai' ? $tanggalSelesai->copy()->addHours(rand(-2, 2)) : null,
                    'keterangan' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                DB::table('perizinan')->insert($perizinanData);

            }
        }

        $this->command->info('✅ ' . count($perizinanData) . ' Perizinan records seeded!');
    }

    private function generateKeperluan($jenis): string
    {
        $keperluan = [
            'pulang' => ['Keperluan keluarga', 'Liburan', 'Acara keluarga', 'Kunjungan rumah'],
            'kunjungan' => ['Dijenguk orang tua', 'Dijenguk keluarga', 'Kunjungan wali'],
            'sakit' => ['Sakit demam', 'Sakit perut', 'Flu', 'Perlu perawatan medis'],
            'keluar_sementara' => ['Urusan bank', 'Beli keperluan', 'Ke toko buku', 'Ambil paket'],
        ];

        $list = $keperluan[$jenis] ?? ['Keperluan'];
        return $list[array_rand($list)];
    }
}