<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'nominal',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get pembayaran for this jenis
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'jenis_pembayaran_id');
    }

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope filter by kategori
     */
    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Formatted nominal attribute
     */
    public function getNominalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Kategori label map
     */
    public static function kategoriLabels(): array
    {
        return [
            'bulanan'     => 'Bulanan',
            'tahunan'     => 'Tahunan',
            'pendaftaran' => 'Pendaftaran',
            'kegiatan'    => 'Kegiatan',
            'lainnya'     => 'Lainnya',
        ];
    }
}