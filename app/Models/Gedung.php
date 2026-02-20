<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gedung extends Model
{
    use SoftDeletes;

    protected $table = 'gedung';

    protected $fillable = [
        'kode_gedung',
        'nama_gedung',
        'jenis_gedung',
        'jumlah_lantai',
        'kapasitas_total',
        'alamat_lokasi',
        'tahun_dibangun',
        'kondisi',
        'fasilitas',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'jumlah_lantai'  => 'integer',
        'kapasitas_total'=> 'integer',
        'fasilitas'      => 'array',   // stored as JSON
    ];

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope filter by jenis
     */
    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis_gedung', $jenis);
    }

    /**
     * Human-readable jenis labels
     */
    public static function jenisLabels(): array
    {
        return [
            'asrama_putra'  => 'Asrama Putra',
            'asrama_putri'  => 'Asrama Putri',
            'kelas'         => 'Kelas',
            'serbaguna'     => 'Serbaguna',
            'masjid'        => 'Masjid',
            'kantor'        => 'Kantor',
            'perpustakaan'  => 'Perpustakaan',
            'lab'           => 'Laboratorium',
            'dapur'         => 'Dapur',
            'lainnya'       => 'Lainnya',
        ];
    }

    /**
     * Human-readable kondisi labels
     */
    public static function kondisiLabels(): array
    {
        return [
            'baik'         => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat'  => 'Rusak Berat',
        ];
    }

    public function getJenisLabelAttribute(): string
    {
        return static::jenisLabels()[$this->jenis_gedung] ?? $this->jenis_gedung;
    }

    public function getKondisiLabelAttribute(): string
    {
        return static::kondisiLabels()[$this->kondisi] ?? $this->kondisi;
    }
}