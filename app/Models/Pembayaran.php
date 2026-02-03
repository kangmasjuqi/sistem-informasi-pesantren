<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'pembayaran';

    protected $fillable = [
        'kode_pembayaran',
        'santri_id',
        'jenis_pembayaran_id',
        'tahun_ajaran_id',
        'tanggal_pembayaran',
        'bulan',
        'tahun',
        'nominal',
        'potongan',
        'denda',
        'total_bayar',
        'metode_pembayaran',
        'nomor_referensi',
        'status',
        'petugas_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'nominal' => 'decimal:2',
        'potongan' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    /**
     * Get the santri that owns the pembayaran
     */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Get the jenis pembayaran
     */
    public function jenisPembayaran(): BelongsTo
    {
        return $this->belongsTo(JenisPembayaran::class, 'jenis_pembayaran_id');
    }

    /**
     * Get the tahun ajaran
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Get the petugas/user who recorded the payment
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Scope for lunas payments
     */
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    /**
     * Scope for belum lunas payments
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'belum_lunas');
    }

    /**
     * Scope for cicilan payments
     */
    public function scopeCicilan($query)
    {
        return $query->where('status', 'cicilan');
    }

    /**
     * Get formatted nominal
     */
    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Get formatted total bayar
     */
    public function getFormattedTotalBayarAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }
}