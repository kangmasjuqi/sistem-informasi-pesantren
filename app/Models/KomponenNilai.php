<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomponenNilai extends Model
{
    use SoftDeletes;

    protected $table = 'komponen_nilai';

    protected $fillable = [
        'kode',
        'nama',
        'bobot',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bobot'     => 'integer',
    ];

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Bobot formatted with % suffix
     */
    public function getBobotFormattedAttribute(): string
    {
        return $this->bobot . '%';
    }
}