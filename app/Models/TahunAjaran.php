<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use SoftDeletes;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'tahun_mulai',
        'tahun_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get pembayaran for this tahun ajaran
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'tahun_ajaran_id');
    }

    /**
     * Scope for active tahun ajaran
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}