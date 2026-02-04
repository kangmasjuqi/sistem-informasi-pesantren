@extends('layouts.dashboard')

@section('page-title', 'Profile - ' . $profile->nama_lengkap)

@section('content')

<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-content">
        <div class="profile-avatar">
            @if($profile->foto)
                <img src="{{ asset('storage/' . $profile->foto) }}" alt="{{ $profile->nama_lengkap }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
            @else
                {{ strtoupper(substr($profile->nama_lengkap, 0, 2)) }}
            @endif
        </div>
        
        <div class="profile-info">
            <h2 class="profile-name">{{ $profile->nama_lengkap }}</h2>
            <div class="profile-meta">
                <div class="profile-meta-item">
                    <div class="profile-meta-label">NIS</div>
                    <div class="profile-meta-value">{{ $profile->nis }}</div>
                </div>
                <div class="profile-meta-item">
                    <div class="profile-meta-label">NISN</div>
                    <div class="profile-meta-value">{{ $profile->nisn ?? '-' }}</div>
                </div>
                <div class="profile-meta-item">
                    <div class="profile-meta-label">Status</div>
                    <div class="profile-meta-value">
                        @php
                            $statusBadge = match($profile->status) {
                                'aktif' => 'success',
                                'lulus' => 'info',
                                'cuti' => 'warning',
                                default => 'danger'
                            };
                        @endphp
                        <span class="badge badge-{{ $statusBadge }}" style="background: rgba(255,255,255,0.2); color: white;">
                            {{ strtoupper($profile->status) }}
                        </span>
                    </div>
                </div>
                <div class="profile-meta-item">
                    <div class="profile-meta-label">Jenis Kelamin</div>
                    <div class="profile-meta-value">{{ ucwords($profile->jenis_kelamin) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs">
    <ul class="tab-list">
        <li><button class="tab-button active" data-tab="profil">Profil</button></li>
        <li><button class="tab-button" data-tab="kelas">Kelas</button></li>
        <li><button class="tab-button" data-tab="kamar">Kamar</button></li>
        <li><button class="tab-button" data-tab="kehadiran">Kehadiran</button></li>
        <li><button class="tab-button" data-tab="nilai">Nilai</button></li>
        <li><button class="tab-button" data-tab="rapor">Rapor</button></li>
        <li><button class="tab-button" data-tab="pembayaran">Pembayaran</button></li>
        <li><button class="tab-button" data-tab="perizinan">Perizinan</button></li>
    </ul>
</div>

<!-- Tab: Profil -->
<div id="profil" class="tab-content active">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Pribadi</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value">{{ $profile->nama_lengkap }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Panggilan</div>
                    <div class="info-value">{{ $profile->nama_panggilan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIK</div>
                    <div class="info-value">{{ $profile->nik ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tempat, Tanggal Lahir</div>
                    <div class="info-value">{{ $profile->tempat_lahir }}, {{ $profile->tanggal_lahir->format('d F Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Usia</div>
                    <div class="info-value">{{ $profile->tanggal_lahir->age }} tahun</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Golongan Darah</div>
                    <div class="info-value">{{ $profile->golongan_darah ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Anak Ke</div>
                    <div class="info-value">{{ $profile->anak_ke ?? '-' }} dari {{ $profile->jumlah_saudara ?? '-' }} bersaudara</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telepon</div>
                    <div class="info-value">{{ $profile->telepon ?? '-' }}</div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Alamat Lengkap</div>
                    <div class="info-value">
                        {{ $profile->alamat_lengkap }}
                        @if($profile->kelurahan || $profile->kecamatan || $profile->kabupaten || $profile->provinsi)
                            <br>
                            <span class="text-muted">
                                {{ collect([$profile->kelurahan, $profile->kecamatan, $profile->kabupaten, $profile->provinsi])->filter()->implode(', ') }}
                                {{ $profile->kode_pos ? ' - ' . $profile->kode_pos : '' }}
                            </span>
                        @endif
                    </div>
                </div>
                @if($profile->riwayat_penyakit)
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Riwayat Penyakit</div>
                    <div class="info-value">{{ $profile->riwayat_penyakit }}</div>
                </div>
                @endif
                <div class="info-item">
                    <div class="info-label">Tanggal Masuk</div>
                    <div class="info-value">{{ $profile->tanggal_masuk->format('d F Y') }}</div>
                </div>
                @if($profile->tanggal_keluar)
                <div class="info-item">
                    <div class="info-label">Tanggal Keluar</div>
                    <div class="info-value">{{ $profile->tanggal_keluar->format('d F Y') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($wali && $wali->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Wali Santri</h3>
        </div>
        <div class="card-body">
            @foreach($wali as $w)
            <div class="mb-3" style="padding-bottom: 1.5rem; border-bottom: 1px solid var(--color-border);">
                <h4 style="font-family: var(--font-display); font-size: 1.125rem; margin-bottom: 1rem; color: var(--color-primary);">
                    {{ ucwords($w->jenis_wali) }}
                    @if($w->status == 'meninggal')
                        <span class="badge badge-default">(Alm.)</span>
                    @endif
                </h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value">{{ $w->nama_lengkap }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">NIK</div>
                        <div class="info-value">{{ $w->nik ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tempat, Tanggal Lahir</div>
                        <div class="info-value">
                            @if($w->tempat_lahir && $w->tanggal_lahir)
                                {{ $w->tempat_lahir }}, {{ \Carbon\Carbon::parse($w->tanggal_lahir)->format('d F Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Pendidikan</div>
                        <div class="info-value">{{ $w->pendidikan_terakhir ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Pekerjaan</div>
                        <div class="info-value">{{ $w->pekerjaan ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Penghasilan/Bulan</div>
                        <div class="info-value">
                            @if($w->penghasilan)
                                Rp {{ number_format($w->penghasilan, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Telepon</div>
                        <div class="info-value">{{ $w->telepon ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $w->email ?? '-' }}</div>
                    </div>
                    @if($w->alamat)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $w->alamat }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Tab: Kelas -->
<div id="kelas" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Kelas</h3>
        </div>
        <div class="card-body">
            @if($kelas && $kelas->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Kelas</th>
                            <th>Tingkat</th>
                            <th>Wali Kelas</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelas as $k)
                        <tr>
                            <td>
                                <strong>{{ $k->tahun_ajaran }}</strong>
                                @if($k->tahun_ajaran_active)
                                    <span class="badge badge-success ml-1">Aktif</span>
                                @endif
                            </td>
                            <td>{{ $k->nama_kelas }}</td>
                            <td>{{ $k->tingkat }}</td>
                            <td>{{ $k->wali_kelas ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($k->tanggal_masuk)->format('d M Y') }}</td>
                            <td>{{ $k->tanggal_keluar ? \Carbon\Carbon::parse($k->tanggal_keluar)->format('d M Y') : '-' }}</td>
                            <td>
                                @php
                                    $statusBadge = match($k->status) {
                                        'aktif' => 'success',
                                        'lulus' => 'info',
                                        'pindah' => 'warning',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ ucwords($k->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Belum ada data kelas.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Kamar -->
<div id="kamar" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Penghuni Kamar</h3>
        </div>
        <div class="card-body">
            @if($kamar && $kamar->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Gedung</th>
                            <th>Jenis</th>
                            <th>Nomor Kamar</th>
                            <th>Nama Kamar</th>
                            <th>Lantai</th>
                            <th>Kapasitas</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kamar as $km)
                        <tr>
                            <td>{{ $km->nama_gedung }}</td>
                            <td>
                                <span class="badge badge-info">{{ str_replace('_', ' ', ucwords($km->jenis_gedung)) }}</span>
                            </td>
                            <td><strong>{{ $km->nomor_kamar }}</strong></td>
                            <td>{{ $km->nama_kamar ?? '-' }}</td>
                            <td>Lantai {{ $km->lantai }}</td>
                            <td>{{ $km->kapasitas }} orang</td>
                            <td>{{ \Carbon\Carbon::parse($km->tanggal_masuk)->format('d M Y') }}</td>
                            <td>{{ $km->tanggal_keluar ? \Carbon\Carbon::parse($km->tanggal_keluar)->format('d M Y') : '-' }}</td>
                            <td>
                                @php
                                    $statusBadge = match($km->status) {
                                        'aktif' => 'success',
                                        'pindah' => 'warning',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ ucwords($km->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Belum ada data penghuni kamar.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Kehadiran -->
<div id="kehadiran" class="tab-content">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Kehadiran</div>
            <div class="stat-value text-success">{{ $kehadiran['overall']->hadir ?? 0 }}</div>
            <div class="stat-description">Dari {{ $kehadiran['overall']->total ?? 0 }} total absensi</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sakit</div>
            <div class="stat-value text-warning">{{ $kehadiran['overall']->sakit ?? 0 }}</div>
            <div class="stat-description">Absensi karena sakit</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Izin</div>
            <div class="stat-value text-info">{{ $kehadiran['overall']->izin ?? 0 }}</div>
            <div class="stat-description">Absensi dengan izin</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Alpa</div>
            <div class="stat-value text-danger">{{ $kehadiran['overall']->alpa ?? 0 }}</div>
            <div class="stat-description">Tanpa keterangan</div>
        </div>
    </div>

    @if($kehadiran['current_semester'])
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Statistik Semester Aktif</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="info-item">
                    <div class="info-label">Total Absensi</div>
                    <div class="info-value">{{ $kehadiran['current_semester']->total ?? 0 }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Hadir</div>
                    <div class="info-value text-success">{{ $kehadiran['current_semester']->hadir ?? 0 }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Sakit</div>
                    <div class="info-value text-warning">{{ $kehadiran['current_semester']->sakit ?? 0 }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Izin</div>
                    <div class="info-value text-info">{{ $kehadiran['current_semester']->izin ?? 0 }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Alpa</div>
                    <div class="info-value text-danger">{{ $kehadiran['current_semester']->alpa ?? 0 }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Persentase Kehadiran</div>
                    <div class="info-value text-success">
                        @if(($kehadiran['current_semester']->total ?? 0) > 0)
                            {{ number_format(($kehadiran['current_semester']->hadir / $kehadiran['current_semester']->total) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Kehadiran (30 Hari Terakhir)</h3>
        </div>
        <div class="card-body">
            @if($kehadiran['recent'] && $kehadiran['recent']->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Mata Pelajaran</th>
                            <th>Kategori</th>
                            <th>Waktu Absen</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kehadiran['recent'] as $h)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($h->tanggal)->format('d M Y') }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $h->jenis_kehadiran)) }}</td>
                            <td>{{ $h->nama_mapel ?? ($h->keterangan_kegiatan ?? '-') }}</td>
                            <td>
                                @if($h->kategori)
                                <span class="badge badge-info">{{ ucwords($h->kategori) }}</span>
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ $h->waktu_absen ?? '-' }}</td>
                            <td>
                                @php
                                    $statusBadge = match($h->status_kehadiran) {
                                        'hadir' => 'success',
                                        'sakit' => 'warning',
                                        'izin' => 'info',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ ucwords($h->status_kehadiran) }}</span>
                            </td>
                            <td>{{ $h->keterangan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Belum ada data kehadiran dalam 30 hari terakhir.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Nilai -->
<div id="nilai" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Nilai Per Mata Pelajaran</h3>
        </div>
        <div class="card-body">
            @if($nilai && $nilai->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Kategori</th>
                            <th>Komponen</th>
                            <th>Bobot</th>
                            <th>Nilai</th>
                            <th>Pengajar</th>
                            <th>Tanggal Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nilai as $n)
                        <tr>
                            <td>{{ $n->tahun_ajaran }}</td>
                            <td>{{ ucwords($n->jenis_semester) }}</td>
                            <td>{{ $n->nama_kelas }}</td>
                            <td><strong>{{ $n->nama_mapel }}</strong></td>
                            <td><span class="badge badge-info">{{ ucwords($n->kategori_mapel) }}</span></td>
                            <td>{{ $n->komponen_nama }}</td>
                            <td>{{ $n->bobot }}%</td>
                            <td>
                                <strong style="font-size: 1.125rem; 
                                    color: {{ $n->nilai >= 80 ? 'var(--color-success)' : ($n->nilai >= 60 ? 'var(--color-warning)' : 'var(--color-danger)') }}">
                                    {{ number_format($n->nilai, 2) }}
                                </strong>
                            </td>
                            <td>{{ $n->nama_pengajar }}</td>
                            <td>{{ \Carbon\Carbon::parse($n->tanggal_input)->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Belum ada data nilai.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Rapor -->
<div id="rapor" class="tab-content">
    @if($rapor_summary && $rapor_summary->count() > 0)
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Ringkasan Rapor Per Semester</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Kelas</th>
                            <th>Rata-rata</th>
                            <th>Ranking</th>
                            <th>Total Mapel</th>
                            <th>Lulus</th>
                            <th>Kehadiran</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Keputusan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rapor_summary as $rs)
                        <tr>
                            <td><strong>{{ $rs->tahun_ajaran }}</strong></td>
                            <td>{{ ucwords($rs->jenis_semester) }}</td>
                            <td>{{ $rs->nama_kelas }}</td>
                            <td>
                                <strong style="font-size: 1.125rem; color: var(--color-primary)">
                                    {{ number_format($rs->rata_rata, 2) }}
                                </strong>
                            </td>
                            <td>
                                @if($rs->ranking_kelas)
                                    <strong>{{ $rs->ranking_kelas }}</strong> dari {{ $rs->total_siswa_kelas ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $rs->total_mapel }}</td>
                            <td><span class="text-success">{{ $rs->total_mapel_lulus }}</span></td>
                            <td><span class="text-success">{{ $rs->total_kehadiran }}</span></td>
                            <td><span class="text-warning">{{ $rs->total_sakit }}</span></td>
                            <td><span class="text-info">{{ $rs->total_izin }}</span></td>
                            <td><span class="text-danger">{{ $rs->total_alpa }}</span></td>
                            <td>
                                @if($rs->keputusan)
                                    @php
                                        $keputusanBadge = match($rs->keputusan) {
                                            'naik_kelas' => 'success',
                                            'lulus' => 'info',
                                            default => 'warning'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $keputusanBadge }}">{{ str_replace('_', ' ', ucwords($rs->keputusan)) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($rs->is_finalized)
                                    <span class="badge badge-success">Final</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rapor Per Mata Pelajaran</h3>
        </div>
        <div class="card-body">
            @if($rapor && $rapor->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Kategori</th>
                            <th>SKS</th>
                            <th>Nilai Akhir</th>
                            <th>Huruf</th>
                            <th>Angka (4.0)</th>
                            <th>Predikat</th>
                            <th>Ranking</th>
                            <th>Lulus</th>
                            <th>Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rapor as $r)
                        <tr>
                            <td>{{ $r->tahun_ajaran }}</td>
                            <td>{{ ucwords($r->jenis_semester) }}</td>
                            <td>{{ $r->nama_kelas }}</td>
                            <td><strong>{{ $r->nama_mapel }}</strong></td>
                            <td><span class="badge badge-info">{{ ucwords($r->kategori_mapel) }}</span></td>
                            <td>{{ $r->bobot_sks }}</td>
                            <td>
                                <strong style="font-size: 1.125rem; 
                                    color: {{ $r->nilai_akhir >= 80 ? 'var(--color-success)' : ($r->nilai_akhir >= 60 ? 'var(--color-warning)' : 'var(--color-danger)') }}">
                                    {{ number_format($r->nilai_akhir, 2) }}
                                </strong>
                            </td>
                            <td><strong>{{ $r->nilai_huruf ?? '-' }}</strong></td>
                            <td>{{ $r->nilai_angka ? number_format($r->nilai_angka, 2) : '-' }}</td>
                            <td>
                                @if($r->predikat)
                                    @php
                                        $predikatBadge = match($r->predikat) {
                                            'sangat_baik' => 'success',
                                            'baik' => 'info',
                                            'cukup' => 'warning',
                                            default => 'danger'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $predikatBadge }}">{{ str_replace('_', ' ', ucwords($r->predikat)) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $r->ranking_kelas ?? '-' }}</td>
                            <td>
                                @if($r->is_lulus)
                                    <span class="badge badge-success">Lulus</span>
                                @else
                                    <span class="badge badge-danger">Tidak Lulus</span>
                                @endif
                            </td>
                            <td>{{ $r->nama_pengajar }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Belum ada data rapor.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Pembayaran -->
<div id="pembayaran" class="tab-content">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Dibayar (Keseluruhan)</div>
            <div class="stat-value text-success">Rp {{ number_format($pembayaran_summary['total_paid'], 0, ',', '.') }}</div>
            <div class="stat-description">Total pembayaran lunas</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Tunggakan (Keseluruhan)</div>
            <div class="stat-value text-danger">Rp {{ number_format($pembayaran_summary['total_outstanding'], 0, ',', '.') }}</div>
            <div class="stat-description">Total yang belum lunas</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Dibayar (Tahun Aktif)</div>
            <div class="stat-value text-success">Rp {{ number_format($pembayaran_summary['current_year_paid'], 0, ',', '.') }}</div>
            <div class="stat-description">Pembayaran tahun ajaran aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tunggakan (Tahun Aktif)</div>
            <div class="stat-value text-warning">Rp {{ number_format($pembayaran_summary['current_year_outstanding'], 0, ',', '.') }}</div>
            <div class="stat-description">Belum lunas tahun ini</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Pembayaran</h3>
        </div>
        <div class="card-body">
            @if($pembayaran && $pembayaran->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Tahun Ajaran</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Periode</th>
                            <th>Nominal</th>
                            <th>Potongan</th>
                            <th>Denda</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayaran as $p)
                        <tr>
                            <td><strong>{{ $p->kode_pembayaran }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_pembayaran)->format('d M Y') }}</td>
                            <td>{{ $p->tahun_ajaran ?? '-' }}</td>
                            <td>{{ $p->jenis_pembayaran }}</td>
                            <td><span class="badge badge-info">{{ ucwords($p->kategori_pembayaran) }}</span></td>
                            <td>
                                @if($p->bulan && $p->tahun)
                                    {{ \Carbon\Carbon::create($p->tahun, $p->bulan)->format('F Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                            <td class="text-success">
                                @if($p->potongan > 0)
                                    -Rp {{ number_format($p->potongan, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-danger">
                                @if($p->denda > 0)
                                    +Rp {{ number_format($p->denda, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td><strong>Rp {{ number_format($p->total_bayar, 0, ',', '.') }}</strong></td>
                            <td>{{ ucwords($p->metode_pembayaran) }}</td>
                            <td>
                                @php
                                    $statusBadge = match($p->status) {
                                        'lunas' => 'success',
                                        'cicilan' => 'warning',
                                        default => 'danger'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ ucwords(str_replace('_', ' ', $p->status)) }}</span>
                            </td>
                            <td>{{ $p->petugas ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Belum ada data pembayaran.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Perizinan -->
<div id="perizinan" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Perizinan</h3>
        </div>
        <div class="card-body">
            @if($perizinan && $perizinan->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nomor Izin</th>
                            <th>Jenis</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Durasi</th>
                            <th>Keperluan</th>
                            <th>Tujuan</th>
                            <th>Penjemput</th>
                            <th>Status</th>
                            <th>Disetujui Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perizinan as $izin)
                        <tr>
                            <td><strong>{{ $izin->nomor_izin }}</strong></td>
                            <td><span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $izin->jenis_izin)) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($izin->tanggal_selesai)) + 1 }} hari
                            </td>
                            <td>{{ $izin->keperluan }}</td>
                            <td>{{ $izin->tujuan ?? '-' }}</td>
                            <td>
                                @if($izin->penjemput_nama)
                                    {{ $izin->penjemput_nama }}
                                    @if($izin->penjemput_hubungan)
                                        <br><small class="text-muted">({{ $izin->penjemput_hubungan }})</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadge = match($izin->status) {
                                        'disetujui' => 'success',
                                        'selesai' => 'info',
                                        'ditolak' => 'danger',
                                        default => 'warning'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ ucwords($izin->status) }}</span>
                            </td>
                            <td>
                                @if($izin->disetujui_oleh_nama)
                                    {{ $izin->disetujui_oleh_nama }}
                                    @if($izin->waktu_persetujuan)
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($izin->waktu_persetujuan)->format('d M Y H:i') }}</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Belum ada data perizinan.</p>
            @endif
        </div>
    </div>
</div>

@endsection