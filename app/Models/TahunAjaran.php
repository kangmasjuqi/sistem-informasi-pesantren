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
        'is_active' => 'boolean',
        'tahun_mulai' => 'integer',
        'tahun_selesai' => 'integer',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relationship with Semester
     */
    public function semesters()
    {
        return $this->hasMany(Semester::class, 'tahun_ajaran_id');
    }

    /**
     * Get semester ganjil
     */
    public function semesterGanjil()
    {
        return $this->hasOne(Semester::class, 'tahun_ajaran_id')
                    ->where('jenis_semester', 'ganjil');
    }

    /**
     * Get semester genap
     */
    public function semesterGenap()
    {
        return $this->hasOne(Semester::class, 'tahun_ajaran_id')
                    ->where('jenis_semester', 'genap');
    }

    /**
     * Scope for active tahun ajaran
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific year range
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('tahun_mulai', $year)
                     ->orWhere('tahun_selesai', $year);
    }

    /**
     * Get formatted period
     */
    public function getPeriodAttribute()
    {
        return "{$this->tahun_mulai}/{$this->tahun_selesai}";
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