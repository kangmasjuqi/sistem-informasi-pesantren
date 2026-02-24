<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajar extends Model
{
    use SoftDeletes;

    protected $table = 'pengajar';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'alamat_lengkap',
        'telepon',
        'email',
        'pendidikan_terakhir',
        'jurusan',
        'universitas',
        'tahun_lulus',
        'keahlian',
        'foto',
        'tanggal_bergabung',
        'tanggal_keluar',
        'status_kepegawaian',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_lahir'     => 'date',
        'tanggal_bergabung' => 'date',
        'tanggal_keluar'    => 'date',
        'keahlian'          => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeKepegawaian($query, string $status)
    {
        return $query->where('status_kepegawaian', $status);
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function statusLabels(): array
    {
        return [
            'aktif'     => 'Aktif',
            'non_aktif' => 'Non Aktif',
            'pensiun'   => 'Pensiun',
        ];
    }

    public static function kepegawaianLabels(): array
    {
        return [
            'tetap'       => 'Tetap',
            'tidak_tetap' => 'Tidak Tetap',
            'honorer'     => 'Honorer',
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getKepegawaianLabelAttribute(): string
    {
        return static::kepegawaianLabels()[$this->status_kepegawaian] ?? $this->status_kepegawaian;
    }

    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir?->age ?? 0;
    }

    public function getMasaKerjaAttribute(): string
    {
        $start = $this->tanggal_bergabung;
        $end   = $this->tanggal_keluar ?? now();
        $diff  = $start->diff($end);
        return $diff->y . ' thn ' . $diff->m . ' bln';
    }
}