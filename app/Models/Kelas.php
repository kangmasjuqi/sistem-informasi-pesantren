<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'tahun_ajaran_id',
        'wali_kelas_id',
        'nama_kelas',
        'tingkat',
        'kapasitas',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'kapasitas' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Pengajar::class, 'wali_kelas_id');
    }

    public function kelasSantri(): HasMany
    {
        return $this->hasMany(KelasSantri::class, 'kelas_id');
    }

    public function santriAktif(): HasMany
    {
        return $this->hasMany(KelasSantri::class, 'kelas_id')->where('status', 'aktif');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInaktif($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeTingkat($query, string $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    public function scopeTahunAjaran($query, int $tahunAjaranId)
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }

    // ── Static label maps ─────────────────────────────────────────

    public static function tingkatOptions(): array
    {
        return [
            '1'           => 'Tingkat 1',
            '2'           => 'Tingkat 2',
            '3'           => 'Tingkat 3',
            '4'           => 'Tingkat 4',
            '5'           => 'Tingkat 5',
            '6'           => 'Tingkat 6',
            'Tsanawiyah'  => 'Tsanawiyah',
            'Aliyah'      => 'Aliyah',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'active'    => 'Aktif',
            'inactive'  => 'Tidak Aktif',
            'completed' => 'Selesai',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'active'    => ['label' => 'Aktif',       'css' => 'status-aktif'],
            'inactive'  => ['label' => 'Tidak Aktif', 'css' => 'status-tidak_aktif'],
            'completed' => ['label' => 'Selesai',     'css' => 'kategori-lainnya'],
        ];
    }

    // ── Computed attributes ───────────────────────────────────────

    public function getTingkatLabelAttribute(): string
    {
        return static::tingkatOptions()[$this->tingkat] ?? $this->tingkat;
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusCssAttribute(): string
    {
        return static::statusOptions()[$this->status]['css'] ?? 'badge-default';
    }

    public function getIsAktifAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getJumlahSantriAttribute(): int
    {
        return $this->santriAktif()->count();
    }

    public function getSisaKapasitasAttribute(): int
    {
        return max(0, $this->kapasitas - $this->jumlah_santri);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->jumlah_santri >= $this->kapasitas;
    }

    public function getNamaLengkapAttribute(): string
    {
        return $this->nama_kelas . ($this->tahunAjaran ? ' — ' . $this->tahunAjaran->nama : '');
    }
}