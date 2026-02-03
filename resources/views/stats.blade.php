@extends('layouts.dashboard')
@push('styles')
<style>

/* Chart Layout */
.chart-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

/* Each Chart Box */
.chart-box {
    background: #fafafa;
    border-radius: 10px;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    min-height: 260px;

    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Title */
.chart-title {
    font-size: 0.95rem;
    font-weight: 600;
    text-align: center;
    margin-bottom: 0.5rem;
    color: #374151;
}

/* Canvas size control */
.chart-box canvas {
    max-height: 200px !important;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .chart-grid {
        grid-template-columns: 1fr;
    }
}

</style>
@endpush

@section('page-title', 'Statistik Santri')

@section('content')

<!-- Tabs Navigation -->
<div class="tabs">
    <ul class="tab-list">
        <li><button class="tab-button active" data-tab="statistik">Statistik</button></li>
        <li><button class="tab-button" data-tab="kehadiran">Kehadiran</button></li>
        <li><button class="tab-button" data-tab="akademik">Akademik</button></li>
        <li><button class="tab-button" data-tab="pembayaran">Pembayaran</button></li>
        <li><button class="tab-button" data-tab="distribusi">Distribusi</button></li>
        <li><button class="tab-button" data-tab="kamar">Kamar</button></li>
        <li><button class="tab-button" data-tab="terbaru">Santri Baru</button></li>
    </ul>
</div>

<!-- Tab: Statistik -->
<div id="statistik" class="tab-content active">

    <!-- Overview Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Santri</div>
            <div class="stat-value">{{ number_format($overview['total_santri']) }}</div>
            <div class="stat-description">Seluruh santri terdaftar</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Santri Aktif</div>
            <div class="stat-value text-success">{{ number_format($overview['active_santri']) }}</div>
            <div class="stat-description">
                {{ number_format(($overview['active_santri'] / max($overview['total_santri'], 1)) * 100, 1) }}% dari total
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Laki-laki</div>
            <div class="stat-value text-info">{{ number_format($overview['male_santri']) }}</div>
            <div class="stat-description">Santri putra aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Perempuan</div>
            <div class="stat-value text-info">{{ number_format($overview['female_santri']) }}</div>
            <div class="stat-description">Santri putri aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Lulus</div>
            <div class="stat-value" style="color: var(--color-accent);">{{ number_format($overview['graduated']) }}</div>
            <div class="stat-description">Total alumni</div>
        </div>
    </div>

    <!-- Critical Alerts Section -->
    <div class="card" style="border-left: 4px solid var(--color-danger); margin-bottom: 2rem;">
        <div class="card-header" style="background: rgba(220, 38, 38, 0.05);">
            <h3 class="card-title" style="color: var(--color-danger);">
                <svg style="width: 24px; height: 24px; display: inline-block; vertical-align: middle; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Perlu Perhatian Khusus
            </h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Masalah Kehadiran</div>
                    <div class="info-value text-danger">{{ $attendance_concerns->count() }} santri</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Masalah Akademik</div>
                    <div class="info-value text-danger">{{ $academic_concerns->count() }} santri</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tunggakan Pembayaran</div>
                    <div class="info-value text-danger">{{ $payment_concerns->count() }} santri</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Summary -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Ringkasan Statistik</h3>
        </div>

        <div class="card-body">

            <div class="chart-grid">

                <!-- Gender -->
                <div class="chart-box">
                    <h4 class="chart-title">Komposisi Gender</h4>
                    <canvas id="genderChart"></canvas>
                </div>

                <!-- Attendance -->
                <div class="chart-box">
                    <h4 class="chart-title">Kehadiran</h4>
                    <canvas id="attendanceChart"></canvas>
                </div>

                <!-- Academic -->
                <div class="chart-box">
                    <h4 class="chart-title">Akademik</h4>
                    <canvas id="academicChart"></canvas>
                </div>

                <!-- Payment -->
                <div class="chart-box">
                    <h4 class="chart-title">Pembayaran</h4>
                    <canvas id="paymentChart"></canvas>
                </div>

                <!-- Status -->
                <div class="chart-box">
                    <h4 class="chart-title">Status Santri</h4>
                    <canvas id="statusChart"></canvas>
                </div>

            </div>

        </div>
    </div>

</div>

<!-- Tab: Kehadiran -->
<div id="kehadiran" class="tab-content">
    @if($attendance_overview)
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Ringkasan Kehadiran Semester Aktif</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Absensi</div>
                    <div class="stat-value">{{ number_format($attendance_overview->total_records) }}</div>
                    <div class="stat-description">Record kehadiran</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Hadir</div>
                    <div class="stat-value text-success">{{ number_format($attendance_overview->hadir) }}</div>
                    <div class="stat-description">
                        {{ number_format(($attendance_overview->hadir / max($attendance_overview->total_records, 1)) * 100, 1) }}%
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Sakit</div>
                    <div class="stat-value text-warning">{{ number_format($attendance_overview->sakit) }}</div>
                    <div class="stat-description">
                        {{ number_format(($attendance_overview->sakit / max($attendance_overview->total_records, 1)) * 100, 1) }}%
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Izin</div>
                    <div class="stat-value text-info">{{ number_format($attendance_overview->izin) }}</div>
                    <div class="stat-description">
                        {{ number_format(($attendance_overview->izin / max($attendance_overview->total_records, 1)) * 100, 1) }}%
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Alpa</div>
                    <div class="stat-value text-danger">{{ number_format($attendance_overview->alpa) }}</div>
                    <div class="stat-description">
                        {{ number_format(($attendance_overview->alpa / max($attendance_overview->total_records, 1)) * 100, 1) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header" style="background: rgba(220, 38, 38, 0.05);">
            <h3 class="card-title" style="color: var(--color-danger);">⚠️ Santri dengan Tingkat Ketidakhadiran Tinggi (>10%)</h3>
        </div>
        <div class="card-body">
            @if($attendance_concerns && $attendance_concerns->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Santri</th>
                            <th>Total Absensi</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Tingkat Ketidakhadiran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendance_concerns as $concern)
                        <tr>
                            <td><strong>{{ $concern->nis }}</strong></td>
                            <td>{{ $concern->nama_lengkap }}</td>
                            <td>{{ $concern->total_records }}</td>
                            <td class="text-success">{{ $concern->hadir }}</td>
                            <td class="text-warning">{{ $concern->sakit }}</td>
                            <td class="text-info">{{ $concern->izin }}</td>
                            <td class="text-danger">{{ $concern->alpa }}</td>
                            <td>
                                <strong style="font-size: 1.125rem; color: var(--color-danger);">
                                    {{ $concern->absence_rate }}%
                                </strong>
                            </td>
                            <td>
                                <a href="{{ route('santri.show', $concern->id) }}" style="color: var(--color-primary); text-decoration: none; font-weight: 600;">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-success" style="text-align: center; padding: 2rem;">
                ✓ Tidak ada santri dengan masalah kehadiran signifikan
            </p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Akademik -->
<div id="akademik" class="tab-content">
    @if($academic_performance)
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Performa Akademik Semester Aktif</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Rata-rata Nilai</div>
                    <div class="stat-value" style="color: {{ $academic_performance->avg_grade >= 75 ? 'var(--color-success)' : ($academic_performance->avg_grade >= 60 ? 'var(--color-warning)' : 'var(--color-danger)') }}">
                        {{ $academic_performance->avg_grade }}
                    </div>
                    <div class="stat-description">Nilai rata-rata seluruh santri</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Nilai Tertinggi</div>
                    <div class="stat-value text-success">{{ $academic_performance->max_grade }}</div>
                    <div class="stat-description">Prestasi terbaik</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Nilai Terendah</div>
                    <div class="stat-value text-danger">{{ $academic_performance->min_grade }}</div>
                    <div class="stat-description">Perlu perhatian</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Sangat Baik (≥80)</div>
                    <div class="stat-value text-success">{{ number_format($academic_performance->excellent_count) }}</div>
                    <div class="stat-description">Nilai mata pelajaran</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Baik (60-79)</div>
                    <div class="stat-value text-info">{{ number_format($academic_performance->good_count) }}</div>
                    <div class="stat-description">Nilai mata pelajaran</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Kurang (<60)</div>
                    <div class="stat-value text-danger">{{ number_format($academic_performance->poor_count) }}</div>
                    <div class="stat-description">Nilai mata pelajaran</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header" style="background: rgba(220, 38, 38, 0.05);">
            <h3 class="card-title" style="color: var(--color-danger);">⚠️ Santri dengan Masalah Akademik</h3>
        </div>
        <div class="card-body">
            @if($academic_concerns && $academic_concerns->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Santri</th>
                            <th>Rata-rata Nilai</th>
                            <th>Total Mata Pelajaran</th>
                            <th>Mata Pelajaran Tidak Lulus</th>
                            <th>Nilai Terendah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academic_concerns as $concern)
                        <tr>
                            <td><strong>{{ $concern->nis }}</strong></td>
                            <td>{{ $concern->nama_lengkap }}</td>
                            <td>
                                <strong style="font-size: 1.125rem; color: {{ $concern->avg_grade >= 60 ? 'var(--color-warning)' : 'var(--color-danger)' }}">
                                    {{ $concern->avg_grade }}
                                </strong>
                            </td>
                            <td>{{ $concern->total_subjects }}</td>
                            <td>
                                @if($concern->failed_subjects > 0)
                                    <span class="badge badge-danger">{{ $concern->failed_subjects }} gagal</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-danger">{{ $concern->lowest_grade }}</td>
                            <td>
                                <a href="{{ route('santri.show', $concern->id) }}" style="color: var(--color-primary); text-decoration: none; font-weight: 600;">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-success" style="text-align: center; padding: 2rem;">
                ✓ Tidak ada santri dengan masalah akademik signifikan
            </p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Pembayaran -->
<div id="pembayaran" class="tab-content">
    @if($payment_overview)
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Ringkasan Pembayaran Tahun Ajaran Aktif</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Terbayar</div>
                    <div class="stat-value text-success">Rp {{ number_format($payment_overview->total_paid, 0, ',', '.') }}</div>
                    <div class="stat-description">{{ $payment_overview->paid_count }} pembayaran lunas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Tunggakan</div>
                    <div class="stat-value text-danger">Rp {{ number_format($payment_overview->total_outstanding, 0, ',', '.') }}</div>
                    <div class="stat-description">{{ $payment_overview->unpaid_count }} pembayaran belum lunas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Santri yang Membayar</div>
                    <div class="stat-value text-info">{{ number_format($payment_overview->total_paying_students) }}</div>
                    <div class="stat-description">Santri aktif melakukan pembayaran</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header" style="background: rgba(220, 38, 38, 0.05);">
            <h3 class="card-title" style="color: var(--color-danger);">⚠️ Santri dengan Tunggakan Pembayaran</h3>
        </div>
        <div class="card-body">
            @if($payment_concerns && $payment_concerns->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Santri</th>
                            <th>Total Terbayar</th>
                            <th>Total Tunggakan</th>
                            <th>Jumlah Tagihan Belum Lunas</th>
                            <th>Pembayaran Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment_concerns as $concern)
                        <tr>
                            <td><strong>{{ $concern->nis }}</strong></td>
                            <td>{{ $concern->nama_lengkap }}</td>
                            <td class="text-success">Rp {{ number_format($concern->total_paid, 0, ',', '.') }}</td>
                            <td>
                                <strong style="font-size: 1.125rem; color: var(--color-danger);">
                                    Rp {{ number_format($concern->total_outstanding, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge badge-danger">{{ $concern->unpaid_count }} tagihan</span>
                            </td>
                            <td>
                                @if($concern->last_payment_date)
                                    {{ \Carbon\Carbon::parse($concern->last_payment_date)->format('d M Y') }}
                                @else
                                    <span class="text-muted">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('santri.show', $concern->id) }}" style="color: var(--color-primary); text-decoration: none; font-weight: 600;">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-success" style="text-align: center; padding: 2rem;">
                ✓ Tidak ada tunggakan pembayaran
            </p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Distribusi -->
<div id="distribusi" class="tab-content">

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Distribusi Status</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                @foreach($status_distribution as $status)
                <div class="stat-card">
                    <div class="stat-label">{{ ucwords($status->status) }}</div>
                    <div class="stat-value">{{ number_format($status->count) }}</div>
                    <div class="stat-description">
                        {{ number_format(($status->count / max($overview['total_santri'], 1)) * 100, 1) }}% dari total
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Distribusi Usia (Santri Aktif)</h3>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                @foreach($age_distribution as $age)
                <div class="stat-card">
                    <div class="stat-label">{{ $age->age_group }}</div>
                    <div class="stat-value">{{ number_format($age->count) }}</div>
                    <div class="stat-description">santri</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Distribusi Kelas (Tahun Ajaran Aktif)</h3>
        </div>
        <div class="card-body">
            @if($class_distribution && $class_distribution->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tingkat</th>
                            <th>Nama Kelas</th>
                            <th>Jumlah Santri</th>
                            <th>Kapasitas</th>
                            <th>Sisa Kuota</th>
                            <th>Tingkat Pengisian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($class_distribution as $class)
                        <tr>
                            <td>{{ $class->tingkat }}</td>
                            <td><strong>{{ $class->nama_kelas }}</strong></td>
                            <td>{{ $class->count }}</td>
                            <td>{{ $class->kapasitas }}</td>
                            <td>{{ max(0, $class->kapasitas - $class->count) }}</td>
                            <td>
                                @php
                                    $percentage = ($class->count / max($class->kapasitas, 1)) * 100;
                                    $color = $percentage >= 90 ? 'var(--color-danger)' : ($percentage >= 75 ? 'var(--color-warning)' : 'var(--color-success)');
                                @endphp
                                <strong style="color: {{ $color }}">
                                    {{ number_format($percentage, 1) }}%
                                </strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Tidak ada data distribusi kelas.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Kamar -->
<div id="kamar" class="tab-content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Okupansi Kamar</h3>
        </div>
        <div class="card-body">
            @if($room_occupancy && $room_occupancy->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Gedung</th>
                            <th>Jenis</th>
                            <th>Nomor Kamar</th>
                            <th>Nama Kamar</th>
                            <th>Kapasitas</th>
                            <th>Penghuni</th>
                            <th>Sisa Tempat</th>
                            <th>Okupansi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($room_occupancy as $room)
                        <tr>
                            <td>{{ $room->nama_gedung }}</td>
                            <td><span class="badge badge-info">{{ str_replace('_', ' ', ucwords($room->jenis_gedung)) }}</span></td>
                            <td><strong>{{ $room->nomor_kamar }}</strong></td>
                            <td>{{ $room->nama_kamar ?? '-' }}</td>
                            <td>{{ $room->kapasitas }}</td>
                            <td>{{ $room->current_occupants }}</td>
                            <td>{{ $room->available_spaces }}</td>
                            <td>
                                @php
                                    $color = $room->occupancy_rate >= 90 ? 'var(--color-danger)' : ($room->occupancy_rate >= 75 ? 'var(--color-warning)' : 'var(--color-success)');
                                @endphp
                                <strong style="color: {{ $color }}">
                                    {{ $room->occupancy_rate }}%
                                </strong>
                                @if($room->occupancy_rate >= 100)
                                    <span class="badge badge-danger ml-1">Penuh</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Tidak ada data okupansi kamar.</p>
            @endif
        </div>
    </div>
</div>

<!-- Tab: Santri Baru -->
<div id="terbaru" class="tab-content">
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Santri Baru (3 Bulan Terakhir)</h3>
        </div>
        <div class="card-body">
            @if($new_admissions && $new_admissions->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Santri</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Masuk</th>
                            <th>Lama Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($new_admissions as $santri)
                        <tr>
                            <td><strong>{{ $santri->nis }}</strong></td>
                            <td>{{ $santri->nama_lengkap }}</td>
                            <td>{{ ucwords($santri->jenis_kelamin) }}</td>
                            <td>{{ \Carbon\Carbon::parse($santri->tanggal_masuk)->format('d M Y') }}</td>
                            <td>{{ $santri->days_since_admission }} hari</td>
                            <td>
                                <a href="{{ route('santri.show', $santri->id) }}" style="color: var(--color-primary); text-decoration: none; font-weight: 600;">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Tidak ada santri baru dalam 3 bulan terakhir.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Alumni Terbaru (6 Bulan Terakhir)</h3>
        </div>
        <div class="card-body">
            @if($recent_graduates && $recent_graduates->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama Santri</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lulus</th>
                            <th>Lama Belajar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_graduates as $santri)
                        <tr>
                            <td><strong>{{ $santri->nis }}</strong></td>
                            <td>{{ $santri->nama_lengkap }}</td>
                            <td>{{ ucwords($santri->jenis_kelamin) }}</td>
                            <td>{{ \Carbon\Carbon::parse($santri->tanggal_keluar)->format('d M Y') }}</td>
                            <td>{{ number_format($santri->days_enrolled / 365, 1) }} tahun</td>
                            <td>
                                <a href="{{ route('santri.show', $santri->id) }}" style="color: var(--color-primary); text-decoration: none; font-weight: 600;">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Tidak ada alumni baru dalam 6 bulan terakhir.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ================= GENDER ================= */

    new Chart(document.getElementById('genderChart'), {
        type: 'pie',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                data: [
                    {{ $overview['male_santri'] }},
                    {{ $overview['female_santri'] }}
                ],
                backgroundColor: ['#3b82f6', '#ec4899']
            }]
        }
    });


    /* ================= ATTENDANCE ================= */

    @if($attendance_overview)
    new Chart(document.getElementById('attendanceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
            datasets: [{
                data: [
                    {{ $attendance_overview->hadir }},
                    {{ $attendance_overview->sakit }},
                    {{ $attendance_overview->izin }},
                    {{ $attendance_overview->alpa }}
                ],
                backgroundColor: [
                    '#22c55e',
                    '#facc15',
                    '#38bdf8',
                    '#ef4444'
                ]
            }]
        }
    });
    @endif


    /* ================= ACADEMIC ================= */

    @if($academic_performance)
    new Chart(document.getElementById('academicChart'), {
        type: 'bar',
        data: {
            labels: ['Sangat Baik', 'Baik', 'Kurang'],
            datasets: [{
                label: 'Jumlah Nilai',
                data: [
                    {{ $academic_performance->excellent_count }},
                    {{ $academic_performance->good_count }},
                    {{ $academic_performance->poor_count }}
                ],
                backgroundColor: [
                    '#22c55e',
                    '#3b82f6',
                    '#ef4444'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    @endif


    /* ================= PAYMENT ================= */

    @if($payment_overview)
    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: ['Terbayar', 'Tunggakan'],
            datasets: [{
                data: [
                    {{ $payment_overview->total_paid }},
                    {{ $payment_overview->total_outstanding }}
                ],
                backgroundColor: ['#22c55e', '#ef4444']
            }]
        }
    });
    @endif


    /* ================= STATUS ================= */

    const statusLabels = [
        @foreach($status_distribution as $s)
            "{{ ucwords($s->status) }}",
        @endforeach
    ];

    const statusData = [
        @foreach($status_distribution as $s)
            {{ $s->count }},
        @endforeach
    ];

    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    '#22c55e',
                    '#3b82f6',
                    '#f97316',
                    '#ef4444',
                    '#a855f7'
                ]
            }]
        }
    });

});
</script>
@endpush


@endsection