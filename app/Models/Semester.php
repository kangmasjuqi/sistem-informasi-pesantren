<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $table = 'semester';

    protected $fillable = [
        'tahun_ajaran_id',
        'jenis_semester',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relationship with TahunAjaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Scope for active semester
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ganjil semester
     */
    public function scopeGanjil($query)
    {
        return $query->where('jenis_semester', 'ganjil');
    }

    /**
     * Scope for genap semester
     */
    public function scopeGenap($query)
    {
        return $query->where('jenis_semester', 'genap');
    }

    /**
     * Get jenis semester display name
     */
    public function getJenisNameAttribute()
    {
        return $this->jenis_semester === 'ganjil' ? 'Ganjil' : 'Genap';
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Aktif</span>' 
            : '<span class="badge bg-secondary">Tidak Aktif</span>';
    }
}