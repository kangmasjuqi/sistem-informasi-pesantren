@extends('layouts.dashboard')

@section('page-title', 'Dashboard Santri')

@section('content')

<!-- Welcome Header -->
<div class="santri-header">
    <div class="santri-content">
        <div class="santri-avatar">
            @if(auth()->user()->foto)
                <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="{{ auth()->user()->nama_lengkap }}">
            @else
                {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 2)) }}
            @endif
        </div>
        
        <div class="santri-info">
            <h1 class="santri-title">
                Assalamu'alaikum, {{ auth()->user()->name }} 🎓
            </h1>
            <p class="santri-subtitle">
                Santri Aktif | {{ now()->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>
</div>

<!-- Profile Cards -->
<div class="profile-grid">
    <div class="profile-card">
        <div class="profile-card-header">
            <svg class="header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
            </svg>
            <h3>Identitas Santri</h3>
        </div>
        <div class="profile-card-body">
            <div class="info-row">
                <span class="info-label">NIS</span>
                <span class="info-value">{{ rand(2025001, 2025999) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelas</span>
                <span class="info-value">X IPA 1</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kamar</span>
                <span class="info-value">Asrama Putra Lt. 2 - A12</span>
            </div>
            <div class="info-row">
                <span class="info-label">Wali Kelas</span>
                <span class="info-value">Ustadz Ahmad Zainudin</span>
            </div>
        </div>
    </div>

    <div class="profile-card">
        <div class="profile-card-header">
            <svg class="header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3>Status Pembayaran</h3>
        </div>
        <div class="profile-card-body">
            <div class="payment-status">
                <div class="payment-month">
                    <span class="month-label">SPP Februari 2026</span>
                    <span class="badge badge-success">LUNAS</span>
                </div>
                <div class="payment-amount">Rp 500.000</div>
            </div>
            <div class="payment-status">
                <div class="payment-month">
                    <span class="month-label">SPP Maret 2026</span>
                    <span class="badge badge-warning">BELUM LUNAS</span>
                </div>
                <div class="payment-amount">Rp 500.000</div>
            </div>
            <a href="#" class="btn-link">Lihat Riwayat Pembayaran →</a>
        </div>
    </div>
</div>

<!-- Activity Cards -->
<div class="activity-section">
    <h3 class="section-title">📚 Kegiatan & Pembelajaran</h3>
    
    <div class="activity-grid">
        <div class="activity-card">
            <div class="activity-icon">📖</div>
            <div class="activity-content">
                <h4>Jadwal Pelajaran</h4>
                <p>Lihat jadwal pelajaran hari ini</p>
            </div>
            <a href="#" class="activity-link">Buka →</a>
        </div>

        <div class="activity-card">
            <div class="activity-icon">📝</div>
            <div class="activity-content">
                <h4>Nilai & Rapor</h4>
                <p>Cek nilai dan rapor semester</p>
            </div>
            <a href="#" class="activity-link">Buka →</a>
        </div>

        <div class="activity-card">
            <div class="activity-icon">📅</div>
            <div class="activity-content">
                <h4>Absensi</h4>
                <p>Rekap kehadiran bulan ini</p>
            </div>
            <a href="#" class="activity-link">Buka →</a>
        </div>

        <div class="activity-card">
            <div class="activity-icon">🏆</div>
            <div class="activity-content">
                <h4>Prestasi</h4>
                <p>Lihat prestasi yang diraih</p>
            </div>
            <a href="#" class="activity-link">Buka →</a>
        </div>
    </div>
</div>

<style>
    .santri-header {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .santri-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .santri-avatar {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.2);
        border: 3px solid rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        overflow: hidden;
    }

    .santri-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .santri-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: white;
    }

    .santri-subtitle {
        font-size: 0.9375rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .profile-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    .profile-card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .header-icon {
        width: 24px;
        height: 24px;
        color: #2563eb;
    }

    .profile-card-header h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
    }

    .profile-card-body {
        padding: 1.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 0.875rem;
        color: #64748b;
    }

    .info-value {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #0f172a;
    }

    .payment-status {
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .payment-month {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .month-label {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #0f172a;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .payment-amount {
        font-size: 1.125rem;
        font-weight: 700;
        color: #2563eb;
    }

    .btn-link {
        display: inline-block;
        margin-top: 0.75rem;
        color: #2563eb;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-link:hover {
        color: #1d4ed8;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.25rem;
    }

    .activity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
    }

    .activity-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid #e5e7eb;
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .activity-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #2563eb;
    }

    .activity-icon {
        font-size: 2.5rem;
    }

    .activity-content h4 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.5rem 0;
    }

    .activity-content p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .activity-link {
        color: #2563eb;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        margin-top: auto;
    }

    @media (max-width: 768px) {
        .santri-content {
            flex-direction: column;
            text-align: center;
        }

        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection