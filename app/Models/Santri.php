<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Santri extends Model
{
    use SoftDeletes;

    protected $table = 'santri';

    protected $fillable = [
        'user_id', 'nis', 'nisn', 'nama_lengkap', 'nama_panggilan',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'nik',
        'alamat_lengkap', 'provinsi', 'kabupaten', 'kecamatan',
        'kelurahan', 'kode_pos', 'telepon', 'anak_ke', 'jumlah_saudara',
        'golongan_darah', 'riwayat_penyakit', 'foto', 'tanggal_masuk',
        'tanggal_keluar', 'status', 'keterangan'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    /**
     * Get complete santri profile with all related data
     */
    public static function getCompleteProfile($id)
    {
        $santri = self::find($id);
        
        if (!$santri) {
            return null;
        }

        return [
            'profile' => $santri,
            'wali' => self::getWaliSantri($id),
            'kamar' => self::getPenghuniKamar($id),
            'kelas' => self::getKelasSantri($id),
            'kehadiran' => self::getKehadiranStats($id),
            'nilai' => self::getNilaiSantri($id),
            'rapor' => self::getRaporSantri($id),
            'rapor_summary' => self::getRaporSummary($id),
            'pembayaran' => self::getPembayaranSantri($id),
            'perizinan' => self::getPerizinanSantri($id),
        ];
    }

    /**
     * Get Wali Santri information
     */
    public static function getWaliSantri($santri_id)
    {
        return DB::table('wali_santri')
            ->where('santri_id', $santri_id)
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Get current and historical room assignments
     */
    public static function getPenghuniKamar($santri_id)
    {
        return DB::table('penghuni_kamar as pk')
            ->join('kamar as k', 'pk.kamar_id', '=', 'k.id')
            ->join('gedung as g', 'k.gedung_id', '=', 'g.id')
            ->where('pk.santri_id', $santri_id)
            ->whereNull('pk.deleted_at')
            ->select(
                'pk.*',
                'k.nomor_kamar',
                'k.nama_kamar',
                'k.lantai',
                'k.kapasitas',
                'g.nama_gedung',
                'g.jenis_gedung'
            )
            ->orderBy('pk.tanggal_masuk', 'desc')
            ->get();
    }

    /**
     * Get class enrollment history and current class
     */
    public static function getKelasSantri($santri_id)
    {
        return DB::table('kelas_santri as ks')
            ->join('kelas as k', 'ks.kelas_id', '=', 'k.id')
            ->join('tahun_ajaran as ta', 'k.tahun_ajaran_id', '=', 'ta.id')
            ->leftJoin('pengajar as p', 'k.wali_kelas_id', '=', 'p.id')
            ->where('ks.santri_id', $santri_id)
            ->whereNull('ks.deleted_at')
            ->select(
                'ks.*',
                'k.nama_kelas',
                'k.tingkat',
                'k.kapasitas',
                'ta.nama as tahun_ajaran',
                'ta.is_active as tahun_ajaran_active',
                'p.nama_lengkap as wali_kelas'
            )
            ->orderBy('ta.tahun_mulai', 'desc')
            ->get();
    }

    /**
     * Get attendance statistics
     */
    public static function getKehadiranStats($santri_id)
    {
        // Overall statistics
        $overall = DB::table('kehadiran')
            ->where('santri_id', $santri_id)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "alpa" THEN 1 ELSE 0 END) as alpa')
            )
            ->first();

        // Current semester statistics
        $currentSemester = DB::table('semester')
            ->where('is_active', 1)
            ->first();

        $semesterStats = null;
        if ($currentSemester) {
            $semesterStats = DB::table('kehadiran')
                ->where('santri_id', $santri_id)
                ->whereBetween('tanggal', [$currentSemester->tanggal_mulai, $currentSemester->tanggal_selesai])
                ->whereNull('deleted_at')
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status_kehadiran = "hadir" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('SUM(CASE WHEN status_kehadiran = "sakit" THEN 1 ELSE 0 END) as sakit'),
                    DB::raw('SUM(CASE WHEN status_kehadiran = "izin" THEN 1 ELSE 0 END) as izin'),
                    DB::raw('SUM(CASE WHEN status_kehadiran = "alpa" THEN 1 ELSE 0 END) as alpa')
                )
                ->first();
        }

        // Recent attendance (last 30 days)
        $recentAttendance = DB::table('kehadiran as k')
            ->leftJoin('pengampu as pg', 'k.pengampu_id', '=', 'pg.id')
            ->leftJoin('mata_pelajaran as mp', 'pg.mata_pelajaran_id', '=', 'mp.id')
            ->where('k.santri_id', $santri_id)
            ->where('k.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 30 DAY)'))
            ->whereNull('k.deleted_at')
            ->select(
                'k.*',
                'mp.nama_mapel',
                'mp.kategori'
            )
            ->orderBy('k.tanggal', 'desc')
            ->limit(50)
            ->get();

        return [
            'overall' => $overall,
            'current_semester' => $semesterStats,
            'recent' => $recentAttendance
        ];
    }

    /**
     * Get nilai (grades) by subject
     */
    public static function getNilaiSantri($santri_id)
    {
        return DB::table('nilai as n')
            ->join('pengampu as pg', 'n.pengampu_id', '=', 'pg.id')
            ->join('mata_pelajaran as mp', 'pg.mata_pelajaran_id', '=', 'mp.id')
            ->join('komponen_nilai as kn', 'n.komponen_nilai_id', '=', 'kn.id')
            ->join('kelas as k', 'pg.kelas_id', '=', 'k.id')
            ->join('semester as s', 'pg.semester_id', '=', 's.id')
            ->join('tahun_ajaran as ta', 's.tahun_ajaran_id', '=', 'ta.id')
            ->join('pengajar as p', 'pg.pengajar_id', '=', 'p.id')
            ->where('n.santri_id', $santri_id)
            ->whereNull('n.deleted_at')
            ->select(
                'n.*',
                'mp.nama_mapel',
                'mp.kategori as kategori_mapel',
                'kn.nama as komponen_nama',
                'kn.bobot',
                'k.nama_kelas',
                's.jenis_semester',
                'ta.nama as tahun_ajaran',
                'p.nama_lengkap as nama_pengajar'
            )
            ->orderBy('ta.tahun_mulai', 'desc')
            ->orderBy('s.jenis_semester', 'desc')
            ->orderBy('mp.nama_mapel')
            ->get();
    }

    /**
     * Get rapor per mata pelajaran
     */
    public static function getRaporSantri($santri_id)
    {
        return DB::table('rapor as r')
            ->join('pengampu as pg', 'r.pengampu_id', '=', 'pg.id')
            ->join('mata_pelajaran as mp', 'pg.mata_pelajaran_id', '=', 'mp.id')
            ->join('kelas as k', 'pg.kelas_id', '=', 'k.id')
            ->join('semester as s', 'pg.semester_id', '=', 's.id')
            ->join('tahun_ajaran as ta', 's.tahun_ajaran_id', '=', 'ta.id')
            ->join('pengajar as p', 'pg.pengajar_id', '=', 'p.id')
            ->where('r.santri_id', $santri_id)
            ->whereNull('r.deleted_at')
            ->select(
                'r.*',
                'mp.nama_mapel',
                'mp.kategori as kategori_mapel',
                'mp.bobot_sks',
                'k.nama_kelas',
                'k.tingkat',
                's.jenis_semester',
                'ta.nama as tahun_ajaran',
                'ta.tahun_mulai',
                'p.nama_lengkap as nama_pengajar'
            )
            ->orderBy('ta.tahun_mulai', 'desc')
            ->orderBy('s.jenis_semester', 'desc')
            ->orderBy('mp.nama_mapel')
            ->get();
    }

    /**
     * Get rapor summary per semester
     */
    public static function getRaporSummary($santri_id)
    {
        return DB::table('rapor_summary as rs')
            ->join('kelas as k', 'rs.kelas_id', '=', 'k.id')
            ->join('semester as s', 'rs.semester_id', '=', 's.id')
            ->join('tahun_ajaran as ta', 's.tahun_ajaran_id', '=', 'ta.id')
            ->where('rs.santri_id', $santri_id)
            ->whereNull('rs.deleted_at')
            ->select(
                'rs.*',
                'k.nama_kelas',
                'k.tingkat',
                's.jenis_semester',
                's.nama as semester_nama',
                'ta.nama as tahun_ajaran',
                'ta.tahun_mulai'
            )
            ->orderBy('ta.tahun_mulai', 'desc')
            ->orderBy('s.jenis_semester', 'desc')
            ->get();
    }

    /**
     * Get payment history
     */
    public static function getPembayaranSantri($santri_id)
    {
        return DB::table('pembayaran as pb')
            ->join('jenis_pembayaran as jp', 'pb.jenis_pembayaran_id', '=', 'jp.id')
            ->leftJoin('tahun_ajaran as ta', 'pb.tahun_ajaran_id', '=', 'ta.id')
            ->leftJoin('users as u', 'pb.petugas_id', '=', 'u.id')
            ->where('pb.santri_id', $santri_id)
            ->whereNull('pb.deleted_at')
            ->select(
                'pb.*',
                'jp.nama as jenis_pembayaran',
                'jp.kategori as kategori_pembayaran',
                'ta.nama as tahun_ajaran',
                'u.nama_lengkap as petugas'
            )
            ->orderBy('pb.tanggal_pembayaran', 'desc')
            ->get();
    }

    /**
     * Get permission/leave history
     */
    public static function getPerizinanSantri($santri_id)
    {
        return DB::table('perizinan as p')
            ->leftJoin('users as u', 'p.disetujui_oleh', '=', 'u.id')
            ->where('p.santri_id', $santri_id)
            ->whereNull('p.deleted_at')
            ->select(
                'p.*',
                'u.nama_lengkap as disetujui_oleh_nama'
            )
            ->orderBy('p.tanggal_mulai', 'desc')
            ->get();
    }

    /**
     * Get payment summary statistics
     */
    public static function getPembayaranSummary($santri_id)
    {
        $currentYear = DB::table('tahun_ajaran')
            ->where('is_active', 1)
            ->first();

        $summary = [
            'total_paid' => 0,
            'total_outstanding' => 0,
            'current_year_paid' => 0,
            'current_year_outstanding' => 0,
        ];

        // Overall totals
        $overall = DB::table('pembayaran')
            ->where('santri_id', $santri_id)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('SUM(CASE WHEN status = "lunas" THEN total_bayar ELSE 0 END) as total_lunas'),
                DB::raw('SUM(CASE WHEN status != "lunas" THEN total_bayar ELSE 0 END) as total_belum_lunas')
            )
            ->first();

        if ($overall) {
            $summary['total_paid'] = $overall->total_lunas ?? 0;
            $summary['total_outstanding'] = $overall->total_belum_lunas ?? 0;
        }

        // Current year totals
        if ($currentYear) {
            $currentYearSummary = DB::table('pembayaran')
                ->where('santri_id', $santri_id)
                ->where('tahun_ajaran_id', $currentYear->id)
                ->whereNull('deleted_at')
                ->select(
                    DB::raw('SUM(CASE WHEN status = "lunas" THEN total_bayar ELSE 0 END) as total_lunas'),
                    DB::raw('SUM(CASE WHEN status != "lunas" THEN total_bayar ELSE 0 END) as total_belum_lunas')
                )
                ->first();

            if ($currentYearSummary) {
                $summary['current_year_paid'] = $currentYearSummary->total_lunas ?? 0;
                $summary['current_year_outstanding'] = $currentYearSummary->total_belum_lunas ?? 0;
            }
        }

        return $summary;
    }
}