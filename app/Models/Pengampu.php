<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Pengampu extends Model
{
    use SoftDeletes;

    protected $table = 'pengampu';

    protected $fillable = [
        'pengajar_id',
        'mata_pelajaran_id',
        'kelas_id',
        'semester_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class, 'pengajar_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSemester(Builder $query, int $semesterId): Builder
    {
        return $query->where('semester_id', $semesterId);
    }

    public function scopePengajar(Builder $query, int $pengajarId): Builder
    {
        return $query->where('pengajar_id', $pengajarId);
    }

    public function scopeKelas(Builder $query, int $kelasId): Builder
    {
        return $query->where('kelas_id', $kelasId);
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function statusLabels(): array
    {
        return [
            'aktif'   => 'Aktif',
            'selesai' => 'Selesai',
            'diganti' => 'Diganti',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'aktif'   => ['label' => 'Aktif',   'cls' => 'status-aktif'],
            'selesai' => ['label' => 'Selesai',  'cls' => 'kategori-lainnya'],
            'diganti' => ['label' => 'Diganti',  'cls' => 'status-tidak_aktif'],
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusCssAttribute(): string
    {
        return static::statusOptions()[$this->status]['css'] ?? 'badge-default';
    }

    public function getIsAktifAttribute(): bool
    {
        return $this->status === 'aktif';
    }

    // ── Business logic helpers ────────────────────────────────────

    /**
     * Check duplicate before insert (mirrors the UNIQUE KEY).
     */
    public static function isDuplicate(
        int $pengajarId,
        int $mataPelajaranId,
        int $kelasId,
        int $semesterId,
        ?int $exceptId = null
    ): bool {
        return static::where('pengajar_id', $pengajarId)
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->where('kelas_id', $kelasId)
            ->where('semester_id', $semesterId)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    /**
     * Batch-insert multiple Kelas × MataPelajaran assignments
     * for one Pengajar in one Semester.
     *
     * $items = [ ['kelas_id' => 1, 'mata_pelajaran_id' => 2, 'tanggal_mulai' => '...'], ... ]
     *
     * Returns ['created' => [...], 'skipped' => [...]]
     */
    public static function batchAssign(
        int $pengajarId,
        int $semesterId,
        array $items,
        ?string $keterangan = null
    ): array {
        $created = [];
        $skipped = [];

        foreach ($items as $item) {
            if (static::isDuplicate($pengajarId, $item['mata_pelajaran_id'], $item['kelas_id'], $semesterId)) {
                $skipped[] = $item;
                continue;
            }

            $created[] = static::create([
                'pengajar_id'       => $pengajarId,
                'mata_pelajaran_id' => $item['mata_pelajaran_id'],
                'kelas_id'          => $item['kelas_id'],
                'semester_id'       => $semesterId,
                'tanggal_mulai'     => $item['tanggal_mulai'],
                'tanggal_selesai'   => $item['tanggal_selesai'] ?? null,
                'status'            => 'aktif',
                'keterangan'        => $keterangan,
            ]);
        }

        return compact('created', 'skipped');
    }
}