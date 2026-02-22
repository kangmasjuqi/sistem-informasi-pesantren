<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenghuniKamar extends Model
{
    use SoftDeletes;

    protected $table = 'penghuni_kamar';

    protected $fillable = [
        'santri_id',
        'kamar_id',
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

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function statusLabels(): array
    {
        return [
            'aktif'  => 'Aktif',
            'keluar' => 'Keluar',
            'pindah' => 'Pindah',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getDurasiHariAttribute(): int
    {
        $end = $this->tanggal_keluar ?? now()->toDateString();
        return $this->tanggal_masuk->diffInDays($end);
    }
}