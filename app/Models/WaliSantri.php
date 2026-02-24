<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliSantri extends Model
{
    use SoftDeletes;

    protected $table = 'wali_santri';

    protected $fillable = [
        'santri_id',
        'jenis_wali',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan_terakhir',
        'pekerjaan',
        'penghasilan',
        'telepon',
        'email',
        'alamat',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'penghasilan'   => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis_wali', $jenis);
    }

    public function scopeHidup($query)
    {
        return $query->where('status', 'hidup');
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function jenisLabels(): array
    {
        return [
            'ayah' => 'Ayah',
            'ibu'  => 'Ibu',
            'wali' => 'Wali',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'hidup'    => 'Hidup',
            'meninggal'=> 'Meninggal',
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getJenisLabelAttribute(): string
    {
        return static::jenisLabels()[$this->jenis_wali] ?? $this->jenis_wali;
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getPenghasilanFormattedAttribute(): string
    {
        return $this->penghasilan
            ? 'Rp ' . number_format($this->penghasilan, 0, ',', '.')
            : '—';
    }
}