<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perizinan extends Model
{
    use SoftDeletes;

    protected $table = 'perizinan';

    protected $fillable = [
        'nomor_izin',
        'santri_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_keluar',
        'waktu_kembali',
        'keperluan',
        'tujuan',
        'penjemput_nama',
        'penjemput_hubungan',
        'penjemput_telepon',
        'penjemput_identitas',
        'status',
        'disetujui_oleh',
        'waktu_persetujuan',
        'catatan_persetujuan',
        'waktu_kembali_aktual',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'        => 'date',
        'tanggal_selesai'      => 'date',
        'waktu_persetujuan'    => 'datetime',
        'waktu_kembali_aktual' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis_izin', $jenis);
    }

    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['diajukan', 'disetujui']);
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function jenisLabels(): array
    {
        return [
            'pulang'           => 'Pulang',
            'kunjungan'        => 'Kunjungan',
            'sakit'            => 'Sakit',
            'keluar_sementara' => 'Keluar Sementara',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'diajukan'  => 'Diajukan',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            'selesai'   => 'Selesai',
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getJenisLabelAttribute(): string
    {
        return static::jenisLabels()[$this->jenis_izin] ?? $this->jenis_izin;
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getDurasiHariAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    /**
     * Generate nomor izin: IZN-YYYYMMDD-XXXX
     */
    public static function generateNomorIzin(): string
    {
        $prefix = 'IZN-' . now()->format('Ymd') . '-';
        $last   = static::where('nomor_izin', 'like', $prefix . '%')
                        ->orderByDesc('id')->first();
        $seq    = $last ? ((int) substr($last->nomor_izin, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}