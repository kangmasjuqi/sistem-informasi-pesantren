<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriInventaris extends Model
{
    use SoftDeletes;

    protected $table = 'kategori_inventaris';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}