<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Nilai extends Model
{
    use SoftDeletes;

    protected $table = 'nilai';

    protected $fillable = [
        'santri_id',
        'pengampu_id',
        'komponen_nilai_id',
        'nilai',
        'catatan',
        'tanggal_input',
    ];

    protected $casts = [
        'nilai'         => 'decimal:2',
        'tanggal_input' => 'date',
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

    public function komponenNilai(): BelongsTo
    {
        return $this->belongsTo(KomponenNilai::class, 'komponen_nilai_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeForPengampu(Builder $query, int $pengampuId): Builder
    {
        return $query->where('pengampu_id', $pengampuId);
    }

    public function scopeForSantri(Builder $query, int $santriId): Builder
    {
        return $query->where('santri_id', $santriId);
    }

    public function scopeForKomponen(Builder $query, int $komponenId): Builder
    {
        return $query->where('komponen_nilai_id', $komponenId);
    }

    // ── Helpers ───────────────────────────────────────────────────

    /**
     * Grade label from numeric score.
     */
    public static function grade(float $nilai): string
    {
        return match(true) {
            $nilai >= 90 => 'A',
            $nilai >= 80 => 'B',
            $nilai >= 70 => 'C',
            $nilai >= 60 => 'D',
            default      => 'E',
        };
    }

    /**
     * CSS class for score colouring in the grid.
     */
    public static function scoreColor(float $nilai): string
    {
        return match(true) {
            $nilai >= 80 => 'score-high',
            $nilai >= 65 => 'score-mid',
            default      => 'score-low',
        };
    }

    /**
     * Batch upsert scores for one pengampu.
     *
     * $rows = [
     *   ['santri_id' => 1, 'komponen_nilai_id' => 3, 'nilai' => 85.5, 'catatan' => '...'],
     *   ...
     * ]
     *
     * Uses updateOrCreate to honour the UNIQUE KEY.
     * Returns count of records written.
     */
    public static function batchUpsert(int $pengampuId, array $rows, string $tanggalInput): int
    {
        $count = 0;
        $today = $tanggalInput;

        foreach ($rows as $row) {
            // Skip blank cells (teacher left it empty intentionally)
            if ($row['nilai'] === null || $row['nilai'] === '') {
                continue;
            }

            static::updateOrCreate(
                [
                    'santri_id'         => $row['santri_id'],
                    'pengampu_id'       => $pengampuId,
                    'komponen_nilai_id' => $row['komponen_nilai_id'],
                ],
                [
                    'nilai'         => (float) $row['nilai'],
                    'catatan'       => $row['catatan'] ?? null,
                    'tanggal_input' => $today,
                ]
            );

            $count++;
        }

        return $count;
    }

    /**
     * Calculate weighted average for a santri in a pengampu,
     * using bobot from komponen_nilai.
     */
    public static function nilaiAkhir(int $santriId, int $pengampuId): ?float
    {
        $scores = static::where('santri_id', $santriId)
            ->where('pengampu_id', $pengampuId)
            ->with('komponenNilai')
            ->get();

        if ($scores->isEmpty()) return null;

        $totalBobot  = 0;
        $totalWeighted = 0;

        foreach ($scores as $score) {
            $bobot = $score->komponenNilai?->bobot ?? 1;
            $totalBobot    += $bobot;
            $totalWeighted += $score->nilai * $bobot;
        }

        return $totalBobot > 0
            ? round($totalWeighted / $totalBobot, 2)
            : null;
    }
}