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
     * Scope for active jenis pembayaran
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}