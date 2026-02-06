<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kategori',
        'bobot_sks',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bobot_sks' => 'integer',
    ];

    /**
     * Scope for active mata pelajaran
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Get kategori display name
     */
    public function getKategoriNameAttribute()
    {
        $kategoriNames = [
            'agama' => 'Agama',
            'umum' => 'Umum',
            'keterampilan' => 'Keterampilan',
            'ekstrakurikuler' => 'Ekstrakurikuler',
        ];

        return $kategoriNames[$this->kategori] ?? $this->kategori;
    }
}