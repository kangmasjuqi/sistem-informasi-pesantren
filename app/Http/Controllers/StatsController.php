<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Display santri statistics dashboard
     */
    public function santri()
    {
        $stats = [
            'overview' => $this->getOverviewStats(),
            'status_distribution' => $this->getStatusDistribution(),
            'gender_distribution' => $this->getGenderDistribution(),
            'age_distribution' => $this->getAgeDistribution(),
            'class_distribution' => $this->getClassDistribution(),
            'attendance_overview' => $this->getAttendanceOverview(),
            'attendance_concerns' => $this->getAttendanceConcerns(),
            'academic_performance' => $this->getAcademicPerformance(),
            'academic_concerns' => $this->getAcademicConcerns(),
            'payment_overview' => $this->getPaymentOverview(),
            'payment_concerns' => $this->getPaymentConcerns(),
            'room_occupancy' => $this->getRoomOccupancy(),
            'new_admissions' => $this->getNewAdmissions(),
            'recent_graduates' => $this->getRecentGraduates(),
            'permission_stats' => $this->getPermissionStats(),
        ];

        return view('stats', $stats);
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats()
    {
        return [
            'total_santri' => DB::table('santri')->whereNull('deleted_at')->count(),
            'active_santri' => DB::table('santri')->where('status', 'aktif')->whereNull('deleted_at')->count(),
            'male_santri' => DB::table('santri')->where('jenis_kelamin', 'laki-laki')->where('status', 'aktif')->whereNull('deleted_at')->count(),
            'female_santri' => DB::table('santri')->where('jenis_kelamin', 'perempuan')->where('status', 'aktif')->whereNull('deleted_at')->count(),
            'on_leave' => DB::table('santri')->where('status', 'cuti')->whereNull('deleted_at')->count(),
            'graduated' => DB::table('santri')->where('status', 'lulus')->whereNull('deleted_at')->count(),
        ];
    }

    /**
     * Get status distribution
     */
    private function getStatusDistribution()
    {
        return DB::table('santri')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereNull('deleted_at')
            ->groupBy('status')
            ->get();
    }

    /**
     * Get gender distribution
     */
    private function getGenderDistribution()
    {
        return DB::table('santri')
            ->select('jenis_kelamin', DB::raw('COUNT(*) as count'))
            ->where('status', 'aktif')
            ->whereNull('deleted_at')
            ->groupBy('jenis_kelamin')
            ->get();
    }

    /**
     * Get age distribution
     */
    private function getAgeDistribution()
    {
        return DB::table('santri')
            ->select(
                DB::raw('CASE 
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 13 THEN "< 13 tahun"
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 13 AND 15 THEN "13-15 tahun"
                    WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 16 AND 18 THEN "16-18 tahun"
                    ELSE "> 18 tahun"
                END as age_group'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'aktif')
            ->whereNull('deleted_at')
            ->groupBy('age_group')
            ->orderBy('age_group')
            ->get();
    }

    /**
     * Get class distribution
     */
    private function getClassDistribution()
    {
        return DB::table('kelas_santri as ks')
            ->join('kelas as k', 'ks.kelas_id', '=', 'k.id')
            ->join('tahun_ajaran as ta', 'k.tahun_ajaran_id', '=', 'ta.id')
            ->select(
                'k.tingkat',
                'k.nama_kelas',
                DB::raw('COUNT(*) as count'),
                'k.kapasitas'
            )
            ->where('ta.is_active', 1)
            ->where('ks.status', 'aktif')
            ->whereNull('ks.deleted_at')
            ->groupBy('k.id', 'k.tingkat', 'k.nama_kelas', 'k.kapasitas')
            ->orderBy('k.tingkat')
            ->orderBy('k.nama_kelas')
            ->get();
    }

    /**
     * Get attendance overview
     */
    private function getAttendanceOverview()
    {
        // Get current semester
        $currentSemester = DB::table('semester')->where('is_active', 1)->first();
        
        if (!$currentSemester) {
            return null;
        }

        $stats = DB::table('kehadiran')
            ->whereBetween('tanggal', [$currentSemester->tanggal_mulai, $currentSemester->tanggal_selesai])
            ->whereNull('deleted_at')
            ->select(
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN status_kehadiran = "alpa" THEN 1 ELSE 0 END) as alpa')
            )
            ->first();

        return $stats;
    }

    /**
     * Get santri with attendance concerns (high absence rate)
     */
    private function getAttendanceConcerns()
    {
        // Get current semester
        $currentSemester = DB::table('semester')->where('is_active', 1)->first();
        
        if (!$currentSemester) {
            return collect([]);
        }

        // Santri with more than 10% absence rate (sakit + izin + alpa)
        return DB::table('santri as s')
            ->join('kehadiran as k', 's.id', '=', 'k.santri_id')
            ->whereBetween('k.tanggal', [$currentSemester->tanggal_mulai, $currentSemester->tanggal_selesai])
            ->where('s.status', 'aktif')
            ->whereNull('s.deleted_at')
            ->whereNull('k.deleted_at')
            ->select(
                's.id',
                's.nis',
                's.nama_lengkap',
                DB::raw('COUNT(*) as total_records'),
                DB::raw('SUM(CASE WHEN k.status_kehadiran = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN k.status_kehadiran = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN k.status_kehadiran = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN k.status_kehadiran = "alpa" THEN 1 ELSE 0 END) as alpa'),
                DB::raw('ROUND((SUM(CASE WHEN k.status_kehadiran != "hadir" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as absence_rate')
            )
            ->groupBy('s.id', 's.nis', 's.nama_lengkap')
            ->having('absence_rate', '>', 10)
            ->orderBy('absence_rate', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get academic performance overview
     */
    private function getAcademicPerformance()
    {
        // Get current semester
        $currentSemester = DB::table('semester')->where('is_active', 1)->first();
        
        if (!$currentSemester) {
            return null;
        }

        // Average grades across all subjects
        return DB::table('rapor as r')
            ->join('pengampu as pg', 'r.pengampu_id', '=', 'pg.id')
            ->where('pg.semester_id', $currentSemester->id)
            ->whereNull('r.deleted_at')
            ->select(
                DB::raw('COUNT(DISTINCT r.santri_id) as total_students'),
                DB::raw('ROUND(AVG(r.nilai_akhir), 2) as avg_grade'),
                DB::raw('ROUND(MIN(r.nilai_akhir), 2) as min_grade'),
                DB::raw('ROUND(MAX(r.nilai_akhir), 2) as max_grade'),
                DB::raw('SUM(CASE WHEN r.nilai_akhir >= 80 THEN 1 ELSE 0 END) as excellent_count'),
                DB::raw('SUM(CASE WHEN r.nilai_akhir >= 60 AND r.nilai_akhir < 80 THEN 1 ELSE 0 END) as good_count'),
                DB::raw('SUM(CASE WHEN r.nilai_akhir < 60 THEN 1 ELSE 0 END) as poor_count'),
                DB::raw('SUM(CASE WHEN r.is_lulus = 0 THEN 1 ELSE 0 END) as failed_subjects')
            )
            ->first();
    }

    /**
     * Get santri with academic concerns (low grades)
     */
    private function getAcademicConcerns()
    {
        // Get current semester
        $currentSemester = DB::table('semester')->where('is_active', 1)->first();
        
        if (!$currentSemester) {
            return collect([]);
        }

        // Santri with average grade below 60 or having failed subjects
        return DB::table('santri as s')
            ->join('rapor as r', 's.id', '=', 'r.santri_id')
            ->join('pengampu as pg', 'r.pengampu_id', '=', 'pg.id')
            ->where('pg.semester_id', $currentSemester->id)
            ->where('s.status', 'aktif')
            ->whereNull('s.deleted_at')
            ->whereNull('r.deleted_at')
            ->select(
                's.id',
                's.nis',
                's.nama_lengkap',
                DB::raw('ROUND(AVG(r.nilai_akhir), 2) as avg_grade'),
                DB::raw('COUNT(*) as total_subjects'),
                DB::raw('SUM(CASE WHEN r.is_lulus = 0 THEN 1 ELSE 0 END) as failed_subjects'),
                DB::raw('MIN(r.nilai_akhir) as lowest_grade')
            )
            ->groupBy('s.id', 's.nis', 's.nama_lengkap')
            ->having(DB::raw('AVG(r.nilai_akhir)'), '<', 60)
            ->orHaving('failed_subjects', '>', 0)
            ->orderBy('avg_grade', 'asc')
            ->limit(20)
            ->get();
    }

    /**
     * Get payment overview
     */
    private function getPaymentOverview()
    {
        // Get current academic year
        $currentYear = DB::table('tahun_ajaran')->where('is_active', 1)->first();
        
        if (!$currentYear) {
            return null;
        }

        return DB::table('pembayaran')
            ->where('tahun_ajaran_id', $currentYear->id)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('COUNT(DISTINCT santri_id) as total_paying_students'),
                DB::raw('SUM(CASE WHEN status = "lunas" THEN total_bayar ELSE 0 END) as total_paid'),
                DB::raw('SUM(CASE WHEN status != "lunas" THEN total_bayar ELSE 0 END) as total_outstanding'),
                DB::raw('COUNT(CASE WHEN status = "lunas" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN status != "lunas" THEN 1 END) as unpaid_count')
            )
            ->first();
    }

    /**
     * Get santri with payment concerns
     */
    private function getPaymentConcerns()
    {
        // Get current academic year
        $currentYear = DB::table('tahun_ajaran')->where('is_active', 1)->first();
        
        if (!$currentYear) {
            return collect([]);
        }

        // Santri with outstanding payments
        return DB::table('santri as s')
            ->leftJoin('pembayaran as p', function($join) use ($currentYear) {
                $join->on('s.id', '=', 'p.santri_id')
                     ->where('p.tahun_ajaran_id', '=', $currentYear->id)
                     ->whereNull('p.deleted_at');
            })
            ->where('s.status', 'aktif')
            ->whereNull('s.deleted_at')
            ->select(
                's.id',
                's.nis',
                's.nama_lengkap',
                DB::raw('COALESCE(SUM(CASE WHEN p.status = "lunas" THEN p.total_bayar ELSE 0 END), 0) as total_paid'),
                DB::raw('COALESCE(SUM(CASE WHEN p.status != "lunas" THEN p.total_bayar ELSE 0 END), 0) as total_outstanding'),
                DB::raw('COUNT(CASE WHEN p.status != "lunas" THEN 1 END) as unpaid_count'),
                DB::raw('MAX(p.tanggal_pembayaran) as last_payment_date')
            )
            ->groupBy('s.id', 's.nis', 's.nama_lengkap')
            ->having('total_outstanding', '>', 0)
            ->orderBy('total_outstanding', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get room occupancy statistics
     */
    private function getRoomOccupancy()
    {
        return DB::table('kamar as k')
            ->join('gedung as g', 'k.gedung_id', '=', 'g.id')
            ->leftJoin('penghuni_kamar as pk', function($join) {
                $join->on('k.id', '=', 'pk.kamar_id')
                     ->where('pk.status', '=', 'aktif')
                     ->whereNull('pk.deleted_at');
            })
            ->where('k.is_active', 1)
            ->whereNull('k.deleted_at')
            ->select(
                'g.nama_gedung',
                'g.jenis_gedung',
                'k.nomor_kamar',
                'k.nama_kamar',
                'k.kapasitas',
                DB::raw('COUNT(pk.id) as current_occupants'),
                DB::raw('k.kapasitas - COUNT(pk.id) as available_spaces'),
                DB::raw('ROUND((COUNT(pk.id) / k.kapasitas) * 100, 2) as occupancy_rate')
            )
            ->groupBy('g.id', 'g.nama_gedung', 'g.jenis_gedung', 'k.id', 'k.nomor_kamar', 'k.nama_kamar', 'k.kapasitas')
            ->orderBy('occupancy_rate', 'desc')
            ->get();
    }

    /**
     * Get new admissions (last 3 months)
     */
    private function getNewAdmissions()
    {
        return DB::table('santri')
            ->where('tanggal_masuk', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 3 MONTH)'))
            ->where('status', 'aktif')
            ->whereNull('deleted_at')
            ->select(
                'id',
                'nis',
                'nama_lengkap',
                'jenis_kelamin',
                'tanggal_masuk',
                DB::raw('DATEDIFF(CURDATE(), tanggal_masuk) as days_since_admission')
            )
            ->orderBy('tanggal_masuk', 'desc')
            ->get();
    }

    /**
     * Get recent graduates (last 6 months)
     */
    private function getRecentGraduates()
    {
        return DB::table('santri')
            ->where('status', 'lulus')
            ->where('tanggal_keluar', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 6 MONTH)'))
            ->whereNull('deleted_at')
            ->select(
                'id',
                'nis',
                'nama_lengkap',
                'jenis_kelamin',
                'tanggal_keluar',
                DB::raw('DATEDIFF(tanggal_keluar, tanggal_masuk) as days_enrolled')
            )
            ->orderBy('tanggal_keluar', 'desc')
            ->get();
    }

    /**
     * Get permission statistics
     */
    private function getPermissionStats()
    {
        return DB::table('perizinan')
            ->where('tanggal_mulai', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 MONTH)'))
            ->whereNull('deleted_at')
            ->select(
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN status = "diajukan" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN status = "disetujui" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "ditolak" THEN 1 ELSE 0 END) as rejected'),
                DB::raw('SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) as completed'),
                'jenis_izin',
                DB::raw('COUNT(*) as count_by_type')
            )
            ->groupBy('jenis_izin')
            ->get();
    }
}