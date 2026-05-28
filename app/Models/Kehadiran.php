<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Kehadiran extends Model
{
    use SoftDeletes;

    protected $table = 'kehadiran';

    protected $fillable = [
        'tanggal',
        'santri_id',
        'pengampu_id',
        'jadwal_pelajaran_id',
        'jenis_kehadiran',
        'status_kehadiran',
        'waktu_absen',
        'created_by',
        'keterangan_kegiatan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function pengampu(): BelongsTo
    {
        return $this->belongsTo(Pengampu::class, 'pengampu_id');
    }

    public function jadwalPelajaran(): BelongsTo
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeHadir(Builder $query): Builder
    {
        return $query->where('status_kehadiran', 'hadir');
    }

    public function scopeSakit(Builder $query): Builder
    {
        return $query->where('status_kehadiran', 'sakit');
    }

    public function scopeIzin(Builder $query): Builder
    {
        return $query->where('status_kehadiran', 'izin');
    }

    public function scopeAlpa(Builder $query): Builder
    {
        return $query->where('status_kehadiran', 'alpa');
    }

    /**
     * Santri who were not physically present (sakit + izin + alpa).
     */
    public function scopeTidakHadir(Builder $query): Builder
    {
        return $query->whereIn('status_kehadiran', ['sakit', 'izin', 'alpa']);
    }

    public function scopeJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis_kehadiran', $jenis);
    }

    public function scopePelajaran(Builder $query): Builder
    {
        return $query->where('jenis_kehadiran', 'pelajaran');
    }

    public function scopeSholat(Builder $query): Builder
    {
        return $query->where('jenis_kehadiran', 'sholat');
    }

    public function scopeKegiatan(Builder $query): Builder
    {
        return $query->where('jenis_kehadiran', 'kegiatan');
    }

    public function scopeTanggal(Builder $query, string $tanggal): Builder
    {
        return $query->where('tanggal', $tanggal);
    }

    public function scopeBulan(Builder $query, int $bulan, int $tahun): Builder
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function scopeUntukSantri(Builder $query, int $santriId): Builder
    {
        return $query->where('santri_id', $santriId);
    }

    public function scopeUntukPengampu(Builder $query, int $pengampuId): Builder
    {
        return $query->where('pengampu_id', $pengampuId);
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function statusLabels(): array
    {
        return [
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin'  => 'Izin',
            'alpa'  => 'Alpa',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'hadir' => ['label' => 'Hadir', 'cls' => 'status-aktif',       'icon' => '✅'],
            'sakit' => ['label' => 'Sakit', 'cls' => 'kategori-kegiatan',  'icon' => '🤒'],
            'izin'  => ['label' => 'Izin',  'cls' => 'kategori-bulanan',   'icon' => '📋'],
            'alpa'  => ['label' => 'Alpa',  'cls' => 'status-tidak_aktif', 'icon' => '❌'],
        ];
    }

    public static function jenisLabels(): array
    {
        return [
            'pelajaran' => 'Pelajaran',
            'sholat'    => 'Sholat',
            'kegiatan'  => 'Kegiatan',
        ];
    }

    public static function jenisOptions(): array
    {
        return [
            'pelajaran' => ['label' => 'Pelajaran', 'icon' => '📚'],
            'sholat'    => ['label' => 'Sholat',    'icon' => '🕌'],
            'kegiatan'  => ['label' => 'Kegiatan',  'icon' => '🎯'],
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status_kehadiran] ?? $this->status_kehadiran;
    }

    public function getStatusCssAttribute(): string
    {
        return static::statusOptions()[$this->status_kehadiran]['cls'] ?? 'badge-default';
    }

    public function getStatusIconAttribute(): string
    {
        return static::statusOptions()[$this->status_kehadiran]['icon'] ?? '—';
    }

    public function getJenisLabelAttribute(): string
    {
        return static::jenisLabels()[$this->jenis_kehadiran] ?? $this->jenis_kehadiran;
    }

    public function getIsHadirAttribute(): bool
    {
        return $this->status_kehadiran === 'hadir';
    }

    public function getIsPelajaranAttribute(): bool
    {
        return $this->jenis_kehadiran === 'pelajaran';
    }

    // ── Business logic helpers ────────────────────────────────────

    /**
     * Batch upsert attendance for a list of santri on a given date + pengampu.
     * Uses updateOrCreate keyed on (santri_id, pengampu_id, tanggal, jenis_kehadiran).
     *
     * $rows = [
     *   [
     *     'santri_id'        => 1,
     *     'status_kehadiran' => 'hadir',
     *     'waktu_absen'      => '07:05',   // optional
     *     'keterangan'       => null,
     *   ],
     *   ...
     * ]
     */
    public static function batchUpsert(
        string  $tanggal,
        int     $pengampuId,
        ?int    $jadwalPelajaranId,
        string  $jenisKehadiran,
        array   $rows
    ): int {
        $count = 0;

        foreach ($rows as $row) {
            static::updateOrCreate(
                [
                    'tanggal'          => $tanggal,
                    'santri_id'        => $row['santri_id'],
                    'pengampu_id'      => $pengampuId,
                    'jenis_kehadiran'  => $jenisKehadiran,
                ],
                [
                    'jadwal_pelajaran_id' => $jadwalPelajaranId,
                    'status_kehadiran'    => $row['status_kehadiran'] ?? 'hadir',
                    'waktu_absen'         => $row['waktu_absen'] ?? null,
                    'created_by'          => $row['created_by'] ?? null,
                    'keterangan_kegiatan' => $row['keterangan_kegiatan'] ?? null,
                    'keterangan'          => $row['keterangan'] ?? null,
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Compute attendance summary for a santri in a given month.
     * Returns ['hadir' => N, 'sakit' => N, 'izin' => N, 'alpa' => N, 'total' => N, 'persen_hadir' => N]
     */
    public static function ringkasanBulan(
        int    $santriId,
        int    $bulan,
        int    $tahun,
        string $jenis = 'pelajaran'
    ): array {
        $rows = static::where('santri_id', $santriId)
            ->where('jenis_kehadiran', $jenis)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw('status_kehadiran, COUNT(*) as jumlah')
            ->groupBy('status_kehadiran')
            ->pluck('jumlah', 'status_kehadiran')
            ->toArray();

        $hadir = $rows['hadir'] ?? 0;
        $sakit = $rows['sakit'] ?? 0;
        $izin  = $rows['izin']  ?? 0;
        $alpa  = $rows['alpa']  ?? 0;
        $total = $hadir + $sakit + $izin + $alpa;

        return [
            'hadir'        => $hadir,
            'sakit'        => $sakit,
            'izin'         => $izin,
            'alpa'         => $alpa,
            'total'        => $total,
            'persen_hadir' => $total > 0 ? round($hadir / $total * 100, 1) : 0,
        ];
    }

    /**
     * Compute attendance summary for ALL santri in a pengampu on a given date.
     * Useful for "rekap per pertemuan" view.
     * Returns keyed by santri_id.
     */
    public static function ringkasanPertemuan(int $pengampuId, string $tanggal): array
    {
        return static::where('pengampu_id', $pengampuId)
            ->where('tanggal', $tanggal)
            ->where('jenis_kehadiran', 'pelajaran')
            ->get(['santri_id', 'status_kehadiran', 'waktu_absen', 'keterangan'])
            ->keyBy('santri_id')
            ->toArray();
    }

    /**
     * Check whether attendance has already been taken for a given
     * pengampu on a given date (i.e. at least one record exists).
     */
    public static function sudahDiabsen(int $pengampuId, string $tanggal): bool
    {
        return static::where('pengampu_id', $pengampuId)
            ->where('tanggal', $tanggal)
            ->where('jenis_kehadiran', 'pelajaran')
            ->exists();
    }

    /**
     * Get dates that have been attended for a santri in a given month
     * — useful for a calendar-style attendance heatmap.
     */
    public static function tanggalHadirBulan(int $santriId, int $bulan, int $tahun): array
    {
        return static::where('santri_id', $santriId)
            ->where('status_kehadiran', 'hadir')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->toArray();
    }
}