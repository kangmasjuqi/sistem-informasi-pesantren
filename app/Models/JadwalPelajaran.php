<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class JadwalPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'pengampu_id',
        'kelas_id',
        'pengajar_id',
        'hari',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'jam_ke' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function pengampu(): BelongsTo
    {
        return $this->belongsTo(Pengampu::class, 'pengampu_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class, 'pengajar_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('jadwal_pelajaran.status', 'aktif');
    }

    public function scopeForKelas(Builder $query, int $kelasId): Builder
    {
        return $query->where('jadwal_pelajaran.kelas_id', $kelasId);
    }

    public function scopeForPengajar(Builder $query, int $pengajarId): Builder
    {
        return $query->where('jadwal_pelajaran.pengajar_id', $pengajarId);
    }

    public function scopeForHari(Builder $query, string $hari): Builder
    {
        return $query->where('jadwal_pelajaran.hari', $hari);
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function hariOptions(): array
    {
        return [
            'senin'   => 'Senin',
            'selasa'  => 'Selasa',
            'rabu'    => 'Rabu',
            'kamis'   => 'Kamis',
            'jumat'   => 'Jumat',
            'sabtu'   => 'Sabtu',
            'minggu'  => 'Minggu',
        ];
    }

    public static function hariOrder(): array
    {
        return array_keys(static::hariOptions());
    }

    public static function statusOptions(): array
    {
        return [
            'aktif'   => ['label' => 'Aktif',   'cls' => 'status-aktif'],
            'libur'   => ['label' => 'Libur',   'cls' => 'kategori-kegiatan'],
            'diganti' => ['label' => 'Diganti', 'cls' => 'status-tidak_aktif'],
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getHariLabelAttribute(): string
    {
        return static::hariOptions()[$this->hari] ?? $this->hari;
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusOptions()[$this->status]['label'] ?? $this->status;
    }

    public function getStatusCssAttribute(): string
    {
        return static::statusOptions()[$this->status]['cls'] ?? 'badge-default';
    }

    public function getWaktuLabelAttribute(): string
    {
        $mulai   = substr($this->jam_mulai,   0, 5);
        $selesai = substr($this->jam_selesai, 0, 5);
        return "{$mulai} – {$selesai}";
    }

    // ── Conflict detection ────────────────────────────────────────

    /**
     * Check if a kelas already has a schedule at this day/time.
     * A kelas can only have one subject at a time.
     */
    public static function hasKelasConflict(
        int    $kelasId,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int   $exceptId = null
    ): bool {
        return static::where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->where('status', 'aktif')
            ->where(fn($q) => $q
                ->whereBetween('jam_mulai',   [$jamMulai, $jamSelesai])
                ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                ->orWhere(fn($q2) => $q2
                    ->where('jam_mulai',   '<=', $jamMulai)
                    ->where('jam_selesai', '>=', $jamSelesai)
                )
            )
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Check if a pengajar is already teaching another class at this day/time.
     */
    public static function hasPengajarConflict(
        int    $pengajarId,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int   $exceptId = null
    ): bool {
        return static::where('pengajar_id', $pengajarId)
            ->where('hari', $hari)
            ->where('status', 'aktif')
            ->where(fn($q) => $q
                ->whereBetween('jam_mulai',   [$jamMulai, $jamSelesai])
                ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                ->orWhere(fn($q2) => $q2
                    ->where('jam_mulai',   '<=', $jamMulai)
                    ->where('jam_selesai', '>=', $jamSelesai)
                )
            )
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Check if a ruangan is already occupied at this day/time.
     */
    public static function hasRuanganConflict(
        string $ruangan,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int   $exceptId = null
    ): bool {
        if (!$ruangan) return false;

        return static::where('ruangan', $ruangan)
            ->where('hari', $hari)
            ->where('status', 'aktif')
            ->where(fn($q) => $q
                ->whereBetween('jam_mulai',   [$jamMulai, $jamSelesai])
                ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                ->orWhere(fn($q2) => $q2
                    ->where('jam_mulai',   '<=', $jamMulai)
                    ->where('jam_selesai', '>=', $jamSelesai)
                )
            )
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Run all three conflict checks and return array of conflict messages.
     * Empty array = no conflicts.
     */
    public static function checkAllConflicts(
        int    $kelasId,
        int    $pengajarId,
        string $ruangan,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int   $exceptId = null
    ): array {
        $conflicts = [];

        if (static::hasKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $exceptId)) {
            $conflicts[] = 'Kelas sudah memiliki jadwal di waktu yang sama.';
        }

        if (static::hasPengajarConflict($pengajarId, $hari, $jamMulai, $jamSelesai, $exceptId)) {
            $conflicts[] = 'Pengajar sudah mengajar kelas lain di waktu yang sama.';
        }

        if ($ruangan && static::hasRuanganConflict($ruangan, $hari, $jamMulai, $jamSelesai, $exceptId)) {
            $conflicts[] = "Ruangan {$ruangan} sudah dipakai di waktu yang sama.";
        }

        return $conflicts;
    }
}