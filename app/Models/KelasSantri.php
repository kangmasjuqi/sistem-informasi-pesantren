<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class KelasSantri extends Model
{
    use SoftDeletes;

    protected $table = 'kelas_santri';

    protected $fillable = [
        'kelas_id',
        'santri_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk'  => 'date',
        'tanggal_keluar' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeLulus(Builder $query): Builder
    {
        return $query->where('status', 'lulus');
    }

    public function scopePindah(Builder $query): Builder
    {
        return $query->where('status', 'pindah');
    }

    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('status', 'keluar');
    }

    /**
     * Santri who have left the class (any non-aktif status).
     */
    public function scopeInaktif(Builder $query): Builder
    {
        return $query->whereIn('status', ['lulus', 'pindah', 'keluar']);
    }

    public function scopeUntukKelas(Builder $query, int $kelasId): Builder
    {
        return $query->where('kelas_id', $kelasId);
    }

    public function scopeUntukSantri(Builder $query, int $santriId): Builder
    {
        return $query->where('santri_id', $santriId);
    }

    /**
     * Filter enrollments active within a given school year.
     */
    public function scopeTahunAjaran(Builder $query, int $tahunAjaranId): Builder
    {
        return $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId));
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function statusLabels(): array
    {
        return [
            'aktif'  => 'Aktif',
            'lulus'  => 'Lulus',
            'pindah' => 'Pindah',
            'keluar' => 'Keluar',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'aktif'  => 'status-aktif',
            'lulus'  => 'kategori-bulanan',
            'pindah' => 'kategori-kegiatan',
            'keluar' => 'status-tidak_aktif',
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return static::statusColors()[$this->status] ?? 'badge-default';
    }

    public function getIsAktifAttribute(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Duration in days from tanggal_masuk to tanggal_keluar (or today if still active).
     */
    public function getDurasiHariAttribute(): int
    {
        $end = $this->tanggal_keluar ?? Carbon::today();
        return (int) $this->tanggal_masuk->diffInDays($end);
    }

    /**
     * Human-readable duration, e.g. "1 tahun 3 bulan".
     */
    public function getDurasiLabelAttribute(): string
    {
        $end   = $this->tanggal_keluar ?? Carbon::today();
        $diff  = $this->tanggal_masuk->diff($end);

        $parts = [];
        if ($diff->y) $parts[] = $diff->y . ' tahun';
        if ($diff->m) $parts[] = $diff->m . ' bulan';
        if (!$diff->y && !$diff->m && $diff->d) $parts[] = $diff->d . ' hari';

        return $parts ? implode(' ', $parts) : '< 1 hari';
    }

    // ── Business logic helpers ────────────────────────────────────

    /**
     * Mark santri as no longer active in this class.
     * Automatically stamps tanggal_keluar if not provided.
     */
    public function keluarkan(string $status, ?string $keterangan = null, ?Carbon $tanggalKeluar = null): bool
    {
        if (!in_array($status, ['lulus', 'pindah', 'keluar'])) {
            throw new \InvalidArgumentException("Status tidak valid: {$status}");
        }

        return $this->update([
            'status'         => $status,
            'tanggal_keluar' => $tanggalKeluar ?? Carbon::today(),
            'keterangan'     => $keterangan ?? $this->keterangan,
        ]);
    }

    /**
     * Check whether another active enrollment already exists for this santri
     * in any class — useful before inserting a new record.
     */
    public static function sudahAktifDiKelas(int $santriId): bool
    {
        return static::where('santri_id', $santriId)
            ->where('status', 'aktif')
            ->exists();
    }

    /**
     * Retrieve the current active enrollment for a santri, if any.
     */
    public static function aktifUntukSantri(int $santriId): ?self
    {
        return static::where('santri_id', $santriId)
            ->where('status', 'aktif')
            ->with(['kelas.tahunAjaran'])
            ->latest('tanggal_masuk')
            ->first();
    }
}