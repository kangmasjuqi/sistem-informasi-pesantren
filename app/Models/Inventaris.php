<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventaris extends Model
{
    use SoftDeletes;

    protected $table = 'inventaris';

    protected $fillable = [
        'kategori_inventaris_id',
        'gedung_id',
        'kode_inventaris',
        'nama_barang',
        'merk',
        'tipe_model',
        'jumlah',
        'satuan',
        'kondisi',
        'tanggal_perolehan',
        'harga_perolehan',
        'nilai_penyusutan',
        'sumber_dana',
        'lokasi',
        'spesifikasi',
        'nomor_seri',
        'tanggal_maintenance_terakhir',
        'penanggung_jawab',
        'foto',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'is_active'                    => 'boolean',
        'jumlah'                       => 'integer',
        'harga_perolehan'              => 'decimal:2',
        'nilai_penyusutan'             => 'decimal:2',
        'tanggal_perolehan'            => 'date',
        'tanggal_maintenance_terakhir' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriInventaris::class, 'kategori_inventaris_id');
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKondisi($query, string $kondisi)
    {
        return $query->where('kondisi', $kondisi);
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function kondisiLabels(): array
    {
        return [
            'baik'         => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat'  => 'Rusak Berat',
            'hilang'       => 'Hilang',
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getKondisiLabelAttribute(): string
    {
        return static::kondisiLabels()[$this->kondisi] ?? $this->kondisi;
    }

    public function getHargaFormattedAttribute(): string
    {
        return $this->harga_perolehan
            ? 'Rp ' . number_format($this->harga_perolehan, 0, ',', '.')
            : '—';
    }

    /**
     * Generate kode inventaris: INV-YYYYMM-XXXX
     */
    public static function generateKode(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $last   = static::where('kode_inventaris', 'like', $prefix . '%')
                        ->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->kode_inventaris, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}