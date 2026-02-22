<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use SoftDeletes;

    protected $table = 'kamar';

    protected $fillable = [
        'gedung_id',
        'nomor_kamar',
        'nama_kamar',
        'lantai',
        'kapasitas',
        'luas',
        'fasilitas',
        'kondisi',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lantai'    => 'integer',
        'kapasitas' => 'integer',
        'luas'      => 'decimal:2',
        'fasilitas' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    public function penghuni(): HasMany
    {
        return $this->hasMany(PenghuniKamar::class, 'kamar_id');
    }

    public function penghuniAktif(): HasMany
    {
        return $this->hasMany(PenghuniKamar::class, 'kamar_id')->where('status', 'aktif');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeGedung($query, int $gedungId)
    {
        return $query->where('gedung_id', $gedungId);
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getKondisiLabelAttribute(): string
    {
        return Gedung::kondisiLabels()[$this->kondisi] ?? $this->kondisi;
    }

    public function getJumlahPenghuniAttribute(): int
    {
        return $this->penghuniAktif()->count();
    }

    public function getSisaKapasitasAttribute(): int
    {
        return max(0, $this->kapasitas - $this->jumlah_penghuni);
    }
}